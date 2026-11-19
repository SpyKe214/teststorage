<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestor en la Nube - Fotos</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 900px; margin: 30px auto; padding: 0 20px; background: #f4f6f9; }
        nav { background: #2c3e50; padding: 15px; border-radius: 6px; margin-bottom: 20px; }
        nav a { color: white; text-decoration: none; margin-right: 20px; font-weight: bold; }
        nav a.active { text-decoration: underline; color: #3498db; }
        .alert { padding: 12px; border-radius: 5px; margin-bottom: 15px; }
        .success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .card { background: white; padding: 20px; border-radius: 6px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); margin-bottom: 20px; }
        .grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(260px, 1fr)); gap: 20px; }
        .photo-card img { width: 100%; height: 180px; object-fit: cover; border-radius: 4px; margin-bottom: 10px; }
        input[type="text"], input[type="file"] { width: 100%; padding: 8px; margin: 5px 0 10px; box-sizing: border-box; }
        button { padding: 8px 14px; border: none; border-radius: 4px; cursor: pointer; color: white; font-weight: bold; }
        .btn-primary { background: #3498db; }
        .btn-warning { background: #f39c12; }
        .btn-danger { background: #e74c3c; margin-top: 8px; width: 100%; }
    </style>
</head>
<body>

    <nav>
        <a href="{{ route('photos.index') }}" class="active">📷 Galería de Fotos</a>
        <a href="{{ route('docs.index') }}">📄 Gestor de Documentos (PDF)</a>
    </nav>

    <h1>Galería de Imágenes</h1>

    @if(session('success'))
        <div class="alert success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert error">{{ session('error') }}</div>
    @endif
    @if($errors->any())
        <div class="alert error">
            <ul style="margin: 0; padding-left: 20px;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card">
        <h3>Subir nueva imagen</h3>
        <form action="{{ route('photos.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <label>Título:</label>
            <input type="text" name="title" value="{{ old('title') }}" required>

            <label>Imagen (máx. 2MB):</label>
            <input type="file" name="file" accept="image/*" required>

            <button type="submit" class="btn-primary">Subir Imagen</button>
        </form>
    </div>

    <div class="grid">
        @forelse($photos as $photo)
            <div class="card photo-card">
                <img src="{{ asset('storage/' . $photo->path) }}" alt="{{ $photo->title }}">
                <h4>{{ $photo->title }}</h4>

                <!-- Formulario para actualizar / reemplazar -->
                <form action="{{ route('photos.update', $photo) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <input type="text" name="title" value="{{ $photo->title }}" required>
                    <input type="file" name="file" accept="image/*">
                    <button type="submit" class="btn-warning" style="width: 100%;">Reemplazar / Actualizar</button>
                </form>

                <!-- Formulario para eliminar -->
                <form action="{{ route('photos.destroy', $photo) }}" method="POST" onsubmit="return confirm('¿Eliminar esta imagen?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn-danger">Eliminar</button>
                </form>
            </div>
        @empty
            <p>No hay imágenes subidas todavía.</p>
        @endforelse
    </div>

</body>
</html>