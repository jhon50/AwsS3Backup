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
    $backup->createDump();
    $backup->sendToS3();
    $backup->removeDump();
    $notifier->sendEmail("Backup realizado com sucesso!");
}catch (Exception $e){
    $notifier->sendEmail("Erro ao realizar backup!");
}
