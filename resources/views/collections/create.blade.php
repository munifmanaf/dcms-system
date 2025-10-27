@extends('layouts.app')

@section('page_title', 'Add New Collection')
@section('breadcrumb', 'Add Collection')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card card-primary">
            <div class="card-header">
                <h3 class="card-title">Create New Collection</h3>
            </div>
            <form action="{{ route('collections.store') }}" method="POST">
                @csrf
                <div class="card-body">
                    <div class="form-group">
                        <label for="name">Collection Name *</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" 
                               id="name" name="name" value="{{ old('name') }}" placeholder="Enter collection name" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" 
                                  id="description" name="description" rows="4" placeholder="Enter collection description">{{ old('description') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="community_id">Community *</label>
                        <select class="form-control @error('community_id') is-invalid @enderror" 
                                id="community_id" name="community_id" required>
                            <option value="">Select Community</option>
                            @foreach($communities as $community)
                            <option value="{{ $community->id }}" 
                                {{ old('community_id', request('community')) == $community->id ? 'selected' : '' }}>
                                {{ $community->name }}
                            </option>
                            @endforeach
                        </select>
                        @error('community_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <div class="custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" id="is_public" name="is_public" value="1" checked>
                            <label class="custom-control-label" for="is_public">Make collection public</label>
                        </div>
                        <small class="form-text text-muted">Public collections are visible to all users.</small>
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Create Collection
                    </button>
                    <a href="{{ route('collections.index') }}" class="btn btn-default">
                        <i class="fas fa-arrow-left"></i> Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection