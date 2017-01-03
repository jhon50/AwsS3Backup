<?php

namespace Setra;

use InvalidArgumentException;
use Exception;

class Configuracao
{
    protected $arquivo;
    protected $dados;

    public function __construct()
    {
        $arquivo = realpath(__DIR__ . "/../") . "/config/config.ini";
        if (file_exists($arquivo) == false || is_readable($arquivo) == false) {
            throw new Exception("O arquivo {$arquivo} nao existe ou nao pode ser lido.");
        }
        $this->arquivo = $arquivo;
        $this->dados   = parse_ini_file($arquivo, true);
    }

    public function getBySection($section)
    {
        if (key_exists($section, $this->dados) == false) {
            throw new InvalidArgumentException("A sessao {$section} nao foi encontrada no arquivo {$this->arquivo}.");
        }
        return $this->dados[$section];
    }
}
