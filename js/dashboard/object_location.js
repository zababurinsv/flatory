var FlObjectLocation = (function () {
    var app = {
        current: {},
        init: function () {

            if (FlRegister.get('fields'))
                this.current = FlRegister.get('fields');

            this.setUpListeners();
            // init map
            if (typeof ymaps === 'object')
                ymaps.ready(app.map.init);
        },
        setUpListeners: function () {
            // change zone_id
            $('[name="zone_id"]').off('change').on('change', function (e) {
                $('[data-zone-content]').hide();
                var p = $('[data-zone-content="' + $(this).find(':selected').data('zone') + '"]');
                p.show();
                p.find('.js-to-select2').select2();
            });

            // open panorama preview
            $('.js-panorama-open').off('click').on('click', function (e) {
                var it = $($(this).data('target'));

                it.toggle();
                if (it.is(":visible")) {
                    $('html, body').animate({
                        scrollTop: $(this).offset().top
                    }, 200);
                }


            });
        },
        map: {
            init: function () {

                $('#ya-map').width('584px').height('432px');

                var map = new ymaps.Map("ya-map", {
                    center: [55.76, 37.64],
                    zoom: 9,
                    controls: ['zoomControl']
                }), p;

                map.geoObjects.add(p = new ymaps.Placemark([app.current.y || 55.76, app.current.x || 37.64], {}, {
                    preset: 'islands#yellowDotIcon',
                    draggable: true
                }));

                app.map.render(app.current.x || '', app.current.y || '');

                p.events.add('dragend', function (e) {
                    // Получение ссылки на объект, который был передвинут.
                    var thisPlacemark = e.get('target');
                    // Определение координат метки
                    var coords = thisPlacemark.geometry.getCoordinates();

                    app.map.render(coords[1], coords[0]);
                });
            },
            render: function (x, y) {
                $('[data-map-y]').text(y);
                $('[data-map-x]').text(x);
                $('[name="y"]').val(y);
                $('[name="x"]').val(x);
            }
        },
        publ: {

        }
    };
    app.init();
    return app.publ;
}());