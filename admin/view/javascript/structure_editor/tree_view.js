/**
 * Tree view
 *
 * @param rootBranchDOMId
 * @param tree
 * @param productsListView
 * @param languageLabels
 * @constructor
 */
function TreeView (rootBranchDOMId, tree, productsListView, languageLabels) {

    /**
     * Expand branch
     *
     * @param parentBranchDOM Container
     * @param branch Data to expand
     * @param branchId
     * @return {*}
     */
    this.expandBranch = function (parentBranchDOM, branch, branchId) {

        if (parentBranchDOM.length == 0) {
            return;
        }

        if (branchId == undefined) {
            branchId = 0;
        }

        // Add branches to parent branch
        var newBranchDOM = $("<ul></ul>");

        // Link to create new branch
        var html = "<li>";
        html += "<a class=\"branch-add\" data-id=\"" + branchId + "\" href=\"#\">" + this._languageLabels["text_add_here"] + "...</a>";
        html += "</li>";

        var branchBody = $(html);
        branchBody.find("a.branch-add").click(this.handlerBranchAddLinkClick);
        newBranchDOM.append(branchBody);

        parentBranchDOM.append(newBranchDOM);

        // Show all children branches
        var sortedBranches = [];
        for (child in branch) {
            var item = branch[child];

            // For some branches only children with negative sort order is allowed
            if (branchId > 0 && this._isModeActiveNso && item.sort_order > -1) {
                 continue;
            }

            sortedBranches.push(branch[child]);
        }
        sortedBranches.sort(function (a, b) {

            var aName = a.name.toLowerCase();
            var bName = b.name.toLowerCase();

            if (aName > bName) {
                return 1;
            } else if (aName < bName) {
                return -1;
            } else {
                return 0;
            }

        });

        for (var i = 0; i < sortedBranches.length; ++ i) {

            // Create branch
            var item = sortedBranches[i];

            html = "<li>";
            html += "<a class=\"branch\" data-id=\"" + item.branch_id + "\" href=\"#\">" + item.name + "</a> - ";
            html += "(<a class=\"branch-edit\" data-id=\"" + item.branch_id + "\" href=\"#\">"
                + this._languageLabels["text_edit_short"] + "</a>, ";
            if (branchId > 0) {
                html += "<a class=\"branch-delete\" data-id=\"" + item.branch_id + "\" href=\"#\">"
                    + this._languageLabels["text_delete_short"] + "</a>, ";
            }

            var isShouldHaveAbillityToClean = this._branchesIdsWhichShouldHaveAbillityToClean.indexOf(parseInt(item.branch_id)) > -1;
            if (isShouldHaveAbillityToClean) {
                html += "<a class=\"clean\" data-id=\"" + item.branch_id + "\" href=\"#\">"
                    + this._languageLabels["text_branch_clean"] + "</a>, ";
            }

            html += "<a class=\"show-products\" data-id=\"" + item.branch_id + "\" href=\"#\">"
                + this._languageLabels["text_products_short"] + " - " + item.products_total_counts + "</a>)";
            html += "</li>";

            var branchBody = $(html);
            newBranchDOM.append(branchBody);

            // Set branch click handlers
            branchBody.find("a.branch").click(function (event) {
                event.preventDefault();
                var anchor = $(this);
                var branchId = anchor.data("id");
                thisObj.handlerBranchClick(branchId, anchor.parent());
            });
            branchBody.find("a.branch-edit").click(this.handlerBranchEditClick);
            branchBody.find("a.branch-delete").click(this.handlerBranchDeleteClick);
            branchBody.find("a.show-products").click(this.handlerShowProductsClick);

            if (isShouldHaveAbillityToClean) {
                branchBody.find("a.clean").click(this.handlerBranchCleanClick);
            }

        }

        return newBranchDOM;
    };

    /**
     * Handler executes when delete link is clicked
     *
     * @param event
     * @returns {boolean}
     */
    this.handlerBranchDeleteClick = function (event) {

        event.preventDefault();

        var categoryId = $(this).data("id");

        if (!confirm(thisObj._languageLabels["text_confirm_action"])) {
            return false;
        }

        var callback = function (id) {
            var anchor = $("a.branch-delete[data-id=\"" + id + "\"]");
            if (anchor.length == 0) {
                return;
            }
            anchor.parent().remove();
        };
        thisObj._removeBranch(categoryId, callback);
    }

    /**
     * Handler executes when clean link is clicked
     *
     * @param event
     */
    this.handlerBranchCleanClick = function (event) {

        event.preventDefault();

        var categoryId = $(this).data("id");

        if (confirm(thisObj._languageLabels["text_confirm_action"])) {
            $.ajax({
                type: "POST",
                url: "/admin/index.php?route=module/structure_editor/handlerCleanCategory&token=" + thisObj.tree.getToken(),
                data: {category_id: categoryId},
                dataType: "json",
                success: function (response) {

                    if (!response.success) {
                        console.error("Can`t clean branch #" + categoryId);
                        return;
                    }

                    thisObj.productsListView.loadProducts(categoryId, 0);
                }
            });
        }
    }

    /**
     * Handler executes when show products link is clicked
     *
     * @param event
     */
    this.handlerShowProductsClick = function (event) {
        event.preventDefault();

        var anchor = $(this);
        var branchId = anchor.data("id");
        var branchData = thisObj.tree.getBranchById(branchId);
        if (!branchData) {
            return;
        }

        // Load clicked branch products
        thisObj.productsListView.loadProducts(branchId);
    }

    /**
     * Handler executes when branch is clicked
     *
     * @param branchId
     */
    this.handlerBranchClick = function (branchId, parentBranchDOM) {

        // Check branch already expanded
        if (thisObj.expandedBranches.hasOwnProperty(branchId)) {
            thisObj.expandedBranches[branchId].remove();
            delete thisObj.expandedBranches[branchId];
            return;
        }

        // Get branch data
        var branchData = thisObj.tree.getBranchById(branchId);
        if (!branchData) {
            return;
        }

        // Create new branch
        if (parentBranchDOM === undefined) {
            parentBranchDOM = $("a[data-id=\"" + branchId + "\"]").parent();
        }

        if (parentBranchDOM.length > 0) {
            thisObj.expandedBranches[branchId] = thisObj.expandBranch(parentBranchDOM, branchData.children, branchId);
        }
    };

    /**
     * Handler executes when branch edit link is clicked
     *
     * @param event
     */
    this.handlerBranchEditClick = function (event) {
        event.preventDefault();

        var th = $(this);
        var branchId = th.data("id");
        var link = "/admin/index.php?route=catalog/category/edit&token=" + thisObj.tree.getToken()
            + "&category_id=" + branchId
            + "&is_iframe=1";

        var popup = window.open(link, "", "toolbar=no,scrollbars=no,location=no,statusbar=no,menubar=no,resizable=0");
        if (popup !== undefined) {
            popup.focus();
            thisObj._branchDialogs[branchId] = popup;
        }
    }

    /**
     * Handler executes when branch edit window is unload
     *
     * @param branchId
     */
    this.handlerBranchEditWindowUnload = function (branchId) {
        if (!this._branchDialogs.hasOwnProperty(branchId)) {
            return;
        }

        if (branchId > 0) {

            var closeDialog = function (id) {
                thisObj._branchDialogs[branchId].close();
            }

            this._updateBranch(branchId, closeDialog);
        }
    }

    /**
     * Handler execute when a branch add window is unload
     *
     * @param branchId
     * @param parentBranchId
     * @param callback
     */
    this.handlerBranchAddWindowUnload = function (branchId, parentBranchId, callback) {

        $.ajax({
            url: "/admin/index.php?route=module/structure_editor/handlerGetCategoryData&token=" + thisObj.tree.getToken(),
            type: "POST",
            dataType: "json",
            data: {category_id: branchId},
            success: function (response) {

                // Check a response
                if (!response.success) {
                    console.log("Server error!");
                    return;
                }
                console.log("Success!");

                if (!response.hasOwnProperty("data")) {
                    console.log("Invalid response from server!");
                    return;
                }

                // Add new branch
                thisObj._addNewBranch(branchId, parentBranchId, response.data.name);

                // Callback
                if (callback !== undefined) {
                    callback();
                }
            }

        });

        callback();
    }

    /**
     * Remove branch
     *
     * @param branchId
     * @param callback
     * @private
     */
    this._removeBranch = function (branchId, callback) {

        console.log("Removing category...");

        $.ajax({
            url: "/admin/index.php?route=module/structure_editor/handlerDeleteCategory&token=" + thisObj.tree.getToken(),
            type: "POST",
            dataType: "json",
            data: {category_id: branchId},
            success: function (response) {

                if (!response.success) {
                    console.log("Server error!");
                    return;
                }
                console.log("Success!");

                callback(branchId);
            }
        });

    }

    /**
     * Update branch
     *
     * @param branchId
     * @private
     */
    this._updateBranch = function (branchId, callback) {

        console.log("Updating branch #" + branchId + " name");

        $.ajax({
            type: "POST",
            "url": "/admin/index.php?route=module/structure_editor/handlerGetCategoryData&token=" + thisObj.tree.getToken(),
            dataType: "json",
            data: {category_id: branchId},
            success: function (response) {
                if (!response.success) {
                    console.log("Server error!");
                    return;
                }

                if (!response.hasOwnProperty("data")) {
                    console.log("Invalid response from server! A property \"data\" is not exists");
                    return;
                }

                // Update branch name
                $("a.branch[data-id=\"" + branchId + "\"]").html(response.data.name);

                // Close dialog
                callback(branchId);
            }
        });
    }

    /**
     * Add a new branch
     *
     * @param branchId
     * @param parentBranchId
     * @param callback
     * @private
     */
    this._addNewBranch = function (branchId, parentBranchId, name, callback) {

        var listDOM = $("a[data-id=\"" + parentBranchId + "\"]").parent().find("ul:first");
        if (listDOM.length == 0) {
            console.log('Can`t find list element');
            return;
        }

        var html = "<li>";
        html += "<a class=\"branch\" data-id=\"" + branchId + "\" href=\"#\">" + name + "</a>";
        html += " - (<a class=\"branch-edit\" data-id=\"" + branchId + "\" href=\"#\">" + this._languageLabels["text_edit_short"] + "</a>,";
        html += " <a class=\"branch-delete\" data-id=\"" + branchId + "\" href=\"#\">" + this._languageLabels["text_delete_short"] + "</a>,";
        html += " <a class=\"show-products\" data-id=\"" + branchId + "\" href=\"#\">" + this._languageLabels["text_products_short"] + "</a>)";
        html += "</li>";

        var branchDOM = $(html);
        listDOM.append(branchDOM);

        // Set branch click handlers
        branchDOM.find("a.branch").click(function (event) {
            event.preventDefault();
            var anchor = $(this);
            var branchId = anchor.data("id");
            thisObj.handlerBranchClick(branchId, anchor.parent());
        });
        branchDOM.find("a.branch-edit").click(thisObj.handlerBranchEditClick);
        branchDOM.find("a.branch-delete").click(thisObj.handlerBranchDeleteClick);
        branchDOM.find("a.show-products").click(thisObj.handlerShowProductsClick);

        thisObj.tree.addBranch(parentBranchId, {
            "name": name,
            branch_id: branchId
        });

        if (callback !== undefined) {
            callback(branchId);
        }
    }

    /**
     * Handler executes when the branch add link is clicked
     */
    this.handlerBranchAddLinkClick = function (event) {
        event.preventDefault();

        var th = $(this);
        var branchId = th.data("id");
        var link = "/admin/index.php?route=catalog/category/add&token=" + thisObj.tree.getToken()
            + "&parent_id=" + branchId
            + "&is_iframe=1";

        window.open(link, "", "toolbar=no,scrollbars=no,location=no,statusbar=no,menubar=no,resizable=0");
    }


    /**
     * Set negative sort only mode as active
     */
    this.setModeNsoActive = function () {
        this._isModeActiveNso = true;
    }

    /**
     * Set negative sort only mode as disabled
     */
    this.setModeNsoDisabled = function () {
        this._isModeActiveNso = false;
    }

    /**
     * Get status of negative only sort mode
     *
     * @returns {boolean}
     */
    this.isModeNsoActive = function () {
        return this._isModeActiveNso;
    }

    /**
     * Collapse branch
     *
     * @param domBranch
     */
    this.collapseBranch = function (domBranch) {
        domBranch.remove();
    };

    // Properties
    this.tree = tree;
    this.productsListView = productsListView;
    this.expandedBranches = {};
    var thisObj = this;
    this._languageLabels = languageLabels;
    this._branchesIdsWhichShouldHaveAbillityToClean = [1];
    this._isModeActiveNso = false;
    this._branchDialogs = {};

    // Find the root element
    this._domRoot = $("div#" + rootBranchDOMId);
    if (this._domRoot.length == 0) {
        throw new Error("The dom root is not found");
    }

    // Show root branch
    this.expandedBranches[0] = this.expandBranch(this._domRoot, this.tree.getInternalTree(), 0);
}