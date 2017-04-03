<?php
/**
 * Class ControllerModuleExcelDocumentsImport
 *
 * Import/export categories and products via xls document.
 *
 * @author Yegor Chuperka <n0044h@gmail.com>
 *
 *
 */
class ControllerModuleExcelDocumentsImport extends Controller
{
    const CATEGORY_ID_DELETED = EXCEL_IMPORT_EXPORT_CATEGORY_ID_REMOVED;

    private $_rootCategoryId = EXCEL_IMPORT_EXPORT_CATEGORY_ID_ROOT;

    private $_categoriesToExcludeFromExport;

    /**
     * процесс
     *
     * @var int
     */
    private $_isProcess = true;

    /**
     * Attributes list
     *
     * @var array
     */
    private $_attributes;

    /**
     * Columns
     *
     * @var array
     */
    private $_columns;

    /**
     * Columns names
     *
     * @var array
     */
    private $_columnsNames;

    /**
     * Columns that excluded from export
     *
     * @var array
     */
    private $_columnsThatExcludedFromExport;

    /**
     * Attributes list received from document
     *
     * @var array
     */
    private $_attributes_from_document;

    /**
     * Attribute groups list
     *
     * @var array
     */
    private $_attribute_groups;

    private $data;

    /**
     * The variable stores max count of product images for the
     * current export process.
     *
     * @var int
     */
    private $_imagesCount;

    /**
     * Attribute group name
     *
     * @var string
     */
    private $_attributeGroupName;

    /**
     * The array stores products images that already updated
     *
     * @var array
     */
    private $_updatedProductsImages = array();

    /**
     * A last exported category url alias
     *
     * @var string
     */
    private $_lastExportedCategoryUrlAlias;

    /**
     * Parent category level (for export purposes)
     *
     * @var int
     */
    private $_realParentCategoryLevel = 0;

    /**
     * Constructor
     *
     * @param
     */
    public function __construct($registry)
    {
        parent::__construct($registry);

        //Load the language file for this module
        $this->load->language('module/excel_documents_import');

        //Set the title from the language file $_['heading_title'] string
        $this->document->setTitle($this->language->get('heading_title'));

        // Load data
        $this->_loadModels();
        $this->_loadTextStrings();
        $this->_loadBreadcrumbs();
        $this->_loadLinks();
        $this->_loadScripts();
        $this->_loadStyles();
        $this->_loadColumnsNames();
        
        // Load PHP Excel
        $php_excel_path = __DIR__ . '/../../../lib/PHPExcel_1.8.0/Classes/PHPExcel.php';
        require_once $php_excel_path;
        PHPExcel_Shared_File::setUseUploadTempDirectory(true);

        // Load attributes
        $this->_attributes_from_document = array();

        // Load attribute groups
        $this->_attribute_groups = $this->model_catalog_attribute_group->getAttributeGroups();

        // Prepare export path
        $this->_exportDocumentFileName = DIR_CACHE . '/excel_export.tmp';

        // Messages
        if (!empty($this->session->data['errors'])) {
            $error = implode(
                '<br>',
                array_unique($this->session->data['errors'])
            );
            $this->data['error'] = $error;
            unset($this->session->data['errors']);
        }
        if (!empty($this->session->data['success'])) {
            $this->data['success'] = $this->session->data['success'];
            unset($this->session->data['success']);
        }

        // Categories to exclude
        $this->_categoriesToExcludeFromExport = array(
            self::CATEGORY_ID_DELETED, // Deleted
            '12908', // Delivery cities
        );

        // Get attribute group name
        $this->_attributeGroupName = $this->language->get('text_attribute_group_name');

    }

    /**
     * Load scripts
     */
    protected function _loadScripts()
    {
        $this->document->addScript('/admin/view/javascript/jquery/ui/jquery-ui-1.8.16.custom.min.js');
    }

    /**
     * Load styles
     */
    protected function _loadStyles()
    {
        $this->document->addStyle('/admin/view/javascript/jquery/ui/themes/ui-lightness/jquery-ui-1.8.16.custom.css');
    }

    /**
     * Load modules
     */
    protected function _loadModels()
    {
        $this->load->model('catalog/attribute');
        $this->load->model('catalog/product');
        $this->load->model('catalog/category');
        $this->load->model('catalog/attribute_group');
        $this->load->model('catalog/manufacturer');
        $this->load->model('catalog/product_model');
    }

    /**
     * Load text strings
     */
    protected function _loadTextStrings()
    {
        $textStrings = array(
            'heading_title',
            'text_module',
            'text_import',
            'text_export',
            'text_import_delete',
            'text_memo',
            'text_input_document',
            'text_manufacturer',
            'text_input_products_update_only',
            'text_input_root_category',
            'text_input_file_type',
            'text_button_upload',
            'text_button_clear',
            'success_import',
            'error_manufacturer_not_found',
            'error_path_is_not_exists',
            'error_parent_category_id_not_found',
            'error_file_not_uploaded',
            'error_exception',
            'error_cant_find_product_url_alias',
            'error_cant_add_new_products',
            'error_invalid_set_of_arguments_passed_into_method',
            'error_file_not_found',
            'error_empty_row',
            'error_column_value_not_found',
            'error_cant_update_product_type',
            'error_cant_update_product_carfit_code',
        );
        
        foreach ($textStrings as $text) {
            $this->data[$text] = $this->language->get($text);
            
        }
        
    }

    /**
     * Load columns
     *
     * @param int $imageCount The max images count
     */
    protected function _loadColumns($imagesCount = 0)
    {
        $columns = array(
            'store_id', 'sort_order', 'model',
            'sku', 'name', 'url_alias', 'parent_url_alias', 'price',
            'quantity', 'title_h1', 'description',
            'mini_description', 'meta_keyword', 'meta_title',
            'meta_description', 'manufacturer',
            'product_type_code', 'product_carfit_codes',
            'is_universal','top','is_menu','is_filter',
            'product_model',
        );

        for ($i = 0; $i < $imagesCount; ++ $i) {
            $columns[] = 'image_' . $i;
        }

        $this->_columns = array();
        foreach ($columns as $number => $name) {
            $this->_columns[$name] = ++$number;
        }

        // Excluded columns
        $this->_columnsThatExcludedFromExport = array(
            'description', 'mini_description', 'meta_description'
        );
    }

    /**
     * Load columns names
     */
    protected function _loadColumnsNames($imagesCount = 0)
    {
        $this->_columnsNames = array(
            'store_id' => $this->language->get('text_column_store_id'),
            'sort_order' => $this->language->get('text_column_sort_order'),
            'model' => $this->language->get('text_column_model'),
            'sku' => $this->language->get('text_column_sku'),
            'name' => $this->language->get('text_column_name'),
            'url_alias' => $this->language->get('text_column_url_alias'),
            'parent_url_alias' => $this->language->get('text_column_parent_url_alias'),
            'price' => $this->language->get('text_column_price'),
            'quantity' => $this->language->get('text_column_quantity'),
            'title_h1' => $this->language->get('text_column_title_h1'),
            'product_model' => $this->language->get('text_column_product_model'),
            'description' => $this->language->get('text_column_description'),
            'mini_description' => $this->language->get('text_column_mini_description'),
             'meta_title' => $this->language->get('text_column_meta_title'),
            'meta_keyword' => $this->language->get('text_column_meta_keyword'),
            'meta_description' => $this->language->get('text_column_meta_description'),
            'manufacturer' => $this->language->get('text_column_manufacturer'),
            'product_type_code' => $this->language->get('text_column_product_type_code'),
            'product_carfit_codes' => $this->language->get('text_column_product_carfit_code'),
            'is_universal' => $this->language->get('text_column_is_universal'),
            'top' => $this->language->get('text_column_top'),
           'is_menu' => $this->language->get('text_column_is_menu'),
           'is_filter' => $this->language->get('text_column_is_filter')
         );

        for ($i = 0; $i < $imagesCount; ++ $i) {

            if ($i == 0) {
                $name = $this->language->get('text_column_main_image');
            } else {
                $name = sprintf($this->language->get('text_column_image_n'), $i);
            }
            
            $this->_columnsNames['image_' . $i] = $name;
        }
        
        $column_names = array();
        foreach ($this->_columnsNames as $index => $text) {
            $column_names[$index] = $text;    
        }
        $this->data['column_names'] = $column_names;
        
    }

    /**
     * Get column name
     *
     * @param string $key
     * @return string
     */
    protected function _getColumnName($key)
    {
        return isset($this->_columnsNames[$key]) ? $this->_columnsNames[$key] : 'Column name not found';
    }

    /**
     * Load links
     */
    protected function _loadLinks()
    {
        $this->data['action'] = $this->url->link('module/excel_documents_import/import', 'token=' . $this->session->data['token'], 'SSL');
        $this->data['export_url_excel'] = html_entity_decode(
            $this->url->link('module/excel_documents_import/downloadExcel', '&token=' . $this->session->data['token'], 'SSL')
        );
        $this->data['export_url_csv'] = html_entity_decode(
            $this->url->link('module/excel_documents_import/downloadCsv', '&token=' . $this->session->data['token'], 'SSL')
        );
    }

    /**
     * Load breadcrumbs
     */
    protected function _loadBreadcrumbs()
    {
        $this->data['breadcrumbs'] = array();

        $this->data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL')
        );

        $this->data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_module'),
            'href' => $this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL')
        );

        $this->data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('module/account', 'token=' . $this->session->data['token'], 'SSL')
        );
    }

    /**
     * Module page
     */
    public function index()
    {

        // Get list of root categories and process it
        // (Categories must be child of root category, must not be
        // "deleted" category, and must have sort order lesser than zero)
        $send = array('sort' => 'name');
        $categories = $this->model_catalog_category->getCategories($send);

        foreach ($categories as $key => $category) {
            if ($category['category_id'] == self::CATEGORY_ID_DELETED
                || $category['parent_id'] != $this->_rootCategoryId) {

                unset($categories[$key]);
                continue;
            }

            $name = $category['name'];
            $semicolonPos = strrpos($name, '&nbsp;');
            if ($semicolonPos) {
                $categories[$key]['name'] = substr($name, $semicolonPos + strlen('&nbsp;'));
            }
        }

        $data['root_categories'] = $categories;
        $data['category_id_root'] = $this->_rootCategoryId;

        //Load brends-manufacturer
        $data['manufacturers'][] = array(
                'manufacturer_code' 	=> 0,
                'name'        		=> "none"
        );
        $results = $this->db->query("SELECT `manufacturer_id` AS manufacturer_code, name FROM ".DB_PREFIX."manufacturer ORDER BY `name` ASC");
        foreach ($results->rows as $result) {
                $data['manufacturers'][] = array(
                        'manufacturer_code' 	=> $result['manufacturer_code'],
                        'name'        		    => $result['name']
                );
        }
        
        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $data = $data + $this->data;

        //Send the output.
        $this->response->setOutput($this->load->view('module/excel_documents_import.tpl', $data));
    }
    /**
     * Get url aliases
     *
     * @param array $ids
     * @param bool $isForProduct
     * @return array|null
     */
    private function _getUrlAliasesByIds(array $ids, $isForProduct)
    {
        if (count($ids) == 0) {
            return null;
        }

        $p = DB_PREFIX;

        $type = $isForProduct ? 'product_id' : 'category_id';
        foreach ($ids as &$id) {
            $id = "'$type=$id'";
        }
        unset($id);
        $inStatementBody = implode(', ', $ids);

        $query = "SELECT `keyword`, SUBSTRING(`query`, 12) AS `product_id` FROM `{$p}url_alias` WHERE `query` IN($inStatementBody)";
        $result = $this->db->query($query);
        if ($result->num_rows == 0) {
            return null;
        }

        $aliases = array();
        foreach ($result->rows as $row) {
            $aliases[$row['product_id']] = $row['keyword'];
        }

        return $aliases;
    }

    /**
     * Download Excel document
     */
    public function downloadExcel()
    {
                
        ini_set('memory_limit', '5024M');
        set_time_limit(0);
        ini_set('max_execution_time', 0);
         
        $path = $_SERVER['DOCUMENT_ROOT'] . '/data';

        if (!file_exists($path) || !is_writable($path)) {
            $this->session->data['errors'][] = sprintf(
                $this->language->get('error_path_is_not_exists'),
                $path
            );
            $this->response->redirect($this->url->link('module/excel_documents_import', 'token=' . $this->session->data['token']));
        }

        $this->_sendHeaders('xlsx');
        //ob_flush();
        //flush();

        $path .= '/export_to_excel.' . date('Y-m-d') . '.csv';
        $this->downloadCsv(null, $path, false);

        $reader = PHPExcel_IOFactory::createReader('CSV');
        $reader->setInputEncoding('WINDOWS-1251')
            ->setDelimiter(';')
            ->setEnclosure('~');
        $doc = $reader->load($path);
        unlink($path);
        unset($reader);

        // Set styles and formats
        $sheet = $doc->getActiveSheet();
        $headerStyle = array(
            'font' => array(
                'name' => 'Arial',
                'size' => 12,
                'bold' => true
            )
        );
        $sheet->getStyle('1:1')->applyFromArray($headerStyle);

        $textColumns = array(
            $this->_columns['sku'],
        );
        $keys = array_keys($this->_columns);
        $column = $this->_columns[$keys[count($keys) - 1]];
        unset($keys);
        foreach ($this->_attributes as $attribute) {
            $textColumns[] = $column;
            $column++;
        }
        unset($column);

        foreach ($textColumns as $index) {
            $columnAsString = PHPExcel_Cell::stringFromColumnIndex($index - 1);
            $sheet->getStyle($columnAsString)->getNumberFormat()
                ->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
        }

        // Cells size
        for ($i = 65; $i < 91; ++ $i) {
            $columnChar = chr($i);
            $sheet->getColumnDimension($columnChar)->setWidth(40);
        }
        unset($columnChar, $i);

        $writer = PHPExcel_IOFactory::createWriter($doc, 'Excel2007');
        $writer->save('php://output');
    }

    /**
     * Export data to CSV document
     *
     * @param array $params
     * @param string $output
     * @param string $output
     * @param bool $sendHeaders
     * @param bool $closeFd
     *
     * @return mixed
     */
    public function downloadCsv(array $params = null, $output = 'php://output', $sendHeaders = true, $closeFd = true)
    {
        // We need more time! MORE! AHAHAH! Ahahahahhahahaha!
        ini_set('max_execution_time', 0);
        ini_set('memory_limit', '5024M');
        set_time_limit(0);

        // Get parent id
        $parentId = $this->_getParentCategoryId();

        // Load attributes
        $this->_attributes = $this->model_catalog_attribute->getAttributesByCategoryIdWhereContentsIsNotEmpty($parentId);

        // Load columns
        $this->_imagesCount = $this->_getMaxImagesCountForCategory($parentId);
        $this->_loadColumns($this->_imagesCount);
        $this->_loadColumnsNames($this->_imagesCount);

        // Create new document
        if ($sendHeaders) $this->_sendHeaders('csv');
        $hnd = fopen($output, 'w');
        $this->_putHeaderIntoCSVDocument($hnd);

        // Put data into document
        $parentCategory = $this->model_catalog_category->getCategory($parentId);
    

        // Parent category data
        $this->_putCategoryIntoCSVDocument($hnd, $parentCategory, 0);

        // Parent category products
        $this->load->model('module/product_fast');
        $products = $this->model_module_product_fast->getProductsByCategoriesIds([$parentId]);
        

        if ($products) {
          
            $data = $this->_prepareIdsAndAttributesAndManufacturersForProducts($products);

            $attributes = $data['attributes'];
            $manufacturers = $data['manufacturers'];
            unset($data);

            foreach ($products as $item) {
                $pAttrs = isset($attributes[$item['product_id']]) ? $attributes[$item['product_id']] : null;
                $pManufs = isset($manufacturers[$item['product_id']]) ? $manufacturers[$item['product_id']] : null;

                $item['title_h1'] = $item['name'];
                $item['is_universal'] = 1;
                $item['product_model'] = 1;
                
                $this->_putProductIntoCSVDocument($hnd, $item, $pAttrs, $pManufs);
            }
        }

        // Load models
        $this->load->model('catalog/category');
        $this->load->model('module/category_fast');
        $this->load->model('module/product_fast');

        // Get parent category level
        $levels = $this->model_module_category_fast->getCategoriesLevelsByCategoriesIds([$parentId]);
        $this->_realParentCategoryLevel = $levels[0]['level'];
        unset($levels);

        // Other categories (parent category children) and its products
        $this->_putDataIntoDocument($parentId, array($this, '_putCategoryIntoCSVDocument'), array($this, '_putProductIntoCSVDocument'), $hnd);

        if ($closeFd) {
            fclose($hnd);
        } else {
            return $hnd;
        }
    }

    /**
     * Get max images count for category
     *
     * @param int $categoryId
     * @return int
     */
    protected function _getMaxImagesCountForCategory($categoryId)
    {
        $p = DB_PREFIX;
        $sql = 'SELECT MAX(`counted`) FROM ('
                . ' SELECT COUNT(`pi`.`product_image_id`) AS `counted`'
                . " FROM `{$p}category_path` `cp`"
                . " INNER JOIN `{$p}product_to_category` `p2c`"
                . ' ON `cp`.`category_id` = `p2c`.`category_id`'
                . " INNER JOIN `{$p}product_image` `pi`"
                . ' ON `pi`.`product_id` = `p2c`.`product_id`'
                . " WHERE `cp`.`path_id` = $categoryId"
                . ' GROUP BY `pi`.`product_id`'
            . ' ) AS `counts`';
        $result = $this->db->query($sql);

        if ($result->num_rows == 0) {
            return 1;
        } else {
            return $result->row['MAX(`counted`)'] + 1;
        }
    }

    /**
     * Add slashes to array items
     *
     * @param array $array
     */
    protected function _addSlashesToArrayItems(array& $array)
    {
        // Turned off
        return;

        array_walk($array, function (&$item, $key) { $item = addslashes($item); });
    }

    /**
     * Prepapre ids and attributes and manufacturers for products
     *
     * @param array $products
     * @return array
     */
    private function _prepareIdsAndAttributesAndManufacturersForProducts(array $products)
    {
        // Prepare products and get products ids
        $ids = array();
        foreach ($products as $key => $item) {
            $ids[] = $item['product_id'];
        }

        // Get attributes
        $attributes = $this->model_module_product_fast->getProductsAttributesByProductsIds($ids);
        if ($attributes) {
            foreach ($attributes as $key => $item) {
                $attributes[$item['product_id']][$item['attribute_id']][$item['attribute_value_id']] = $item;
                unset($attributes[$key]);
            }
        }

        // Get manufacturers
        $manufacturers = $this->model_module_product_fast->getManufacturersByProductsIds($ids);
       
        if ($manufacturers) {
            foreach ($manufacturers as $key => $item) {
                $manufacturers[$item['product_id']] = $item;
                unset($manufacturers[$key]);
            }
        }

        return [
            'ids' => $ids,
            'attributes' => $attributes,
            'manufacturers' => $manufacturers
        ];
    }

    /**
     * Put data into document
     *
     * @param int $parentId
     * @param callable $callbackPutCategory
     * @param callable $callbackPutProduct
     * @param mixed $handleDoc
     * @return bool
     */
    protected function _putDataIntoDocument($parentId, $callbackPutCategory, $callbackPutProduct, $handleDoc)
    {
        // Check args
        if (!is_numeric($parentId)
            || !is_callable($callbackPutCategory)
            || !is_callable($callbackPutProduct)) {
            return false;
        }

        $subCategories = $this->model_module_category_fast->getCategories($parentId);

        $limit = 24;
        $scCount = count($subCategories);
        $steps = ceil($scCount / $limit);

        for ($i = 0, $offset = 0; $i < $steps; ++ $i, $offset += $limit) {

            // Get products
            $ids = array();
            $currentStepSubCategories = array_slice($subCategories, $offset, $limit);
            foreach ($currentStepSubCategories as $item) {
                if (in_array($item['category_id'], $this->_categoriesToExcludeFromExport)) {
                    continue;
                }
                $ids[] = $item['category_id'];
            }
            $ids[] = $parentId;

            // Get categories levels
            $levels = $this->model_module_category_fast->getCategoriesLevelsByCategoriesIds($ids);
            foreach ($levels as $key => $item) {
                $levels[$item['category_id']] = $item['level'] - $this->_realParentCategoryLevel;
                unset($levels[$key]);
            }

            // Get products
            $products = $this->model_module_product_fast->getProductsByCategoriesIds($ids);
            if ($products) {
                $data = $this->_prepareIdsAndAttributesAndManufacturersForProducts($products);
                $attributes = $data['attributes'];
                $manufacturers = $data['manufacturers'];
                unset($data);

                foreach ($products as $key => $item) {
                    $products[$item['category_id']][] = $item;
                    unset($products[$key]);
                }
            }

            // Put data into document
            foreach ($currentStepSubCategories as $category) {
                // Put category into document
                $level = isset($levels[$category['category_id']]) ? $levels[$category['category_id']] : 0;
                call_user_func($callbackPutCategory, $handleDoc, $category, $level);

                // Put products into document
                if ($products && isset($products[$category['category_id']])) {
                    foreach ($products[$category['category_id']] as $item) {

                        $productAttributes = isset($attributes[$item['product_id']])
                            ? $attributes[$item['product_id']] : null;
                        $productManufacturer = isset($manufacturers[$item['product_id']])
                            ? $manufacturers[$item['product_id']] : null;
                        call_user_func($callbackPutProduct, $handleDoc, $item, $productAttributes, $productManufacturer);

                        unset($products[$category['category_id']]);
                    }
                }

                // Put current category children data into document
                $this->_putDataIntoDocument($category['category_id'], $callbackPutCategory, $callbackPutProduct, $handleDoc);

            }
        }

        gc_collect_cycles();

        return true;
    }

    /**
     * Get parent category id
     *
     * @return int
     */
    protected function _getParentCategoryId()
    {
        if (!isset($_REQUEST['parent_category_id'])) {
            $this->session->data['errors'][] = $this->language->get('error_parent_category_id_not_found');
            $this->response->redirect($this->url->link('module/excel_documents_import', 'token=' . $this->session->data['token']));
        }

        $id = (int)$_GET['parent_category_id'];
        
        return $id;
    }

    /**
     * Send attachment headers with specified extension
     *
     * @param string $ext
     */
    protected function _sendHeaders($ext)
    {
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename=store-items.' . date('Y-m-d_H:i') . '.' . $ext);
    }

    /**
     * Trim value... I don`t know about this shit..=)
     *
     * @param $value
     */
    public function trim_value(&$value)
    {
        $value = trim(urldecode(str_replace('%26nbsp%3B', '', urlencode($value))));
    }

    /**
     * Data import
     */
    public function import()
    {
        // We need more time! MORE! AHAHAH! Ahahahahhahahaha!
        ini_set('max_execution_time', 0);
        ini_set('memory_limit', '5024M');
        set_time_limit(0);

        // Check file
        if (isset($_FILES['data']) AND strlen($_FILES['data']['tmp_name']) == 0 || !file_exists($_FILES['data']['tmp_name']) || !is_uploaded_file($_FILES['data']['tmp_name'])) {
            $this->session->data['errors'][] = $this->language->get('error_file_not_uploaded');
            $this->response->redirect($this->url->link('module/excel_documents_import', 'token=' . $this->session->data['token']));
        }

        // Get flag values
        $should_update_only_products = isset($_POST['jst-upd']);
        $document_type = substr($_FILES['data']['type'], strrpos($_FILES['data']['type'], '/') + 1);

        // Load attributes
        $this->_attributes = $this->model_catalog_attribute->getAttributes();

        // Select document type
        $isImportSucceed = false;
        switch ($document_type) {

            case 'vnd.ms-excel':
            case 'vnd.openxmlformats-officedocument.spreadsheetml.sheet':

                // Try to load
                $doc = null;
                try {
                    $doc = PHPExcel_IOFactory::load($_FILES['data']['tmp_name']);
                } catch (PHPExcel_Reader_Exception $ex) {
                    $this->session->data['errors'][] = sprintf(
                        $this->language->get('error_exception'), $ex->getMessage()
                    );
                    $this->response->redirect($this->url->link('module/excel_documents_import', 'token=' . $this->session->data['token']));
                }

                // Process document
                $isImportSucceed = $this->_processDocument($doc, $should_update_only_products);

                break;

            case 'csv':

                $hnd = fopen($_FILES['data']['tmp_name'], 'r');

                // Process CSV document
                $isImportSucceed = $this->_processCSVDocument($hnd, $should_update_only_products);

                break;
        }

        // Redirect to module page
        if ($isImportSucceed) {
         
            if (!isset($this->session->data['errors']) || count($this->session->data['errors']) == 0) {
                $this->session->data['success'] = $this->language->get('success_import');
            }

            /*
            // Flush cache
            if (class_exists('Memcache')) {
                $m = new Memcache(CACHE_HOSTNAME, CACHE_PORT);
                $m->flush();
                sleep(1);
                $m->set(CACHE_PREFIX .'key', 'value'); // repopulate the cache
            }*/
        }
        $this->response->redirect($this->url->link('module/excel_documents_import', 'token=' . $this->session->data['token']));
    }

    /**
     * Set columns indexes
     *
     * @param array $row
     */
    protected function _setColumnsIndexes(array $row)
    {
        foreach ($this->_columns as $key => $value) {
            $this->_columns[$key] = false;
        }

        foreach ($row as $index => $name) {

            if (!in_array($name, $this->_columnsNames)) {
                continue;
            }

            $columnKey = null;
            foreach ($this->_columnsNames as $key => $item) {
                if ($item != $name) {
                    continue;
                }
                $columnKey = $key;
                break;
            }

            $this->_columns[$columnKey] = $index;
        }
        
        //Проверка на обязательные поля
        if(!$this->_columns['name']){
            $this->session->data['errors'][] = 'Не найдено обязательного поля Название товара! Процесс остановлен.';
            return false;
        }
        if(!$this->_columns['model']){
            $this->session->data['errors'][] = 'Не найдено обязательного поля СисАртикл товара! Процесс остановлен.';
            return false;
        }
        
        return true;

    }

    /**
     * Process document
     *
     * @param PHPExcel $doc
     * @param bool $update_only_products
     *
     * @return bool
     */
    protected function _processDocument(PHPExcel $doc, $update_only_products = false)
    {
        $sheet = $doc->getActiveSheet();
        $is_header = true;
        $processed_categories = array();
        foreach ($sheet->getRowIterator() as $row) {

            //Остановили процесс
            if(!$this->_isProcess) return false;
        
            // If header
            if ($is_header) {
                $is_header = false;

                // Prepare row as array
                $columns = $this->_getDataFromRow($row);

                // Get count of images
                $this->_imagesCount = $this->_getCountOfImagesColumnsInDocument($columns);
                $this->_loadColumns($this->_imagesCount);
                $this->_loadColumnsNames($this->_imagesCount);

                // Set columns indexes
                if(!$this->_setColumnsIndexes($columns)) return false;

                 
                // Check attributes existing and create each who not exists
                $this->_checkAttributesAndCreateIfNotExists($row);

                continue;
            }

            // Get data from row
            $data_from_row = $this->_getDataFromRow($row);
            if (count($data_from_row) == 0) {
                break;
            }

            $this->_prepareRowForDatabase($data_from_row);

            if (!$update_only_products)
                if (!$this->_tryToFindAndProcessCategory($data_from_row, $processed_categories)) {
                    continue;
                }

            $res = $this->_tryToFindAndProcessProduct($data_from_row, $processed_categories, $update_only_products);
            if (!$res){
                continue;
            }

        }

        return true;
    }

    /**
     * Get count of images columns in document
     *
     * @param array $row
     * @return int
     */
    protected function _getCountOfImagesColumnsInDocument(array $row)
    {
        $count = 0;
        foreach ($row as $item) {
            if (mb_stripos($item, $this->language->get('text_image')) !== false) {
                ++ $count;
            }
        }

        return $count;
    }

    /**
     * Process CSV document
     *
     * @param resource $hnd
     * @param bool $update_only_products
     *
     * @return bool
     */
    protected function _processCSVDocument($hnd, $update_only_products = false)
    {
        $is_header = true;
        $processed_categories = array();
        while (($row_from_csv = $this->_fgetCsv($hnd, null, ';')) AND $this->_isProcess) {

            $this->_convertArrayFieldsEncoding($row_from_csv, 'UTF-8', 'WINDOWS-1251');

            $this->_prepareRowForDatabase($row_from_csv);

            if ($is_header) {
                $is_header = false;

                // Get count of images
                $this->_imagesCount = $this->_getCountOfImagesColumnsInDocument($row_from_csv);
                $this->_loadColumns($this->_imagesCount);
                $this->_loadColumnsNames($this->_imagesCount);

                // Set columns indexes and create attributes
                if(!$this->_setColumnsIndexes($row_from_csv)) return false;
                $this->_checkAttributesAndCreateIfNoExistsFromCSV($row_from_csv);

                continue;
            }

            if (!$update_only_products) {
                if (!$this->_tryToFindAndProcessCategory($row_from_csv, $processed_categories)) {
                    continue;
                }
            }
            
            $res = $this->_tryToFindAndProcessProduct($row_from_csv, $processed_categories, $update_only_products);
            if (!$res){
                continue;
            }
        }

            // ================= Ч И С Т И М   К О Р Е Н Ь   О Т   Н Е Н У Ж Н Ы Х   Ф О Т О ============================================================                      
            //Удаляем из корня
            //Итак - мы фиг знаем куда и зачем еще будет использоваться это фото.
            //Поэтому прежде чем удалить - проверим не испозьзуется ли оно больше ни у кого в корне
            $main_path = DIR_IMAGE . 'data/products_pictures/';
            
            if ($handle = opendir($main_path) AND $this->_isProcess) {
                 /* Именно этот способ чтения элементов каталога является правильным. */
                while (false !== ($file = readdir($handle)) AND $this->_isProcess) { 
                    
                    if($file != "." && $file != ".." && !is_dir($main_path.$file)){
                        
                        $oldImageFullPath = 'data/products_pictures/'.$file;
                        
                        $sql = 'SELECT product_id FROM '.DB_PREFIX.'product WHERE image = "'.$oldImageFullPath.'" OR image_preview = "'.$oldImageFullPath.'";';
                        $r_i = $this->db->query($sql);
            
                        //Провим в таблице продукта и в таблице фоток                
                        if($r_i->num_rows == 0){
                            $sql = 'SELECT product_id FROM '.DB_PREFIX.'product_image WHERE image = "'.$oldImageFullPath.'" OR image_preview = "'.$oldImageFullPath.'";';
                            $r_i = $this->db->query($sql);
                            
                            if($r_i->num_rows == 0){
                                
                                $this->session->data['errors'][] = "<br>Ни к чему не привязан = $file\n";
                                //unlink($oldImageFullPath);
                            
                            }
                        }
                    }
                    
                }
            
                closedir($handle); 
            }
            //======================================================================================================

        
        return true;
    }

    /**
     * Prepare row for database
     *
     * @param array $row
     */
    private function _prepareRowForDatabase(array& $row)
    {
        // Have problems with slashes
        return;
        foreach ($row as $key => $item) {
            $row[$key] = $this->db->escape($item);
        }
    }

    /**
     * Create default attribute group, if not exists
     *
     * @return int
     */
    private function _createDefaultAttributeGroupIfNotExists()
    {
        // Check attribute group and create if not exists
        $attribute_group_id = 0;
        foreach ($this->_attribute_groups as $ag) {
            if ($ag['name'] === $this->_attributeGroupName) {
                $attribute_group_id = $ag['attribute_group_id'];
                break;
            }
        }
        if ($attribute_group_id == 0) {
            // Create new one
            $data = array(
                'sort_order' => 0,
                'attribute_group_description' => array(
                    $this->config->get('config_language_id') => array(
                        'name' => $this->_attributeGroupName
                    )
                )
            );

            $this->model_catalog_attribute_group->addAttributeGroup($data);

            $query = $this->db->query('SELECT LAST_INSERT_ID() AS `lid`');
            $attribute_group_id = $query->row['lid'];
        }

        return $attribute_group_id;
    }

    /**
     * Find attribute by name
     *
     * @param string $attribute_name
     * @return mixed
     */
    protected function _findAttributeByName($attribute_name)
    {
        foreach ($this->_attributes as $attr) {
            if ($attr['name'] === $attribute_name)
                return $attr['attribute_id'];
        }

        return 0;
    }

    /**
     * Find attribute by name
     *
     * @param string $attribute_name
     * @return attribute type
     */
    protected function _findAttributeTypeByName($attribute_name)
    {
        foreach ($this->_attributes as $attr) {
            if ($attr['name'] === $attribute_name)
                return $attr['attribute_type'];
        }

        return 0;
    }

    /**
     * Create attribute
     *
     * @param int $attribute_group_id
     * @param string $attribute_name
     * @return mixed
     */
    protected function _createAttribute($attribute_group_id, $attribute_name)
    {
        $data = array(
            'attribute_group_id' => $attribute_group_id,
            'sort_order' => 0,
            'attribute_description' => array(
                $this->config->get('config_language_id') => array(
                    'name' => $attribute_name
                )
            )
        );

        $this->model_catalog_attribute->addAttribute($data);

        $query = $this->db->query('SELECT LAST_INSERT_ID() AS `lid`');
        return $query->row['lid'];
    }

    /**
     * Is valid attribute name?
     *
     * @param string $name
     * @param int $index
     * @return bool
     */
    protected function _isValidAttributeName($name, $index)
    {
        if (strlen($name) == 0) return false;
        return true;
    }

    /**
     * Check attribute name and create new if need,
     * than add new record into attribute from document list.
     *
     * @param int $attribute_group_id
     * @param int $column
     * @param string $value
     * @return bool
     */
    private function _checkAttributeNameAndCreateNewIfNeedThanAddNewRecordIntoAttributesFromDocumentList(
        $attribute_group_id, $column, $value
    )
    {
        // Get attribute name
        $attribute_name = trim($value);
        if (!$this->_isValidAttributeName($attribute_name, $column)) {
            return false;
        }

        // Try to find attribute
        $attribute_id = $this->_findAttributeByName($attribute_name);

        // Try to find attribute
        $attribute_type = $this->_findAttributeTypeByName($attribute_name);
        
        // Nothing found, create new one
        if ($attribute_id == 0) {
            $this->session->data['errors'][] = 'Не смог найти поле или атрибут: '.$attribute_name. '';
            return false;
            //Не создаем атрибут!
            //$attribute_id = $this->_createAttribute($attribute_group_id, $attribute_name);
        }

        // Store
        $this->_attributes_from_document[] = array(
            'attribute_id' => $attribute_id,
            'name' => $attribute_name,
            'attribute_type' => $attribute_type,
            'column' => $column
        );

        return true;
    }

    /**
     * Check attributes and create if not exists
     *
     * @param PHPExcel_Worksheet_Row $header
     */
    protected function _checkAttributesAndCreateIfNotExists(PHPExcel_Worksheet_Row $header)
    {

        $attribute_group_id = $this->_createDefaultAttributeGroupIfNotExists();

        foreach ($header->getCellIterator() as $cell) {

            // Process only if it is column for attributes
            $value = $cell->getValue();
            if (in_array($value, $this->_columnsNames)) {
                continue;
            }

            if (!$this->_checkAttributeNameAndCreateNewIfNeedThanAddNewRecordIntoAttributesFromDocumentList(
                $attribute_group_id, PHPExcel_Cell::columnIndexFromString($cell->getColumn()) - 1, $cell->getValue()
            )) {
                continue;
            }
        }
    }

    /**
     * Check attributes in CSV document and create
     * if not exists
     *
     * @param array $row
     */
    private function _checkAttributesAndCreateIfNoExistsFromCSV(array $row)
    {

        $attribute_group_id = $this->_createDefaultAttributeGroupIfNotExists();

        foreach ($row as $key => $item) {

            // Process only if it is column for attributes
            if (in_array($item, $this->_columnsNames) || strlen($item) == 0)
                continue;

            if (!$this->_checkAttributeNameAndCreateNewIfNeedThanAddNewRecordIntoAttributesFromDocumentList(
                $attribute_group_id, $key, $item
            )) {
                continue;
            }
        }
    }



    /**
     * Clean item name before import
     *
     * @param string $name
     * @return string
     */
    protected function _cleanItemName($name)
    {
        return str_replace('"', '&quot;', $name);
    }

    /**
     * Prepare keyword
     *
     * @param string $kw
     * @return string
     */
    protected function _prepareKeyword($kw)
    {
        // Remove spaces
        $kw = trim($kw);

        // Remove first slash if exists
        if ($kw{0} == '/') {
            $kw = substr($kw, 1);
        }

        // Remove last slash if exists
        $lastSlashPos = strrpos($kw, '/');
        if (!$lastSlashPos) {
            return $kw;
        }

        $len = strlen($kw);
        if ($len > $lastSlashPos + 1) {
            return $kw;
        }

        return substr($kw, 0, $len - ($len - $lastSlashPos));
    }

    /**
     * Get value from row or from old data
     *
     *
     * @param string $key
     * @param array $dataFromRow
     * @param array $oldData
     * @param mixed $default
     * @return int
     */
    private function _getValueFromRowOrFromOldData($key, array $dataFromRow, array $oldData = null, $default = 0)
    {
        if (isset($this->_columns[$key]) && $this->_columns[$key] !== false
            && isset($dataFromRow[$this->_columns[$key]]) && strlen($dataFromRow[$this->_columns[$key]]) > 0) {
            return $dataFromRow[$this->_columns[$key]];
        } else if ($oldData !== null && isset($oldData[$key])) {
            $item = $oldData[$key];
            if (is_string($item) && strlen($item) > 0) {
                return $item;
            } else if (is_array($item) && count($item) > 0) {
                return $item;
            } else {
                return $default;
            }
        } else {
            return $default;
        }
    }

    /**
     * Get store ids
     *
     * @param array $dataFromRow
     * @param array $oldData
     * @return array|int
     */
    protected function _getStoreIds(array $dataFromRow, array $oldData = null) {
        $store_ids = $this->_getValueFromRowOrFromOldData('store_id', $dataFromRow, $oldData);
        if (strlen($store_ids) > 0 && substr_count($store_ids, ',') > 0) {
            $store_ids = explode(',', $store_ids);
            foreach ($store_ids as $key => $value) {
                $store_ids[$key] = (int)$value;
            }
            $store_ids = array_unique($store_ids);
        } else if (strlen($store_ids) > 0 && is_numeric($store_ids)) {
            $store_ids = array($store_ids);
        } else {
            $store_ids = array(0);
        }

        return $store_ids;
    }

    
    /*
     *Проверяем есть ли такая категория главная
     */
    protected function _isMainCategoryPresent($name) {
        
        return true;
    
        header("Content-Type: text/html; charset=UTF-8");
  
        $sql = 'SELECT C.category_id FROM '.DB_PREFIX.'category_description CD
                        LEFT JOIN '.DB_PREFIX.'category C ON C.category_id = CD.category_id
                        WHERE CD.name = "'.$name.'" AND C.parent_id = "2" LIMIT 0, 1;';
        $r = $this->db->query($sql) or die($sql);
        
        //$this->session->data['errors'][] = $sql;
        
        if($r->num_rows){
            return true;
        }
        
        return false;
    }
    /**
     * Try to find and process category
     *
     * @param array $data_from_row
     * @param array & $processed_categories
     * @throws InvalidArgumentException
     *
     * @return bool
     */
    protected function _tryToFindAndProcessCategory(array $data_from_row, array& $processed_categories)
    {
       
        // Category don`t have model
        if ($this->_columns['model'] !== false
            && isset($data_from_row[$this->_columns['model']])
            && strlen($data_from_row[$this->_columns['model']]) != 0) {
            
             return true;
        }

        // Check required fields
        $required_fields = array(
            $this->_columns['name'] => sprintf($this->language->get('error_empty_required_field'), 'name'),
            $this->_columns['url_alias'] => sprintf($this->language->get('error_empty_required_field'), 'url_alias'),
        );

        if (!$this->_checkRequiredFields($required_fields, $data_from_row)) {
               return false;
        }

        // Get name and level
        $name = $data_from_row[$this->_columns['name']];
        $level = substr_count($name, '!');

        // Prepare category data
        $name = str_replace('!', null, trim($name));

        //Проверяем корневую категорию
        if($level == 0){
            if(!$this->_isMainCategoryPresent($name)){
                
                $this->session->data['errors'][] = 'Не найдена корневая категория: '.$name;
                $this->session->data['errors'][] = 'Процесс остановлен.';
                
                $this->_isProcess = false;

                return false;
            }
        }
    
        
        // Get category old data
        $keyword = $this->_prepareKeyword($data_from_row[$this->_columns['url_alias']]);
        $oldData = null;
        if (strlen($keyword) > 0) {
            $query = $this->db->query(
                'SELECT SUBSTRING(`query`, 13) AS `category_id` FROM `' . DB_PREFIX . 'url_alias` WHERE `keyword` = \'' . $keyword . '\''
            );
            if ($query->num_rows > 0) {
                $oldData = $this->model_catalog_category->getCategory($query->row['category_id']);
            }
            unset($query);
        }

        // Prepare category new data
        $data = array(
            'category_store' => $this->_getValueFromRowOrFromOldData('store_id', $data_from_row, $oldData),
            'sort_order' => $this->_getValueFromRowOrFromOldData('sort_order', $data_from_row, $oldData),
            'is_universal' => $this->_getValueFromRowOrFromOldData('is_universal', $data_from_row, $oldData),
            'top' => $this->_getValueFromRowOrFromOldData('top', $data_from_row, $oldData),
            'is_menu' => $this->_getValueFromRowOrFromOldData('is_menu', $data_from_row, $oldData),
            'is_filter' => $this->_getValueFromRowOrFromOldData('if_filter', $data_from_row, $oldData),
            'parent_id' => $this->_tryToFindParentId($processed_categories, $level),
            'column' => 0,
            'status' => 1,
            'keyword' => $keyword,
            'level' => $level,
            'category_description' => array(
                $this->config->get('config_language_id') => array(
                    'name' => $name,
                    'meta_title' => str_replace('&quot;', '"', $name),
                    'meta_description' => $this->_getValueFromRowOrFromOldData('meta_description', $data_from_row, $oldData, ''),
                    'meta_keyword' => $this->_getValueFromRowOrFromOldData('meta_keyword', $data_from_row, $oldData, ''),
                    'meta_title' => str_replace('&quot;', '"', $this->_getValueFromRowOrFromOldData('meta_title', $data_from_row, $oldData, '')),
                    'title_h1' => $this->_getValueFromRowOrFromOldData('title_h1', $data_from_row, $oldData, ''),
                    'description' => $this->_getValueFromRowOrFromOldData('description', $data_from_row, $oldData, '')
                )
            )
        );

        // If category already exists (by seo keyword), update it
        $category_id = ($oldData !== null && isset($oldData['category_id'])) ? $oldData['category_id'] : 0;
        if ($category_id != 0) {
            $this->model_catalog_category->editCategory($category_id, $data);
        }

        // If category not found, create new one
        if ($category_id == 0) {
            $category_id = $this->model_catalog_category->addCategory($data);
        }

        //Если к категории прилетели модели
        if(isset($this->_columns['product_carfit_codes'])){
            if(isset($data_from_row[$this->_columns['product_carfit_codes']])){
                //Если оно есть и пустое - почистим это
               //if($data_from_row[$this->_columns['product_carfit_codes']] != ''){
                    //$this->model_catalog_category->editCategoryCarfitFromStrind($category_id, $data_from_row[$this->_columns['product_carfit_codes']]);
                //}
            }
        }
        
        $data['category_id'] = $category_id;

        // Store data
        $processed_categories[] = $data;

        return true;
    }

    /**
     * Try to find parent id
     *
     * @param array $processed_categories
     * @param int $level
     * @return int
     */
    protected function _tryToFindParentId(array $processed_categories, $level = 0)
    {
        if ($level == 0)
            return $this->_rootCategoryId;

        $reversed = array_reverse($processed_categories);
        $need_to_find_level = $level - 1;
        foreach ($reversed as $category) {
            if ($category['level'] == $need_to_find_level)
                return $category['category_id'];
        }
    }

    /**
     * Try to find and process product
     *
     * @param array $data_from_row
     * @param array $processed_categories
     * @param bool $update_only_products
     *
     * @return bool
     */
    protected function _tryToFindAndProcessProduct(array $data_from_row, array $processed_categories, $update_only_products = false)
    {
        
        // Each product should have model
        if ($this->_columns['model'] === false
            || !isset($data_from_row[$this->_columns['model']])
            || strlen($data_from_row[$this->_columns['model']]) == 0) {
            return true;
        }

        
         //Если прилетел флаг удаления продуктов
        if(isset($this->request->post ['delete_btn'])){
            
            $model = $data_from_row[$this->_columns['model']];
            
            $product_id = $this->model_catalog_product->getProductIdOnModel($model);
            
            $this->session->data['errors'][] = 'Удалил: '.$model. ' => ['. $product_id.']';
            
            if($product_id AND $product_id > 0){
                $this->model_catalog_product->deleteProduct($product_id);
               
               /* Счетчик удаленных товаров
                if(isset($this->session->data['errors']['count'])){
                    $this->session->data['errors']['count'] = (int)$this->session->data['errors']['count'] + 1;
                }else{
                    $this->session->data['errors']['count'] = 1;
                }*/
            }
           
            return true;
        }
        
        
        // Check required fields
        $required_fields = array(
            $this->_columns['model'] => sprintf($this->language->get('error_empty_required_field'), 'model'),
        );
        if (!$this->_checkRequiredFields($required_fields, $data_from_row)) {
            return false;
        }

        // Get manufacturer id
        if ($this->_columns['manufacturer'] !== false
            && isset($data_from_row[$this->_columns['manufacturer']])
            && strlen($data_from_row[$this->_columns['manufacturer']]) > 0) {
                $m_name = $data_from_row[$this->_columns['manufacturer']];
                $manufacturer_id = $this->_getManufacturerIdByName($m_name);
                //$this->session->data['errors'][] = $m_name;
        } else {
            $manufacturer_id = 0;
        }

        //Если не найден бренд - отваливаемся нафиг
        if($manufacturer_id == 0 AND !isset($data_from_row[$this->_columns['manufacturer']])) {
            //return false;
        }elseif($manufacturer_id == 0 AND isset($data_from_row[$this->_columns['manufacturer']])) {
            $this->session->data['errors'][] = sprintf(
                $this->language->get('error_manufacturer_not_found'), $data_from_row[$this->_columns['manufacturer']]
            );
        }
        
        //$this->session->data['errors'][] = '==>'.$manufacturer_id. ' '.$m_name.' '.$this->_columns['manufacturer'];        
  
        // Prepare data
        $product_attributes = array();
        foreach ($this->_attributes_from_document as $attribute) {
            $product_attributes[] = array(
                'attribute_id' => $attribute['attribute_id'],
                'attribute_type' => $attribute['attribute_type'],
                'product_attribute_description' => array(
                    $this->config->get('config_language_id') => array(
                        'text' => isset($data_from_row[$attribute['column']]) ? $data_from_row[$attribute['column']] : '',
                    )
                )
            );

        }

        $lastProcessedCategoryId = $this->_getLastProcessedCategoryId($processed_categories);
        if ($lastProcessedCategoryId > 0) {
            $product_category = array($lastProcessedCategoryId);
            $lastProcessedCategory = $processed_categories[count($processed_categories) - 1];
            $keyword = $lastProcessedCategory['keyword']
                . '/' . $data_from_row[$this->_columns['model']];
        } else {
            $product_category = null;
            $keyword = null;
        }

        // Get product old data
        $p = DB_PREFIX;
        $model = $this->db->escape($data_from_row[$this->_columns['model']]);
        
        $result = $this->db->query("SELECT `product_id` FROM `{$p}product` WHERE `model` = '$model'");
        unset($model);

        $oldData = null;
        if ($result->num_rows > 0) {
            $pId = $result->row['product_id'];
            $oldData = $this->model_catalog_product->getProduct($pId);

            $productType = $this->model_catalog_product->getProductType($pId);
            if ($productType) {
                $oldData['product_type_code'] = $productType['product_type_kod'];
            }

            $productCarFitData = false; //$this->model_catalog_product->getProductCarFit($pId);
            if ($productCarFitData) {
                $codes = [];
                foreach ($productCarFitData as $item) {
                    $codes[] = $item['product_carfit_kod'];
                }
                if (count($codes) > 0) {
                    $oldData['product_carfit_codes'] = $codes;
                }
            }
        }
        unset($result);

        // Determine product is universal
        $is_universal_mark = $this->_getValueFromRowOrFromOldData(
            'is_universal',
            $data_from_row,
            $oldData,
            $this->language->get('text_no')
        );
        $is_universal = $is_universal_mark === $this->language->get('text_yes');
        unset($is_universal_mark);
  
    //$tmp = $this->_getValueFromRowOrFromOldData('sku', $data_from_row, $oldData, '');
    /*
    if($data_from_row[$this->_columns['model']] == '478101'){
        $this->session->data['errors'][] = '----------------- *';
    }else{
        $this->session->data['errors'][] = $data_from_row[$this->_columns['model']];
    }
    */
        $data = array(
            'model' => $data_from_row[$this->_columns['model']],
            'sku' => $this->_getValueFromRowOrFromOldData('sku', $data_from_row, $oldData, ''),
            'upc' => '',
            'ean' => '',
            'jan' => '',
            'isbn' => '',
            'mpn' => '',
            'location' => '',
            'quantity' => $this->_getValueFromRowOrFromOldData('quantity', $data_from_row, $oldData, 0),
            'minimum' => 1,
            'subtract' => 0,
            'stock_status_id' => 0,
            'date_available' => $this->_getValueFromRowOrFromOldData('date_available', $data_from_row, $oldData, date('Y-m-d H:i:s')),
            'manufacturer_id' => ($manufacturer_id == 0) ? $this->_getValueFromRowOrFromOldData('manufacturer_id', $data_from_row, $oldData, 0)
                : $manufacturer_id,
            'shipping' => 0,
            'price' => $this->_getValueFromRowOrFromOldData('price', $data_from_row, $oldData, 0),
            'points' => 0,
            'weight' => 0,
            'weight_class_id' => 0,
            'length' => 0,
            'width' => 0,
            'height' => 0,
            'length_class_id' => 0,
            'status' => 1,
            'tax_class_id' => 0,
            'sort_order' => $this->_getValueFromRowOrFromOldData('sort_order', $data_from_row, $oldData),
            'product_description' => array(
                $this->config->get('config_language_id') => array(
                    'name'          => $this->_cleanItemName($this->_getValueFromRowOrFromOldData('name', $data_from_row, $oldData, '')),
                    'meta_title'    => str_replace('&quot;', '"', $this->_cleanItemName($this->_getValueFromRowOrFromOldData('name', $data_from_row, $oldData, ''))),
                    'meta_keyword'  => $this->_getValueFromRowOrFromOldData('meta_keyword', $data_from_row, $oldData, ''), 
                    'meta_title'    => str_replace('&quot;', '"', $this->_getValueFromRowOrFromOldData('meta_title', $data_from_row, $oldData, '')),
                    'meta_description' => $this->_getValueFromRowOrFromOldData('meta_description', $data_from_row, $oldData, ''),
                    'description'   => $this->_getValueFromRowOrFromOldData('description', $data_from_row, $oldData, ''),
                    'mini_description' => $this->_getValueFromRowOrFromOldData('mini_description', $data_from_row, $oldData, ''),
                    'tag'           => $this->_getValueFromRowOrFromOldData('tag', $data_from_row, $oldData, ''),
                )
            ),
            'product_attribute' => $product_attributes,
            'product_category' => $product_category,
            'keyword' => $keyword !== null ? $this->_prepareKeyword($keyword) : $keyword,
            'product_store' => $this->_getStoreIds($data_from_row, $oldData),
            'is-universal' => $is_universal,
            /*'product_model' => $product_model*/
        );
        
        

  
        // If product exists
        $product_id = ($oldData !== null && isset($oldData['product_id'])) ? $oldData['product_id'] : 0;
        if ($product_id > 0) {
            
            $shouldUpdateRelationships = false;
            if (is_array($data['product_category'])) {
                $productCurrentCategories = $this->model_catalog_product->getProductCategories($product_id);
                if (count($productCurrentCategories) > 0) {
                    foreach ($data['product_category'] as $category_id) {
                        if (!in_array($category_id, $productCurrentCategories)) {
                            $shouldUpdateRelationships = true;
                            break;
                        }
                    }

                    /*
                     * Do not merge relationships. Uncomment the block to merge relationships.
                    if ($shouldUpdateRelationships) {
                        $data['product_category'] = array_merge($data['product_category'], $productCurrentCategories);
                        $data['product_category'] = array_unique($data['product_category']);
                    }
                    */
                } else {
                    $shouldUpdateRelationships = true;
                }
                unset($productCurrentCategories);
            }
            
            //Проверим!!! Если оператор завтыкал проставить категорию для товара!!!
            //Нужно перегенерить Категорию и Алиас - Но только если они ПУСТЫЕ! Если у товара назначено уже чтото - Ничего не делаем!
            
            //Назначим категорию - если ее нет!
            $parent_url_alias = $this->_getValueFromRowOrFromOldData('parent_url_alias', $data_from_row, $oldData, '');
 
            if($parent_url_alias){
                $this->_setCategoryIfNotExist($product_id, $parent_url_alias);
            }
            //Назначим алиас - если его нет!
            $this->_setAliasIfNotExist($product_id);
            
            // If keyword is null, try to find other
            if ($data['keyword'] === null) {
                $aliases = $this->_getUrlAliasesByIds(array($product_id), true);
                if (count($aliases) > 0) {
                    $data['keyword'] = array_pop($aliases);
                } else {
                    
                    
                    $this->session->data['errors'][] = sprintf($this->language->get('error_cant_find_product_url_alias'), $product_id). ' => ' . $this->_cleanItemName($this->_getValueFromRowOrFromOldData('name', $data_from_row, $oldData, '')).' <a href="/orders/nomenklatur/edit-magaz-tovar.php?tovid='.$product_id.'" target="_blank">[редактрировать]</a>';
                    return true;
                }
            }

            $product_attributes = $this->model_catalog_product->getProductAttributes($product_id);
            $data['product_attribute'] = $this->_mergeProductAttributes($product_attributes, $data['product_attribute']);

            $data['update_relationships'] = $shouldUpdateRelationships;
            if ($shouldUpdateRelationships
                && is_array($data['product_category'])
                && count($data['product_category']) > 0) {
                $data['main_relationships'] = $data['product_category'][0];
            }
            $data['update_model'] = false;
            $data['delete_images'] = false;
            
            
            //Алиас у нас генерится отдельно. Поэтому уберем эту переменную
            $data['keyword'] = false;
            
            $this->model_catalog_product->editProduct($product_id, $data, false, false);

        } else {
            // Nothing found, create new one
            if ($update_only_products) {
                $this->session->data['errors'][] = $this->language->get('error_cant_add_new_products');
            } else {
                $data['update_relationships'] = true;
                if (is_array($data['product_category'])
                    && count($data['product_category']) > 0) {
                    $data['main_relationships'] = $data['product_category'][0];
                }
                $data['stock_status_id'] = 1;
                if($manufacturer_id == 0){
                    $this->session->data['errors'][] = 
                                $this->language->get('error_manufacturer_not_found'). ' Product: '  . $data_from_row[$this->_columns['model']];
                    return false;
                }

                $product_id = $this->model_catalog_product->addProduct($data);
                 $this->session->data['errors'][] = $product_id;
            }
        }
      
        // Set product type and product car fit
        $product_type_code = $this->_getValueFromRowOrFromOldData(
            'product_type_code', $data_from_row, $oldData, null
        );
        if ($product_type_code !== null) {
            $result = $this->model_catalog_product->setProductType(
                $product_id,
                $product_type_code
            );
            if (strlen($product_type_code) > 0 && !$result) {
                $this->session->data['errors'][] = sprintf(
                    $this->language->get('error_cant_update_product_type'), $product_id, $product_type_code
                );
            }
        }
        unset($product_type_code);

        //Модели товара. Обязательно после обновления Бренда и Типа. Поиск модели ведется на основании Бренда и Типа Продукта
        $product_model = $this->_getValueFromRowOrFromOldData('product_model', $data_from_row, $oldData, 0);
        $product_model = trim($product_model);
        $result = $this->model_catalog_product_model->setProductModel($product_id, $product_model);
        if(!is_numeric($result)){
            //$this->session->data['errors'][] = 'Не смог назначить модель - ('.$product_model.')  продукту '.$this->_cleanItemName($this->_getValueFromRowOrFromOldData('name', $data_from_row, $oldData, '')). ' ('.$result.') <a href="/orders/nomenklatur/edit-magaz-tovar.php?tovid='.$product_id.'" target="_blank">[редактрировать]</a>';
        }
        
     
        $product_carfit_codes = $this->_getValueFromRowOrFromOldData(
            'product_carfit_codes', $data_from_row, $oldData, ''
        );
        $product_carfit_codes = explode('#', $product_carfit_codes);
        if (count($product_carfit_codes) == 1 && $product_carfit_codes[0] === '') {
            unset($product_carfit_codes[0]);
        }

        if (count($product_carfit_codes) > 0) {

            // Check codes
            $allCodesExists = $this->model_catalog_product->isAllProductCarfitCodesExists($product_carfit_codes);
            if (!$allCodesExists) {
                $nonExistentCodes = $this->model_catalog_product->getNonExistentProductCarfitCodes();
                $this->session->data['errors'][] = sprintf(
                    $this->language->get('error_carfit_codes_not_found'), implode(', ', $nonExistentCodes)
                );
            } else {
                // Set carfit data
                $result = $this->model_catalog_product->setProductCarFit(
                    $product_id,
                    $product_carfit_codes
                );
                if (!$result) {
                    $this->session->data['errors'][] = sprintf(
                        $this->language->get('error_cant_update_product_carfit_code'), $product_id, implode(', ', $product_carfit_codes)
                    );
                }
            }
        }

        // Update product data in the system`s database
        $this->_updateProductDataInTheSystemsDatabase($data);

        // Process product images
        if ($product_id > 0 && $this->_imagesCount > 0) {

            // Prepare product images list
            if ($oldData !== null) {
                $productImages = array($oldData['image']);
                if ($this->_imagesCount > 1) {
                    array_merge(
                        $productImages, $this->model_catalog_product->getProductImages($product_id)
                    );
                }
            } else {
                $productImages = array();
            }

            // Delete product images records
            if ($this->_imagesCount > 1) {
                $p = DB_PREFIX;
                $sql = "DELETE FROM `{$p}product_image` WHERE `product_id` = $product_id";
                $this->db->query($sql);
            }

            // Process each product image
            for ($i = 0; $i < $this->_imagesCount; ++$i) {
                $key = 'image_' . $i;
                
                if ($this->_columns[$key] !== false
                    && isset($data_from_row[$this->_columns[$key]])) {
                    
                    $res = $this->_processProductImage($product_id,
                                                    $i,
                                                    $productImages,
                                                    $data_from_row[$this->_columns[$key]]);
                    //if($res == 'no_brand'){
                     //   return no_brand;
                    //}
                    
                }
            }
            
            
        }

        return true;
    }

    /**
     * Update product data in the system`s database
     *
     * @param array $data
     * @return bool
     */
    protected function _updateProductDataInTheSystemsDatabase(array $data)
    {
        // Check product model
        if (!isset($data['model'])
            || strlen($data['model']) == 0) {
            return false;
        }

        // Load system`s access model
        /*
        $this->load->model('module/innersystem');

        // Get the system database access object and config
        $sysDb = $this->model_module_innersystem->getDb();
        $sysCf = $this->model_module_innersystem->getSystemConfig();

        // Update data (just sort_order for now)
        $sortOrder = isset($data['sort_order']) ? (int)$data['sort_order'] : 0;
        $data['keyword'] .= '.html';
        $sql = "UPDATE `{$sysCf['ppt']}nomenkl` SET"
                . " `sort_order` = $sortOrder,"
            . " `TovarURL` = '{$data['keyword']}'"
            . " WHERE `ArticulSystem` = '{$data['model']}'";
        $sysDb->query($sql);
        */
        
        return true;
    }

    /**
     * Process product image
     *
     * @param mixed $productId Product id
     * @param int $imageIndex Current image index
     * @param array $productImages Product images list
     * @param string $newImageFilename New image filename
     * @return bool
     */
    protected function _processProductImage($productId, $imageIndex, array $productImages, $newImageFilename, $brand = '')
    {
        // Check args
        if (!is_numeric($productId)
            || !is_numeric($imageIndex)
            || !is_array($productImages)
            || !is_string($newImageFilename)) {
            $this->session->data['errors'][] = sprintf(
                $this->language->get('error_invalid_set_of_arguments_passed_into_method'),
                '_processProductImage'
            );
            return false;
        }

        // If new image filename is empty, the delete image data
        
        $p = DB_PREFIX;
        if (strlen($newImageFilename) == 0) {
            if ($imageIndex == 0) {
                $sql = "UPDATE `{$p}product` SET `image` = '', `image_preview` = '' WHERE `product_id` = $productId";
                $this->db->query($sql);
            }

            return true;
        }
        
        if(strpos($newImageFilename, 'http') !== false){
            
            $file_to_load = $newImageFilename;
            $tmp = explode('/',$newImageFilename);
            
            $newImageFilename = $tmp[count($tmp) - 1];
            
            
        }
        
        
        ini_set('error_reporting', E_ALL);
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
     
        //get product brand =======================
        $sqlb = "SELECT M.code
                        FROM ".DB_PREFIX."product P
                        LEFT JOIN ".DB_PREFIX."manufacturer M ON P.manufacturer_id = M.manufacturer_id 
                        WHERE P.product_id = '$productId';";
        $r = $this->db->query($sqlb);
        
        $brand = '';
        if($r->num_rows > 0){
			$brand = $r->row['code'];
			$brand_tmp = $r->row['code'];
		}
        
        if($brand != '' AND strpos($newImageFilename,'brand') === false){
            $brand = 'brand/' . $brand . '/';
        }else{
            $brand = '';
        }
        
        //Не нашел бренд! Отваливаемся
        if($brand_tmp == ''){
            $sql = 'SELECT model FROM '.DB_PREFIX.'product WHERE product_id = "'.$productId.'";';
            $model = $this->db->query($sql);

            $msg = '<br>Не нашел бренд. Продукт = ' . $model->row['model'] . ' ';
            $msg .= '<a href="/orders/nomenklatur/edit-magaz-tovar.php?tovid='.$productId.'" target="_blank">[редактрировать]</a>';
            $this->session->data['errors'][] = $msg;//.' '.$sqlb;
            //die('Остановлено!');
            //return 'no_brand';
        }
        
        //echo '<br><br>='.strpos($newImageFilename, $brand).'=>'.$brand;
        //==================================================
        
        
        // Check new image is old image
        if ($newImageFilename{0} == '/') $newImageFilename = substr($newImageFilename, 1);
        
        $main_path = DIR_IMAGE.'product/';
        $brand_path = DIR_IMAGE.'product/' . $brand;
        
        if(!file_exists( DIR_IMAGE .'product/'.$brand)){
            mkdir(DIR_IMAGE.'product/'.$brand, 0777);
            chmod(DIR_IMAGE.'product/'.$brand, 0777);
        }
        
        $newImagePath = 'product/' .$brand . $newImageFilename;
        $oldImagePath = 'product/' . $newImageFilename;
        
         $newImageFullPath = DIR_IMAGE . $newImagePath;
         $oldImageFullPath = DIR_IMAGE . $oldImagePath;
      
      
    // ================= Д Л Я    О С Н О В Н О Г О   Ф О Т О ============================================================      
        //Проверим есть ли фаил в бренде. Если нет - поищем в корне. Если найме - скопируем его в парвильное место
        //У всех товаров есть бренд - товар должен лежать в бренде! НИкаких корней.
        if (!file_exists($newImageFullPath) AND file_exists($oldImageFullPath)){

            //Копируем в бренд
            copy($oldImageFullPath, $newImageFullPath);

            //Удаляем из корня
            //Итак - мы фиг знаем куда и зачем еще будет использоваться это фото.
            //Поэтому прежде чем удалить - проверим не испозьзуется ли оно больше ни у кого в корне
            $sql = 'SELECT product_id FROM '.DB_PREFIX.'product WHERE image = "'.$oldImageFullPath.'" OR image_preview = "'.$oldImageFullPath.'";';
            $r_i = $this->db->query($sql);

            //Провим в таблице продукта и в таблице фоток                
            if($r_i->num_rows == 0){
                $sql = 'SELECT product_id FROM '.DB_PREFIX.'product_image WHERE image = "'.$oldImageFullPath.'" OR image_preview = "'.$oldImageFullPath.'";';
                $r_i = $this->db->query($sql);
                
                if($r_i->num_rows == 0){
                
                    unlink($oldImageFullPath);
                
                }
            }

        }
        
        //Если файла вообще нигде нет
        if (!file_exists($newImageFullPath) AND !file_exists($oldImageFullPath)){
            
            $no_file = true;
        
        }
       
        // Get preview for new image
        $newImagePath = str_replace('product/product/','product/',$this->db->escape($newImagePath));
        $isKeyFoundInUpdateProductsImagesList = isset($this->_updatedProductsImages[$newImagePath]);
        if (!$isKeyFoundInUpdateProductsImagesList) {
        } else {
            $imagePreview = $this->_updatedProductsImages[$newImagePath];
        }
        unset($newImageFullPath);

        // Update image data in database
        if ($imageIndex == 0) {
            $sql = "UPDATE `{$p}product` SET `image` = '$newImagePath' WHERE `product_id` = $productId";
        } else {
            $sql = "INSERT INTO `{$p}product_image` (`image`,  `product_id`)"
                ." VALUES ('$newImagePath',  $productId)";
        }
        $this->db->query($sql);
        
        
        //Если у нас есть полный урл на фаил и реально файла нет
        if(isset($file_to_load)){
            
            $sql = "SELECT * FROM `{$p}import_pic` WHERE `from` = '$file_to_load' AND `to` = '$newImagePath'";
            $r = $this->db->query($sql);
            
            if($r->num_rows ==0){
                $sql = "INSERT INTO `{$p}import_pic` SET `from` = '$file_to_load', `to` = '$newImagePath'";
                $this->db->query($sql);
            }
        }

        

        if (!$isKeyFoundInUpdateProductsImagesList) {
            $this->_updatedProductsImages[$newImagePath] = $newImagePath;
        }
        
        return true;
    }

    /**
     * Resize image resource
     *
     * @param resource $imageResource
     * @return null|resource
     */
    protected function _resizeImageResource($imageResource)
    {
        if (!is_resource($imageResource)) {
            return null;
        }

        $newWidth = $this->config->get('config_image_popup_width');
        $newHeight = $this->config->get('config_image_popup_height');
        $sourceWidth = imagesx($imageResource);
        $sourceHeight = imagesy($imageResource);

        $sourceAspectRatio = $sourceWidth / $sourceHeight;
        $newAspectRatio = $newWidth / $newHeight;

        if ($sourceAspectRatio == $newAspectRatio) {
            $newWidth = $sourceWidth;
            $newHeight = $sourceHeight;
        } else if ($newAspectRatio > $sourceAspectRatio) {
            $newWidth = (int) ($newHeight * $sourceAspectRatio);
        } else {
            $newHeight = (int) ($newWidth / $sourceAspectRatio);
        }

        $resizedImage = imagecreatetruecolor($newWidth, $newHeight);
        imagecopyresampled($resizedImage, $imageResource, 0, 0, 0, 0, $newWidth, $newHeight, $sourceWidth, $sourceHeight);

        return $resizedImage;
    }

    /**
     * Merge product attributes
     *
     * @param array $first
     * @param array $second
     * @return array
     */
    private function _mergeProductAttributes(array $first, array $second)
    {
        $key = 'product_attribute_description';
        $langId = $this->config->get('config_language_id');

        $first = $this->_prepareAttributesForSearch($first);
        $second = $this->_prepareAttributesForSearch($second);

        foreach ($first as $attrId => $item) {
            if (!isset($second[$attrId])) {
                continue;
            }
            $first[$attrId][$key][$langId]['text'] = $second[$attrId][$key][$langId]['text'];
            unset($second[$attrId]);
        }

        if (count($second) > 0) {
            foreach ($second as $secondItem) {
                $first[] = $secondItem;
            }
        }

        return $first;
    }

    /**
     * Prepare attributes for search
     *
     * @param array $attributes
     * @return array
     */
    private function _prepareAttributesForSearch(array $attributes)
    {
        $prepared = array();
        foreach ($attributes as $item) {
            $prepared[$item['attribute_id']] = $item;
        }

        return $prepared;
    }

    /**
     * Get manufacturer id by name
     *
     * @param string $name
     * @return int
     */
    private function _getManufacturerIdByName($name)
    {
        if (!$name || strlen($name) == 0)
            return 0;
        
        $name = trim($name);
        
        $manufacturers = $this->model_catalog_manufacturer->getManufacturers(
            array(
                'filter_name' => $name,
                'limit' => 1,
                'start' => 0
            )
        );
        if (count($manufacturers) == 0)
            return 0;

            $manufacturer = array_shift($manufacturers);
            
        return (int)$manufacturer['manufacturer_id'];
    }

    /**
     * Check required fields
     *
     * @param array $fields_data
     * @param array $data_from_row
     * @return bool
     */
    private function _checkRequiredFields(array $fields_data, array $data_from_row)
    {
        if (count($data_from_row) == 0) {
            $this->session->data['errors'][] = $this->language->get('error_empty_row');
            return false;
        }

        $check_passed = true;

        $identifier = 'Unknown';
        if (isset($data_from_row[$this->_columns['model']]) && strlen($data_from_row[$this->_columns['model']]) > 0) {
            $identifier = $data_from_row[$this->_columns['model']];
        } elseif (isset($data_from_row[$this->_columns['name']]) && strlen($data_from_row[$this->_columns['name']]) > 0) {
            $identifier = $data_from_row[$this->_columns['name']];
        }

        foreach ($fields_data as $rf => $message) {

            if (!isset($data_from_row[$rf]) || strlen($data_from_row[$rf]) == 0) {
                $this->session->data['errors'][] = sprintf(
                    $this->language->get('error_column_value_not_found'),
                    $identifier, $message
                );

                if ($check_passed){
                    $check_passed = false;
                }
            }
        }

        return $check_passed;
    }

    /**
     * Get last processed category id
     *
     * @param array $processed_categories
     * @return int
     */
    private function _getLastProcessedCategoryId(array $processed_categories)
    {
        $cnt = count($processed_categories);
        if ($cnt == 0)
            return 0;

        return $processed_categories[$cnt - 1]['category_id'];
    }

    /**
     * Get data from row
     *
     * @param PHPExcel_Worksheet_Row $row
     * @return array
     */
    private function _getDataFromRow(PHPExcel_Worksheet_Row $row)
    {
        $data = array();
        foreach ($row->getCellIterator() as $cell) {
            // Get data
            $cell_index = PHPExcel_Cell::columnIndexFromString($cell->getColumn()) - 1;
            $cell_value = $cell->getValue();
            if ($this->_isExcelFormula($cell_value)) {
                $cell_value = $cell->getOldCalculatedValue();
            }
            $data[$cell_index] = stripslashes($cell_value);
        }

        return $data;
    }

    /**
     * Is Excel formula?
     *
     * @param string $value
     * @return bool
     */
    private function _isExcelFormula($value)
    {
        if (!is_string($value) || strlen($value) == 0) {
            return false;
        }
        return $value{0} === '=';
    }

    /**
     * Put header into CSV document
     *
     * @param resource $hnd
     */
    protected function _putHeaderIntoCSVDocument($hnd)
    {

        $fields = array();
        $keys = array_keys($this->_columns);
        foreach ($keys as $c) {

            if (in_array($c, $this->_columnsThatExcludedFromExport)) {
                continue;
            }

            $fields[] = $this->_columnsNames[$c];
        }
        unset($keys);

        foreach ($this->_attributes as $attribute)
            $fields[] = $attribute['name'];

        $this->_addSlashesToArrayItems($fields);
        $this->_convertArrayFieldsEncoding($fields, 'WINDOWS-1251', 'UTF-8');
        array_walk($fields, function (&$item, $key) { $item = str_replace("\n", null, $item); });
        fputcsv($hnd, $fields, ';', '~');
    }

    /**
     * Prepare item name for export
     *
     * @param string $name
     * @return string
     */
    private function _prepareItemName($name)
    {
        return str_replace(array('&quot;', '&nbsp;'), array('"', null), trim($name));
    }

    /**
     * Put category into CSV document
     *
     * @param resource $hnd
     * @param array $category
     * @param int $level
     */
    protected function _putCategoryIntoCSVDocument($hnd, array $category, $level)
    {
        $category['name'] = $this->_putLevelIntoCategoryName($category['name'], $level);

        // Get SEO keyword
        $query = $this->db->query(
            'SELECT `keyword` FROM `' . DB_PREFIX . 'url_alias` WHERE `query` = \'category_id='
            . $category['category_id'] . '\''
        );
        $seo_keyword = $query->num_rows > 0 ? $query->row['keyword'] : '';

        // Get category description
        $cds = $this->model_catalog_category->getCategoryDescriptions($category['category_id']);
        $lang_id = $this->config->get('config_language_id');
        if (isset($cds[$lang_id]))
            $category_description = $cds[$lang_id];
        else {
            if (count($cds) == 0)
                $category_description = array(
                    'meta_keyword' => '',
                    'meta_title' => '',
                    'meta_description' => '',
                );
            else
                $category_description = $cds[count($cds) - 1];
        }
        //array_walk($category_description, function (&$item, $key) { $item = addslashes($item); });
//header("Content-Type: text/html; charset=UTF-8");
//echo '<pre>'; print_r(var_dump( $category  ));
//die();

        // Get category record, need image path (OpenCart sucks...)
        $category_record = $this->model_catalog_category->getCategory($category['category_id']);

        // Get category store id
        $store_id = $this->model_catalog_category->getCategoryStore($category['category_id']);
        
        // Get category carfit
        $carfit = $this->model_catalog_category->getCategoryCarfitString($category['category_id']);
     
        $fields = array(
            $store_id, // store_id
            $category['sort_order'], // sort_order
            '', // model
            '', // sku
            $category['name'], // name
            $seo_keyword, // url_alias
            '', // parent_url_alias
            '', // price
            '', // quantity
            $category_description['title_h1'], // title_h1
            str_replace('&quot;', '"', str_replace('&quot;', '"', $category_description['meta_title'])), // Meta_title
            $category_description['meta_keyword'], 
            '',
            '', // manufacturer
            '', // product_type_code
            $carfit, // product_carfit_code
            (isset($category['is_universal']) AND $category['is_universal']) ? $this->language->get('text_yes') : $this->language->get('text_no'),// is_universal
            ($category['top']) ? $this->language->get('text_yes') : $this->language->get('text_no'),// top
            ($category['is_menu']) ? $this->language->get('text_yes') : $this->language->get('text_no'),// is_menu
            ($category['is_filter']) ? $this->language->get('text_yes') : $this->language->get('text_no'),// is_filter
            '', //Product_model
        );

        $this->_addSlashesToArrayItems($fields);
        $this->_convertArrayFieldsEncoding($fields, 'WINDOWS-1251', 'UTF-8');
        array_walk($fields, function (&$item, $key) { $item = str_replace("\n", null, $item); });
        fputcsv($hnd, $fields, ';', '~');

        $this->_lastExportedCategoryUrlAlias = $seo_keyword;
    }

    /**
     * Put level into category name
     *
     * @param string $name
     * @param int $level
     * @return string
     */
    protected function _putLevelIntoCategoryName($name, $level)
    {
        $name = $this->_prepareItemName($name);
        $result = '';
        for ($i = 0; $i < $level; $i++) {
            $result .= '!';
        }
        $result .= $name;
       
        return $result;
    }

    /**
     * Put product into CSV document
     *
     * @param resource $hnd
     * @param array $product
     */
    protected function _putProductIntoCSVDocument($hnd, array $product, array $productAttributes = null,
                                                array $productManufacturer = null)
    {
        $sku = !empty($product['sku']) ? $product['sku'] : '';
        // Get product type
        $product_type = $this->model_catalog_product->getProductType($product['product_id']);

        // Get product car fit data
        /*
        $pCarfitData = false; //$this->model_catalog_product->getProductCarFit($product['product_id']);
        if ($pCarfitData !== null) {
            $pCarfitCodeList = '';
            foreach ($pCarfitData as $item) {
                $pCarfitCodeList[] = $item['product_carfit_kod'];
            }
        }
        unset($pCarfitData);
        */

        // Get product manufacturer name
        $manufacturer_name = $productManufacturer && count($productManufacturer) > 0
            ? $productManufacturer['name'] : '';
    
        // Put general data
        $product['title_h1'] = $product['name'] = $this->_prepareItemName($product['name']);
        $parent_url_alias = $this->_lastExportedCategoryUrlAlias ? $this->_lastExportedCategoryUrlAlias : '';
        $fields = array(
            '0', // store_id
            $product['sort_order'], // sort_order
            $product['model'], // model
            $sku, // sku
            $product['name'], // name
            '', // url_alias
            $parent_url_alias, // parent_url_alias
            $product['price'], // price
            $product['quantity'], // quantity
            $product['title_h1'], // title_h1
            $product['meta_keyword'],
            str_replace('&quot;', '"', $product['meta_title']), // meta_title
            $manufacturer_name, // manufacturer
            $product_type['product_type_kod'], // product_type_name
            isset($pCarfitCodeList) ? implode('#', $pCarfitCodeList) : '', // product_carfit_kod
            $this->language->get('text_yes'),//((bool)$product['is_universal'])? $this->language->get('text_yes') : $this->language->get('text_no'), // is_universal
            '', //top
            '', //is_menu
            '', //is_filter
            '',//$this->model_catalog_product_model->getAlias($product['product_model']), //Product_model
        );

        // Put images
        $product['image'] = $this->_prepareImagePath($product['image']);
        $productImages = $this->model_catalog_product->getProductImages($product['product_id']);
        for ($i = 0; $i < $this->_imagesCount; ++ $i) {
            if ($i == 0) {
                $fields[] = $product['image'];
                continue;
            }
            $index = $i - 1;
            if (isset($productImages[$index])) {
                $fields[] = $this->_prepareImagePath($productImages[$index]['image']);
            } else {
                $fields[] = '';
            }
        }

        // Put attributes
        foreach ($this->_attributes as $attribute) {

            $value = '';
            if (isset($productAttributes[$attribute['attribute_id']])) {
                foreach($productAttributes[$attribute['attribute_id']] as $attribute_details){
                    $value .= $attribute_details['text'].'#';
                }
            }

            $fields[] = trim($value,' #');
        }
 
        $this->_addSlashesToArrayItems($fields);
        $this->_convertArrayFieldsEncoding($fields, 'WINDOWS-1251', 'UTF-8');
        array_walk($fields, function (&$item, $key) { $item = str_replace("\n", null, $item); });
        fputcsv($hnd, $fields, ';', '~');
    }

    /**
     * Prepare image path
     *
     * @param string $imagePath
     * @return array
     */
    private function _prepareImagePath($imagePath)
    {
        $signature = 'data/products_pictures/';
        $signaturePos = strpos($imagePath, $signature);
        $signatureLength = strlen($signature);

        $imagePathLength = strlen($imagePath);
        if ($signaturePos === false || $imagePathLength == $signatureLength) {
            return $imagePath;
        }

        return substr($imagePath, $signaturePos + $signatureLength);
    }

    /**
     * Convert array fields encoding
     *
     * @param array $array
     * @param string $toEncoding
     * @param string|null $fromEncoding
     */
    private function _convertArrayFieldsEncoding(array& $array, $toEncoding, $fromEncoding = null)
    {
        foreach ($array as $key => $value) {
            $array[$key] = mb_convert_encoding($value, $toEncoding, $fromEncoding);
        }
    }

    /**
     * @param resource $handle
     * @param null $length
     * @param string $delimiter
     * @param string $enclosure
     * @return array|bool
     */
    private function _fgetCsv($handle, $length = null, $delimiter = ';', $enclosure = '~')
    {
        $line = fgets($handle);
        if (!$line) {
            return false;
        }

        $items = array();
        $lineLength = strlen($line);
        $inEnclosure = false;
        $itemContent = '';
        for ($i = 0; $i < $lineLength; ++ $i) {

            $c = substr($line, $i, 1);
            if (($c == $delimiter || $c == PHP_EOL) && !$inEnclosure) {
                $items[] = $itemContent;
                $itemContent = '';
                continue;
            }

            if ($inEnclosure && $c == $enclosure) {
                $inEnclosure = false;
                continue;
            }

            $isEnclosureStart = $c == $enclosure;
            if ($isEnclosureStart) {
                $inEnclosure = true;
                continue;
            }

            $itemContent .= $c;
        }

        return $items;
    }
    
        /**
     * Put product into CSV document
     *
     * @param resource $hnd
     * @param array $product
     */
    protected function _putProductIntoCSVDocumentOptimal($hnd, array $product, array $productAttributes = null,
                                                array $productManufacturer = null)
    {
        $sku = !empty($product['sku']) ? $product['sku'] : '';

        // Get product type
        $product_type = $this->model_catalog_product->getProductType($product['product_id']);

        // Get product car fit data
        $pCarfitData = false; //$this->model_catalog_product->getProductCarFit($product['product_id']);
        if ($pCarfitData !== null) {
            $pCarfitCodeList = '';
            foreach ($pCarfitData as $item) {
                $pCarfitCodeList[] = $item['product_carfit_kod'];
            }
        }
        unset($pCarfitData);

        // Get product manufacturer name
        $manufacturer_name = $productManufacturer && count($productManufacturer) > 0
            ? $productManufacturer['name'] : '';

        // Put general data
        $product['name'] = $this->_prepareItemName($product['name']);
        $parent_url_alias = $this->_lastExportedCategoryUrlAlias ? $this->_lastExportedCategoryUrlAlias : '';
        
      
        $fields = array(
            '0', // store_id
            $product['sort_order'], // sort_order
            $product['model'], // model
            $sku, // sku
            $product['name'], // name
            '', // url_alias
            $parent_url_alias, // parent_url_alias
            $product['price'], // price
            $product['quantity'], // quantity
            $product['title_h1'], // title_h1
            $product['meta_keyword'], 
            str_replace('&quot;', '"', $product['meta_title']), // meta_title
            '',
            $manufacturer_name, // manufacturer
            $product_type['product_type_kod'], // product_type_name
            isset($pCarfitCodeList) ? implode('#', $pCarfitCodeList) : '', // product_carfit_kod
            ((bool)$product['is_universal'])
                ? $this->language->get('text_yes') : $this->language->get('text_no'), // is_universal
            '', //top
            '', //is_menu
            '', //is_filter
            $this->model_catalog_product_model->getAlias($product['product_model']), //Product_model
        );

        // Put images
        $product['image'] = $this->_prepareImagePath($product['image']);
        $productImages = $this->model_catalog_product->getProductImages($product['product_id']);
        for ($i = 0; $i < $this->_imagesCount; ++ $i) {
            if ($i == 0) {
                $fields[] = $product['image'];
                continue;
            }
            $index = $i - 1;
            if (isset($productImages[$index])) {
                $fields[] = $this->_prepareImagePath($productImages[$index]['image']);
            } else {
                $fields[] = '';
            }
        }

        // Put attributes
        foreach ($this->_attributes as $attribute) {

            $value = '';
            if (isset($productAttributes[$attribute['attribute_id']])) {
                foreach($productAttributes[$attribute['attribute_id']] as $attribute_details){
                    $value .= $attribute_details['text'].'#';
                }
            }

            $fields[] = trim($value,' #');
        }
 
        $this->_addSlashesToArrayItems($fields);
        $this->_convertArrayFieldsEncoding($fields, 'WINDOWS-1251', 'UTF-8');
        array_walk($fields, function (&$item, $key) { $item = str_replace("\n", null, $item); });
        fputcsv($hnd, $fields, ';', '~');
        
        //For optimization testing
    }
    
    protected function _setCategoryIfNotExist($product_id, $parent_url_alias){
        //return false;
        $sql = 'SELECT category_id FROM '.DB_PREFIX.'product_to_category WHERE product_id = \''.$product_id.'\';';
        $r = $this->db->query($sql);
        
        //Если у товара у же есть категория - отваливаемся
        if($r->num_rows > 0){
            return true;
        }
        
        $sql = 'SELECT query FROM '.DB_PREFIX.'url_alias WHERE query like \'category_id=%\' AND keyword = \''.strtolower($parent_url_alias).'\';';
        $r = $this->db->query($sql);
    //echo '<br>'.$sql;    
        //Если не нашли категорию по алиасу
        if($r->num_rows == 0){
            return false;
        }
        $tmp = $r->row;
        $category_id = (int)str_replace('category_id=', '', $tmp['query']);
    
        $sql = 'INSERT INTO '.DB_PREFIX.'product_to_category SET category_id = \''.$category_id.'\', product_id = \''.$product_id.'\';';

        $this->db->query($sql);

    }
    
    protected function _setAliasIfNotExist($product_id){
      
       //return false;
        $sql = 'SELECT keyword FROM '.DB_PREFIX.'url_alias WHERE query = \'product_id='.$product_id.'\';';
        $r = $this->db->query($sql);
        //Если есть алиас - отваливаемся
        //if($r->num_rows > 0){
        //    return false;
        //}
 
        //Получим данные по родительской категории 
        $sql = 'SELECT category_id, model FROM '.DB_PREFIX.'product_to_category CP
                    LEFT JOIN '.DB_PREFIX.'product P ON P.product_id = CP.product_id
                    WHERE CP.product_id = \''.$product_id.'\';';
        $r = $this->db->query($sql);
        
        //Если у товару не назначена категория
        if($r->num_rows == 0){
            return false;
        }
        $category = $r->row;                
                
        $sql = 'SELECT keyword FROM '.DB_PREFIX.'url_alias WHERE query = \'category_id='.$category['category_id'].'\';';
        $r = $this->db->query($sql);
                
        //Если не нашли категорию по алиасу
        if($r->num_rows == 0){
            return false;
        }
        $alias = $r->row;
        
        $newUrlAlias = $alias['keyword'];
        
        //Добавляем бренд и модель_товара - Только для детского сайта
		//Если будет более одного сайта - вынести этот ключ в конфиг!!!
         
		if(strpos($_SERVER['HTTP_HOST'], 'kidsmegashop') !== false){
			 
            $sql = 'SELECT P.product_id, PM.prod_model_code AS model_code, M.code AS brand_code, P.model
                            FROM '.DB_PREFIX.'product P
                            LEFT JOIN '.DB_PREFIX.'product_model PM ON PM.prod_model_id = P.product_model
                            LEFT JOIN '.DB_PREFIX.'manufacturer M ON M.manufacturer_id = P.manufacturer_id
                            WHERE product_id='.$product_id.' LIMIT 0, 1;';
            $r1 = $this->db->query($sql);
                    
            //Если не нашли категорию по алиасу
            if($r1->num_rows == 0){
                return false;
            }
            $product = $r1->row;
            $productCode = $product['model'];
            $product_model = '';
            $brand_code = '';
            if(isset($product['model_code'])) $product_model = $product['model_code'];
			if(isset($product['brand_code'])) $brand_code = $product['brand_code'];
			
			$newUrlAlias .= '/' . $brand_code . '/' . $product_model . '/' . $productCode;
			$newUrlAlias = str_replace('//','/', $newUrlAlias);
		}else{
			//Если добрались сюда - соединям строку в алиас
			$newUrlAlias .= '/' . $category['model'];
		}
	    
        
        
        $newUrlAlias = str_replace('//', '/', $newUrlAlias);
        //$alias_str = $alias['keyword'] . '/' . $category['model'];
        //echo '<br>'.$newUrlAlias;die();
        
        $sql = 'INSERT INTO '.DB_PREFIX.'url_alias SET query = \'product_id='.$product_id.'\', keyword = \''.$newUrlAlias.'\'
                    ON DUPLICATE KEY UPDATE keyword = \''.$newUrlAlias.'\';';
        $this->db->query($sql) or die('Не удалось обновить Алиас - '.$sql);
        
        //$this->session->data['errors'][]  = $sql;    
    }

}
