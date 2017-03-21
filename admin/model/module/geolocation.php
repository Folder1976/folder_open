<?php
/**
 *
 * Geolocation model
 *
 * @author n04h <contact@n04h.com>
 */
class ModelModuleGeolocation extends Model {

    const COOKIE_LIFETIME_CITY_NAME = 3600;

    /**
     * IPGeoBase instance
     *
     * @var \IPGeoBase
     */
    private $_ipGeoBase;

    /**
     * @var string
     */
    private $_ipField;

    /**
     * Storage for queries caching
     *
     * @var array
     */
    private $_internalCache = array();

    /**
     * Constructor
     *
     * @param mixed $registry
     *
     * @throws \Exception
     */
    public function __construct($registry)
    {
        parent::__construct($registry);

        $ds = DIRECTORY_SEPARATOR;
        $ipgbPath = realpath(
            dirname(__FILE__) . $ds . '..' . $ds . '..' . $ds . '..'
        ) . $ds . 'orders' . $ds . 'ip_geo_location' . $ds . 'ipgeobase.php';

        if (!file_exists($ipgbPath)) {
            throw new Exception('"IPGeoBase" not found, path: ' . $ipgbPath);
        }

        require_once $ipgbPath;
        $this->_ipGeoBase = new IPGeoBase();

        $this->load->model('module/cache_wrapper');
        $this->load->model('module/innersystem');

        $sysConfig = $this->model_module_innersystem->getSystemConfig();
        if (!isset($sysConfig['CHECKGEOFIELD'])) {
            throw new \Exception('Constant "CHECKGEOFIELD" not found in system config!');
        }
        $this->_ipField = $sysConfig['CHECKGEOFIELD'];
    }

    /**
     * Get record for current remote address
     *
     * @return array|bool|null
     */
    protected function _getRecordForCurrentRemoteAddress()
    {
        $ip = isset($_SERVER[$this->_ipField]) ? $_SERVER[$this->_ipField] : $_SERVER['REMOTE_ADDR'];
        $record = $this->getRecordFromDb($ip);
        if (!$record) {
            return null;
        }

        return $record;
    }

    /**
     * Get field from record by current remote address
     *
     * @param string $field
     * @return null
     */
    protected function _getFieldFromRecordByCurrentRemoteAddress($field)
    {
        $record = $this->_getRecordForCurrentRemoteAddress();

        if (!$record) {
            return null;
        }

        if (!isset($record[$field])) {
            return null;
        }

        return $record[$field];
    }

    /**
     * Get city name (by client IP address)
     *
     * @return string|null
     */
    public function getCityName()
    {
        return $this->_getFieldFromRecordByCurrentRemoteAddress('city');
    }

    /**
     * Get country code (by client IP address)
     *
     * @return string|null
     */
    public function getCountryCode()
    {
        return $this->_getFieldFromRecordByCurrentRemoteAddress('cc');
    }

    /**
     * Get region name
     *
     * @return string|null
     */
    public function getRegionName()
    {
        return $this->_getFieldFromRecordByCurrentRemoteAddress('region');
    }

    /**
     * Get record from the geo database
     *
     * @param string $ip
     * @return array|bool|null
     */
    public function getRecordFromDb($ip)
    {
        if (strlen($ip) == 0) {
            return null;
        }

        if (!isset($this->_internalCache[$ip])) {
            $key = 'geolocation_ip_addr_hash_' . md5($ip);
            $cache = $this->model_module_cache_wrapper->getCao();
            $record = $cache->get($key);
            if (!$record) {
                $record = $this->_ipGeoBase->getRecord($ip);
                $cache->set($key, $record);
            }
            $this->_internalCache[$ip] = $record;
        } else {
            $record = $this->_internalCache[$ip];
        }

        return $record;
    }
}
