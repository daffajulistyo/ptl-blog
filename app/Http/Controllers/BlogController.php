<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    public function index()
    {
        $blogs = Blog::latest()->paginate(10);
        return view('blog.index', compact('blogs'));
    }

    public function create()
    {
        return view('blog.create');
    }

    public function store(Request $request)
    {
        // Validasi input
        $request->validate([
            'image' => 'required|image|mimes:png,jpg,jpeg|max:2048', // Maksimal 2MB
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        // Ambil file gambar dari request
        $image = $request->file('image');

        // Debugging: Cek apakah file gambar ada
        if (!$image) {
            dd('Gambar tidak ditemukan!');
        }


        // Simpan gambar dan ambil nama file
        $imageName = $image->hashName(); // Menghasilkan nama file unik
        $imagePath = $image->storeAs('public/blogs', $imageName); // Simpan gambar ke storage

        // Debugging: Cek apakah gambar berhasil disimpan
        if (!$imagePath) {
            dd('Gambar gagal disimpan!');
        }

        // Buat entri blog
        $blog = Blog::create([
            'image' => $imageName,
            'title' => $request->title,
            'content' => $request->content,
        ]);

        // Redirect dengan pesan sukses atau error
        if ($blog) {
            return redirect()->route('blog.index')->with(['success' => 'Data Berhasil Disimpan!']);
        } else {
            return redirect()->route('blog.index')->with(['error' => 'Data Gagal Disimpan!']);
        }
    }
}
