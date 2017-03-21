/**
 * Products list view
 *
 * @param DOMId
 * @param token
 * @param limit
 * @param languageLabels
 * @constructor
 */
function ProductsListView (DOMId, token, limit, languageLabels) {

    /**
     * Handler executes when before category products load
     *
     * @param token
     * @param branchId
     */
    this.handlerCategoryProductsOnBeforeLoad = function (token, branchId) {
        this._domRoot.html("");
        this._overlay.fadeToggle("fast");
    }

    /**
     * Load products
     *
     * @param branchId
     * @param page
     * @param callback
     */
    this.loadProducts = function (branchId, page, callback) {

        if (page == undefined) {
            page = 0;
        }

        this.handlerCategoryProductsOnBeforeLoad(token, branchId);
        $.ajax({
            type: "POST",
            url: "/admin/index.php?route=module/structure_editor/handlerGetCategoryProducts&token=" + token,
            data: {category_id: branchId, "page": page},
            dataType: "json",
            success: function (response) {

                if (!response.success) {
                    console.error("Can`t get products for category #" + branchId);
                    return;
                }

                if (!response.hasOwnProperty("data")) {
                    console.log("Empty products list for category #" + branchId);
                    thisObj.handlerCategoryProductsOnLoad(token, branchId);
                    return;
                }

                thisObj.handlerCategoryProductsOnLoad(token, branchId, page, response.data);
                if (typeof(callback) != "undefined") {
                    callback();
                }
            }
        });
    }

    /**
     * Handler executes when category products load
     *
     * @param token
     * @param branchId
     * @param page
     * @param data
     */
    this.handlerCategoryProductsOnLoad = function (token, branchId, page, data) {

        if (data != undefined) {
            // Put products into table
            if (data.hasOwnProperty("items")) {
                this._putProductsIntoTable(branchId, data.items);
            } else {
                this._putProductsIntoTable(branchId, []);
            }

            // Prepare pagination
            if (data.hasOwnProperty("total_counts")) {
                this._preparePagination(branchId, page, data.total_counts);
            }
        } else {
            this._putProductsIntoTable(branchId, []);
        }

        // Scroll to page begining
        $("body").scrollTop(0);

        // Toggle off preloader
        this._overlay.fadeToggle("fast");
    }

    /**
     * Prepare pagination
     *
     * @param branchId
     * @param page
     * @param totalCounts
     * @private
     */
    this._preparePagination = function (branchId, page, totalCounts) {

        // Clear pagination root element
        this._paginationRoot.html("");

        // Get pagination root parent
        var paginationContainer = this._paginationRoot.parent();

        // If total counts lesser than limit, then hide pagination element container
        if (totalCounts < this._limit) {
            if (paginationContainer.css("display") == "block") {
                paginationContainer.css("display", "none");
            }
            return;
        }

        // Build pages elements
        var pagesCount = Math.ceil(totalCounts / this._limit);
        for (var i = 0; i < pagesCount; ++ i) {

            // Page number to show (greater than original by one)
            var pageNum = i + 1;

            // Build html
            var html = "<li>";
            if (i == page) {
                html += "<a href=\"#\" class=\"active\" data-page=\"" + i + "\">" + pageNum + "</a>";
            } else {
                html += "<a href=\"#\" data-page=\"" + i + "\">" + pageNum + "</a>";
            }
            html += "</li>";

            // Append element to root
            var element = $(html);
            this._paginationRoot.append(element);

            // Set click handler
            element.find("a").click(function (event) {
                event.preventDefault();
                thisObj.handlerPageLinkClick(branchId, $(this).data("page"));
            });
        }

        // Toggle on pagination element
        if (paginationContainer.css("display", "none")) {
            this._paginationRoot.parent().css("display", "block");
        }
    }

    /**
     * Put products into table
     *
     * @param branchId
     * @param products
     * @private
     */
    this._putProductsIntoTable = function (branchId, products) {

        var html = "<tr>";
        html += "<td>...</td>";
        html += "<td>...</td>";
        html += "<td>...</td>";
        html += "<td><ul>";
        html += "<li><a class=\"product-add-link\" href=\"#\">" + this._languageLabels["text_add_here"] + "...</a></li>";
        html += "</ul></td>";
        html += "</tr>";

        var firstRowBody = $(html);
        firstRowBody.find("a.product-add-link").click(function (event) {
            event.preventDefault();
            thisObj.handlerProductAddClick(token, branchId)
        });
        this._domRoot.append(firstRowBody);

        for (var i = 0; i < products.length; ++ i) {

            var item = products[i];

            html = "<tr>";
            html += "<td>" + item.product_id + "</td>";
            html += "<td>" + item.name + " (" + item.model + ")</td>";
            html += "<td>" + item.quantity + "</td>";
            html += "<td><ul>";
            html += "<li><a data-id=\"" + item.product_id + "\" class=\"product-edit-link\" href=\"#\">" + this._languageLabels["text_edit"] + "</a></li>";
            html += "</ul></td>";
            html += "</tr>";

            var productBody = $(html);
            productBody.find("a.product-edit-link").click(function (event) {
                event.preventDefault();

                var productId = $(this).data("id");
                thisObj.handlerProductEditClick(branchId, productId, token);
            });
            this._domRoot.append(productBody);
        }

    }

    /**
     * Handler executes when the page link is clicked
     *
     * @param page
     */
    this.handlerPageLinkClick = function (branchId, page) {
        this.loadProducts(branchId, page);
    }

    /**
     * Handler executes when product edit link is clicked
     *
     * @param branchId
     * @param productId
     * @param token
     */
    this.handlerProductEditClick = function (branchId, productId, token) {
        var link = "/admin/index.php?route=catalog/product/edit&token=" + token
            + "&product_id=" + productId
            + "&is_pd_iframe=1";

        var popup = window.open(link, "", "toolbar=no,scrollbars=no,location=no,statusbar=no,menubar=no,resizable=0");
        if (popup == undefined) {
            return;
        }

        popup.focus();

        this._currentCategoryId = branchId;
    }

    /**
     * Handler executes when product edit window is unload
     *
     * @param branchId
     */
    this.handlerProductEditWindowUnload = function (callback, branchId) {

        if (typeof(branchId) == "undefined") {
            branchId = this.getCurrentCategoryId();
        }

        // Get page
        var page = this._paginationRoot.find("a.active").data("page");

        // Reload products
        this.loadProducts(branchId, page, callback);
    }

    /**
     * Handler executes when the product add link is clicked
     *
     * @param token
     * @param branchId
     */
    this.handlerProductAddClick = function (token, branchId) {
        var link = "/admin/index.php?route=catalog/product/add&token=" + token
            + "&default_category_id=" + branchId
            + "&is_pd_iframe=1";

        var popup = window.open(link, "", "toolbar=no,scrollbars=no,location=no,statusbar=no,menubar=no,resizable=0");
        if (popup == undefined) {
            return;
        }
        popup.focus();

        this._currentCategoryId = branchId;
    }

    // Find the root element
    var selector = "div#" + DOMId + " table tbody";
    this._domRoot = $(selector);
    if (this._domRoot.length == 0) {
        throw new Error("The root element is not found, selector: " + selector);
    }

    // Find the overlay
    selector = "div#" + DOMId + " div.overlay";
    this._overlay = $(selector);
    if (this._overlay.length == 0) {
        throw new Error("The overlay element is not found, selector: " + selector);
    }

    // Set click handler for product add link
    this._domRoot.find("a.product-add-link").click(function (event) {
        event.preventDefault();
        thisObj.handlerProductAddClick(thisObj._token, 0);
    });

    // Find the pagination root element
    selector = "div#" + DOMId + " div.pagination ul";
    this._paginationRoot = $(selector);
    if (this._paginationRoot.length == 0) {
        throw new Error("The pagination element is not found, selector: " + selector);
    }

    this.getCurrentCategoryId = function() {
        return this._currentCategoryId;
    };

    var thisObj = this;
    this._token = token;
    this._limit = limit;
    this._languageLabels = languageLabels;
    this._currentCategoryId = 0;
}