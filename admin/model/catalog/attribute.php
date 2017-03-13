<?php
class ModelCatalogAttribute extends Model {
	public function addAttribute($data) {
		$this->event->trigger('pre.admin.attribute.add', $data);

		if(!isset($data['enable'])) $data['enable'] = 0;
		
		$this->db->query("INSERT INTO " . DB_PREFIX . "attribute SET attribute_group_id = '" . (int)$data['attribute_group_id'] . "',
						 filter_name = '" . $this->db->escape($data['filter_name']) . "',
						 sort_order = '" . (int)$data['sort_order'] . "',
						 enable = '" . (int)$data['enable'] . "'
						 ");

		$attribute_id = $this->db->getLastId();

		foreach ($data['attribute_description'] as $language_id => $value) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "attribute_description SET
							 attribute_id = '" . (int)$attribute_id . "',
							 language_id = '" . (int)$language_id . "',
							 name = '" . $this->db->escape($value['name']) . "'");
		}

		$this->event->trigger('post.admin.attribute.add', $attribute_id);

		return $attribute_id;
	}

	public function editAttribute($attribute_id, $data) {
		$this->event->trigger('pre.admin.attribute.edit', $data);

		$this->db->query("UPDATE " . DB_PREFIX . "attribute SET attribute_group_id = '" . (int)$data['attribute_group_id'] . "',
						 sort_order = '" . (int)$data['sort_order'] . "',
						 filter_name = '" . $this->db->escape($data['filter_name']) . "'
						 WHERE attribute_id = '" . (int)$attribute_id . "'");

		$this->db->query("DELETE FROM " . DB_PREFIX . "attribute_description WHERE attribute_id = '" . (int)$attribute_id . "'");

		foreach ($data['attribute_description'] as $language_id => $value) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "attribute_description SET attribute_id = '" . (int)$attribute_id . "', language_id = '" . (int)$language_id . "', name = '" . $this->db->escape($value['name']) . "'");
		}

		$this->event->trigger('post.admin.attribute.edit', $attribute_id);
	}

	public function deleteAttribute($attribute_id) {
		$this->event->trigger('pre.admin.attribute.delete', $attribute_id);

		$this->db->query("DELETE FROM " . DB_PREFIX . "attribute WHERE attribute_id = '" . (int)$attribute_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "attribute_description WHERE attribute_id = '" . (int)$attribute_id . "'");

		$this->event->trigger('post.admin.attribute.delete', $attribute_id);
	}

	public function getAttribute($attribute_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "attribute a LEFT JOIN " . DB_PREFIX . "attribute_description ad ON (a.attribute_id = ad.attribute_id) WHERE a.attribute_id = '" . (int)$attribute_id . "' AND ad.language_id = '" . (int)$this->config->get('config_language_id') . "'");

		return $query->row;
	}

	public function getAttributes($data = array()) {
		$sql = "SELECT *, (SELECT agd.name FROM " . DB_PREFIX . "attribute_group_description agd WHERE agd.attribute_group_id = a.attribute_group_id AND agd.language_id = '" . (int)$this->config->get('config_language_id') . "') AS attribute_group FROM " . DB_PREFIX . "attribute a LEFT JOIN " . DB_PREFIX . "attribute_description ad ON (a.attribute_id = ad.attribute_id) WHERE ad.language_id = '" . (int)$this->config->get('config_language_id') . "'";

		if (!empty($data['filter_name'])) {
			$sql .= " AND ad.name LIKE '" . $this->db->escape($data['filter_name']) . "%'";
		}

		if (!empty($data['filter_attribute_group_id'])) {
			$sql .= " AND a.attribute_group_id = '" . $this->db->escape($data['filter_attribute_group_id']) . "'";
		}

		$sort_data = array(
			'ad.name',
			'attribute_group',
			'a.sort_order'
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY attribute_group, ad.name";
		}

		if (isset($data['order']) && ($data['order'] == 'DESC')) {
			$sql .= " DESC";
		} else {
			$sql .= " ASC";
		}

		if (isset($data['start']) || isset($data['limit'])) {
			if ($data['start'] < 0) {
				$data['start'] = 0;
			}

			if ($data['limit'] < 1) {
				$data['limit'] = 20;
			}

			$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
		}

		$query = $this->db->query($sql);

		return $query->rows;
	}

	public function getAttributeDescriptions($attribute_id) {
		$attribute_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "attribute_description WHERE attribute_id = '" . (int)$attribute_id . "'");

		foreach ($query->rows as $result) {
			$attribute_data[$result['language_id']] = array('name' => $result['name']);
		}

		return $attribute_data;
	}

	public function getAttributesByAttributeGroupId($data = array()) {
		$sql = "SELECT *, (SELECT agd.name FROM " . DB_PREFIX . "attribute_group_description agd WHERE agd.attribute_group_id = a.attribute_group_id AND agd.language_id = '" . (int)$this->config->get('config_language_id') . "') AS attribute_group FROM " . DB_PREFIX . "attribute a LEFT JOIN " . DB_PREFIX . "attribute_description ad ON (a.attribute_id = ad.attribute_id) WHERE ad.language_id = '" . (int)$this->config->get('config_language_id') . "'";

		if (!empty($data['filter_name'])) {
			$sql .= " AND ad.name LIKE '" . $this->db->escape($data['filter_name']) . "%'";
		}

		if (!empty($data['filter_attribute_group_id'])) {
			$sql .= " AND a.attribute_group_id = '" . $this->db->escape($data['filter_attribute_group_id']) . "'";
		}

		$sort_data = array(
			'ad.name',
			'attribute_group',
			'a.sort_order'
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY ad.name";
		}

		if (isset($data['order']) && ($data['order'] == 'DESC')) {
			$sql .= " DESC";
		} else {
			$sql .= " ASC";
		}

		if (isset($data['start']) || isset($data['limit'])) {
			if ($data['start'] < 0) {
				$data['start'] = 0;
			}

			if ($data['limit'] < 1) {
				$data['limit'] = 20;
			}

			$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
		}

		$query = $this->db->query($sql);

		return $query->rows;
	}

	public function getTotalAttributes() {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "attribute");

		return $query->row['total'];
	}

	public function getTotalAttributesByAttributeGroupId($attribute_group_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "attribute WHERE attribute_group_id = '" . (int)$attribute_group_id . "'");

		return $query->row['total'];
	}
	public function getAttributeOnName($attribute_name){
	
		$sql = "SELECT attribute_id FROM " . DB_PREFIX . "attribute_description
									WHERE upper(`name`) LIKE '".mb_strtoupper(addslashes($attribute_name[1]),'UTF-8')."' OR
										upper(`name`) LIKE '".mb_strtoupper(addslashes($attribute_name[2]),'UTF-8')."'
									LIMIT 0,1;";
		
		
		$r = $this->db->query($sql);
		
		if($r->num_rows){
			
			$row = $r->row;
			return $row['attribute_id'];
			
		}
		
		return 0;
		
	}
	
	public function getAttributeOrAdd($attribute_name, $attribute_group_id = 0){
	
		$attribute_id = $this->getAttributeOnName($attribute_name, $attribute_group_id);
		
		if($attribute_id) return $attribute_id;
		
		$data = array();
		$data['attribute_description'][1]['name'] = $attribute_name[1];
		$data['attribute_description'][1]['description'] = $attribute_name[1];
		$data['attribute_description'][2]['name'] = $attribute_name[2];
		$data['attribute_description'][2]['description'] = $attribute_name[2];
		$data['filter_name'] = strtolower(str_replace(' ','',$attribute_name[2]));
		
		$data['attribute_group_id'] = $attribute_group_id;
		$data['sort_order'] = 1;
		$data['enable'] = 1;
		
		//header("Content-Type: text/html; charset=UTF-8");
		//echo "<pre>";  print_r(var_dump( $data )); echo "</pre>";die();
		
		return $this->addAttribute($data);
		
	}
	

}