<?php

/**
 * Class ModelModuleUserLabel
 */
class ModelModuleUserLabel extends Model {

    /**
     * Get user labels list attached to the orders
     *
     * @param array $ordersIds
     * @return array|bool|null
     */
    public function getUserLabelsListAttachedToTheOrders(array $ordersIds) {

        if (!is_array($ordersIds) || count($ordersIds) == 0) {
            return false;
        }

        $p = DB_PREFIX;
        $ordersIds = implode(', ', $ordersIds);
        $sql = "SELECT `ulb`.`name`, `u2o`.`value`, `u2o`.`order_id` FROM `user_label_to_order` `u2o`"
            . " INNER JOIN `{$p}user_label` `ulb` ON `ulb`.`user_label_id` = `u2o`.`user_label_id`"
            . " WHERE `u2o`.`order_id` IN ($ordersIds)";
        unset($ordersIds, $p);
        $result = $this->db->query($sql);
        unset($sql);
        if ($result->num_rows == 0) {
            return null;
        }

        $list = [];
        foreach ($result->rows as $row) {
            $list[$row['order_id']][] = [
                'name' => $row['name'],
                'value' => $row['value']
            ];
        }

        return $list;
    }

}