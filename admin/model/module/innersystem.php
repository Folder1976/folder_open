<?php

/**
 *
 * Inner System (/orders) model
 *
 * @author n04h <contact@n04h.com>
 */
class ModelModuleInnersystem extends Model
{

    /**
     * Database access object
     *
     * @var DB
     */
    private $_db;

    /**
     * Table`s prefix
     *
     * @var string
     */
    private $_prefix;

    /**
     * Cookie`s prefix
     *
     * @var string
     */
    private $_cookiesPrefix;

    /**
     * Auth salt
     *
     * @var string
     */
    private $_authSalt;
    private $_cookieSalt;

    /**
     * User can see disable products
     *
     * @var string
     */
    private $_undisableProductsUsers;

    private $_sysConfig;

    private $_isAUHere = null;

    /**
     * Constructor
     *
     * @param mixed $registry
     *
     * @throws Exception
     */
    public function __construct($registry)
    {

        parent::__construct($registry);
        // Parse System configuration
        $ds = DIRECTORY_SEPARATOR;
        $dirname = dirname(__FILE__);
        $sysCfgPath = "$dirname$ds..$ds..$ds..{$ds}orders{$ds}config.php";
        $this->_sysConfig = $this->_parseSystemConfig($sysCfgPath);

        // Connect to the System database
        $this->_db = new DB(
            'mysqli', $this->_sysConfig['shhost'], $this->_sysConfig['shuser'],
            $this->_sysConfig['shpass'], $this->_sysConfig['shname']
        );
        $this->_prefix = $this->_sysConfig['ppt'];
        $this->_cookiesPrefix = $this->_sysConfig['COOKIEPREFIX'];
        $this->_authSalt = $this->_sysConfig['AUTH_SALT_CHECK'];
        if (isset($this->_sysConfig['AUTH_UNDISABLE_PRODUCTS']))
            $this->_undisableProductsUsers = $this->_sysConfig['AUTH_UNDISABLE_PRODUCTS'];
    }

    /**
     * Get system config
     *
     * @return array
     */
    public function getSystemConfig()
    {
        return $this->_sysConfig;
    }

    /**
     * Get system database access object
     *
     * @return DB
     */
    public function getDb()
    {
        return $this->_db;
    }

    /**
     * Get database prefix
     *
     * @return string
     */
    public function getDbPrefix()
    {
        return $this->_prefix;
    }

    /**
     * Get cookies prefix
     *
     * @return string
     */
    public function getCookiesPrefix()
    {
        return $this->_cookiesPrefix;
    }

    /**
     * Get authorisation salt
     *
     * @return string
     */
    public function getAuthSalt()
    {
        return $this->_authSalt;
    }

    /**
     * Get undisable products users
     *
     * @return string
     */
    public function getUndisableProductsUsers()
    {
        return $this->_undisableProductsUsers;
    }

    /**
     * Check user is authorized
     *
     * @return bool
     */
    public function isAuthorizedUserHere()
    {
        if ($this->_isAUHere !== null) {
            return $this->_isAUHere;
        }

        // If some cookies defined
        $cp = $this->_cookiesPrefix;
        if (isset($_COOKIE[$cp . 'password']) && isset($_COOKIE[$cp . 'userid'])
            && is_numeric($_COOKIE[$cp . 'userid'])
        ) {

            // Get data about user from database
            $result = $this->_db->query(
                "SELECT `uadm_id`, `uadm_fio`, `uadm_login`, `uadm_passw`, `u_group` "
                . "FROM `{$this->_prefix}ta_usrsadm_main` "
                . "WHERE `uadm_id` = {$_COOKIE[$cp . 'userid']} "
                . "LIMIT 1"
            );

            if ($result->num_rows == 0) {

                // If nothing found, remove cookies
                $this->_removeCookies();

                return $this->_isAUHere = false;

            } else {

                // Else if found something
                // Check credentials 
                $as = $this->_authSalt;
                $row = $result->row;
                $signature = md5($row['uadm_passw'] . $as);
                if ($signature === $_COOKIE[$cp . 'password']) {

                    //Refresh cookies and return true
                    setcookie($cp . 'password', $signature, time() + 3600 * 48, '/');
                    setcookie($cp . 'userid', $row['uadm_id'], time() + 3600 * 48, '/');
                    setcookie($cp . 'editor', $row['uadm_id'], time() + 3600 * 48, '/');
                    setcookie($cp . 'login', $row['uadm_login'], time() + 3600 * 48, '/');

                    return $this->_isAUHere = true;

                } else {
                    // Invalid credentials
                    // Remove cookies and return false
                    $this->_removeCookies();
                    return $this->_isAUHere = false;
                }

            }

        } else {
            return $this->_isAUHere = false;
        }
    }

    /**
     * Get authorized user login
     *
     * @return mixed|null
     */
    public function getAuthorizedUserLogin()
    {
        if (!$this->isAuthorizedUserHere()) {
            return null;
        }

        $cp = $this->_cookiesPrefix;
        $key = $cp . 'login';

        if (isset($this->request->cookie[$key])) {
            return $this->request->cookie[$key];
        } else {
            return null;
        }
    }
    
    /**
     * Get authorized user info
     *
     * @return mixed|null
     */
    public function getAuthorizedUserInfo()
    {
        if (!$this->isAuthorizedUserHere()) {
            return null;
        }

        $cp = $this->_cookiesPrefix;
        $key = $cp . 'login';

        if (isset($this->request->cookie[$key])) {
            
            $sql = 'SELECT user_id, user_group_id, username, firstname, lastname, email, status FROM user WHERE username = \''.$this->request->cookie[$key].'\';';
            $user = $this->db->query($sql) or die($sql);
            
            return $user->row;
        } else {
            return null;
        }
    }

    /**
     * Get cities list
     *
     * @return array|null
     */
    public function getCitiesList($country_id = null)
    {
        $sql = "SELECT `c`.`CityLable` AS `city`, `cn`.`CountryName` AS `country`, `c`.`CountryID` AS `country_id`  "
            . "FROM `{$this->_prefix}citys` `c` "
            . "INNER JOIN `{$this->_prefix}nomenkl_countries` `cn` ON `cn`.`CountryID` = `c`.`CountryID`";

        if($country_id != null) $sql .= " WHERE `c`.`CountryID` = '$country_id';";
            
        $result = $this->_db->query($sql);

        if ($result === null || $result->num_rows == 0) {
            return null;
        }

        return $result->rows;
    }

    public function getCountryByCity($city)
    {
        $sql = "SELECT `cn`.`CountryName` AS `country` FROM `{$this->_prefix}nomenkl_countries` `cn`"
            . " INNER JOIN `{$this->_prefix}citys` `c` ON `c`.`CountryID` = `cn`.`CountryID`"
            . " WHERE `c`.`CityLable` = '{$this->db->escape($city)}'";

        $result = $this->_db->query($sql);
        if ($result === null || $result->num_rows == 0) {
            return null;
        }

        return $result->row['country'];
    }

    /**
     * Get users list for which need hide hidden for others elements
     *
     * @return array
     */
    public function getUsersListForWhichNeedHideHiddenForOthersElements()
    {
        return ['oleg'];
    }

    /**
     * Get city points data
     *
     * @param null $city
     * @return mixed
     */
    public function getCityPointsData($city = null)
    {
        $sql = 'SELECT DISTINCT `cp`.`PointNazv` AS `name`, `c_tk_p`.`PointCode` AS `code`, `c`.`CityLable` AS `city`,'
            . ' `cp`.`PointID` AS `city_point_id`'
            . " FROM `{$this->_prefix}citys_points` `cp`"
            . " INNER JOIN `{$this->_prefix}citys` `c` ON `c`.`CityID` = `cp`.`CityID`"
            . " INNER JOIN `{$this->_prefix}citys_tk_points` `c_tk_p` ON `c_tk_p`.`PointID` = `cp`.`PointID`";
            

        if ($city !== null) {
            $sql .= ' WHERE `c`.`CityLable` = \'' . $this->getDb()->escape($city) . '\'';
        }
        
        $sql .= ' ORDER BY `c_tk_p`.`PointCode`';
        return $this->getDb()->query($sql)->rows;
    }

    /**
     * Remove cookies
     */
    protected function _removeCookies()
    {
        $cp = $this->getCookiesPrefix();
        setcookie($cp . 'password', '', time() - 3600, '/');
        setcookie($cp . 'userid', '', time() - 3600, '/');
        setcookie($cp . 'editor', '', time() - 3600, '/');
    }

    /**
     * Parse system config
     *
     * @param string $path
     * @return array
     */
    protected function _parseSystemConfig($path)
    {
        // Check file exists
        if (!file_exists($path)) {
            throw new InvalidArgumentException('File not found');
        }

        // Load file
        $cfgContents = file_get_contents($path);

        // Parse
        $result = array();

        $patterns = array(
            'variable_pattern' => '/\$(.+)\s*=\s*(.+);/',
            'constant_pattern' => '/define\((.+)\,(.+)\);/',
        );

        foreach ($patterns as $pattern) {
            if (preg_match_all($pattern, $cfgContents, $matches)) {
                $this->_mergeMatches($matches, $result);
            }
        }

        return $result;
    }

    /**
     * Merge matches
     *
     * @param array $matches
     * @param array $result
     */
    private function _mergeMatches(array $matches, array& $result)
    {
        foreach ($matches[1] as $key => $value) {

            if (!isset($matches[2][$key])) {
                continue;
            }

            $badChars = array('"', '\'', ' ');
            $value = str_replace($badChars, null, $value);
            $result[$value] = str_replace($badChars, null, $matches[2][$key]);
        }
    }
}
