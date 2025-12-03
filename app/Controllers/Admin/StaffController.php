<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

use CodeIgniter\Shield\Entities\User;
use CodeIgniter\Shield\Models\UserModel;

class StaffController extends BaseController
{
    public function create()
    {
        $rules = [
            'username' => 'required|is_unique[users.username]',
            'email'    => 'required|valid_email|is_unique[auth_identities.secret]',
            'password' => 'required|min_length[8]',
            'role'     => 'required|in_list[admin,cashier,waiter,kitchen,delivery]'
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = $this->request->getPost();

        $users = model(UserModel::class);

        $user = new User([
            'username' => $data['username'],
            'email'    => $data['email'],
            'password' => $data['password'],
            'active'   => 1,
        ]);

        $users->save($user);

        $user = $users->findById($users->getInsertID()); 
        $user->addGroup($data['role']);

        return redirect()->to('/admin/staff')->with('message', 'Empleado registrado correctamente.');
    }
}