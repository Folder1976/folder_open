<?php

include('print/php-barcode-generator-master/src/BarcodeGenerator.php');
include('print/php-barcode-generator-master/src/BarcodeGeneratorPNG.php');
include('print/php-barcode-generator-master/src/BarcodeGeneratorSVG.php');
include('print/php-barcode-generator-master/src/BarcodeGeneratorJPG.php');
include('print/php-barcode-generator-master/src/BarcodeGeneratorHTML.php');

$generatorSVG = new Picqer\Barcode\BarcodeGeneratorSVG();
file_put_contents('tests/verified-files/081231723897-ean13.svg', $generatorSVG->getBarcode('081231723897', $generatorSVG::TYPE_EAN_13));

$generatorHTML = new Picqer\Barcode\BarcodeGeneratorHTML();
file_put_contents('tests/verified-files/081231723897-code128.html', $generatorHTML->getBarcode('081231723897', $generatorHTML::TYPE_CODE_128));

$generatorSVG = new Picqer\Barcode\BarcodeGeneratorSVG();
file_put_contents('tests/verified-files/0049000004632-ean13.svg', $generatorSVG->getBarcode('0049000004632', $generatorSVG::TYPE_EAN_13));
