/**
 * Very primitive tree
 *
 * @param branches
 * @param token
 * @constructor
 */
function Tree (branches, token) {

    /**
     * Get branch which has child without children
     * @param branch
     * @param depth
     * @param parent
     * @returns {*}
     */
    this.getBranchWhichHasChildWithoutChildren = function (branch, depth, parent) {

        if (depth == undefined) {
            depth = 1;
        }

        if (depth < 0) {
            return branch;
        }

        if (parent == undefined) {
            parent = branch;
        }

        if (branch.hasOwnProperty("children")) {
            for (key in branch.children) {
                return this.getBranchWhichHasChildWithoutChildren(branch.children[key], --depth, parent);
            }
        } else {
            return parent;
        }

    }

    /**
     * Get branch by id
     */
    this.getBranchById = function (branchId, parentBranch, load_data, dataLoadCallback) {

        // Check params
        if (branchId == undefined) {
            return false;
        }
        if (parentBranch == undefined) {
            parentBranch = this._tree;
        }
        if (load_data == undefined) {
            load_data = true;
        }

        // Search
        if (parentBranch.hasOwnProperty(branchId)) {

            // Current branch has child we need

            var branch = parentBranch[branchId];
            // Load data for current branch`s children if it not loaded yet
            if (load_data && !this._branchesIdsForWhichDataIsAlreadyLoaded.hasOwnProperty(branchId)) {
                // Get branch which has child who does not have children
                var branchWhichHasChildWithoutChild = this.getBranchWhichHasChildWithoutChildren(branch);
                if (branchWhichHasChildWithoutChild !== undefined) {
                    // Load children list for branch`s children
                    this.loadChildrenForBranchChildren(branchWhichHasChildWithoutChild, dataLoadCallback);
                }
                // Not load data again next time
                this._branchesIdsForWhichDataIsAlreadyLoaded[branchId] = 0;
            }

            return branch;

        } else {
            // Curent branch doest not have child, search in current branch children list
            for (key in parentBranch) {
                // For each current branch item do.
                // If current branch has children
                if (parentBranch[key].hasOwnProperty("children")) {
                    // Search in children list
                    var children = parentBranch[key].children;
                    var found = this.getBranchById(branchId, children, load_data, dataLoadCallback);
                    if (found !== false) {
                        return found;
                    }
                } else {
                    // Next iteration, because current branch does not have the children
                    continue;
                }
            }
        }

        return false;
    }

    /**
     * Load children for branch children
     *
     * @param branch
     * @param dataLoadCallback
     */
    this.loadChildrenForBranchChildren = function (branch, dataLoadCallback) {
        // If branch has children then use its to build ids list,
        // else use branch id to build ids list
        var ids = [];
        if (branch.hasOwnProperty("children")) {
            for (key in branch.children) {
                ids.push(branch.children[key].branch_id);
            }
        } else {
            ids.push(branch.branch_id);
        }

        // Make non-blocking request
        $.ajax({
            type: "POST",
            url: "/admin/index.php?route=module/structure_editor/handlerGetCategoriesChildren&token=" + this._token,
            data: {"ids": ids},
            dataType: 'json',
            success: function (response) {
                if (!response.success) {
                    console.error("Can`t request children for branch children, ids: " + ids);
                    return;
                }

                if (response.data.length == undefined) {
                    console.log("Empty children search result for ids: " + ids);
                    return;
                }

                for (var i = 0; i < response.data.length; ++ i) {
                    // Child data from response
                    var child = response.data[i];

                    // Try to find parent
                    var parent = thisObj.getBranchById(child.parent_id, this._tree, false);
                    if (!parent) {
                        console.error("Parent for child with parent_id = " + child.parent_id + " is not found");
                        continue;
                    }

                    if (!parent.hasOwnProperty("children")) {
                        parent.children = {};
                    }

                    // Push child to parent`s children list
                    parent.children[child.branch_id] = child;
                }

                if (dataLoadCallback !== undefined) {
                    dataLoadCallback(response);
                }
            }
        });
    }

    /**
     * Add a branch
     *
     * @param parentBranchId
     * @param branchData
     */
    this.addBranch = function (parentBranchId, branchData) {

        var branch = this.getBranchById(parentBranchId);
        if (branch === undefined) {
            console.error("Branch #" + parentBranchId + " is not found");
            return;
        }

        if (branch.children.hasOwnProperty(branchData.branch_id)) {
            console.log("Branch #" + branchData.branch_id + " is already a child for branch #" + parentBranchId);
            return;
        }

        branch.children[branchData.branch_id] = {
            name: branchData.name,
            branch_id: branchData.branch_id,
            children: {},
            products_total_counts: 0
        };
    }

    /**
     * Get branch parent id
     *
     * @param branchId
     * @param branchesList
     * @returns {*}
     */
    this.getBranchParentId = function (branchId, branchesList) {

        if (branchesList === undefined) {
            branchesList = this._tree;
            console.log("Searching parent for branch #" + branchId);
        }

        var result = -1;

        // Search
        if (branchesList.hasOwnProperty(branchId)) {
            var recursionLevelIsZero = branchesList === this._tree;
            if (recursionLevelIsZero) {
                return 0;
            } else {
                return true;
            }
        }

        for (var currentBranchId in branchesList) {

            var branch = branchesList[currentBranchId];

            // If current branch has a property "children" and list is not empty
            if (branch.hasOwnProperty("children")) {
                // Try to find branch in a children list
                result = this.getBranchParentId(branchId, branch.children);
                if (result === true) {
                    console.log("Parent id is " + currentBranchId);
                    // Ok, it is found. Current branch it is a parent of branch which id is passed as first arg
                    return currentBranchId;
                } else if (isFinite(result) && result > -1) {
                    break;
                }
            }

        }


        return result;
    }

    this.reload = function (successCallback) {

        console.log("Reloading a tree!");

        $.ajax({
            type: "POST",
            url: "/admin/index.php?route=module/structure_editor/handlerGetTopLevelBranches&token=" + this._token,
            dataType: "json",
            success: function (response) {

                if (!response.success) {
                    console.log("Server error occur! ");
                    return;
                }

                if (!response.hasOwnProperty("data")) {
                    console.log("Invalid server response! Property \"data\" is not found!");
                    return;
                }

                var data = response.data;
                if (!data.hasOwnProperty("branches")) {
                    console.log("Invalid data from server! Property \"branches\" is not found!");
                    return;
                }

                thisObj._branchesIdsForWhichDataIsAlreadyLoaded = {};
                thisObj._tree = data.branches;

                successCallback(thisObj._tree);
            }
        });

    }

    /**
     * Mark branch data as old
     *
     * @param branchId
     */
    this.markBranchDataAsOld = function (branchId) {
        if (!this._branchesIdsForWhichDataIsAlreadyLoaded.hasOwnProperty(branchId)) {
            return;
        }

        delete this._branchesIdsForWhichDataIsAlreadyLoaded[branchId];
    }

    /**
     * Get internal tree
     *
     * @returns {*}
     */
    this.getInternalTree = function () {
        return this._tree;
    }

    /**
     * Get token
     *
     * @returns {*}
     */
    this.getToken = function () {
        return this._token;
    }

    var thisObj = this;
    this._token = token;
    this._branchesIdsForWhichDataIsAlreadyLoaded = {};

    // Internal tree
    this._tree = branches;
}