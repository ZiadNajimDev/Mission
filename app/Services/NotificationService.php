<?php
// app/Services/NotificationService.php


namespace App\Services;

use App\Models\User;
use App\Models\Notification;
use App\Models\Mission;

class NotificationService
{
    // Create notification for a specific user
    public function notifyUser($userId, $title, $message, $type = 'primary', $icon = 'bell', $link = null, $relatedId = null)
    {
        return Notification::create([
            'user_id' => $userId,
            'title' => $title,
            'message' => $message,
            'type' => $type,
            'icon' => $icon,
            'link' => $link,
            'related_id' => $relatedId
        ]);
    }

    // Notify users by role
    public function notifyByRole($role, $title, $message, $type = 'primary', $icon = 'bell', $link = null, $relatedId = null)
    {
        $users = User::where('role', $role)->get();
        foreach ($users as $user) {
            $this->notifyUser($user->id, $title, $message, $type, $icon, $link, $relatedId);
        }
    }

    // Notify when mission is submitted
    public function notifyMissionSubmitted(Mission $mission)
    {
        // Get department head
        $departmentHeads = User::where('role', 'chef_departement')
            ->where('department', $mission->user->department)
            ->get();
        
        foreach ($departmentHeads as $head) {
            $this->notifyUser(
                $head->id,
                'Nouvelle mission soumise',
                'Une nouvelle mission a été soumise par ' . $mission->user->name,
                'primary',
                'paper-plane',
                route('chef.mission_validate'),
                $mission->id
            );
        }
    }

    // Notify when mission is validated by department head
    public function notifyMissionValidatedByHead(Mission $mission)
    {
        // Notify teacher
        $this->notifyUser(
            $mission->user_id,
            'Mission validée par chef de département',
            'Votre mission a été validée par le chef de département.',
            'success',
            'check-circle',
            route('teacher.missions.show', $mission->id),
            $mission->id
        );

        // Notify director
        $this->notifyByRole(
            'directeur',
            'Nouvelle mission à valider',
            'Une mission validée par le chef de département est en attente de votre validation.',
            'primary',
            'info-circle',
            route('director.pending_missions'),
            $mission->id
        );
    }

    // Notify when mission is validated by director
    public function notifyMissionValidatedByDirector(Mission $mission)
    {
        // Notify teacher
        $this->notifyUser(
            $mission->user_id,
            'Mission approuvée',
            'Votre mission a été approuvée par le directeur.',
            'success',
            'check-circle',
            route('teacher.missions.show', $mission->id),
            $mission->id
        );

        // Notify accountant
        $this->notifyByRole(
            'comptable',
            'Nouvelle mission approuvée',
            'Une nouvelle mission a été approuvée et nécessite des réservations.',
            'info',
            'plane',
            route('accountant.reservations'),
            $mission->id
        );

        // Notify department head
        $departmentHeads = User::where('role', 'chef_departement')
            ->where('department', $mission->user->department)
            ->get();
        
        foreach ($departmentHeads as $head) {
            $this->notifyUser(
                $head->id,
                'Mission approuvée par le directeur',
                'Une mission de votre département a été approuvée par le directeur.',
                'success',
                'check-circle',
                null,
                $mission->id
            );
        }
    }

    // Notify when mission is rejected
    public function notifyMissionRejected(Mission $mission, $rejectedBy)
    {
        $rejectorRole = $rejectedBy === 'chef' ? 'chef de département' : 'directeur';
        
        // Notify teacher
        $this->notifyUser(
            $mission->user_id,
            'Mission rejetée',
            'Votre mission a été rejetée par le ' . $rejectorRole . '.',
            'danger',
            'times-circle',
            route('teacher.missions.show', $mission->id),
            $mission->id
        );
    }

    // Notify when travel arrangements are made
    public function notifyTravelArranged(Mission $mission)
    {
        // Notify teacher
        $this->notifyUser(
            $mission->user_id,
            'Voyage réservé',
            'Vos arrangements de voyage ont été effectués.',
            'success',
            'ticket-alt',
            route('teacher.missions.show', $mission->id),
            $mission->id
        );
    }

    // Notify when proof documents are required
    public function notifyProofRequired(Mission $mission)
    {
        // Notify teacher
        $this->notifyUser(
            $mission->user_id,
            'Justificatifs requis',
            'N\'oubliez pas de soumettre vos justificatifs pour la mission.',
            'warning',
            'exclamation-circle',
            route('teacher.proof_documents'),
            $mission->id
        );
    }
}