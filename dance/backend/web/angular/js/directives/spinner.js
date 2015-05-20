angular.module('app')
    .directive('mycSpinner', function () {
        return {
            restrict: 'A',
            template: '<div class="timer-loader" ng-show="enable">Loading...</div>'
        };
    });