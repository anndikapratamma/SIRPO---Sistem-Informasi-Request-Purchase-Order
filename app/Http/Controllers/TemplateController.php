<?php

namespace App\Http\Controllers;

use App\Models\Template;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class TemplateController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            // Debug - Check if user is authenticated
            if (!Auth::check()) {
                return redirect()->route('login')->with('error', 'Anda harus login terlebih dahulu');
            }

            $user = Auth::user();

            // Debug - Check user role
            Log::info('Template index accessed by user: ' . $user->name . ' with role: ' . ($user->role ?? 'no role'));

            // Admin bisa melihat semua template, User hanya template aktif
            if ($user->role === 'admin') {
                $templates = Template::latest()->get();
                return view('templates.index-working', compact('templates'));
            } else {
                // User hanya melihat template aktif
                $templates = Template::active()->latest()->get();
                return view('templates.index-working', compact('templates'));
            }
        } catch (\Exception $e) {
            Log::error('Error in TemplateController@index: ' . $e->getMessage());
            return response()->view('errors.500', ['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Hanya admin yang bisa membuat template
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Unauthorized');
        }

        return view('templates.create-working');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Hanya admin yang bisa menyimpan template
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Unauthorized');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'excel_file' => 'required|file|mimes:xlsx,xls|max:5120' // Max 5MB
        ]);

        $file = $request->file('excel_file');
        $filename = time() . '_' . $file->getClientOriginalName();
        $filePath = $file->storeAs('templates', $filename, 'public');

        try {
            // Use raw SQL without created_by field
            DB::table('templates')->insert([
                'name' => $request->name,
                'description' => $request->description,
                'file_path' => $filePath,
                'original_filename' => $file->getClientOriginalName(),
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            return redirect()->route('templates.index')->with('success', 'Template berhasil ditambahkan!');
        } catch (\Exception $e) {
            // Delete uploaded file if database insertion fails
            if (Storage::disk('public')->exists($filePath)) {
                Storage::disk('public')->delete($filePath);
            }

            Log::error('Error creating template: ' . $e->getMessage());
            return back()->withErrors(['Error: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            // Cari template berdasarkan ID
            $template = Template::find($id);

            if (!$template) {
                Log::error('Template not found with ID: ' . $id);
                return redirect()->route('templates.index')->with('error', 'Template tidak ditemukan');
            }

            // User hanya bisa lihat template aktif, Admin bisa lihat semua
            if (Auth::user()->role !== 'admin' && !$template->is_active) {
                abort(404, 'Template tidak tersedia');
            }

            Log::info('Showing template: ' . $template->name . ' (ID: ' . $template->id . ')');
            return view('templates.show', compact('template'));

        } catch (\Exception $e) {
            Log::error('Error in TemplateController@show: ' . $e->getMessage());
            return redirect()->route('templates.index')->with('error', 'Error: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        // Hanya admin yang bisa mengedit template
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Unauthorized');
        }

        try {
            // Cari template berdasarkan ID
            $template = Template::find($id);

            if (!$template) {
                Log::error('Template not found for editing with ID: ' . $id);
                return redirect()->route('templates.index')->with('error', 'Template tidak ditemukan');
            }

            Log::info('Editing template: ' . $template->name . ' (ID: ' . $template->id . ')');

            return view('templates.edit', compact('template'));
        } catch (\Exception $e) {
            Log::error('Error loading edit view: ' . $e->getMessage());
            return redirect()->route('templates.index')->with('error', 'Error: ' . $e->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Template $template)
    {
        // Hanya admin yang bisa update template
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Unauthorized');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'excel_file' => 'nullable|file|mimes:xlsx,xls|max:5120',
            'is_active' => 'boolean'
        ]);

        $updateData = [
            'name' => $request->name,
            'description' => $request->description,
            'is_active' => $request->has('is_active')
        ];

        // Jika ada file baru, replace file lama
        if ($request->hasFile('excel_file')) {
            // Hapus file lama
            if (Storage::disk('public')->exists($template->file_path)) {
                Storage::disk('public')->delete($template->file_path);
            }

            // Upload file baru
            $file = $request->file('excel_file');
            $filename = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('templates', $filename, 'public');

            $updateData['file_path'] = $filePath;
            $updateData['original_filename'] = $file->getClientOriginalName();
        }

        $template->update($updateData);

        return redirect()->route('templates.index')->with('success', 'Template berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Template $template)
    {
        // Hanya admin yang bisa hapus template
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Unauthorized');
        }

        // Hapus file dari storage
        if (Storage::disk('public')->exists($template->file_path)) {
            Storage::disk('public')->delete($template->file_path);
        }

        $template->delete();

        return redirect()->route('templates.index')->with('success', 'Template berhasil dihapus!');
    }

    /**
     * Download template file
     */
    public function download(Template $template)
    {
        $filePath = storage_path('app/public/' . $template->file_path);

        if (!file_exists($filePath)) {
            abort(404, 'File tidak ditemukan');
        }

        return response()->download($filePath, $template->original_filename);
    }

    /**
     * API untuk mendapatkan template aktif (untuk dropdown user)
     */
    public function getActiveTemplates()
    {
        $templates = Template::active()->select('id', 'name', 'description')->get();
        return response()->json($templates);
    }
}
