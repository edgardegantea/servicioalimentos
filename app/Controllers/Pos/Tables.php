<?php

namespace App\Controllers\Pos;

use App\Controllers\BaseController;
use App\Models\TableModel;
use App\Models\OrderModel;

class Tables extends BaseController
{
    public function index()
    {
        $tableModel = new TableModel();
        $orderModel = new OrderModel();
        $tables = $tableModel->orderBy('name', 'ASC')->findAll();

        foreach ($tables as &$table) {
            $table['active_order'] = $orderModel->where('table_id', $table['id'])
                                                ->whereNotIn('status', ['paid', 'cancelled'])
                                                ->first();
            
            if(empty($table['active_order']) && $table['status'] !== 'available') {
                 $tableModel->update($table['id'], ['status' => 'available']);
                 $table['status'] = 'available';
            }
        }

        return view('Pos/tables_view', ['tables' => $tables]);
    }

    public function occupy($tableId)
    {
        $tableModel = new TableModel();
        $orderModel = new OrderModel();
        $db = \Config\Database::connect();

        $table = $tableModel->find($tableId);
        if(!$table || $table['status'] !== 'available') {
             return redirect()->back()->with('error', 'Esa mesa ya no está disponible.');
        }

        $db->transStart();

        try {
            $newOrderData = [
                'order_number' => $orderModel->generateOrderNumber(),
                'user_id'      => auth()->id(), // El mesero que abrió la mesa
                'table_id'     => $tableId,
                'type'         => 'dine_in', // Venta en sitio
                'status'       => 'pending'
            ];
            
            $orderModel->insert($newOrderData);
            $newOrderId = $orderModel->getInsertID();

            $tableModel->update($tableId, ['status' => 'occupied']);

            $db->transComplete();

            // 3. ¡MAGIA! Guardar el ID de la orden en sesión para que el POS lo reconozca
            session()->set('active_order_id', $newOrderId);
            
            // 4. Redirigir a la terminal de venta
            return redirect()->to('/pos');

        } catch (\Exception $e) {
            $db->transRollback();
            return redirect()->back()->with('error', 'Error al abrir la mesa.');
        }
    }
}