var FlMap = (function() {
    var app = {
        map: {},
        zone: [],
        init: function() {
            this._defineZone();
            this._initMap();
            this._getObjects();

            app.setUpListeners();

        },
        setUpListeners: function() {
            $('.mark').on('click', app._defineZone);
            $(window).on("resize", app._resize);
        },
        _defineZone: function(e) {
            app.zone = [];
            $('.mark-active').each(function(a, b) {
                app.zone.push($(b).data('geo'));
            });
        },
        _resize: function(e) {
            app._initMap();
            app._getObjects();
        },
        _initMap: function() {

            $('.fiters_bar').height($(window).height() - 70);

            if ($(window).height() < 700)
                $('.fiters_bar').css({overflow: 'auto'});
            else
                $('.fiters_bar').css({overflow: 'hidden'})

            $('#YMapsmap').empty();
            $('#YMapsmap').width($(window).width() - 300);
            $('#YMapsmap').height($('.left_column').height());

            var map = new YMaps.Map(YMaps.jQuery('#YMapsmap')[0]);

            // template
            var template = new YMaps.Template();

            // Создает базовый стиль для значка
            var baseStyle = new YMaps.Style('');
            baseStyle.iconStyle = new YMaps.IconStyle();
            baseStyle.iconStyle.offset = new YMaps.Point(-10, -13);
            baseStyle.iconStyle.size = new YMaps.Point(20, 27);
            baseStyle.iconStyle.href = "/images/map/flatoryPlacemark.png";
            // style baloon
            var sampleBalloonTemplate = new YMaps.LayoutTemplate(app._mapStyles.balloonLayouts.sample);
            baseStyle.balloonStyle = {template: sampleBalloonTemplate};
            // content template
            baseStyle.balloonContentStyle = new YMaps.BalloonContentStyle(template);
            app._mapStyles.baseStyle = baseStyle;

            map.addControl(new YMaps.Zoom());
            map.enableScrollZoom();
            map.setCenter(new YMaps.GeoPoint(37.64, 55.76), 8);

            app.map = map;
            return map;
        },
        /**
         * Get objects by current zone
         * @returns {undefined}
         */
        _getObjects: function() {
            var url, cacheIndex, cache, searchResult;
            url = 'http://' + location.host + '/ajax/by_zone/';
            cacheIndex = location.search ? location.search : 'map__' + app.zone.join('_');

            cache = FlCache.get(cacheIndex, true);
            cache = cache === false ? {} : cache;
            
            // search result
            if (location.search) {
                searchResult = FlRegister.get('searchResult');

                if (searchResult.success) {
                    cache = searchResult.data;
                } else {
                    // objects not found (show popup)
                    $('#modal_empty_search').modal('show');
                    return false;
                }
            }
//            console.log(cache);
//            console.log(searchResult);

            // cache not found - send request
            if (!cache.length) {

                $.getJSON(url, {z: app.zone}, function(data) {
                    if (data.success === true) {
                        cache = data.data;
                        // save cache for 5 min
                        FlCache.set(cacheIndex, cache, 5 * 60);
                        app._renderPlacemarks(cache);
                        return false;
                    }
                });
            } 

            app._renderPlacemarks(cache);
        },
        _renderPlacemarks: function(points) {
            // clear map
            app.map.removeAllOverlays();
            for (var k in points) {
                if (points.hasOwnProperty(k)) {
                    var point = points[k].point.split('&p=');
                    point = point[1].split(',');
                    var placemark = new YMaps.Placemark(new YMaps.GeoPoint(point[0], point[1]), {
                        hasBalloon: 1,
                        style: app._mapStyles.baseStyle
                    }
                    );
//                    placemark.name = points[k].name;
//                    placemark.description = points[k].adres;
//                    placemark.url = '/catalog/' + points[k].alias;
                    placemark.setBalloonContent(app._mapStyles.templates.sample(points[k]));
                    app.map.addOverlay(placemark);
                }
            }
        },
        _mapStyles: {
            baseStyle: {},
            balloonLayouts: {
                sample: function() {
                    this.element = YMaps.jQuery(
                            "<div class=\"flatory-balloon-layout\"><div class=\"flatory-balloon-content\"></div><div class=\"close\"></div><div class=\"flatory-balloon-tail\"></div></div>");

                    this.close = this.element.find(".close");
                    this.content = this.element.find(".flatory-balloon-content");

                    // Отключает кнопку закрытия балуна
                    this.disableClose = function() {
                        this.close.unbind("click").css("display", "none");
                    };

                    // Включает кнопку закрытия балуна
                    this.enableClose = function(callback) {
                        this.close.bind("click", callback).css("display", "");
                        return false;
                    };

                    // Добавляет макет на страницу
                    this.onAddToParent = function(parentNode) {
                        YMaps.jQuery(parentNode).append(this.element);
                    };

                    // Удаляет макет со страницы
                    this.onRemoveFromParent = function() {
                        this.element.find(".flatory-balloon-content").empty();
                        this.element.remove();
                    };

                    // Устанавливает содержимое балуна
                    this.setContent = function(content) {
                        content.onAddToParent(this.content[0]);
                    };

                    // Обновляет балун
                    this.update = function() {
//            this.element.css("margin-top", "-" + (this.content.height() + 245 + 37) + "px");
                    };
                }
            },
            templates: {
                sample: function(data) {

                    data.url = '/catalog/' + data.alias;
                    data.img = !!data.image_1 ? '/images/original/' + data.image_1 : '/images/no_photo.jpg';

                    var name, address, costMin, spaceMin, spaceMax, space, delivery, _dg, _dgy, _dgs, _dgys;

                    name = data.name.length > 37 ? data.name.substring(0, 37) + '...' : data.name;
                    address = data.adres.length > 45 ? data.adres.substring(0, 45) + '...' : data.adres;

                    if (!FlHelper.arr.get(data, 'cost_min', ''))
                        costMin = '<span>−</span>';
                    else
                        costMin = 'от <h2>' + FlHelper.numberFormat(data.cost_min, 0, '.', ' ') + '</h2> руб.';

                    spaceMin = Number(FlHelper.arr.get(data, 'space_min', 0));
                    spaceMax = Number(FlHelper.arr.get(data, 'space_max', 0));
                    if (spaceMin && spaceMax)
                        space = 'от <h2>' + spaceMin + '</h2> до <h2>' + spaceMax + '</h2> м<sup>2</sup>';
                    else
                    if (spaceMin)
                        space = 'от <h2>' + spaceMin + '</h2> м<sup>2</sup>';
                    else
                    if (spaceMax)
                        space = 'до <h2>' + spaceMax + '</h2> м<sup>2</sup>';
                    else
                        space = '<span>−</span>';

                    _dg = Number(FlHelper.arr.get(data, 'delivery_quarter', 0));
                    _dgy = Number(FlHelper.arr.get(data, 'delivery_year', 0));
                    _dgs = Number(FlHelper.arr.get(data, 'delivery_quarter_start', 0));
                    _dgys = Number(FlHelper.arr.get(data, 'delivery_year_start', 0));

                    if ((_dg !== 0 && _dgy !== 0) && (_dgs !== 0 && _dgys !== 0))
                        delivery = 'с ' + _dgs + ' кв ' + _dgys + ' г. по ' + _dg + ' кв ' + _dgy + ' г.';
                    else
                    if (_dg !== 0 && _dgy !== 0)
                        delivery = _dg + '-й квартал ' + _dgy + ' г.';

                    var tpl = '<a href="' + data.url + '" target="_blank" class"fb-left"><img src="' + data.img + '"></a>'
                            + '<div class="object_list_container">'
                            + '<h3><a href="' + data.url + '" target="_blank">' + name + '</a></h3>'
                            + '<h4><span class="fb-icon-xs fb-icon-address"></span>' + address + '</h4>'
                            + '<div class="fb-icon fb-icon-rub">' + costMin + '</div>'
                            + '<div class="fb-icon fb-icon-space">' + space + '</div>'
                            + '<div class="fb-gray"><strong>Срок ввода: </strong><span>' + delivery + '</span></div>'
                            + '</div>';
                    return tpl;
                }
            }

        },
        publ: {
        }
    };
    app.init();
    return app.publ;
}());

//function SampleBalloonLayout() {
//    this.element = YMaps.jQuery(
//            "<div class=\"flatory-balloon-layout\"><div class=\"flatory-balloon-content\"></div><div class=\"close\"></div><div class=\"flatory-balloon-tail\"></div></div>");
//
//    this.close = this.element.find(".close");
//    this.content = this.element.find(".flatory-balloon-content");
//
//    // Отключает кнопку закрытия балуна
//    this.disableClose = function() {
//        this.close.unbind("click").css("display", "none");
//    };
//
//    // Включает кнопку закрытия балуна
//    this.enableClose = function(callback) {
//        this.close.bind("click", callback).css("display", "");
//        return false;
//    };
//
////        this.$closeButton = $('.close', this.getParentElement());
//
//    // Добавляет макет на страницу
//    this.onAddToParent = function(parentNode) {
//        YMaps.jQuery(parentNode).append(this.element);
//    };
//
//    // Удаляет макет со страницы
//    this.onRemoveFromParent = function() {
//        this.element.find(".flatory-balloon-content").empty();
//        this.element.remove();
//    };
//
//    // Устанавливает содержимое балуна
//    this.setContent = function(content) {
//        content.onAddToParent(this.content[0]);
//    };
//
//    // Обновляет балун
//    this.update = function() {
////            this.element.css("margin-top", "-" + (this.content.height() + 245 + 37) + "px");
//    };
//}
//;

//$('#map_search').click(function() {
//    $(".bg_map_search").css('display', 'block');
//    map_msk = new YMaps.Map(YMaps.jQuery('#YMapsmap')[0]),
//            flagLoad = 0;
//
//    // template
//    var template = new YMaps.Template(
//            "<div class=\"fb-left\">\
//                        <a href=\"$[url|url]\"><img src=\"$[metaDataProperty.img|/images/catalog/object_131/27270a0cba73ef4172e13531e5214199Spasskiy_most_kartmal.jpg]\"></a>\
//                </div>\
//                <div class=\"fb-right\">\
//                        <h1><a href=\"$[url|url]\">$[name|объект]</a></h1>\
//                        <h4>$[description|адрес]</h4>\
//                        <div class=\"fb-icon fb-icon-rub\">\
//                                <strong>Цена: </strong> от \
//                                <h2>$[metaDataProperty.price_start]</h2> руб.\
//                        </div>\
//                        <div class=\"fb-icon fb-icon-space\">\
//                                <strong>Площадь: </strong> от \
//                                <h2>$[metaDataProperty.space_start]</h2> до\
//                                <h2>$[metaDataProperty.space_end]</h2> м<i class=\"fb-hight-indent\">2</i>\
//                        </div>\
//                        <div class=\"fb-gray\">\
//                                <strong>Срок ввода: </strong> \
//                                <span>$[metaDataProperty.complite]</span>\
//                        </div>\
//                </div>");
//
//    // style icon
//    // Создает базовый стиль для значка
//    var baseStyle = new YMaps.Style();
//    baseStyle.iconStyle = new YMaps.IconStyle();
//    baseStyle.iconStyle.offset = new YMaps.Point(-10, -13);
//    baseStyle.iconStyle.size = new YMaps.Point(20, 27);
//    baseStyle.iconStyle.href = "/images/map/flatoryPlacemark.png";
//    // style baloon
//    var sampleBalloonTemplate = new YMaps.LayoutTemplate(SampleBalloonLayout);
//    baseStyle.balloonStyle = {template: sampleBalloonTemplate};
//    // content template
//    baseStyle.balloonContentStyle = new YMaps.BalloonContentStyle(template);
//
//
//    map_msk.addControl(new YMaps.Zoom());
//
//    map_msk.enableScrollZoom();
//    map_msk.setCenter(new YMaps.GeoPoint(37.64, 55.76), 8);
//    var post_url;
//    if (location) {
//        post_url = '/search/get_objects/1';
//    } else {
//        post_url = '/search/get_objects/2';
//    }
//    $.post(post_url).success(function(data) {
//        var arr = data != "" ? JSON.parse(data) : {};
//        length = 0;
//        for (var k in arr) {
//            if (arr.hasOwnProperty(k)) {
//                point = arr[k].point.split(',');
//                var placemark = new YMaps.Placemark(new YMaps.GeoPoint(point[0], point[1]), {
//                    hasBalloon: 1,
//                    style: baseStyle
//                }
//                );
//                placemark.name = arr[k].name;
//                placemark.description = arr[k].adres;
//                placemark.url = '/objectcard/' + arr[k].alias;
//                map_msk.addOverlay(placemark);
//            }
//        }
//    });
//    select_once();
//});


/* END */
//function map_search() {
//
//    var post_url;
//    console.log(location)
//    if (location) {
//        post_url = '/search/get_objects/1';
//    } else {
//        post_url = '/search/get_objects/2';
//    }
//    $.post(post_url).success(function(data) {
//        var arr = data != "" ? JSON.parse(data) : {};
//        length = 0;
//        for (var k in arr) {
//            if (arr.hasOwnProperty(k)) {
//                point = arr[k].point.split(',');
//                var placemark = new YMaps.Placemark(new YMaps.GeoPoint(point[0], point[1]), {
//                    hasBalloon: 1,
//                }
//                );
//                placemark.name = arr[k].name;
//                placemark.description = 'Адрес: ' + arr[k].adres + '<br />Ссылка: <a target="_blank" href="/objectcard/' + arr[k].alias + '">Перейти к объекту</a>';
//                map_msk.addOverlay(placemark);
//            }
//        }
//    });
//}
//
//map_search();

