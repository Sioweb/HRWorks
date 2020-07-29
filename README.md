# HRWorks API PHP Example Client

This client can be used to connect with HRWorks API.

## Installation

`composer req sioweb/hrworks`

## Credentials

First create, or update, `.env`-File:

```
HRWORKS_ACCESS_KEY="your_access_key"
HRWORKS_SECRET_KEY="your_secret_key"
```

## Example

Make sure your admin gave you the correct permissions for the actions you want to use.

After installation yout can create a PHP-File with following content. Please be sure, that you use the correct path to `vendor/autoload.php`.

```php
<?php

use Sioweb\Hrworks\Core\Client;

include 'vendor/autoload.php';

$Client = new Client();
$GetAllActivePersons = $Client->load('GetAllActivePersons');

$Organisations = $Client->load('GetAllOrganizationUnits');
foreach($Organisations['organizationUnits'] as $Organisation) {
    echo '<h3>' . $Organisation['organizationUnitName'] . ' (' . $Organisation['organizationUnitNumber'] . ')</h3>';
    echo '<pre>' . __METHOD__ . ":\n" . print_r($Client->load('GetPresentPersonsOfOrganizationUnit', [
        'organizationUnitNumber' => $Organisation['organizationUnitNumber']
    ]), true) . "\n#################################\n\n" . '</pre>';
}
```