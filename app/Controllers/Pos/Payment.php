<?php

namespace App\Controllers\Pos;

use App\Controllers\BaseController;
use App\Models\OrderModel;
use App\Models\OrderItemModel;
use App\Models\TableModel;

class Payment extends BaseController
{
    public function index($orderId)
    {
        $orderModel = new OrderModel();
        $itemModel = new OrderItemModel();

        // Cargar orden
        $order = $orderModel->select('orders.*, tables.name as table_name, users.username as waiter_name')
                            ->join('tables', 'tables.id = orders.table_id', 'left')
                            ->join('users', 'users.id = orders.user_id', 'left')
                            ->find($orderId);

        if (!$order) {
            return redirect()->to('/pos/tables');
        }

        $items = $itemModel->where('order_id', $orderId)->findAll();

        return view('Pos/payment', [
            'order' => $order,
            'items' => $items,
            // USAMOS EL TOTAL QUE GUARDÓ EL CHECKOUT, NO RECALCULAMOS
            'calculatedTotal' => $order['total'] 
        ]);
    }

    public function pay($orderId)
    {
        $request = $this->request->getJSON();
        $method  = $request->payment_method ?? 'cash'; 

        $orderModel = new OrderModel();
        $tableModel = new TableModel();
        $db = \Config\Database::connect();

        $order = $orderModel->find($orderId);

        if (!$order) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Orden inválida']);
        }

        // TRANSACCIÓN: Pagar Orden + Liberar Mesa
        $db->transStart();

        try {
            // 1. Actualizar Orden a PAGADA
            $orderModel->update($orderId, [
                'status'         => 'paid',
                'payment_method' => $method,
                'updated_at'     => date('Y-m-d H:i:s')
            ]);

            // 2. Liberar la Mesa (Si aplica)
            if ($order['table_id']) {
                $tableModel->update($order['table_id'], ['status' => 'available']);
            }

            // Aquí podrías insertar en la tabla 'cash_register' para el arqueo (Opcional por ahora)

            $db->transComplete();

            return $this->response->setJSON(['status' => 'success']);

        } catch (\Exception $e) {
            $db->transRollback();
            return $this->response->setJSON(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }
}