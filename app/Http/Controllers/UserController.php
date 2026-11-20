<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Category;
use App\Models\Court;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    // Menampilkan katalog lapangan aktif dan fitur pencarian untuk pelanggan
    public function dashboard(Request $request)
    {
        $categories = Category::where('is_active', true)
            ->when($request->cat_search, fn($q) => $q->where('name', 'like', "%{$request->cat_search}%"))
            ->get();

        $courts = Court::with('category')
            ->where('is_active', true)
            ->whereHas('category', fn($q) => $q->where('is_active', true))
            ->when($request->category_id, fn($q) => $q->where('category_id', $request->category_id))
            ->when($request->search, fn($q) => $q->where(function ($sub) use ($request) {
                $sub->where('name', 'like', "%{$request->search}%")
                    ->orWhere('code', 'like', "%{$request->search}%");
            }))
            ->get();

        $myBookingsCount = Booking::where('user_id', Auth::id())->count();

        return view('user.dashboard', compact('categories', 'courts', 'myBookingsCount'));
    }

    // Menampilkan detail lapangan, jadwal terbooking pada tanggal tertentu, dan form pemesanan
    public function courtDetail(Request $request, Court $court)
    {
        abort_if(!$court->is_active, 404);

        $selectedDate = $request->date ?? now()->toDateString();

        $existingBookings = Booking::where('court_id', $court->id)
            ->whereDate('booking_date', $selectedDate)
            ->where('status', 'aktif')
            ->orderBy('start_time')
            ->get();

        return view('user.court-detail', compact('court', 'selectedDate', 'existingBookings'));
    }

    // Memproses penyimpanan booking baru beserta validasi jadwal dan metode pembayaran (QRIS / Cash)
    public function storeBooking(Request $request, Court $court)
    {
        // Validasi input: proof_image wajib jika payment_method = qris, boleh kosong jika cash[cite: 3]
        $request->validate([
            'booking_date' => 'required|date|after_or_equal:today',
            'start_time' => 'required',
            'end_time' => 'required',
            'payment_method' => 'required|in:qris,cash',
            'proof_image' => 'required_if:payment_method,qris|nullable|image|mimes:jpg,jpeg,png|max:2048',
        ], [
            'booking_date.after_or_equal' => 'Tanggal booking tidak boleh di masa lalu.',
            'payment_method.required' => 'Pilih metode pembayaran terlebih dahulu (QRIS atau Cash).',
            'payment_method.in' => 'Metode pembayaran harus QRIS atau Cash.',
            'proof_image.required_if' => 'Foto bukti pembayaran wajib diunggah untuk metode QRIS.',
            'proof_image.image' => 'Bukti pembayaran harus berupa file gambar (JPG/PNG).',
            'proof_image.mimes' => 'Format gambar harus JPG, JPEG, atau PNG.',
            'proof_image.max' => 'Ukuran gambar maksimal 2MB.',
        ]);

        // Mengambil angka jam dari format 'HH:00'
        $startHour = (int) substr($request->start_time, 0, 2);
        $endHour = (int) substr($request->end_time, 0, 2);

        // Validasi 1: Jam selesai harus lebih besar dari jam mulai
        if ($endHour <= $startHour) {
            return back()->withErrors(['time_error' => 'Jam selesai harus lebih besar daripada jam mulai.'])->withInput();
        }

        // Validasi 2: Memastikan waktu berada di dalam jam operasional (07:00 - 22:00 WIB)
        if ($startHour < 7 || $endHour > 22) {
            return back()->withErrors(['time_error' => 'Pemesanan ditolak: di luar jam operasional (07:00 - 22:00 WIB).'])->withInput();
        }

        // Format waktu ke standar 'HH:00:00'
        $startFormatted = sprintf('%02d:00:00', $startHour);
        $endFormatted = sprintf('%02d:00:00', $endHour);

        // Validasi 3: Mencegah bentrok jadwal dengan booking aktif yang beririsan
        $conflict = Booking::where('court_id', $court->id)
            ->whereDate('booking_date', $request->booking_date)
            ->where('status', 'aktif')
            ->where(function ($q) use ($startFormatted, $endFormatted) {
                $q->where('start_time', '<', $endFormatted)
                    ->where('end_time', '>', $startFormatted);
            })
            ->exists();

        if ($conflict) {
            return back()->withErrors(['time_error' => 'Jadwal lapangan bertabrakan dengan booking aktif pada jam tersebut.'])->withInput();
        }

        // Validasi 4: Menghitung durasi dan total biaya di sisi server (durasi x tarif per jam)
        $duration = $endHour - $startHour;
        $totalPrice = $duration * $court->price_per_hour;

        // Membuat kode booking unik
        $bookingCode = 'RS-' . now()->format('ymd') . '-' . str_pad(Booking::count() + 1, 3, '0', STR_PAD_LEFT);

        // Menyimpan data booking utama
        $booking = Booking::create([
            'booking_code' => $bookingCode,
            'user_id' => Auth::id(),
            'court_id' => $court->id,
            'booking_date' => $request->booking_date,
            'start_time' => $startFormatted,
            'end_time' => $endFormatted,
            'duration_hours' => $duration,
            'total_price' => $totalPrice,
            'status' => 'aktif',
        ]);

        // Menentukan bukti bayar & status pembayaran sesuai metode yang dipilih[cite: 3]
        $proofPath = null;
        $payStatus = 'belum_bayar';

        if ($request->payment_method === 'qris' && $request->hasFile('proof_image')) {
            // Simpan bukti bayar QRIS ke storage/app/public/payment_proofs
            $proofPath = $request->file('proof_image')->store('payment_proofs', 'public');
            // Status awal menunggu cek bukti oleh admin
            $payStatus = 'menunggu_cek';
        } elseif ($request->payment_method === 'cash') {
            // Untuk cash: tanpa upload bukti, status menunggu pembayaran/verifikasi admin di kasir[cite: 3]
            $payStatus = 'belum_bayar';
        }

        Payment::create([
            'booking_id' => $booking->id,
            'amount' => $totalPrice,
            'proof_image' => $proofPath,
            'status' => $payStatus,
            'uploaded_at' => $proofPath ? now() : null,
        ]);

        return redirect()->route('user.booking.detail', $booking)
            ->with('success', "Booking berhasil dibuat! Kode Booking Anda: {$bookingCode}");
    }

    // Menampilkan daftar booking milik pelanggan yang sedang login
    public function myBookings()
    {
        $bookings = Booking::with(['court.category', 'payment'])
            ->where('user_id', Auth::id())
            ->latest()
            ->get();

        return view('user.bookings', compact('bookings'));
    }

    // Menampilkan detail booking dan status pembayaran milik pelanggan sendiri
    public function myBookingDetail(Booking $booking)
    {
        abort_if($booking->user_id !== Auth::id(), 403);

        $booking->load(['court.category', 'payment']);
        return view('user.booking-detail', compact('booking'));
    }
}
