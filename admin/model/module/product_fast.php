<?php
class ModelModuleProductFast extends Model {

    private $_prefix;

    public function __construct($registry)
    {
        parent::__construct($registry);
        $this->_prefix = DB_PREFIX;
    }

    public function getProductsByCategoriesIds(array $ids)
    {
        
        $langId = (int)$this->config->get('config_language_id');
        $ids = implode(', ', $ids);
        $sql = "SELECT `p`.*, `pd`.*, `p2c`.`category_id` AS `category_id`"
            . " FROM `{$this->_prefix}product` `p`"
            . " INNER JOIN `{$this->_prefix}product_description` `pd` ON `pd`.`product_id` = `p`.`product_id`"
            . " INNER JOIN `{$this->_prefix}product_to_category` `p2c` ON `p2c`.`product_id` = `p`.`product_id`"
            . " WHERE `p2c`.`category_id` IN ($ids) AND `pd`.`language_id` = $langId";
            
        //Если прилетел фильтр по Бренду
        if(isset($this->request->get['manufacturer']) AND $this->request->get['manufacturer'] > 0){
            $sql .= ' AND p.manufacturer_id = \''.$this->request->get['manufacturer'] . '\';';
        }

        $result = $this->db->query($sql);
        if ($result->num_rows == 0) {
            return false;
        }

        return $result->rows;
    }

    public function getProductsAttributesByProductsIds(array $ids)
    {
        $ids = implode(', ', $ids);
        $langId = (int)$this->config->get('config_language_id');
        $sql = "SELECT * FROM `{$this->_prefix}product_attribute` WHERE `product_id` IN ($ids) AND `language_id` = $langId";
        $result = $this->db->query($sql);
        if ($result->num_rows == 0) {
            return false;
        }
        
        return $result->rows;
    }

    public function getManufacturersByProductsIds(array $ids)
    {
        $ids = implode(', ', $ids);
        $sql = "SELECT * FROM `{$this->_prefix}manufacturer` `m`"
            . " INNER JOIN `{$this->_prefix}product` `p` ON `p`.`manufacturer_id` = `m`.`manufacturer_id`"
            . " WHERE `p`.`product_id` IN ($ids)";
        
        $result = $this->db->query($sql);
        if ($result->num_rows == 0) {
            return false;
        }

        return $result->rows;
    }
    
      public function getProductsByCategoriesTree(array $ids)
    {
        
        $langId = (int)$this->config->get('config_language_id');
        $ids = implode(', ', $ids);
        $sql = "SELECT `p`.*, `pd`.*, `p2c`.`category_id` AS `category_id`"
            . " FROM `{$this->_prefix}product` `p`"
            . " INNER JOIN `{$this->_prefix}product_description` `pd` ON `pd`.`product_id` = `p`.`product_id`"
            . " INNER JOIN `{$this->_prefix}category_path` `cp` ON `cp`.`category_id` = `p`.`category_id`"
            . " INNER JOIN `{$this->_prefix}product_to_category` `p2c` ON `p2c`.`product_id` = `p`.`product_id`"
            . " WHERE `p2c`.`category_id` IN ($ids) AND `pd`.`language_id` = $langId";
            
        //Если прилетел фильтр по Бренду
        if(isset($this->request->get['manufacturer']) AND $this->request->get['manufacturer'] > 0){
            $sql .= ' AND p.manufacturer_id = \''.$this->request->get['manufacturer'] . '\';';
        }

        $result = $this->db->query($sql);
        if ($result->num_rows == 0) {
            return false;
        }

        return $result->rows;
    }

}