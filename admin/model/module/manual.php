<?php

/**
 * Class ModelModuleManual
 *
 * Manual model
 *
 * @author Yegor Chuperka (ychuperka@gmail.com)
 */
class ModelModuleManual extends ModelModuleItemConnectedToProduct
{

    private $_dpx = DB_PREFIX;

    /**
     * Add manual
     *
     * @param array $data
     * @throws Exception
     *
     * @return mixed
     */
    public function addManual(array $data)
    {
        // Check if relationships already exists
        $sql = "SELECT COUNT(`manual_id`) AS `cnt` FROM `{$this->_dpx}manual` WHERE `product_id` = {$data['product_id']}";
        $result = $this->db->query($sql);
        if ($result->row['cnt'] > 0) {
            return 0;
        }

        // Create new relationships
        $data = $this->_prepareData($data);
        $sql = "INSERT INTO `{$this->_dpx}manual` (`product_id`, `filename`, `should_update`) VALUES ({$data['product_id']}, '{$data['filename']}', 1)";
        $this->db->query($sql);

        return $this->db->getLastId();
    }

    /**
     * Edit manual
     *
     * @param array $data
     * @throws Exception
     */
    public function editManual(array $data)
    {
        $data = $this->_prepareData($data);

        $manualId = (int)$data['manual'];
        if ($manualId == 0) {
            throw new Exception('Invalid manual id');
        }

        $sql = "UPDATE `{$this->_dpx}manual` SET `product_id` = {$data['product_id']}, `filename` = '{$data['filename']}'"
            . " WHERE `manual_id` = $manualId";
        $this->db->query($sql);
    }

    /**
     * Prepare data
     *
     * @param array $data
     * @return array
     *
     * @throws Exception
     */
    private function _prepareData(array $data)
    {
        $preparedData = array(
            'product_id' => (int)$data['product_id'],
            'filename' => $this->db->escape($data['filename'])
        );

        if ($preparedData['product_id'] == 0) {
            throw new Exception('Invalid product id');
        } else if (strlen($preparedData['filename']) == 0) {
            throw new Exception('Empty filename');
        }

        return $preparedData;
    }

    /**
     * Delete manual
     *
     * @param int $manualId
     * @throws InvalidArgumentException
     */
    public function deleteManual($manualId)
    {
        $manualId = (int)$manualId;
        if ($manualId == 0) {
            throw new InvalidArgumentException('Manual id is empty');
        }

        $sql = "DELETE FROM `{$this->_dpx}manual` WHERE `manual_id` = $manualId";
        $this->db->query($sql);
    }

    /**
     * Delete all manuals
     */
    public function deleteAllManuals()
    {
        $sql = "DELETE FROM `{$this->_dpx}manual` WHERE `should_update` = 1";
        $this->db->query($sql);
    }

    /**
     * Get manual by id
     *
     * @param int $manualId
     * @return mixed
     * @throws \Exception
     */
    public function get($manualId)
    {
        $manualId = (int)$manualId;
        if ($manualId == 0) {
            throw new \Exception('Invalid manual id');
        }

        $sql = "SELECT * FROM `{$this->_dpx}manual` WHERE `{$this->_dpx}manual`.`manual_id` = $manualId";
        $result = $this->db->query($sql);
        if ($result->num_rows == 0) {
            return null;
        }

        return $result->row;
    }

    /**
     * Get list of manuals
     *
     * @param int $page
     * @param int $limit
     * @param array $filters
     * @return mixed
     * @throws InvalidArgumentException
     * @throws Exception
     */
    public function getList($page = 1, $limit = 10, array $filters = null)
    {
        $page = (int)$page;
        if ($page == 0) {
            throw new InvalidArgumentException('Invalid page');
        }
        $page--;

        $limit = (int)$limit;
        if ($limit < 1) {
            throw new InvalidArgumentException('Invalid limit');
        }

        $startPosition = $page * $limit;
        $sql = 'SELECT '
            . "`{$this->_dpx}manual`.`manual_id`, `{$this->_dpx}manual`.`product_id`, `{$this->_dpx}manual`.`filename`, "
            . "`{$this->_dpx}product`.`sku` AS `product_sku`, `{$this->_dpx}product`.`model` AS `product_model` FROM "
            . "`{$this->_dpx}manual` JOIN `{$this->_dpx}product` ON"
            . "`{$this->_dpx}product`.`product_id` = `{$this->_dpx}manual`.`product_id`";

        if ($filters !== null && count($filters) > 0) {

            if (isset($filters['filter-sku']) && strlen($filters['filter-sku'])) {
                $sql .= " WHERE `{$this->_dpx}product`.`sku` LIKE '{$this->db->escape($filters['filter-sku'])}%'"
                    . " OR `{$this->_dpx}product`.`model` LIKE '{$this->db->escape($filters['filter-sku'])}%'";
            }

            if (isset($filters['filter-order-direction']) && strlen($filters['filter-order-direction']) >= 3) {

                $orderDirection = $filters['filter-order-direction'];
                switch ($orderDirection) {
                    case 'asc':
                    case 'desc':
                        break;
                    default:
                        throw new Exception('Invalid order direction');
                }

                $sql .= " ORDER BY `{$this->_dpx}manual`.`manual_id` " . strtoupper($orderDirection);
            }
        }

        $sql .= " LIMIT $startPosition,$limit";

        return $this->db->query($sql);
    }

    /**
     * Get total count
     *
     * @return int
     */
    public function totalCount()
    {
        $sql = "SELECT COUNT(`manual_id`) AS `cnt` FROM `{$this->_dpx}manual`";
        $result = $this->db->query($sql);

        return (int)$result->row['cnt'];
    }

    protected function _prepareFileName($raw)
    {
        return substr(
            $raw,
            strpos($raw, '/module')
        );
    }
}