<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Material;
use App\Models\Media;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MaterialController extends Controller
{
    public function index(Request $request)
    {
        $query = Material::query();

        // Handle search
        if ($request->has('search')) {
            $search = $request->search;
            $query->where('title', 'like', "%{$search}%");
        }

        // Handle sorting
        $sort = $request->get('sort', 'created_at'); // default sort by created_at
        $direction = $request->get('direction', 'asc'); // default direction ascending

        // Validate sort field to prevent SQL injection
        $allowedSortFields = ['title', 'created_at'];
        if (in_array($sort, $allowedSortFields)) {
            $query->orderBy($sort, $direction);
        }

        $materials = $query->with('creator')->get();

        return view('admin.materials.index', compact('materials'));
    }

    public function create()
    {
        return view('admin.materials.create');
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'content' => 'required|string',
                'created_by' => 'required|exists:users,id',
                'cover_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);

            $material = Material::create([
                'title' => $validated['title'],
                'content' => $validated['content'],
                'created_by' => $validated['created_by'],
            ]);

            // Handle cover image upload
            if ($request->hasFile('cover_image')) {
                // Simpan file ke direktori images
                $path = $request->file('cover_image')->store('materials', 'images');
                
                Media::create([
                    'material_id' => $material->id,
                    'media_type' => 'image',
                    'media_url' => '/images/' . $path,
                    'media_description' => $request->title . ' - Cover Image'
                ]);
            }

            return redirect()->route('admin.materials.index')
                ->with('success', 'Materi berhasil ditambahkan.');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function edit(Material $material)
    {
        return view('admin.materials.edit', compact('material'));
    }

    public function update(Request $request, Material $material)
    {
        try {
            $request->validate([
                'title' => 'required|string|max:255',
                'content' => 'required|string',
                'cover_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);

            $material->update([
                'title' => $request->title,
                'content' => $request->content,
            ]);

            // Handle cover image replacement
            if ($request->hasFile('cover_image')) {
                // Delete existing cover image if any
                $existingMedia = $material->media()->where('media_type', 'image')->first();
                
                if ($existingMedia) {
                    // Delete file from storage
                    $path = $existingMedia->media_url;
                    
                    if (str_starts_with($path, '/images/')) {
                        $path = str_replace('/images/', '', $path);
                        if (Storage::disk('images')->exists($path)) {
                            Storage::disk('images')->delete($path);
                        }
                    } 
                    elseif (str_starts_with($path, '/storage/')) {
                        $path = str_replace('/storage/', '', $path);
                        if (Storage::disk('public')->exists($path)) {
                            Storage::disk('public')->delete($path);
                        }
                    }
                    
                    // Delete the media record
                    $existingMedia->delete();
                }
                
                // Upload new cover image
                $path = $request->file('cover_image')->store('materials', 'images');
                
                Media::create([
                    'material_id' => $material->id,
                    'media_type' => 'image',
                    'media_url' => '/images/' . $path,
                    'media_description' => $request->title . ' - Cover Image'
                ]);
            }

            return redirect()->route('admin.materials.index')
                ->with('success', 'Materi berhasil diperbarui.');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function destroy(Material $material)
    {
        try {
            // First, handle question_banks related to this material
            // Option 1: Delete the related question_banks records
            $material->questionBanks()->delete();
            // OR if you have a relationship for question bank configs
            if (method_exists($material, 'questionBankConfigs')) {
                $material->questionBankConfigs()->delete();
            }
            
            // Delete associated media files
            foreach($material->media as $media) {
                // Remove 'storage/' from the beginning of the path
                $path = str_replace('storage/', '', $media->media_url);
                if (Storage::disk('public')->exists($path)) {
                    Storage::disk('public')->delete($path);
                }
                $media->delete(); // Delete the media record explicitly
            }
            
            // Now it's safe to delete the material
            $material->delete();

            return redirect()->route('admin.materials.index')
                ->with('success', 'Materi berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->route('admin.materials.index')
                ->with('error', 'Gagal menghapus materi: ' . $e->getMessage());
        }
    }
    
    public function deleteMedia($id)
    {
        try {
            $media = Media::findOrFail($id);
            $materialId = $media->material_id;
            
            // Hapus file dari disk yang sesuai
            $path = $media->media_url;
            
            // Jika URL dimulai dengan '/images/'
            if (str_starts_with($path, '/images/')) {
                $path = str_replace('/images/', '', $path);
                if (Storage::disk('images')->exists($path)) {
                    Storage::disk('images')->delete($path);
                }
            } 
            // Jika URL dimulai dengan '/storage/'
            else if (str_starts_with($path, '/storage/')) {
                $path = str_replace('/storage/', '', $path);
                if (Storage::disk('public')->exists($path)) {
                    Storage::disk('public')->delete($path);
                }
            }
            
            // Hapus hanya record media, bukan materinya
            $media->delete();
            
            return redirect()->route('admin.materials.edit', $materialId)
                ->with('success', 'Media berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal menghapus media: ' . $e->getMessage());
        }
    }
}