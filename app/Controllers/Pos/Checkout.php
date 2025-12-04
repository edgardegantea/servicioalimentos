<?php

namespace App\Controllers\Pos;

use App\Controllers\BaseController;
use App\Models\OrderModel;
use App\Models\OrderItemModel;
use App\Models\ProductModel;

class Checkout extends BaseController
{
    public function process()
    {
        $json = $this->request->getJSON();

        if (empty($json->items)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Carrito vacío.']);
        }

        $orderModel     = new OrderModel();
        $orderItemModel = new OrderItemModel();
        $productModel   = new ProductModel();
        $db             = \Config\Database::connect();

        // 1. Detectar si hay una orden activa en sesión (Mesa ocupada)
        $activeOrderId = session()->get('active_order_id');

        // Iniciar Transacción
        $db->transStart();

        try {
            $orderId = null;
            $currentOrderNumber = null;

            if ($activeOrderId) {
                // CASO A: Mesa existente (Agregamos a lo que ya pidieron)
                $orderId = $activeOrderId;
                $existingOrder = $orderModel->find($orderId);

                if (!$existingOrder) {
                    // Si por alguna razón la orden en sesión ya no existe en BD
                    throw new \Exception("La orden activa no fue encontrada. Intenta recargar la página.");
                }

                $currentOrderNumber = $existingOrder['order_number'];
            } else {
                // CASO B: Orden Nueva (Venta Rápida / Delivery)
                $orderData = [
                    'order_number'   => $orderModel->generateOrderNumber(),
                    'user_id'        => auth()->id(),
                    'table_id'       => null,
                    'status'         => 'pending',
                    'subtotal'       => 0,
                    'total'          => 0,
                    'payment_method' => 'cash'
                ];
                $orderModel->insert($orderData);
                $orderId = $orderModel->getInsertID();
                $currentOrderNumber = $orderData['order_number'];
            }

            // 2. Insertar Items y Descontar Stock
            foreach ($json->items as $item) {
                // Verificación estricta de stock
                if (!$productModel->hasStock($item->id, $item->qty)) {
                    throw new \Exception("Stock insuficiente para: " . $item->name);
                }

                $orderItemModel->insert([
                    'order_id'     => $orderId,
                    'product_id'   => $item->id,
                    'product_name' => $item->name,
                    'quantity'     => $item->qty,
                    'price'        => $item->price,
                    'status'       => 'pending'
                ]);

                // Actualizar inventario
                $productModel->updateStock($item->id, $item->qty);
            }

            // 3. CÁLCULO DEL TOTAL (CORREGIDO)
            // En lugar de usar selectSum complejo, traemos los items y sumamos en PHP.
            // Esto evita el error de SQL y garantiza precisión.
            $allItems = $orderItemModel->where('order_id', $orderId)->findAll();

            $realTotal = 0;
            foreach ($allItems as $row) {
                $realTotal += ($row['price'] * $row['quantity']);
            }

            // 4. Actualizar el encabezado de la orden
            $orderModel->update($orderId, [
                'subtotal'   => $realTotal,
                'total'      => $realTotal,
                'updated_at' => date('Y-m-d H:i:s')
            ]);

            // Finalizar transacción
            $db->transComplete();

            if ($db->transStatus() === false) {
                // Si hubo un error interno en la base de datos
                return $this->response->setJSON(['status' => 'error', 'message' => 'Error al guardar en la base de datos.']);
            }

            // Limpiar sesión solo si era venta rápida
            if (!$activeOrderId) {
                session()->remove('active_order_id');
            }

            return $this->response->setJSON([
                'status'  => 'success',
                'folio'   => $currentOrderNumber,
                'total'   => $realTotal
            ]);

        } catch (\Exception $e) {
            $db->transRollback();
            return $this->response->setJSON(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }
}