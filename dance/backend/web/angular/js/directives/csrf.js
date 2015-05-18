angular.module('app')
    .directive('csrfToken',
        [       '$timeout',
    function (   $timeout) {
        return {
            restrict: 'E',
            scope: {
                csrf: '=parentScope'
            },
            link: function ($scope, $element, $attrs) {
                $timeout( function() {
                    $scope.csrf.value = $attrs.value;
                }, 100);
            }
        };
    }]);