<?php

class ModelModuleWatermark extends Model {

    public function put($imageResource, $watermarkPath = null)
    {
        // Check image resource
        if (!is_resource($imageResource)) {
            return false;
        }

        // Cehck or prepare watermark path
        if ($watermarkPath === null) {
            $watermarkPath = DIR_IMAGE . 'product_watermark.png';
        }
        if (!file_exists($watermarkPath)) {
            return false;
        }

        $watermarkContents = file_get_contents($watermarkPath);
        $watermark = imagecreatefromstring($watermarkContents);
        unset($watermarkContents);

        $wtWidth = imagesx($watermark);
        $wtHeight = imagesy($watermark);

        imagecopy($imageResource, $watermark, 0, 0, 0, 0, $wtWidth, $wtHeight);
        imagedestroy($watermark);

        return true;
    }

}