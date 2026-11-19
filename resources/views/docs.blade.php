<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestor en la Nube - Documentos</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 900px; margin: 30px auto; padding: 0 20px; background: #f4f6f9; }
        nav { background: #2c3e50; padding: 15px; border-radius: 6px; margin-bottom: 20px; }
        nav a { color: white; text-decoration: none; margin-right: 20px; font-weight: bold; }
        nav a.active { text-decoration: underline; color: #3498db; }
        .alert { padding: 12px; border-radius: 5px; margin-bottom: 15px; }
        .success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .card { background: white; padding: 20px; border-radius: 6px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); margin-bottom: 20px; }
        .doc-item { display: flex; flex-direction: column; gap: 10px; border-bottom: 1px solid #eee; padding: 15px 0; }
        .doc-link { font-size: 1.1em; font-weight: bold; color: #2980b9; text-decoration: none; }
        .doc-link:hover { text-decoration: underline; }
        .actions { display: flex; gap: 10px; flex-wrap: wrap; align-items: center; }
        input[type="text"], input[type="file"] { padding: 6px; }
        button { padding: 8px 14px; border: none; border-radius: 4px; cursor: pointer; color: white; font-weight: bold; }
        .btn-primary { background: #3498db; }
        .btn-warning { background: #f39c12; }
        .btn-danger { background: #e74c3c; }
    </style>
</head>
<body>

    <nav>
        <a href="{{ route('photos.index') }}">📷 Galería de Fotos</a>
        <a href="{{ route('docs.index') }}" class="active">📄 Gestor de Documentos (PDF)</a>
    </nav>

    <h1>Gestor de Documentos PDF</h1>

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
        <h3>Subir nuevo PDF</h3>
        <form action="{{ route('docs.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div style="margin-bottom: 10px;">
                <label>Título:</label><br>
                <input type="text" name="title" value="{{ old('title') }}" style="width: 100%; box-sizing: border-box;" required>
            </div>
            <div style="margin-bottom: 10px;">
                <label>Archivo PDF (máx. 5MB):</label><br>
                <input type="file" name="file" accept="application/pdf" required>
            </div>
            <button type="submit" class="btn-primary">Subir Documento</button>
        </form>
    </div>

    <div class="card">
        <h3>Documentos guardados</h3>
        @forelse($docs as $doc)
            <div class="doc-item">
                <!-- Abre el PDF en una pestaña nueva -->
                <a href="{{ asset('storage/' . $doc->path) }}" target="_blank" class="doc-link">
                    📄 {{ $doc->title }} (Abrir PDF ↗)
                </a>

                <div class="actions">
                    <!-- Formulario para actualizar / reemplazar -->
                    <form action="{{ route('docs.update', $doc) }}" method="POST" enctype="multipart/form-data" style="display: flex; gap: 8px; flex-wrap: wrap;">
                        @csrf
                        @method('PUT')
                        <input type="text" name="title" value="{{ $doc->title }}" required>
                        <input type="file" name="file" accept="application/pdf">
                        <button type="submit" class="btn-warning">Reemplazar / Actualizar</button>
                    </form>

                    <!-- Formulario para eliminar -->
                    <form action="{{ route('docs.destroy', $doc) }}" method="POST" onsubmit="return confirm('¿Eliminar este documento?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn-danger">Eliminar</button>
                    </form>
                </div>
            </div>
        @empty
            <p>No hay documentos subidos todavía.</p>
        @endforelse
    </div>

</body>
</html>