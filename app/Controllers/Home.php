<?php

namespace App\Controllers;

class Home extends BaseController
{
    public function index()
    {
        // 1. Si NO está logueado, mostrar la Landing Page pública
        if (! auth()->loggedIn()) {
            return view('welcome_message'); // O tu vista 'landing_page'
        }

        // 2. Si ESTÁ logueado, verificar su ROL y redirigir
        $user = auth()->user();

        // A. Gerentes y Superadmin -> Dashboard
        if ($user->inGroup('superadmin', 'admin')) {
            return redirect()->to('/admin/dashboard');
        }

        // B. Cajeros y Meseros -> Punto de Venta (POS)
        if ($user->inGroup('cashier', 'waiter')) {
            return redirect()->to('/pos');
        }

        // C. Cocina -> Monitor KDS
        if ($user->inGroup('kitchen')) {
            return redirect()->to('/kitchen/monitor');
        }

        // D. Repartidores -> Panel de Envíos (Si existiera)
        if ($user->inGroup('delivery')) {
            return redirect()->to('/delivery/dashboard'); // Ajustar según tus rutas
        }

        // E. Clientes (Por defecto) -> Menú Digital
        return redirect()->to('/menu');
    }
}