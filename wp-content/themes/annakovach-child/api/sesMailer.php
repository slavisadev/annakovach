<?php

require 'vendor/autoload.php';
use Aws\Ses\SesClient;

class SesMailer{

    private $client = null;

    public function __construct()
    {
        $this->client = SesClient::factory(array(
            'key'    => 'AKIAJD3OZLYM23YVQIQQ',
            'secret' => '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',
            'region' => 'us-east-1'
        ));
    }

    public function sendSesMail($data,$subject,$email)
    {
        $options = array(
            'Source'      => 'readings@annakovach.com',
            'Destination' => array(
                'ToAddresses' => array('readings@annakovach.com',$email)
                //'ToAddresses' => array($email)
            ),
            'Message' => array(
                'Subject' => array(
                    'Data' => $subject,
                    'Charset' => 'utf8'
                ),
                'Body' => array(
                    'Html' => array(
                        'Data' => $data,
                        'Charset' => 'utf8'
                    )
                )
            )
        );

        try {
            if($this->client->sendEmail($options))
            {
                return true;
            }
            else
            {
                return false;
            }

        } catch(Exception $e) {
            //echo $e->getMessage();
            return false;
        }
    }

}
