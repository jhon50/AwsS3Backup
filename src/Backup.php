<?php
namespace Setra;

use Aws\Credentials\CredentialProvider;
use Aws\Sdk;
use Exception;


class Backup
{
    public function main()
    {
        $this->uploadToS3("/home/desenvolvedor/PhpstormProjects/AwsS3Backup/composer.json", "Backup.php");
    }

    public function uploadToS3($origem, $destino)
    {
        //why is this like this???
        $pr = CredentialProvider::ini("default", realpath(dirname(__FILE__) . "/../") . "/config/credentials.ini");
        $provider = CredentialProvider::memoize($pr);

        $config = [
            'region' => 'us-west-2',
            'version' => '2006-03-01',
            'credentials' => $provider
        ];

        $sdk = new Sdk($config);

        $client = $sdk->createS3();

        $client->putObject([
            'Bucket' => 'hexabackups',
            'SourceFile' => $origem,

            /* Nome do arquivo a ser salvo no servidor */
            'Key' => $destino
        ]);
    }
}
