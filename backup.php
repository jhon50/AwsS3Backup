<?php
use Setra\Backup;

require("vendor/autoload.php");

    $backup = new Backup();
    $backup->main();
    echo var_dump($backup);
