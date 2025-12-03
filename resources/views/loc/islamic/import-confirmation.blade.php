{{-- resources/views/loc/islamic/import-confirmation.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h3 class="card-title">
                        <i class="fas fa-check-circle"></i> Import Confirmation
                    </h3>
                </div>
                <div class="card-body">
                    @if(session('success'))
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i> {{ session('success') }}
                    </div>
                    @endif
                    
                    @if(session('error'))
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle"></i> {{ session('error') }}
                    </div>
                    @endif
                    
                    <div class="text-center py-5">
                        <i class="fas fa-cloud-upload-alt fa-4x text-primary mb-4"></i>
                        <h4>Import Complete!</h4>
                        <p class="text-muted">Your selected records have been imported from Library of Congress.</p>
                        
                        <div class="row mt-4">
                            <div class="col-md-4">
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <h2 class="text-success">{{ session('imported', 0) }}</h2>
                                        <p class="mb-0">Imported</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <h2 class="text-warning">{{ session('skipped', 0) }}</h2>
                                        <p class="mb-0">Skipped</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <h2 class="text-danger">{{ session('failed', 0) }}</h2>
                                        <p class="mb-0">Failed</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mt-5">
                            <a href="{{ route('items.index', ['source' => 'loc-oai']) }}" class="btn btn-primary">
                                <i class="fas fa-eye"></i> View Imported Items
                            </a>
                            <a href="{{ route('oai.harvest.history') }}" class="btn btn-info">
                                <i class="fas fa-history"></i> View Harvest History
                            </a>
                            <a href="{{ route('loc.islamic.index') }}" class="btn btn-success">
                                <i class="fas fa-search"></i> Import More
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection