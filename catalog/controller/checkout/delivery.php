<?php
class ControllerCheckoutDelivery extends Controller {
	public function index() {
	}

	public function getDeliveryOnCountryId() {
		$json = array();

		$json = $this->getDeliveryOnCountryIdArray();
	
	
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
	
	public function getDeliveryOnCountryIdArray() {
		$json = array();
		
		$this->load->model('localisation/country');
		
		if(isset($this->request->post['country_id'])) {
			$this->session->data['country_id'] = $country_id = (int)$this->request->post['country_id'];
		}elseif(isset($this->session->data['country_id'])) {
			$country_id = (int)$this->session->data['country_id'];
		}else{
			$this->session->data['country_id'] = $country_id = 176;
		}

		if (isset($this->request->post['country_code'])) {
			$this->session->data['country_code'] = $this->request->post['country_code'];
			$country_info = $this->model_localisation_country->getCountryOnCode($this->request->post['country_code']);
			$country_id = $country_info['country_id'];
		}elseif (isset($this->request->get['country_code'])) {
			$this->session->data['country_code'] = $this->request->get['country_code'];
			$country_info = $this->model_localisation_country->getCountryOnCode($this->request->get['country_code']);
			$country_id = $country_info['country_id'];
		}else{
			$country_info = $this->model_localisation_country->getCountry($country_id);
			$this->session->data['country_code'] = $country_info['iso_code_2'];
		}
		
		$this->load->model('checkout/delivery');

		if(is_numeric($country_id)){
			$json = $this->model_checkout_delivery->getDeliveryOnCountryId($country_id);
		}else{
			$json = $this->model_checkout_delivery->getDeliveryOnCountryCode($country_id);	
		}
		
		if(!$json){
			$json = $this->model_checkout_delivery->getDeliveryOnCountryId(176);
		}
		
		$this->session->data['country_id'] = $country_id;
		
		$products = $this->cart->getProducts();
		$total_weight = 0;
		foreach($products as $product){
			$total_weight += (float)$product['weight'] * (int)$product['quantity'];
		}
	
	
		//Проставим стоимость посылки
		foreach($json as $index => $row){
			$price = 0;
			$weight = $total_weight;
			
			//Если забыли указать максимальный вес коробки
			if($row['max_kg'] == 0) $row['max_kg'] = 5;
			
			//Если есть бесплатная доставка от определенной суммы. Сумма в долларах
			if($row['order_sum'] > 0 AND $this->cart->getTotal() > $row['order_sum']){
				
				$price = 0;
				
			//Если есть фиксированная цена - проставляем ее
			}elseif($row['price'] > 0){
				
				//фиксированная цена с учетом максимального веса на одну посылку
				$price = ceil($weight / $row['max_kg']) * $row['price'];
				
			//Если нет - собираем ее	
			}else{
			
				//Если есть цена за +1 кг. Проставим недостающие цены
				if($row['price_p1'] > 0 AND $row['price_10'] > 0){
					
					if(!$row['price_15']) $row['price_15'] = $row['price_10'] + $row['price_p1'];
					if(!$row['price_20']) $row['price_20'] = $row['price_10'] + $row['price_p1'];
					if(!$row['price_25']) $row['price_25'] = $row['price_20'] + $row['price_p1'];
					if(!$row['price_30']) $row['price_30'] = $row['price_20'] + $row['price_p1'];
					if(!$row['price_35']) $row['price_35'] = $row['price_30'] + $row['price_p1'];
					if(!$row['price_40']) $row['price_40'] = $row['price_30'] + $row['price_p1'];
					if(!$row['price_45']) $row['price_45'] = $row['price_40'] + $row['price_p1'];
					if(!$row['price_50']) $row['price_50'] = $row['price_40'] + $row['price_p1'];
					
				}
				
				
				//Тут количество посылок
				for($i=1;$i<=ceil($total_weight / $row['max_kg']);$i++){
					//Вес картона
					$weight = ceil($total_weight / $row['max_kg']) * $i;
					//Если этот картон меньше макс - возьмем остаток
					if($total_weight < $weight) $weight = $total_weight - (ceil($total_weight / $row['max_kg']) * ($i - 1) );
					
					//Идем по циклу с шагом 0.5 кг
					while($weight > 0){
						
						if($weight >= 4.5){
							$price += $row['price_50']; $weight -= 5;
						}elseif($weight >= 4){
							$price += $row['price_45']; $weight -= 4.5;
						}elseif($weight >= 3.5){
							$price += $row['price_40']; $weight -= 5;
						}elseif($weight >= 3){
							$price += $row['price_35']; $weight -= 5;
						}elseif($weight >= 2.5){
							$price += $row['price_30']; $weight -= 5;
						}elseif($weight >= 2){
							$price += $row['price_25']; $weight -= 5;
						}elseif($weight >= 1.5){
							$price += $row['price_20']; $weight -= 5;
						}elseif($weight >= 1){
							$price += $row['price_15']; $weight -= 5;
						}elseif($weight >= 0.5){
							$price += $row['price_10']; $weight -= 1.0;
						}elseif($weight >= 0){
							$price += $row['price_05']; $weight -= 0.5;
						}
				
						//Если у нас еще остался вес на остатке
						if($weight > 0){
							
							//Накинем на каждый килограм по наценке
							while($weight > 0){
								$price += $row['price_p1'];
								$weight--;
							}
							
						}
						
					}
				}
				
				
			}
			//echo $this->session->data['currency'].$this->currency->getSymbolRight($this->session->data['currency']).'<br>';
			$json[$index]['realprice'] = number_format($this->currency->convert($price, $row['code'], $this->session->data['currency']),2,'.',' ');
			$json[$index]['real_simbol_left'] = $this->currency->getSymbolLeft($this->session->data['currency']);
			$json[$index]['real_simbol_right'] = $this->currency->getSymbolRight($this->session->data['currency']);
			
		}
	
		return $json;
	}

	public function setDeliveryData(){
		
		$this->session->data['delivery_realprice'] = $this->request->post['delivery_realprice'];
		$this->session->data['delivery_real_simbol_left'] = $this->request->post['delivery_real_simbol_left'];
		$this->session->data['delivery_real_simbol_right'] = $this->request->post['delivery_real_simbol_right'];
		$this->session->data['delivery_to_country_id'] = $this->request->post['delivery_to_country_id'];
		
	}
	
}
