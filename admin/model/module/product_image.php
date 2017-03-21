<?php

class ModelModuleProductImage extends ModelModuleItemConnectedToProduct {

    /**
     * Update product image
     *
     * @param int $productId
     * @param string $imageUrlPath
     * @param array $previewSignatures
     * @return bool
     */
    public function updateProductImage($productId, $imageUrlPath, array $previewSignatures)
    {
        $previewUrlPath = $this->_findImagePreview($imageUrlPath, $previewSignatures);
        if (!$previewUrlPath) {
            return false;
        }

        $p = DB_PREFIX;
        $sql = "UPDATE `{$p}product` SET `image` = '$imageUrlPath', `image_preview` = '$previewUrlPath'"
            . " WHERE `product_id` = $productId";
        $this->db->query($sql);

        return true;
    }

    /**
     * Create relationships with additional product image
     *
     * @param int $productId
     * @param string $imageUrlPath
     * @param array $previewSignatures
     * @return bool
     */
    public function createRelationshipsWithAdditionalProductImage($productId, $imageUrlPath, array $previewSignatures)
    {
        $previewUrlPath = $this->_findImagePreview($imageUrlPath, $previewSignatures);
        if (!$previewUrlPath) {
            return false;
        }

        $p = DB_PREFIX;
        $sql = "INSERT INTO `{$p}product_image` (`product_id`, `image`, `image_preview`)"
            . " VALUES($productId, '$imageUrlPath', '$previewUrlPath')";
        $this->db->query($sql);

        return true;
    }

    /**
     * Drop relationships with additional images
     *
     * @param int $productId
     * @param array $clauses
     */
    public function dropRelationshipsWithAdditionalImages($productId = 0, array $clauses = null)
    {

        $isClausesSpecified = is_array($clauses) && count($clauses) > 0;

        $p = DB_PREFIX;
        $sql = ($productId > 0 || $isClausesSpecified) ? 'DELETE FROM' : 'TRUNCATE TABLE';
        $sql .= " `{$p}product_image`";

        if ($isClausesSpecified) {
            $sql .= ' WHERE';
        }
        if ($productId > 0) {
            if (!$isClausesSpecified) {
                $sql .= ' WHERE';
            }
            $sql .= " `product_id` = $productId";
        }
        if ($isClausesSpecified) {
            foreach ($clauses as $item) {
                $sql .= " $item";
            }
        }

        $this->db->query($sql);
    }

    /**
     * Find image preview
     *
     * @param string $imageUrlPath
     * @param array $previewSignatures
     * @return null|string
     */
    protected function _findImagePreview($imageUrlPath, array $previewSignatures)
    {
        if (count($previewSignatures) == 0) {
            return null;
        }

        $lastPtPos = strrpos($imageUrlPath, '.');
        if ($lastPtPos === false) {
            return null;
        }

        $len = strlen($imageUrlPath);
        $filename = substr($imageUrlPath, 0, $len - ($len - $lastPtPos));
        $extension = substr($imageUrlPath, $lastPtPos + 1);

        $isPreviewFound = false;
        foreach ($previewSignatures as $sgn) {
            $imagePreviewUrlPath = $filename . $sgn . '.' . $extension;
            $imagePreviewUrlFullPath = DIR_IMAGE . $imagePreviewUrlPath;
            $isPreviewFound = file_exists($imagePreviewUrlFullPath);
            if ($isPreviewFound) {
                break;
            }
        }

        return $isPreviewFound ? $imagePreviewUrlPath : null;
    }

    protected function _prepareFileName($raw)
    {
        return substr(
            $raw,
            strpos($raw, '/image')
        );
    }
}