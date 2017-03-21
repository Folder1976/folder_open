<?php

/**
 * Class ModelModuleCacheWrapper
 */
class ModelModuleCacheWrapper extends Model {

    const LIFETIME = 3600;

    /**
     * Cache access object
     *
     * @var Cache
     */
    private $_cache;

    /**
     * Constructor
     *
     * @param mixed $registry
     */
    public function __construct($registry)
    {
        parent::__construct($registry);

        $backend = defined('CACHE_BACKEND') ? CACHE_BACKEND : 'file';
        $this->_cache = new Cache($backend, self::LIFETIME);
    }

    /**
     * Get cache access object
     *
     * @return Cache
     */
    public function getCao()
    {
        return $this->_cache;
    }

}