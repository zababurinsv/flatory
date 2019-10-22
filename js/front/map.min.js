;
/**
 * @use ymaps 2.1
 * @see https://tech.yandex.ru/maps/doc/jsapi/2.1/dg/concepts/about-docpage/
 * @type Function|@exp;app@pro;publ
 */
var FlMap = (function() {

    var debug = location.href.indexOf('beta') !== -1;

    var objectModel = {
        filters: {},
        search: function(successCallback, errorCallback) {
            var f = typeof this.filters === 'object' ? this.filters : {},
                    defaultCallback = function(data) {
                        console.log(data);
                    }, i = JSON.stringify(f), c;

            successCallback = typeof successCallback === 'function' ? successCallback : defaultCallback;
            errorCallback = typeof errorCallback === 'function' ? errorCallback : defaultCallback;

            if (!debug) {
                // search from cache
                c = FlCache.get(i, true);
                if (c) {
                    successCallback(c);
                    $.publish('map_objects_loaded', c);
                    return;
                }
            }

            $.getJSON('/ajax/object_search_map', f, function(response) {
                // set cache with 1 hour ttl
                FlCache.set(i, response.data || {}, 60 * 60);
                if (response.success) {
                    successCallback(response.data);
                } else {
                    errorCallback(response);
                }
                $.publish('map_objects_loaded', response);
            }).error(function(er) {
                console.log(er);
                $.publish('map_objects_loaded', er);
            });
        }
    };

    var app = {
        map: false,
        mapPlace: [],
        mapName: 'y-map',
        objectManager: false,
        config: {
            center: [55.76, 37.64],
            zoom: 9,
            controls: [],
            is_open_baloon: true
        },
        init: function() {

            this.mapPlace = $('#' + this.mapName);

            if (!this.mapPlace.length)
                throw new Error('map place not found!');

            if (typeof configFlMap === 'object') {
                for (var k in this.config) {
                    if (typeof this.config[k] === typeof configFlMap[k])
                        this.config[k] = configFlMap[k];
                }
            }

            if (this.mapPlace.hasClass('y-map-fs')) {
                this.mapPlace.width($(window).width());
                this.mapPlace.height($(window).height());
            } else {
                this.config.controls = this.config.controls.length ? this.config.controls : ['zoomControl', 'fullscreenControl'];
            }


            if (typeof ymaps !== 'object')
                throw new Error('ymaps not found!');

            ymaps.ready(function() {
                app.map = new ymaps.Map(app.mapName, app.config);

                // create object manager
                app.objectManager = new ymaps.ObjectManager({
                    // use clusterize
                    clusterize: true,
                    // hide placemark with opening baloon
                    geoObjectOpenBalloonOnClick: false
                });

                if (app.config.is_open_baloon) {
                    app.objectManager.objects.events.add('click', function(e) {
                        // set event open baloon
                        var objectId = e.get('objectId');
                        app.objectManager.objects.balloon.open(objectId);
                    });
                }
                
                app.objectManager.objects.balloon.events.add('open', function(e) {
                    // set event close baloon
                    $('.fl-bl-l__map .close').off('click').on('click', function(e) {
                        app.objectManager.objects.balloon.close();
                    });
                });

                var balloonLayout = ymaps.templateLayoutFactory.createClass('<div class="flatory-balloon-layout fl-bl-l__map">' +
                        '<div class="flatory-balloon-content f-obj-preview">' +
                        '<a href="/catalog/{{ properties.alias }}" target="_blank" class="fb-left"><img src="{{ properties.image }}"></a>' +
                        '<div class="object_list_container">' +
                        '<h3 class="f-obj-preview__name"><a href="/catalog/{{ properties.alias }}" target="_blank">{{ properties.name }}</a></h3>' +
                        '<table class="f-obj-preview__address_block"><tr>' +
                        '<td class="address_block__icon"><span class="fb-icon-xs fb-icon-address"></span></td>' +
                        '<td class="address_block__content" title="{{ properties.full_address }}">{{ properties.address }}</td></tr>' +
                        '</table>' +
                        '<div class="fb-icon fb-icon-rub">{% if properties.cost_min != 0 %}от <h2>{{ properties.cost_min }}</h2> руб.{% else %}<span>−</span>{% endif %}</div>' +
                        '<div class="fb-icon fb-icon-space">' +
                        '{% if properties.space_min && properties.space_max %}' +
                        'от <h2>{{ properties.space_min }}</h2> до <h2>{{ properties.space_max }}</h2> м<sup>2</sup>' +
                        '{% elseif properties.space_min %}' +
                        'от <h2>{{ properties.space_min }}</h2> м<sup>2</sup>' +
                        '{% elseif properties.space_max %}' +
                        'до <h2>{{ properties.space_max }}</h2> м<sup>2</sup>' +
                        '{% else %}' +
                        '<span>−</span>' +
                        '{% endif %}' +
                        '</div>' +
                        '{% if properties.delivery %}<div class="fb-gray"><strong>Срок ввода: </strong><span>{{ properties.delivery }}</span></div>{% endif%}' +
                        '</div>' +
                        '</div>' +
                        '<div class="close"></div>' +
                        '<div class="flatory-balloon-tail"></div>' +
                        '</div>');

                // Чтобы задать опции одиночным объектам и кластерам,
                // обратимся к дочерним коллекциям ObjectManager.
                app.objectManager.objects.options.set({
                    preset: 'islands#yellowDotIcon',
                    balloonLayout: balloonLayout
                });
                app.objectManager.clusters.options.set({preset: 'islands#yellowClusterIcons'});

                app.map.geoObjects.add(app.objectManager);

                $.publish('fl_map_ready');

                // default search - deprecated
//                objectModel.search(function(data) {
//                    app.objectManager.add(data);
//                });

            });

            this.setUpListeners();
        },
        setUpListeners: function() {
        },
        publ: {
            getMap: function() {
                return app.map;
            },
            getObjectManager: function() {
                return app.objectManager;
            },
            /**
             * search objects async
             * @event map_objects_loaded - return response.data
             * @param {object} filters
             * @returns {undefined}
             */
            searchObjects: function(filters) {
                // remove objects form map                
                app.objectManager.removeAll();
                // set filters
                objectModel.filters = typeof filters === 'object' ? filters : {};
                // get & render objects on map
                objectModel.search(function(data) {
                    app.objectManager.add(data);
                });
            },
            setObjects: function(data) {
                // remove objects form map                
                app.objectManager.removeAll();
                if (typeof data === 'object')
                    app.objectManager.add(data);
            }
        }
    };
    app.init();
    return app.publ;
}());