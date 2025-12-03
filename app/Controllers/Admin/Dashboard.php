<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\ProductModel;
use CodeIgniter\Shield\Models\UserModel;
use App\Models\OrderModel;
use App\Models\TableModel;

class Dashboard extends BaseController
{
    public function index()
    {
        $userModel    = new UserModel();
        $productModel = new ProductModel();
        $orderModel   = new OrderModel();
        $tableModel   = new TableModel();

        $today = date('Y-m-d');

        // --- CÁLCULOS ---
        $salesQuery = $orderModel->selectSum('total')
                                 ->where('status', 'paid')
                                 ->where("DATE(created_at)", $today)
                                 ->first();
        $salesToday = $salesQuery['total'] ?? 0.00;

        $ordersPending = $orderModel->whereIn('status', ['pending', 'cooking', 'ready'])
                                    ->countAllResults();

        $lowStock = $productModel->where('track_stock', 1)
                                 ->where('stock <', 10)
                                 ->countAllResults();
                                 
        // Calculamos el total de productos para mostrar "De X productos"
        $totalProducts = $productModel->countAllResults(); 

        $totalStaff = $userModel->countAllResults();

        $occupiedTables = $tableModel->where('status', 'occupied')->countAllResults();
        $totalTables    = $tableModel->countAllResults();

        // --- ENVIAR DATOS A LA VISTA ---
        $data = [
            'title'          => 'Panel de Control',
            'salesToday'     => $salesToday,
            'ordersPending'  => $ordersPending,
            'lowStock'       => $lowStock,
            'totalProducts'  => $totalProducts, // <--- ¡ESTA FALTABA!
            'totalStaff'     => $totalStaff,
            'occupancy'      => [
                'occupied' => $occupiedTables,
                'total'    => $totalTables
            ]
        ];

        return view('Admin/dashboard', $data);
    }
}