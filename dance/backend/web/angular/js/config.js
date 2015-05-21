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
                    var filter = ['template'],
                        untouch = false;

                    filter.forEach(function (elem, index, self) {
                        if(request.url.indexOf(elem) === 0) {
                            untouch = true;
                        }
                    });

                    if (untouch == false) {
                        request.url = 'http://localhost/school/dance/backend/web/' + request.url;
                    }

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
  ])
  .run();