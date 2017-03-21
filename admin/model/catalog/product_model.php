<?php
/*
 *Class for working w product model
 */
class ModelCatalogProductModel extends Model {

	public function generateAlias($prod_model_id){
		
		$sql = 'SELECT prod_model_code AS model, brand_code AS manufacturer, product_type_kod AS type
				FROM product_model PM
				LEFT JOIN product_type PT ON PM.product_type_id = PT.product_type_id
				WHERE prod_model_id = \''.$prod_model_id.'\';';
		$r = $this->db->query($sql);
		
		if($r->num_rows == 0){
			return '';
		}
		
		$alias = '';
		$tmp = $r->row;
		
		if($tmp['type'] != ''){
			$alias .= $tmp['type'] . '/';
		}
		if($tmp['manufacturer'] != ''){
			$alias .= $tmp['manufacturer'] . '/';
		}
		if($tmp['model'] != ''){
			$alias .= $tmp['model'] . '';
		}
		
		return $alias;
			
	}
	
	//Точно такой же генератор есть и в рабочем контролере - не забывать обновлять и его!	
	public function generateDinamicAlias($product_type_id, $prod_brand_code, $prod_model_code){
		
		//Это вариант запроса если алиас будем генерить по пути типа	
		/*
		$sql = 'SELECT product_type_kod AS code, level FROM product_type_path PPT
						LEFT JOIN product_type PT ON PT.product_type_id = path_id
						WHERE PPT.product_type_id = \''.$product_type_id.'\'
						ORDER BY level ASC;
					';
		*/			
		//Этот вариант запроса если в коде типа уже есть полный путь
		$sql = 'SELECT product_type_kod AS code, "1" AS level FROM product_type 
						WHERE product_type_id = \''.$product_type_id.'\';
					';
		$r = $this->db->query($sql);
		if($r->num_rows == 0){
			return '';
		}
		
		$alias = '';
		
		foreach($r->rows as $type){
			if($type['level'] > 0){
				$alias .= $type['code'].'/';
			}
		}
		
		$alias .= $prod_brand_code.'/';
		$alias .= $prod_model_code.'/';
		
		$alias = str_replace('//', '/', $alias);
		$alias = trim($alias, '/');
		
		return $alias;
			
	}
	
	public function getAlias($prod_model_id){
		
		$sql = 'SELECT keyword 
				FROM url_alias 
				WHERE query = \'product_model='.$prod_model_id.'\';';
		$r = $this->db->query($sql);
		
		if($r->num_rows == 0){
			return '';
		}
		
		$tmp = $r->row;
		return $tmp['keyword'];
			
	}
	
	public function setProductModel($product_id, $product_model){
		
		if(is_numeric($product_model)){
			$id = $product_model;
		}else{
			
			//Получим данные по товару. Тип и Бренд
			$sql = 'SELECT P.product_type_id, PT.product_type_kod, `code` AS brand_code FROM product P
						LEFT JOIN product_type PT ON PT.product_type_id = P.product_type_id
						LEFT JOIN manufacturer M ON P.manufacturer_id = M.manufacturer_id
						WHERE P.product_id = \''.$product_id.'\';';
			$r = $this->db->query($sql);
			
			if($r->num_rows == 0){
				return 'Не нашел Продукта';
			}
			$product = $r->row;
		
			//Получим дерево типов. Будем искать в родителях еслине найдем
			$sql = 'SELECT path_id AS product_type_id FROM product_type_path WHERE product_type_id = \''.$product['product_type_id'].'\' ORDER BY level DESC;';
			$r = $this->db->query($sql);
			if($r->num_rows == 0){
				return 'Не верный тип';
			}
				
			foreach($r->rows as $type){
				$sql = 'SELECT prod_model_id, prod_model_code FROM product_model WHERE
						product_type_id = \''.$type['product_type_id'].'\' AND
						brand_code = \''.$product['brand_code'].'\' AND
						(
							upper(`prod_model_name`) LIKE "%'.mb_strtoupper(addslashes($product_model),'UTF-8').'%" or
							upper(`prod_model_code`) LIKE "%'.mb_strtoupper(addslashes($product_model),'UTF-8').'%"
						);';
				$r = $this->db->query($sql);
				//Если нашли модель - вываливаемся из цикла
				if($r->num_rows > 0){
					break;
				}
			}
			
			//Как мы сюда попали - потому что модель нашли или потому что цикл закончился.
			if($r->num_rows == 0){
				//echo $sql; die('<br>END');
				return 'Не нашел сочетание Тип+Бренд';
			}
			
			$model = $r->row;
			
			$sql = 'UPDATE product SET product_model = \''.$model['prod_model_id'].'\' WHERE product_id = \''.$product_id.'\';';
			$this->db->query($sql);
			
			//echo $product['product_type_id'].' '. $product['brand_code'].' '.$model['prod_model_code'];
			
			//Если все ОК - сгенерим алиас для этой модели
			//Можно прогнать через генератор, а можно слепить из годов. Коды у нас есть.
			//Но генератор может работать с путями!!!
			//ИД видтуально модели содержит в себе Ид Типа в который она вложена
			$alias = $this->generateDinamicAlias($product['product_type_id'], $product['brand_code'],$model['prod_model_code']);
			//$alias = $product['product_type_kod'].'/'.$product['brand_code'].'/'.$model['prod_model_code'];
			$sql = 'INSERT INTO url_alias SET
							query = \'virtual_product_model='.($product['product_type_id'] .'*'. $model['prod_model_id']).'\',
							keyword = \''.$alias.'\',
							is_main = \'0\'	
						ON DUPLICATE KEY UPDATE
							keyword = \''.$alias.'\'
					;';
			//die($sql);
			$this->db->query($sql) or die($sql);
			
			return (int)$model['prod_model_id'];
			//$id = $this->getIdOnAlias($product_model);
			//$id = $this->getIdOnName($product_model);
		}
			
	}
	
	public function getIdOnName($alias){
		
		$sql = 'SELECT prod_model_id 
				FROM product_model 
				WHERE
				prod_model_name like \'product_model=%\' AND 
				keyword = \''.$alias.'\';';
		$r = $this->db->query($sql);
		
		if($r->num_rows == 0){
			return 0;
		}
		
		$tmp = $r->row;
		return (int)str_replace('product_model=','',$tmp['query']);
			
	}

	public function getIdOnAlias($alias){
		
		$sql = 'SELECT query 
				FROM url_alias 
				WHERE
				query like \'product_model=%\' AND 
				keyword = \''.$alias.'\';';
		$r = $this->db->query($sql);
		
		if($r->num_rows == 0){
			return 0;
		}
		
		$tmp = $r->row;
		return (int)str_replace('product_model=','',$tmp['query']);
			
	}


}
