<?php

namespace Setra;

use InvalidArgumentException;
use Exception;
use PHPMailer;

class Email
{
    private $SMTP;
    private $mail;

    public function __construct(Configuracao $configuracao)
    {
        $this->SMTP = $configuracao->getBySection("smtp");
        $this->configSMTP();
    }

    protected function configSMTP()
    {
        $this->mail = new PHPMailer;
        $this->mail->SMTPDebug = 0;
        $this->mail->isSMTP();
        $this->mail->Host = $this->SMTP['host'];
        $this->mail->SMTPAuth = true;
        $this->mail->Username = $this->SMTP['username'];
        $this->mail->Password = $this->SMTP['password'];
        $this->mail->SMTPSecure = $this->SMTP['secure'];
        $this->mail->Port = $this->SMTP['port'];
        $this->mail->setFrom($this->SMTP['from'], 'Mailer');
        $this->mail->addAddress($this->SMTP['to'], 'Joe User');
        $this->mail->isHTML(true);
    }

    public function sendEmail($message, $status)
    {
        $this->mail->Subject = $status;
        $this->mail->Body = $message;
        return $this->mail->send();
    }
}
