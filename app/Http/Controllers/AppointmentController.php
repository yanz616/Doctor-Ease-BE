<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AppointmentController extends Controller
{
    // Ambil semua appointment milik user
    public function index()
    {
        $user = Auth::user();

        // Admin bisa melihat semua appointment
        if ($user->is_admin ?? false) {
            return Appointment::with('doctor', 'user')->get();
        }

        // Pasien hanya melihat miliknya
        return Appointment::with('doctor')
            ->where('user_id', $user->id)
            ->orderBy('scheduled_at', 'asc')
            ->get();
    }

    // Buat appointment baru
    public function store(Request $request)
    {
        $request->validate([
            'doctor_id' => 'required|exists:doctors,id',
            'scheduled_at' => 'required|date',
        ]);

        $appointment = Appointment::create([
            'user_id' => Auth::id(),
            'doctor_id' => $request->doctor_id,
            'scheduled_at' => $request->scheduled_at,
            'status' => 'pending', //test
        ]);

        return response()->json($appointment, 201);
    }

    // Ubah appointment (status atau jadwal)
    public function update(Request $request, $id)
    {
        $appointment = Appointment::findOrFail($id);
        $user = Auth::user();

        if ($user->id !== $appointment->user_id && !($user->is_admin ?? false)) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'scheduled_at' => 'nullable|date',
            'status' => 'nullable|in:pending,approved,canceled,completed',
        ]);

        if ($request->filled('scheduled_at')) {
            $appointment->scheduled_at = $request->scheduled_at;
        }

        if ($request->filled('status')) {
            $appointment->status = $request->status;
        }

        $appointment->save();

        return response()->json($appointment);
    }

    // Hapus appointment
    public function destroy($id)
    {
        $appointment = Appointment::findOrFail($id);
        $user = Auth::user();

        if ($user->id !== $appointment->user_id && !($user->is_admin ?? false)) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $appointment->delete();

        return response()->json(['message' => 'Appointment deleted']);
    }
}
