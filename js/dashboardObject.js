var FlObjGeoControls = (function () {
    var app = {
        init: function () {
            app.setUpListeners();
            // selected current
            this.selectCurrent();
        },
        setUpListeners: function () {
            $('[name="district_id"]').off('change').on('change', app._self.changeDistrict);
            $('.metro_station_add').off('click').on('click', app.publ.metroStationAdd);
            $('.metro_station_del').off('click').on('click', app.publ.metroStationDel);
            // autocomplete
            $('.get_metro_station').off('keypress').on('keypress', app.getMetroStation);
        },
        // отмечаем текущие значения для селестов
        selectCurrent: function () {
            // если нет полей формы и нет данных по текущему полю вернем false 
            var fields = FlRegister.get('fields');
            if (!fields)
                return false;
            for (var k in fields) {
                var el = $('[name="' + k + '"]');
                if (el.length !== 0) {

                    switch (el.prop("tagName")) {
                        case 'SELECT':
                            FlDashboardForm.updateSelect(k);
                            this.publ.selectSelected(el, fields[k]);
                            break;
                        case 'INPUT':
                            if ($.inArray(el.prop("type"), ['text', 'hidden', 'textarea']) !== -1)
                                $(el).val(fields[k]);
                            break;
                    }

                }
            }
        },
        // autoload metro
        getMetroStation: function () {
            var autoCompelteElement = this;
            var hiddenElementID = $(autoCompelteElement).siblings('input[type="hidden"]');
            var metroColor = $(autoCompelteElement).siblings('.input-group-addon').find('img');
            $(autoCompelteElement).autocomplete(
                    {
                        source: function (request, response) {
                            // ajax load
                            var url = 'http://' + window.location.host + '/ajax/metro_station',
                                    params = {metro_staition: request.term};

                            $.getJSON(url, params, function (data) {
                                // check success
                                if (data.success === false) {
                                    return false;
                                }

                                response($.map(data.data, function (item) {
                                    return{
                                        label: item.label,
                                        value: item.value,
                                        color: item.color
                                    }
                                }));
                            });
                        },
                        select: function (event, ui) {
                            var selectedObj = ui.item;
                            $(autoCompelteElement).val(selectedObj.label);
                            $(hiddenElementID).val(selectedObj.value);
                            $(metroColor).css({'background': selectedObj.color});
                            return false;
                        }
                    }
            );

        },
        _self: {
            changeDistrict: function () {
                var zone = $(this).data('zone'),
                        value = $(this).val();
                app.publ.getSelectData(zone, value);
            },
            createSelectOptions: function (zone, data) {
                switch (zone) {
                    case 'MOW': // msk
                        var target = '[name="square_id"]';
                        // clear select
                        app.publ.clearSelect(target);
                        // append options
                        for (var k in data) {
                            $(target).append(new Option(data[k]['name'], data[k]['square_id']))
                        }
                        break;
                    case 'MOS': // mo
                        var target = '[name="populated_locality_id"]';
                        // clear select
                        app.publ.clearSelect(target);
                        // append options
                        for (var k in data) {
                            $(target).append(new Option(data[k]['name'], data[k]['populated_locality_id']))
                        }
                        break;
                }
                // refresh select ui
                $(target + ' option[value="0"]').attr('selected', 'selected');
            },
            /**
             * Toggle btn Add Del metro
             * @returns {undefined}
             */
            toggleMetroAddDel: function () {

                var target = '#metro_staition_with_distance .metro_station_control',
                        lastEl = $(target).length - 1;
                $(target).each(function (a, b) {
                    if (a !== lastEl)
                        $(b).removeClass('metro_station_add')
                                .removeClass('btn-success')
                                .addClass('metro_station_del')
                                .addClass('btn-danger')
                                .find('span')
                                .removeClass('glyphicon-plus')
                                .addClass('glyphicon-minus')
                                .siblings('label').text('Удалить');
                });
                // events set
                app.setUpListeners();
            }
        },
        publ: {
            /**
             * Selected current option
             * @param {object/string} target - obj: $(el), string: name of select
             * @param {obj/string/int} value
             * @returns {undefined}
             */
            selectSelected: function (target, value) {
                if (typeof target === 'object') {
                    $(target).find('option[value="' + value + '"]').attr('selected', 'selected');
                    $(document).on('ready', function () {
                        $(target).trigger('change');
                    });
                } else {
                    if (typeof value === 'object') {
                        for (var k in value) {
                            $('[name="' + target + '"] option[value="' + value[k] + '"]').attr('selected', 'selected');
                            $(document).on('ready', function () {
                                $('[name="' + target + '"]').trigger('change');
                            });
                        }
                    } else {
                        $('[name="' + target + '"] option[value="' + value + '"]').attr('selected', 'selected');
                        $(document).on('ready', function () {
                            $('[name="' + target + '"]').trigger('change');
                        });
                    }
                }
            },
            getSelectData: function (zone, value) {

                if (Number(value) !== 0) {
                    // get zone from cache
                    var cacheZone = FlCache.get(zone);

                    if (cacheZone !== false && cacheZone[value] !== undefined) {
                        // cache get data (LS)
                        app._self.createSelectOptions(zone, cacheZone[value]);
//                        console.log(cacheZone[value]);

                    } else {
                        // ajax get data
                        var url = 'http://' + window.location.host + '/ajax/subregion',
                                params = {z: zone, v: value};

                        $.getJSON(url, params, function (data) {
                            // check success
                            if (data.success === false)
                                return false;
                            app._self.createSelectOptions(data.zone, data.data)
                            // если в кеше такая зона существует добавляем в нее данные и сохраняем
                            if (cacheZone !== false) {
                                cacheZone[value] = data.data;
                                FlCache.set(zone, cacheZone);
                            } else {
                                var obj = {};
                                obj[value] = data.data;
                                FlCache.set(zone, obj);
                            }
                        });
                    }
                } else {
                    // @todo clear options
                    app._self.createSelectOptions(zone, {});
                }
            },
            clearSelect: function (target) {
                $(target + ' option[value!="0"]').remove();
            },
            metroStationAdd: function () {
                var tmplRow = $(this).parents('.row').html(),
                        index = Number($(this).data('index')),
                        regSearchIndex = new RegExp('metro_staition\\[' + index + '\\]', 'g'),
                        bootstrapSpace = '<div class="col-sm-2"></div>',
                        target = '#metro_staition_with_distance .metro_station_control',
                        lastEl = $(target).length - 1;

                // change index
                tmplRow = tmplRow.replace(regSearchIndex, 'metro_staition[' + (index + 1) + ']')
                        .replace('data-index="' + index + '"', 'data-index="' + (index + 1) + '"')
                        .replace(/value\=".*?"/g, 'value=""')
                        .replace(/style="background:.*?"/g, '')
                        ;
                // add margin column if index 0 (first add)
                if (index === 0 || lastEl === 0) {
                    tmplRow = bootstrapSpace + tmplRow;
                }
                $(this).parents('.form-group').append($('<div>', {class: 'row', html: tmplRow}));
                app._self.toggleMetroAddDel();
            },
            // @todo $.confurm
            metroStationDel: function () {
                if (!confirm("Вы уверены?"))
                    return false;
                var target = '#metro_staition_with_distance .metro_station_control',
                        lastEl = $(target).length - 1,
                        indexEl = $(target).index(this);
                if (lastEl === 1 || indexEl === 0)
                    $(this).closest('.row').next('.row').find('.col-sm-2:first-child').remove();

                $(this).closest('.row').remove();
            }
        }
    };
    app.init();
    return app.publ;
}());