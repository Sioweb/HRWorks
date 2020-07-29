# HRWorks API PHP Example Client

This client can be used to connect with HRWorks API https://www.hrworks.de/produkt/api/

## Installation

`composer req sioweb/hrworks`

## Credentials

First create, or update, `.env`-File in root directory:

```
HRWORKS_ACCESS_KEY="your_access_key"
HRWORKS_SECRET_KEY="your_secret_key"
```

## Example

Make sure your admin gave you the correct permissions for the actions you want to use.

After installation yout can create a PHP-File in `/web` directory. Its recommendet to use a subdirectory, so users cannot access sensitive directories like `/vendor`, or `/var/logs` if you use it.

```php
<?php
// /web/index.php
use Sioweb\Hrworks\Core\Client;

include '../vendor/autoload.php';

$Client = new Client();
$Organisations = $Client->load('GetAllOrganizationUnits');

foreach ($Organisations['organizationUnits'] as $Organisation) {
    echo '<h3>' . $Organisation['organizationUnitName'] . ' (Unit id: ' . $Organisation['organizationUnitNumber'] . ')</h3>';
    echo '<pre>' . print_r($Client->load('GetPresentPersonsOfOrganizationUnit', [
        'organizationUnitNumber' => $Organisation['organizationUnitNumber']
    ]), true) . '</pre>';
}
```

## Client

### Load

Load requires minimum a target. For some actions like `GetPresentPersonsOfOrganizationUnit` you need to add payload as Array: 

```php
// Client::load(String $Target, Array Payload = NULL)

$Client->load('GetPresentPersonsOfOrganizationUnit', [
    'organizationUnitNumber' => 1
])
```

### setRootDir

The client loads `.env` from root dir, which is by default `$_SERVER['DOCUMENT_ROOT'] . '/../`. If you install script in `/` instead of `/web`, you need to run something like `php $Client->setRootDir($_SERVER['DOCUMENT_ROOT']);`.

#### $_SERVER['DOCUMENT_ROOT'] . '/../

Example output: `/var/www/html/yourdomain/web/../`

## Postman

You can also create a [Postman](https://www.postman.com) import with this package. You simply have to use Postman::load instead of Client::load. 

**Attention:** Postman will not send correct signature for now. Maybe this will work in some future.

```json
{"type":"InvalidSignatureError","errorCode":403,"errorMessage":"The signature included in your request does not match the signature we calculated."}
```

Keep in mind, that you have to update the date and the x-hrworks-date all 15 minutes.