angular.module('app')
    .directive('toasterContainerExt',
        function (toasterContainerDirective) {
            return angular.extend({}, toasterContainerDirective[0],
                {
                    template: '<div id="toast-container" ng-class="[config.position, config.animation]">' +
                    '<div ng-repeat="toaster in toasters" class="toast" ng-click="click(toaster)" ng-mouseover="stopTimer(toaster)" ng-mouseout="restartTimer(toaster)" style="background-color:#c8e5bc;box-shadow:0 0 0 #fff;color:#fff">' +
                    '<button type="button" class="toast-close-button" ng-show="toaster.showCloseButton" ng-click="click(toaster, true)">&times;</button>' +
                    '<div class="timer-loader">Loading…</div><span>Processing...</span>' + '</div>' + '</div>'
                });
        });