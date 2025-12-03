<?php

namespace App\Controllers\Kitchen;

use App\Controllers\BaseController;
use App\Models\OrderModel;
use App\Models\OrderItemModel;

class Monitor extends BaseController
{
    public function index()
    {
        $orderModel = new OrderModel();
        $itemModel  = new OrderItemModel();

        // 1. Obtener órdenes activas (Pendientes o Cocinando)
        // Usamos 'users' para saber quién la pidió (mesero) y 'tables' para la mesa
        $orders = $orderModel->select('orders.*, tables.name as table_name, users.username as waiter_name')
                             ->join('tables', 'tables.id = orders.table_id', 'left')
                             ->join('users', 'users.id = orders.user_id', 'left')
                             ->whereIn('orders.status', ['pending', 'cooking'])
                             ->orderBy('orders.status', 'DESC') // 'pending' primero, luego 'cooking' (o al revés según prefieras)
                             ->orderBy('orders.created_at', 'ASC') // Las más viejas primero (FIFO)
                             ->findAll();

        // 2. Inyectar los items a cada orden (Hydration manual)
        // Esto evita hacer queries complejos en la vista
        foreach ($orders as &$order) {
            $order['items'] = $itemModel->where('order_id', $order['id'])->findAll();
        }

        return view('Kitchen/monitor', ['orders' => $orders]);
    }

    /**
     * Método AJAX para cambiar estado (Pending -> Cooking -> Ready)
     */
    public function updateStatus($id)
    {
        $request = $this->request->getJSON();
        $newStatus = $request->status ?? null;

        if (!in_array($newStatus, ['cooking', 'ready', 'cancelled'])) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Estado inválido']);
        }

        $orderModel = new OrderModel();
        
        // Actualizamos estado
        $orderModel->update($id, ['status' => $newStatus]);

        return $this->response->setJSON(['status' => 'success']);
    }
}