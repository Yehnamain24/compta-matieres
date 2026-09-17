<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Statuts — Comptabilité-Matières</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link
        href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,500;9..144,600;9..144,700&family=Inter:wght@400;500;600;700&family=IBM+Plex+Mono:wght@500;600&display=swap"
        rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

    <link rel="stylesheet" href="{{ asset('css/admin/admin.css') }}">
</head>

<body>

    @php
        $activeMenu = 'statuts';
        $pageTitle = 'Statuts';
        $pageSubtitle = 'États possibles du matériel';
    @endphp

    @include('User\Layouts\Sidebar')

    <div class="main-content">

        @include('User/Layouts/Navbar')

        <div class="page-body">

            {{-- ================= MESSAGE DE SUCCÈS ================= --}}
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fa-solid fa-circle-check me-2"></i>
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fermer"></button>
                </div>
            @endif

            {{-- ================= MESSAGE D'ERREUR GLOBAL ================= --}}
            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fa-solid fa-triangle-exclamation me-2"></i>
                    <strong>Erreur :</strong> Veuillez corriger les champs ci-dessous.
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fermer"></button>
                </div>
            @endif

            {{-- ================= TOOLBAR ================= --}}
            <div class="list-toolbar">
                <button type="button" class="btn btn-navy" data-bs-toggle="modal" data-bs-target="#modalAjouterStatut">
                    <i class="fa-solid fa-plus me-2"></i>Ajouter un statut
                </button>

                <div class="search-box">
                    <i class="fa-solid fa-magnifying-glass"></i>
                   <input type="text" id="searchInput" placeholder="Rechercher un statut…">
                </div>
            </div>

            {{-- ================= TABLE ================= --}}
            <div class="list-panel">
                <div class="table-responsive">
                    <table class="table align-middle" id="dataTable">
                        <thead>
                            <tr>
                                <th>Statut</th>
                                <th>Aperçu</th>
                                <th>Matériels associés</th>
                                <th>Créé le</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($statuses as $status)
                                <tr>
                                    <td class="item-name">{{ $status->name }}</td>
                                    <td><span class="badge-status status-neuf">{{ $status->name }}</span></td>
                                    <td>{{ $status->items->count() }}</td>
                                    <td>{{ $status->created_at->format('d/m/Y H:i') }}</td>
                                    <td>
                                        <div class="row-actions">
                                            <button type="button" class="btn-action" data-bs-toggle="modal"
                                                data-bs-target="#modalModifierStatut{{ $status->id }}"
                                                aria-label="Modifier"><i class="fa-solid fa-pen"></i></button>
                                            <button type="button" class="btn-action btn-action-danger"
                                                data-bs-toggle="modal"
                                                data-bs-target="#modalSupprimerStatut{{ $status->id }}"
                                                aria-label="Supprimer"><i class="fa-solid fa-trash"></i></button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">
                                        Aucun statut enregistré pour le moment.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>

    {{-- ================= MODAL AJOUTER ================= --}}
    <div class="modal fade" id="modalAjouterStatut" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content modal-content-registre">
                <div class="modal-header">
                    <h5 class="modal-title">Ajouter un statut</h5>
                    <button type="button" class="btn-close-registre" data-bs-dismiss="modal" aria-label="Fermer"><i
                            class="fa-solid fa-xmark"></i></button>
                </div>
                <form method="POST" action="{{ route('user.status.store') }}">
                    @csrf
                    <div class="modal-body">
                        <label for="nom_statut_ajout" class="form-label">Nom du statut</label>
                        <input type="text" 
                               class="form-control @error('name') is-invalid @enderror" 
                               id="nom_statut_ajout" 
                               name="name"
                               value="{{ old('name') }}"
                               placeholder="Ex. En attente de contrôle" 
                               required>
                        
                        {{-- Affichage de l'erreur sous le champ --}}
                        @error('name')
                            <div class="invalid-feedback d-block mt-2">
                                <i class="fa-solid fa-circle-exclamation me-1"></i> {{ $message }}
                            </div>
                        @enderror
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-navy" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-navy"><i
                                class="fa-solid fa-check me-2"></i>Enregistrer</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- ================= MODALES MODIFIER / SUPPRIMER PAR STATUT ================= --}}
    @foreach ($statuses as $status)
        <div class="modal fade" id="modalModifierStatut{{ $status->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content modal-content-registre">
                    <div class="modal-header">
                        <h5 class="modal-title">Modifier le statut</h5>
                        <button type="button" class="btn-close-registre" data-bs-dismiss="modal" aria-label="Fermer"><i
                                class="fa-solid fa-xmark"></i></button>
                    </div>
                    <form method="POST" action="{{ route('user.status.update', $status->id) }}">
                        @csrf
                        @method('PUT')
                        <div class="modal-body">
                            <label for="nom_statut_{{ $status->id }}" class="form-label">Nom du statut</label>
                            <input type="text" 
                                   class="form-control @error('name') is-invalid @enderror" 
                                   id="nom_statut_{{ $status->id }}"
                                   name="name" 
                                   value="{{ old('name', $status->name) }}" 
                                   required>
                            
                            @error('name')
                                <div class="invalid-feedback d-block mt-2">
                                    <i class="fa-solid fa-circle-exclamation me-1"></i> {{ $message }}
                                </div>
                            @enderror
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-navy"
                                data-bs-dismiss="modal">Annuler</button>
                            <button type="submit" class="btn btn-navy"><i class="fa-solid fa-check me-2"></i>Mettre
                                à jour</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="modal fade" id="modalSupprimerStatut{{ $status->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content modal-content-registre">
                    <div class="modal-header">
                        <h5 class="modal-title">Supprimer le statut</h5>
                        <button type="button" class="btn-close-registre" data-bs-dismiss="modal"
                            aria-label="Fermer"><i class="fa-solid fa-xmark"></i></button>
                    </div>
                    <div class="modal-body">
                        <p class="mb-0"><i class="fa-solid fa-triangle-exclamation me-2"
                                style="color:var(--red-700);"></i>Voulez-vous vraiment supprimer
                            le statut <strong>« {{ $status->name }} »</strong> ? Cette action est irréversible.</p>
                    </div>
                    <form method="POST" action="{{ route('user.status.destroy', $status->id) }}">
                        @csrf
                        @method('DELETE')
                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-navy"
                                data-bs-dismiss="modal">Annuler</button>
                            <button type="submit" class="btn btn-navy"
                                style="background:var(--red-700); border-color:var(--red-700);"><i
                                    class="fa-solid fa-trash me-2"></i>Supprimer</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endforeach

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        <script>
        const searchInput = document.getElementById('searchInput');
        const tableRows = document.querySelectorAll('#dataTable tbody tr');
        if (searchInput) {
        searchInput.addEventListener('input', function () {
            const term = this.value.toLowerCase();
            tableRows.forEach(function (row) {
                row.style.display = row.textContent.toLowerCase().includes(term) ? '' : 'none';
            });
        });
    }
</script>
    </script>
</body>

</html>