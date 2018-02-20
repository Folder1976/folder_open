<?php

/*
- TYPE_CODE_39
- TYPE_CODE_39_CHECKSUM
- TYPE_CODE_39E
- TYPE_CODE_39E_CHECKSUM
- TYPE_CODE_93
- TYPE_STANDARD_2_5
- TYPE_STANDARD_2_5_CHECKSUM
- TYPE_INTERLEAVED_2_5
- TYPE_INTERLEAVED_2_5_CHECKSUM
- TYPE_CODE_128
- TYPE_CODE_128_A
- TYPE_CODE_128_B
- TYPE_CODE_128_C
- TYPE_EAN_2
- TYPE_EAN_5
- TYPE_EAN_8
- TYPE_EAN_13
- TYPE_UPC_A
- TYPE_UPC_E
- TYPE_MSI
- TYPE_MSI_CHECKSUM
- TYPE_POSTNET
- TYPE_PLANET
- TYPE_RMS4CC
- TYPE_KIX
- TYPE_IMB
- TYPE_CODABAR
- TYPE_CODE_11
- TYPE_PHARMA_CODE
- TYPE_PHARMA_CODE_TWO_TRACKS
*/

include('php-barcode-generator-master/src/BarcodeGenerator.php');
include('php-barcode-generator-master/src/BarcodeGeneratorPNG.php');
$generator = new \Picqer\Barcode\BarcodeGeneratorPNG();

include "Zebra/CommunicationException.php";
include "Zebra/Contracts/Zpl/Image.php";
include "Zebra/Client.php";
include "Zebra/Zpl/Image.php";
include "Zebra/Zpl/Builder.php";

use Zebra\CommunicationException;
use Zebra\Client;
use Zebra\Zpl\Image;
use Zebra\Zpl\Builder;

$image = new Image(file_get_contents("data:image/png;base64,".base64_encode($generator->getBarcode($_GET['barcode'], $generator::TYPE_EAN_2))));

$zpl = new Builder();
$zpl->fo(50, 50)->gf($image)->fs();

$client = new Client('127.0.0.1');
$client->send($zpl);