<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Codes d'invitation — Comptabilité-Matières</title>

    <link href="{{ asset('vendor/fonts/inter/inter.css') }}" rel="stylesheet">
    <link href="{{ asset('vendor/fonts/fraunces/fraunces.css') }}" rel="stylesheet">
    <link href="{{ asset('vendor/fonts/ibm-plex-mono/ibm-plex-mono.css') }}" rel="stylesheet">

    <link href="{{ asset('vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('vendor/fontawesome/css/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin/admin.css') }}">
</head>

<body>

    @php
        $activeMenu = 'codes';
        $pageTitle = 'Codes d\'invitation';
        $pageSubtitle = 'Générer et gérer les codes d\'accès à la plateforme';
    @endphp

    @include('User\Layouts\Sidebar')

    <div class="main-content">

        @include('User\Layouts\Navbar')

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
                <div class="alert-registre alert-registre-error" role="alert">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                    <div class="alert-registre-content">
                        <span class="alert-registre-title">Erreur</span>
                        {{ $errors->first() }}
                    </div>
                    <button type="button" class="alert-registre-close" aria-label="Fermer">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>
            @endif

            {{-- ================= TOOLBAR ================= --}}
            <div class="list-toolbar">
                <form method="POST" action="{{ route('admin.codes.generate') }}">
                    @csrf
                    <button type="submit" class="btn btn-navy">
                        <i class="fa-solid fa-plus me-2"></i>Générer un nouveau code
                    </button>
                </form>

                <div class="search-box">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    <input type="text" id="searchCode" placeholder="Rechercher un code…">
                </div>
            </div>

            {{-- ================= TABLE ================= --}}
            <div class="list-panel">
                <div class="table-responsive">
                    <table class="table align-middle" id="tableCodes">
                        <thead>
                            <tr>
                                <th>Code</th>
                                <th>Créé par</th>
                                <th>Créé le</th>
                                <th>Expire le</th>
                                <th>Statut</th>
                                <th>Utilisé par</th>
                                <th class="text-end">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($codes as $code)
                                @php
                                    if ($code->used_by) {
                                        $statut = 'Utilisé';
                                        $badgeClass = 'badge-sortie';
                                    } elseif ($code->expires_at && $code->expires_at->isPast()) {
                                        $statut = 'Expiré';
                                        $badgeClass = 'badge-retour';
                                    } else {
                                        $statut = 'Disponible';
                                        $badgeClass = 'badge-entree';
                                    }
                                @endphp
                                <tr>
                                    <td>
                                        <span class="code-display" id="code-{{ $code->id }}">
                                            {{ $code->code }}
                                        </span>
                                    </td>
                                    <td>{{ $code->creator->name ?? '—' }}</td>
                                    <td>{{ $code->created_at->format('d/m/Y H:i') }}</td>
                                    <td>
                                        @if ($code->expires_at)
                                            {{ $code->expires_at->format('d/m/Y H:i') }}
                                        @else
                                            <span class="text-muted">Illimité</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge-mouvement {{ $badgeClass }}">
                                            {{ $statut }}
                                        </span>
                                    </td>
                                    <td>
                                        @if ($code->usedBy)
                                            {{ $code->usedBy->name }} {{ $code->usedBy->surname }}
                                            <br>
                                            <small class="text-muted">
                                                le {{ $code->used_at?->format('d/m/Y H:i') }}
                                            </small>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        @if (!$code->used_by)
                                            <button type="button" 
                                                    class="btn-action" 
                                                    onclick="copierCode('{{ $code->code }}')"
                                                    title="Copier le code">
                                                <i class="fa-solid fa-copy"></i>
                                            </button>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-4">
                                        <i class="fa-solid fa-key fa-2x mb-2 d-block"></i>
                                        Aucun code d'invitation généré pour le moment.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>

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

        // ===== Fermeture des alertes =====
        document.querySelectorAll('.alert-registre-close').forEach(function(bouton) {
            bouton.addEventListener('click', function() {
                bouton.closest('.alert-registre').remove();
            });
        });

        // ===== Copier le code dans le presse-papier (compatible HTTP) =====
        function copierCode(code) {
            // Méthode 1 : navigator.clipboard (fonctionne en HTTPS ou localhost)
            if (navigator.clipboard && window.isSecureContext) {
                navigator.clipboard.writeText(code).then(function () {
                    afficherNotificationCopie(code);
                }).catch(function () {
                    copierViaTextarea(code);
                });
            } else {
                // Méthode 2 : Fallback pour HTTP
                copierViaTextarea(code);
            }
        }

        // ===== Méthode alternative avec un textarea temporaire =====
        function copierViaTextarea(code) {
            const textarea = document.createElement('textarea');
            textarea.value = code;
            textarea.style.position = 'fixed';
            textarea.style.left = '-9999px';
            document.body.appendChild(textarea);
            textarea.select();
            textarea.setSelectionRange(0, 99999);

            try {
                document.execCommand('copy');
                afficherNotificationCopie(code);
            } catch (err) {
                alert('Impossible de copier le code. Copiez-le manuellement : ' + code);
            }

            document.body.removeChild(textarea);
        }

        // ===== Notification visuelle =====
        function afficherNotificationCopie(code) {
            const notif = document.createElement('div');
            notif.className = 'alert-registre alert-registre-success';
            notif.style.position = 'fixed';
            notif.style.top = '20px';
            notif.style.right = '20px';
            notif.style.zIndex = '9999';
            notif.style.maxWidth = '350px';
            notif.innerHTML = `
                <i class="fa-solid fa-circle-check"></i>
                <div class="alert-registre-content">
                    <span class="alert-registre-title">Copié !</span>
                    Le code <strong>${code}</strong> a été copié dans le presse-papier.
                </div>
            `;
            document.body.appendChild(notif);
            setTimeout(() => notif.remove(), 3000);
        }

        // ===== Recherche dans la table =====
        const searchInput = document.getElementById('searchCode');
        if (searchInput) {
            searchInput.addEventListener('input', function () {
                const term = this.value.toLowerCase();
                document.querySelectorAll('#tableCodes tbody tr').forEach(row => {
                    row.style.display = row.textContent.toLowerCase().includes(term) ? '' : 'none';
                });
            });
        }
    </script>

    <style>
        .code-display {
            font-family: 'IBM Plex Mono', monospace;
            font-weight: 600;
            font-size: 0.95rem;
            background-color: #f1f3f5;
            padding: 4px 10px;
            border-radius: 6px;
            color: #12283F;
            letter-spacing: 1px;
        }
    </style>
</body>

</html>