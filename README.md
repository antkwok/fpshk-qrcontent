# fpshk-qrcontent
Hong Kong FPS QrCode content

### Install

Require this package with composer using the following command:

```bash
composer require antkwok/fpshk-qrcontent
```

### Usage
```php
// use autoload when you use composer
include_once __DIR__ . '/../src/ContentGenerator.php';

use AntKwok\FPSHKQrContent\ContentGenerator;

// temporary support FPS Id only, will support later 
$obj = new ContentGenerator("4231197", 10);
//$obj->setBillNumber(1234);
//$obj->setCustomerLabel("I'm fool");
//$obj->setMobileNumber("91530123");
//$obj->setReferenceLabel("028-2-044981");
echo $obj->generateQrCodeContent();
