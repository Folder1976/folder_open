<?php echo $header; ?>
<style type="text/css">
    .ui-progressbar {
        position: relative;
    }

    .progress-label {
        position: absolute;
        left: 50%;
        top: 4px;
        font-weight: bold;
        text-shadow: 1px 1px 0 #fff;
    }

    div#progressbar {
        display: none;
    }

    input[type="file"] {
        opacity: 0;
        height: 0px;
    }

</style>
<script type="text/javascript">
$(function () {
    var exportForm = $("form#export-form");
    var submitButton = exportForm.find("input[type=submit]");
    var typeRadioButtons = exportForm.find("input[type=radio]");

    // Export form submit handler
    exportForm.submit(function (event) {

        event.preventDefault();

        // Get root category id
        var rootCategoryId = exportForm.find("select#root-category-id").val();
        var rootManufacturerId = exportForm.find("select#manufacturer").val();

        // Get type
        var type = typeRadioButtons.filter(":checked").val();
        if (type == undefined) {
            alert('Please, select document type!');
            return;
        }

        var url = null;
        if (type == 'xls') {
            url = '<?php echo $export_url_excel; ?>';
        } else if (type == 'csv') {
            url = '<?php echo $export_url_csv; ?>';
        } else {
            alert('You select invalid type of document!');
            return;
        }
        url += '&parent_category_id=' + rootCategoryId+'&manufacturer=' + rootManufacturerId;

        // Go to url
        window.location.href = url;

    });

    /*
     * Upload form
     */
    // Page body container
    var pageBodyContainer = $('div.page-body');

    // File input
    var fileInput = $('input#data');
    var allowedExtensions = ['csv', 'xls', 'xlsx'];
    fileInput.change(function (event) {
        // This object (e.g. fileInput)
        var thisObj = $(this);

        // Get extension
        var value = thisObj.val();
        if (value == '') {
            return;
        }
        var lastDotPos = value.lastIndexOf('.');
        var extension = value.substr(lastDotPos + 1);

        // Set upload button label as selected filename
        var lastSlashPos = value.lastIndexOf('/');
        if (lastSlashPos == -1) {
            lastSlashPos = value.lastIndexOf('\\');
        }
        var filename = value.substr(lastSlashPos + 1);
        buttonUpload.html(filename);
        buttonClear.prop('disabled', false);

        // Check file extension
        function blockImportAndShowMessage(message) {
            buttonImport.prop('disabled', true);
            var html = '<div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i>&nbsp;'
                            + message
                            + '<button type="button" class="close" data-dismiss="alert">&times;</button></div>';
            pageBodyContainer.prepend(html);
        }
        var isValidExtension = $.inArray(extension, allowedExtensions) != -1;
        if (!isValidExtension) {
            blockImportAndShowMessage('Invalid extension');
            return;
        }

        // If all is ok, then unblock import and clear buttons
        buttonImport.prop('disabled', false);
        buttonDelete.prop('disabled', false);
    });

    // Clear button
    var buttonClear = $('button#button-clear');
    buttonClear.click(function (event) {
        fileInput.val('');
        buttonUpload.html('<i class="fa fa-upload"></i> Upload');
        buttonClear.prop('disabled', true);
        buttonImport.prop('disabled', true);
        $('div[class="alert alert-danger"]').remove();
    });

    // Import button
    var buttonImport = $('input#import');
    var buttonDelete = $('input#delete');

    // Upload button
    var buttonUpload = $('button#button-upload');
    buttonUpload.click(function (event) {
        fileInput.trigger('click');
    });

});
</script>
<?php echo $column_left; ?>
<div id="content">
    <div class="page-header">
        <div class="container-fluid">
            <div class="pull-right">
            <h1><?php echo $heading_title; ?></h1>
            <ul class="breadcrumb">
                <?php foreach ($breadcrumbs as $breadcrumb) { ?>
                <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
                <?php } ?>
            </ul>
        </div>
    </div>
    <div class="page-body container-fluid">
        <?php if (isset($error)): ?>
        <div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $error; ?>
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
        <?php endif; ?>
        <?php if (isset($success)): ?>
        <div class="alert alert-success"><i class="fa fa-exclamation-circle"></i> <?php echo $success; ?>
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
        <?php endif; ?>
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title"><i class="fa fa-pencil"></i> <?php echo $heading_title; ?></h3>
            </div>
            <div class="panel-body">

                <fieldset>

                    <legend><?php echo $text_import; ?></legend>
                    <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form"
                          class="form-horizontal">

                        <div class="form-group">
                            <label class="col-sm-2  control-label"><?php echo $text_input_document; ?>:</label>

                            <div class="col-sm-10">
                                <button type="button" id="button-upload" data-loading-text="Loading..."
                                        class="btn btn-primary"><i class="fa fa-upload"></i> <?php echo $text_button_upload ?>
                                </button>
                                <button type="button" id="button-clear" data-loading-text="Loading..."
                                        disabled="disabled" class="btn btn-danger"><i class="fa fa-eraser"></i> <?php echo $text_button_clear ?>
                                </button>
                                <input id="data" type="file" name="data"/>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2  control-label">

                                <?php echo $text_input_products_update_only ?>:

                            </label>

                            <div class="col-sm-10">
                                <div class="checkbox">
                                    <label>
                                        <input id="jst-upd" type="checkbox" name="jst-upd"/>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-2 control-label">
                                <input type="submit" id="import" value="<?php echo $text_import ?>" style="width: 150px;"
                                       class="btn btn-primary" disabled="disabled"/>
                            </div>
                            <div class="col-sm-2 control-label">
                                <input type="submit" id="delete" name="delete_btn" onclick="javascript:if(confirm('Удалить?!')) {return true;} else{return false;}" value="<?php echo $text_import_delete ?>" style="width: 120px;"
                                       class="btn btn-danger" disabled="disabled"/>
                            </div>
                            <div class="col-sm-10">

                            </div>
                        </div>

                    </form>

                    <br/>

                    <legend><?php echo $text_export; ?></legend>
                    <br>

                    <form action="" method="post" id="export-form" class="form-horizontal">
<!--brend-->
                        <div class="form-group">
                            <label class="col-sm-2 control-label" for="input-brend"><?php echo $text_manufacturer; ?></label>
                            <div class="col-sm-10">
                                <select name="manufacturer" id="manufacturer" class="form-control">
                                    <?php foreach ($manufacturers as $manufacturer) { ?>
                                        <?php if (isset($manufacturer) && $manufacturer == $manufacturer['manufacturer_code']) { ?>
                                            <option value="<?php echo $manufacturer['manufacturer_code']; ?>" selected="selected"><?php echo $manufacturer['name']; ?></option>
                                        <?php } else { ?>
                                            <option value="<?php echo $manufacturer['manufacturer_code']; ?>"><?php echo $manufacturer['name']; ?></option>
                                        <?php } ?>
                                    <?php } ?>
                                </select>
                             </div>  
                        </div>
                  
                        <div class="form-group">
                            <label class="col-sm-2 control-label"><?php echo $text_input_root_category; ?>:</label>

                            <div class="col-sm-10">
                                <select name="root-category-id" id="root-category-id" class="form-control">

                                    <option value="<?php echo $category_id_root; ?>">...</option>

                                    <?php foreach ($root_categories as $rc): ?>

                                    <option value="<?php echo $rc['category_id']; ?>"><?php echo $rc['name']; ?></option>

                                    <?php endforeach; ?>

                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-2  control-label">

                                <?php echo $text_input_file_type ?>:

                            </label>

                            <div class="col-sm-10">
                                <label class="radio-inline  control-label">
                                    <input type="radio" name="type" value="csv"/>
                                    CSV
                                </label>
                                <label class="radio-inline  control-label">
                                    <input type="radio" name="type" value="xls"/>
                                    Excel
                                </label>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-2 control-label">
                                <input type="submit" value="<?php echo $text_export; ?>" class="btn btn-primary"/>
                            </div>
                            <div class="col-sm-10">

                            </div>
                        </div>
                    </form>


<!-- Памятка -->                    
                    <br>
                        <legend><?php echo $text_memo; ?></legend>
                    <fieldset>
                        <?php foreach ($column_names as $column => $memo) { ?>
                            <div class="form-group">
                                <label class="col-sm-2 control-label"><?php echo $column; ?>:</label>
                                <div class="col-sm-10"><?php echo $memo; ?></div>
                            </div><br>
                        <?php } ?>
                    
                    </fieldset>
            </div>
        </div>
    </div>
</div>
<?php echo $footer; ?>