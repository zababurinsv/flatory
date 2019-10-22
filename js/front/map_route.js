;
/**
 * Map with Route
 * @param {float} x - coord x
 * @param {float} y - coord y
 * @param {string} mapId - element id for map
 * @param {object} settings - map config {width: 'string', height: 'string', controls: [array]}<br>
 * @use ymaps 2.1
 * @see https://tech.yandex.ru/maps/doc/jsapi/2.1/dg/concepts/about-docpage/
 * @returns {FlMapRoute.app.publ|app.publ}
 */
var FlMapRoute = function (x, y, mapId, settings) {

    var app = {
        init: function (x, y, mapId, settings) {

            if (typeof ymaps !== 'object')
                throw new Error('ymaps not found!');

            if (!$('#' + mapId).length)
                throw new Error('map place not found!');

            // устанавливаем координаты объекта
            this.objectCoords = [y, x];

            // определяем место для карты
            this.mapPlace = $('#' + mapId);
            this.config = {
                center: this.objectCoords,
                zoom: 9,
                controls: []
            };

            if (typeof settings === 'object') {

                if (typeof settings.width === 'string' && !!settings.width)
                    this.mapPlace.width(settings.width);

                if (typeof settings.height === 'string' && !!settings.height)
                    this.mapPlace.height(settings.height);

                if ($.isArray(settings.controls))
                    this.config.controls = settings.controls;
            }

            ymaps.ready(function () {
                app.map = new ymaps.Map(app.mapId, app.config);

                // create object manager
                app.objectManager = new ymaps.ObjectManager({
                    // use clusterize
                    clusterize: true,
                    // hide placemark with opening baloon
                    geoObjectOpenBalloonOnClick: false
                });

                // Чтобы задать опции одиночным объектам и кластерам,
                // обратимся к дочерним коллекциям ObjectManager.
                app.objectManager.objects.options.set({
                    preset: 'islands#yellowDotIcon'
                });

                // добавляем метку объекта на карту
                app.objectManager.add({
                    type: "FeatureCollection",
                    features: [
                        {type: "Feature", geometry: {type: "Point", coordinates: app.objectCoords}}
                    ]
                });

                // добавляем менеджер объектов на карту
                app.map.geoObjects.add(app.objectManager);

                // определяем кнопки типов марщрутов
                var typeList = new ymaps.control.ListBox({
                    data: {
                        content: 'Как добраться'
                    },
                    items: [
                        new ymaps.control.ListBoxItem({
                            data: {
                                content: 'На машине',
                                type: 'auto'
                            },
                            options: {
                                selectOnClick: false
                            }
                        }),
                        new ymaps.control.ListBoxItem({
                            data: {
                                content: 'На общественном транспорте',
                                type: 'masstransport'
                            },
                            options: {
                                selectOnClick: false
                            }
                        })
                    ]
                });
                
                typeList.events.add('click', function (e) {
                    // Получаем ссылку на объект, по которому кликнули.
                    // События элементов списка пропагируются
                    // и их можно слушать на родительском элементе.
                    var item = e.get('target'),
                            // последний тип маршрута
                            lastRouteType = app.routType;

                    // type not found - no changes
                    if ($.inArray(item.data.get('type'), ['auto', 'masstransport']) === -1)
                        return;

                    // устанавливаем тип маршрута
                    app.routType = item.data.get('type');
                    // сворачиваем выпадающее меню
                    typeList.collapse();
                    // перерисовываем маршрут если поменялся тип и есть координаты отправления и данный тип маршрута существует
                    if (app.routType !== lastRouteType && app.routeReferencePoint && typeof app.routes[app.routType] === 'function')
                        app.routes[app.routType].call();

                });


                // определяем тип маршрута по умолчанию
                app.routType = 'auto';

                // Обработка события, возникающего при щелчке
                // левой кнопкой мыши в любой точке карты.
                // При возникновении такого события откроем балун.
                app.map.events.add('click', function (e) {
                    // определяем точку клика
                    app.routeReferencePoint = e.get('coords');
                    // Добавляем мультимаршрут на карту.
                    if (typeof app.routes[app.routType] !== 'function') {
                        console.log('route type not found!');
                        return;
                    }
                    app.routes[app.routType].call();
                });

                app.map.controls.add(typeList, {floatIndex: 0});
            });

            this.setUpListeners();
            this.mapId = mapId;
        },
        /**
         * @todo - пересмотреть подход к инициализации ymaps.multiRouter.MultiRoute
         */
        routes: {
            auto: function () {

                if (typeof app.multiRoute === 'object' && typeof app.multiRoute.setParent === 'function')
                    app.multiRoute.setParent(null);

                app.multiRoute = new ymaps.multiRouter.MultiRoute({
                    // Описание опорных точек мультимаршрута.
                    referencePoints: [app.routeReferencePoint, app.objectCoords],
                    // Параметры маршрутизации.
                    params: {
                        // Ограничение на максимальное количество маршрутов, возвращаемое маршрутизатором.
                        results: 2
                    }
                }, {
                    // Автоматически устанавливать границы карты так, чтобы маршрут был виден целиком.
                    boundsAutoApply: true,
                    // Включаем режим панели для балуна.
                    balloonPanelMaxMapArea: Infinity
                });
                app.map.geoObjects.add(app.multiRoute);
            },
            masstransport: function () {
                if (typeof app.multiRoute === 'object' && typeof app.multiRoute.setParent === 'function')
                    app.multiRoute.setParent(null);

                app.multiRoute = new ymaps.multiRouter.MultiRoute({
                    // Описание опорных точек мультимаршрута.
                    referencePoints: [app.routeReferencePoint, app.objectCoords],
                    // Параметры маршрутизации.
                    params: {
                        routingMode: 'masstransit'
                    }
                }, {
                    // Автоматически устанавливать границы карты так, чтобы маршрут был виден целиком.
                    boundsAutoApply: true,
                    // Включаем режим панели для балуна.
                    balloonPanelMaxMapArea: Infinity
                });
                app.map.geoObjects.add(app.multiRoute);
            }
        },
        setUpListeners: function () {

        },
        publ: {
            getId: function () {
                return app.mapId;
            },
            get: function (name) {
                return app[name];
            }
        }
    };

    app.init(x, y, mapId, settings);
    return app.publ;

};