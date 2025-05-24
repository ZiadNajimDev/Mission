@extends('layouts.accountant')

@section('title', 'Détail du justificatif')

@section('page-title', 'Détail du justificatif')

@section('content')
<div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Détail du justificatif</h5>
        <div>
            <a href="{{ route('accountant.proofs.download', $proofDocument->id) }}" class="btn btn-sm btn-primary">
                <i class="fas fa-download me-1"></i> Télécharger
            </a>
            <a href="{{ route('accountant.proofs') }}" class="btn btn-sm btn-outline-secondary ms-2">
                <i class="fas fa-arrow-left me-1"></i> Retour
            </a>
        </div>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <h6 class="border-bottom pb-2 mb-3">Information sur le justificatif</h6>
                <div class="mb-3">
                    <p class="fw-bold mb-1">Titre:</p>
                    <p>{{ $proofDocument->title }}</p>
                </div>
                <div class="mb-3">
                    <p class="fw-bold mb-1">Type:</p>
                    <p>
                        @if($proofDocument->type == 'transport')
                            <span class="badge bg-info">Transport</span>
                        @elseif($proofDocument->type == 'hotel')
                            <span class="badge bg-primary">Hébergement</span>
                        @elseif($proofDocument->type == 'conference')
                            <span class="badge bg-warning">Conférence</span>
                        @else
                            <span class="badge bg-secondary">Autre</span>
                        @endif
                    </p>
                </div>
                <div class="mb-3">
                    <p class="fw-bold mb-1">Montant:</p>
                    <p>{{ number_format($proofDocument->amount, 0) }} DH</p>
                </div>
                <div class="mb-3">
                    <p class="fw-bold mb-1">Description:</p>
                    <p>{{ $proofDocument->description ?? 'Aucune description fournie' }}</p>
                </div>
                <div class="mb-3">
                    <p class="fw-bold mb-1">Statut actuel:</p>
                    <p>
                        @if($proofDocument->status == 'pending')
                            <span class="badge bg-warning">En attente</span>
                        @elseif($proofDocument->status == 'approved')
                            <span class="badge bg-success">Approuvé</span>
                        @else
                            <span class="badge bg-danger">Rejeté</span>
                        @endif
                    </p>
                </div>
                <div class="mb-3">
                    <p class="fw-bold mb-1">Date de soumission:</p>
                    <p>{{ $proofDocument->created_at->format('d/m/Y à H:i') }}</p>
                </div>
            </div>
            
            <div class="col-md-6">
                <h6 class="border-bottom pb-2 mb-3">Information sur la mission</h6>
                <div class="mb-3">
                    <p class="fw-bold mb-1">Mission:</p>
                    <p>{{ $proofDocument->mission->title }}</p>
                </div>
                <div class="mb-3">
                    <p class="fw-bold mb-1">Enseignant:</p>
                    <p>{{ $proofDocument->mission->user->name }}</p>
                </div>
                <div class="mb-3">
                    <p class="fw-bold mb-1">Département:</p>
                    <p>{{ $proofDocument->mission->user->department }}</p>
                </div>
                <div class="mb-3">
                    <p class="fw-bold mb-1">Destination:</p>
                    <p>{{ $proofDocument->mission->destination }}</p>
                </div>
                <div class="mb-3">
                    <p class="fw-bold mb-1">Dates de mission:</p>
                    <p>{{ \Carbon\Carbon::parse($proofDocument->mission->start_date)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($proofDocument->mission->end_date)->format('d/m/Y') }}</p>
                </div>
                
                @if($proofDocument->reviewed_at)
                    <h6 class="border-bottom pb-2 mb-3 mt-4">Information de vérification</h6>
                    <div class="mb-3">
                        <p class="fw-bold mb-1">Vérifié par:</p>
                        <p>{{ $proofDocument->reviewer->name ?? 'Système' }}</p>
                    </div>
                    <div class="mb-3">
                        <p class="fw-bold mb-1">Date de vérification:</p>
                        <p>{{ \Carbon\Carbon::parse($proofDocument->reviewed_at)->format('d/m/Y à H:i') }}</p>
                    </div>
                    @if($proofDocument->reviewer_comment)
                        <div class="mb-3">
                            <p class="fw-bold mb-1">Commentaire du vérificateur:</p>
                            <p>{{ $proofDocument->reviewer_comment }}</p>
                        </div>
                    @endif
                @endif
            </div>
        </div>
        
        @if($proofDocument->status == 'pending')
            <div class="row mt-4">
                <div class="col-12">
                    <h6 class="border-bottom pb-2 mb-3">Actions</h6>
                    <div class="d-flex gap-2">
                        <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#approveProofModal">
                            <i class="fas fa-check me-1"></i> Approuver
                        </button>
                        <button class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#rejectProofModal">
                            <i class="fas fa-times me-1"></i> Rejeter
                        </button>
                    </div>
                </div>
            </div>
        @endif
        
        <div class="row mt-4">
            <div class="col-12">
                <h6 class="border-bottom pb-2 mb-3">Aperçu du document</h6>
                <div class="document-preview p-3 border rounded text-center">
                    @php
                        $extension = pathinfo($proofDocument->document_path, PATHINFO_EXTENSION);
                    @endphp
                    
                    @if(in_array(strtolower($extension), ['jpg', 'jpeg', 'png', 'gif']))
                        <img src="{{ Storage::url($proofDocument->document_path) }}" alt="{{ $proofDocument->title }}" class="img-fluid" style="max-height: 500px;">
                    @elseif(strtolower($extension) === 'pdf')
                        <div class="pdf-preview">
                            <i class="fas fa-file-pdf fa-5x text-danger mb-3"></i>
                            <p>Le document est au format PDF. Veuillez le télécharger pour le consulter.</p>
                            <a href="{{ route('accountant.proofs.download', $proofDocument->id) }}" class="btn btn-primary">
                                <i class="fas fa-download me-1"></i> Télécharger le PDF
                            </a>
                        </div>
                    @else
                        <div class="file-preview">
                            <i class="fas fa-file fa-5x text-primary mb-3"></i>
                            <p>Le fichier n'est pas prévisualisable. Veuillez le télécharger pour le consulter.</p>
                            <a href="{{ route('accountant.proofs.download', $proofDocument->id) }}" class="btn btn-primary">
                                <i class="fas fa-download me-1"></i> Télécharger le fichier
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        
        <!-- Mission Completion Section -->
        <div class="row mt-4">
            <div class="col-12">
                <h6 class="border-bottom pb-2 mb-3">Compléter la mission</h6>
                
                @php
                    $mission = $proofDocument->mission;
                    $requiredCategories = ['financier', 'execution', 'retour'];
                    $approvedProofs = $mission->proofs()
                        ->whereIn('category', $requiredCategories)
                        ->where('status', 'approved')
                        ->get();
                    
                    $allApproved = $approvedProofs->count() >= count($requiredCategories);
                    $hasPayment = $mission->payment && $mission->payment->status == 'paid';
                    $endDatePassed = now()->greaterThan(\Carbon\Carbon::parse($mission->end_date));
                @endphp
                
                <div class="card bg-light">
                    <div class="card-body">
                        <h6>Conditions pour compléter la mission :</h6>
                        <ul class="list-group mb-3">
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Date de fin de mission passée
                                <span class="badge bg-{{ $endDatePassed ? 'success' : 'danger' }} rounded-pill">
                                    <i class="fas fa-{{ $endDatePassed ? 'check' : 'times' }}"></i>
                                </span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Tous les justificatifs approuvés ({{ $approvedProofs->count() }}/{{ count($requiredCategories) }})
                                <span class="badge bg-{{ $allApproved ? 'success' : 'danger' }} rounded-pill">
                                    <i class="fas fa-{{ $allApproved ? 'check' : 'times' }}"></i>
                                </span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Paiement effectué
                                <span class="badge bg-{{ $hasPayment ? 'success' : 'danger' }} rounded-pill">
                                    <i class="fas fa-{{ $hasPayment ? 'check' : 'times' }}"></i>
                                </span>
                            </li>
                        </ul>
                        
                        @if($mission->status === 'billet_reserve' && $allApproved && $hasPayment && $endDatePassed)
                            <form action="{{ route('accountant.missions.complete', $mission->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-success w-100">
                                    <i class="fas fa-check-circle me-2"></i> Marquer la mission comme terminée
                                </button>
                            </form>
                        @else
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                @if(!$allApproved)
                                    Certains justificatifs ne sont pas approuvés.
                                @elseif(!$hasPayment)
                                    Le paiement n'a pas été effectué.
                                @elseif(!$endDatePassed)
                                    La mission n'est pas encore terminée (date future).
                                @else
                                    Certaines conditions ne sont pas remplies pour compléter cette mission.
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Approve Proof Modal -->
<div class="modal fade" id="approveProofModal" tabindex="-1" aria-labelledby="approveProofModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="approveProofModalLabel">Approuver le justificatif</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="{{ route('accountant.proofs.process', $proofDocument->id) }}">
                @csrf
                <input type="hidden" name="status" value="approved">
                <div class="modal-body">
                    <p>Vous êtes sur le point d'approuver ce justificatif.</p>
                    
                    <div class="mb-3">
                        <label for="approveComment" class="form-label">Commentaire (optionnel)</label>
                        <textarea class="form-control" id="approveComment" name="reviewer_comment" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-success">Approuver</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Reject Proof Modal -->
<div class="modal fade" id="rejectProofModal" tabindex="-1" aria-labelledby="rejectProofModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="rejectProofModalLabel">Rejeter le justificatif</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="{{ route('accountant.proofs.process', $proofDocument->id) }}">
                @csrf
                <input type="hidden" name="status" value="rejected">
                <div class="modal-body">
                    <p>Vous êtes sur le point de rejeter ce justificatif.</p>
                    
                    <div class="mb-3">
                        <label for="rejectComment" class="form-label">Motif du rejet *</label>
                        <textarea class="form-control" id="rejectComment" name="reviewer_comment" rows="3" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-danger">Rejeter</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection