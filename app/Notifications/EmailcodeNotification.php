<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;


class EmailcodeNotification extends Notification
{
    public function __construct($code)
    {
        $this->code = $code;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {

        return (new MailMessage)
            ->subject(__('Código de ingreso'))
            ->line(__('A continuación encontrará el código para ingresar.'))
            ->action(__($this->code),'')
            ->line(__('Si no solicitó ingreso al sistema no debe realizar ninguna acción.'));
    }
}
