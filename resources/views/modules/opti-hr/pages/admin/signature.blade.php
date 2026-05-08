@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <h1>Gestion de la Signature du DG</h1>
        </div>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <h4 class="alert-heading">Erreur !</h4>
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Télécharger une Signature</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.signature.upload') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="mb-3">
                            <label for="signature" class="form-label">Fichier de signature <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input
                                    type="file"
                                    class="form-control @error('signature') is-invalid @enderror"
                                    id="signature"
                                    name="signature"
                                    accept="image/png,image/jpeg,image/gif,image/svg+xml"
                                    required
                                >
                                <button class="btn btn-primary" type="submit">Télécharger</button>
                            </div>
                            <small class="form-text text-muted d-block mt-2">
                                Format acceptés: PNG, JPG, GIF, SVG (Max 5MB)
                            </small>
                            @error('signature')
                                <span class="invalid-feedback d-block">{{ $message }}</span>
                            @enderror
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Signature Actuelle</h5>
                </div>
                <div class="card-body text-center">
                    @if ($hasSignature && $signatureUrl)
                        <img src="{{ $signatureUrl }}" alt="Signature du DG" class="img-fluid mb-3" style="max-width: 200px; max-height: 150px;">
                        <p class="text-success mb-3">
                            <i class="fas fa-check-circle"></i> Signature disponible
                        </p>
                        <form action="{{ route('admin.signature.delete') }}" method="POST" style="display: inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette signature ?')">
                                <i class="fas fa-trash"></i> Supprimer
                            </button>
                        </form>
                    @else
                        <p class="text-muted">
                            <i class="fas fa-times-circle"></i> Aucune signature téléchargée
                        </p>
                        <small class="text-muted">
                            Téléchargez une image de signature pour l'intégrer automatiquement dans les documents PDF.
                        </small>
                    @endif
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-header">
                    <h5 class="mb-0">Aide</h5>
                </div>
                <div class="card-body">
                    <h6>Comment ajouter une signature ?</h6>
                    <ol class="small">
                        <li>Préparez une image de la signature du DG (PNG, JPG ou GIF)</li>
                        <li>Cliquez sur "Télécharger"</li>
                        <li>La signature sera automatiquement intégrée dans tous les documents PDF générés</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
