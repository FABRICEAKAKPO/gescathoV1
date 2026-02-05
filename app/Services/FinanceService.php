<?php

namespace App\Services;

use App\Models\Recette;
use App\Models\Depense;
use App\Models\User;
use Carbon\Carbon;

class FinanceService
{
    /**
     * Récupère les données financières pour un utilisateur spécifique
     * 
     * @param int|null $userId ID de l'utilisateur (null pour tous les utilisateurs)
     * @param string|null $period Période ('day', 'week', 'month', 'year', null pour tout)
     * @param string|null $date Date spécifique (format Y-m-d)
     * @return array
     */
    public function getUserFinancialData($userId = null, $period = null, $date = null)
    {
        $queryRecettes = Recette::query();
        $queryDepenses = Depense::query();
        
        // Filtrer par utilisateur si spécifié
        if ($userId) {
            // Si les modèles ont une relation avec user_id, on filtre
            // Pour l'instant, on filtre par date de création proche de l'utilisateur
            // Vous pouvez adapter cette logique selon votre structure
        }
        
        // Filtrer par période
        if ($period && !$date) {
            $startDate = match($period) {
                'day' => Carbon::today(),
                'week' => Carbon::now()->startOfWeek(),
                'month' => Carbon::now()->startOfMonth(),
                'year' => Carbon::now()->startOfYear(),
                default => null
            };
            
            if ($startDate) {
                $queryRecettes->where('date', '>=', $startDate);
                $queryDepenses->where('date', '>=', $startDate);
            }
        }
        
        // Filtrer par date spécifique
        if ($date) {
            $queryRecettes->whereDate('date', $date);
            $queryDepenses->whereDate('date', $date);
        }
        
        // Calculer les totaux
        $totalRecettes = $queryRecettes->sum('montant');
        $totalDepenses = $queryDepenses->sum('montant');
        $solde = $totalRecettes - $totalDepenses;
        
        // Récupérer les données détaillées
        $recettes = $queryRecettes->orderBy('date', 'desc')->get();
        $depenses = $queryDepenses->orderBy('date', 'desc')->get();
        
        // Répartition par type/motif
        $recettesParType = $queryRecettes->select('type', \DB::raw('SUM(montant) as total'))
            ->groupBy('type')
            ->get()
            ->pluck('total', 'type');
            
        $depensesParMotif = $queryDepenses->select('motif', \DB::raw('SUM(montant) as total'))
            ->groupBy('motif')
            ->get()
            ->pluck('total', 'motif');
        
        return [
            'recettes' => $recettes,
            'depenses' => $depenses,
            'total_recettes' => $totalRecettes,
            'total_depenses' => $totalDepenses,
            'solde' => $solde,
            'recettes_par_type' => $recettesParType,
            'depenses_par_motif' => $depensesParMotif
        ];
    }
    
    /**
     * Récupère les données financières pour la période courante
     * 
     * @param int|null $userId
     * @return array
     */
    public function getCurrentPeriodData($userId = null)
    {
        return $this->getUserFinancialData($userId, 'month');
    }
    
    /**
     * Récupère les données pour une date spécifique
     * 
     * @param string $date Format Y-m-d
     * @param int|null $userId
     * @return array
     */
    public function getDailyData($date, $userId = null)
    {
        return $this->getUserFinancialData($userId, null, $date);
    }
    
    /**
     * Formatte un montant en FCFA
     * 
     * @param float $amount
     * @return string
     */
    public function formatAmount($amount)
    {
        return number_format($amount, 0, ',', ' ') . ' FCFA';
    }
}