@extends('layouts.app')

@section('title', 'Manajemen Template Excel')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3>Manajemen Template Excel</h3>
        <a href="{{ route('templates.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Tambah Template
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-bordered">
                    <thead class="table-dark">
                        <tr>
                            <th>Nama Template</th>
                            <th>Deskripsi</th>
                            <th>File</th>
                            <th>Status</th>
                            <th>Dibuat</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($templates as $template)
                            <tr>
                                <td>
                                    <strong>{{ $template->name }}</strong>
                                </td>
                                <td>
                                    {{ $template->description ?? '-' }}
                                </td>
                                <td>
                                    <i class="fas fa-file-excel text-success"></i>
                                    {{ $template->original_filename }}
                                </td>
                                <td>
                                    <span class="badge {{ $template->is_active ? 'bg-success' : 'bg-danger' }}">
                                        {{ $template->is_active ? 'Aktif' : 'Nonaktif' }}
                                    </span>
                                </td>
                                <td>
                                    {{ $template->created_at->format('d/m/Y H:i') }}
                                    <br>
                                    <small class="text-muted">
                                        oleh {{ $template->creator->name }}
                                    </small>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('templates.download', $template) }}"
                                           class="btn btn-sm btn-info"
                                           title="Download Template">
                                            <i class="fas fa-download"></i>
                                        </a>
                                        <a href="{{ route('templates.edit', $template) }}"
                                           class="btn btn-sm btn-warning"
                                           title="Edit Template">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('templates.destroy', $template) }}"
                                              method="POST"
                                              class="d-inline"
                                              onsubmit="return confirm('Yakin ingin menghapus template ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="btn btn-sm btn-danger"
                                                    title="Hapus Template">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">
                                    <i class="fas fa-folder-open fa-2x mb-2"></i>
                                    <br>
                                    Belum ada template tersimpan
                                    <br>
                                    <a href="{{ route('templates.create') }}" class="btn btn-primary btn-sm mt-2">
                                        Tambah Template Pertama
                                    </a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
