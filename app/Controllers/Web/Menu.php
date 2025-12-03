<?php

namespace App\Controllers\Web;

use App\Controllers\BaseController;
use App\Models\ProductModel;

class Menu extends BaseController
{
    public function index()
    {
        $productModel = new ProductModel();
        
        // Obtenemos productos agrupados (lógica simple para vista)
        $products = $productModel->select('products.*, categories.name as category_name')
                                 ->join('categories', 'categories.id = products.category_id')
                                 ->where('products.is_visible', 1)
                                 ->where('categories.active', 1)
                                 ->orderBy('category_name', 'ASC')
                                 ->findAll();

        // Agrupar por categoría
        $menu = [];
        foreach($products as $prod) {
            $menu[$prod['category_name']][] = $prod;
        }

        return view('Web/menu', ['menu' => $menu]);
    }
}