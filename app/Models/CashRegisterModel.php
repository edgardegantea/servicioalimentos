<?php

namespace App\Models;

use CodeIgniter\Model;

class CashRegisterModel extends Model
{
    protected $table            = 'cash_registers';
    protected $primaryKey       = 'id';
    protected $allowedFields    = [
        'user_id', 'opening_amount', 'closing_amount', 
        'system_amount', 'difference', 'status', 
        'opened_at', 'closed_at', 'notes'
    ];
    
    // Validaciones
    protected $validationRules = [
        'opening_amount' => 'required|numeric',
    ];

    /**
     * Busca si el usuario tiene una caja abierta actualmente
     */
    public function getOpenRegister($userId)
    {
        return $this->where('user_id', $userId)
                    ->where('status', 'open')
                    ->first();
    }

    /**
     * Calcula el total de ventas en EFECTIVO desde que se abrió esta caja
     */
    public function getCashSales($userId, $openedAt)
    {
        $orderModel = model('OrderModel');
       
        $result = $orderModel->selectSum('total')
                             ->where('user_id', $userId)
                             ->where('status', 'paid') 
                             ->where('payment_method', 'cash')
                             ->where('updated_at >=', $openedAt)
                             ->first();

        return $result['total'] ?? 0.00;
    }
}