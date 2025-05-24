
@extends('layouts.accountant')

@section('title', 'Paiements')

@section('page-title', 'Gestion des paiements')

@section('content')
<div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Gestion des paiements</h5>
        <div>
            <div class="btn-group" role="group">
                <input type="radio" class="btn-check" name="paymentFilter" id="filter-all" autocomplete="off" checked>
                <label class="btn btn-outline-primary" for="filter-all">Tous</label>

                <input type="radio" class="btn-check" name="paymentFilter" id="filter-pending" autocomplete="off">
                <label class="btn btn-outline-primary" for="filter-pending">En attente</label>

                <input type="radio" class="btn-check" name="paymentFilter" id="filter-paid" autocomplete="off">
                <label class="btn btn-outline-primary" for="filter-paid">Payés</label>
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
                        <th>Dates</th>
                        <th>Type</th>
                        <th>Indemnités</th>
                        <th>Frais</th>
                        <th>Total</th>
                        <th>Statut</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($missions as $mission)
                        <tr class="payment-row" data-payment-status="{{ $mission->payment ? $mission->payment->status : 'pending' }}">
                            <td>{{ $mission->title }}</td>
                            <td>{{ $mission->user->name }}</td>
                            <td>{{ \Carbon\Carbon::parse($mission->start_date)->format('d-d') }} {{ \Carbon\Carbon::parse($mission->start_date)->format('M Y') }}</td>
                            <td>
                                @if($mission->type == 'internationale')
                                    <span class="badge bg-info">Internationale</span>
                                @else
                                    <span class="badge bg-primary">Nationale</span>
                                @endif
                            </td>
                            
                            @php
                                $startDate = \Carbon\Carbon::parse($mission->start_date);
                                $endDate = \Carbon\Carbon::parse($mission->end_date);
                                $durationDays = $startDate->diffInDays($endDate) + 1;
                                
                                $dailyAllowance = $mission->type === 'internationale' ? 2000 : 400;
                                $allowanceAmount = $dailyAllowance * $durationDays;
                                
                                $transportAmount = $mission->reservations->sum('cost');
                                $totalAmount = $allowanceAmount + $transportAmount;
                            @endphp
                            
                            <td>{{ number_format($allowanceAmount, 0) }} DH</td>
                            <td>{{ number_format($transportAmount, 0) }} DH</td>
                            <td>{{ number_format($totalAmount, 0) }} DH</td>
                            <td>
                                @if($mission->payment && $mission->payment->status === 'paid')
                                    <span class="badge bg-success">Payé</span>
                                @else
                                    <span class="badge bg-warning">En attente</span>
                                @endif
                            </td>
                            <td>
                                @if($mission->payment && $mission->payment->status === 'paid')
                                    <a href="{{ route('accountant.payments.print', $mission->payment->id) }}" class="btn btn-sm btn-outline-secondary">
                                        <i class="fas fa-print"></i>
                                    </a>
                                @else
                                    <button class="btn btn-sm btn-outline-primary me-1" data-bs-toggle="modal" data-bs-target="#paymentModal" 
                                        data-mission-id="{{ $mission->id }}"
                                        data-mission-details="{{ json_encode([
                                            'id' => $mission->id,
                                            'title' => $mission->title,
                                            'teacher' => $mission->user->name,
                                            'allowance' => number_format($allowanceAmount, 0),
                                            'transport' => number_format($transportAmount, 0),
                                            'total' => number_format($totalAmount, 0),
                                        ]) }}">
                                        <i class="fas fa-money-bill-wave"></i>
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center">Aucune mission à afficher</td>
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

<!-- Payment Modal -->
<div class="modal fade" id="paymentModal" tabindex="-1" aria-labelledby="paymentModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="paymentModalLabel">Effectuer un paiement</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="paymentForm" method="POST" action="{{ route('accountant.payments.store') }}">
                @csrf
                <input type="hidden" name="mission_id" id="mission_id">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Mission</label>
                        <input type="text" class="form-control" id="mission-title" readonly>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Enseignant</label>
                        <input type="text" class="form-control" id="teacher-name" readonly>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Indemnités journalières</label>
                            <input type="text" class="form-control" id="allowance-amount" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Frais de transport</label>
                            <input type="text" class="form-control" id="transport-amount" readonly>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Total à payer</label>
                        <input type="text" class="form-control fw-bold" id="total-amount" readonly>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Méthode de paiement *</label>
                        <select class="form-select" name="payment_method" required>
                            <option value="" selected disabled>Choisir</option>
                            <option value="virement">Virement bancaire</option>
                            <option value="cheque">Chèque</option>
                            <option value="especes">Espèces</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Référence de paiement</label>
                        <input type="text" class="form-control" name="payment_reference" placeholder="Numéro de chèque/virement">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Date de paiement *</label>
                        <input type="date" class="form-control" name="payment_date" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Commentaire</label>
                        <textarea class="form-control" name="comments" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">Enregistrer le paiement</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Handle payment modal
        var paymentModal = document.getElementById('paymentModal')
        paymentModal.addEventListener('show.bs.modal', function (event) {
            var button = event.relatedTarget
            var missionId = button.getAttribute('data-mission-id')
            var missionDetails = JSON.parse(button.getAttribute('data-mission-details'))
            
            var modal = this
            modal.querySelector('#mission_id').value = missionId
            modal.querySelector('#mission-title').value = missionDetails.title
            modal.querySelector('#teacher-name').value = missionDetails.teacher
            modal.querySelector('#allowance-amount').value = missionDetails.allowance + ' DH'
            modal.querySelector('#transport-amount').value = missionDetails.transport + ' DH'
            modal.querySelector('#total-amount').value = missionDetails.total + ' DH'
            
            // Set today's date as default payment date
            document.querySelector('input[name="payment_date"]').valueAsDate = new Date()
        })
        
        // Filter functionality
        const filterButtons = document.querySelectorAll('[name="paymentFilter"]')
        filterButtons.forEach(button => {
            button.addEventListener('change', function() {
                const filterValue = this.id.replace('filter-', '')
                const rows = document.querySelectorAll('.payment-row')
                
                rows.forEach(row => {
                    if (filterValue === 'all') {
                        row.style.display = 'table-row'
                    } else if (filterValue === 'pending') {
                        const status = row.getAttribute('data-payment-status')
                        if (status === 'pending') {
                            row.style.display = 'table-row'
                        } else {
                            row.style.display = 'none'
                        }
                    } else if (filterValue === 'paid') {
                        const status = row.getAttribute('data-payment-status')
                        if (status === 'paid') {
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