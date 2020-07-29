<?php

declare(strict_types=1);

namespace Sioweb\Hrworks\Core;

use Symfony\Component\Dotenv\Dotenv;

class Build
{
    protected $target = '';
    protected $payload = '';

    protected $debug = 0;

    protected $currentDate = null;
    protected $dayString = null;

    private $rootDir = null;

    public function __construct($debug = 0)
    {
        $this->debug = $debug;
        header('Access-Control-Allow-Origin: *');
    }

    public function init()
    {
        $dotenv = new Dotenv(true);
        $dotenv->load($this->getRootDir() . '.env');

        if ($this->debug >= 2) {
            putenv("HRWORKS_ACCESS_KEY=kdVDiLrylwri8+oLffNi");
            putenv("HRWORKS_SECRET_KEY=BGWVXYEYrPJVzU9W9B1x2o2vov0SdihAv");
        }

        if (getenv('HRWORKS_ENVIRONMENT') === false) {
            putenv("HRWORKS_ENVIRONMENT=production");
        }

        if (getenv('HRWORKS_HMAC') === false) {
            putenv("HRWORKS_HMAC=HRWORKS-HMAC-SHA256");
        }

        if (getenv('HRWORKS_API_HOST') === false) {
            putenv("HRWORKS_API_HOST=api.hrworks.de");
        }

        $this->setupDate();
    }

    public function setRootDir(String $rootDir)
    {
        $this->rootDir = $rootDir;
    }

    public function getRootDir()
    {
        return rtrim($this->rootDir??$_SERVER['DOCUMENT_ROOT'] . '/..', '/') . '/';
    }

    protected function request(Array $Header)
    {
        try {
            $curl = curl_init();
            curl_setopt($curl, CURLINFO_HEADER_OUT, true);
            curl_setopt($curl, CURLOPT_URL, 'https://' . getenv('HRWORKS_API_HOST'));
            curl_setopt($curl, CURLOPT_POSTFIELDS, $this->payload);
            curl_setopt($curl, CURLOPT_HTTPHEADER, $Header);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_POST, true);

            $content = curl_exec($curl);

            if ($content === false) {
                throw new Exception(curl_error($curl), curl_errno($curl));
            }

            if ($this->debug >= 1) {
                echo '<h3>CURL-Info:</h3>';
                echo '<pre>' . print_r(curl_getinfo($curl), true) . "\n#################################\n\n" . '</pre><br><hr><br>';
            }

            curl_close($curl);
            if ($this->debug >= 2) {
                die;
            }
            return json_decode($content, true);
        } catch (\Exception $e) {
            die(sprintf(
                'Curl failed with error #%d: %s',
                $e->getCode(),
                $e->getMessage()
            ));
        }
    }

    protected function setupDate()
    {
        $dateTime = new \DateTime();
        $dateTime->setTimeZone(new \DateTimeZone('UTC'));

        if ($this->debug >= 2) {
            $this->currentDate = '20200409T093020Z';
            $this->dayString = '20200409';
        } else {
            $this->currentDate = $dateTime->format('Ymd\THis\Z');
            $this->dayString = $dateTime->format('Ymd');
        }
    }

    protected function header()
    {
        $header = str_replace(' ', '', implode("\n", [
            'POST',
            '/',
            '',
            'content-type:application/json',
            'host:' . getenv('HRWORKS_API_HOST'),
            'x-hrworks-date:' . $this->currentDate,
            'x-hrworks-target:' . $this->target
        ]));

        if ($this->debug >= 1) {
            echo '<h3>Header:</h3>';
            echo '<pre>' . print_r($header . "\n\n" . hash('SHA256', $this->payload), true) . "<br>\t===<br>3546fe6fceac608eec5313fe9842506e8720b6c203b8405cb420ac8df7bc0840</pre><br><hr><br>";
            /**
             * POST
             * /
             *
             * content-type:application/json
             * host:api.hrworks.de
             * x-hrworks-date:20200409T093020Z
             * x-hrworks-target:GetPresentPersonsOfOrganizationUnit
             * 
             * 3546fe6fceac608eec5313fe9842506e8720b6c203b8405cb420ac8df7bc0840
             */
        }

        return $header;
    }

    protected function signature()
    {
        $a = hash_hmac('SHA256', $this->dayString, 'HRWORKS' . getenv('HRWORKS_SECRET_KEY'), true);
        $b = hash_hmac('SHA256', getenv('HRWORKS_ENVIRONMENT'), $a, true);
        $c = hash_hmac('SHA256', 'hrworks_api_request', $b, true);
        $Request = implode("\n", [
            getenv('HRWORKS_HMAC'),
            $this->currentDate,
            hash('SHA256', $this->header() . "\n\n" . hash('SHA256', $this->payload))
        ]);

        if ($this->debug >= 1) {
            echo '<h3>Signature:</h3>';
            echo '<pre>' . print_r($Request, true) . "<br>\t===<br>2daeec35f547252882ff0819c1ddd4ab127b389e3920fcfd7d56b13071bab86d</pre><br><hr><br>";
            /**
             * HRWORKS-HMAC-SHA256
             * 20200409T093020Z
             * 2daeec35f547252882ff0819c1ddd4ab127b389e3920fcfd7d56b13071bab86d
             */
        }

        $d = hash_hmac('SHA256', $Request, $c, true);

        if ($this->debug >= 1) {
            echo '<pre>' . print_r(bin2hex($d), true) . "<br>\t===<br>617d7b1b2bd897f4878e1c4f19b9b8ad471530a11a72a8ce928d6e753a5db5f2</pre><br><hr><br>";
            /**
             * 617d7b1b2bd897f4878e1c4f19b9b8ad471530a11a72a8ce928d6e753a5db5f2
             */
        }
        return bin2hex($d);
    }

    protected function authorisation()
    {
        $Authorization = [
            'Credential=' . urlencode(getenv('HRWORKS_ACCESS_KEY')) . '/' . getenv('HRWORKS_ENVIRONMENT'),
            'SignedHeaders=content-type;host;x-hrworks-date;x-hrworks-target',
            'Signature=' . $this->signature()
        ];
        if ($this->debug >= 1) {
            echo '<h3>Authorisation:</h3>';
            echo '<pre>' . print_r($Authorization, true) . "<br>\t===<br>Signature=617d7b1b2bd897f4878e1c4f19b9b8ad471530a11a72a8ce928d6e753a5db5f2</pre><br><hr><br>";
            /**
             * HRWORKS-HMAC-SHA256 Credential=kdVDiLrylwri8%2BoLffNi/production,
             * SignedHeaders=content-type;host;x-hrworks-date;x-hrworks-target,
             * Signature=617d7b1b2bd897f4878e1c4f19b9b8ad471530a11a72a8ce928d6e753a5db5f2
             */
        }
        return implode(', ', $Authorization);
    }
}
