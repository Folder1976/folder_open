<?php

/**
 * Class ModelModuleImage
 *
 * Manual model
 *
 * @author Yegor Chuperka (ychuperka@live.com)
 */
class ModelModuleImage extends ModelModuleItemConnectedToProduct
{
    protected function _prepareFileName($raw)
    {
        return substr(
            $raw,
            strpos($raw, '/image')
        );
    }
}