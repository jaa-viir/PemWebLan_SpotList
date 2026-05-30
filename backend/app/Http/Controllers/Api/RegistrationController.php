<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Registration;
use App\Models\Spot;
use Illuminate\Support\Facades\Auth;

class RegistrationController extends Controller
{
    // MEMBER: register for a spot
    public function register($spot_id)
    {
        $spot = Spot::find($spot_id);

        if (!$spot)
            return response()->json(['success' => false, 'message' => 'Spot tidak ditemukan'], 404);

        if ($spot->status !== 'open')
            return response()->json(['success' => false, 'message' => 'Registrasi event ini sudah ditutup'], 403);

        $already = Registration::where('user_id', Auth::id())->where('spot_id', $spot_id)->exists();
        if ($already)
            return response()->json(['success' => false, 'message' => 'Kamu sudah mendaftar event ini'], 409);

        $registration = Registration::create([
            'user_id' => Auth::id(),
            'spot_id' => $spot_id,
            'status'  => 'pending',
        ]);

        return response()->json(['success' => true, 'message' => 'Berhasil mendaftar event', 'data' => $registration], 201);
    }

    // MEMBER: view my registrations
    public function myRegistrations()
    {
        $registrations = Registration::with('spot')
            ->where('user_id', Auth::id())
            ->latest()
            ->get();

        return response()->json(['success' => true, 'data' => $registrations]);
    }

    // MEMBER: cancel my registration
    public function cancel($spot_id)
    {
        $registration = Registration::where('user_id', Auth::id())->where('spot_id', $spot_id)->first();

        if (!$registration)
            return response()->json(['success' => false, 'message' => 'Registrasi tidak ditemukan'], 404);

        $registration->update(['status' => 'cancelled']);

        return response()->json(['success' => true, 'message' => 'Registrasi dibatalkan']);
    }

    // ADMIN: view all registrations for a spot
    public function spotRegistrations($spot_id)
    {
        $spot = Spot::find($spot_id);
        if (!$spot)
            return response()->json(['success' => false, 'message' => 'Spot tidak ditemukan'], 404);

        $registrations = Registration::with('user')->where('spot_id', $spot_id)->get();

        return response()->json(['success' => true, 'data' => $registrations]);
    }
}
