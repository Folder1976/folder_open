<?php
class ModelLocalisationCountry extends Model {
	public function getCountry($country_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "country C
								  LEFT JOIN " . DB_PREFIX . "country_description CD ON (C.country_id = CD.country_id)
								  WHERE C.country_id = '" . (int)$country_id . "' AND status = '1' AND
								  CD.language_id = '" . (int)$this->config->get('config_language_id') . "'
								  ");

		return $query->row;
	}

	public function getCountryOnCode($country_code) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "country C
								  LEFT JOIN " . DB_PREFIX . "country_description CD ON (C.country_id = CD.country_id)
								  WHERE iso_code_2 = '" . $country_code . "' AND status = '1' AND
								  CD.language_id = '" . (int)$this->config->get('config_language_id') . "'");

		return $query->row;
	}
	
	public function getCountries() {
		$country_data = false; //$this->cache->get('country.status');

		if (!$country_data) {
			$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "country C
									  LEFT JOIN " . DB_PREFIX . "country_description CD ON (C.country_id = CD.country_id)
									  WHERE C.status = '1' AND
									  CD.language_id = '" . (int)$this->config->get('config_language_id') . "'
									  ORDER BY CD.name ASC");

			$country_data = $query->rows;

			$this->cache->set('country.status', $country_data);
		}

		return $country_data;
	}
	
	public function getCountriesForDelivery() {
		$country_data = false;//$this->cache->get('country.status');

		if (!$country_data) {
			
			$sql = "SELECT * FROM " . DB_PREFIX . "delivery_to_country C2D
									  LEFT JOIN " . DB_PREFIX . "country C ON C2D.country_id = C.country_id
									  LEFT JOIN " . DB_PREFIX . "country_description CD ON (C.country_id = CD.country_id)
									  WHERE C2D.status = '1' AND
									  CD.language_id = '" . (int)$this->config->get('config_language_id') . "'
									  GROUP BY CD.name ORDER BY CD.name ASC";
			$query = $this->db->query($sql);

			$country_data = $query->rows;

			$this->cache->set('country.status', $country_data);
		}

		return $country_data;
	}
}