<?php
class ControllerCheckoutCheckout extends Controller {
	public function index() {
		// Validate cart has products and has stock.
		if ((!$this->cart->hasProducts() && empty($this->session->data['vouchers'])) || (!$this->cart->hasStock() && !$this->config->get('config_stock_checkout'))) {
			
			$this->response->redirect($this->url->link('checkout/cart'));
		}

		$data['language_href'] = $this->session->data['language_href'];
		
		$this->load->model('checkout/delivery');
		
		// Validate minimum quantity requirements.
		$products = $this->cart->getProducts();

		$data['language_href'] = $this->session->data['language_href'];
		
		foreach ($products as $product) {
			$product_total = 0;

			foreach ($products as $product_2) {
				if ($product_2['product_id'] == $product['product_id']) {
					$product_total += $product_2['quantity'];
				}
			}

			if ($product['minimum'] > $product_total) {
				$this->response->redirect($this->url->link('checkout/cart'));
			}
		}
		
		
		if(isset($this->session->data['delivery_to_country_id'])){
			$data['delivery_info'] = $this->model_checkout_delivery->getDeliveryInfo((int)$this->session->data['delivery_to_country_id']);
		}

		$this->load->language('checkout/checkout');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->document->addScript('catalog/view/javascript/jquery/datetimepicker/moment.js');
		$this->document->addScript('catalog/view/javascript/jquery/datetimepicker/bootstrap-datetimepicker.min.js');
		$this->document->addStyle('catalog/view/javascript/jquery/datetimepicker/bootstrap-datetimepicker.min.css');

		// Required by klarna
		if ($this->config->get('klarna_account') || $this->config->get('klarna_invoice')) {
			$this->document->addScript('http://cdn.klarna.com/public/kitt/toc/v1.0/js/klarna.terms.min.js');
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/home')
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_cart'),
			'href' => $this->url->link('checkout/cart')
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('checkout/checkout', '', 'SSL')
		);

		$data['heading_title'] = $this->language->get('heading_title');

		$data['text_checkout_option'] = $this->language->get('text_checkout_option');
		$data['text_checkout_account'] = $this->language->get('text_checkout_account');
		$data['text_checkout_payment_address'] = $this->language->get('text_checkout_payment_address');
		$data['text_checkout_shipping_address'] = $this->language->get('text_checkout_shipping_address');
		$data['text_checkout_shipping_method'] = $this->language->get('text_checkout_shipping_method');
		$data['text_checkout_payment_method'] = $this->language->get('text_checkout_payment_method');
		$data['text_checkout_confirm'] = $this->language->get('text_checkout_confirm');

		$data['text_delivery'] = $this->language->get('text_delivery');
	
	$data['text_select_delive_adress'] = $this->language->get('text_select_delive_adress');
	$data['text_cart'] = $this->language->get('text_cart');
	$data['text_order'] = $this->language->get('text_order');
	$data['text_confirmation_order'] = $this->language->get('text_confirmation_order');
	$data['text_delivery_information'] = $this->language->get('text_delivery_information');
	$data['text_summary_information_on_ordering'] = $this->language->get('text_summary_information_on_ordering');
	$data['text_help_is_needed'] = $this->language->get('text_help_is_needed');
	$data['text_write_to_us'] = $this->language->get('text_write_to_us');
	$data['text_faq'] = $this->language->get('text_faq');
	$data['text_send_email'] = $this->language->get('text_send_email');
	$data['text_email'] = $this->language->get('text_email');
	$data['text_email_placeholder'] = $this->language->get('text_email_placeholder');
	$data['text_password'] = $this->language->get('text_password');
	$data['text_password_placeholder'] = $this->language->get('text_password_placeholder');
	$data['text_password_confirm'] = $this->language->get('text_password_confirm');
	$data['text_password_confirm_placeholder'] = $this->language->get('text_password_confirm_placeholder');
	$data['text_name'] = $this->language->get('text_name');
	$data['text_name_placeholder'] = $this->language->get('text_name_placeholder');
	$data['text_last_name'] = $this->language->get('text_last_name');
	$data['text_last_name_placeholder'] = $this->language->get('text_last_name_placeholder');
	$data['text_address1'] = $this->language->get('text_address1');
	$data['text_address1_placeholder'] = $this->language->get('text_address1_placeholder');
	$data['text_address2'] = $this->language->get('text_address2');
	$data['text_address2_placeholder'] = $this->language->get('text_address2_placeholder');
	$data['text_fields_zip'] = $this->language->get('text_fields_zip');
	$data['text_fields_zip_placeholder'] = $this->language->get('text_fields_zip_placeholder');
	$data['text_city'] = $this->language->get('text_city');
	$data['text_city_placeholder'] = $this->language->get('text_city_placeholder');
	$data['text_country'] = $this->language->get('text_country');
	$data['text_fields_phone'] = $this->language->get('text_fields_phone');
	$data['text_fields_phone_placeholder'] = $this->language->get('text_fields_phone_placeholder');
	$data['text_have_questions'] = $this->language->get('text_have_questions');
	$data['text_live_chat'] = $this->language->get('text_live_chat');
	$data['text_email_us'] = $this->language->get('text_email_us');
	$data['text_delivery_info'] = $this->language->get('text_delivery_info');
	$data['text_delivery_edit'] = $this->language->get('text_delivery_edit');
	$data['text_payment_details'] = $this->language->get('text_payment_details');
	$data['text_credit_card_number'] = $this->language->get('text_credit_card_number');
	$data['text_credit_card_number_placeholder'] = $this->language->get('text_credit_card_number_placeholder');
	$data['text_credit_card_expiration'] = $this->language->get('text_credit_card_expiration');
	$data['text_month'] = $this->language->get('text_month');
	$data['text_month_mm'] = $this->language->get('text_month_mm');
	$data['text_january'] = $this->language->get('text_january');
	$data['text_february'] = $this->language->get('text_february');
	$data['text_march'] = $this->language->get('text_march');
	$data['text_april'] = $this->language->get('text_april');
	$data['text_may'] = $this->language->get('text_may');
	$data['text_june'] = $this->language->get('text_june');
	$data['text_july'] = $this->language->get('text_july');
	$data['text_august'] = $this->language->get('text_august');
	$data['text_september'] = $this->language->get('text_september');
	$data['text_october'] = $this->language->get('text_october');
	$data['text_november'] = $this->language->get('text_november');
	$data['text_december'] = $this->language->get('text_december');
	$data['text_year'] = $this->language->get('text_year');
	$data['text_year_yyyy'] = $this->language->get('text_year_yyyy');
	$data['text_credit_card_cvn'] = $this->language->get('text_credit_card_cvn');
	$data['text_credit_card_cvn_placeholder'] = $this->language->get('text_credit_card_cvn_placeholder');
	$data['text_what_is_it'] = $this->language->get('text_what_is_it');
	$data['text_what_is_cvn'] = $this->language->get('text_what_is_cvn');
	$data['text_what_is_cvn_text'] = $this->language->get('text_what_is_cvn_text');
	$data['text_credit_card_name'] = $this->language->get('text_credit_card_name');
	$data['text_credit_card_name_placeholder'] = $this->language->get('text_credit_card_name_placeholder');
	$data['text_agreement'] = $this->language->get('text_agreement');
	$data['text_error_name'] = $this->language->get('text_error_name');
	$data['text_error_last_name'] = $this->language->get('text_error_last_name');
	$data['text_error_email'] = $this->language->get('text_error_email');
	$data['text_error_password'] = $this->language->get('text_error_password');
	$data['text_error_password_confirm'] = $this->language->get('text_error_password_confirm');
	$data['text_error_addres1'] = $this->language->get('text_error_addres1');
	$data['text_error_addres2'] = $this->language->get('text_error_addres2');
	$data['text_error_fields_zip'] = $this->language->get('text_error_fields_zip');
	$data['text_error_city'] = $this->language->get('text_error_city');
	$data['text_error_fields_phone'] = $this->language->get('text_error_fields_phone');
	$data['text_error_credit_card_number'] = $this->language->get('text_error_credit_card_number');
	$data['text_error_month'] = $this->language->get('text_error_month');
	$data['text_error_year'] = $this->language->get('text_error_year');
	$data['text_error_credit_card_cvn'] = $this->language->get('text_error_credit_card_cvn');
	$data['text_error_credit_card_name'] = $this->language->get('text_error_credit_card_name');
	$data['text_error_agreement'] = $this->language->get('text_error_agreement');
	$data['text_order_q'] = $this->language->get('text_order_q');
	$data['text_purchase_terms'] = $this->language->get('text_purchase_terms');
	
	$data['text_error_form_valid'] = $this->language->get('text_error_form_valid');

		
		
		if (isset($this->session->data['error'])) {
			$data['error_warning'] = $this->session->data['error'];
			unset($this->session->data['error']);
		} else {
			$data['error_warning'] = '';
		}

		$data['logged'] = $this->customer->isLogged();
		
		$data['customer_info'] = array();
		if($this->customer->isLogged()){
			
			$this->load->model('account/customer');
			$data['customer_info'] = $this->model_account_customer->getCustomer($this->customer->isLogged());
			
			$this->load->model('account/address');
			$data['customer_info']['addresses'] = $this->model_account_address->getAddresses();
			
		}
	
		if (isset($this->session->data['account'])) {
			$data['account'] = $this->session->data['account'];
		} else {
			$data['account'] = '';
		}

		
		// Totals
		$this->load->model('extension/extension');
		
		$total_data = array();
		$total = 0;
		$taxes = $this->cart->getTaxes();

		// Display prices
		if (($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) {
			$sort_order = array();

			$results = $this->model_extension_extension->getExtensions('total');

			foreach ($results as $key => $value) {
				$sort_order[$key] = $this->config->get($value['code'] . '_sort_order');
			}

			array_multisort($sort_order, SORT_ASC, $results);

			foreach ($results as $result) {
				if ($this->config->get($result['code'] . '_status')) {
					$this->load->model('total/' . $result['code']);

					$this->{'model_total_' . $result['code']}->getTotal($total_data, $total, $taxes);
				}
			}

			$sort_order = array();

			foreach ($total_data as $key => $value) {
				$sort_order[$key] = $value['sort_order'];
			}

			array_multisort($sort_order, SORT_ASC, $total_data);
		}

		//echo $this->session->data['country_id'];
		$data['country_code'] = 'RU';
		
		//Подставим страну по выбранному языку
		if($data['language_href'] != '') $data['country_code'] = strtoupper(str_replace('/', '', $data['language_href']));
		
		//Если страна уже выбиралась и есть в сессии - возьмем ее
		if(isset($this->session->data['country_code'])) $data['country_code'] = $this->session->data['country_code'];
	
		if (isset($this->session->data['country_id'])) {
			$country_id = (int)$this->session->data['country_id'];
		}else {
			$country_id = 176;
		}

		
/*
		if(is_numeric($country_id)){
			$delivery_info = $this->model_checkout_delivery->getDeliveryOnCountryId($country_id);
		}else{
			$delivery_info = $this->model_checkout_delivery->getDeliveryOnCountryCode($country_id);	
		}
		
		if(!$delivery_info){
			$delivery_info = $this->model_checkout_delivery->getDeliveryOnCountryId(176);
		}
	
		foreach($delivery_info as $index => $row){
			
			$data['delivery']['realprice'] = number_format($row['price'],2,'.',' ');
			$data['delivery']['real_simbol_left'] = $this->currency->getSymbolLeft($this->session->data['currency']);
			$data['delivery']['real_simbol_right'] = $this->currency->getSymbolRight($this->session->data['currency']);
			
		}
	*/

		$realprice = $data['delivery']['realprice'] = $this->session->data['delivery_realprice'];
		$data['delivery']['real_simbol_left'] = $this->session->data['delivery_real_simbol_left'];
		$data['delivery']['real_simbol_right'] = $this->session->data['delivery_real_simbol_right'];
	
		$realprice = preg_replace('/[^0-9.]/', '', $realprice);
		

	
		$data['totals'] = array();
		
		$data['totals'][] = array(
				'title' => $total_data[0]['title'],
				'text'  => $this->currency->format($total_data[0]['value']),
			);
		$data['totals'][] = array(
				'title' => $data['text_delivery'],
				'text'  => $data['delivery']['real_simbol_left'].$data['delivery']['realprice'].$data['delivery']['real_simbol_right'],
			);
		$data['totals'][] = array(
				'title' => $total_data[1]['title'],
				'text'  => $data['delivery']['real_simbol_left'].((float)$realprice + preg_replace('/[^0-9.]/', '',$this->currency->format($total_data[0]['value']))).$data['delivery']['real_simbol_right'],
			);
/*
		foreach ($total_data as $result) {
			$data['totals'][] = array(
				'title' => $result['title'],
				'text'  => $this->currency->format($result['value']),
			);
		}
*/
		$this->load->model('localisation/country');
		$data['countries'] = $this->model_localisation_country->getCountries();

		$data['shipping_required'] = $this->cart->hasShipping();

		$data['column_left'] = $this->load->controller('common/column_left');
		$data['column_right'] = $this->load->controller('common/column_right');
		$data['content_top'] = $this->load->controller('common/content_top');
		$data['content_bottom'] = $this->load->controller('common/content_bottom');
		$data['footer'] = $this->load->controller('common/footer');
		$data['header'] = $this->load->controller('common/header');

		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/checkout/checkout.tpl')) {
			$this->response->setOutput($this->load->view($this->config->get('config_template') . '/template/checkout/checkout.tpl', $data));
		} else {
			$this->response->setOutput($this->load->view('default/template/checkout/checkout.tpl', $data));
		}
	}

	public function country() {
		$json = array();

		$this->load->model('localisation/country');

		$country_info = $this->model_localisation_country->getCountry($this->request->get['country_id']);

		if ($country_info) {
			$this->load->model('localisation/zone');

			$json = array(
				'country_id'        => $country_info['country_id'],
				'name'              => $country_info['name'],
				'iso_code_2'        => $country_info['iso_code_2'],
				'iso_code_3'        => $country_info['iso_code_3'],
				'address_format'    => $country_info['address_format'],
				'postcode_required' => $country_info['postcode_required'],
				'zone'              => $this->model_localisation_zone->getZonesByCountryId($this->request->get['country_id']),
				'status'            => $country_info['status']
			);
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function customfield() {
		$json = array();

		$this->load->model('account/custom_field');

		// Customer Group
		if (isset($this->request->get['customer_group_id']) && is_array($this->config->get('config_customer_group_display')) && in_array($this->request->get['customer_group_id'], $this->config->get('config_customer_group_display'))) {
			$customer_group_id = $this->request->get['customer_group_id'];
		} else {
			$customer_group_id = $this->config->get('config_customer_group_id');
		}

		$custom_fields = $this->model_account_custom_field->getCustomFields($customer_group_id);

		foreach ($custom_fields as $custom_field) {
			$json[] = array(
				'custom_field_id' => $custom_field['custom_field_id'],
				'required'        => $custom_field['required']
			);
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
	
	public function get_address(){
		
		if(isset($this->request->post['delivery_address'])){
			$address_id = (int)$this->request->post['delivery_address'];
			
			$this->load->model('account/address');
			$addresses = $this->model_account_address->getAddress($address_id);
		
			if(is_array($addresses)){
				$addresses['success'] = 1;
			}else{
				$addresses['success'] = 0;	
			}
			
		
			$this->response->addHeader('Content-Type: application/json');
			$this->response->setOutput(json_encode($addresses));
			
			return true;
		}else{
			
			$addresses['success'] = 0;
			
			$this->response->addHeader('Content-Type: application/json');
			$this->response->setOutput(json_encode($addresses));
			
			return false;	
		}
		
		
	}
}