<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Category;
use App\Models\Court;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;

class AdminController extends Controller
{
    // Menampilkan halaman dashboard utama admin beserta statistik & booking terbaru
    public function dashboard()
    {
        $totalBookings = Booking::count();
        $activeBookings = Booking::where('status', 'aktif')->count();
        $totalCourts = Court::count();
        $activeCourts = Court::where('is_active', true)->count();
        $totalCategories = Category::count();
        $activeCategories = Category::where('is_active', true)->count();
        $totalCustomers = User::where('role', 'pelanggan')->count();

        $paidPaymentsQuery = Payment::where('status', 'lunas');
        $totalRevenue = $paidPaymentsQuery->sum('amount');
        $paidTransactions = $paidPaymentsQuery->count();

        $pendingVerifications = Payment::where('status', 'menunggu_cek')->count();

        $recentBookings = Booking::with(['user', 'court.category', 'payment'])
            ->latest()
            ->take(8)
            ->get();

        if (view()->exists('admin.dashboard')) {
            return view('admin.dashboard', compact(
                'totalBookings',
                'activeBookings',
                'totalCourts',
                'activeCourts',
                'totalCategories',
                'activeCategories',
                'totalCustomers',
                'totalRevenue',
                'paidTransactions',
                'pendingVerifications',
                'recentBookings'
            ));
        }

        return view('admin.bookings', [
            'bookings' => $recentBookings,
            'allCourts' => Court::orderBy('name')->get(),
            'counts' => [
                'all' => $totalBookings,
                'menunggu_cek' => $pendingVerifications,
                'lunas' => $paidTransactions,
                'belum_bayar' => Payment::where('status', 'belum_bayar')->count(),
            ],
        ]);
    }

    // Menampilkan halaman manajemen kategori & lapangan
    public function courts(Request $request)
    {
        $categories = Category::withCount('courts')
            ->when($request->cat_search, function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->cat_search . '%');
            })
            ->latest()
            ->get();

        $courts = Court::with('category')
            ->when($request->category_id, function ($q) use ($request) {
                $q->where('category_id', $request->category_id);
            })
            ->when($request->search, function ($q) use ($request) {
                $search = $request->search;
                $q->where(function ($sub) use ($search) {
                    $sub->where('name', 'like', '%' . $search . '%')
                        ->orWhere('code', 'like', '%' . $search . '%');
                });
            })
            ->latest()
            ->get();

        return view('admin.courts', compact('categories', 'courts'));
    }

    // Menyimpan kategori olahraga baru
    public function storeCategory(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:categories,name'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        Category::create([
            'name' => $validated['name'],
            'is_active' => $request->boolean('is_active', true),
        ]);

        return back()->with('success', 'Kategori berhasil ditambahkan.');
    }

    // Memperbarui data kategori olahraga
    public function updateCategory(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:categories,name,' . $category->id],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $category->update([
            'name' => $validated['name'],
            'is_active' => $request->boolean('is_active', $category->is_active),
        ]);

        return back()->with('success', 'Kategori berhasil diperbarui.');
    }

    // Mengaktifkan atau menonaktifkan status kategori
    public function toggleCategory(Category $category)
    {
        $category->update([
            'is_active' => !$category->is_active,
        ]);

        return back()->with('success', 'Status kategori berhasil diubah.');
    }

    // Menyimpan data lapangan baru
    public function storeCourt(Request $request)
    {
        $validated = $request->validate([
            'category_id' => ['required', 'exists:categories,id'],
            'code' => ['required', 'string', 'max:255', 'unique:courts,code'],
            'name' => ['required', 'string', 'max:255'],
            'price_per_hour' => ['required', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        Court::create([
            'category_id' => $validated['category_id'],
            'code' => strtoupper(trim($validated['code'])),
            'name' => $validated['name'],
            'price_per_hour' => $validated['price_per_hour'],
            'is_active' => $request->boolean('is_active', true),
        ]);

        return back()->with('success', 'Lapangan berhasil ditambahkan.');
    }

    // Memperbarui data lapangan
    public function updateCourt(Request $request, Court $court)
    {
        $validated = $request->validate([
            'category_id' => ['required', 'exists:categories,id'],
            'code' => ['required', 'string', 'max:255', 'unique:courts,code,' . $court->id],
            'name' => ['required', 'string', 'max:255'],
            'price_per_hour' => ['required', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $court->update([
            'category_id' => $validated['category_id'],
            'code' => strtoupper(trim($validated['code'])),
            'name' => $validated['name'],
            'price_per_hour' => $validated['price_per_hour'],
            'is_active' => $request->boolean('is_active', $court->is_active),
        ]);

        return back()->with('success', 'Lapangan berhasil diperbarui.');
    }

    // Mengaktifkan atau menonaktifkan status lapangan
    public function toggleCourt(Court $court)
    {
        $court->update([
            'is_active' => !$court->is_active,
        ]);

        return back()->with('success', 'Status lapangan berhasil diubah.');
    }

    // Menampilkan daftar seluruh booking & pembayaran untuk admin beserta filter & tab status
    public function bookings(Request $request)
    {
        $allCourts = Court::orderBy('name')->get();

        $counts = [
            'all' => Booking::count(),
            'menunggu_cek' => Booking::whereHas('payment', fn($q) => $q->where('status', 'menunggu_cek'))->count(),
            'lunas' => Booking::whereHas('payment', fn($q) => $q->where('status', 'lunas'))->count(),
            'belum_bayar' => Booking::whereHas('payment', fn($q) => $q->where('status', 'belum_bayar'))->count(),
        ];

        $bookings = Booking::with(['user', 'court.category', 'payment'])
            ->when($request->payment_status, function ($query) use ($request) {
                $query->whereHas('payment', function ($q) use ($request) {
                    $q->where('status', $request->payment_status);
                });
            })
            ->when($request->date, function ($query) use ($request) {
                $query->whereDate('booking_date', $request->date);
            })
            ->when($request->court_id, function ($query) use ($request) {
                $query->where('court_id', $request->court_id);
            })
            ->when($request->search, function ($query) use ($request) {
                $search = $request->search;
                $query->where(function ($sub) use ($search) {
                    $sub->where('booking_code', 'like', '%' . $search . '%')
                        ->orWhereHas('user', function ($u) use ($search) {
                            $u->where('name', 'like', '%' . $search . '%');
                        })
                        ->orWhereHas('court', function ($c) use ($search) {
                            $c->where('name', 'like', '%' . $search . '%')
                                ->orWhere('code', 'like', '%' . $search . '%');
                        });
                });
            })
            ->latest()
            ->get();

        return view('admin.bookings', compact('bookings', 'counts', 'allCourts'));
    }

    // Menampilkan halaman detail booking dan status pembayaran di sisi admin
    public function bookingDetail(Booking $booking)
    {
        $booking->load(['user', 'court.category', 'payment']);

        return view('admin.booking-detail', compact('booking'));
    }

    // Memverifikasi bukti pembayaran yang valid oleh admin
    public function verifyPayment(Booking $booking)
    {
        Cache::forget('rejected_payment_booking_' . $booking->id);

        $payment = $booking->payment;
        if ($payment) {
            $payment->update([
                'status' => 'terverifikasi',
                'verified_at' => now(),
            ]);
        }

        try {
            if ($booking->status === 'ditolak') {
                $booking->update(['status' => 'aktif']);
            }
        } catch (\Throwable $e) {
            // Abaikan jika enum tidak memiliki nilai 'ditolak'
        }

        if (Schema::hasColumn('bookings', 'notes') && $booking->notes === 'Ditolak oleh admin') {
            $booking->update(['notes' => null]);
        }

        return back()->with('success', 'Pembayaran berhasil diverifikasi oleh admin.');
    }

    // Menolak pembayaran oleh admin agar di sisi user statusnya tampil Ditolak
    public function rejectPayment(Booking $booking)
    {
        // Tandai booking ini sebagai ditolak oleh admin (aman walau kolom status bertipe ENUM)
        Cache::forever('rejected_payment_booking_' . $booking->id, true);

        try {
            $booking->update(['status' => 'ditolak']);
        } catch (\Throwable $e) {
            // Abaikan jika enum bookings.status tidak menyertakan 'ditolak'
        }

        if (Schema::hasColumn('bookings', 'notes')) {
            $booking->update(['notes' => 'Ditolak oleh admin']);
        }

        $payment = $booking->payment;
        if ($payment) {
            try {
                $payment->update([
                    'status' => 'ditolak',
                    'verified_at' => null,
                ]);
            } catch (\Throwable $e) {
                $payment->update([
                    'status' => 'belum_bayar',
                    'verified_at' => null,
                ]);
            }

            if (Schema::hasColumn('payments', 'notes')) {
                $payment->update(['notes' => 'Ditolak oleh admin']);
            }
        }

        return back()->with('success', 'Pembayaran ditolak oleh admin. Di sisi pelanggan statusnya kini tampil Ditolak.');
    }

    // Mengubah status pembayaran menjadi lunas
    public function markAsPaid(Booking $booking)
    {
        Cache::forget('rejected_payment_booking_' . $booking->id);

        $payment = $booking->payment;
        if ($payment) {
            $payment->update([
                'status' => 'lunas',
                'verified_at' => $payment->verified_at ?? now(),
                'paid_at' => now(),
            ]);
        }

        try {
            if ($booking->status === 'ditolak') {
                $booking->update(['status' => 'aktif']);
            }
        } catch (\Throwable $e) {
            // Abaikan jika enum tidak memiliki nilai 'ditolak'
        }

        if (Schema::hasColumn('bookings', 'notes') && $booking->notes === 'Ditolak oleh admin') {
            $booking->update(['notes' => null]);
        }

        return back()->with('success', 'Status pembayaran berhasil diubah menjadi Lunas.');
    }

    // Menampilkan halaman laporan transaksi & pendapatan lunas
    public function reports(Request $request)
    {
        $paidBookings = Booking::with(['user', 'court.category', 'payment'])
            ->whereHas('payment', function ($q) {
                $q->where('status', 'lunas');
            })
            ->when($request->from_date, function ($q) use ($request) {
                $q->whereDate('booking_date', '>=', $request->from_date);
            })
            ->when($request->to_date, function ($q) use ($request) {
                $q->whereDate('booking_date', '<=', $request->to_date);
            })
            ->latest()
            ->get();

        $totalIncome = $paidBookings->sum('total_price');

        return view('admin.reports', compact('paidBookings', 'totalIncome'));
    }
}
