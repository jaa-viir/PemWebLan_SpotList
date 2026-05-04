<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Spot;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class SpotController extends Controller
{
    // PUBLIC: list published spots
    public function index()
    {
        $spots = Spot::where('status', 'open')
            ->latest()
            ->paginate(10)
            ->through(fn($spot) => [
                'id'         => $spot->id,
                'title'      => $spot->title,
                'thumbnail'  => $spot->thumbnail,
                'category'   => $spot->category,
                'location'   => $spot->location,
                'event_date' => $spot->event_date,
                'price'      => $spot->price,
                'status'     => $spot->status,
            ]);

        return response()->json(['success' => true, 'data' => $spots]);
    }

    // PUBLIC: detail single spot
    public function show(int $id)
    {
        $spot = Spot::find($id);
        if (!$spot) return response()->json(['success' => false, 'message' => 'Spot tidak ditemukan'], 404);
        return response()->json(['success' => true, 'data' => $spot]);
    }

    // ADMIN: list all spots (including drafts/closed)
    public function adminIndex()
    {
        $spots = Spot::latest()
            ->paginate(10)
            ->through(fn($spot) => [
                'id'                => $spot->id,
                'title'             => $spot->title,
                'thumbnail'         => $spot->thumbnail,
                'category'          => $spot->category,
                'location'          => $spot->location,
                'event_date'        => $spot->event_date,
                'price'             => $spot->price,
                'status'            => $spot->status,
                'participant_limit' => $spot->participant_limit,
            ]);

        return response()->json(['success' => true, 'data' => $spots]);
    }

    // ADMIN: create spot
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title'             => 'required|string',
            'thumbnail'         => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'description'       => 'required',
            'category'          => 'required|in:Competition,Convention,Concert,Workshop,Exhibition,Festival,Community Meetup',
            'location'          => 'required|string',
            'event_date'        => 'required|date',
            'organizer'         => 'required|string',
            'price'             => 'required|integer',
            'participant_limit' => 'nullable|integer',
            'registration_link' => 'nullable|url',
            'status'            => 'required|in:open,closed,draft',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors'  => $validator->errors()
            ], 422);
        }

        $image = $request->file('thumbnail');
        $image->storeAs('public/spots', $image->hashName());

        $spot = Spot::create([
            ...$request->except('thumbnail'),
            'thumbnail' => $image->hashName(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Spot berhasil ditambahkan',
            'data'    => $spot
        ], 201);
    }

    // ADMIN: update spot
    public function update(Request $request, $id)
    {
        $spot = Spot::find($id);
        if (!$spot) return response()->json(['success' => false, 'message' => 'Spot tidak ditemukan'], 404);

        $request->validate([
            'thumbnail'         => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'title'             => 'sometimes|required|string',
            'category'          => 'sometimes|required|string',
            'location'          => 'sometimes|required|string',
            'event_date'        => 'sometimes|required|date',
            'organizer'         => 'sometimes|required|string',
            'price'             => 'sometimes|required|integer',
            'participant_limit' => 'nullable|integer',
            'registration_link' => 'nullable|url',
            'status'            => 'sometimes|required|in:open,closed,draft',
        ]);

        $data = $request->except('thumbnail');

        if ($request->hasFile('thumbnail')) {
            $image = $request->file('thumbnail');
            $image->storeAs('public/spots', $image->hashName());
            Storage::delete('public/spots/' . basename($spot->getRawOriginal('thumbnail')));
            $data['thumbnail'] = $image->hashName();
        }

        $spot->update($data);
        return response()->json(['success' => true, 'message' => 'Spot berhasil diperbarui', 'data' => $spot]);
    }

    // ADMIN: delete spot
    public function destroy($id)
    {
        $spot = Spot::find($id);
        if (!$spot) return response()->json(['success' => false, 'message' => 'Spot tidak ditemukan'], 404);

        Storage::delete('public/spots/' . basename($spot->getRawOriginal('thumbnail')));
        $spot->delete();

        return response()->json(['success' => true, 'message' => 'Spot berhasil dihapus']);
    }
}
