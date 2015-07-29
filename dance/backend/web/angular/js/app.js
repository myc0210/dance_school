'use strict';


angular.module('app', [
    'ngAnimate',
    'ngCookies',
    'ngResource',
    'ngSanitize',
    'ngTouch',
    'ngStorage',
    'ui.router',
    'ct.ui.router.extras',
    'ui.bootstrap',
    'ui.utils',
    'ui.load',
    'ui.jq',
    'ui.select',
    'oc.lazyLoad',
    'datatables',
    'oitozero.ngSweetAlert',
    'ngToast'
])
.factory('DTLoadingTemplate', dtLoadingTemplate);

function dtLoadingTemplate() {
    return {
        html: '<div style="position:relative; margin:0 auto; text-align: center; font-size: 20px; height: 240px;"><div class="throbber-loader" style="top: 40%;">Loading…</div></div>'
    };
};