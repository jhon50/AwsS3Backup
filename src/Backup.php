<?php
namespace Setra;

use Aws\Credentials\CredentialProvider;
use Aws\Sdk;
use Exception;


class Backup
{
    private $configuracao;
    private $dbName;
    private $dbConn;
    private $directory;

    public function __construct(Configuracao $configuracao)
    {
        $this->configuracao = $configuracao;
        $this->dbConn = $this->configuracao->getBySection("mysql");
        $this->dbName = $this->configuracao->getBySection("database");
        $this->directory = $this->configuracao->getBySection("directory");
    }

    public function sendToS3()
    {
        // Retrieve current DateTime
        $now = date("Y-m-d-H-i-s");

        // Upload file from (directory) to (remote file name)
        $this->uploadFileToS3("{$this->directory['source']}/dump.bz2", "{$this->directory['destination']}/{$this->dbName['nome_dump']}-{$now}.bz2");
    }

    public function createDirectoryIfDoesntExist(){
        if(!file_exists($this->directory['source'])){
            mkdir($this->directory['source']);
        }
    }

    /* Why use this? execute console command??*/
    private function exec($comando)
    {
        $retorno = null;
        system($comando, $retorno);
        if ($retorno !== 0) {
            throw new Exception("Ocorreu um erro ao executar o comando: {$comando}");
        }
        return true;
    }

    public function createTemp()
    {
        $senha = addslashes($this->dbConn['senha']);
        $query = "mysqldump -u {$this->dbConn['usuario']} -p\"{$senha}\" {$this->dbName['nome_banco']}";
        $query .= " | bzip2 > {$this->directory['source']}/dump.bz2";
        $this->exec($query);
        return $this;
    }

    public function removeTemp()
    {
        $comando = "rm -rf {$this->directory['source']}";
        return $this->exec($comando);
    }

    public function uploadFileToS3($origem, $destino)
    {
        $pr = CredentialProvider::ini("default", realpath(dirname(__FILE__) . "/../") . "/config/credentials.ini");
        $provider = CredentialProvider::memoize($pr);

        /* Pega a section no arquivo config.ini */
        $s3 = $this->configuracao->getBySection("s3");

        $config = [
            'region' => $s3['regiao'],
            'version' => $s3['versao'],
            'credentials' => $provider
        ];

        $sdk = new Sdk($config);
        $client = $sdk->createS3();
        $client->putObject([
            'Bucket' => $s3['bucket'],
            'SourceFile' => $origem,

            /* Nome do arquivo a ser salvo no servidor */
            'Key' => $destino
        ]);
    }
}
