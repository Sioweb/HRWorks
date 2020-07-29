<?php

declare(strict_types=1);

namespace Sioweb\Hrworks\Core;

use Symfony\Component\Dotenv\Dotenv;

class Client extends Build
{
    public function load(String $Target, Array $Payload = null)
    {
        $this->target = $Target;
        if($Payload !== null) {
            $this->payload = json_encode($Payload);
        }

        if($this->debug >= 2) {
            $this->payload = '{"organizationUnitNumber":"3"}'; 
            $this->target = 'GetPresentPersonsOfOrganizationUnit';
        }

        return $this->request([
            'Host:' . getenv('HRWORKS_API_HOST'),
            'x-hrworks-date:' . $this->currentDate,
            'date:' . $this->currentDate, // Deprecated Value but still required
            'Authorization: ' . getenv('HRWORKS_HMAC') . ' ' . $this->authorisation(),
            'Content-Type: application/json',
            'Connection: Keep-Alive',
            'x-hrworks-target: ' . $this->target,
            $this->payload
        ]);
    }
}