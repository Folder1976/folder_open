<!DOCTYPE html>

<?php if(!defined("USA_MODE")){ ?> 
    <html dir="<?php echo $direction; ?>" lang="ru">
<?php }else{ ?>
    <html dir="<?php echo $direction; ?>" lang="en">
<?php } ?>

<head>
	
	<meta charset="utf-8" />
    <title><?php echo $title; ?></title>
	<meta http-equiv="Cache-Control" content="no-cache" />
	<meta http-equiv="Cache-Control" content="private" />
	<meta http-equiv="Cache-Control" content="max-age=300, must-revalidate" />

    <meta name="KEYWORDS" content="<?php echo $keywords; ?>">
    <meta name="DESCRIPTION" content="<?php echo $description; ?>">

<?php //Тут мета от поиска яндекса! Он должен быть в хедере ?>
<?php if(strpos($_SERVER['SERVER_NAME'],'alta-karter') !== false){ ?>
<meta name="yandex-verification" content="23df015685ab33c0" />	
<?php } ?>

    <?php foreach ($links as $link) { ?>
    <link href="<?php echo $link['href']; ?>" rel="<?php echo $link['rel']; ?>"/>
    <?php } ?>
  
<?php
	
	if(isset($og)){ ?>
		
		<meta property="og:type" content="<?php echo $og['type'];?>" />
        <meta property="og:title" content="<?php echo $og['name'];?>" />
        <meta property="og:url" content="<?php echo $CDN.$og['keyword'];?>" />
        <meta property="og:image" content="<?php echo $CDN.''.$og['image'];?>" />
        <meta property="og:description" content="<?php echo str_replace('"',"'",$og['meta_description']);?>" />
	
	<?php } ?>
  
    <?php if(!defined("USA_MODE")){ ?> 
    <meta name='yandex-verification' content='465c9ac1412d3ccf' />
    <?php } ?>
    
    <?php if ($icon) { ?>
    <link href="/favicon.ico" rel="shortcut icon" />
    <link href="/favicon.ico" rel="icon" type="image/x-icon" />
    <?php } ?>

    
    <meta name='yandex-verification' content='4f070ce2a1cd5d95' />
    <meta name=viewport content="width=device-width, initial-scale=1" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1" />

	
    <!-- Новая верска -->
    <script src="//code.jquery.com/jquery-3.0.0.slim.min.js"></script>
    <script src="//code.jquery.com/jquery-2.1.4.min.js"></script>
	<script type="text/javascript" src="<?php echo $CDN;?>catalog/view/javascript/jquery-ui.js"></script>
    <!--script src="//code.jquery.com/ui/1.12.0/jquery-ui.js" integrity="sha256-0YPKAwZP7Mp3ALMRVB2i8GXeEndvCq3eSl/WsAl1Ryk=" crossorigin="anonymous"></script-->

	<script type="text/javascript" src="<?php echo $CDN;?>catalog/view/theme/4WB/js/click-carousel.js"></script>
	<script type="text/javascript" src="<?php echo $CDN;?>catalog/view/theme/4WB/js/carousel.js"></script>
	
 
	<script type="text/javascript" src="<?php //echo $CDN;?>/catalog/view/javascript/common.min.js"></script>
	<script type="text/javascript" src="<?php echo $CDN;?>catalog/view/javascript/common_2.0.min.js"></script>


    <style>
        /* font-family: "SegoeUIRegular"; */
        @font-face {
            font-family: "SegoeUIRegular";
            src: url("<?php echo $CDN;?>catalog/view/theme/4WB/fonts/SegoeUIRegular/SegoeUIRegular.eot");
            src: url("<?php echo $CDN;?>catalog/view/theme/4WB/fonts/SegoeUIRegular/SegoeUIRegular.eot?#iefix")format("embedded-opentype"),
                url("<?php echo $CDN;?>catalog/view/theme/4WB/fonts/SegoeUIRegular/SegoeUIRegular.woff") format("woff"),
                url("<?php echo $CDN;?>catalog/view/theme/4WB/fonts/SegoeUIRegular/SegoeUIRegular.ttf") format("truetype");
            font-style: normal;
            font-weight: normal;
        }
        /* font-family: "SegoeUIBold"; */
        @font-face {
            font-family: "SegoeUIBold";
            src: url("<?php echo $CDN;?>catalog/view/theme/4WB/fonts/SegoeUIBold/SegoeUIBold.eot");
            src: url("<?php echo $CDN;?>catalog/view/theme/4WB/fonts/SegoeUIBold/SegoeUIBold.eot?#iefix")format("embedded-opentype"),
                url("<?php echo $CDN;?>catalog/view/theme/4WB/fonts/SegoeUIBold/SegoeUIBold.woff") format("woff"),
                url("<?php echo $CDN;?>catalog/view/theme/4WB/fonts/SegoeUIBold/SegoeUIBold.ttf") format("truetype");
            font-style: normal;
            font-weight: normal;
        }
        /* font-family: "SegoeUIItalic"; */
        @font-face {
            font-family: "SegoeUIItalic";
            src: url("<?php echo $CDN;?>catalog/view/theme/4WB/fonts/SegoeUIItalic/SegoeUIItalic.eot");
            src: url("<?php echo $CDN;?>catalog/view/theme/4WB/fonts/SegoeUIItalic/SegoeUIItalic.eot?#iefix")format("embedded-opentype"),
                url("<?php echo $CDN;?>catalog/view/theme/4WB/fonts/SegoeUIItalic/SegoeUIItalic.woff") format("woff"),
                url("<?php echo $CDN;?>catalog/view/theme/4WB/fonts/SegoeUIItalic/SegoeUIItalic.ttf") format("truetype");
            font-style: normal;
            font-weight: normal;
        }
        /* font-family: "SegoeUILight"; */
        @font-face {
            font-family: "SegoeUILight";
            src: url("<?php echo $CDN;?>catalog/view/theme/4WB/fonts/SegoeUILight/SegoeUILight.eot");
            src: url("<?php echo $CDN;?>catalog/view/theme/4WB/fonts/SegoeUILight/SegoeUILight.eot?#iefix")format("embedded-opentype"),
                url("<?php echo $CDN;?>catalog/view/theme/4WB/fonts/SegoeUILight/SegoeUILight.woff") format("woff"),
                url("<?php echo $CDN;?>catalog/view/theme/4WB/fonts/SegoeUILight/SegoeUILight.ttf") format("truetype");
            font-style: normal;
            font-weight: normal;
        }
        /* font-family: "SegoeUISemiBold"; */
        @font-face {
            font-family: "SegoeUISemiBold";
            src: url("<?php echo $CDN;?>catalog/view/theme/4WB/fonts/SegoeUISemiBold/SegoeUISemiBold.eot");
            src: url("<?php echo $CDN;?>catalog/view/theme/4WB/fonts/SegoeUISemiBold/SegoeUISemiBold.eot?#iefix")format("embedded-opentype"),
                url("<?php echo $CDN;?>catalog/view/theme/4WB/fonts/SegoeUISemiBold/SegoeUISemiBold.woff") format("woff"),
                url("<?php echo $CDN;?>catalog/view/theme/4WB/fonts/SegoeUISemiBold/SegoeUISemiBold.ttf") format("truetype");
            font-style: normal;
            font-weight: normal;
        }
    </style>

    <?php foreach ($styles as $style) { ?>
    <link href="<?php echo $style['href']; ?>" type="text/css" rel="<?php echo $style['rel']; ?>" media="<?php echo $style['media']; ?>" />
    <?php } ?>


	<link href="<?php echo $CDN; ?>catalog/view/javascript/highslide.css" rel="stylesheet">
	<script type="text/javascript" src="<?php echo $CDN;?>catalog/view/javascript/highslide-with-html.packed.js"></script>
	<link type="text/css" href="/catalog/view/javascript/checkout_tabs/checkout_tabs.css" rel="stylesheet">


 	<?php if(isset($_GET['route']) AND strpos($_GET['route'],'checkout/checkout') !== false){ ?>
		<script type="text/javascript" src="<?php echo $CDN;?>catalog/view/javascript/checkout_tabs/checkout_tabs.js"></script>
		<script type="text/javascript" src="<?php echo $CDN;?>catalog/view/javascript/checkout_tabs/checkout_table.js"></script>
		<script type="text/javascript" src="<?php echo $CDN;?>catalog/view/javascript/fancybox2/lib/jquery.mousewheel-3.0.6.pack.js"></script>
		<link rel="stylesheet" href="<?php echo $CDN;?>catalog/view/javascript/fancybox2/source/jquery.fancybox.css?v=2.1.5" type="text/css" media="screen" />
		<script type="text/javascript" src="<?php echo $CDN;?>catalog/view/javascript/fancybox2/source/jquery.fancybox.pack.js?v=2.1.5"></script>
		<link rel="stylesheet" href="<?php echo $CDN;?>catalog/view/javascript/fancybox2/source/helpers/jquery.fancybox-buttons.css?v=1.0.5" type="text/css" media="screen" />
		<script type="text/javascript" src="<?php echo $CDN;?>catalog/view/javascript/fancybox2/source/helpers/jquery.fancybox-buttons.js?v=1.0.5"></script>
		<script type="text/javascript" src="<?php echo $CDN;?>catalog/view/javascript/fancybox2/source/helpers/jquery.fancybox-media.js?v=1.0.6"></script>
		<link rel="stylesheet" href="<?php echo $CDN;?>catalog/view/javascript/fancybox2/source/helpers/jquery.fancybox-thumbs.css?v=1.0.7" type="text/css" media="screen" />
		<script type="text/javascript" src="<?php echo $CDN;?>catalog/view/javascript/fancybox2/source/helpers/jquery.fancybox-thumbs.js?v=1.0.7"></script>
	<?php } ?>	
	<!--script type="text/javascript" src="<?php echo $CDN; ?>catalog/view/theme/4WB/js/ui.js"></script-->

	
	<script type="text/javascript" src="<?php echo $CDN;?>catalog/view/javascript/emulate.js"></script>
	

<style>

/* =================================== Меню side-menu.css ============================================ */
.pure-img-responsive{max-width:100%;height:auto}
.img_link{padding-right:0!important}
.nav.active > div p{display:block}
.menu-link{position:absolute;display:block;top:0;left:0;font-size:10px;z-index:10;width:2.5em;padding:4.2em 3em 3.7em;box-sizing:content-box;z-index:1003;height:100%}
.menu-link ins{border:1px solid #dcdcdc;text-decoration:none;background:#f53700;position:absolute;left:0;top:0;width:8.5em;height:8.7em}
.menu-link.active{height:auto}
.menu-link span{position:relative;display:block}
.menu-link span,.menu-link span:before,.menu-link span:after{background-color:#fff;width:100%;height:.2em}
.menu-link span:before,.menu-link span:after{position:absolute;margin-top:-.6em;content:" "}
.menu-link span:after{margin-top:.6em}
@media (min-width: 1180px) {
#layout{display:table;left:0;z-index:1}
.nav ul li ul{left:276px;z-index:1003}
.menu-link{position:absolute;left:0;display:none;top:-87px;box-sizing:content-box;pointer-events:none;cursor:default;height:auto;background:#363030}
.nav > div p{display:block;font-family:'SegoeUISemiBold'}
#layout.active .menu-link{left:280px}
#main{padding-left:0;min-width:780px}.sale-section{overflow:hidden;width:860px}
}
@media (max-width: 1180px) {#layout.active{position:relative;left:0}.menu-link ins{height:7.4em}}
@media (min-width: 1180px) {#layout.active #menu{left:0;width:80px;position:absolute;margin-left:0}#layout.active .menu-link{left:15px}}
@media (max-width: 700px) {.nav ul li ul li a span.about_menu{display:none!important}}
@media (max-width: 650px) {.menu-link{padding:35px 30px;height:auto}/*.nav ul li ul li a span{color:#fff}*/}
/* =================================== Меню side-menu.css ============================================ */

.sort form * {
    box-sizing: border-box;
}
.sort-form {
    padding-bottom: 15px;
	padding-top: 20px;
}
section.sort .btn_reset {
    margin-top: -1px;
    border-radius: 25px;
    background: #f0f4fb;
    text-align: center;
    border: 1px solid #bbbbbb;
    color: #202020;
    text-transform: uppercase;
    font: 0.8em/1em 'SegoeUIRegular' !important;
    height: 41px;
    width: 4%;
    cursor: pointer;
}
section.sort .btn_reset:hover {
    background: #f53700;
    border: 1px #f53700 solid;
    color: #fff;
}

section.sort input.btn_active[type="submit"] {
    margin-top: -1px;
    border-radius: 25px;
    background: #f0f4fb;
    text-align: center;
    border: 1px solid #bbbbbb;
    color: #202020;
    text-transform: uppercase;
    font: 0.8em/1em 'SegoeUIRegular' !important;
    height: 41px;
    width: 18%;
    cursor: pointer;
}
section.sort input.btn_active[type="submit"]:hover {
    background: #f53700;
    border: 1px #f53700 solid;
    color: #fff;
}
.sort .jq-selectbox.dropdown.opened {
    z-index: 300 !important;
}
@media (max-width: 1200px) {
    .sort form {
        display: block;
    }
    .sort .jq-selectbox {
        width: 45%;
        margin-bottom: 10px;
    }
    .sort .jq-selectbox__select {
        width: 100%;
    }
    section.sort input.btn_active[type="submit"] {
        width: 37%;
    }
    section.sort .btn_reset {
        width: 7%;
    }
}

@media (max-width: 1020px) {
    .sort .sort-form { margin-left: 15px; margin-right: 15px; }
}

@media (max-width: 900px) {
    .sort .sort-form { margin-left: 15px; margin-right: 15px; }
}

@media (max-width: 899px) {
    .sort .jq-selectbox {}
    section.sort input.btn_active[type="submit"] {
        width: 80%;
    }
    section.sort .btn_reset {
        width: 18%;
    }
}
@media (max-width: 650px) {
    .sort .sort-form {
        margin-left: 10%;
        margin-right: 10%;
        width: 80%;
    }
/*    .menu-link {
        display: none;
    }
    .menu-link-mob {
        top: 75px;
        position: absolute;
        left: 0;
        display: block;
    }*/
}

<?php //$is_authorized_system_user = 1; ?>

<?php if(defined('CONTACT_MENU') AND CONTACT_MENU == true){ ?>
	
<?php }elseif(isset($is_authorized_system_user) && $is_authorized_system_user){ ?>

	@media (max-width: 899px) {
		.menu-link {
			top: -334px !important;
		}
		#layout.active #menu {
			top: -347px !important;
		}

	}
	@media (max-width: 560px) {
		#layout.active #menu {
			top: -381px !important;
		}
	}
	@media (max-width: 440px) {
		.menu-link {
			top: -408px !important;
		}
		#layout.active #menu {
			top: -455px !important;
		}
	}
	@media (max-width: 658px) {
		.catalog-all{margin-top: 120px;}
	}
<?php } else { ?>

	@media (max-width: 650px) {
		#layout.active #menu {
			top: -159px !important;
		}
	}
	@media (max-width: 560px) {
		#layout.active #menu {
			top: -193px !important;
		}
	}
	@media (max-width: 440px) {
		#layout.active #menu {
			top: -267px !important;
		}
	}
	/* sort form END */

<?php } ?>

	@media (max-width: 1180px) {
		#layout #main {
			min-width: calc(100% - 90px);
		}
	}
	@media (max-width: 899px) {
		#layout #main {
			min-width: 100%;
			padding-left: 0;
		}
		
	}
    
</style>

<?php if(defined('CONTACT_MENU') AND CONTACT_MENU == true){ ?>

	<style>
		@media (max-width: 600px){
			.menu-link {
				margin-top: -123px; 
			}
		}
		
	</style>

<?php }else{ ?>
	<style>
		#menu{top: 15px;}
		@media (max-width: 600px){
			#menu {top: 76px;}
		}
		@media (max-width: 899px) {
		.menu-link {
			top: -74px !important;
		}
		#layout.active #menu {
			top: -88px !important;
		}

	}
	</style>
<?php } ?>


</head>

<body>
<header class="header">
     
    <div class="overlay"></div>
       <?php if(isset($extra_tags)){ ?>
              <?php foreach($extra_tags as $extra_tag) {?>
                     <meta <?php echo ($extra_tag['name']) ? 'name="' . $extra_tag['name'] . '" ' : ''; ?><?php echo (!in_array($extra_tag['property'], array("noprop", "noprop1", "noprop2", "noprop3", "noprop4"))) ? 'property="' . $extra_tag['property'] . '" ' : ''; ?> content="<?php echo addslashes($extra_tag['content']); ?>" />
              <?php } ?>
       <?php } ?>
                   
    <div class="header_wrapper">
        <?php if ($logo) { ?>
            <a href="/" class="main_logo">
            <img src="<?php echo $logo; ?>" title="<?php echo $name; ?>" alt="<?php echo $name; ?>" class="logo"></a>
        <?php } ?>
        <ul>
            <li><a href="javascript:" class="search modalLink_new"></a></li>
            <li onclick="document.location = '<?php echo $checkout; ?>&mode=cart'">
                <?php echo $cart; ?>
             </li>
        </ul>
        <?php if(defined('USA_MODE') AND USA_MODE == true){ ?>
        
        <?php }else{ ?>
            <div class="phones"><ins>Пн-вс:<br />10:00-23:00</ins>
                <?php $tel_find = array(' ','(',')','-'); $tel_rep = array('','','','');
                        $tel_tmp = explode(') ', $contacts_array['Localphone']);
                ?>
                <a href="tel:<?php echo str_replace($tel_find, $tel_rep, $contacts_array['Localphone']); ?>" class="header_phone_href"><?php echo str_replace('+7','8',$tel_tmp[0]); ?>) <span class="red"><?php echo str_replace('-', ' ', $tel_tmp[1]); ?></span></a>
                <br>
                <a href="tel:88005550276">8 (800) <span class="red">555 02 76</span></a> <span> (бесплатно по России)</span>
            </div>
        <?php } ?>
    </div>
    <!--link rel="stylesheet" type="text/css" href="catalog/view/theme/4WB/css/stylesheet.css" /-->
 </header>


        <?php if(defined('CONTACT_MENU') AND CONTACT_MENU == true){ ?>
      		<section class="menu">
				<div>
						<ul>
							<?php if(isset($_SESSION['sub_domain']) AND isset($_SESSION['sub_domain']['Preff']) AND $_SESSION['sub_domain']['Preff'] != ''){ ?>
								<li><a href="/dostavka" class="moscow contact_city"><?php echo $contacts_array['CityContactLable']; ?></a></li>
							<?php }else{ ?>
								<li><a href="<?php echo $contacts_array['CityDostavkaUrl']; ?>" class="moscow contact_city"><?php echo $contacts_array['CityContactLable']; ?></a></li>
							<?php } ?>
							
							<li><a href="<?php echo $main_www;?>/dostavka-po-rossii.html" class="russia">Доставка по России</a></li>
					  
							<li><a href="/oplata.html" class="pay">Способы оплаты</a></li>
							
							<li id="menu_hide" style="width: 150px;">&nbsp;</li>
							
							<?php if(isset($_SESSION['sub_domain']) AND isset($_SESSION['sub_domain']['Preff']) AND $_SESSION['sub_domain']['Preff'] != ''){ ?>
								<li class="contacts"><a href="/contact" class="contacts">Контакты</a></li>
							<?php }else{ ?>
								<li class="contacts"><a href="<?php echo $contacts_array['CityContactUrl']; ?>" class="contacts">Контакты</a></li>
							<?php } ?>
							<li class="company"><a href="<?php echo $main_www;?>/about" class="company">О компании</a></li>
				
						</ul>
				</div>
			</section>			
	    <?php } ?>



    <section class="sort">
<?php
/*
header("Content-Type: text/html; charset=UTF-8");
echo '<pre>'; print_r(var_dump( $marks  ));
die();
*/
?>
<!--======Выбор марки и модели================================================================================================================================ -->
        <?php if((isset($is_authorized_system_user) && $is_authorized_system_user) AND $is_mmg_view){ ?>
            <?php if(isset($_GET['_route_']) OR 1){ ?>
            
                <form action="" style="z-index:1000;display:none;" class="sort-form"> 
                    <!-- тут храним данные о авто клиента. Если они уже есть в куках - подгрузим себе для наглядности -->
                    <input type="hidden" id="selected_mark" value="<?php echo isset($_COOKIE['selected_mark']) ? $_COOKIE['selected_mark'] : ''; ?>">
                    <input type="hidden" id="selected_model" value="<?php echo isset($_COOKIE['selected_model']) ? $_COOKIE['selected_model'] : ''; ?>">
                    <input type="hidden" id="selected_year" value="<?php echo isset($_COOKIE['selected_year']) ? $_COOKIE['selected_year'] : ''; ?>">
                    <input type="hidden" id="selected_generation" value="<?php echo isset($_COOKIE['selected_generation']) ? $_COOKIE['selected_generation'] : ''; ?>">
                    
                    <select class="active select_mark">
                        <option value="0">Марка</option>
                        <?php if(isset($marks) AND count($marks) > 0){ ?>
                            <?php foreach($marks as $mark){ ?>
                                <?php if(isset($_COOKIE['selected_mark']) AND $_COOKIE['selected_mark'] > 0 AND $_COOKIE['selected_mark'] == $mark['id_car_mark']){ ?>
                                    <option value="<?php echo $mark['id_car_mark'];?>" selected><?php echo $mark['name'];?></option>
                                <?php }else{ ?>
                                    <option value="<?php echo $mark['id_car_mark'];?>"><?php echo $mark['name'];?></option>
                                <?php } ?>
                            <?php } ?>
                        <?php } ?>
                    </select>
                    <select class="select_model" id="select_model">
                        <?php if(isset($models) AND count($models) > 0){ ?>
                            <?php foreach($models as $model){ ?>
                                <?php if(isset($_COOKIE['selected_model']) AND $_COOKIE['selected_model'] > 0 AND $_COOKIE['selected_model'] == $model['id_car_model']){ ?>
                                    <option value="<?php echo $model['id_car_model'];?>" selected><?php echo $model['name'];?></option>
                                <?php }else{ ?>
                                    <option value="<?php echo $model['id_car_model'];?>"><?php echo $model['name'];?></option>
                                <?php } ?>
                            <?php } ?>
                        <?php }else{ ?>
                            <option class="error">Модель</option>
                            <option class="error">Выберите модель</option>
                        <?php } ?>
                    </select>
             
                    <select class="select_year" id="select_year">
                          <?php if(isset($years) AND count($years) > 0){ ?>
                            <?php foreach($years as $year){ ?>
                                <?php if(isset($_COOKIE['selected_year']) AND $_COOKIE['selected_year'] > 0 AND $_COOKIE['selected_year'] == $year){ ?>
                                    <option value="<?php echo $year;?>" selected><?php echo $year;?></option>
                                <?php }else{ ?>
                                    <option value="<?php echo $year;?>"><?php echo $year;?></option>
                                <?php } ?>
                            <?php } ?>
                        <?php }else{ ?>
                            <option class="error">Год выпуска</option>
                            <option class="error">Выберите модель</option>
                        <?php } ?>
                    </select>
                    <select class="select_generation" id="select_generation">
                          <?php if(isset($generations) AND count($generations) > 0){ ?>
							<option class="error">Выберите поколение</option>
                            <?php foreach($generations as $generation){ ?>
                                <?php if(isset($_COOKIE['selected_generation']) AND $_COOKIE['selected_generation'] > 0 AND $_COOKIE['selected_generation'] == $generation['id_car_generation']){ ?>
                                    <option value="<?php echo $generation['id_car_generation'];?>" selected><?php echo $generation['name'];?></option>
                                <?php }else{ ?>
                                    <option value="<?php echo $generation['id_car_generation'];?>"><?php echo $generation['name'];?></option>
                                <?php } ?>
                            <?php } ?>
                        <?php }else{ ?>
                            <option class="error">Поколение</option>
                            <option class="error">Выберите поколение</option>
                        <?php } ?>
                    </select>
                    <input type="button" title="Сброс" class="btn_reset" value="&#10540;" <?php echo isset($_COOKIE['selected_mark']) ? '': 'style="display:none;"';?>>
                    <input type="submit" value="Подобрать товары" class="btn_active" />
                    <input type="hidden" value="true" name="mmgfilter" />
                </form>
                
                <script>
                    $(document).ready(function(){
                        setTimeout(function(){
                            $(".sort-form").show(500);
                        }, 500);    
                    });
                    
                    $(".btn_reset").click(function(){
                        
                        
                        
                        $("#selected_mark").val("");
                        $("#selected_model").val("");
                        $("#selected_year").val("");
						$("#selected_generation").val("");
                        
                        dellcookie('selected_mark');
                        dellcookie('selected_model');
                        dellcookie('selected_mark_keyword');
                        dellcookie('selected_model_keyword');
                        dellcookie('selected_year');
						dellcookie('selected_generation');
						dellcookie('selected_generation_keyword');
						dellcookie('mmg_url');
            
                        setTimeout(function(){
                            var redir = "/<?php echo isset($_GET['_route_']) ? $_GET['_route_'] : '';?>";
                            redir = redir.replace("<?php echo isset($_COOKIE['last_mark_filter']) ? $_COOKIE['last_mark_filter'] : '';?>","");
                            location.href = '/';//redir;
                            //location.reload();
                        },500);
                    });
                </script>
            <?php } ?>
        <?php } ?>
<!-- ================================ -->

					<?php if(!isset($is_authorized_system_user) || (isset($is_authorized_system_user) && !$is_authorized_system_user) || !$is_mmg_view){ ?>
                     <div class="search_wrapper">
                            <div class="search_cont">
                                   <div class="search_input">
                                    <div class="form-search">
                                        <input autocomplete="off" size="10" class="form-search-input" title="поиск" type="text">
                                        <div class="form-search-btn-clear">×</div>
                                        <a class="form-search-button"></a>
                                    </div>
										<div class="zgin-zaraza">
											
											<?php if(strpos($_SERVER['SERVER_NAME'],'4wheeledbeast') === false){ ?>
											

												<script>
												  (function() {
													var cx = '016960351563808739010:lwdjsa3egto';
													var gcse = document.createElement('script');
													gcse.type = 'text/javascript';
													gcse.async = true;
													gcse.src = 'https://cse.google.com/cse.js?cx=' + cx;
													var s = document.getElementsByTagName('script')[0];
													s.parentNode.insertBefore(gcse, s);
												  })();
												</script>
												<gcse:search></gcse:search>
											
											<?php }else{ ?>
									
												<script>
													(function() {
													  var cx = '016960351563808739010:v44c9qgygby';
													  var gcse = document.createElement('script');
													  gcse.type = 'text/javascript';
													  gcse.async = true;
													  gcse.src = 'https://cse.google.com/cse.js?cx=' + cx;
													  var s = document.getElementsByTagName('script')[0];
													  s.parentNode.insertBefore(gcse, s);
													})();
												  </script>
												  <gcse:search></gcse:search>
											
											<?php } ?>
											
										</div>
                                   </div>
                            </div>
                     </div>
					<?php } ?>
</section>
	
                                   
<script type="text/javascript">
    var show_velsof_xtrapopup = "1";
</script>

    <div class="modal-popup"  style="top: 100px; left: 52px; display: none; position: fixed;z-index: 999999;overflow-y: auto;max-height: 80%;">
        <a class="closeBtn" href="#" style="right: 10px;"></a>
        <p><?php echo $text_shopping_cart;?></p>
        <div class="basket-small modal-popup-form" id="velsof-popup-dialog">
           
        </div>
    </div>


<?php if(isset($main_banner) AND $main_banner AND is_array($main_banner)){ ?>
	<?php if(defined('USA_MODE') AND USA_MODE == true){ ?>
			
	<?php }else{ ?>
		<section class="main_banner">
			<div style="margin: 0px auto; max-width: 1200px; height: <?php echo $main_banner['height']; ?>px;">
	
				<?php echo htmlspecialchars_decode($main_banner['text'], ENT_QUOTES); ?>
	
			</div>
		</section>
		<script>
			$(document).ready(function(){
				console.log('menu '+<?php echo $main_banner['height']; ?>);
				$('#menuLink ins').css('top','-<?php echo (int)$main_banner['height']+20; ?>px');
				$('#menuLink span').css('top','-<?php echo (int)$main_banner['height']+20; ?>px');
			});
		</script>

	<?php } ?>
<?php } ?>

