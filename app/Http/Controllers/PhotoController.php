<?php

namespace App\Http\Controllers;

use App\Models\Photo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PhotoController extends Controller
{
    public function index()
    {
        try {
            $photos = Photo::latest()->get();
            return view('photos', compact('photos'));
        } catch (\Exception $e) {
            return view('photos', ['photos' => collect()])
                ->with('error', 'Error al cargar las imágenes: ' . $e->getMessage());
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'file'  => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048', // Máx 2MB
        ]);

        try {
            $path = $request->file('file')->store('photos', 'public');

            Photo::create([
                'title' => $request->title,
                'path'  => $path,
            ]);

            return redirect()->route('photos.index')->with('success', 'Imagen subida correctamente.');
        } catch (\Exception $e) {
            return redirect()->route('photos.index')->with('error', 'Error al subir la imagen: ' . $e->getMessage());
        }
    }

    public function update(Request $request, Photo $photo)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'file'  => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048', // Máx 2MB
        ]);

        try {
            $data = ['title' => $request->title];

            if ($request->hasFile('file')) {
                if ($photo->path && Storage::disk('public')->exists($photo->path)) {
                    Storage::disk('public')->delete($photo->path);
                }
                $data['path'] = $request->file('file')->store('photos', 'public');
            }

            $photo->update($data);

            return redirect()->route('photos.index')->with('success', 'Imagen actualizada correctamente.');
        } catch (\Exception $e) {
            return redirect()->route('photos.index')->with('error', 'Error al actualizar la imagen: ' . $e->getMessage());
        }
    }

    public function destroy(Photo $photo)
    {
        try {
            if ($photo->path && Storage::disk('public')->exists($photo->path)) {
                Storage::disk('public')->delete($photo->path);
            }

            $photo->delete();

            return redirect()->route('photos.index')->with('success', 'Imagen eliminada correctamente.');
        } catch (\Exception $e) {
            return redirect()->route('photos.index')->with('error', 'Error al eliminar la imagen: ' . $e->getMessage());
        }
    }
}