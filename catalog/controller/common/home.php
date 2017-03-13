<?php
class ControllerCommonHome extends Controller {
	public function index() {
		$this->document->setTitle($this->config->get('config_meta_title'));
		$this->document->setDescription($this->config->get('config_meta_description'));
		$this->document->setKeywords($this->config->get('config_meta_keyword'));

		$this->load->model('catalog/product');
		$product_tags = $this->model_catalog_product->getProductsTags(array(0=>0));
	
		foreach($product_tags as $find => $replace){

			$this->document->setTitle(str_replace($find, $replace, $this->document->getTitle()));
			$this->document->setDescription(str_replace($find, $replace, $this->document->getDescription()));
			$this->document->setKeywords(str_replace($find, $replace, $this->document->getKeywords()));

		}
		

		
		$data['description'] = html_entity_decode($this->config->get('config_comment'), ENT_QUOTES, 'UTF-8');
		if (isset($this->request->get['route'])) {
			$this->document->addLink(HTTP_SERVER, 'canonical');
		}

		$data['column_left'] = $this->load->controller('common/column_left');
		$data['column_right'] = $this->load->controller('common/column_right');
		$data['content_top'] = $this->load->controller('common/content_top');
		$data['content_bottom'] = $this->load->controller('common/content_bottom');
		$data['footer'] = $this->load->controller('common/footer');
		$data['header'] = $this->load->controller('common/header');

		$data['language_href'] = $this->session->data['language_href'];
		
		
		//$data['left_category'] = $left_menu_categorys;
		$data['large_banners'] = $this->model_design_banner->getBannerLargeAll();
		//$data['large_banner'] = $this->model_design_banner->getBannerLarge();
		$data['medium_banners'] = $this->model_design_banner->getBannerMediumAll();
		//$data['medium_banners'] = $this->model_design_banner->getBannerRandom('medium', 3);
		//$data['season_products'] = $this->model_design_banner->getBannerRandom('season_pro', 5);
		//$data['manufacturer_baners'] = $this->model_catalog_manufacturer->getManufacturerBanner();
		
		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/common/home.tpl')) {
			$this->response->setOutput($this->load->view($this->config->get('config_template') . '/template/common/home.tpl', $data));
		} else {
			$this->response->setOutput($this->load->view('default/template/common/home.tpl', $data));
		}
	}
}