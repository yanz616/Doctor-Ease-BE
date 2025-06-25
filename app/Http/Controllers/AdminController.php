<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Doctor;
use App\Models\Appointment;

class AdminController extends Controller
{
    // Ambil semua doctor
    public function doctors()
    {
        return response()->json(Doctor::all());
    }

    // Tambah doctor
    public function storeDoctor(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string',
            'specialization' => 'required|string',
            'photo_url' => 'nullable|url',
        ]);

        $doctor = Doctor::create($data);
        return response()->json($doctor, 201);
    }

    // Update doctor
    public function updateDoctor(Request $request, $id)
    {
        $doctor = Doctor::findOrFail($id);

        $data = $request->validate([
            'name' => 'sometimes|string',
            'specialization' => 'sometimes|string',
            'photo_url' => 'nullable|url',
        ]);

        $doctor->update($data);
        return response()->json($doctor);
    }

    // Hapus doctor
    public function destroyDoctor($id)
    {
        $doctor = Doctor::findOrFail($id);
        $doctor->delete();
        return response()->json(['message' => 'Doctor deleted']);
    }

    // Ambil semua appointment (sudah ada sebelumnya)
    public function appointments()
    {
        return response()->json(Appointment::with('user', 'doctor')->get());
    }

    // Update appointment (status, jadwal, dll)
    public function updateAppointment(Request $request, $id)
    {
        $appointment = Appointment::findOrFail($id);

        $data = $request->validate([
            'scheduled_at' => 'sometimes|date',
            'purpose' => 'sometimes|string',
            'status' => 'sometimes|in:pending,approved,canceled,completed'
        ]);

        $appointment->update($data);
        return response()->json($appointment);
    }

    // Hapus appointment
    public function destroyAppointment($id)
    {
        $appointment = Appointment::findOrFail($id);
        $appointment->delete();
        return response()->json(['message' => 'Appointment deleted']);
    }

    // Ambil semua user
    public function users()
    {
        return response()->json(User::all());
    }

    // Update user
    public function updateUser(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $data = $request->validate([
            'name' => 'sometimes|string',
            'email' => 'sometimes|email|unique:users,email,' . $id,
            'is_admin' => 'sometimes|boolean'
        ]);

        $user->update($data);
        return response()->json($user);
    }

    // Hapus user
    public function destroyUser($id)
    {
        try {
            $user = User::findOrFail($id);
            $user->delete();
            return response()->json(['message' => 'User deleted']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'User not found or failed to delete'], 500);
        }
    }

}
