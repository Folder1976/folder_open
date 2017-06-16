<?php
class ControllerCommonHeader extends Controller {
	
	
	public function index() {

		$cache_key = MEMCACHE_PREF.'header_';//.$data['is_authorized_system_user'].'_'.md5($data['cityName']);
	
		//Залогинен ли пользователь	
		$this->load->model('module/innersystem');
		$data['is_authorized_system_user'] = $this->model_module_innersystem->isAuthorizedUserHere();
			
		//Только для Картера не корзинных доменов
		if($_SERVER['HTTPS'] AND $_SERVER['HTTP_HOST'] == "alta-karter.ru"){
			if(isset($_GET['route']) AND (strpos($_GET['route'],'checkout/') !== false OR strpos($_GET['route'],'account/') !== false)){
			}else{
				header('HTTP/1.1 301 Moved Permanently');
				header("Location: http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']."", true);
				return true;
			}
		}
		
		//Если это поддомен - создадим указатель на Родительский сат
		$data['main_www'] = '//'.$_SERVER['HTTP_HOST'];
		if(isset($this->session->data['sub_domain']) AND
			isset($this->session->data['sub_domain']['Preff']) AND
				$this->session->data['sub_domain']['Preff'] != ''){
			
			$data['main_www'] = str_replace($this->session->data['sub_domain']['Preff'].'.','',$data['main_www']);
			
		}
		
		//$this->load->model('module/geolocation');
		//$this->load->model('module/geocontacts');
					
		$data['is_secure'] = $this->request->isSecure();
		$data['imageUrlDomain'] = $data['is_secure'] ? IMAGE_HTTPS_PREF : IMAGE_HTTP_PREF;
		$imageUrlDomain = $data['imageUrlDomain'];
	
		$this->load->model('setting/server');
		$data = array_merge($data, $this->model_setting_server->getCdnServer());

	
		$data['contacts_array'] = $this->session->data['contacts_array'];
		$data['cityName'] = $cityName = $this->session->data['cityName'];
	
		$this->document->setContactArray($data['contacts_array'] );
		
		//================================ shopping_cart_popup_0.xml
			//Убрать после перехода на новый дизайн
			$this->document->addScript($data['CDN']."catalog/view/javascript/shoppingcartpopupxtra/shoppingcartpopupxtra.js");
			$this->document->addScript($data['CDN']."catalog/view/javascript/shoppingcartpopupxtra/jquery.gritter.min.js");
			$this->document->addStyle($data['CDN']."catalog/view/theme/default/stylesheet/shoppingcartpopupxtra/shoppingcartpopupxtra.css");
			$this->document->addStyle($data['CDN']."catalog/view/theme/default/stylesheet/shoppingcartpopupxtra/jquery.gritter.css");
			
	
			$this->load->model("setting/setting");
			$result = $this->model_setting_setting->getSetting("cart_popup", $this->config->get("config_store_id"));
			$is_mmg_view = $this->model_setting_setting->getSetting('config',$this->config->get("config_store_id"));
			
			$data['is_mmg_view'] = false;
			if(isset($is_mmg_view['config_mmg']) AND $is_mmg_view['config_mmg']){
				$data['is_mmg_view'] = true;
			}
	
	
			$result = Array(
							'shoppingcartpopupxtra' => Array(
									'general' => Array(
											'enable' => 1,
											'enable_mobile' => 0,
											'show' => 'none',
											'banner_count' => 0
										),

									'cart' => Array(
											'need_help' => 0,
											'email' => "",
											'enable_coupon' => 0,
											'enable_voucher' => 0
										)

								)
			);
			
		if(isset($result["shoppingcartpopupxtra"]["general"]["enable"])) {
			$popup_enable = $result["shoppingcartpopupxtra"]["general"]["enable"];
		} else {
			 $popup_enable = 0;
		}

		if (!class_exists("Mobile_Detect_Velocity")) {
			require_once(DIR_SYSTEM."helper/Mobile_Detect_Velocity.php");
		}
		$device_detect_velsof = new Mobile_Detect_Velocity();
		if(isset($result["shoppingcartpopupxtra"]["general"]["enable_mobile"])) {
			$enable_mobile = $result["shoppingcartpopupxtra"]["general"]["enable_mobile"];
		} else {
			 $enable_mobile = 0;
		}
	   
		if($device_detect_velsof->isMobile() || $device_detect_velsof->isTablet()) {
			if($enable_mobile == 0) {
				$data["show_velsof_xtrapopup"] = 0;
			} else {
				$data["show_velsof_xtrapopup"] = 1;
			}
		} else {
			if($popup_enable == 1) {
				$data["show_velsof_xtrapopup"] = 1;
			} else {
				$data["show_velsof_xtrapopup"] = 0;
			}
		}
		//================================ end shopping_cart_popup_0.xml
		
		//Редирект для некоторых страниц на HTTPS
		//Только для МОСКВЫ и Картера
		
		if(strpos($_SERVER['HTTP_HOST'],'alta-karter') !== false AND isset($this->session->data['sub_domain']) AND $this->session->data['sub_domain']['Domain'] == 'moscow'){
			if (defined('REDIRECT_TO_SECURE_URL') && REDIRECT_TO_SECURE_URL === true) {
				//$this->load->controller('module/redirect_to_secure_url');
			}
		}
		
		// Load auto login controller
		$this->load->controller('account/login_auto');

		// Load user labels controller
		$this->load->controller('module/userlabels');

		// Load cart controller
		$this->load->controller('module/cart');

		//Копия скпипта у нас
		$this->document->addScript($data['CDN'].'catalog/view/javascript/jquery.super-smart-menu-serjopepper.min.js');
		
		$data['title'] = str_replace('"', "'",htmlspecialchars_decode($this->document->getTitle(), ENT_QUOTES));
	
		$currentGMDate = gmdate('D, d M Y H:i:s ') . 'GMT';
		$this->response->addHeader('Last-Modified: ' . $currentGMDate);
		$this->response->addHeader('Expires: ' . $currentGMDate);

		if ($this->request->server['HTTPS']) {
			$server = $this->config->get('config_ssl');
		} else {
			$server = $this->config->get('config_url');
		}

		$sub_domain = isset($this->session->data['sub_domain']['Domain']) ? $this->session->data['sub_domain']['Domain'] : '';
		$data['base'] = $server = str_replace('domain',$sub_domain,$server);
		$data['home'] = $server;
		$data['description'] = str_replace('"', "'",htmlspecialchars_decode($this->document->getDescription(), ENT_QUOTES));
		$data['keywords'] = $this->document->getKeywords();
		$data['links'] = $this->document->getLinks();
		$data['styles'] = $this->document->getStyles();
		$data['scripts'] = $this->document->getScripts();
		
		//$data['extra_tags'] = $this->document->getExtraTags();
		$data['lang'] = $this->language->get('code');
		$data['direction'] = $this->language->get('direction');
		$data['google_analytics'] = html_entity_decode($this->config->get('config_google_analytics'), ENT_QUOTES, 'UTF-8');
		$data['name'] = $this->document->updateTags($this->config->get('config_name'));

		if (is_file(DIR_IMAGE . $this->config->get('config_icon'))) {
			$data['icon'] = $imageUrlDomain . 'image/' . $this->config->get('config_icon');
		} else {
			$data['icon'] = '';
		}

		$data['logo'] = '/image/catalog/logo.png';
	
		//Поднимаем Кеш ****************************************************************************************
		$cache_data = $this->cache->get($cache_key.'language');
		if($cache_data){
		   $data = array_merge($data, $cache_data);
		}else{
		//Поднимаем Кеш ****************************************************************************************
	
			$this->load->language('common/header');
			//==================================Tamplate_Strings_Load_From_Language_Files.xml
			$labels = array(
						'text_system_db_is_not_available',
					);
			foreach ($labels as $item) {
				$data1[$item] = $this->language->get($item);
			}
			//==================================end Tamplate_Strings_Load_From_Language_Files.xml
			
			$data1['text_home'] = $this->language->get('text_home');
			$data1['text_wishlist'] = sprintf($this->language->get('text_wishlist'), (isset($this->session->data['wishlist']) ? count($this->session->data['wishlist']) : 0));
			$data1['text_shopping_cart'] = $this->language->get('text_shopping_cart');
			$data1['text_logged'] = sprintf($this->language->get('text_logged'), $this->url->link('account/account', '', 'SSL'), $this->customer->getFirstName(), $this->url->link('account/logout', '', 'SSL'));
	
			$data1['text_account'] = $this->language->get('text_account');
			$data1['text_register'] = $this->language->get('text_register');
			$data1['text_login'] = $this->language->get('text_login');
			$data1['text_order'] = $this->language->get('text_order');
			$data1['text_transaction'] = $this->language->get('text_transaction');
			$data1['text_download'] = $this->language->get('text_download');
			$data1['text_logout'] = $this->language->get('text_logout');
			$data1['text_checkout'] = $this->language->get('text_checkout');
			$data1['text_category'] = $this->language->get('text_category');
			$data1['text_all'] = $this->language->get('text_all');
			$data1['text_javascript_disabled'] = $this->language->get('text_javascript_disabled');
			$data1['text_old_browser'] = $this->language->get('text_old_browser');
			
			$data1['wishlist'] = $this->url->link('account/wishlist', '', 'SSL');
			$data1['logged'] = $this->customer->isLogged();
			$data1['account'] = $this->url->link('account/account', '', 'SSL');
			$data1['register'] = $this->url->link('account/register', '', 'SSL');
			$data1['login'] = $this->url->link('account/login', '', 'SSL');
			$data1['order'] = $this->url->link('account/order', '', 'SSL');
			$data1['transaction'] = $this->url->link('account/transaction', '', 'SSL');
			$data1['download'] = $this->url->link('account/download', '', 'SSL');
			$data1['logout'] = $this->url->link('account/logout', '', 'SSL');
			$data1['shopping_cart'] = $this->url->link('checkout/cart');
			$data1['checkout'] = $this->url->link('checkout/checkout', '', 'SSL');
			$data1['contact'] = $this->url->link('information/contact');
			$data1['telephone'] = $this->config->get('config_telephone');

			//Кеш ****************************************************************************************
			$this->cache->set($cache_key.'language', $data1, time() + MEMCACHE_TIME);
			$data = array_merge($data, $data1);
			unset($data1);
			//Кеш ****************************************************************************************
		
		}
		
		//Для поддоменов
		if(isset($this->session->data['sub_domain']) AND isset($this->session->data['sub_domain']['Preff']) AND $this->session->data['sub_domain']['Preff'] != ''){
			$data['checkout'] = str_replace('://','://'.$this->session->data['sub_domain']['Preff'].'.', $data['checkout']);
		}
		
		$status = true;

		if (isset($this->request->server['HTTP_USER_AGENT'])) {
			$robots = explode("\n", str_replace(array("\r\n", "\r"), "\n", trim($this->config->get('config_robots'))));

			foreach ($robots as $robot) {
				if ($robot && strpos($this->request->server['HTTP_USER_AGENT'], trim($robot)) !== false) {
					$status = false;

					break;
				}
			}
		}

		//$data['language'] = $this->load->controller('common/language');
		//$data['currency'] = $this->load->controller('common/currency');
		//$data['search'] = $this->load->controller('common/search');
		$data['cart'] = $this->load->controller('common/cart');
		
		//Новости и Вики
		//Поднимаем Кеш ****************************************************************************************
		$cache_data = $this->cache->get($cache_key.'blog');
		if($cache_data){
		   $data = array_merge($data, $cache_data);
		}else{
		//Поднимаем Кеш ****************************************************************************************
			$this->language->load('module/news');
			$data1['news_url'] = $this->url->link('news/ncategory');
			$data1['news_name'] = $this->language->get('text_blognews');
		
			$data1['blog_url'] = '/wiki';$this->url->link('blog/home');
			$data1['blog_name'] = $this->language->get('text_blogpage');
		
			if ($this->config->get('ncategory_bnews_top_link')) {
				$this->language->load('module/news');
				$blog_url = $this->url->link('news/ncategory');
				$blog_name = $this->language->get('text_blogpage');
				if (isset($data1['categories']) && count($data1['categories'])) {
					$data1['categories'][] = array(
						'name'     => $blog_name,
						'children' => array(),
						'column'   => 1,
						'href'     => $blog_url
					);
				}
			}
			
			//Кеш ****************************************************************************************
			$this->cache->set($cache_key.'blog', $data1, time() + MEMCACHE_TIME);
			$data = array_merge($data, $data1);
			unset($data1);
			//Кеш ****************************************************************************************

		}
		
		// For page specific css
		if (isset($this->request->get['route'])) {
			if (isset($this->request->get['product_id'])) {
				$class = '-' . $this->request->get['product_id'];
				
				//Это для разметки мета OG
				$this->load->model('catalog/product');
				$data['og'] = $this->model_catalog_product->getProduct($this->request->get['product_id']);
				$data['og']['keyword'] = $this->request->get['_route_'].'';
				$data['og']['type'] = 'product';
				$data['og']['image'] = 'image/'.$data['og']['image'];
				
			} elseif (isset($this->request->get['category_id'])) {
				$class = '-' . $this->request->get['category_id'];
				
				//Это для разметки мета OG
				$this->load->model('catalog/category');
				$category = $data['og'] = $this->model_catalog_category->getCategory($this->request->get['category_id']);
				$data['og']['keyword'] = $this->request->get['_route_'].'';
				$data['og']['type'] = 'product.group';
				$data['og']['name'] = $category['title_h1'];
				
				//Определение картинки для категории
				$data['og']['image']  = $this->model_catalog_category->getCategoryBackgroundImage($this->request->get['category_id']);
				
				
			} elseif (isset($this->request->get['path'])) {
				$class = '-' . $this->request->get['path'];
			} elseif (isset($this->request->get['manufacturer_id'])) {
				$class = '-' . $this->request->get['manufacturer_id'];
			} else {
				$class = '';
			}

			$data['class'] = str_replace('/', '-', $this->request->get['route']) . $class;
		} else {
			$data['class'] = 'common-home';
		}
		
		if(defined('FB_APP_ID')){
			
			$data['fb']['app_id'] = FB_APP_ID;
			
		}
		
		// Main menu
		$store_id = $this->config->get('config_store_id');
		$mmDirName = 'default';

		$this->load->model('design/banner');
		$data['main_banner'] = $this->model_design_banner->getBannerMain();
		
		//Для нового дизайна нам нужен список моделей
		if($this->config->get('config_template') == '4WB'){
				$this->load->model('catalog/cars');
				
				$data['marks'] = $this->model_catalog_cars->getMarks();
				
				//Если у пользователя определена марка - загрузим модель, и года подгрузи - модель же знаем
				if(isset($_COOKIE['selected_mark']) AND $_COOKIE['selected_mark'] > 0){
					$data['models'] = $this->model_catalog_cars->getModels($_COOKIE['selected_mark']);
									
					//Если есть еще и кука модели - соберем года
					if(isset($_COOKIE['selected_model']) AND $_COOKIE['selected_model'] > 0){
						$data['years'] = $this->model_catalog_cars->getMarkModelYears($_COOKIE['selected_mark'],$_COOKIE['selected_model']);
					}
					
					//Если есть еще и кука модели - соберем года
					if(isset($_COOKIE['selected_year']) AND $_COOKIE['selected_year'] > 0){
						$data['generations'] = $this->model_catalog_cars->getCarfitGenerations($_COOKIE['selected_model'],$_COOKIE['selected_year']);
					}
					
				}
				
		}
		
		return $this->load->view($this->config->get('config_template') . '/template/common/header.tpl', $data);
		
	}
	
	public function custom_setcookie(){
		
		$key = $this->request->post['key'];
		$value = $this->request->post['value'];
		
		//Список допустимых кук... чтоб нам чтото левое не подсунули
		$array_cookie = array(
							 'selected_year',
							 'selected_model',
							 'selected_mark',
							 'selected_generation',
							  'selected_generation_keyword',
							 'view_edit'
							 );
		
		$find = array(' ',"'");
		$repl = array('_', '');
		
		if(in_array($key, $array_cookie)){
			
			setcookie($key, $value, time() + 3600 * 48 * 24, '/');
			
			if($key == 'selected_mark'){
				
				//$r = $this->db->query('SELECT MarkCode FROM car_make WHERE MarkID = "'.$value.'" LIMIT 1');
				$r = $this->db->query('SELECT code FROM carfit_car_mark WHERE id_car_mark = "'.$value.'" LIMIT 1');
				if($r->num_rows){
					$r->row['name'] = str_replace($find, $repl,strtolower($r->row['code']));
					setcookie('selected_mark_keyword', $r->row['code'], time() + 3600 * 48 * 24, '/');
					$_COOKIE['selected_mark_keyword'] = $r->row['code'];
				}
				
			}else if($key == 'selected_model'){
				
				//$r = $this->db->query('SELECT ModelCode FROM car_model WHERE ModelID = "'.$value.'" LIMIT 1');
				$r = $this->db->query('SELECT code FROM carfit_car_model WHERE id_car_model = "'.$value.'" LIMIT 1');
				if($r->num_rows){
					
					$r->row['name'] = str_replace($find, $repl,strtolower($r->row['code']));
					setcookie('selected_model_keyword', $r->row['code'], time() + 3600 * 48 * 24, '/');
					$_COOKIE['selected_model_keyword'] = $r->row['code'];
				}
				
			}else if($key == 'selected_generation'){
				
				//$r = $this->db->query('SELECT ModelCode FROM car_model WHERE ModelID = "'.$value.'" LIMIT 1');
				$r = $this->db->query('SELECT code FROM carfit_car_generation WHERE id_car_generation = "'.$value.'" LIMIT 1');
				if($r->num_rows){
					
					$r->row['name'] = str_replace($find, $repl,strtolower($r->row['code']));
					setcookie('selected_generation_keyword', $r->row['code'], time() + 3600 * 48 * 24, '/');
					$_COOKIE['selected_generation_keyword'] = $r->row['code'];
				}
				
			}
			
			
			
		}
		
		
	}

	public function custom_dellcookie(){
		
		$key = $this->request->post['key'];
		
		//Список допустимых кук... чтоб нам чтото левое не подсунули
		$array_cookie = array(
							 'selected_year',
							 'selected_model',
							 'selected_mark',
							 'selected_generation',
							 'selected_generation_keyword',
							 'view_edit',
							 'mmg_url',
							 'selected_mark_keyword',
							 'selected_model_keyword'
							 );
		
		if(in_array($key, $array_cookie)){
			
			setcookie($key, '', time() - 3600 , '/');
			
			unset($_COOKIE[$key]);
			unset($_SESSION[$key]);
		}
		
		
	}

}