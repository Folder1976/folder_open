<?php
/*
 *Class for working w product type
 */
class ModelCatalogProductType extends Model {
	
	public function getProductTypeName($id) {
		
		$query = $this->db->query("SELECT product_type_name FROM product_type WHERE product_type_id = '$id';");
		
		if($query->row){
			$tmp = $query->row;
		
			return $tmp['product_type_name'];
		}
		
		return '';
	}
	
/*
 *Making product type tree w scripts
 *
 *var $type array[product_type_id] for make select
 *return html code of product type tree
 */
	public function getProductTypeTreeLinks() {
		$this->event->trigger('pre.admin.category.add', $data);
		$this->load->language('catalog/product_type');
		
		$query = $this->db->query("SELECT * FROM product_type ORDER BY product_type_sort ASC, product_type_name ASC;");
		
		$Types[0]['id'] = 0;
		$Types[0]['name'] = $this->language->get('product_type_main_unit_name');
		
		$body = "<!--div id=\"container-type\" class = \"treeclosed-type\"-->
			<div id=\"product_type\" class = \"product-type-tree treeclosed-type\">
			<div id=\"treecloase-type\"><a href=\"javascript:\">закрыть [х]</a></div>
			<ul  id=\"celebTree-type\"><li><span id=\"span_0\"><a class = \"tree-type main-type\" href=\"javascript:\" id=\"set_0\">
				&nbsp;".$this->language->get('product_type_main_unit_name')."</a></span><ul>";
		$types_for_java = "";
		if ($query->rows) {
			foreach ($query->rows as $Type) {
				$Types[$Type['product_type_id']]['id'] = $Type['product_type_id'];
				$Types[$Type['product_type_id']]['name'] = $Type['product_type_name'];
				$types_for_java .= "'".$Type['product_type_id']."':'".$Type['product_type_name']."', ";
				
				if ($Type['product_parent_id'] == 0) {
					$body .= "<li><span id=\"span_" . $Type['product_type_id'] . "\"><a class = \"tree-type type main-type\" href=\"javascript:\" id=\"set_" . $Type['product_type_id'] . "\">";
					$body .= "" . $Type['product_type_name'] . "</a>";
					$body .= "</span>". $this->getTypeOnParentIdList($Type['product_type_id'], $query);// . readTree($tmp['product_type_id'], $aglnk);
					$body .= "</li>";
				}
			}
		}
		$body .= '</ul>
			</li></ul>
			</div>
			<script>
			 function get_name_list(id_mass){
				var for_return = "";
				var names = {'.trim($types_for_java," ,").'};
				for_return = "["+names[id_mass]+"]";
				return for_return;
			  }
			</script>';
		
		$css = "";
		$script = "";
		
		$data['body'] = $body;
		$data['types'] = $Types;
		
		return $data;
	}
	
	public function getProductTypeTree($type) {
		$this->event->trigger('pre.admin.category.add', $data);
		$this->load->language('catalog/product_type');
		
		$query = $this->db->query("SELECT * FROM product_type ORDER BY product_type_sort ASC, product_type_name ASC;");
		
		$Types[0]['id'] = 0;
		$Types[0]['name'] = $this->language->get('product_type_main_unit_name');
		
		$body = "<!--div id=\"container-type\" class = \"treeclosed-type\"-->
			<div id=\"product_type\" class = \"product-type-tree treeclosed-type\">
			<div id=\"treecloase-type\"><a href=\"javascript:\">закрыть [х]</a></div>
			<ul  id=\"celebTree-type\"><li><span id=\"span_0\"><a class = \"tree-type main-type\" href=\"javascript:\" id=\"0\">
				<input type=\"checkbox\" id=\"set_0\">&nbsp;".$this->language->get('product_type_main_unit_name')."</a></span><ul>";
		$types_for_java = "";
		if ($query->rows) {
			foreach ($query->rows as $Type) {
				$Types[$Type['product_type_id']]['id'] = $Type['product_type_id'];
				$Types[$Type['product_type_id']]['name'] = $Type['product_type_name'];
				$types_for_java .= "'".$Type['product_type_id']."':'".$Type['product_type_name']."', ";
				
				if ($Type['product_parent_id'] == 0) {
					$body .= "<li><span id=\"span_" . $Type['product_type_id'] . "\"><a class = \"tree-type type main-type\" href=\"javascript:\" id=\"" . $Type['product_type_id'] . "\">
					<input type=\"checkbox\" ";
					if (in_array($Type['product_type_id'], $type, true)) $body .="checked";
					$body .=" id=\"set_" . $Type['product_type_id'] . "\" >&nbsp;" . $Type['product_type_name'] . "</a>";
					$body .= "</span>". $this->getTypeOnParentId($Type['product_type_id'], $query, $type);// . readTree($tmp['product_type_id'], $aglnk);
					$body .= "</li>";
				}
			}
		}
		$body .= '</ul>
			</li></ul>
			</div>
			<script>
			 function get_name_list(id_mass){
				var for_return = "";
				var id = id_mass.split("#");
				var names = {'.trim($types_for_java," ,").'};
				for(var i=0; i<id.length-1; i++){
					    for_return = for_return + names[id[i]]+", ";
					}
				return for_return;
			  }
			</script>';
		
		$css = "";
		$script = "";
		
		$data['body'] = $body;
		$data['types'] = $Types;
		
		return $data;
	}
	
/*
 *return html code of product type tree filtered by parent id
 *
 *$parent_id int
 *$query mysql_result
 */
	public function getTypeOnParentIdList($parent_id, $query){

		$body = "";
	   	foreach ($query->rows as $Type) {
			if($Type['product_parent_id'] == $parent_id){
				$body .= "<li><span id=\"span_" . $Type['product_type_id'] . "\"><a class = \"tree-type type\" href=\"javascript:\" id=\"set_" . $Type['product_type_id'] . "\">";
				$body .="" . $Type['product_type_name'] . "</a>";
				$body .= "</span>" . $this->getTypeOnParentIdList($Type['product_type_id'], $query);
				$body .= "</li>";
			}
		}
		if ($body != "") $body = "<ul>$body</ul>";
		
		return $body;
			
	}
	
	public function getTypeOnParentId($parent_id, $query, $type){

		$body = "";
	   	foreach ($query->rows as $Type) {
			if($Type['product_parent_id'] == $parent_id){
				$body .= "<li><span id=\"span_" . $Type['product_type_id'] . "\"><a class = \"tree-type type\" href=\"javascript:\" id=\"" . $Type['product_type_id'] . "\">
				<input type=\"checkbox\" ";
				if (in_array($Type['product_type_id'], $type, true)) $body .="checked";
				$body .=" id=\"set_" . $Type['product_type_id'] . "\" >&nbsp;" . $Type['product_type_name'] . "</a>";
				$body .= "</span>" . $this->getTypeOnParentId($Type['product_type_id'], $query, $type);
				$body .= "</li>";
			}
		}
		if ($body != "") $body = "<ul>$body</ul>";
		
		return $body;
			
	}
	

}
