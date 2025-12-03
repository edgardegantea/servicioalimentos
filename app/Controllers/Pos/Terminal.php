<?php

namespace App\Controllers\Pos;

use App\Controllers\BaseController;
use App\Models\ProductModel;
use App\Models\CategoryModel;
use App\Models\OrderModel;
use App\Models\CashRegisterModel;

class Terminal extends BaseController
{
    public function index()
    {
        // -----------------------------------------------------------
        // 1. BLOQUEO DE SEGURIDAD: VERIFICAR CAJA ABIERTA
        // -----------------------------------------------------------
        $cashModel = new CashRegisterModel();
        
        // Verificamos si el usuario logueado (auth()->id()) tiene un turno 'open'
        if (!$cashModel->getOpenRegister(auth()->id())) {
            // Si no tiene caja abierta, lo mandamos a la pantalla de apertura
            return redirect()->to('/pos/register/open')->with('error', 'Debes abrir caja antes de comenzar a vender.');
        }

        // -----------------------------------------------------------
        // 2. DETECCIÓN DE CONTEXTO (MESA VS VENTA RÁPIDA)
        // -----------------------------------------------------------
        // Cuando hicimos clic en una mesa en el paso anterior, guardamos el ID en sesión.
        $activeOrderId = session()->get('active_order_id');
        $activeOrderInfo = null;

        if ($activeOrderId) {
            $orderModel = new OrderModel();
            
            // Buscamos la info básica de la orden y el nombre de la mesa
            // Usamos un JOIN para obtener el nombre legible de la mesa (Ej. "Mesa 4")
            $activeOrderInfo = $orderModel->select('orders.id, orders.order_number, tables.name as table_name')
                                          ->join('tables', 'tables.id = orders.table_id', 'left')
                                          ->find($activeOrderId);

            // Validación de integridad: Si la orden se borró de la BD pero sigue en sesión
            if (!$activeOrderInfo) {
                session()->remove('active_order_id'); // Limpiamos la sesión corrupta
            }
        }

        // -----------------------------------------------------------
        // 3. CARGA DE CATÁLOGO (MENÚ)
        // -----------------------------------------------------------
        $productModel = new ProductModel();
        $categoryModel = new CategoryModel();

        $data = [
            'title'       => 'Punto de Venta',
            // Solo categorías activas
            'categories'  => $categoryModel->where('active', 1)->findAll(),
            // Solo productos visibles y no eliminados
            'products'    => $productModel->where('is_visible', 1)->findAll(),
            // Pasamos la info de la orden a la vista (puede ser null si es venta rápida)
            'activeOrder' => $activeOrderInfo 
        ];

        return view('Pos/terminal', $data);
    }
}