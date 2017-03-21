<?php

/**
 * Html block model
 *
 * @author n04h <iam@n04h.com>
 */
class ModelModuleHtmlBlocks extends Model
{

    /**
     * Get html blocks
     *
     * @param int $page
     * @param int $limit
     *
     * @return array|null
     */
    public function get($page = 0, $limit = 10)
    {
        $page = $page * $limit;

        // Select html blocks
        $p = DB_PREFIX;
        $sql = 'SELECT * '
            . "FROM `{$p}html_block` "
            . 'ORDER BY `sort_order` '
            . "LIMIT $page,$limit";

        $hbResult = $this->db->query($sql);

        if ($hbResult->num_rows == 0) {
            return null;
        }

        // Select html block`s description
        $hbIds = array();
        foreach ($hbResult->rows as $row) {
            $hbIds[] = $row['html_block_id'];
        }

        $sql = 'SELECT * '
            . "FROM `{$p}html_block_description` "
            . 'WHERE `html_block_id` IN (' . implode(', ', $hbIds) . ') ';
        
        $hbdResult = $this->db->query($sql);

        // Process query result
        $items = array();
        foreach ($hbResult->rows as $hbRow) {
            $item = $hbRow;
            foreach ($hbdResult->rows as $key => $hbdRow) {
                if ($hbdRow['html_block_id'] == $item['html_block_id']) {

                    // Set item description
                    if (!isset($item['title'])) {
                        $item['title'] = array();
                    }
                    $langId = $hbdRow['language_id'];
                    $item['title'][$langId] = $hbdRow['title'];
                    $item['content'][$langId] = $hbdRow['content'];

                    // Delete current entry
                    unset($hbdResult->rows[$key]);

                    // Put item into list
                    $items[] = $item;
                } 
            }
        }

        return $items;
    }

    /**
     * Get keys
     *
     * @return array|null
     */
    public function getAllKeys()
    {
        $p = DB_PREFIX;
        $sql = "SELECT `key` FROM `{$p}html_block`";
        $result = $this->db->query($sql);
        if ($result->num_rows == 0) {
            return null;
        }

        $keys = array();
        foreach ($result->rows as $row) {
            $keys[$row['key']] = $row['key'];
        }

        return $keys;
    }

    /**
     * Create html block
     *
     * Param "data" description:
     * Possible fields is: title, content and sort_order.
     * Title is a html block title, content is a 
     * html block content and sort_order is a html block sort order 
     * position.
     *
     * Returns zero if fail or last insert id if success.
     *
     * @param array $data
     *
     * @return int
     */
    public function create(array $data)
    {
        // Check data
        if (!$this->_isDataValid($data, true)) {
            return 0;
        }

        if (!isset($data['sort_order'])) {
            $data['sort_order'] = 0;
        } else if (!is_numeric($data['sort_order'])) {
            return 0;
        }

        // Drop cache record (may be exists, why not?)
        $this->_dropCacheRecord($data['key']);

        // Insert records
        $p = DB_PREFIX;
        $key = $this->db->escape($data['key']);

        $sql = "INSERT INTO `{$p}html_block` (`store_id`, `key`, `sort_order`) "
            . "VALUES ({$data['store_id']}, '$key', {$data['sort_order']})";
        $this->db->query($sql);

        // Check query result
        $hbId = $this->db->getLastId();
        if ($hbId == 0) {
            return 0;
        }

        foreach ($data['title'] as $langId => $title) {

            // Check content with same language id exists
            if (!isset($data['content'][$langId])) {
                continue;
            }

            // Prepare and execute query
            $title = $this->db->escape($title);
            $content = $this->db->escape(html_entity_decode($data['content'][$langId]));
            $sql = "INSERT INTO `{$p}html_block_description` (`html_block_id`, `language_id`, `title`, `content`) "
                . "VALUES ($hbId, $langId, '$title', '$content')";
            $this->db->query($sql);
        }

        return $hbId;
    }


    /**
     * Update html block
     *
     * Param "data" description:
     * Possible fields is: title, content, sort_order, html_block_id
     * Title is a html block title, content is a html block content,
     * sort order is a html block sort order position, html_block_id
     * is a record id that will be updated.
     *
     * @param array $data
     *
     * @return bool
     */
    public function update(array $data)
    {
        // Check data
        if (!$this->_isDataValid($data, false)) {
            return false;
        }

        // Drop cache record
        $this->_dropCacheRecord($data['key']);

        // Update records
        $p = DB_PREFIX;
        $key = $this->db->escape($data['key']);

        $sql = "UPDATE `{$p}html_block` SET `store_id` = {$data['store_id']}, `key` = '$key', `sort_order` = {$data['sort_order']} "
            . "WHERE `html_block_id` = {$data['html_block_id']}";
        $this->db->query($sql);

        foreach ($data['title'] as $langId => $title) {
            // Check if content exists
            if (!isset($data['content'][$langId])) {
                continue;
            }

            $title = $this->db->escape($title);
            $content = $this->db->escape(html_entity_decode($data['content'][$langId]));
            $sql = "UPDATE `{$p}html_block_description` SET `title` = '$title', `content` = '$content', "
                . "`language_id` = $langId WHERE `html_block_id` = {$data['html_block_id']}";
            $this->db->query($sql);

            return true;
        }
    }

    /**
     * Delete html block
     *
     * @param array $keys
     *
     * @return bool|int
     */
    public function delete(array $keys)
    {
        // Check array
        if (count($keys) == 0) {
            return false;
        }

        // Drop cache records
        foreach ($keys as $k) {
            $this->_dropCacheRecord($k);
        }

        // Delete blocks
        $p = DB_PREFIX;

        // Delete html blocks
        array_walk(
            $keys,
            function(&$item, $key) {
                $item = "'$item'";
            }
        );
        $keysAsString = implode(', ', $keys);
        $sql = "SELECT `html_block_id` FROM `html_block` WHERE `key` IN($keysAsString)";
        $result = $this->db->query($sql);
        if ($result->num_rows == 0) {
            return false;
        }
        unset($keysAsString);

        $ids = array();
        foreach ($result->rows as $row) {
            $ids[] = $row['html_block_id'];
        }
        $idsAsString = implode(', ', $ids);

        $sql = "DELETE FROM `{$p}html_block` "
            . "WHERE `html_block_id` IN($idsAsString)";
        $this->db->query($sql);
        $countAffected = $this->db->countAffected();

        // Delete html block`s description
        $sql = "DELETE FROM `{$p}html_block_description` "
            . "WHERE `html_block_id` IN($idsAsString)";
        $this->db->query($sql);
        $countAffected += $this->db->countAffected();

        return $countAffected;
    }

    /**
     * Drop cache record
     *
     * @param string $hbKey
     * @return bool
     */
    protected function _dropCacheRecord($hbKey)
    {
        if (!is_string($hbKey) || strlen($hbKey) == 0) {
            return false;
        }

        // Select blocks data
        $p = DB_PREFIX;
        $hbKey = $this->db->escape($hbKey);
        $sql = "SELECT * FROM `{$p}html_block` WHERE `key` = '$hbKey'";
        $result = $this->db->query($sql);
        if ($result->num_rows == 0) {
            return false;
        }

        // Load cache wrapper and get cache access object
        if (!isset($this->model_module_cache_wrapper)) {
            $this->load->model('module/cache_wrapper');
        }
        $cache = $this->model_module_cache_wrapper->getCao();

        // For each html block data record de
        $htmlBlocks = $result->rows;
        foreach ($htmlBlocks as $hb) {
            // Select language ids for current block
            $sql = "SELECT `language_id` FROM `{$p}html_block_description` WHERE `html_block_id` = {$hb['html_block_id']}";
            $result = $this->db->query($sql);
            if ($result->num_rows == 0) {
                continue;
            }

            // Delete cache records
            foreach ($result->rows as $hbDesc) {
                $cacheKey = 'html_block_' . md5($hb['key'] . '_' . $hb['store_id'] . '_' . $hbDesc['language_id']);
                $cache->delete($cacheKey);
            }
        }

        return true;
    }

    /**
     * Is data valid?
     *
     * @param array $data
     * @param bool $isNew
     * @return bool
     */
    protected function _isDataValid(array $data, $isNew)
    {

        if (!isset($data['title']) || count($data['title']) == 0
            || !isset($data['content']) || count($data['content']) == 0
            || !isset($data['sort_order'])
            || !isset($data['key'])
            || !isset($data['store_id'])) {

            return false;
        }

        if (strlen($data['key']) == 0) {
            return false;
        }

        if (!is_numeric($data['sort_order'])) {
            return false;
        }

        if (!is_numeric($data['store_id'])) {
            return false;
        }

        if (!$isNew) {

            if (!isset($data['html_block_id'])) {
                return false;
            }

            if (!is_numeric($data['html_block_id'])
                || $data['html_block_id'] < 1) {
                return false;
            }

        }

        return true;
    }

}
