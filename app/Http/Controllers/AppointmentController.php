<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AppointmentController extends Controller
{
    // 1. Buat appointment baru
    public function store(Request $request)
    {
        $request->validate([
            'doctor_id' => 'required|exists:doctors,id',
            'scheduled_at' => 'required|date',
            'purpose' => 'required|string',
        ]);

        $appointment = Appointment::create([
            'user_id' => Auth::id(),
            'doctor_id' => $request->doctor_id,
            'scheduled_at' => $request->scheduled_at,
            'purpose' => $request->purpose,
            'status' => 'pending',
        ]);

        return response()->json([
            'message' => 'Appointment berhasil dibuat',
            'data' => $appointment,
        ], 201);
    }

    // 2. Lihat semua appointment milik user yang sedang login
    public function index()
    {
        $appointments = Appointment::with(['doctor'])
            ->where('user_id', Auth::id())
            ->orderBy('scheduled_at', 'asc')
            ->get();

        return response()->json($appointments);
    }

    public function update(Request $request, $id)
    {
        // Cari appointment
        $appointment = Appointment::findOrFail($id);

        // Pastikan appointment milik user yang sedang login
        if ($appointment->user_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized.'], 403);
        }

        // Validasi input
        $validated = $request->validate([
            'scheduled_at' => 'sometimes|date',
            'purpose' => 'sometimes|string',
            // status di sini opsional, misalnya jika user boleh set ulang ke pending
            // tapi biasanya user tidak bisa ubah status, hanya admin
        ]);

        // Update hanya field yang valid
        $appointment->update($validated);

        return response()->json([
            'message' => 'Appointment updated successfully.',
            'data' => $appointment
        ]);
    }



    // 3. Hapus appointment milik sendiri (cancel/batalkan)
    public function destroy($id)
    {
        $appointment = Appointment::findOrFail($id);

        if ($appointment->user_id !== Auth::id()) {
            return response()->json(['message' => 'Tidak diizinkan menghapus appointment ini'], 403);
        }

        $appointment->delete();

        return response()->json(['message' => 'Appointment berhasil dibatalkan/hapus']);
    }
}
