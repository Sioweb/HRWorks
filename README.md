# HRWorks API PHP Example Client

This client can be used to connect with HRWorks API https://www.hrworks.de/produkt/api/

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
// index.php
use Sioweb\Hrworks\Core\Client;

include 'vendor/autoload.php';

$Client = new Client();
$Organisations = $Client->load('GetAllOrganizationUnits');

foreach ($Organisations['organizationUnits'] as $Organisation) {
    echo '<h3>' . $Organisation['organizationUnitName'] . ' (Unit id: ' . $Organisation['organizationUnitNumber'] . ')</h3>';
    echo '<pre>' . print_r($Client->load('GetPresentPersonsOfOrganizationUnit', [
        'organizationUnitNumber' => $Organisation['organizationUnitNumber']
    ]), true) . '</pre>';
}
```

## Load

Load requires minimum a target. For some actions like `GetPresentPersonsOfOrganizationUnit` you need to add payload as Array: 

```php
// Client::load(String $Target, Array Payload = NULL)

$Client->load('GetPresentPersonsOfOrganizationUnit', [
    'organizationUnitNumber' => 1
])
```

## Postman

You can also create a [Postman](https://www.postman.com) import with this package. You simply have to use Postman::load instead of Client::load. 

**Attention:** Postman will not send correct signature for now. Maybe this will work in some future.

```json
{"type":"InvalidSignatureError","errorCode":403,"errorMessage":"The signature included in your request does not match the signature we calculated."}
```