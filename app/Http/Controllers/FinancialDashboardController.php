<?php

namespace App\Http\Controllers;

use App\Services\FinanceService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FinancialDashboardController extends Controller
{
    protected $financeService;
    
    public function __construct(FinanceService $financeService)
    {
        $this->financeService = $financeService;
    }
    
    /**
     * Affiche le tableau de bord financier pour un utilisateur
     * 
     * @param Request $request
     * @param int|null $userId
     * @return View
     */
    public function userDashboard(Request $request, $userId = null): View
    {
        $period = $request->get('period', 'month');
        $date = $request->get('date');
        
        if ($date) {
            $financialData = $this->financeService->getDailyData($date, $userId);
        } else {
            $financialData = $this->financeService->getUserFinancialData($userId, $period);
        }
        
        return view('financial.dashboard', compact('financialData', 'userId', 'period', 'date'));
    }
    
    /**
     * Affiche un résumé financier simple
     * 
     * @param Request $request
     * @return View
     */
    public function summary(Request $request): View
    {
        $userId = $request->get('user_id');
        $period = $request->get('period', 'month');
        
        $financialData = $this->financeService->getUserFinancialData($userId, $period);
        
        return view('financial.summary', compact('financialData', 'userId', 'period'));
    }
    
    /**
     * API endpoint pour récupérer les données financières
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function apiData(Request $request)
    {
        $userId = $request->get('user_id');
        $period = $request->get('period', 'month');
        $date = $request->get('date');
        
        if ($date) {
            $data = $this->financeService->getDailyData($date, $userId);
        } else {
            $data = $this->financeService->getUserFinancialData($userId, $period);
        }
        
        return response()->json([
            'success' => true,
            'data' => $data,
            'formatted' => [
                'total_recettes' => $this->financeService->formatAmount($data['total_recettes']),
                'total_depenses' => $this->financeService->formatAmount($data['total_depenses']),
                'solde' => $this->financeService->formatAmount($data['solde'])
            ]
        ]);
    }
}