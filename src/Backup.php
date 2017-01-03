<?php
namespace Setra;

use Aws\Credentials\CredentialProvider;
use Aws\Sdk;
use Exception;


class Backup
{
    private $configuracao;
    private $dump;

    public function __construct(Configuracao $configuracao)
    {
        $this->configuracao = $configuracao;
        $this->dump = $this->configuracao->getBySection("dump");
    }

    public function sendToS3()
    {
        // Retrieve current DateTime
        $now = date("Y-m-d H:i:s");

        // Upload file from (directory) to (remote file name)
        $this->uploadFileToS3("/home/desenvolvedor/PhpstormProjects/AwsS3Backup/dump.bz2", "{$this->dump['nome_dump']}-{$now}");
    }

    /* Why use this? Provavelmente para capturar erro de execucao de SQL */
    private function exec($comando)
    {
        $retorno = null;
        system($comando, $retorno);
        if ($retorno !== 0) {
            throw new Exception("Ocorreu um erro ao executar o comando: {$comando}");
        }
        return true;
    }

    public function createDump()
    {
        // Dados de conexão do mysql
        $dbConn = $this->configuracao->getBySection("mysql");
        // Nome do banco de dados
        $dbName = $this->configuracao->getBySection("database");

        $senha = addslashes($dbConn['senha']);
        $query = "mysqldump -u {$dbConn['usuario']} -p\"{$senha}\" {$dbName['nome_banco']}";
        $query .= " | bzip2 > dump.bz2";
        $this->exec($query);
        return $this;
    }

    public function removeDump()
    {
        $comando = "rm dump*";
        return $this->exec($comando);
    }

    public function uploadFileToS3($origem, $destino)
    {
        //why is this like this???
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
