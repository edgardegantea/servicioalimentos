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

        // 1. Detectar si hay una orden activa (Mesa ocupada) o es nueva
        $activeOrderId = session()->get('active_order_id');
        
        $db->transStart();

        try {
            $orderId = null;
            $currentOrderNumber = null;

            if ($activeOrderId) {
                // --- ESCENARIO A: AGREGAR A ORDEN EXISTENTE ---
                $orderId = $activeOrderId;
                $existingOrder = $orderModel->find($orderId);
                $currentOrderNumber = $existingOrder['order_number'];
                
                // No tocamos el encabezado todavía, solo agregaremos items
            } else {
                // --- ESCENARIO B: ORDEN NUEVA (Venta Rápida) ---
                $orderData = [
                    'order_number'   => $orderModel->generateOrderNumber(),
                    'user_id'        => auth()->id(),
                    'table_id'       => null, // Es delivery/barra
                    'status'         => 'pending',
                    'subtotal'       => 0, // Se calculará abajo
                    'total'          => 0,
                    'payment_method' => 'cash' // Por defecto
                ];
                $orderModel->insert($orderData);
                $orderId = $orderModel->getInsertID();
                $currentOrderNumber = $orderData['order_number'];
            }

            // 2. Insertar los Items y Descontar Stock
            foreach ($json->items as $item) {
                // Verificar Stock
                if (!$productModel->hasStock($item->id, $item->qty)) {
                    throw new \Exception("Stock insuficiente: " . $item->name);
                }

                $orderItemModel->insert([
                    'order_id'     => $orderId,
                    'product_id'   => $item->id,
                    'product_name' => $item->name,
                    'quantity'     => $item->qty,
                    'price'        => $item->price,
                    'status'       => 'pending'
                ]);

                // Descontar inventario
                $productModel->updateStock($item->id, $item->qty);
            }

            // 3. CRÍTICO: Recalcular el TOTAL REAL de la orden completa
            // Sumamos TODO lo que hay en la tabla order_items para este ID
            $calculated = $orderItemModel->selectSum('price * quantity', 'grand_total')
                                         ->where('order_id', $orderId)
                                         ->first();
            
            $realTotal = $calculated['grand_total'] ?? 0;

            // 4. Actualizar el encabezado de la orden con el nuevo total
            $orderModel->update($orderId, [
                'subtotal' => $realTotal,
                'total'    => $realTotal, // Aquí podrías sumar impuestos si usas
                'updated_at' => date('Y-m-d H:i:s')
            ]);

            $db->transComplete();

            if ($db->transStatus() === false) {
                return $this->response->setJSON(['status' => 'error', 'message' => 'Error en BD']);
            }

            // Si es venta rápida (sin mesa), limpiamos sesión por si acaso
            if (!$activeOrderId) {
                session()->remove('active_order_id');
            }

            return $this->response->setJSON([
                'status'  => 'success', 
                'folio'   => $currentOrderNumber,
                'total'   => $realTotal // Devolvemos el total actualizado
            ]);

        } catch (\Exception $e) {
            $db->transRollback();
            return $this->response->setJSON(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }
}