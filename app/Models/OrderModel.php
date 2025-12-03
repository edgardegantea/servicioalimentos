<?php

namespace App\Models;

use CodeIgniter\Model;

class OrderModel extends Model
{
    protected $table            = 'orders';
    protected $primaryKey       = 'id';
    protected $useSoftDeletes   = true;
    
    // Campos permitidos para asignación masiva
    protected $allowedFields    = [
        'order_number',     // Folio único (ej. A-0001)
        'user_id',          // Quién creó la orden (Mesero/Cliente)
        'table_id',         // NULL si es Delivery/Para llevar
        'customer_name',    // Nombre del cliente (opcional)
        'type',             // dine_in, delivery, pickup
        'status',           // pending, cooking, ready, delivered, paid, cancelled
        'payment_method',   // cash, card, transfer
        'subtotal', 
        'tax', 
        'total',
        'notes'
    ];
    
    protected $useTimestamps    = true;

    // Estados válidos para flujo de trabajo
    const STATUS_PENDING   = 'pending';   // Recién creada
    const STATUS_COOKING   = 'cooking';   // En cocina
    const STATUS_READY     = 'ready';     // Lista para entregar
    const STATUS_PAID      = 'paid';      // Cobrada y cerrada

    /**
     * Genera un folio único legible para humanos
     */
    public function generateOrderNumber()
    {
        $prefix = date('Ymd'); // 20231129
        $lastOrder = $this->withDeleted()->orderBy('id', 'DESC')->first();
        
        $nextId = ($lastOrder) ? ($lastOrder['id'] + 1) : 1;
        return 'ORD-' . $prefix . '-' . str_pad($nextId, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Calcula los totales sumando los items (Se usará después de agregar productos)
     */
    public function calculateTotals($orderId)
    {
        $itemModel = model('OrderItemModel');
        $items = $itemModel->where('order_id', $orderId)->findAll();
        
        $total = 0;
        foreach ($items as $item) {
            $total += $item['price'] * $item['quantity'];
        }

        // Ejemplo simple sin impuestos complejos
        $this->update($orderId, [
            'subtotal' => $total,
            'total'    => $total // Aquí podrías sumar IVA o propina si se requiere
        ]);
        
        return $total;
    }
}