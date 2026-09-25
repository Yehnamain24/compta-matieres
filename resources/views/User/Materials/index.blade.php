<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Matériels — Comptabilité-Matières</title>

    
   <link href="{{ asset('vendor/fonts/inter/inter.css') }}" rel="stylesheet">
    <link href="{{ asset('vendor/fonts/fraunces/fraunces.css') }}" rel="stylesheet">
    <link href="{{ asset('vendor/fonts/ibm-plex-mono/ibm-plex-mono.css') }}" rel="stylesheet">

    <link href="{{ asset('vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('vendor/fontawesome/css/all.min.css') }}">

    <link rel="stylesheet" href="{{ asset('css/admin/admin.css') }}">
</head>

<body>

    @php
        $activeMenu = 'materiels';
        $pageTitle = 'Matériels';
        $pageSubtitle = 'Fiches de stock du parc matériel';
    @endphp

    @include('User/Layouts/Sidebar')

    <div class="main-content">

        @include('User/Layouts/Navbar')

        <div class="page-body">

            {{-- ================= TOOLBAR ================= --}}
            <div class="list-toolbar">
                <button type="button" class="btn btn-navy" data-bs-toggle="modal"
                    data-bs-target="#modalAjouterMateriel">
                    <i class="fa-solid fa-plus me-2"></i>Ajouter un matériel
                </button>

                <div class="d-flex flex-wrap gap-2">
                    <div class="search-box">
                        <i class="fa-solid fa-magnifying-glass"></i>
                        <input type="text" id="searchInput" placeholder="Rechercher un code ou une désignation…">
                    </div>
                    <select class="filter-select">
                        <option selected>Toutes les catégories</option>
                        @foreach ($categories as $category)
                            <option>{{ $category->name }}</option>
                        @endforeach
                    </select>
                    <select class="filter-select">
                        <option selected>Tous les statuts</option>
                        @foreach ($statuses as $status)
                            <option>{{ $status->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- ================= TABLE ================= --}}
            <div class="list-panel">
                <div class="table-responsive">
                    <table class="table align-middle" id="dataTable">
                        <thead>
                            <tr>
                                <th>Code</th>
                                <th>Désignation</th>
                                <th>Catégorie</th>
                                <th>Statut</th>
                                <th>Quantité</th>
                                <th>Seuil d'alerte</th>
                                <th>Localisation</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($items as $item)
                                {{-- ✅ LIGNE CLIQUABLE : Ouvre la modale de modification --}}
                                <tr data-bs-toggle="modal" 
                                    data-bs-target="#modalModifierMateriel{{ $item->id }}"
                                    style="cursor: pointer;">
                                    
                                    <td class="item-code">{{ $item->code }}</td>
                                    <td class="item-name">{{ $item->name }}</td>
                                    {{-- ✅ CORRECTION : Affiche la vraie catégorie --}}
                                    <td>{{ $item->category->name ?? '—' }}</td>
                                    <td><span class="badge-status status-bon">{{ $item->status->name ?? '—' }}</span></td>
                                    <td>{{ $item->quantity }}</td>
                                    <td>{{ $item->alert_threshold }}</td>
                                    <td>{{ $item->location ?? '—' }}</td>
                                    <td>
                                        {{-- ✅ stopPropagation empêche le double-clic --}}
                                        <div class="row-actions" onclick="event.stopPropagation();">
                                            <button type="button" class="btn-action" data-bs-toggle="modal"
                                                data-bs-target="#modalModifierMateriel{{ $item->id }}"
                                                aria-label="Modifier"><i class="fa-solid fa-pen"></i></button>
                                            <button type="button" class="btn-action btn-action-danger"
                                                data-bs-toggle="modal"
                                                data-bs-target="#modalSupprimerMateriel{{ $item->id }}"
                                                aria-label="Supprimer"><i class="fa-solid fa-trash"></i></button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center text-muted py-4">
                                        Aucun matériel enregistré pour le moment.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>

    {{-- ================= MODAL AJOUTER UN MATÉRIEL ================= --}}
    <div class="modal fade" id="modalAjouterMateriel" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content modal-content-registre">
                <div class="modal-header">
                    <h5 class="modal-title">Ajouter un matériel</h5>
                    <button type="button" class="btn-close-registre" data-bs-dismiss="modal" aria-label="Fermer">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>
                <form method="POST" action="{{ route('user.items.save') }}">
                    @csrf
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label for="code" class="form-label">Code</label>
                                <input type="text" class="form-control" id="code" name="code"
                                    placeholder="Ex. SM-2026-0201" required>
                            </div>
                            <div class="col-md-8">
                                <label for="name" class="form-label">Désignation</label>
                                <input type="text" class="form-control" id="name" name="name"
                                    placeholder="Ex. Microscope binoculaire" required>
                            </div>

                            <div class="col-12">
                                <label for="description" class="form-label">Description <span
                                        class="text-muted fw-normal">(optionnel)</span></label>
                                <input type="text" class="form-control" id="description" name="description"
                                    placeholder="Précisions utiles sur le matériel">
                            </div>

                            <div class="col-md-6">
                                <label for="category_id" class="form-label">Catégorie</label>
                                <select class="form-select" id="category_id" name="category_id" required>
                                    <option value="" selected disabled>Sélectionner une catégorie</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="status_id" class="form-label">Statut</label>
                                <select class="form-select" id="status_id" name="status_id" required>
                                    <option value="" selected disabled>Sélectionner un statut</option>
                                    @foreach ($statuses as $status)
                                        <option value="{{ $status->id }}">{{ $status->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label for="quantity" class="form-label">Quantité</label>
                                <input type="number" min="0" class="form-control" id="quantity"
                                    name="quantity" placeholder="0" required>
                            </div>
                            <div class="col-md-4">
                                <label for="alert_threshold" class="form-label">Seuil d'alerte</label>
                                <input type="number" min="0" class="form-control" id="alert_threshold"
                                    name="alert_threshold" placeholder="0" required>
                            </div>
                            <div class="col-md-4">
                                <label for="location" class="form-label">Localisation</label>
                                <input type="text" class="form-control" id="location" name="location"
                                    placeholder="Ex. Magasin central">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-navy" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-navy">
                            <i class="fa-solid fa-check me-2"></i>Enregistrer
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- ================= MODALES MODIFIER / SUPPRIMER PAR MATÉRIEL ================= --}}
    @foreach ($items as $item)
        {{-- Modale de modification --}}
        <div class="modal fade" id="modalModifierMateriel{{ $item->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content modal-content-registre">
                    <div class="modal-header">
                        <h5 class="modal-title">Modifier le matériel</h5>
                        <button type="button" class="btn-close-registre" data-bs-dismiss="modal"
                            aria-label="Fermer">
                            <i class="fa-solid fa-xmark"></i>
                        </button>
                    </div>
                    <form method="POST" action="{{ route('items.update', $item->id) }}">
                        @csrf
                        @method('PUT')
                        <div class="modal-body">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label for="code_{{ $item->id }}" class="form-label">Code</label>
                                    <input type="text" class="form-control" id="code_{{ $item->id }}"
                                        name="code" value="{{ $item->code }}" required>
                                </div>
                                <div class="col-md-8">
                                    <label for="name_{{ $item->id }}" class="form-label">Désignation</label>
                                    <input type="text" class="form-control" id="name_{{ $item->id }}"
                                        name="name" value="{{ $item->name }}" required>
                                </div>

                                <div class="col-12">
                                    <label for="description_{{ $item->id }}" class="form-label">Description <span
                                            class="text-muted fw-normal">(optionnel)</span></label>
                                    <input type="text" class="form-control" id="description_{{ $item->id }}"
                                        name="description" value="{{ $item->description }}">
                                </div>

                                <div class="col-md-6">
                                    <label for="category_id_{{ $item->id }}" class="form-label">Catégorie</label>
                                    <select class="form-select" id="category_id_{{ $item->id }}"
                                        name="category_id" required>
                                        @foreach ($categories as $category)
                                            <option value="{{ $category->id }}"
                                                {{ $item->category_id == $category->id ? 'selected' : '' }}>
                                                {{ $category->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="status_id_{{ $item->id }}" class="form-label">Statut</label>
                                    <select class="form-select" id="status_id_{{ $item->id }}" name="status_id"
                                        required>
                                        @foreach ($statuses as $status)
                                            <option value="{{ $status->id }}"
                                                {{ $item->status_id == $status->id ? 'selected' : '' }}>
                                                {{ $status->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-4">
                                    <label for="quantity_{{ $item->id }}" class="form-label">Quantité</label>
                                    <input type="number" min="0" class="form-control"
                                        id="quantity_{{ $item->id }}" name="quantity"
                                        value="{{ $item->quantity }}" required>
                                </div>
                                <div class="col-md-4">
                                    <label for="alert_threshold_{{ $item->id }}" class="form-label">Seuil
                                        d'alerte</label>
                                    <input type="number" min="0" class="form-control"
                                        id="alert_threshold_{{ $item->id }}" name="alert_threshold"
                                        value="{{ $item->alert_threshold }}" required>
                                </div>
                                <div class="col-md-4">
                                    <label for="location_{{ $item->id }}" class="form-label">Localisation</label>
                                    <input type="text" class="form-control" id="location_{{ $item->id }}"
                                        name="location" value="{{ $item->location }}">
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-navy"
                                data-bs-dismiss="modal">Annuler</button>
                            <button type="submit" class="btn btn-navy">
                                <i class="fa-solid fa-check me-2"></i>Mettre à jour
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Modale de suppression --}}
        <div class="modal fade" id="modalSupprimerMateriel{{ $item->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content modal-content-registre">
                    <div class="modal-header">
                        <h5 class="modal-title">Supprimer le matériel</h5>
                        <button type="button" class="btn-close-registre" data-bs-dismiss="modal"
                            aria-label="Fermer">
                            <i class="fa-solid fa-xmark"></i>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p class="mb-0">
                            <i class="fa-solid fa-triangle-exclamation me-2" style="color:var(--red-700);"></i>
                            Voulez-vous vraiment supprimer le matériel
                            <strong>« {{ $item->name }} » ({{ $item->code }})</strong> ? Cette action est
                            irréversible.
                        </p>
                    </div>
                    <form method="POST" action="{{ route('items.destroy', $item->id) }}">
                        @csrf
                        @method('DELETE')
                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-navy"
                                data-bs-dismiss="modal">Annuler</button>
                            <button type="submit" class="btn btn-navy"
                                style="background:var(--red-700); border-color:var(--red-700);">
                                <i class="fa-solid fa-trash me-2"></i>Supprimer
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endforeach

    <script src="{{ asset('vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script>
        // ===== Sidebar toggle =====
        const sidebar = document.getElementById('sidebar');
        const btnToggleSidebar = document.getElementById('btnToggleSidebar');
        if (btnToggleSidebar && sidebar) {
            btnToggleSidebar.addEventListener('click', function() {
                sidebar.classList.toggle('show');
            });
        }

        // ===== Recherche dans la table =====
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
</body>

</html>