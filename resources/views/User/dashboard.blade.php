<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de bord — Comptabilité-Matières</title>

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

            {{-- ================= ACTIONS ================= --}}
            <div class="d-flex flex-wrap justify-content-end gap-2 mb-4">
                <a href="{{ route('materiels.export') }}" class="btn btn-outline-navy">
                    <i class="fa-solid fa-file-export me-2"></i>Exporter les données (.XLS/CSV)
                </a>
                <a href="{{ route('user.items.show') }}" class="btn btn-navy">
                    <i class="fa-solid fa-plus me-2"></i>Nouvelle fiche
                </a>
            </div>

            {{-- ================= KPI ================= --}}
            <div class="row g-3 mb-4">
                <div class="col-sm-6 col-xl-3">
                    <div class="kpi-card" style="--accent:var(--gold-600);">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <span class="kpi-icon"><i class="fa-solid fa-boxes-stacked"></i></span>
                        </div>
                        <p class="kpi-label mb-1">Fiches enregistrées</p>
                        <p class="kpi-value mb-0">{{ $totalItems ?? '0' }}</p>
                    </div>
                </div>
                <div class="col-sm-6 col-xl-3">
                    <div class="kpi-card" style="--accent:var(--navy-700);">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <span class="kpi-icon"><i class="fa-solid fa-right-left"></i></span>
                        </div>
                        <p class="kpi-label mb-1">Mouvements ce mois</p>
                        <p class="kpi-value mb-0">{{ $movementsThisMonth ?? '0' }}</p>
                    </div>
                </div>
                <div class="col-sm-6 col-xl-3">
                    <div class="kpi-card" style="--accent:var(--green-700);">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <span class="kpi-icon"><i class="fa-solid fa-tags"></i></span>
                        </div>
                        <p class="kpi-label mb-1">Catégories suivies</p>
                        <p class="kpi-value mb-0">{{ $totalCategories ?? '0' }}</p>
                    </div>
                </div>
                <div class="col-sm-6 col-xl-3">
                    <div class="kpi-card" style="--accent:var(--red-700);">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <span class="kpi-icon"><i class="fa-solid fa-triangle-exclamation"></i></span>
                        </div>
                        <p class="kpi-label mb-1">Matériels sous seuil d'alerte</p>
                        <p class="kpi-value mb-0">{{ $itemsUnderThreshold ?? '0' }}</p>
                    </div>
                </div>
            </div>

            {{-- ================= GRAPHIQUE + DERNIERS MOUVEMENTS ================= --}}
            <div class="row g-3">
                {{-- Graphique --}}
                <div class="col-lg-8">
                    <div class="panel">
                        <p class="panel-title mb-0">Répartition des matériels par catégorie</p>
                        <p class="panel-subtitle mb-3">Quantité totale en stock par catégorie</p>

                        <div style="position: relative; height: 350px; width: 100%;">
                            <canvas id="graphiqueCategories"></canvas>
                        </div>
                    </div>
                </div>

                {{-- Derniers mouvements --}}
                <div class="col-lg-4">
                    <div class="panel">
                        <p class="panel-title">Derniers mouvements</p>
                        <p class="panel-subtitle">Entrées, sorties et retours récents</p>
                        <div class="table-responsive">
                            <table class="table table-registre mb-0">
                                <thead>
                                    <tr>
                                        <th>Matériel</th>
                                        <th>Type</th>
                                        <th>Qté</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($derniersMouvements ?? [] as $mouvement)
                                        <tr>
                                            <td>{{ $mouvement->item->name ?? '—' }}</td>
                                            <td>
                                                @php
                                                    $typeName = strtolower(str_replace(['é','è','ê'], 'e', $mouvement->movementType->name ?? ''));
                                                    $badgeClass = 'badge-entree';
                                                    if (str_contains($typeName, 'sortie')) {
                                                        $badgeClass = 'badge-sortie';
                                                    } elseif (str_contains($typeName, 'retour')) {
                                                        $badgeClass = 'badge-retour';
                                                    }
                                                @endphp
                                                <span class="badge-mouvement {{ $badgeClass }}">
                                                    {{ $mouvement->movementType->name ?? '—' }}
                                                </span>
                                            </td>
                                            <td>{{ $mouvement->quantity ?? 0 }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="text-center text-muted py-3">
                                                Aucun mouvement récent.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ================= MATÉRIELS SOUS SEUIL D'ALERTE ================= --}}
            <div class="row g-3 mt-1">
                <div class="col-12">
                    <div class="panel">
                        <div class="d-flex flex-wrap justify-content-between align-items-start gap-2 mb-1">
                            <div>
                                <p class="panel-title mb-0">Matériels sous seuil d'alerte</p>
                                <p class="panel-subtitle mb-0">Fiches dont la quantité est inférieure ou égale au seuil
                                    paramétré</p>
                            </div>
                        </div>

                        @if (($lowStockItems ?? collect())->isNotEmpty())
                            <div class="table-responsive">
                                <table class="table table-registre mb-0">
                                    <thead>
                                        <tr>
                                            <th>Code</th>
                                            <th>Matériel</th>
                                            <th>Catégorie</th>
                                            <th>Quantité</th>
                                            <th>Seuil d'alerte</th>
                                            <th>Localisation</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($lowStockItems as $lowStockItem)
                                            <tr class="alert-stock-row">
                                                <td class="item-code">{{ $lowStockItem->code }}</td>
                                                <td class="item-name">{{ $lowStockItem->name }}</td>
                                                <td>{{ $lowStockItem->category->name ?? '—' }}</td>
                                                <td class="alert-stock-qty">{{ $lowStockItem->quantity }}</td>
                                                <td>{{ $lowStockItem->alert_threshold }}</td>
                                                <td>{{ $lowStockItem->location ?? '—' }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="alert-stock-empty">
                                <i class="fa-solid fa-circle-check"></i>
                                Aucun matériel sous le seuil d'alerte pour le moment.
                            </div>
                        @endif
                    </div>
                </div>
            </div>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
    <script>
        // Ouverture / fermeture du sidebar en version mobile
        const sidebar = document.getElementById('sidebar');
        const btnToggleSidebar = document.getElementById('btnToggleSidebar');

        if (btnToggleSidebar && sidebar) {
            btnToggleSidebar.addEventListener('click', function() {
                sidebar.classList.toggle('show');
            });
        }

        // Fermeture des bandeaux de messages (succès / erreurs)
        document.querySelectorAll('.alert-registre-close').forEach(function(bouton) {
            bouton.addEventListener('click', function() {
                bouton.closest('.alert-registre').remove();
            });
        });

        // ===== Graphique : répartition des matériels par catégorie =====
        const donneesCategories = @json($categoriesChartData ?? []);

        // Débogage : à regarder dans la console (F12)
        console.log('Données du graphique :', donneesCategories);

        const ctxCategories = document.getElementById('graphiqueCategories');

        if (ctxCategories && donneesCategories.length > 0) {
            new Chart(ctxCategories, {
                type: 'bar',
                data: {
                    labels: donneesCategories.map(c => c.label),
                    datasets: [{
                        label: 'Quantité totale en stock',
                        data: donneesCategories.map(c => c.total),
                        backgroundColor: '#12283F',
                        hoverBackgroundColor: '#A9782C',
                        borderRadius: 2,
                        barThickness: 16,
                    }]
                },
                options: {
                    indexAxis: 'y',
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            backgroundColor: '#12283F',
                            padding: 10,
                            titleFont: { family: 'Inter' },
                            bodyFont: { family: 'Inter' },
                            callbacks: {
                                label: function(context) {
                                    return 'Quantité : ' + context.parsed.x;
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            beginAtZero: true,
                            grid: { color: '#E2DFD3' },
                            ticks: {
                                font: { family: 'Inter', size: 11 },
                                color: '#4B5665',
                                precision: 0,
                                stepSize: 1
                            }
                        },
                        y: {
                            grid: { display: false },
                            ticks: {
                                font: { family: 'Inter', size: 11 },
                                color: '#1C2530'
                            }
                        }
                    }
                }
            });
        } else if (ctxCategories) {
            // Si aucune donnée, on affiche un message
            const parent = ctxCategories.parentElement;
            parent.innerHTML = '<div class="text-center text-muted py-5">' +
                '<i class="fa-solid fa-chart-bar fa-2x mb-2 d-block"></i>' +
                'Aucune donnée à afficher. Ajoutez des matériels dans des catégories.' +
                '</div>';
        }
    </script>
</body>

</html>