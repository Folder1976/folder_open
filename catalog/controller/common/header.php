<?php
class ControllerCommonHeader extends Controller {
	public function index() {
		
	    
		// Analytics
		$this->load->model('extension/extension');

		$data['analytics'] = array();

		$analytics = $this->model_extension_extension->getExtensions('analytics');

		
		
		//Социальные сети
		global $adapters;
		$data['adapters'] = $adapters;
		global $social_images;
		$data['social_images'] = $social_images;
		//==========================================	
		
		if (isset($this->request->get['manufacturer_main_category'])) {
				$data['manufacturer_main_category'] = true;
		}
		
		foreach ($analytics as $analytic) {
			if ($this->config->get($analytic['code'] . '_status')) {
				$data['analytics'][] = $this->load->controller('analytics/' . $analytic['code']);
			}
		}

		if ($this->request->server['HTTPS']) {
			$server = $this->config->get('config_ssl');
		} else {
			$server = $this->config->get('config_url');
		}

		if (is_file(DIR_IMAGE . $this->config->get('config_icon'))) {
			$this->document->addLink($server . 'image/' . $this->config->get('config_icon'), 'icon');
		}

		$data['title'] = $this->document->getTitle();
		$data['meta_teg'] = $this->document->getMetaTeg();
		//$data['shop'] = $this->document->getShop();

		if(isset($this->request->get['category_id'])){
			$data['category_id'] = $this->request->get['category_id'];
		}else{
			$data['category_id'] = 0;
		}

		// Menu
		$this->load->model('catalog/category');

		$this->load->model('catalog/product');

		$data['categories'] = $this->document->getCategoryMenu();

		$data['category_path'] =$this->model_catalog_category->getCategoryPath($data['category_id']);
		
		
		
		$data['base'] = $server;
		$data['description'] = $this->document->getDescription();
		$data['keywords'] = $this->document->getKeywords();
		$data['money'] = $this->document->getMoney();
		$data['param'] = $this->document->getParam();
		$data['ip_list'] = $this->document->getIpList();
		$data['links'] = $this->document->getLinks();
		$data['styles'] = $this->document->getStyles();
		$data['scripts'] = $this->document->getScripts();
		$data['lang'] = $this->language->get('code');
		$data['direction'] = $this->language->get('direction');
	
	$data['cart_products_total'] = $this->cart->countProducts();
	$data['language_href'] = $this->session->data['language_href'];
	
		$data['name'] = $this->config->get('config_name');

		if (is_file(DIR_IMAGE . $this->config->get('config_logo'))) {
			$data['logo'] = $server . 'image/' . $this->config->get('config_logo');
		} else {
			$data['logo'] = '';
		}

		$this->load->language('common/header');

		$data['text_home'] = $this->language->get('text_home');

		$data['text_alert_copy'] = $this->language->get('text_alert_copy');
		$data['text_cookies_off_copy'] = $this->language->get('text_cookies_off_copy');
		$data['text_sale'] = $this->language->get('text_sale');
		$data['text_search'] = $this->language->get('text_search');
		$data['text_search_legend'] = $this->language->get('text_search_legend');
		$data['text_search_submit_button'] = $this->language->get('text_search_submit_button');
		$data['text_cart_title'] = $this->language->get('text_cart_title');
		$data['text_cart_qty'] = $this->language->get('text_cart_qty');
		$data['text_login_enter'] = $this->language->get('text_login_enter');
		$data['text_email_required'] = $this->language->get('text_email_required');
		$data['text_pass_required'] = $this->language->get('text_pass_required');
		$data['text_pass'] = $this->language->get('text_pass');
		$data['text_rememberme'] = $this->language->get('text_rememberme');
		$data['text_wanted_spam'] = $this->language->get('text_wanted_spam');
		$data['text_privacy_box_1'] = $this->language->get('text_privacy_box_1');
		$data['text_privacy_box_2'] = $this->language->get('text_privacy_box_2');
		$data['text_enter'] = $this->language->get('text_enter');
		$data['text_enter_to_account'] = $this->language->get('text_enter_to_account');
		$data['text_qtn'] = $this->language->get('text_qtn');
		$data['text_total'] = $this->language->get('text_total');
		$data['text_content_asset'] = $this->language->get('text_content_asset');
		$data['text_register_now'] = $this->language->get('text_register_now');
		$data['text_make_account_now'] = $this->language->get('text_make_account_now');
		$data['text_forgotten_pass'] = $this->language->get('text_forgotten_pass');
		$data['text_next'] = $this->language->get('text_next');
		$data['text_cookie_error'] = $this->language->get('text_cookie_error');

		$data['text_email'] = $this->language->get('text_email');
$data['text_password_reset'] = $this->language->get('text_password_reset');
$data['text_i_remember_password'] = $this->language->get('text_i_remember_password');
$data['text_reestablish'] = $this->language->get('text_reestablish');
$data['text_name'] = $this->language->get('text_name');
$data['text_register_new_buyer'] = $this->language->get('text_register_new_buyer');
$data['text_register_new_wholesale_buyer'] = $this->language->get('text_register_new_wholesale_buyer');
$data['text_sign_up'] = $this->language->get('text_sign_up');
$data['text_cabinet'] = $this->language->get('text_cabinet');
$data['text_enter_in_account'] = $this->language->get('text_enter_in_account');
$data['text_back_to_shopping'] = $this->language->get('text_back_to_shopping');
$data['text_go_back'] = $this->language->get('text_go_back');
$data['text_error_name'] = $this->language->get('text_error_name');
$data['text_error_email'] = $this->language->get('text_error_email');
$data['text_error_password'] = $this->language->get('text_error_password');
$data['text_error_password_confirm'] = $this->language->get('text_error_password_confirm');
$data['text_error_form_valid'] = $this->language->get('text_error_form_valid');
$data['text_cart'] = $this->language->get('text_cart');
$data['text_wishlist'] = $this->language->get('text_wishlist');
$data['text_service_center'] = $this->language->get('text_service_center');
$data[''] = $this->language->get('');
$data[''] = $this->language->get('');

		if (isset($this->session->data['user_id']) AND $this->session->data['user_id']) {
			$data['is_user'] = $this->session->data['user_id'];
		}
		// Wishlist
		if ($this->customer->isLogged()) {
			$this->load->model('account/wishlist');
			$this->load->model('account/customer');
			$this->load->model('catalog/shops');

			$data['text_wishlist'] = sprintf($this->language->get('text_wishlist'), $this->model_account_wishlist->getTotalWishlist());
			
			$data['customer_info'] = $customer_info = $this->model_account_customer->getCustomer($this->customer->isLogged());
		
			if(isset($customer_info['customer_shop_id'])){
				//основные данные по магазину и деньгам
				$data['shop'] = $this->model_catalog_shops->getShop($customer_info['customer_shop_id']);
				$this->document->setShop($data['shop']);
			}
			
		} else {
			$data['text_wishlist'] = sprintf($this->language->get('text_wishlist'), (isset($this->session->data['wishlist']) ? count($this->session->data['wishlist']) : 0));
		}

		$data['text_shopping_cart'] = $this->language->get('text_shopping_cart');
		
		$data['text_logged'] = sprintf($this->language->get('text_logged'), $this->url->link('account/account', '', 'SSL'), $this->customer->getFirstName(), $this->url->link('account/logout', '', 'SSL'));

		$data['text_account'] = $this->language->get('text_account');
		$data['text_register'] = $this->language->get('text_register');
		$data['text_login'] = $this->language->get('text_login');
		$data['text_order'] = $this->language->get('text_order');
		$data['text_transaction'] = $this->language->get('text_transaction');
		$data['text_download'] = $this->language->get('text_download');
		$data['text_logout'] = $this->language->get('text_logout');
		$data['text_checkout'] = $this->language->get('text_checkout');
		$data['text_category'] = $this->language->get('text_category');
		$data['text_all'] = $this->language->get('text_all');

		$data['home'] = $this->url->link('common/home');
		$data['wishlist'] = $this->url->link('account/wishlist', '', 'SSL');
		$data['logged'] = $this->customer->isLogged();
	
		$data['account'] = $this->url->link('account/account', '', 'SSL');
		$data['register'] = $this->url->link('account/register', '', 'SSL');
		$data['login'] = $this->url->link('account/login', '', 'SSL');
		$data['order'] = $this->url->link('account/order', '', 'SSL');
		$data['transaction'] = $this->url->link('account/transaction', '', 'SSL');
		$data['download'] = $this->url->link('account/download', '', 'SSL');
		$data['logout'] = $this->url->link('account/logout', '', 'SSL');
		$data['shopping_cart'] = $this->url->link('checkout/cart');
		$data['checkout'] = $this->url->link('checkout/checkout', '', 'SSL');
		$data['contact'] = $this->url->link('information/contact');
		$data['telephone'] = $this->config->get('config_telephone');

	$data['text_sale'] = $this->language->get('text_sale');
	$data['text_brands'] = $this->language->get('text_brands');

	if(!$this->document->isSale() AND isset($this->request->get['category_id'])){
		unset($data['text_sale']);
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

	
	

		$data['language'] = $this->load->controller('common/language');
		//$data['currency'] = $this->load->controller('common/currency');
		$data['search'] = $this->load->controller('common/search');
		$data['cart'] = $this->load->controller('common/cart');
		$data['cart_array'] = $this->load->controller('common/cart/getArray');
		
		if($this->customer->isLogged()){
			$data['total_viewed_products'] = (int)$this->model_catalog_product->getTotalViewedProducts();
			if($data['total_viewed_products'] > 99){
				$data['total_viewed_products'] = '99';
			}if($data['total_viewed_products'] < 0){
				$data['total_viewed_products'] = '';
			}
			
			$data['total_loved_products'] = (int)$this->model_catalog_product->getTotalLovedProducts();
			if($data['total_loved_products'] > 99){
				$data['total_loved_products'] = '99';
			}elseif($data['total_loved_products'] < 1){
				$data['total_loved_products'] = '';
			}
		}else{
			$data['total_viewed_products'] = $data['total_loved_products'] = 0;
		}
		
		// For page specific css
		if (isset($this->request->get['route'])) {
			if (isset($this->request->get['product_id'])) {
				$class = '-' . $this->request->get['product_id'];
				
				//Это для разметки мета OG
				$this->load->model('catalog/product');
				$data['og'] 			= $this->model_catalog_product->getProduct($this->request->get['product_id']);
				$data['og']['type'] 	= 'product';
				$data['og']['name'] 	= $this->document->getTitle();
				$data['og']['keyword'] 	= $this->request->get['_route_'].'';
				$data['og']['image'] 	= "image/".$data['og']['image'];
				$data['og']['meta_description'] = $data['og']['meta_description'];
				
			}elseif (isset($this->request->get['category_id'])) {
				
				//Это для разметки мета OG
				$this->load->model('catalog/category');
				$class = '';
				$category = $this->model_catalog_category->getCategory($this->request->get['category_id']);
				
				$data['og'] = $category;
				$data['og']['type'] 	= 'product.group';
				$data['og']['name'] 	= $this->document->getTitle();
				$data['og']['keyword'] 	= $this->request->get['_route_'];
				$data['og']['image'] 	= "image/placeholder_210.png";
				$data['og']['meta_description'] = str_replace('@block_name_rod@', $category['name'], $category['meta_description']);;
				
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

		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/common/header.tpl')) {
			return $this->load->view($this->config->get('config_template') . '/template/common/header.tpl', $data);
		} else {
			return $this->load->view('default/template/common/header.tpl', $data);
		}
	}
}
