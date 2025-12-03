<?php

namespace App\Controllers\Pos;

use App\Controllers\BaseController;
use App\Models\CashRegisterModel;

class CashRegister extends BaseController
{
    // --- APERTURA DE CAJA ---
    public function open()
    {
        // Si ya tiene caja abierta, redirigir al POS
        $model = new CashRegisterModel();
        if ($model->getOpenRegister(auth()->id())) {
            return redirect()->to('/pos');
        }

        return view('Pos/Cash/open');
    }

    public function processOpen()
    {
        $amount = $this->request->getPost('amount');
        
        $model = new CashRegisterModel();
        $model->insert([
            'user_id'        => auth()->id(),
            'opening_amount' => $amount,
            'status'         => 'open',
            'opened_at'      => date('Y-m-d H:i:s')
        ]);

        return redirect()->to('/pos')->with('message', 'Caja abierta correctamente.');
    }

    // --- CIERRE DE CAJA (ARQUEO) ---
    public function close()
    {
        $model = new CashRegisterModel();
        $register = $model->getOpenRegister(auth()->id());

        if (!$register) {
            return redirect()->to('/pos/register/open')->with('error', 'No tienes una caja abierta.');
        }

        // Calcular cuánto dinero debería haber
        $cashSales = $model->getCashSales(auth()->id(), $register['opened_at']);
        $expectedTotal = $register['opening_amount'] + $cashSales;

        return view('Pos/Cash/close', [
            'register'      => $register,
            'cashSales'     => $cashSales,
            'expectedTotal' => $expectedTotal
        ]);
    }

    public function processClose()
    {
        $model = new CashRegisterModel();
        $register = $model->getOpenRegister(auth()->id());
        
        // Datos del formulario
        $realAmount = $this->request->getPost('real_amount');
        $notes      = $this->request->getPost('notes');

        // Recalcular sistema por seguridad
        $cashSales = $model->getCashSales(auth()->id(), $register['opened_at']);
        $systemAmount = $register['opening_amount'] + $cashSales;
        
        // Diferencia (Positiva = Sobra dinero, Negativa = Falta dinero)
        $difference = $realAmount - $systemAmount;

        // Guardar cierre
        $model->update($register['id'], [
            'closing_amount' => $realAmount,
            'system_amount'  => $systemAmount,
            'difference'     => $difference,
            'status'         => 'closed',
            'closed_at'      => date('Y-m-d H:i:s'),
            'notes'          => $notes
        ]);

        // Cerrar sesión del usuario al terminar turno
        return redirect()->to('/logout');
    }
}