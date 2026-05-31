<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Guide;
use App\Http\Resources\GuideResource;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class GuideController extends Controller
{
    // Method untuk menampilkan semua data guides
    public function index()
    {
        $guides = Guide::with('user')->latest()->get();
        
        return response()->json([
            'success' => true,
            'message' => 'List Data Guides',
            'data'    => $guides
        ], 200);
    }

    public function store(Request $request)
    {
        // Validasi input
        $validator = Validator::make($request->all(), [
            'banner_image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'title'        => 'required',
            'content'      => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Upload image
        $image = $request->file('banner_image');
        $image->storeAs('public/guides', $image->hashName());

        // Simpan data guide baru
        $guide = Guide::create([
            'banner_image' => $image->hashName(),
            'title'        => $request->title,
            'slug'         => Str::slug($request->title), 
            'content'      => $request->content,
            'user_id'      => auth('api')->id(), 
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Data Guide Berhasil Ditambahkan!',
            'data'    => $guide
        ], 201);
    }

    // Menampilkan detail data guide
    public function show($id)
    {
        $guide = Guide::with('user')->find($id);
        
        if (!$guide) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak ditemukan'
            ], 404);
        }
        
        return response()->json([
            'success' => true,
            'message' => 'Detail Data Guide!',
            'data'    => $guide
        ], 200);
    }

    // Mengubah data guide
    public function update(Request $request, $id)
    {
        $guide = Guide::find($id);
        
        if (!$guide) {
            return response()->json([
                'success' => false,
                'message' => 'Data Guide Tidak Ditemukan'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'banner_image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'title'        => 'required',
            'content'      => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        if ($request->hasFile('banner_image')) {
            $image = $request->file('banner_image');
            $image->storeAs('public/guides', $image->hashName());

            $oldImage = $guide->getAttribute('banner_image');
            if ($oldImage) {
                Storage::delete('public/guides/' . $oldImage);
            }

            $guide->update([
                'banner_image' => $image->hashName(),
                'title'        => $request->title,
                'slug'         => Str::slug($request->title),
                'content'      => $request->content,
            ]);

        } else {
            $guide->update([
                'title'   => $request->title,
                'slug'    => Str::slug($request->title),
                'content' => $request->content, 
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Data Guide Berhasil Diubah!',
            'data'    => $guide
        ], 200);
    }

    // Menghapus data guide
    public function destroy($id)
    {
        $guide = Guide::find($id);
        if (!$guide) {
            return response()->json(['message' => 'Data tidak ditemukan'], 404);
        }

        Storage::delete('public/guides/'.basename($guide->banner_image));
        $guide->delete();

        return response()->json([
            'success' => true,
            'message' => 'Data Guide Berhasil Dihapus!'
        ]);
    }
}