@extends('layouts.app')

@section('title', 'Server Error')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-danger text-white">
                    <h4><i class="fas fa-exclamation-triangle"></i> Server Error</h4>
                </div>
                <div class="card-body">
                    <div class="alert alert-danger">
                        <h5>Terjadi kesalahan pada server</h5>
                        <p>Silakan coba lagi nanti atau hubungi administrator.</p>

                        @if(config('app.debug') && isset($error))
                        <hr>
                        <h6>Debug Information:</h6>
                        <pre class="text-small">{{ $error }}</pre>
                        @endif
                    </div>

                    <div class="mt-4">
                        <a href="{{ route('dashboard') }}" class="btn btn-primary">
                            <i class="fas fa-home"></i> Kembali ke Dashboard
                        </a>
                        <a href="javascript:history.back()" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Kembali
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
