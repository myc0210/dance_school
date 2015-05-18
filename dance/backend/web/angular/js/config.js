// config

var app =  
angular.module('app')
  .config(
    [        '$controllerProvider', '$compileProvider', '$filterProvider', '$httpProvider', '$locationProvider', '$provide',
    function ($controllerProvider,   $compileProvider,   $filterProvider,   $httpProvider,   $locationProvider,   $provide) {

        // lazy controller, directive and service
        app.controller   = $controllerProvider.register;
        app.directive    = $compileProvider.directive;
        app.filter       = $filterProvider.register;
        app.factory      = $provide.factory;
        app.service      = $provide.service;
        app.constant     = $provide.constant;
        app.value        = $provide.value;

        $httpProvider.interceptors.push(function ($q) {
            return {
                request: function (request) {
                    request.url = 'http://localhost/school/dance/backend/web/' + request.url;
                    return request || $q.when(request);
                }
            }
        });

        // enable html5mode which vanish '#' in the url
        $locationProvider.html5Mode({
          enabled: true,
          requireBase: true
        });
    }
  ]).run(

    );
  // .config(['$translateProvider', function($translateProvider){
  //   // Register a loader for the static files
  //   // So, the module will search missing translation tables under the specified urls.
  //   // Those urls are [prefix][langKey][suffix].
  //   $translateProvider.useStaticFilesLoader({
  //     prefix: 'l10n/',
  //     suffix: '.js'
  //   });
  //   // Tell the module what language to use by default
  //   $translateProvider.preferredLanguage('en');
  //   // Tell the module to store the language in the local storage
  //   $translateProvider.useLocalStorage();
  // }]);