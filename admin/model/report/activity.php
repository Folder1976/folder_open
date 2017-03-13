<?php
class ModelReportActivity extends Model {
	public function getActivities() {
		
		$sql = "SELECT a.key, a.data, a.date_added FROM ((SELECT CONCAT('customer_', ca.key) AS `key`, ca.data, ca.date_added FROM `" . DB_PREFIX . "customer_activity` ca) UNION (SELECT CONCAT('affiliate_', aa.key) AS `key`, aa.data, aa.date_added FROM `" . DB_PREFIX . "affiliate_activity` aa)) a ORDER BY a.date_added DESC LIMIT 0,5";
		
		$query = $this->db->query($sql);
		
		$return = array();
		foreach($query->rows as $row){
			
			$return[$row['date_added']] = $row;
			$return[$row['date_added']]['data'] = html_entity_decode(html_entity_decode($row['data']));
			
		}

		return $return;
	}
}