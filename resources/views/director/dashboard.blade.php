<!-- resources/views/director/dashboard.blade.php -->
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de Bord - Directeur</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .sidebar {
            min-height: 100vh;
            background-color: #343a40;
            color: white;
        }
        .sidebar .nav-link {
            color: rgba(255, 255, 255, 0.8);
        }
        .sidebar .nav-link:hover, .sidebar .nav-link.active {
            color: white;
            background-color: rgba(255, 255, 255, 0.1);
        }
        .notification-badge {
            position: absolute;
            top: 0;
            right: 0;
            transform: translate(25%, -25%);
        }
        .progress {
            height: 10px;
        }
        .stats-card {
            transition: all 0.3s ease;
            border-radius: 0.5rem;
            overflow: hidden;
            height: 100%;
        }
        .stats-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        }
        .stats-icon {
            font-size: 2rem;
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 d-md-block sidebar collapse">
                <div class="position-sticky">
                <div class="d-flex align-items-center justify-content-center py-4 mb-3">
    <img src="{{ asset('images/logo.jpg') }}" alt="Logo" height="40" class="me-2">
    <span class="fs-4 text-white">{{ Auth::user()->name }}</span>
</div>
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link active" href="{{ route('director.dashboard') }}">
                                <i class="fas fa-tachometer-alt me-2"></i> Tableau de bord
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('director.pending_missions') }}">
                                <i class="fas fa-check-circle me-2"></i> Missions à valider
                                
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('director.all_missions') }}">
                                <i class="fas fa-list me-2"></i> Toutes les missions
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('director.departments') }}">
                                <i class="fas fa-university me-2"></i> Départements
                            </a>
                        </li>
                        <li class="nav-item">
    <a class="nav-link" href="{{ route('director.reports') }}">
        <i class="fas fa-chart-line me-2"></i> Rapports
    </a>
</li>

@php
    use App\Helpers\NotificationHelper;
@endphp
                                                <li class="nav-item position-relative">
                            <a class="nav-link" href="{{ route('notifications.index') }}">
                                <i class="fas fa-bell me-2"></i> Notifications
                                @if(\App\Helpers\NotificationHelper::getUnreadCount() > 0)
                                    <span class="badge bg-danger notification-badge">{{ \App\Helpers\NotificationHelper::getUnreadCount() }}</span>
                                @endif
                            </a>
                        </li>
                        <li class="nav-item mt-3">
                            <a class="nav-link" href="{{ route('director.settings') }}">
                                <i class="fas fa-cog me-2"></i> Paramètres
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-danger" href="{{ route('logout') }}" 
                               onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                <i class="fas fa-sign-out-alt me-2"></i> Déconnexion
                            </a>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                @csrf
                            </form>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Main Content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Tableau de bord du Directeur</h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <div class="btn-group me-2">
                            <button type="button" class="btn btn-sm btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-calendar-alt me-1"></i> {{ date('Y') }}
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="#">{{ date('Y') }}</a></li>
                                <li><a class="dropdown-item" href="#">{{ date('Y') - 1 }}</a></li>
                                <li><a class="dropdown-item" href="#">{{ date('Y') - 2 }}</a></li>
                            </ul>
                        </div>
                        <a href="{{ route('director.statistics') }}" class="btn btn-sm btn-outline-secondary">
                            <i class="fas fa-download me-1"></i> Rapport
                        </a>
                    </div>
                </div>

                <!-- Stats Cards Row -->
                <div class="row mb-4">
                    <div class="col-md-3 mb-4">
                        <div class="card stats-card border-0 shadow-sm">
                            <div class="card-body">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="stats-icon bg-primary bg-opacity-10 text-primary me-3">
                                        <i class="fas fa-clipboard-list"></i>
                                    </div>
                                    <div>
                                        <h6 class="text-muted mb-0">Missions en attente</h6>
                                        <h2 class="mb-0">{{ $missionStats['pending'] }}</h2>
                                    </div>
                                </div>
                                <div class="progress mb-2">
                                    <div class="progress-bar bg-primary" role="progressbar" style="width: 100%" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                                <div class="text-end">
                                    <a href="{{ route('director.pending_missions') }}" class="text-primary text-decoration-none">
                                        Voir les détails <i class="fas fa-arrow-right ms-1"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-3 mb-4">
                        <div class="card stats-card border-0 shadow-sm">
                            <div class="card-body">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="stats-icon bg-success bg-opacity-10 text-success me-3">
                                        <i class="fas fa-check-circle"></i>
                                    </div>
                                    <div>
                                        <h6 class="text-muted mb-0">Missions approuvées</h6>
                                        <h2 class="mb-0">{{ $missionStats['approved'] }}</h2>
                                    </div>
                                </div>
                                <div class="progress mb-2">
                                    <div class="progress-bar bg-success" role="progressbar" style="width: 100%" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                                <div class="text-end">
                                    <a href="{{ route('director.all_missions') }}?status=validee_directeur" class="text-success text-decoration-none">
                                        Voir les détails <i class="fas fa-arrow-right ms-1"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-3 mb-4">
                        <div class="card stats-card border-0 shadow-sm">
                            <div class="card-body">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="stats-icon bg-info bg-opacity-10 text-info me-3">
                                        <i class="fas fa-university"></i>
                                    </div>
                                    <div>
                                        <h6 class="text-muted mb-0">Départements</h6>
                                        <h2 class="mb-0">{{ $departmentCount }}</h2>
                                    </div>
                                </div>
                                <div class="progress mb-2">
                                    <div class="progress-bar bg-info" role="progressbar" style="width: 100%" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                                <div class="text-end">
                                    <a href="{{ route('director.departments') }}" class="text-info text-decoration-none">
                                        Voir les détails <i class="fas fa-arrow-right ms-1"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-3 mb-4">
                        <div class="card stats-card border-0 shadow-sm">
                            <div class="card-body">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="stats-icon bg-warning bg-opacity-10 text-warning me-3">
                                        <i class="fas fa-users"></i>
                                    </div>
                                    <div>
                                        <h6 class="text-muted mb-0">Enseignants</h6>
                                        <h2 class="mb-0">{{ $teacherCount }}</h2>
                                    </div>
                                </div>
                                <div class="progress mb-2">
                                    <div class="progress-bar bg-warning" role="progressbar" style="width: 100%" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                                <div class="text-end">
                                    <a href="#" class="text-warning text-decoration-none">
                                        Voir les détails <i class="fas fa-arrow-right ms-1"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mb-4">
                    <!-- Pending Missions -->
                    <div class="col-lg-7 mb-4">
                        <div class="card shadow-sm h-100">
                            <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                                <h5 class="mb-0">Missions en attente de validation</h5>
                                <a href="{{ route('director.pending_missions') }}" class="btn btn-sm btn-primary">
                                    Voir tout
                                </a>
                            </div>
                            <div class="card-body p-0">
                                @if($pendingMissions->isEmpty())
                                    <div class="p-4 text-center">
                                        <i class="fas fa-check-circle text-success fa-3x mb-3"></i>
                                        <p class="text-muted mb-0">Aucune mission en attente de validation.</p>
                                    </div>
                                @else
                                    <div class="table-responsive">
                                        <table class="table table-hover mb-0">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>Enseignant</th>
                                                    <th>Département</th>
                                                    <th>Mission</th>
                                                    <th>Destination</th>
                                                    <th>Dates</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($pendingMissions as $mission)
                                                    <tr>
                                                        <td>{{ $mission->user->name }}</td>
                                                        <td>{{ $mission->user->department }}</td>
                                                        <td>{{ $mission->title }}</td>
                                                        <td>{{ $mission->destination_city }}</td>
                                                        <td>{{ \Carbon\Carbon::parse($mission->start_date)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($mission->end_date)->format('d/m/Y') }}</td>
                                                        <td>
                                                            <div class="btn-group btn-group-sm">
                                                                <a href="{{ route('director.mission_details', $mission->id) }}" class="btn btn-outline-primary">
                                                                    <i class="fas fa-eye"></i>
                                                                </a>
                                                                <button class="btn btn-outline-success" data-bs-toggle="modal" data-bs-target="#approveModal{{ $mission->id }}">
                                                                    <i class="fas fa-check"></i>
                                                                </button>
                                                                <button class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#rejectModal{{ $mission->id }}">
                                                                    <i class="fas fa-times"></i>
                                                                </button>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Missions by Department -->
                    <div class="col-lg-5 mb-4">
                        <div class="card shadow-sm h-100">
                            <div class="card-header bg-white py-3">
                                <h5 class="mb-0">Missions par département</h5>
                            </div>
                            <div class="card-body">
                                <canvas id="departmentChart" height="300"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mb-4">
                    <!-- Monthly Activity Chart -->
                    <div class="col-lg-8 mb-4">
                        <div class="card shadow-sm h-100">
                            <div class="card-header bg-white py-3">
                                <h5 class="mb-0">Activité mensuelle ({{ date('Y') }})</h5>
                            </div>
                            <div class="card-body">
                                <canvas id="monthlyChart" height="300"></canvas>
                            </div>
                        </div>
                    </div>

                    <!-- Popular Destinations -->
                    <div class="col-lg-4 mb-4">
                        <div class="card shadow-sm h-100">
                            <div class="card-header bg-white py-3">
                                <h5 class="mb-0">Destinations populaires</h5>
                            </div>
                            <div class="card-body">
                                @if($popularDestinations->isEmpty())
                                    <div class="text-center p-4">
                                        <p class="text-muted mb-0">Aucune donnée disponible.</p>
                                    </div>
                                @else
                                    <div class="table-responsive">
                                        <table class="table">
                                            <thead>
                                                <tr>
                                                    <th>Destination</th>
                                                    <th>Missions</th>
                                                    <th>%</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @php
                                                    $totalMissions = $popularDestinations->sum('count');
                                                @endphp
                                                @foreach($popularDestinations as $destination)
                                                    <tr>
                                                        <td>{{ $destination->destination_city }}</td>
                                                        <td>{{ $destination->count }}</td>
                                                        <td>
                                                            @php
                                                                $percentage = $totalMissions > 0 ? round(($destination->count / $totalMissions) * 100) : 0;
                                                            @endphp
                                                            <div class="progress" style="height: 6px;">
                                                                <div class="progress-bar bg-info" role="progressbar" style="width: {{ $percentage }}%;" aria-valuenow="{{ $percentage }}" aria-valuemin="0" aria-valuemax="100"></div>
                                                            </div>
                                                            <small class="text-muted">{{ $percentage }}%</small>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Mission Type Distribution -->
                <div class="row mb-4">
                    <div class="col-md-6 mb-4">
                        <div class="card shadow-sm h-100">
                            <div class="card-header bg-white py-3">
                                <h5 class="mb-0">Types de missions</h5>
                            </div>
                            <div class="card-body d-flex flex-column justify-content-center">
                                <div class="row align-items-center">
                                    <div class="col-md-6">
                                        <canvas id="typeChart" height="200"></canvas>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <div>
                                                <i class="fas fa-map-marker-alt text-primary me-2"></i> Nationales
                                            </div>
                                            <span class="badge bg-primary">{{ $missionTypes['nationale'] }}</span>
                                        </div>
                                        <div class="progress mb-3" style="height: 8px;">
                                            @php
                                                $totalMissionTypes = array_sum($missionTypes);
                                                $nationalePercent = $totalMissionTypes > 0 ? ($missionTypes['nationale'] / $totalMissionTypes) * 100 : 0;
                                            @endphp
                                            <div class="progress-bar bg-primary" role="progressbar" style="width: {{ $nationalePercent }}%;" aria-valuenow="{{ $nationalePercent }}" aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>
                                        
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <div>
                                                <i class="fas fa-globe text-info me-2"></i> Internationales
                                            </div>
                                            <span class="badge bg-info">{{ $missionTypes['internationale'] }}</span>
                                        </div>
                                        <div class="progress" style="height: 8px;">
                                            @php
                                                $internationalePercent = $totalMissionTypes > 0 ? ($missionTypes['internationale'] / $totalMissionTypes) * 100 : 0;
                                            @endphp
                                            <div class="progress-bar bg-info" role="progressbar" style="width: {{ $internationalePercent }}%;" aria-valuenow="{{ $internationalePercent }}" aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6 mb-4">
                        <div class="card shadow-sm h-100">
                            <div class="card-header bg-white py-3">
                                <h5 class="mb-0">Récemment approuvées</h5>
                            </div>
                            <div class="card-body p-0">
                                @if($recentlyApprovedMissions->isEmpty())
                                    <div class="p-4 text-center">
                                        <p class="text-muted mb-0">Aucune mission approuvée récemment.</p>
                                    </div>
                                @else
                                    <div class="list-group list-group-flush">
                                        @foreach($recentlyApprovedMissions as $mission)
                                            <a href="{{ route('director.mission_details', $mission->id) }}" class="list-group-item list-group-item-action">
                                                <div class="d-flex w-100 justify-content-between">
                                                    <h6 class="mb-1">{{ $mission->title }}</h6>
                                                    <small class="text-success">
                                                        <i class="fas fa-check-circle me-1"></i> Approuvée
                                                    </small>
                                                </div>
                                                <div class="d-flex justify-content-between">
                                                    <p class="mb-1 text-muted">
                                                        <i class="fas fa-user me-1"></i> {{ $mission->user->name }}
                                                        <span class="ms-2"><i class="fas fa-building me-1"></i> {{ $mission->user->department }}</span>
                                                    </p>
                                                    <small>{{ \Carbon\Carbon::parse($mission->director_approval_date)->format('d/m/Y') }}</small>
                                                </div>
                                            </a>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Approval Modals -->
    @foreach($pendingMissions as $mission)
        <!-- Approve Mission Modal -->
        <div class="modal fade" id="approveModal{{ $mission->id }}" tabindex="-1" aria-labelledby="approveModalLabel{{ $mission->id }}" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title" id="approveModalLabel{{ $mission->id }}">Approuver la mission</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('director.process_mission', $mission->id) }}" method="POST">
                        @csrf
                        <input type="hidden" name="decision" value="approve">
                        <div class="modal-body">
                            <p>Vous êtes sur le point d'approuver la mission suivante :</p>
                            <div class="alert alert-info">
                                <strong>{{ $mission->title }}</strong><br>
                                <span>Enseignant : {{ $mission->user->name }}</span><br>
                                <span>Département : {{ $mission->user->department }}</span><br>
                                <span>Dates : {{ \Carbon\Carbon::parse($mission->start_date)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($mission->end_date)->format('d/m/Y') }}</span>
                            </div>
                            <div class="mb-3">
                                <label for="comments{{ $mission->id }}" class="form-label">Commentaires (optionnel)</label>
                                <textarea class="form-control" id="comments{{ $mission->id }}" name="comments" rows="3"></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                            <button type="submit" class="btn btn-success">Approuver la mission</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Reject Mission Modal -->
        <div class="modal fade" id="rejectModal{{ $mission->id }}" tabindex="-1" aria-labelledby="rejectModalLabel{{ $mission->id }}" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title" id="rejectModalLabel{{ $mission->id }}">Rejeter la mission</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('director.process_mission', $mission->id) }}" method="POST">
                        @csrf
                        <input type="hidden" name="decision" value="reject">
                        <div class="modal-body">
                            <p>Vous êtes sur le point de rejeter la mission suivante :</p>
                            <div class="alert alert-warning">
                                <strong>{{ $mission->title }}</strong><br>
                                <span>Enseignant : {{ $mission->user->name }}</span><br>
                                <span>Département : {{ $mission->user->department }}</span><br>
                                <span>Dates : {{ \Carbon\Carbon::parse($mission->start_date)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($mission->end_date)->format('d/m/Y') }}</span>
                            </div>
                            <div class="mb-3">
                                <label for="rejection_reason{{ $mission->id }}" class="form-label">Raison du rejet *</label>
                                <textarea class="form-control" id="rejection_reason{{ $mission->id }}" name="rejection_reason" rows="3" required></textarea>
                                <div class="form-text">Veuillez expliquer la raison du rejet. Cette explication sera visible par l'enseignant et le chef de département.</div>
                            </div>
                            <div class="mb-3">
                                <label for="comments{{ $mission->id }}" class="form-label">Commentaires supplémentaires (optionnel)</label>
                                <textarea class="form-control" id="comments{{ $mission->id }}" name="comments" rows="2"></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                            <button type="submit" class="btn btn-danger">Rejeter la mission</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endforeach

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Department Chart
        const departmentCtx = document.getElementById('departmentChart').getContext('2d');
        const departmentChart = new Chart(departmentCtx, {
            type: 'pie',
            data: {
                labels: Object.keys({!! json_encode($missionsByDepartment) !!}),
                datasets: [{
                    data: Object.values({!! json_encode($missionsByDepartment) !!}),
                    backgroundColor: [
                        'rgba(255, 99, 132, 0.6)',
                        'rgba(54, 162, 235, 0.6)',
                        'rgba(255, 206, 86, 0.6)',
                        'rgba(75, 192, 192, 0.6)',
                        'rgba(153, 102, 255, 0.6)',
                        'rgba(255, 159, 64, 0.6)',
                        'rgba(199, 199, 199, 0.6)',
                        'rgba(83, 102, 255, 0.6)',
                        'rgba(40, 159, 64, 0.6)',
                        'rgba(210, 102, 255, 0.6)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'right',
                    }
                }
            }
        });

        // Monthly Chart
        const monthlyCtx = document.getElementById('monthlyChart').getContext('2d');
        const monthlyChart = new Chart(monthlyCtx, {
            type: 'bar',
            data: {
                labels: @json($monthLabels),
                datasets: [{
                    label: 'Nombre de missions',
                    data: @json($monthData),
                    backgroundColor: 'rgba(54, 162, 235, 0.5)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0
                        }
                    }
                }
            }
        });

        // Mission Type Chart
        const typeCtx = document.getElementById('typeChart').getContext('2d');
        const typeChart = new Chart(typeCtx, {
            type: 'doughnut',
            data: {
                labels: ['Nationales', 'Internationales'],
                datasets: [{
                    data: [{{ $missionTypes['nationale'] }}, {{ $missionTypes['internationale'] }}],
                    backgroundColor: [
                        'rgba(0, 123, 255, 0.6)',
                        'rgba(23, 162, 184, 0.6)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                cutout: '70%'
            }
        });
    </script>
</body>
</html>