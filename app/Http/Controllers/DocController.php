<?php

namespace App\Http\Controllers;

use App\Models\Doc;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DocController extends Controller
{
    public function index()
    {
        try {
            $docs = Doc::latest()->get();
            return view('docs', compact('docs'));
        } catch (\Exception $e) {
            return view('docs', ['docs' => collect()])
                ->with('error', 'Error al cargar los documentos: ' . $e->getMessage());
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'file'  => 'required|mimes:pdf|max:5120',
        ]);

        try {
            $path = $request->file('file')->store('docs', 'public');

            Doc::create([
                'title' => $request->title,
                'path'  => $path,
            ]);

            return redirect()->route('docs.index')->with('success', 'Documento PDF subido correctamente.');
        } catch (\Exception $e) {
            return redirect()->route('docs.index')->with('error', 'Error al subir el documento: ' . $e->getMessage());
        }
    }

    public function update(Request $request, Doc $doc)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'file'  => 'nullable|mimes:pdf|max:5120',
        ]);

        try {
            $data = ['title' => $request->title];

            if ($request->hasFile('file')) {
                if ($doc->path && Storage::disk('public')->exists($doc->path)) {
                    Storage::disk('public')->delete($doc->path);
                }
                $data['path'] = $request->file('file')->store('docs', 'public');
            }

            $doc->update($data);

            return redirect()->route('docs.index')->with('success', 'Documento actualizado correctamente.');
        } catch (\Exception $e) {
            return redirect()->route('docs.index')->with('error', 'Error al actualizar el documento: ' . $e->getMessage());
        }
    }

    public function destroy(Doc $doc)
    {
        try {
            if ($doc->path && Storage::disk('public')->exists($doc->path)) {
                Storage::disk('public')->delete($doc->path);
            }

            $doc->delete();

            return redirect()->route('docs.index')->with('success', 'Documento eliminado correctamente.');
        } catch (\Exception $e) {
            return redirect()->route('docs.index')->with('error', 'Error al eliminar el documento: ' . $e->getMessage());
        }
    }
}