<?php
class ModelModuleCategoryFast extends Model {

    public function getCategories($parent_id = 0) {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "category c LEFT JOIN " . DB_PREFIX . "category_description cd ON (c.category_id = cd.category_id) WHERE c.parent_id = '" . (int)$parent_id . "' AND cd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND c.status = '1' ORDER BY c.sort_order, LCASE(cd.name)");

        return $query->rows;
    }

    /**
     * Get categories by parent id
     *
     * @param int $parentId
     * @return bool
     */
    public function getCategoriesByParentId($parentId)
    {
        $parentId = $this->db->escape($parentId);
        $p = DB_PREFIX;
        $sql = "SELECT * FROM `{$p}category_path` `cp`"
            . " INNER JOIN `{$p}category` `c` ON `c`.`category_id` = `cp`.`category_id`"
            . " INNER JOIN `{$p}category_description` `cd` ON `cd`.`category_id` = `c`.`category_id`"
            . " WHERE `cp`.`path_id` = $parentId";

        $result = $this->db->query($sql);
        if ($result->num_rows == 0) {
            return false;
        }

        return $result->rows;
    }

    public function getCategoriesLevelsByCategoriesIds(array $ids)
    {
        $ids = implode(', ', $ids);
        $p = DB_PREFIX;
        $sql = "SELECT (COUNT(*) - 1) AS `level`, `cp`.`category_id` FROM `{$p}category_path` `cp` "
            . "WHERE `cp`.`category_id` IN ($ids) GROUP BY `cp`.`category_id`";
        $result = $this->db->query($sql);
        if ($result->num_rows == 0) {
            return false;
        }

        return $result->rows;
    }

}