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

            <div class="row g-3">
                <div class="col-12">
                    <div class="panel">
                        <p class="panel-title mb-0">Utilisateurs</p>
                        <p class="panel-subtitle mb-3">Comptes actifs et leurs rôles</p>

                        <div class="table-responsive">
                            <table class="table table-registre mb-0">
                                <thead>
                                    <tr>
                                        <th>Nom</th>
                                        <th>Matricule</th>
                                        <th>Email</th>
                                        <th>Rôle</th>
                                        <th>Statut</th>
                                        <th class="text-end">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($users as $user)
                                    <tr>
                                        <td>{{ $user->name }} {{ $user->surname }}</td>
                                        <td>{{ $user->matricule }}</td>
                                        <td>{{ $user->email }}</td>
                                        <td><span class="badge-mouvement badge-entree">{{ $user->role }}</span></td>
                                        <td>{{ $user->is_approved ? 'Approuvé' : 'En attente' }}</td>
                                        <td class="text-end">
                                            <form method="POST" action="{{ route('admin.users.update-role', $user) }}" class="d-inline">
                                                @csrf
                                                @method('PATCH')
                                                <select name="role" onchange="this.form.submit()" class="form-select form-select-sm d-inline w-auto">
                                                    <option value="comptable_matieres" @selected($user->role == 'comptable_matieres')>Comptable-matières</option>
                                                    <option value="admin" @selected($user->role == 'admin')>Admin</option>
                                                </select>
                                            </form>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
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