<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Paramètres — Comptabilité-Matières</title>

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
            <div class="row g-3">
                <div class="col-12">
                    <div class="panel">
                        <p class="panel-title mb-0">Paramètres</p>
                        <p class="panel-subtitle mb-3">Configuration de l'application</p>
                        <div class="alert-stock-empty">
                            <i class="fa-solid fa-circle-info"></i>
                            Page en construction.
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
    </script>
</body>

</html>