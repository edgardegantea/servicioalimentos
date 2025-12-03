<?php

namespace App\Models;

use CodeIgniter\Model;

class CategoryModel extends Model
{
    protected $table            = 'categories';
    protected $primaryKey       = 'id';
    protected $useSoftDeletes   = true;
    protected $allowedFields    = ['name', 'slug', 'image', 'active'];
    protected $useTimestamps    = true;

    // Validaciones automáticas
    protected $validationRules = [
        'name' => 'required|min_length[3]|max_length[100]|is_unique[categories.name,id,{id}]',
    ];
    protected $validationMessages = [
        'name' => [
            'is_unique' => 'Ya existe una categoría con este nombre.'
        ]
    ];
}