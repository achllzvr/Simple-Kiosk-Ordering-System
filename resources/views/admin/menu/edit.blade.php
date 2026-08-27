@extends('ordering.layout')

@section('content')
<div class="admin-page">
    <div class="row mb-4">
        <div class="col-md-12">
            <h1 class="page-title text-md-start">Edit Menu Item</h1>
        </div>
    </div>

    @if($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <div class="row">
        <div class="col-md-6">
            <form action="{{ route('admin.menu.update', $item) }}" method="POST" enctype="multipart/form-data" class="js-guard-submit">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label for="name" class="form-label">Item Name *</label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $item->name) }}" required>
                    @error('name')
                    <span class="text-danger small">{{ $message }}</span>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="category" class="form-label">Category *</label>
                    <input type="text" class="form-control @error('category') is-invalid @enderror" id="category" name="category" list="categories" value="{{ old('category', $item->category) }}" required>
                    <datalist id="categories">
                        @foreach($categories as $cat)
                        <option value="{{ $cat }}">
                        @endforeach
                    </datalist>
                    @error('category')
                    <span class="text-danger small">{{ $message }}</span>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="price" class="form-label">Price *</label>
                    <div class="input-group">
                        <span class="input-group-text">₱</span>
                        <input type="number" class="form-control @error('price') is-invalid @enderror" id="price" name="price" value="{{ old('price', $item->price) }}" step="0.01" min="0" required>
                    </div>
                    @error('price')
                    <span class="text-danger small">{{ $message }}</span>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label">Description</label>
                    <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description', $item->description) }}</textarea>
                    @error('description')
                    <span class="text-danger small">{{ $message }}</span>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Current image</label>
                    <div>
                        <img src="{{ $item->image_url }}" alt="{{ $item->name }}" style="max-width: 180px; max-height: 140px; object-fit: cover; border-radius: 12px; border: 2px solid var(--kfc-black);">
                    </div>
                </div>

                <div class="mb-3">
                    <label for="image_file" class="form-label">Replace image</label>
                    <input type="file" class="form-control @error('image_file') is-invalid @enderror" id="image_file" name="image_file" accept="image/jpeg,image/png,image/webp,image/gif">
                    <small class="text-muted">Leave empty to keep the current image. JPEG, PNG, WebP, or GIF — max 4MB</small>
                    @error('image_file')
                    <span class="text-danger small d-block">{{ $message }}</span>
                    @enderror
                </div>

                <div class="mb-3 form-check">
                    <input type="checkbox" class="form-check-input" id="is_active" name="is_active" value="1" {{ old('is_active', $item->is_active) ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_active">
                        Active (Available for ordering)
                    </label>
                </div>

                <div class="d-grid gap-2 d-md-flex justify-content-md-start">
                    <button type="submit" class="btn btn-kfc btn-lg">Update Item</button>
                    <a href="{{ route('admin.menu.index') }}" class="btn btn-outline-kfc btn-lg">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
