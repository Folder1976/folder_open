<?php
header("Content-Type: text/html; charset=UTF-8");


	include('../../config.php');
	include('../config.php');
	include('../core.php');

	include('php-barcode-generator-master/src/BarcodeGenerator.php');
	//include('print/php-barcode-generator-master/src/BarcodeGeneratorPNG.php');
	//$generator = new \Picqer\Barcode\BarcodeGeneratorPNG();
	include('php-barcode-generator-master/src/BarcodeGeneratorJPG.php');
	$generator = new \Picqer\Barcode\BarcodeGeneratorJPG();
	
	$product = $Product->getProduct((int)$_GET['product_id']);
	
	include __DIR__."/../class/brand.class.php";
	$Brand = new Brand();
	
	include __DIR__."/../class/country.class.php";
	$Country = new Country();
	
	$manufacturer = $Brand->getBrand($product['manufacturer_id']);
	
	$country_id = $manufacturer['country_id'];
	if($manufacturer['country_shop_id'] > 0){
		$country_id = $manufacturer['country_shop_id'];
	}
	$country = $Country->getCountry($country_id);
	
	?>
	<style>
		.wrapper{
			width: 210px;
			height: 110px;
			/*border: 1px solid black;
			border-radius: 20px;*/
			text-align: center;
			padding-top: 20px;
		}
		.wrapper:first-of-type{
			padding-top: 0px;
		}
		#code{
			font-size: 12px;
		}
		.barcode{
			width: 90%;
		}
		.image{
			float: left;
		}
		.size{
			width: 27%;
			font-size: 44px;
			font-weight: bold;
			margin-top: 10px;
			margin-left: 10px;
			float: left;
		}
		
		@media print {
				.wrapper:first-child {
					page-break-before: auto;
				} 
		} 
  
	</style>
	<div class="wrapper">
		<div id="barcode">
			<img class="image" src="data:image/png;base64,<?php echo base64_encode($generator->getBarcode($product['code'], $generator::TYPE_CODE_128, 1, 70));?>">
		<span class="size">XL</span></div>
		<div style="clear: both;margin-bottom: 0px;margin-top: 10px;"></div>
		<div id="code" style="float: left;margin-top: -10px;"><?php echo $product['code']; ?></div>
		<div style="clear: both;margin-bottom: 1px;"></div>
		<div id="manufacturer">Фiрма: <?php echo $product['manufacturer']; ?></div>
		<div id="country">Країна: <?php echo $country['name']; ?></div>
	</div>


	<div class="wrapper">
		<div id="barcode">
			<img class="image" src="data:image/png;base64,<?php echo base64_encode($generator->getBarcode($product['code'], $generator::TYPE_CODE_128, 1, 70));?>">
		<span class="size">XL</span></div>
		<div style="clear: both;margin-bottom: 0px;margin-top: 10px;"></div>
		<div id="code" style="float: left;margin-top: -10px;"><?php echo $product['code']; ?></div>
		<div style="clear: both;margin-bottom: 1px;"></div>
		<div id="manufacturer">Фiрма: <?php echo $product['manufacturer']; ?></div>
		<div id="country">Країна: <?php echo $country['name']; ?></div>
	</div>
	
	<div class="wrapper">
		<div id="barcode">
			<img class="image" src="data:image/png;base64,<?php echo base64_encode($generator->getBarcode($product['code'], $generator::TYPE_CODE_128, 1, 70));?>">
		<span class="size">XL</span></div>
		<div style="clear: both;margin-bottom: 0px;margin-top: 10px;"></div>
		<div id="code" style="float: left;margin-top: -10px;"><?php echo $product['code']; ?></div>
		<div style="clear: both;margin-bottom: 1px;"></div>
		<div id="manufacturer">Фiрма: <?php echo $product['manufacturer']; ?></div>
		<div id="country">Країна: <?php echo $country['name']; ?></div>
	</div>