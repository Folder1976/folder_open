<?php
class ModelCatalogShops extends Model {

	public function getShops() {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "shops` ORDER BY `name`;");

		return $query->rows;
	}

	public function getShopIdOnName($name) {
		$query = $this->db->query("SELECT id FROM `" . DB_PREFIX . "shops` WHERE name='".$name."' LIMIT 1;");
		
		if($query->num_rows == 0) return 1; // Noname shop
		
		return $query->row['id'];
	}

	public function getShop($shop_id) {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "shops` WHERE id='".$shop_id."' LIMIT 1;");

		return $query->row;
	}

}
