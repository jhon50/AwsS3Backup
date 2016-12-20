<?php

require 'vendor/autoload.php';

class Backup
{
    private $sharedConfig = [
        'region' => 'us-west-2',
        'version' => 'latest'
    ];

    public function uploadToS3()
    {
        $sdk = new \Aws\Sdk($this->sharedConfig);
        $client = $sdk->createS3();

        $result = $client->putObject([
            'Bucket' => 'my-bucket',
            'Key'    => 'my-key',
            'Body'   => 'this is the body!'
        ]);
        

    }


}
