<?php

namespace App\Controllers\Pos;

use App\Controllers\BaseController;
use App\Models\OrderModel;
use App\Models\OrderItemModel;

class DeliveryMonitor extends BaseController
{
    public function index()
    {
        $orderModel = new OrderModel();
        $itemModel = new OrderItemModel();

        // 1. Buscamos SOLO las órdenes que están 'ready' (Listas para recoger)
        $orders = $orderModel->select('orders.*, tables.name as table_name, users.username as waiter_name')
                             ->join('tables', 'tables.id = orders.table_id', 'left')
                             ->join('users', 'users.id = orders.user_id', 'left')
                             ->where('orders.status', 'ready') 
                             ->orderBy('orders.updated_at', 'ASC') // Las que llevan más tiempo esperando primero
                             ->findAll();

        // 2. Inyectamos los items para que el mesero verifique qué lleva
        foreach ($orders as &$order) {
            $order['items'] = $itemModel->where('order_id', $order['id'])->findAll();
        }

        return view('Pos/delivery_monitor', ['orders' => $orders]);
    }

    /**
     * Marcar como Entregado en Mesa (Status: delivered)
     */
    public function markDelivered($id)
    {
        $orderModel = new OrderModel();
        
        // Cambiamos a 'delivered' (Ya en mesa, comiendo)
        // El siguiente paso sería 'paid' cuando paguen.
        $orderModel->update($id, ['status' => 'delivered']);

        return $this->response->setJSON(['status' => 'success']);
    }
}