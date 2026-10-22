<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail; // Descomenta se fores enviar email real

class ContactoController extends Controller
{
    /**
     * Exibe a página de contactos.
     */
    public function index()
    {
        return view('contacto');
    }

    /**
     * Processa o envio do formulário de contacto.
     */
    public function enviar(Request $request)
    {
        // 1. Validar os dados do formulário
        $dadosValidados = $request->validate([
            'nome'      => 'required|string|max:255',
            'email'     => 'required|email|max:255',
            'endereco'  => 'required|string|max:255',
            'assunto'   => 'required|string|max:255',
            'mensagem'  => 'required|string|min:5',
        ]);

        // 2. Lógica de envio (Exemplo: Enviar email ou guardar na base de dados)
        // Se quiseres enviar um email para o suporte, podes usar algo do género:
        /*
        Mail::raw($dadosValidados['mensagem'], function ($message) use ($dadosValidados) {
            $message->to('suporte@tudodigital.com')
                    ->subject('Contacto via Site: ' . $dadosValidados['assunto'])
                    ->replyTo($dadosValidados['email'], $dadosValidados['nome']);
        });
        */

        // Por enquanto, redirecionamos de volta com uma mensagem de sucesso
        return redirect()->route('contacto.index')
            ->with('success', 'A sua mensagem foi enviada com sucesso! Entraremos em contacto brevemente.');
    }
}