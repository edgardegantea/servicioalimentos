<?php

namespace App\Models;

use CodeIgniter\Model;

class TableModel extends Model
{
    protected $table            = 'tables';
    protected $primaryKey       = 'id';
    protected $useSoftDeletes   = true;
    protected $allowedFields    = ['name', 'capacity', 'status', 'qr_code']; // status: available, occupied, reserved
    protected $useTimestamps    = true;

    protected $validationRules = [
        'name'     => 'required|min_length[1]|max_length[50]',
        'capacity' => 'required|integer|greater_than[0]',
        'status'   => 'in_list[available,occupied,reserved,cleaning]',
    ];

    /**
     * Libera una mesa (útil al cerrar una cuenta)
     */
    public function markAsAvailable($id)
    {
        return $this->update($id, ['status' => 'available']);
    }

    /**
     * Ocupa una mesa (útil al abrir nueva orden)
     */
    public function markAsOccupied($id)
    {
        return $this->update($id, ['status' => 'occupied']);
    }
}