<?php

class ModelModuleRedirect extends Model {

    private $_invalidFieldKey;

    public function __construct($registry) {
        parent::__construct($registry);

        $this->load->language('module/redirect');
    }

    /**
     * Get one
     *
     * @param int $id
     * @return array
     * @throws Exception
     * @throws InvalidArgumentException
     */
    public function getOne($id) {

        if (!is_numeric($id)) {
            throw new InvalidArgumentException($this->langauge->get('error_invalid_redirect_id'));
        }

        $p = DB_PREFIX;
        $sql = "SELECT * FROM `{$p}redirect` WHERE `redirect_id` = $id";
        $result = $this->db->query($sql);
        if ($result->num_rows == 0) {
            throw new Exception(
                sprintf($this->language->get('error_redirect_not_found'), ' #' . $id)
            );
        }

        return $result->row;
    }

    /**
     * Get list
     *
     * @param int $limit
     * @param int $page
     * @return array
     * @throws InvalidArgumentException
     */
    public function getList($limit, $page = 0) {

        if (!is_numeric($limit)
            || !is_numeric($page)) {
            throw new InvalidArgumentException($this->language->get('error_argument_is_not_numeric'));
        }
        if ($limit < 0 || $page < 0) {
            throw new InvalidArgumentException($this->language->get('error_argument_is_lesser_than_zero'));
        }

        $p = DB_PREFIX;
        $offset = $page * $limit;

        $sql = "SELECT * FROM `{$p}redirect` LIMIT $offset,$limit";
        $result = $this->db->query($sql);

        return $result->rows;
    }

    /**
     * Add a new record
     *
     * @param array $data
     * @throws InvalidArgumentException
     * @throws Exception
     */
    public function add(array $data) {

        if (!$this->_isValidData($data)) {
            throw new InvalidArgumentException(
                sprintf($this->language->get('error_data_is_invalid'), $this->_invalidFieldKey)
            );
        }
        $this->_escapeDataItems($data);

        $p = DB_PREFIX;
        $sql = "INSERT INTO `{$p}redirect` (`url_from`, `url_to`, `redirect_type`)"
            . " VALUES('{$data['url_from']}', '{$data['url_to']}', {$data['redirect_type']})";
        $result = $this->db->query($sql);

        if (is_object($result) && isset($result->has_error)) {
            $errorData = $this->db->getErrorData();
            throw new Exception(
                sprintf($this->language->get('error_db'), $errorData['code'], $errorData['message'])
            );
        }
    }

    /**
     * Delete a record by id
     *
     * @param int $redirectId
     * @throws InvalidArgumentException
     * @throws Exception
     */
    public function delete($redirectId) {

        if (!is_numeric($redirectId)) {
            throw new InvalidArgumentException($this->language->get('error_argument_is_not_numeric'));
        }

        $p = DB_PREFIX;
        $sql = "DELETE FROM `{$p}redirect` WHERE `redirect_id` = $redirectId";
        $this->db->query($sql);

        if (is_object($result) && isset($result->has_error)) {
            $errorData = $this->db->getErrorData();
            throw new Exception(
                sprintf($this->language->get('error_db'), $errorData['code'], $errorData['message'])
            );
        }
    }

    /**
     * Get total count
     *
     * @return int
     */
    public function totalCount() {
        $p = DB_PREFIX;
        $sql = "SELECT COUNT(`redirect_id`) AS `count` FROM `{$p}redirect`";
        $result = $this->db->query($sql);
        return (int)$result->row['count'];
    }

    /**
     * Edit a record
     *
     * @param array $data A new data
     * @throws InvalidArgumentException
     * @throws Exception
     */
    public function edit(array $data) {

        if (!$this->_isValidData($data, true)) {
            throw new InvalidArgumentException(
                sprintf($this->language->get('error_data_is_invalid'), $this->_invalidFieldKey)
            );
        }
        $this->_escapeDataItems($data);

        $p = DB_PREFIX;
        $sql = "UPDATE `{$p}redirect` SET `url_from` = '{$data['url_from']}', `url_to` = '{$data['url_to']}',"
            . " `redirect_type` = {$data['redirect_type']}"
            . " WHERE `redirect_id` = {$data['redirect_id']}";
        $this->db->query($sql);

        if (is_object($result) && isset($result->has_error)) {
            $errorData = $this->db->getErrorData();
            throw new Exception(
                sprintf($this->language->get('error_db'), $errorData['code'], $errorData['message'])
            );
        }
    }

    /**
     * Escape data items
     *
     * @param array $data
     */
    protected function _escapeDataItems(array& $data) {
        foreach ($data as $k => $v) {
            if (!$v) {
                continue;
            }
            if (strpos($k, 'url') === 0 && strpos($v, '/') === 0) {
                $v = substr($v, 1);
            }
            $data[$k] = $this->db->escape($v);
        }
    }

    /**
     * Is valid data?
     *
     * @param array $data Data to check
     * @param bool $edit Is it data for edit?
     * @return bool
     */
    protected function _isValidData(array $data, $edit = false) {

        if (count($data) == 0) {
            return false;
        }

        $fields = [
            'url_from',
            'url_to',
            'redirect_type',
        ];
        if ($edit) {
            $fields[] = 'redirect_id';
        }

        foreach ($fields as $key) {
            $isTheRecordValid = true;
            if (!isset($data[$key])) {
                $isTheRecordValid = false;
            } else if (!$data[$key]) {
                $isTheRecordValid = false;
            }
            if (!$isTheRecordValid) {
                $this->_invalidFieldKey = $key;
                return false;
            }
        }

        return true;
    }

}