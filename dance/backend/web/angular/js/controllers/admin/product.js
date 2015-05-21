'use strict';

app.controller('ProductGeneralController',
    [        '$scope', '$http',
    function ($scope,   $http) {
        $scope.page = {
            title: ''
        };
    }]
)
.controller('ProductListController',
    [        '$scope', '$http',
    function ($scope,   $http) {
        $scope.page.title = 'Product List';
        $scope.product = {};

    }]
)
.controller('ProductAddController',
    [        '$scope', '$http',
    function ($scope,   $http) {
        $scope.page.title = 'Add New Product';
        $scope.product = {
            name: '',
            code: '',
            color: 'N/A',
            size: 'N/A',
            price: '0.00',
            cost: '0.00',
            description: ''
        };
        $scope.csrf = {value: ''};

    }]
)
.controller('ProductCategoryController',
    [        '$scope', '$http',
    function ($scope,   $http) {
        $scope.page.title = 'Add New Product -> Select Category';
        $scope.categories = [
            {
                name: '1',
                subCategories: [
                    {
                        name: '1.1',
                        subCategories: []
                    },
                    {
                        name: '1.2',
                        subCategories: []
                    }
                ]
            },
            {
                name: '2',
                subCategories: [
                    {
                        name: '2.1',
                        subCategories: []
                    }
                ]
            },
        ];
        $scope.csrf = {value: ''};

        $scope.remove = function(scope) {
            scope.remove();
        };

        $scope.toggle = function(scope) {
            console.log('toggle');
            console.log(scope);
            scope.toggle();
        };
    }]
);