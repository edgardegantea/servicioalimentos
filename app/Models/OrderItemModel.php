<?php

namespace App\Models;

use CodeIgniter\Model;

class OrderItemModel extends Model
{
    protected $table            = 'order_items';
    protected $primaryKey       = 'id';
    protected $allowedFields    = [
        'order_id', 
        'product_id', 
        'product_name', // Guardamos el nombre por si se borra el producto original
        'quantity', 
        'price',        // Precio unitario AL MOMENTO de la venta (Snapshot)
        'options',      // JSON: "Sin cebolla", "Término medio"
        'status'        // pending, cooking, done (Para control individual en cocina)
    ];
    protected $useTimestamps    = true;
}