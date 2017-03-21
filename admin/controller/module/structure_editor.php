<?php
class ControllerModuleStructureEditor extends Controller {

    const PRODUCTS_LIMIT = 90;

    private $_categoriesWhichHasAbillityToClean = array(1);
    private $_registry;

    public function __construct($registry) {
        parent::__construct($registry);
        $this->_registry = $registry;
    }

    /**
     * The module index page
     */
    public function index()
    {
        // Variables for view
        $data = [
            'token' => $this->session->data['token'],
            'limit' => self::PRODUCTS_LIMIT,
            'handler_url_delete_branch' => $this->url->link(
                'module/structure_editor/handlerDeleteBranch', 'token=' . $this->session->data['token'], 'SSL'
            )
        ];

        // Load language
        $this->language->load('module/structure_editor');
        $itemsToLoad = array(
            'heading_title', 'text_edit', 'text_edit_short',
            'text_products_short', 'text_add_here', 'text_module',
            'text_product_id', 'text_product_name', 'text_product_quantity',
            'text_product_manage', 'text_pages', 'text_branch_clean',
            'text_confirm_action', 'text_toggle_sort_order',
            'text_delete_short',
        );
        $languageLabels = array();
        foreach ($itemsToLoad as $item) {
            $data[$item] = $this->language->get($item);
            $languageLabels[$item] = $data[$item];
        }
        $data['language_labels'] = json_encode($languageLabels);

        $this->document->setTitle($this->language->get('heading_title'));

        // Say page to load scripts and styles
        $this->document->addScript('/admin/view/javascript/structure_editor/tree.js');
        $this->document->addScript('/admin/view/javascript/structure_editor/tree_view.js');
        $this->document->addScript('/admin/view/javascript/structure_editor/products_list_view.js');

        $this->document->addStyle('/admin/view/stylesheet/structure_editor/stylesheet.css');

        // Prepare branches
        $this->_prepareBranches($data);

        // Load view and send response
        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput(
            $this->load->view('module/structure_editor.tpl', $data)
        );
    }

    /**
     * The handler cleans the category by id
     */
    public function handlerCleanCategory()
    {
        // Get category id
        $categoryId = $this->_getFilteredRequestParam('category_id', FILTER_SANITIZE_NUMBER_INT, 'filter_var');
        if (!$categoryId) {
            $this->_sendJSONResponse(false);
            return;
        }
        $categoryId = $this->db->escape($categoryId);

        // Check category id
        if (!in_array($categoryId, $this->_categoriesWhichHasAbillityToClean)) {
            $this->_sendJSONResponse(false);
            return;
        }

        // Get products
        $this->load->model('catalog/product');
        $products = $this->model_catalog_product->getProductsByCategoryId($categoryId);
        foreach ($products as $product) {
            $this->model_catalog_product->deleteProduct($product['product_id']);
        }

        $this->_sendJSONResponse(true);
    }

    /**
     * Handler to delete a branch
     */
    public function handlerDeleteBranch()
    {
        $redirectUrl = $this->url->link('module/structure_editor', 'token=' . $this->session->data['token'], 'SSL');

        if (!isset($this->request->request['branch_id'])
            && !is_numeric($this->request->request['branch_id'])) {
            $this->response->redirect($redirectUrl);
            return;
        }

        $branchId = $this->request->request['branch_id'];
        $action = new Action('catalog/category/delete', ['category_id' => $branchId]);
        $action->execute($this->_registry);

        $this->response->redirect($redirectUrl);
    }

    /**
     * The handler gets categories children
     */
    public function handlerGetCategoriesChildren()
    {
        // Get ids list
        $ids = $this->_getFilteredRequestParam('ids', FILTER_SANITIZE_NUMBER_INT);
        if (!$ids) {
            $this->_sendJSONResponse(false);
            return;
        }

        // Get children
        $children = $this->_getChildrenByParentsIds($ids);
        if ($children) {

            // Prepare children ids list
            $ids = array();
            foreach ($children as $item) {
                $ids[] = $item['branch_id'];
            }

            // Get products total counts for each child
            $totalCounts = $this->_getTotalCountsByCategoriesIds($ids);
            foreach ($children as &$item) {
                $item['products_total_counts'] = isset($totalCounts[$item['branch_id']])
                    ? $totalCounts[$item['branch_id']] : 0;
            }
            unset($item, $totalCounts);

        }

        $this->_sendJSONResponse(true, $children);
    }

    /**
     * The handler gets category products
     */
    public function handlerGetCategoryProducts()
    {
        $categoryId = $this->_getFilteredRequestParam('category_id', FILTER_SANITIZE_NUMBER_INT, 'filter_var');
        if (!$categoryId) {
            $this->_sendJSONResponse(false);
            return;
        }
        $categoryId = $this->db->escape($categoryId);

        $page = $this->_getFilteredRequestParam('page', FILTER_SANITIZE_NUMBER_INT, 'filter_var');
        if (!$page) {
            $page = 0;
        }

        $langId = (int)$this->config->get('config_language_id');
        $offset = $page * self::PRODUCTS_LIMIT;
        $sql = $this->_buildQueryToSelectProducts($categoryId, $langId, $offset);
        $result = $this->db->query($sql);
        if ($result->num_rows == 0) {
            $this->_sendJSONResponse(true);
        } else {

            $sql = $this->_buildQueryToSelectProducts($categoryId, $langId, $offset, true);
            $cntResult = $this->db->query($sql);

            $data = array(
                'total_counts' => $cntResult->row['cnt'],
                'items' => $result->rows,
            );

            $this->_sendJSONResponse(true, $data);
        }
    }

    /**
     * A handler to get category data
     */
    public function handlerGetCategoryData()
    {
        $categoryId = $this->_getFilteredRequestParam('category_id', FILTER_SANITIZE_NUMBER_INT, 'filter_var');
        if (!$categoryId) {
            $this->_sendJSONResponse(false);
            return;
        }

        $this->load->model('catalog/category');
        $data = $this->model_catalog_category->getCategory($categoryId);
        $this->_sendJSONResponse(true, $data);
    }

    /**
     * A handler to delete category
     */
    public function handlerDeleteCategory()
    {
        $categoryId = $this->_getFilteredRequestParam('category_id', FILTER_SANITIZE_NUMBER_INT, 'filter_var');
        if (!$categoryId) {
            $this->_sendJSONResponse(false);
            return;
        }

        $this->load->model('catalog/category');
        $data = $this->model_catalog_category->deleteCategory($categoryId);
        $this->_sendJSONResponse(true, $data);
    }

    /**
     * Build query to select products
     *
     * @param mixed $categoryData
     * @param int $langId
     * @param int $offset
     * @param bool $selectTotalCounts
     *
     * @return string
     */
    private function _buildQueryToSelectProducts($categoryData, $langId, $offset, $selectTotalCounts = false)
    {
        $isArraySpecified = is_array($categoryData);

        if ($selectTotalCounts) {
            $sql = "SELECT COUNT(`p2c`.`product_id`) AS `cnt`";
            if ($isArraySpecified) {
                $sql .= " , `cp`.`path_id` AS `branch_id`";
            }
        } else {
            $sql = "SELECT `pd`.`name`, `p`.`quantity`, `p`.`product_id`, `p`.`model`";
        }

        if ($isArraySpecified) {
            $categoryData = implode(', ', $categoryData);
        }

        $p = DB_PREFIX;
        $sql .= " FROM `{$p}category_path` `cp`"
            . " LEFT JOIN `{$p}product_to_category` `p2c` ON (`p2c`.`category_id` = `cp`.`category_id`)"
            . " LEFT JOIN `{$p}product` `p` ON `p`.`product_id` = `p2c`.`product_id`"
            . " LEFT JOIN `{$p}product_description` `pd` ON `pd`.`product_id` = `p`.`product_id`";

        if ($isArraySpecified) {
            $sql .= " WHERE `cp`.`path_id` IN ($categoryData)";
        } else {
            $sql .= " WHERE `cp`.`path_id` = $categoryData";
        }

        $sql .= " AND `pd`.`language_id` = $langId";

        if ($isArraySpecified) {
            $sql .= " GROUP BY `cp`.`path_id`";
        } else {
            $sql .= " ORDER BY `p`.`sort_order` ASC";
        }

        if (!$selectTotalCounts) {
            $sql .= " LIMIT $offset," . self::PRODUCTS_LIMIT;
        }

        return $sql;
    }

    /**
     * Get filtered request param
     *
     * @param string $key
     * @param int $filter
     * @param string $filterFunc
     * @return bool|array
     */
    protected function _getFilteredRequestParam($key, $filter = FILTER_SANITIZE_STRING, $filterFunc = 'filter_var_array')
    {
        if (!isset($_REQUEST[$key])) {
            return false;
        }

        $data = call_user_func($filterFunc, $_REQUEST[$key], $filter);
        if (!$data || count($data) == 0) {
            return false;
        }

        return $data;
    }

    /**
     * Send json serialized response
     *
     * @param bool $success
     * @param mixed $data
     */
    protected function _sendJSONResponse($success, $data = null)
    {
        $response = array(
            'success' => $success,
        );
        if (!is_null($data)) {
            $response['data'] = $data;
        }

        $this->response->setOutput(json_encode($response));
    }

    /**
     * Prepare branches
     *
     * @param array $viewData
     * @param bool $serialize
     */
    protected function _prepareBranches(array& $viewData, $serialize = true)
    {
        // Get top level branches
        $branches = $this->_getTopLevelBranches();
        if (!$branches) {
            return;
        }

        $ids = array();
        $prepared = array();
        foreach ($branches as $key => $item) {
            $id = (int)$item['branch_id'];
            $ids[] = $id;
            $prepared[$id] = $item;
            unset($branches[$key]);
        }
        $branches = $prepared;
        unset($item, $prepared);

        $totalCounts = $this->_getTotalCountsByCategoriesIds($ids);
        foreach ($branches as &$item) {
            $item['products_total_counts'] = isset($totalCounts[$item['branch_id']])
                ? $totalCounts[$item['branch_id']] : 0;
        }
        unset($item, $totalCounts);

        $children = $this->_getChildrenByParentsIds($ids);
        $ids = array();
        foreach ($children as $item) {
            $ids[] = $item['branch_id'];
        }

        $totalCounts = $this->_getTotalCountsByCategoriesIds($ids);
        foreach ($children as &$item) {
            $item['products_total_counts'] = isset($totalCounts[$item['branch_id']])
                ? $totalCounts[$item['branch_id']] : 0;
        }
        unset($item, $totalCounts);

        $this->_putChildrenIntoParents($children, $branches);

        $viewData['branches'] = $serialize ? json_encode($branches) : $branches;
    }

    /**
     * Get top level branches
     *
     * @return mixed
     */
    protected function _getTopLevelBranches()
    {
        $p = DB_PREFIX;
        $langId = (int)$this->config->get('config_language_id');
        $sql = "SELECT `name`, `c`.`category_id` AS `branch_id`, `c`.`sort_order` FROM `{$p}category_description` `cd`"
            . " INNER JOIN `{$p}category` `c` ON `c`.`category_id` = `cd`.`category_id`"
            . " WHERE (`c`.`parent_id` = 0 OR `c`.`parent_id` = `c`.`category_id`) AND `cd`.`language_id` = $langId";

        $result = $this->db->query($sql);

        if ($result->num_rows == 0) {
            return false;
        } else {
            return $result->rows;
        }
    }

    /**
     * Get children by parents ids
     *
     * @param array $ids
     * @return array|bool
     */
    protected function _getChildrenByParentsIds(array $ids)
    {
        $p = DB_PREFIX;
        array_walk($ids, function (&$item, $key) { $item = (int)$this->db->escape($item); });
        $ids = implode(', ', $ids);
        $langId = (int)$this->config->get('config_language_id');
        $sql = "SELECT `name`, `c`.`parent_id`, `c`.`category_id` AS `branch_id`, `c`.`sort_order` FROM `{$p}category_description` `cd`"
            . " INNER JOIN `{$p}category` `c` ON `c`.`category_id` = `cd`.`category_id`"
            . " WHERE `c`.`parent_id` IN ($ids) AND `cd`.`language_id` = $langId";

        $result = $this->db->query($sql);

        if ($result->num_rows == 0) {
            return false;
        } else {
            return $result->rows;
        }
    }

    /**
     * Get total counts by categories ids
     *
     * @param array $ids
     * @return bool|array
     */
    protected function _getTotalCountsByCategoriesIds(array $ids)
    {
        $sql = $this->_buildQueryToSelectProducts(
            $ids, (int)$this->config->get('config_language_id'), 0, true
        );
        $result = $this->db->query($sql);

        if ($result->num_rows == 0) {
            return false;
        } else {

            $prepared = array();
            foreach ($result->rows as $row) {
                $prepared[$row['branch_id']] = $row['cnt'];
            }

            return $prepared;
        }
    }

    /**
     * Put children into parents
     *
     * @param array $children
     * @param array $parents
     */
    private function _putChildrenIntoParents(array $children, array& $parents) {
        foreach ($children as &$item) {
            $parentId = $item['parent_id'];
            $childId = $item['branch_id'];
            $parents[$parentId]['children'][$childId] = $item;
        }
        unset($item);
    }


    protected function validate()
    {
        if (!$this->user->hasPermission('modify', 'module/structure_editor')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        return !$this->error;
    }
}