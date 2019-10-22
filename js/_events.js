$(document).ready(function() {

    function SampleBalloonLayout() {
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
        
//        this.$closeButton = $('.close', this.getParentElement());

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
    };

    $('#map_search').click(function() {
        $(".bg_map_search").css('display', 'block');
        map_msk = new YMaps.Map(YMaps.jQuery('#YMapsmap')[0]),
                flagLoad = 0;

        // template
        var template = new YMaps.Template(
                "<div class=\"fb-left\">\
                        <a href=\"$[url|url]\"><img src=\"$[metaDataProperty.img|/images/catalog/object_131/27270a0cba73ef4172e13531e5214199Spasskiy_most_kartmal.jpg]\"></a>\
                </div>\
                <div class=\"fb-right\">\
                        <h1><a href=\"$[url|url]\">$[name|объект]</a></h1>\
                        <h4>$[description|адрес]</h4>\
                        <div class=\"fb-icon fb-icon-rub\">\
                                <strong>Цена: </strong> от \
                                <h2>$[metaDataProperty.price_start]</h2> руб.\
                        </div>\
                        <div class=\"fb-icon fb-icon-space\">\
                                <strong>Площадь: </strong> от \
                                <h2>$[metaDataProperty.space_start]</h2> до\
                                <h2>$[metaDataProperty.space_end]</h2> м<i class=\"fb-hight-indent\">2</i>\
                        </div>\
                        <div class=\"fb-gray\">\
                                <strong>Срок ввода: </strong> \
                                <span>$[metaDataProperty.complite]</span>\
                        </div>\
                </div>");

        // style icon
        // Создает базовый стиль для значка
        var baseStyle = new YMaps.Style();
        baseStyle.iconStyle = new YMaps.IconStyle();
        baseStyle.iconStyle.offset = new YMaps.Point(-10, -13);
        baseStyle.iconStyle.size = new YMaps.Point(20, 27);
        baseStyle.iconStyle.href = "/images/map/flatoryPlacemark.png";
        // style baloon
        var sampleBalloonTemplate = new YMaps.LayoutTemplate(SampleBalloonLayout);
        baseStyle.balloonStyle = {template: sampleBalloonTemplate};
        // content template
        baseStyle.balloonContentStyle = new YMaps.BalloonContentStyle(template);
        

        map_msk.addControl(new YMaps.Zoom());

        map_msk.enableScrollZoom();
        map_msk.setCenter(new YMaps.GeoPoint(37.64, 55.76), 8);
        var post_url;
        if (location) {
            post_url = '/search/get_objects/1';
        } else {
            post_url = '/search/get_objects/2';
        }
        $.post(post_url).success(function(data) {
            var arr = data != "" ? JSON.parse(data) : {};
            length = 0;
            for (var k in arr) {
                if (arr.hasOwnProperty(k)) {
                    point = arr[k].point.split(',');
                    var placemark = new YMaps.Placemark(new YMaps.GeoPoint(point[0], point[1]), {
                        hasBalloon: 1,
                        style: baseStyle
                    }
                    );
                    placemark.name = arr[k].name;
                    placemark.description = arr[k].adres;
                    placemark.url = '/objectcard/' + arr[k].alias;
                    map_msk.addOverlay(placemark);
                }
            }
        });
        select_once();
    });


    /* END */
    function map_search() {
        map_msk = new YMaps.Map(YMaps.jQuery('#YMapsmap')[0]),
                flagLoad = 0;

        map_msk.addControl(new YMaps.Zoom());

        map_msk.enableScrollZoom();
        map_msk.setCenter(new YMaps.GeoPoint(37.64, 55.76), 8);
        var post_url;
        console.log(location)
        if (location) {
            post_url = '/search/get_objects/1';
        } else {
            post_url = '/search/get_objects/2';
        }
        $.post(post_url).success(function(data) {
            var arr = data != "" ? JSON.parse(data) : {};
            length = 0;
            for (var k in arr) {
                if (arr.hasOwnProperty(k)) {
                    point = arr[k].point.split(',');
                    var placemark = new YMaps.Placemark(new YMaps.GeoPoint(point[0], point[1]), {
                        hasBalloon: 1,
                    }
                    );
                    placemark.name = arr[k].name;
                    placemark.description = 'Адрес: ' + arr[k].adres + '<br />Ссылка: <a target="_blank" href="/objectcard/' + arr[k].alias + '">Перейти к объекту</a>';
                    map_msk.addOverlay(placemark);
                }
            }
        });
    }
   

   
});
