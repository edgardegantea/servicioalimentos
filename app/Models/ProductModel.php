<?php

namespace App\Models;

use CodeIgniter\Model;

class ProductModel extends Model
{
    protected $table            = 'products';
    protected $primaryKey       = 'id';
    protected $useSoftDeletes   = true;
    
    // Campos que permitimos modificar
    protected $allowedFields    = [
        'category_id', 
        'name', 
        'slug', 
        'description', 
        'price', 
        'cost', 
        'stock', 
        'track_stock', // 1 = Descuenta inventario, 0 = Servicio/Ilimitado
        'image', 
        'is_visible'
    ];

    // Gestión automática de fechas
    protected $useTimestamps    = true;
    protected $createdField     = 'created_at';
    protected $updatedField     = 'updated_at';
    protected $deletedField     = 'deleted_at';

    // Reglas de Validación (Protección de datos)
    protected $validationRules = [
        'category_id' => 'required|integer',
        'name'        => 'required|min_length[3]|max_length[150]',
        'price'       => 'required|decimal|greater_than[0]',
        'cost'        => 'permit_empty|decimal', // Costo es opcional pero recomendado
        'stock'       => 'integer',
    ];

    protected $validationMessages = [
        'name' => [
            'required' => 'El nombre del platillo es obligatorio.',
        ],
        'price' => [
            'greater_than' => 'El precio debe ser mayor a 0.'
        ]
    ];

    // -------------------------------------------------------------------------
    // MÉTODOS DE NEGOCIO (Lógica Restaurantera)
    // -------------------------------------------------------------------------

    /**
     * Verifica si hay suficiente stock para una cantidad solicitada.
     * Si track_stock es 0 (ej. servicio de descorche), siempre retorna true.
     * * @param int $productId ID del producto
     * @param int $quantity Cantidad requerida
     * @return bool
     */
    public function hasStock(int $productId, int $quantity = 1): bool
    {
        $product = $this->select('stock, track_stock')->find($productId);

        if (!$product) {
            return false;
        }

        // Si no rastreamos stock, siempre está disponible
        if ($product['track_stock'] == 0) {
            return true;
        }

        return $product['stock'] >= $quantity;
    }

    /**
     * Descuenta (o devuelve) stock de forma segura.
     * Útil al cerrar una venta o cancelar una orden.
     * * @param int $productId
     * @param int $quantity Cantidad a restar (positivo) o sumar (negativo)
     */
    public function updateStock(int $productId, int $quantity)
    {
        $product = $this->select('stock, track_stock')->find($productId);

        // Solo actualizamos si el producto rastrea stock
        if ($product && $product['track_stock'] == 1) {
            $newStock = $product['stock'] - $quantity;
            
            // Evitamos stock negativo (opcional, dependiendo de tu regla de negocio)
            if ($newStock < 0) $newStock = 0;

            $this->update($productId, ['stock' => $newStock]);
        }
    }

    /**
     * Obtiene productos con su categoría (Join) para listados
     */
    public function getWithCategory()
    {
        return $this->select('products.*, categories.name as category_name')
                    ->join('categories', 'categories.id = products.category_id')
                    ->where('products.deleted_at', null)
                    ->findAll();
    }
}