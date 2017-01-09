<?php

namespace Setra;

use InvalidArgumentException;
use Exception;
use PHPMailer;

class Email
{
    private $SMTP;

    public function __construct(Configuracao $configuracao)
    {
        $this->SMTP = $configuracao->getBySection("smtp");
    }

    public function sendEmail($message){
        $mail = new PHPMailer;
        $mail->SMTPDebug = 0;
        $mail->isSMTP();
        $mail->Host = $this->SMTP['host'];
        $mail->SMTPAuth = true;
        $mail->Username = $this->SMTP['username'];
        $mail->Password = $this->SMTP['password'];
        $mail->SMTPSecure = $this->SMTP['secure'];
        $mail->Port = $this->SMTP['port'];
        $mail->setFrom('setrasolucoes2015@gmail.com', 'Mailer');
        $mail->addAddress('washigton@setrasolucoes.com.br', 'Joe User');
        $mail->isHTML(true);                                  // Set email format to HTML

        $mail->Subject = 'Notificação de Backup Setrapedia';
        $mail->Body    = $message;
        //$mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

        if(!$mail->send()) {
            echo 'Message could not be sent.';
            echo 'Mailer Error: ' . $mail->ErrorInfo;
        } else {
            echo 'Message has been sent';
        }
    }

}
