<?php
namespace App\Database\Seeds;
use CodeIgniter\Database\Seeder;

class MenuSeeder extends Seeder
{
    public function run()
    {
        // 1. Crear Categorías
        $this->db->table('categories')->insertBatch([
            ['name' => 'Hamburguesas', 'slug' => 'hamburguesas', 'active' => 1],
            ['name' => 'Bebidas', 'slug' => 'bebidas', 'active' => 1],
            ['name' => 'Postres', 'slug' => 'postres', 'active' => 1],
        ]);
        
        // Obtener IDs (Asumiendo que son 1, 2, 3 por el auto-increment)
        
        // 2. Crear Productos
        $data = [
            [
                'category_id' => 1, // Hamburguesas
                'name'        => 'Hamburguesa Doble',
                'slug'        => 'hamburguesa-doble',
                'description' => 'Doble carne con tocino.',
                'price'       => 150.00,
                'stock'       => 50,
                'track_stock' => 1,
                'is_visible'  => 1
            ],
            [
                'category_id' => 1, 
                'name'        => 'Hamburguesa de Pollo',
                'slug'        => 'hamburguesa-pollo',
                'description' => 'Crujiente y picante.',
                'price'       => 110.00,
                'stock'       => 20,
                'track_stock' => 1,
                'is_visible'  => 1
            ],
            [
                'category_id' => 2, // Bebidas
                'name'        => 'Coca Cola',
                'slug'        => 'coca-cola',
                'description' => 'Lata 355ml',
                'price'       => 25.00,
                'stock'       => 100,
                'track_stock' => 1,
                'is_visible'  => 1
            ],
             [
                'category_id' => 3, // Postres
                'name'        => 'Pastel de Chocolate',
                'slug'        => 'pastel-chocolate',
                'description' => 'Rebanada.',
                'price'       => 65.00,
                'stock'       => 10,
                'track_stock' => 1,
                'is_visible'  => 1
            ]
        ];

        $this->db->table('products')->insertBatch($data);
    }
}