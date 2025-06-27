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
