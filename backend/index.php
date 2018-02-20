<?php
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Expires: " . date("r"));
header("Content-Type: text/html; charset=UTF-8");
	
mb_internal_encoding("UTF-8");

ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);


//Загонем сессию по пользователю
include('../config.php');
include('config.php');
include('core.php');

if (!isset($_SESSION)) session_start();

$User->setParamToSession();

if(!isset($_SESSION['default']['token'])){
	
	header("Location: http://lshoes.com.ua/admin/");
	die();
	
}

if(!isset($_SESSION['default'])){
	header('Location: /'.TMP_DIR.'admin');
    die();
}

if(!isset($_SESSION['default']['user_id']) AND (int)$_SESSION['default']['user_id'] < 1){
    header('Location: /'.TMP_DIR.'admin');
    die();
}

?>

<link rel="stylesheet" type="text/css" href="/<?php echo TMP_DIR;?>backend/css/backend.css?v1">
<link rel="stylesheet" type="text/css" href="/<?php echo TMP_DIR;?>backend/css/main_menu.css">
<link rel="stylesheet" type="text/css" href="/<?php echo TMP_DIR;?>backend/css/ui/style.css">
<link rel="stylesheet" type="text/css" href="/<?php echo TMP_DIR;?>backend/css/new_style.css">
<script type="text/javascript" src="/<?php echo TMP_DIR;?>backend/js/jquery.js"></script>
<script type="text/javascript" src="/<?php echo TMP_DIR;?>backend/js/ui/jquery-ui.js"></script>
<script type="text/javascript" src="/<?php echo TMP_DIR;?>backend/js/log.js"></script>
<script type="text/javascript" src="/<?php echo TMP_DIR;?>backend/js/common_backend.js"></script>
<script>
    var tmp_dir = "<?php echo TMP_DIR; ?>";
</script>



<!-- SmartMenus core CSS (required) -->
<link href="/<?php echo TMP_DIR;?>backend/libs/main_menu/src/css/sm-core-css.css" rel="stylesheet" type="text/css" />
<!-- "sm-simple" menu theme (optional, you can use your own CSS, too) -->
<!--link href="/<?php echo TMP_DIR;?>backend/libs/main_menu/src/css/sm-simple/sm-simple.css" rel="stylesheet" type="text/css" /-->
<!--link href="/<?php echo TMP_DIR;?>backend/libs/main_menu/src/css/sm-mint/sm-mint.css" rel="stylesheet" type="text/css" /-->
<!--link href="/<?php echo TMP_DIR;?>backend/libs/main_menu/src/css/sm-clean/sm-clean.css" rel="stylesheet" type="text/css" /-->
<link href="/<?php echo TMP_DIR;?>backend/libs/main_menu/src/css/sm-blue/sm-blue.css" rel="stylesheet" type="text/css" />
<!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
<!--[if lt IE 9]>
  <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
<![endif]-->
<!-- jQuery -->
<!--script type="text/javascript" src="/<?php echo TMP_DIR;?>backend/libs/main_menu/src/libs/jquery/jquery.js"></script-->
<!-- SmartMenus jQuery plugin -->
<script type="text/javascript" src="/<?php echo TMP_DIR;?>backend/libs/main_menu/src/jquery.smartmenus.js"></script>


<!-- SmartMenus jQuery init -->
<script type="text/javascript">
	$(function() {
		console.log('load menu');
		$('#main-menu').smartmenus({
			mainMenuSubOffsetX: -1,
			subMenusSubOffsetX: 10,
			subMenusSubOffsetY: 0
		});
	});
</script>


<!--script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.3.1/jquery.min.js"></script-->


<input type="hidden" id="route" value="<?php echo (isset($_GET['route'])) ? $_GET['route'] : ''; ?>">


<?php $user = $users[(int)$_SESSION['default']['user_id']];

	$_SESSION['default']['shop_id_limit'] = $user['shop_id'];
	
	if($user['shop_id'] > 0 OR !isset($_SESSION['default']['shop_id'])){
		$_SESSION['default']['shop_id'] = $user['shop_id'];
	}

	if($user['shop_id'] == 0){
		$shops_tmp = $Shops->getShops();
		
		$shop_lists[0] = array(
							   'shop_id' 	=> 0,
							   'name' 		=> 'Все магазины'
							   );
		foreach($shops_tmp as $index => $row){
			
			$shop_lists[$row['shop_id']] = $row;
		}
	}else{
		$shop_lists[] = $Shops->getShop($_SESSION['default']['shop_id']);
	}
	
	
	
?>

<?php $user_menus = $User->getUserMainMenu((int)$_SESSION['default']['user_id']); ?>    
<?php $user_sub_menus = $User->getUserSubMenu((int)$_SESSION['default']['user_id']); ?>    
<?php $functions = array(); ?>
<nav id="main-nav" role="navigation">
	<ul id="main-menu" class="sm sm-blue">
		<!--li class="ui-state-disabled">Aberdeen</li-->
	<?php foreach($user_menus AS $group_index => $user_menu){ ?>
		<?php if(isset($user_menu['name'])){ ?>
			<li><a href="javascript:"><?php echo $user_menu['name']; ?></a>
			<?php if(isset($user_menu['menu']) AND count($user_menu['menu']) > 0){ ?>
				<ul>
					<?php foreach($user_menu['menu'] AS $index => $menu){ ?>
						<?php if($menu['is_show'] == 1 ){ ?>
							<li><a href="/<?php echo TMP_DIR;?>backend/index.php?route=<?php echo $menu['dir'].'/'.$menu['file']; ?>"><?php echo $menu['name']; ?></a>
								<?php if(isset($user_sub_menus[$index])){ ?>
									<ul>
										<?php foreach($user_sub_menus[$index] AS $sub_index => $sub_menu){ ?>
											<?php if(isset($sub_menu['is_show']) AND $sub_menu['is_show'] == 1 ){ ?>
												<li><a href="/<?php echo TMP_DIR;?>backend/index.php?route=<?php echo $sub_menu['dir'].'/'.$sub_menu['file']; ?>"><?php echo $sub_menu['name']; ?></a></li>
												<?php $functions[$sub_menu['dir'].'/'.$sub_menu['file']] = $sub_menu['name']; ?>
											<?php } ?>
										<?php } ?>
									</ul>
								<?php } ?>
							</li>
							
						<?php } ?>
						<?php $functions[$menu['dir'].'/'.$menu['file']] = $menu['name']; ?>
						
					<?php } ?>
				</ul>
			<?php } ?>
		<?php } ?>
		</li>
	<?php } ?>
	
	
	<li style="background-color: #95FF91;">
		<a href="/<?php echo TMP_DIR;?>admin?token=<?php echo $_SESSION['default']['token'];?>">Админка опенкарт</a>
	</li>
	<?php
	
		$operation_in_road = $Operation->getOperationInRoad();
	
		if($operation_in_road){ ?>
			
			<li style="color: #F4465E;">
				<a href="javascript:" style="color: #F4465E;">Накладные в дороге</a>
				<ul>
					<?php foreach($operation_in_road as $row){ ?>
					
						<?php $shop = $Warehouse->getShop($row['from_warehouse_id']); ?>
						
						<li><a href="/backend/index.php?route=operation_shop/in.shop.index.php&invert_operation_id=<?php echo $row['operation_id']; ?>"><?php echo $row['operation_id']. ' [' .$row['date']. '] <b>' .$shop['name'].' -> '.$shops[abs($row['to_warehouse_id'])]['name'].'</b>'; ?></a></li>	
					<?php } ?>
				</ul>
			</li>
			
		<?php } ?>
	
		<li style="background-color: #F4465E;">
			<select name="shop_id" id="shop_id">
				<?php foreach($shop_lists as $shop_id => $row){ ?>
					<?php if(isset($_SESSION['default']['shop_id'] ) AND $_SESSION['default']['shop_id']  == $shop_id){ ?>
						<option value="<?php echo $shop_id;?>" selected><?php echo $row['name'];?></option>
					<?php }else{ ?>
						<option value="<?php echo $shop_id;?>"><?php echo $row['name'];?></option>
					<?php } ?>
				
				<?php } ?>
			</select>
		</li>
			
		<li style="font-size:12px;">&nbsp;
			Пользователь: <br>&nbsp;<b><?php echo $user['firstname'].' '.$user['lastname']; ?></b>
		</li>
	
</ul>
</nav>

	<?php //echo $Shops->isNewPrice(); ?>

	<?php if($Shops->isNewPrice()){ ?>
		
		<a href="/backend/report_price/report.price.print.php">
			<img src="/backend/img/new_prices.jpg" class="new_price" style="display: block;position: fixed;top: calc(100% - 158px);z-index: 10;border: 1px solid #FFAAAA;">
		</a>
		
	<?php } ?>
	

<div style="clear: both;"></div>
<style>
 
	html, body {
        height: 100%;
        width: 100%;
        overflow: auto;
        }
	#shop_id{
		height: 39px;
		padding-left: 10px;
		padding-right: 15px;
		font-size: 14px;
	}
	
</style>
<script>
	$(document).on('change','#shop_id', function(){
		var shop_id = $(this).val();
		
		$.ajax({
			url: '/backend/ajax/ajax_session.php',
			method: "POST",
			data: 'value='+shop_id+'&index=shop_id&key=set_user_session',
			dataType: 'text',
			success: function(json) {
				console.log(json);
				location.reload();
		}
		});
		
	});
</script>


<?php

if(isset($_GET['route'])){
    	
	if(isset($functions[$_GET['route']])){ ?>
	<header>
		<title><?php  echo $functions[$_GET['route']]; ?></title>
	</header>
	
	<?php
		include($_GET['route']);
	}

}

?>

<!-- Тут перетаскиваемые окна -->
  <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
  <script>
	//http://slyweb.ru/jquerydoc/draggable-options.php
	$( function() {
		$( ".attribute_list" ).draggable({
			stop: function(event, ui) {
				set_param('attribute_list_left', parseInt(ui.offset.left));
				set_param('attribute_list_top', parseInt(ui.offset.top));
			}
		});
	} );
	
	$( function() {
		$( ".global_setup" ).draggable({
			stop: function(event, ui) {
				set_param('global_setup_left', parseInt(ui.offset.left));
				set_param('global_setup_top', parseInt(ui.offset.top));
			}
		});
	} );
	
	function set_param(param, value){
		var post = 'key=set_param';
		post = post + '&param='+param;
		post = post + '&value='+value;
		jQuery.ajax({
			type: "GET",
			url: "/backend/ajax/ajax_session.php",
			dataType: "text",
			data: post
		});
		
	}
  </script>
  
	<style>
		.global_setup{
			cursor: move;	
		}
		.global_setup select, .global_setup input{
			cursor: auto;
		}
	</style>
  
  <?php if(isset($_SESSION['auto'])){ ?>
  <script>
	
		$(document).on('click','.size-box', function(event){
				
				//console.log(event.target);
				
				var el = $(this).children('input');
				
				if(el.hasClass('disabled')){
					
				}else{
				
					var qnt = el.val();
					
					
					if(isNaN(parseInt(qnt))){
						qnt = 0;
					}
					
					qnt = parseInt(qnt) + 1;
					el.val(qnt);
					el.trigger('change');
					//el.addClass('disabled');
				
				}
				/*
				setTimeout(
					function(){el.removeClass('disabled')}
						   ,400);
				*/
			});
	
  </script>
  <?php } ?>
  
  
    <?php if(!isset($_SESSION['default']['attribute_list_left']) OR !isset($_SESSION['default']['attribute_list_top'])){
		$_SESSION['default']['attribute_list_left'] = 100;
		$_SESSION['default']['attribute_list_top'] = 100;
	} ?>
	<?php if(!isset($_SESSION['default']['global_setup_left']) OR !isset($_SESSION['default']['global_setup_top'])){ 
		$_SESSION['default']['global_setup_left'] = 100;
		$_SESSION['default']['global_setup_top'] = 100;
	} ?>

	<style>
		div.attribute_list{
			top:<?php echo $_SESSION['default']['attribute_list_top']; ?>px;
			left:<?php echo $_SESSION['default']['attribute_list_left']; ?>px;
			position:fixed;
		}
		div.global_setup{
			top:<?php echo $_SESSION['default']['global_setup_top']; ?>px;
			left: <?php echo $_SESSION['default']['global_setup_left']; ?>px;
			position:fixed;
			display: block;
		}
	</style>

