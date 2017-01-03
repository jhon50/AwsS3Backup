<?php
use Setra\Backup;
use Setra\Configuracao;
require("vendor/autoload.php");

$configuracao = new Configuracao();
$backup = new Backup($configuracao);
$backup->createDump();
$backup->sendToS3();

echo var_dump($backup);