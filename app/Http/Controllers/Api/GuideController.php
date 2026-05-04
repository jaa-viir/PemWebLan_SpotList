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
        $guides = Guide::latest()->paginate(5);
        return new GuideResource(true, 'List Data Guides', $guides);
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
            'slug'         => Str::slug($request->title), // Generate slug otomatis
            'content'      => $request->content,
            'user_id'      => auth('api')->id(), // Ambil ID Admin yang sedang login
        ]);

        return new GuideResource(true, 'Data Guide Berhasil Ditambahkan!', $guide);
    }

    // Menampilkan detail data guide
    public function show($id)
    {
        $guide = Guide::find($id);
        
        if (!$guide) {
            return response()->json(['message' => 'Data tidak ditemukan'], 404);
        }
        
        return new GuideResource(true, 'Detail Data Guide!', $guide);
    }

    // Mengubah data guide
    public function update(Request $request, $id)
    {
        // 1. Cari data guide berdasarkan ID
        $guide = Guide::find($id);
        
        if (!$guide) {
            return response()->json([
                'success' => false,
                'message' => 'Data Guide Tidak Ditemukan'
            ], 404);
        }

        // 2. Validasi input
        $validator = Validator::make($request->all(), [
            'banner_image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'title'        => 'required',
            'content'      => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // 3. Cek apakah ada file gambar baru yang diunggah
        if ($request->hasFile('banner_image')) {

            // Upload gambar baru ke folder storage/app/public/guides
            $image = $request->file('banner_image');
            $image->storeAs('public/guides', $image->hashName());

            // Hapus gambar lama dari storage agar tidak memenuhi server
            // Menggunakan getAttribute untuk menghindari error protected visibility
            $oldImage = $guide->getAttribute('banner_image');
            if ($oldImage) {
                Storage::delete('public/guides/' . $oldImage);
            }

            // Update data guide dengan gambar baru
            $guide->update([
                'banner_image' => $image->hashName(),
                'title'        => $request->title,
                'slug'         => Str::slug($request->title),
                'content'      => $request->content,
            ]);

        } else {
            
            // Update data guide tanpa mengganti gambar
            $guide->update([
                'title'   => $request->title,
                'slug'    => Str::slug($request->title),
                'content' => $request->content, 
            ]);
        }

        // 4. Return response menggunakan GuideResource
        return new GuideResource(true, 'Data Guide Berhasil Diubah!', $guide);
    }

    // Menghapus data guide
    public function destroy($id)
    {
        $guide = Guide::find($id);
        if (!$guide) {
            return response()->json(['message' => 'Data tidak ditemukan'], 404);
        }

        // Hapus gambar dari storage
        Storage::delete('public/guides/'.basename($guide->banner_image));
        // Atau jika menggunakan getAttribute untuk menghindari error protected visibility
        // $namaFileAsli = $guide->getAttributes()['banner_image'] ?? null;
        // if ($namaFileAsli) {
        //     Storage::delete('public/guides/' . $namaFileAsli);
        // }


        // Hapus data dari database
        $guide->delete();

        return response()->json([
            'success' => true,
            'message' => 'Data Guide Berhasil Dihapus!'
        ]);
    }
}