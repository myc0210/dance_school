'use strict';

app.controller('AdminProductGeneralController',
    [        '$scope', '$http',
    function ($scope,   $http) {
        $scope.page = {
            title: ''
        };
    }]
)
.controller('AdminProductListController',
    [        '$scope', '$resource', '$compile', '$state', 'DTOptionsBuilder', 'DTColumnBuilder', 'SweetAlert', 'ngToast', 'AdminProductService',
    function ($scope,   $resource,   $compile,   $state,   DTOptionsBuilder,       DTColumnBuilder,   SweetAlert,   ngToast,   AdminProductService) {
        $scope.page.title = 'Product List';
        $scope.page.buttons = [
            {
                function: function goToCreate() {
                    $state.go('admin.product.create');
                },
                name: 'Create New Product'
            }
        ];

        $scope.dtInstance = {};

        $scope.dtOptions = DTOptionsBuilder.fromFnPromise(function() {
            return $resource('product/product-list').query().$promise;
        })
            .withPaginationType('full_numbers')
            .withOption('createdRow', createdRow);

        $scope.dtColumns =  [
            //DTColumnBuilder.newColumn('id').withTitle('ID'),
            DTColumnBuilder.newColumn('name').withTitle('Product Name'),
            DTColumnBuilder.newColumn('code').withTitle('Product Code'),
            DTColumnBuilder.newColumn('category_name').withTitle('Category'),
            DTColumnBuilder.newColumn('subcategory_name').withTitle('Subcategory'),
            DTColumnBuilder.newColumn('level').withTitle('Suitable Course Level'),
            DTColumnBuilder.newColumn('size').withTitle('Size'),
            DTColumnBuilder.newColumn('color').withTitle('Color'),
            DTColumnBuilder.newColumn('cost').withTitle('cost'),
            DTColumnBuilder.newColumn('price').withTitle('price'),
            DTColumnBuilder.newColumn(null).withTitle('Actions').notSortable()
                .renderWith(actionsHtml)
        ];

        $scope.edit = function (id) {
            $state.go('admin.product.edit', {productId: id});
        }

        $scope.delete = function (id) {
            SweetAlert.swal({
                    title: "Are you sure?",
                    text: "Your will not be able to recover!",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "Yes, delete it!",
                    cancelButtonText: "No, cancel plx!"
                },
                function(isConfirm){
                    if (isConfirm) {
                        var waitToast = ngToast.create({
                            className: 'info',
                            content: '<div class="timer-loader"></div>Processing...'
                        });
                        var promise = AdminProductService.variationProductDelete(id);
                        promise.then(
                            function (data) {
                                ngToast.dismiss(waitToast);
                                ngToast.create({
                                    className: 'success',
                                    dismissOnTimeout: true,
                                    content: 'Success!'
                                });
                                $scope.dtInstance.reloadData();
                            },
                            function (reason) {
                                ngToast.dismiss(waitToast);
                                ngToast.create({
                                    className: 'danger',
                                    dismissOnTimeout: true,
                                    content: 'Error: ' + reason + '!'
                                });
                            }
                        );
                    }
                });
        }

        function createdRow(row, data, dataIndex) {
            // Recompiling so we can bind Angular directive to the DT
            $compile(angular.element(row).contents())($scope);
        }

        function actionsHtml(data, type, full, meta) {
            return '<button class="btn btn-primary" ng-click="edit(' + data.id + ')">' +
                '   <i class="fa fa-pencil-square-o"></i>' +
                '</button>&nbsp;' +
                '<button class="btn btn-danger" ng-click="delete(' + data.id + ')">' +
                '   <i class="fa fa-trash-o"></i>' +
                '</button>';
        }
    }]
)
.controller('AdminProductCreateController',
    [        '$scope', '$http', '$state', '$localStorage', 'ngToast', 'AdminProductService', 'preloadCategories',
    function ($scope,   $http,   $state,   $localStorage,   ngToast,   AdminProductService,   preloadCategories) {
        var variationProductTemplate = {
            code: '',
            level: '',
            color: '',
            size: '',
            quantity: '',
            cost: '',
            price: ''
        };

        $scope.page.title = 'Add New Product';
        $scope.csrf = {value: ''};
        $scope.attachedImages = [];

        $scope.init = function () {
            var variationProduct = {},
                productCache = $localStorage.adminProductCache,
                attachedImages = $localStorage.attachedImages;
            $scope.categories = preloadCategories.categories;
            $scope.subcategories = [];

            if (productCache) {
                $scope.product = productCache;
                $scope.product.category = $localStorage.selectedCategory;
                $scope.subcategories = $localStorage.selectedCategory.subCategories;
                $scope.product.subcategory = $localStorage.selectedSubcategory;
            } else {
                $scope.product = {
                    name: '',
                    variationProducts: [
                        variationProduct,
                    ],
                    description: ''
                };
            }



            if (attachedImages) {
                $scope.product.attachedImages = attachedImages;
            }
        };

        $scope.addImages = function () {
            $localStorage.adminProductCache = $scope.product;
            $localStorage.selectedCategory = $scope.product.category;
            $localStorage.selectedSubcategory = $scope.product.subcategory;
            $state.go('admin.media-manager', {select: true});
        };

        $scope.addCategories = function () {
            $localStorage.adminProductCache = $scope.product;
            $state.go('admin.product.category')
        };

        $scope.duplicate = function (variationProductSrc) {
            var variationProduct = {};
            angular.copy(variationProductSrc, variationProduct);
            $scope.product.variationProducts.push(variationProduct);
        };

        $scope.deleteVariationProduct = function (index) {
            if ($scope.course.variationProducts.length > 1) {
                $scope.product.variationProducts.splice(index, 1);
            }
        };

        $scope.newVariationProduct = function () {
            var variationProduct = {};
            angular.copy(variationProductTemplate, variationProduct);
            $scope.product.variationProducts.push(variationProduct);
        };

        $scope.saveProduct = function () {
            var waitToast = ngToast.create({
                className: 'info',
                content: '<div class="timer-loader"></div>Processing...'
            });
            var promise = AdminProductService.productCreate($scope.product);
            promise.then(
                function (data) {
                    ngToast.dismiss(waitToast);
                    ngToast.create({
                        className: 'success',
                        dismissOnTimeout: true,
                        content: 'Success!'
                    });

                    delete $localStorage.adminProductCache;
                    delete $localStorage.attachedImages;
                    delete $localStorage.defaultImageIndex;

                    $state.transitionTo($state.current, {}, {
                        reload: true
                    });
                },
                function (data) {
                    ngToast.dismiss(waitToast);
                    ngToast.create({
                        className: 'danger',
                        dismissOnTimeout: true,
                        content: 'System Error: ' + data.error
                    });
                }
            );
        };

        $scope.thumbnailSetDefault = function (index) {
            var defaultImage = $scope.product.attachedImages[index];

            angular.forEach($scope.product.attachedImages, function (value, key, object) {
                if (value.default == true) {
                    object[key].default = false;
                }
            });

            defaultImage.default = true;
            //$scope.product.showcaseDetail.image = defaultImage;

            $localStorage.defaultImageIndex = index;
        };

        $scope.thumbnailDelete = function (index) {
            var attachedImages = $scope.product.attachedImages;

            attachedImages.splice(index, 1);

            if (attachedImages.length == 0) {
                delete $localStorage.defaultImageIndex;
            } else {
                attachedImages[0].default = true;
                $localStorage.defaultImageIndex = 0;
            }

        };

        $scope.updateSubcategories = function () {
            console.log($scope.product.category);
            $scope.subcategories = $scope.product.category.subCategories;
        };

        $scope.init();
    }]
)
.controller('AdminProductUpdateController',
    [   '$scope', '$state', '$http', '$localStorage', 'ngToast', 'AdminProductService', 'preloadProduct', 'preloadCategories', '$log',
function($scope,   $state,   $http,   $localStorage,   ngToast,   AdminProductService, preloadProduct,    preloadCategories, $log) {
    var variationProductTemplate = {
        code: '',
        level: '',
        color: '',
        size: '',
        quantity: '',
        cost: '',
        price: ''
    };

    $scope.page.title = 'Edit Product';
    $scope.csrf = {value: ''};
    $scope.attachedImages = [];

    $scope.init = function () {
        var variationProducts = [{
                id: preloadProduct.id,
                code: preloadProduct.code,
                level: preloadProduct.level,
                color: preloadProduct.color,
                size: preloadProduct.size,
                quantity: preloadProduct.quantity,
                cost: preloadProduct.cost,
                price: preloadProduct.price,
            }],
            product = {
                id: preloadProduct.product_id,
                name: preloadProduct.name,
                description: preloadProduct.description,
                variationProducts: variationProducts
            },
            productEditCache = $localStorage.productEditCache,
            attachedImages = JSON.parse(preloadProduct.gallery_detail);

        $scope.categories = preloadCategories.categories;

        angular.forEach(preloadCategories.categories, function(value, key) {
            if (value.id == preloadProduct.category_id) {
                $scope.subcategories = value.subcategories;
                product.category = value;
            } else if (value.id == preloadProduct.subcategory_id) {
                product.subcategories = value;
            }
        });

        if (preloadProduct.subcategory_id == 0) {
            $scope.subcategories = [];
        }

        if (productEditCache) {
            $scope.product = productEditCache;
        } else {
            $scope.product = product;
            $scope.product.attachedImages = attachedImages;
            $log.log($scope);
        }
    };

    $scope.addImages = function () {
        $localStorage.productEditCache = $scope.product;
        $state.go('admin.media-manager', {select: true});
    };

    $scope.addCategories = function () {
        $localStorage.adminProductCache = $scope.product;
        $state.go('admin.product.category')
    };

    $scope.duplicate = function (variationProductSrc) {
        var variationProduct = {};
        angular.copy(variationProductSrc, variationProduct);
        $scope.product.variationProducts.push(variationProduct);
    };

    $scope.deleteVariationProduct = function (index) {
        $scope.product.variationProducts.splice(index, 1);
    };

    $scope.newVariationProduct = function () {
        var variationProduct = {};
        angular.copy(variationProductTemplate, variationProduct);
        $scope.product.variationProducts.push(variationProduct);
    };

    $scope.saveProduct = function () {
        var waitToast = ngToast.create({
            className: 'info',
            content: '<div class="timer-loader"></div>Processing...'
        });
        var promise = AdminProductService.variationProductEdit($scope.product);
        promise.then(
            function (data) {
                ngToast.dismiss(waitToast);
                ngToast.create({
                    className: 'success',
                    dismissOnTimeout: true,
                    content: 'Success!'
                });

                //delete $localStorage.productEditCache;
                //
                //$state.transitionTo('admin.product.list', {}, {
                //    reload: true
                //});
            },
            function (data) {
                ngToast.dismiss(waitToast);
                ngToast.create({
                    className: 'danger',
                    dismissOnTimeout: true,
                    content: 'System Error: ' + data.error
                });
            }
        );
    };

    $scope.thumbnailSetDefault = function (index) {
        var defaultImage = $scope.product.attachedImages[index];

        angular.forEach($scope.product.attachedImages, function (value, key, object) {
            if (value.default == true) {
                object[key].default = false;
            }
        });

        defaultImage.default = true;
        $scope.product.showcaseDetail.image = defaultImage;

        $localStorage.defaultImageIndex = index;
    };

    $scope.thumbnailDelete = function (index) {
        var attachedImages = $scope.product.attachedImages;

        if (attachedImages[index].default == true) {
            attachedImages[0].default = true;
        }

        attachedImages.splice(index, 1);

        if (attachedImages.length == 0) {
            delete $localStorage.defaultImageIndex;
        } else {
            attachedImages[0].default = true;
            $localStorage.defaultImageIndex = 0;
        }

    };

    $scope.updateSubcategories = function () {
        $scope.subcategories = $scope.product.category.subCategories;
    };

    $scope.init();
}])
.controller('AdminProductCategoryController',
    [        '$scope', '$http', '$log', '$localStorage', 'AdminProductService',
    function ($scope,   $http,   $log,   $localStorage,   AdminProductService) {
        $scope.page.title = 'Product Category List';
        $scope.csrf = {value: ''};
        $scope.uiTree = {
            maxDepth: 2
        };

        var promise = AdminProductService.listCategory();
        promise.then(
            function (data) {
                $scope.categories = data.categories;
                $scope.maxId = data.maxId;
            },
            function (reason) {
                $scope.serverError = reason;
            }
        );

        $scope.newCategory = function (scope) {
            $scope.categories.push({id: ++$scope.maxId, name: '', subCategories: []});
        };

        $scope.newSubCategory = function (scope) {
            var node = scope.$modelValue;
            if (scope.depth() < $scope.uiTree.maxDepth) {
                node.subCategories.push({id: ++$scope.maxId, name: '', subCategories: []});
            } else {
                alert('Only support 2 level category structure. Category > Subcategory');
            }
        };

        $scope.edit = function (scope) {
            var node = scope.$modelValue;
            node.edit = true;
        };

        $scope.editFinish = function (scope) {
            var node = scope.$modelValue;
            node.edit = false;
        };

        $scope.save = function () {
            var promise = AdminProductService.updateCategory($scope.categories);
            //promise.then(
            //    function (data) {
            //        $log.log(data);
            //    },
            //    function (reason) {
            //        $scope.serverError = reason;
            //    }
            //);
        };
    }]
);