<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class EncomendaEfetuadaMail extends Mailable
{
    use Queueable, SerializesModels;

    public $referencia;
    public $carrinho;
    public $dadosCliente;
    public $contasBancarias;

    public function __construct($referencia, $carrinho, $dadosCliente, $contasBancarias)
    {
        $this->referencia = $referencia;
        $this->carrinho = $carrinho;
        $this->dadosCliente = $dadosCliente;
        $this->contasBancarias = $contasBancarias;
    }

    public function build()
    {
        return $this->subject('Confirmação de Encomenda - ' . $this->referencia)
                    ->view('emails.encomenda');
    }
}