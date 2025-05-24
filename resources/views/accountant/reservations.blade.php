
@extends('layouts.accountant')

@section('title', 'Réservations')

@section('page-title', 'Gestion des réservations')

@section('content')
<div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Gestion des réservations</h5>
        <div>
            <div class="btn-group" role="group">
                <input type="radio" class="btn-check" name="reservationFilter" id="filter-all" autocomplete="off" checked>
                <label class="btn btn-outline-primary" for="filter-all">Toutes</label>

                <input type="radio" class="btn-check" name="reservationFilter" id="filter-pending" autocomplete="off">
                <label class="btn btn-outline-primary" for="filter-pending">En attente</label>

                <input type="radio" class="btn-check" name="reservationFilter" id="filter-completed" autocomplete="off">
                <label class="btn btn-outline-primary" for="filter-completed">Complétées</label>

                <input type="radio" class="btn-check" name="reservationFilter" id="filter-urgent" autocomplete="off">
                <label class="btn btn-outline-primary" for="filter-urgent">Urgentes</label>
            </div>
        </div>
    </div>
    <div class="card-body">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Mission</th>
                        <th>Enseignant</th>
                        <th>Département</th>
                        <th>Dates</th>
                        <th>Type</th>
                        <th>Transport</th>
                        <th>Statut</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($missions as $mission)
                        <tr class="reservation-row" 
                            data-status="{{ $mission->status }}" 
                            data-days-until="{{ $mission->start_date ? \Carbon\Carbon::now()->diffInDays(\Carbon\Carbon::parse($mission->start_date), false) : 0 }}">
                            <td>
                                <div class="fw-bold">{{ $mission->title }}</div>
                                <small>{{ $mission->destination }}</small>
                            </td>
                            <td>{{ $mission->user->name }}</td>
                            <td>{{ $mission->user->department }}</td>
                            <td>{{ \Carbon\Carbon::parse($mission->start_date)->format('d-d') }} {{ \Carbon\Carbon::parse($mission->start_date)->format('M Y') }}</td>
                            <td>
                                @if($mission->type == 'international')
                                    <span class="badge bg-info">Internationale</span>
                                @else
                                    <span class="badge bg-primary">Nationale</span>
                                @endif
                            </td>
                            <td>
                                @if($mission->reservations->count() > 0)
                                    @php
                                        $transportTypes = [];
                                        foreach($mission->reservations as $res) {
                                            if($res->type == 'flight') $transportTypes[] = 'Vol';
                                            if($res->type == 'train') $transportTypes[] = 'Train';
                                            if($res->type == 'hotel') $transportTypes[] = 'Hôtel';
                                        }
                                    @endphp
                                    {{ implode(' + ', $transportTypes) }}
                                @else
                                    À déterminer
                                @endif
                            </td>
                            <td>
                                @php
                                    $daysUntil = \Carbon\Carbon::now()->diffInDays(\Carbon\Carbon::parse($mission->start_date), false);
                                @endphp
                                
                                @if($mission->status == 'validee_directeur')
    @if($daysUntil < 7)
        <span class="badge bg-danger">À faire avant {{ \Carbon\Carbon::now()->addDays(3)->format('d/m') }}</span>
    @elseif($daysUntil < 14)
        <span class="badge bg-warning">À faire avant {{ \Carbon\Carbon::now()->addDays(5)->format('d/m') }}</span>
    @else
        <span class="badge bg-info">À réserver</span>
    @endif
@elseif($mission->status == 'billet_reserve')
    <span class="badge bg-success">Complétée</span>
@else
    <span class="badge bg-secondary">{{ ucfirst($mission->status) }}</span>
@endif
                            </td>
                            <td>
                                <button class="btn btn-sm btn-outline-primary me-1" data-bs-toggle="modal" data-bs-target="#reservationModal" 
                                    data-mission-id="{{ $mission->id }}"
                                    data-mission-details="{{ json_encode([
                                        'id' => $mission->id,
                                        'teacher' => $mission->user->name,
                                        'department' => $mission->user->department,
                                        'title' => $mission->title,
                                        'destination' => $mission->destination,
                                        'start_date' => \Carbon\Carbon::parse($mission->start_date)->format('d/m/Y'),
                                        'end_date' => \Carbon\Carbon::parse($mission->end_date)->format('d/m/Y'),
                                        'type' => $mission->type,
                                    ]) }}">
                                    @if($mission->status == 'billet_reserve')
                                        <i class="fas fa-eye"></i>
                                    @else
                                        <i class="fas fa-edit"></i>
                                    @endif
                                </button>
                                
                                @if(in_array($mission->status, ['billet_reserve', 'terminee']))
    {{-- Show view/print button if reservation is done or mission is complete --}}
    <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#reservationModal" 
        data-mission-id="{{ $mission->id }}"
        data-mission-details="{{ json_encode([ /* ... existing details ... */ ]) }}">
        <i class="fas fa-eye"></i> {{-- Or fas fa-print if that's the intended action --}}
    </button>
@elseif($mission->status == 'validee_directeur')
    {{-- Show mark complete button only if waiting for reservation --}}
    <button class="btn btn-sm btn-success mark-complete-btn" data-mission-id="{{ $mission->id }}">
        <i class="fas fa-check"></i>
    </button>
@else
    {{-- Optionally handle other statuses or show nothing --}}
    {{-- <span class="text-muted">-</span> --}}
@endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center">Aucune mission à afficher</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($missions->count() > 0)
            <nav aria-label="Page navigation" class="mt-3">
                <ul class="pagination justify-content-center">
                    <li class="page-item {{ $missions->onFirstPage() ? 'disabled' : '' }}">
                        <a class="page-link" href="{{ $missions->previousPageUrl() }}" tabindex="-1" aria-disabled="{{ $missions->onFirstPage() ? 'true' : 'false' }}">Précédent</a>
                    </li>
                    
                    @for($i = 1; $i <= $missions->lastPage(); $i++)
                        <li class="page-item {{ $missions->currentPage() == $i ? 'active' : '' }}">
                            <a class="page-link" href="{{ $missions->url($i) }}">{{ $i }}</a>
                        </li>
                    @endfor
                    
                    <li class="page-item {{ $missions->hasMorePages() ? '' : 'disabled' }}">
                        <a class="page-link" href="{{ $missions->nextPageUrl() }}">Suivant</a>
                    </li>
                </ul>
            </nav>
        @endif
    </div>
</div>

<!-- Reservation Modal -->
<div class="modal fade" id="reservationModal" tabindex="-1" aria-labelledby="reservationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="reservationModalLabel">Gestion de réservation</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="reservationForm" method="POST" action="{{ route('accountant.reservations.store') }}" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="mission_id" id="mission_id">
                
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Détails de la mission</h6>
                            <div class="table-responsive">
                                <table class="table table-sm table-borderless">
                                    <tbody>
                                        <tr>
                                            <th width="40%">Enseignant:</th>
                                            <td id="teacher-name"></td>
                                        </tr>
                                        <tr>
                                            <th>Département:</th>
                                            <td id="department-name"></td>
                                        </tr>
                                        <tr>
                                            <th>Type:</th>
                                            <td id="mission-type"></td>
                                        </tr>
                                        <tr>
                                            <th>Titre:</th>
                                            <td id="mission-title"></td>
                                        </tr>
                                        <tr>
                                            <th>Dates:</th>
                                            <td id="mission-dates"></td>
                                        </tr>
                                        <tr>
                                            <th>Destination:</th>
                                            <td id="mission-destination"></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h6>Réservation</h6>
                            <div class="mb-3">
                                <label class="form-label">Type de transport *</label>
                                <select class="form-select" name="type" id="transport-type" required>
                                    <option value="" selected disabled>Choisir</option>
                                    <option value="flight">Avion</option>
                                    <option value="train">Train</option>
                                    <option value="bus">Bus</option>
                                    <option value="car">Voiture personnelle</option>
                                </select>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Date de départ *</label>
                                    <input type="date" name="departure_date" class="form-control" id="departure-date" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Date de retour *</label>
                                    <input type="date" name="return_date" class="form-control" id="return-date" required>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Compagnie de transport *</label>
                                <input type="text" name="provider" class="form-control" placeholder="Air France, ONCF, etc." required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Numéro de vol/train *</label>
                                <input type="text" name="reservation_number" class="form-control" placeholder="AF1234, TNR123, etc." required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Coût du transport (DH) *</label>
                                <input type="number" name="cost" class="form-control" step="0.01" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Hébergement nécessaire</label>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="needHotel" name="need_hotel">
                                    <label class="form-check-label" for="needHotel">
                                        Réserver un hôtel
                                    </label>
                                </div>
                            </div>

                            <div id="hotelInfo" style="display: none;">
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Nom de l'hôtel</label>
                                        <input type="text" name="hotel_name" class="form-control" placeholder="Hôtel Mercure">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Coût de l'hôtel (DH)</label>
                                        <input type="number" name="hotel_cost" class="form-control" step="0.01">
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Documents</label>
                                <input class="form-control" type="file" name="attachment" id="reservationDocuments">
                                <div class="form-text">Joindre les billets/réservations</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">Enregistrer la réservation</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="confirmCompleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">Marquer comme complété</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Êtes-vous sûr de vouloir marquer cette réservation comme complétée? Cette action mettra à jour le statut de la mission.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <form id="complete-form" action="" method="POST">
                    @csrf
                    @method('PUT')
                    <button type="submit" class="btn btn-success">Confirmer</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Handle reservation modal
        var reservationModal = document.getElementById('reservationModal')
        reservationModal.addEventListener('show.bs.modal', function (event) {
            var button = event.relatedTarget
            var missionId = button.getAttribute('data-mission-id')
            var missionDetails = JSON.parse(button.getAttribute('data-mission-details'))
            
            var modal = this
            modal.querySelector('#mission_id').value = missionId
            modal.querySelector('#teacher-name').textContent = missionDetails.teacher
            modal.querySelector('#department-name').textContent = missionDetails.department
            modal.querySelector('#mission-title').textContent = missionDetails.title
            modal.querySelector('#mission-destination').textContent = missionDetails.destination
            modal.querySelector('#mission-dates').textContent = missionDetails.start_date + ' - ' + missionDetails.end_date
            
            // Set dates in form
            document.getElementById('departure-date').value = missionDetails.start_date.split('/').reverse().join('-');
            document.getElementById('return-date').value = missionDetails.end_date.split('/').reverse().join('-');
            
            if (missionDetails.type === 'international') {
                modal.querySelector('#mission-type').innerHTML = '<span class="badge bg-info">Internationale</span>'
            } else {
                modal.querySelector('#mission-type').innerHTML = '<span class="badge bg-primary">Nationale</span>'
            }
        })
        
        // Show/hide hotel info based on checkbox
        document.getElementById('needHotel').addEventListener('change', function() {
            const hotelInfo = document.getElementById('hotelInfo');
            if (this.checked) {
                hotelInfo.style.display = 'block';
            } else {
                hotelInfo.style.display = 'none';
            }
        });
        
        // Mark complete button functionality
        const markCompleteBtns = document.querySelectorAll('.mark-complete-btn');
        markCompleteBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                const missionId = this.getAttribute('data-mission-id');
                const completeForm = document.getElementById('complete-form');
                completeForm.action = `/accountant/reservations/${missionId}/complete`;
                
                const confirmModal = new bootstrap.Modal(document.getElementById('confirmCompleteModal'));
                confirmModal.show();
            });
        });
        
        // Filter functionality
        const filterButtons = document.querySelectorAll('[name="reservationFilter"]')
        filterButtons.forEach(button => {
            button.addEventListener('change', function() {
                const filterValue = this.id.replace('filter-', '')
                const rows = document.querySelectorAll('.reservation-row')
                
rows.forEach(row => {
    if (filterValue === 'all') {
        row.style.display = 'table-row'
    } else if (filterValue === 'pending') {
        const status = row.getAttribute('data-status')
        if (status === 'validee_directeur') {
            row.style.display = 'table-row'
        } else {
            row.style.display = 'none'
        }
    } else if (filterValue === 'completed') {
        const status = row.getAttribute('data-status')
        if (status === 'billet_reserve') {
            row.style.display = 'table-row'
        } else {
            row.style.display = 'none'
        }
    } else if (filterValue === 'urgent') {
        const daysUntil = parseInt(row.getAttribute('data-days-until'), 10)
        if (daysUntil >= 0 && daysUntil < 10) {
            row.style.display = 'table-row'
        } else {
            row.style.display = 'none'
        }
    }
})
            })
        })
    })
</script>
@endsection