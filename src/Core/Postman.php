<?php

declare(strict_types=1);

namespace Sioweb\Hrworks\Core;

use Symfony\Component\Dotenv\Dotenv;

class Postman extends Build
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

        die(json_encode([
            "info" => [
                "_postman_id" => "6651bb56-841d-4e6e-ab4f-449d3103473d",
                "name" => "HRWorks Example",
                "schema" => "https://schema.getpostman.com/json/collection/v2.1.0/collection.json"
            ],
            "item" => [
                [
                    "name" => 'Execute ' . $this->target,
                    "protocolProfileBehavior" => [
                        "disableBodyPruning" => true,
                        "disabledSystemHeaders" => [
                            "content-type" => true,
                            "accept" => true,
                            "accept-encoding" => true,
                            "connection" => true,
                            "content-length" => true,
                            "host" => true,
                            "user-agent" => true
                        ]
                    ],
                    "request" => [
                        "method" => "GET",
                        "header" => [
                            [
                                "key" => "Host",
                                "value" => getenv('HRWORKS_API_HOST'),
                                "type" => "text"
                            ],
                            [
                                "key" => "x-hrworks-date",
                                "value" => $this->currentDate,
                                "type" => "text"
                            ],
                            [
                                "key" => "date",
                                "value" => $this->currentDate,
                                "type" => "text"
                            ],
                            [
                                "key" => "Authorization",
                                "value" => getenv('HRWORKS_HMAC') . ' ' . $this->authorisation(),
                                "type" => "text"
                            ],
                            [
                                "key" => "Content-Type",
                                "value" => "application/json",
                                "type" => "text"
                            ],
                            [
                                "key" => "x-hrworks-target",
                                "value" => $this->target,
                                "type" => "text"
                            ]
                        ],
                        "body" => [
                            "mode" => "raw",
                            "raw" => $this->payload
                        ],
                        "url" => [
                            "raw" => "https://" . getenv('HRWORKS_API_HOST'),
                            "protocol" => "https",
                            "host" => [
                                "api",
                                "hrworks",
                                "de"
                            ]
                        ],
                        "description" => $this->target
                    ],
                    "response" => []
                ]
            ],
            "protocolProfileBehavior" => []
        ], JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES));
    }
}