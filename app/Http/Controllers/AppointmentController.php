<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AppointmentController extends Controller
{
    /**
     * Menampilkan semua janji temu.
     * - Admin bisa melihat semua.
     * - Pasien hanya melihat miliknya sendiri.
     */
    public function index()
    {
        $user = Auth::user();

        if ($user->is_admin ?? false) {
            return Appointment::with('doctor', 'user')->get();
        }

        return Appointment::with('doctor')
            ->where('user_id', $user->id)
            ->orderBy('scheduled_at', 'asc')
            ->get();
    }

    /**
     * Membuat janji temu baru.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'doctor_id'    => 'required|exists:doctors,id',
            'scheduled_at' => 'required|date|after_or_equal:today',
            'purpose'      => 'required|string|max:255',
            'status'       => 'nullable|in:pending,approved,canceled,completed',
        ]);

        $appointment = Appointment::create([
            'user_id'      => Auth::id(),
            'doctor_id'    => $validated['doctor_id'],
            'scheduled_at' => $validated['scheduled_at'],
            'purpose'      => $validated['purpose'],
            'status'       => $validated['status'] ?? 'pending',
        ]);

        return response()->json($appointment, 201);
    }
    
    /**
     * Menghapus janji temu.
     */
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
