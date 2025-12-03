<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\ProductModel;
use App\Models\CategoryModel;

class Products extends BaseController
{
    protected $productModel;
    protected $categoryModel;

    public function __construct()
    {
        $this->productModel  = new ProductModel();
        $this->categoryModel = new CategoryModel();
    }

    /**
     * Listado de productos (GET admin/products)
     */
    public function index()
    {
        // Traemos productos con la info de su categoría (Join o consulta separada)
        // Para simplificar y aprovechar el modelo, haremos un select manual o relación
        $products = $this->productModel
             ->select('products.*, categories.name as category_name')
             ->join('categories', 'categories.id = products.category_id')
             ->findAll();

        $data = [
            'title'    => 'Gestión de Menú',
            'products' => $products
        ];

        return view('Admin/Products/index', $data);
    }

    /**
     * Formulario de creación (GET admin/products/new)
     */
    public function new()
    {
        $data = [
            'title'      => 'Nuevo Producto',
            'categories' => $this->categoryModel->where('active', 1)->findAll(),
            'product'    => null // Null indica que es creación
        ];

        return view('Admin/Products/form', $data);
    }

    /**
     * Procesar creación (POST admin/products)
     */
    public function create()
    {
        $input = $this->request->getPost();
        
        // 1. Manejo de Imagen
        $img = $this->request->getFile('image');
        $imgName = null;

        if ($img && $img->isValid() && ! $img->hasMoved()) {
            $imgName = $img->getRandomName(); // Generar nombre único
            $img->move(FCPATH . 'uploads/products', $imgName); // Mover a public/uploads/products
        }

        // 2. Preparar datos
        $data = [
            'category_id' => $input['category_id'],
            'name'        => $input['name'],
            'slug'        => url_title($input['name'], '-', true), // Generar slug automático
            'description' => $input['description'],
            'price'       => $input['price'],
            'cost'        => $input['cost'],
            'stock'       => $input['stock'],
            'track_stock' => isset($input['track_stock']) ? 1 : 0,
            'is_visible'  => isset($input['is_visible']) ? 1 : 0,
            'image'       => $imgName,
        ];

        // 3. Guardar
        if (! $this->productModel->save($data)) {
            // Si falla validación, volver con errores
            return redirect()->back()->withInput()->with('errors', $this->productModel->errors());
        }

        return redirect()->to('/admin/products')->with('message', 'Producto creado correctamente.');
    }

    /**
     * Formulario de edición (GET admin/products/(:num)/edit)
     */
    public function edit($id = null)
    {
        $product = $this->productModel->find($id);
        if (!$product) {
            return redirect()->to('/admin/products')->with('error', 'Producto no encontrado');
        }

        $data = [
            'title'      => 'Editar Producto',
            'categories' => $this->categoryModel->where('active', 1)->findAll(),
            'product'    => $product
        ];

        return view('Admin/Products/form', $data);
    }

    /**
     * Procesar actualización (PUT/PATCH admin/products/(:num))
     * Nota: En HTML forms usaremos POST simulando PUT
     */
    public function update($id = null)
    {
        $input = $this->request->getPost();
        $product = $this->productModel->find($id);

        // 1. Manejo de Imagen (Si suben una nueva, reemplaza la anterior)
        $img = $this->request->getFile('image');
        $imgName = $product['image']; // Mantener la vieja por defecto

        if ($img && $img->isValid() && ! $img->hasMoved()) {
            // Borrar imagen vieja si existe (opcional, buena práctica para no llenar el server)
            if (!empty($product['image']) && file_exists(FCPATH . 'uploads/products/' . $product['image'])) {
                unlink(FCPATH . 'uploads/products/' . $product['image']);
            }
            
            $imgName = $img->getRandomName();
            $img->move(FCPATH . 'uploads/products', $imgName);
        }

        // 2. Preparar datos
        $data = [
            'id'          => $id, // Importante para que el save() sepa que es UPDATE
            'category_id' => $input['category_id'],
            'name'        => $input['name'],
            'slug'        => url_title($input['name'], '-', true),
            'description' => $input['description'],
            'price'       => $input['price'],
            'cost'        => $input['cost'],
            'stock'       => $input['stock'],
            'track_stock' => isset($input['track_stock']) ? 1 : 0,
            'is_visible'  => isset($input['is_visible']) ? 1 : 0,
            'image'       => $imgName,
        ];

        if (! $this->productModel->save($data)) {
            return redirect()->back()->withInput()->with('errors', $this->productModel->errors());
        }

        return redirect()->to('/admin/products')->with('message', 'Producto actualizado.');
    }

    /**
     * Eliminar (DELETE admin/products/(:num))
     */
    public function delete($id = null)
    {
        $this->productModel->delete($id);
        return redirect()->to('/admin/products')->with('message', 'Producto eliminado.');
    }
}