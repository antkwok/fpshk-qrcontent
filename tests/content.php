<?php

include_once __DIR__ . '/../src/ContentGenerator.php';

use AntKwok\FPSHKQrContent\ContentGenerator;

$obj = new ContentGenerator("4231197", 10);
//$obj->setBillNumber(1234);
//$obj->setCustomerLabel("I'm fool");
//$obj->setMobileNumber("91530123");
//$obj->setReferenceLabel("028-2-044981");
echo $obj->generateQrCodeContent();
