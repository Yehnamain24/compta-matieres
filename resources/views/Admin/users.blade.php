<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Utilisateurs — Comptabilité-Matières</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link
        href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,500;9..144,600;9..144,700&family=Inter:wght@400;500;600;700&family=IBM+Plex+Mono:wght@500;600&display=swap"
        rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

    <link rel="stylesheet" href="{{ asset('css/admin/admin.css') }}">
</head>

<body>

    {{-- ================= SIDEBAR ================= --}}
    @include('User.Layouts.Sidebar')

    <div class="main-content">

        {{-- ================= TOPBAR ================= --}}
        @include('User.Layouts.Navbar')

        <div class="page-body">

            {{-- Message de succès --}}
            @if (session('success'))
                <div class="alert-registre alert-registre-success" role="alert">
                    <i class="fa-solid fa-circle-check"></i>
                    <div class="alert-registre-content">
                        <span class="alert-registre-title">Succès</span>
                        {{ session('success') }}
                    </div>
                    <button type="button" class="alert-registre-close" aria-label="Fermer">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>
            @endif

            {{-- Message d'erreur --}}
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="row g-3">
                <div class="col-12">
                    <div class="panel">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <p class="panel-title mb-0">Utilisateurs</p>
                                <p class="panel-subtitle mb-0">Comptes actifs et leurs rôles</p>
                            </div>
                            {{-- Bouton pour ouvrir la modale de création --}}
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalCreateAdmin">
                                <i class="fa-solid fa-plus"></i> Ajouter un Administrateur
                            </button>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-registre mb-0">
                                <thead>
                                    <tr>
                                        <th>Nom</th>
                                        <th>Matricule</th>
                                        <th>Email</th>
                                        <th>Rôle</th>
                                        <th>Statut</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($users as $user)
                                    <tr>
                                        <td>{{ $user->name }} {{ $user->surname }}</td>
                                        <td>{{ $user->matricule }}</td>
                                        <td>{{ $user->email }}</td>
                                        {{-- Le rôle est maintenant fixe : Administrateur --}}
                                        <td><span class="badge-mouvement badge-entree">Administrateur</span></td>
                                        <td>{{ $user->is_approved ? 'Approuvé' : 'En attente' }}</td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted">Aucun utilisateur trouvé.</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    {{-- ================= MODALE DE CRÉATION ================= --}}
    <div class="modal fade" id="modalCreateAdmin" tabindex="-1" aria-labelledby="modalCreateAdminLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('admin.users.store') }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalCreateAdminLabel">Créer un Administrateur</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="name" class="form-label">Nom</label>
                            <input type="text" name="name" id="name" class="form-control" value="{{ old('name') }}" required>
                        </div>
                        <div class="mb-3">
                            <label for="surname" class="form-label">Prénom</label>
                            <input type="text" name="surname" id="surname" class="form-control" value="{{ old('surname') }}" required>
                        </div>
                        <div class="mb-3">
                            <label for="matricule" class="form-label">Matricule</label>
                            <input type="text" name="matricule" id="matricule" class="form-control" value="{{ old('matricule') }}" required>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" name="email" id="email" class="form-control" value="{{ old('email') }}" required>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Mot de passe</label>
                            <input type="password" name="password" id="password" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="password_confirmation" class="form-label">Confirmer le mot de passe</label>
                            <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" required>
                        </div>
                        
                        {{-- On force le rôle Admin --}}
                        <input type="hidden" name="role" value="admin">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-primary">Créer l'Administrateur</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const sidebar = document.getElementById('sidebar');
        const btnToggleSidebar = document.getElementById('btnToggleSidebar');
        btnToggleSidebar.addEventListener('click', function() {
            sidebar.classList.toggle('show');
        });
        document.querySelectorAll('.alert-registre-close').forEach(function(bouton) {
            bouton.addEventListener('click', function() {
                bouton.closest('.alert-registre').remove();
            });
        });
    </script>
</body>

</html>