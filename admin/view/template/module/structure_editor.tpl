<?php print $header; ?>
<?php print $column_left; ?>
<?php $isBranchesPresent = isset($branches); ?>
<div id="content">
    <div class="page-header">
        <div class="container-fluid">
            <div class="pull-right">
                <button id="toggle-mode-nso" data-toggle="tooltip" title="<?php echo $text_toggle_sort_order; ?>" class="btn btn-primary"><i class="fa fa-filter"></i></button>
            </div>
        </div>
    </div>
    <div class="container-fluid">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title"><i class="fa fa-bars"></i><?php print $heading_title ?></h3>
            </div>
            <div class="panel-body">
                <?php if ($isBranchesPresent): ?>
                <div id="tree"></div>
                <?php endif; ?>
                <div id="products">
                    <table>
                        <thead>
                        <tr>
                            <td><?php print $text_product_id ?></td>
                            <td><?php print $text_product_name ?></td>
                            <td><?php print $text_product_quantity ?></td>
                            <td><?php print $text_product_manage ?></td>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td>...</td>
                            <td>...</td>
                            <td>...</td>
                            <td>
                                <ul>
                                    <li><a class="product-add-link" href="#"><?php print $text_add_here ?>...</a></li>
                                </ul>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                    <div class="pagination">
                        <span><?php print $text_pages ?>:</span><br>
                        <ul></ul>
                    </div>
                    <div class="overlay">
                        <img alt="loading..." src="/admin/view/image/preloader.gif">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php if ($isBranchesPresent): ?>
<script type="text/javascript">
    var branches = <?php print $branches ?>;
    var treeView = null;
    var productsListView = null;

    $(function() {
        try {
            var tree = new Tree(branches, '<?php print $token ?>');
            productsListView = new ProductsListView("products", tree.getToken(), <?php print $limit ?>, <?php print $language_labels ?>);
            treeView = new TreeView("tree", tree, productsListView, <?php print $language_labels ?>);
        } catch (err) {
            console.error(err);
            return;
        }

        $("button#toggle-mode-nso").click(function (event) {
            var isActive = !treeView.isModeNsoActive();

            var thisObj = $(this);
            if (isActive) {
                treeView.setModeNsoActive();
                thisObj.addClass("active");
            } else {
                treeView.setModeNsoDisabled();
                thisObj.removeClass("active");
            }
        });
    });

</script>
<?php endif; ?>
<?php print $footer; ?>