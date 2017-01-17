<?php
use Setra\Backup;
use Setra\Configuracao;
use Setra\Email;
require("vendor/autoload.php");

$configuracao = new Configuracao();
$backup = new Backup($configuracao);
$notifier = new Email($configuracao);

try {
    $backup->createDirectoryIfDoesntExist();
    $backup->createTemp();
    $backup->sendToS3();
    $backup->removeTemp();
    $notifier->sendEmail("Backup realizado com sucesso!", ":: Sucesso :: Backup Setrapedia");
}catch (Exception $e){
    $notifier->sendEmail("Erro ao realizar backup! {$e->getMessage()}", ":: Erro Fatal :: Backup Setrapedia");
}
