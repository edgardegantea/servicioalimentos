<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use CodeIgniter\Shield\Entities\User;
use CodeIgniter\Shield\Models\UserModel;

class UserSeeder extends Seeder
{
    public function run()
    {
        $userModel = model(UserModel::class);

        // Borramos usuarios previos para evitar duplicados al probar (opcional)
        // $this->db->table('users')->truncate(); 

        // 1. Crear el SUPER ADMIN (Tu usuario principal)
        $this->createUser($userModel, [
            'username' => 'admin',
            'email'    => 'admin@restaurante.com',
            'password' => 'admin123', // ¡Cambiar en producción!
        ], 'superadmin');

        // 2. Crear un CAJERO de prueba
        $this->createUser($userModel, [
            'username' => 'cajero1',
            'email'    => 'caja@restaurante.com',
            'password' => 'caja123',
        ], 'cashier');

        // 3. Crear un MESERO de prueba
        $this->createUser($userModel, [
            'username' => 'mesero1',
            'email'    => 'mesero@restaurante.com',
            'password' => 'mesero123',
        ], 'waiter');

        // 4. Crear un usuario de COCINA
        $this->createUser($userModel, [
            'username' => 'chef1',
            'email'    => 'cocina@restaurante.com',
            'password' => 'cocina123',
        ], 'kitchen');
        
        echo "✅ Usuarios de prueba creados correctamente.\n";
    }

    /**
     * Función auxiliar para crear y asignar rol en un solo paso
     */
    private function createUser($model, $data, $role)
    {
        // Verificar si ya existe para no fallar si corres el seeder 2 veces
        $existing = $model->findByCredentials(['email' => $data['email']]);
        
        if ($existing) {
            echo "⚠️ El usuario {$data['email']} ya existe. Saltando...\n";
            return;
        }

        // Crear Entidad User
        $user = new User([
            'username' => $data['username'],
            'email'    => $data['email'],
            'password' => $data['password'],
            'active'   => 1, // Importante: Activarlo directamente
        ]);

        // Guardar en BD
        $model->save($user);

        // Recuperar el ID generado para asignar el grupo
        $user = $model->findById($model->getInsertID());

        // Asignar el Rol (Grupo)
        $user->addGroup($role);
        
        echo "👤 Usuario creado: {$data['username']} (Rol: $role)\n";
    }
}