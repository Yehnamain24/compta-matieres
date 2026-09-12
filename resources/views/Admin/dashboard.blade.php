<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administration — Comptabilité-Matières</title>

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

            {{-- ================= MESSAGES (succès / erreurs) ================= --}}
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

            @if ($errors->any())
                <div class="alert-registre alert-registre-error" role="alert">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                    <div class="alert-registre-content">
                        <span class="alert-registre-title">
                            {{ $errors->count() > 1 ? 'Veuillez corriger les erreurs suivantes' : 'Une erreur est survenue' }}
                        </span>
                        @if ($errors->count() > 1)
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        @else
                            {{ $errors->first() }}
                        @endif
                    </div>
                    <button type="button" class="alert-registre-close" aria-label="Fermer">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>
            @endif

            {{-- ================= KPI ================= --}}
            <div class="row g-3 mb-4">
                <div class="col-sm-6 col-xl-3">
                    <div class="kpi-card" style="--accent:var(--navy-700);">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <span class="kpi-icon"><i class="fa-solid fa-users"></i></span>
                        </div>
                        <p class="kpi-label mb-1">Utilisateurs enregistrés</p>
                        <p class="kpi-value mb-0">{{ $totalUsers ?? '0' }}</p>
                    </div>
                </div>
                <div class="col-sm-6 col-xl-3">
                    <div class="kpi-card" style="--accent:var(--gold-600);">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <span class="kpi-icon"><i class="fa-solid fa-user-clock"></i></span>
                        </div>
                        <p class="kpi-label mb-1">Inscriptions en attente</p>
                        <p class="kpi-value mb-0">{{ $pendingUsers ?? '0' }}</p>
                    </div>
                </div>
                <div class="col-sm-6 col-xl-3">
                    <div class="kpi-card" style="--accent:var(--green-700);">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <span class="kpi-icon"><i class="fa-solid fa-boxes-stacked"></i></span>
                        </div>
                        <p class="kpi-label mb-1">Fiches matériels</p>
                        <p class="kpi-value mb-0">{{ $totalItems ?? '0' }}</p>
                    </div>
                </div>
                <div class="col-sm-6 col-xl-3">
                    <div class="kpi-card" style="--accent:var(--red-700);">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <span class="kpi-icon"><i class="fa-solid fa-right-left"></i></span>
                        </div>
                        <p class="kpi-label mb-1">Mouvements ce mois</p>
                        <p class="kpi-value mb-0">{{ $movementsThisMonth ?? '0' }}</p>
                    </div>
                </div>
            </div>

            {{-- ================= INSCRIPTIONS EN ATTENTE ================= --}}
            <div class="row g-3">
                <div class="col-12">
                    <div class="panel">
                        <div class="d-flex flex-wrap justify-content-between align-items-start gap-2 mb-1">
                            <div>
                                <p class="panel-title mb-0">Inscriptions en attente d'approbation</p>
                                <p class="panel-subtitle mb-0">Comptes créés mais non encore validés</p>
                            </div>
                        </div>

                        @if (($pendingUsersList ?? collect())->isNotEmpty())
                            <div class="table-responsive">
                                <table class="table table-registre mb-0">
                                    <thead>
                                        <tr>
                                            <th>Nom</th>
                                            <th>Matricule</th>
                                            <th>Email</th>
                                            <th>Date d'inscription</th>
                                            <th class="text-end">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($pendingUsersList as $pendingUser)
                                            <tr>
                                                <td>{{ $pendingUser->name }} {{ $pendingUser->surname }}</td>
                                                <td>{{ $pendingUser->matricule }}</td>
                                                <td>{{ $pendingUser->email }}</td>
                                                <td>{{ $pendingUser->created_at->format('d/m/Y') }}</td>
                                                <td class="text-end">
                                                    <form action="{{ route('admin.users.approve', $pendingUser->id) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button class="btn btn-sm btn-navy" type="submit">Approuver</button>
                                                    </form>                                                   
                                                    <form action="{{ route('admin.users.reject', $pendingUser->id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button class="btn btn-sm btn-outline-danger" type="submit">Refuser</button>
                                                </form>
                                               
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="alert-stock-empty">
                                <i class="fa-solid fa-circle-check"></i>
                                Aucune inscription en attente pour le moment.
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- ================= LISTE DES UTILISATEURS ================= --}}
            <div class="row g-3 mt-1">
                <div class="col-12">
                    <div class="panel">
                        <p class="panel-title">Tous les utilisateurs</p>
                        <p class="panel-subtitle">Comptes actifs et leurs rôles</p>
                        <div class="table-responsive">
                            <table class="table table-registre mb-0">
                                <thead>
                                    <tr>
                                        <th>Nom</th>
                                        <th>Matricule</th>
                                        <th>Email</th>
                                        <th>Rôle</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach (($users ?? []) as $u)
                                        <tr>
                                            <td>{{ $u->name }} {{ $u->surname }}</td>
                                            <td>{{ $u->matricule }}</td>
                                            <td>{{ $u->email }}</td>
                                            <td><span class="badge-mouvement badge-entree">{{ $u->role }}</span></td>
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