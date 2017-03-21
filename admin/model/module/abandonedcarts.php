<?php 
class ModelModuleAbandonedcarts extends Model {
	public function install() {
		$this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "abandonedcarts` (
			`id` INT(11) NOT NULL AUTO_INCREMENT,
			`date_created` DATETIME NULL DEFAULT NULL, 
			`date_modified` DATETIME NULL DEFAULT NULL, 
			`cart` TEXT NULL DEFAULT NULL, 
			`customer_info` TEXT NULL DEFAULT NULL,
			`last_page` VARCHAR(255) NULL DEFAULT NULL,
			`restore_id` VARCHAR(255) NULL DEFAULT NULL,
			`ip` VARCHAR(100) NULL DEFAULT NULL,
			`store_id` TINYINT NOT NULL DEFAULT 0,  
			 PRIMARY KEY (`id`))");
			 
		$this->db->query("UPDATE `" . DB_PREFIX . "modification` SET status=1 WHERE `name` LIKE'%AbandonedCarts by iSenseLabs%'");
		$modifications = $this->load->controller('extension/modification/refresh');
	}
	
	public function uninstall() {
	//	$this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "abandonedcarts`");
	
		$this->db->query("UPDATE `" . DB_PREFIX . "modification` SET status=0 WHERE `name` LIKE'%AbandonedCarts by iSenseLabs%'");
		$modifications = $this->load->controller('extension/modification/refresh');
	}
	
	public function viewAbandonedCarts($page=1, $limit=8, $store=0,$sort="id", $order="DESC") {	
		$this->load->model('sale/customer');
	
		if ($page) {
				$start = ($page - 1) * $limit;
			}
			$query =  $this->db->query("SELECT * FROM `" . DB_PREFIX . "abandonedcarts`
			WHERE `store_id`='".$store."'
			ORDER BY `id` DESC
			LIMIT ".$start.", ".$limit);
			
			$abandonedCarts = array();
			foreach ($query->rows as $row) {
				$row['customer_info'] = json_decode($row['customer_info'], true);
				
				if (isset($row['customer_info']['language'])) {
					$lang_image = $this->getLanguageImage($row['customer_info']['language']);

					$row['customer_info']['lang_image'] = 'view/image/flags/'.$lang_image;
					if (isset($lang_image)) {
						$row['customer_info']['lang_image'] = 'view/image/flags/'.$lang_image;
					}
				}
				$row['customer_info'] = json_encode($row['customer_info']);
				$abandonedCarts[] = $row;
				
			}
		return $abandonedCarts; 
	}
	
	public function getTotalAbandonedCarts($store=0){
		$query = $this->db->query("SELECT COUNT(*) as `count`  FROM `" . DB_PREFIX . "abandonedcarts` WHERE `store_id`=".$store);
		
		return $query->row['count']; 
	}
	
	public function getCartInfo($id) {	
		$query =  $this->db->query("SELECT * FROM `" . DB_PREFIX . "abandonedcarts`
			WHERE `id`=".$id);
		
		return $query->row; 
	}
	
	public function isUniqueCode($randomCode) {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "coupon` WHERE code='".$this->db->escape($randomCode)."'");
				if($query->num_rows == 0) {
					return true;
							} else {
					return false;
				}	
	}
	
	public function sendMail($data = array()) {
		$this->load->model('setting/store');
		$this->load->model('setting/setting');
		$this->load->model('module/abandonedcarts');

		$store_info = $this->model_setting_store->getStore($this->request->get['store_id']);

		if ($store_info) {
			$store_name = $store_info['name'];
		} else {
			$store_name = $this->config->get('config_name');
		}
		
		$mailToUser = new Mail($this->config->get('config_mail'));
		$mailToUser->setTo($data['email']);
		$mailToUser->setFrom($this->config->get('config_email'));
		$mailToUser->setSender($store_name);
		$mailToUser->setSubject(html_entity_decode($data['subject'], ENT_QUOTES, 'UTF-8'));
		$mailToUser->setHtml($data['message']);
		
		$abandonedCartsSettings = $this->model_setting_setting->getSetting('AbandonedCarts', $this->config->get('store_id'));
		if(isset($abandonedCartsSettings['AbandonedCarts']['BCC']) && $abandonedCartsSettings['AbandonedCarts']['BCC'] == 'yes') { 
			$mailToUser->setAbandonedCartsBcc($this->config->get('config_email'));
		}
		
		$mailToUser->send(); 

		if ($mailToUser) 
		return true;
			else
		return false;
	}

	public function getGivenCoupons($data=array()) {
		$givenCoupons = $this->db->query("	SELECT *
											FROM " . DB_PREFIX . "coupon
											WHERE name LIKE  '%AbCart [%'
											ORDER BY " . $data['sort'] . " ". $data['order'] . " 
											LIMIT " . $data['start'].", " . $data['limit'] );										 
		return $givenCoupons->rows;
	}

	public function getTotalGivenCoupons() {
		$givenCoupons = $this->db->query("SELECT COUNT(*) as count FROM " . DB_PREFIX . "coupon WHERE name LIKE '%AbCart [%'"); 
		return $givenCoupons->row['count'];
	}
	
	public function getUsedCoupons($data=array()) {
		$usedCoupons = $this->db->query("SELECT *
		 								  FROM `" . DB_PREFIX . "coupon` AS c
										  JOIN `" . DB_PREFIX . "coupon_history` AS ch ON c.coupon_id=ch.coupon_id
										  WHERE name LIKE  '%AbCart [%'
										  ORDER BY " . $data['sort'] . " ". $data['order'] . " 
										  LIMIT " . $data['start'].", " . $data['limit'] );
		return $usedCoupons->rows;
	}
	
	public function getTotalUsedCoupons() {
		$givenCoupons = $this->db->query("SELECT COUNT(*) as count FROM `" . DB_PREFIX . "coupon` as c
											JOIN `" . DB_PREFIX . "coupon_history` AS ch ON c.coupon_id=ch.coupon_id
											WHERE name LIKE  '%AbCart [%'"); 
		return $givenCoupons->row['count'];
	}
	
	public function getLanguageImage($language_code) {
		$query = $this->db->query("SELECT image FROM " . DB_PREFIX . "language WHERE code = '" . $language_code . "'");
		return $query->row['image'];
	}
	
	public function getTotalRegisteredCustomers() {
		$TotalRegisteredCustomers = $this->db->query("SELECT count(*) as `registered` FROM `" . DB_PREFIX . "abandonedcarts` WHERE `customer_info` LIKE '%email%'"); 
		return $TotalRegisteredCustomers->row['registered'];
	}
	
	public function getTotalCustomers() {
		$TotalCustomers = $this->db->query("SELECT count(*) as `count` FROM `" . DB_PREFIX . "abandonedcarts`"); 
		return $TotalCustomers->row['count'];
	}
	
	public function getMostVisitedPages() {
		$Pages = $this->db->query("SELECT `last_page`, count(`last_page`) as count FROM `" . DB_PREFIX . "abandonedcarts` GROUP BY `last_page` ORDER BY count DESC LIMIT 15"); 
		return $Pages->rows;
	}
	
	
}
?>