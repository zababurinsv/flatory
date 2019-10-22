/**
 * Search
 * @returns {app.publ|SearchWidget.app.publ}
 */
var SearchWidget = function() {
    var app = {
        debug: false,
        page: ['MOW'],
        cacheVar: 'flSearch',
        cacheSection: '',
        filterZone: [],
        cache: {},
        bigSearch: false,
        targets: {
            rangeSliders: [
                '.remoteness-slider',
                '.floor-slider',
                '.floor-slider-map',
                '.space-slider',
                '.space-slider-map',
                '.cost-slider',
                '.cost-m-slider',
                '.cost-m-slider-map',
                '.complite-slider',
                '.complite-slider-map'
            ]
        },
        init: function() {
            if (location.pathname === '/catalog/map/')
                app.page = ['MOW', 'MOS'];
            // select menu
            app._self.stylingSelect();

            app.getPage();
            app.showPage();

            // events 
            app.setUpListeners();

            if ($.isEmptyObject(FlHelper.arr.except(FlHelper.Get(), ['', 'sd', 'sf', 'vt', 'name'])))
                app.bigSearch = false;

            if (location.pathname === '/' || location.pathname === '/catalog/') {
                app.bigSearch = true;
            } else {
                if ($.map(FlHelper.Get(), function(n, i) {
                    return i;
                }).length > 10 && FlCache.get('smallSearch'))
                    app.bigSearch = false;
            }
            if (FlCache.get('smallSearch'))
                app.bigSearch = false;


            // small search
            app.publ.toggleBigSearch(app.bigSearch);

        },
        setUpListeners: function() {
            // set search filters on default values
            $('.clear_filter').on('click', app.publ.clearFilters);
            // change search filter group (by zone code)
            $('.mark').off('click').on('click', app.changePage);
            // change radio transport to metro
            $('#transport_page input[type="radio"]').off('change').on('change', app.mowTrnsportChange);
            // send search form
            $('#page_search_start').on('click', app.publ.search);
            // autocomplete
            $('.get_metro_station').off('keypress').on('keypress', app.getMetroStation);
            $('.search_input').off('keypress').on('keypress', app.getObjectNames);
            // ПОКАЗАТЬ СКРЫТЬ РАСШИРЕННЫЙ ПОИСК
            $('.extends_search').on('click', app.changeSizeSearch);
        },
        /**
         * check big or small search now
         * @returns {Boolean}
         */
        isBigSearch: function() {
            return $('.search').height() === 368;
        },
        /**
         * Change size of search 
         * @param {object} e  - event
         * @returns {undefined}
         */
        changeSizeSearch: function(e) {

            if ($.map(FlHelper.Get(), function(n, i) {
                return i;
            }).length > 10 || app.isBigSearch())
                FlCache.set('smallSearch', true);
            else
                FlCache.set('smallSearch', false);

            app.publ.toggleBigSearch(!app.isBigSearch());
        },
        // event change MOW transport to metro
        mowTrnsportChange: function() {
            var current = $(this).val();
            app.publ.transportChange(current);
        },
        /**
         * Range slider (RS) methods
         */
        rangeSlider: {
            /**
             * Replace positions for all RS
             * @returns {undefined}
             */
            replaceAll: function() {
                for (var k in app.targets.rangeSliders) {
                    if (!$(app.targets.rangeSliders[k]).hasClass('disabled'))
                        this.setPosition(app.targets.rangeSliders[k]);
                }
            },
            /**
             * Установить ползунки на текущие значения из инпутов
             * @param {string} selector
             * @returns {undefined}
             */
            setPosition: function(selector) {

                var rangeSlider = $(selector).find('.range-slider');
                var newMin = Number($(rangeSlider).find('.range-min input').val());
                var newMax = Number($(rangeSlider).find('.range-max input').val());

                if (app.debug) {
                    console.log('RS:: ');
                    console.log($(rangeSlider));
                    console.log('RS:: ' + selector + ' :: ' + 'min: ' + newMin + ' max: ' + newMax);
                }

                /**
                 * Фикс бага: Ползунки застревают на максимуме
                 * Такая конструкция нужна для случаев когда минимум = максимуму и не на 0,
                 * чтобы активным оставался ползунок минимума
                 * Если будут использоваться минимумы не равные 0 , 
                 * необходимо будет скорректировать текущий метод и передавать в него минимум 
                 * иначе может появится баг застревания ползунков на минимуме
                 */
                if (newMin === newMax && newMin !== 0) {
                    $(rangeSlider).find('.range-slider-bar').slider("values", 1, newMax);
                    $(rangeSlider).find('.range-slider-bar').slider("values", 0, newMin);
                } else {
                    $(rangeSlider).find('.range-slider-bar').slider({values: [newMin, newMax]});
                }
                // Фильтры Срок ввода  - проставляем подписи для ползунков
                if ($.inArray(selector, ['.complite-slider', '.complite-slider-map']) !== -1) {
                    this.addLableCompliteSlider(selector, newMin, newMax);
                }

            },
            /**
             * Toggle disable
             * @param {string} selector
             * @param {bool} status
             * @returns {undefined}
             */
            disableToggle: function(selector, status) {
                var status = status === undefined ? true : false;
                if (status === true) {
                    $(selector).addClass('disabled');
                    $(selector).find('input').attr('disabled', 'disabled');
                    $(selector).find('input').val('');
                } else {
                    $(selector).removeClass('disabled');
                    $(selector).find('input').removeAttr('disabled');
                }
            },
            /**
             * Фильтр: "Срок ввода" : устанавливаем значения
             * @param {type} event
             * @param {type} ui
             * @param {type} rangeSlider
             * @returns {undefined}
             */
            setCompliteSliderValues: function(event, ui, rangeSlider) {
                var min = ui.values[ 0 ].toString().replace(/\B(?=(\d{3})+(?!\d))/g, " "),
                        max = ui.values[ 1 ].toString().replace(/\B(?=(\d{3})+(?!\d))/g, " ");

                app.rangeSlider.addLableCompliteSlider(rangeSlider, min, max);

            },
            /**
             * Фильтр: "Срок ввода": подписи к ползункам
             * @param {string} rangeSlider
             * @param {int} min
             * @param {int} max
             * @returns {undefined}
             */
            addLableCompliteSlider: function(rangeSlider, min, max) {
                var date = new Date();
                var generateLable = function(val, dt) {
                    var y = String(dt.getFullYear()).substr(-2),
                            kv = val;
                    if (val > 4) {
                        kv = val % 4;
                        kv = kv === 0 ? 4 : kv;
                        y = Number(y) + 1;
                    }
                    return kv + 'кв' + y;
                };
                var minText = generateLable(min, date);
                var maxText = generateLable(max, date);

                if (min == 0 || min == 9) {
                    $(rangeSlider).find('.pipe_grid_values .min_label').hide();
                } else {
                    $(rangeSlider).find('.pipe_grid_values .min_label').show();
                    $(rangeSlider).find('.pipe_grid_values .min_label').text(minText);
                    $(rangeSlider).find('.pipe_grid_values .min_label').css({"left": (min * 10) + '%'});
                }

                if (max == 9 || max == 0) {
                    $(rangeSlider).find('.pipe_grid_values .max_label').hide();
                } else {
                    $(rangeSlider).find('.pipe_grid_values .max_label').show();
                    $(rangeSlider).find('.pipe_grid_values .max_label').text(maxText);
                    $(rangeSlider).find('.pipe_grid_values .max_label').css({"left": (max * 10) + '%'});
                }
                if (max - min < 2) {
                    $(rangeSlider).find('.pipe_grid_values .max_label').css({"top": '-10px'});
                } else {
                    $(rangeSlider).find('.pipe_grid_values .max_label').css({"top": '0'});
                }
            },
            /**
             * get default filters values
             * @use FlRegister, FlHelper
             * @returns {SearchWidget.app.rangeSlider.getDefaultFilters.filters}
             */
            getDefaultFilters: function() {
                var maxFilters = FlRegister.get('maxFilters');
                maxFilters = maxFilters === false ? {} : maxFilters;

                // max by region (page)
                if (app.page.length === 1) {
                    maxFilters = maxFilters[app.page[0]];
                } else {
                    // определяем максимальные значения по текущим гео зонам
                    var tmp = {};
                    var fl = ['floor_max', 'space_max', 'cost_m_max', 'cost_max'];
                    for (var k in app.page) {
                        for (var fk in fl) {
                            if (tmp[fl[fk]] === undefined)
                                tmp[fl[fk]] = [];
                            tmp[fl[fk]].push(Number(maxFilters[app.page[k]][fl[fk]]))
                        }
                    }
                    var maxFilters = {};
                    for (var k in tmp) {
                        maxFilters[k] = Math.max.apply(null, tmp[k]);
                    }
                }


                var filters = {
                    remoteness: {min: 0, max: 2, step: 0.1},
                    remotenessByTransport: {min: 0, max: 50},
//                    remoteness_mkad: {min: 0, max: 100},
                    floor: {min: 0, max: FlHelper.arr.get(maxFilters, 'floor_max', 20)},
                    space: {min: 0, max: FlHelper.arr.get(maxFilters, 'space_max', 200)},
                    cost_m: {min: 0, max: FlHelper.arr.get(maxFilters, 'cost_m_max', 200)},
                    cost: {min: 0, max: FlHelper.arr.get(maxFilters, 'cost_max', 100)},
                    complite: {min: 0, max: 9}
                };
                return filters;
            }
        },
        changePage: function() {
            // активные кнопки до начала работы 
            var countActiveZone = $('.mark-active').length;
            // активна ли текущая кнопка
            var isActive = $(this).hasClass('mark-active');
            
            console.log(isActive);

            // если текущая кнопка активна и активных кнопок больше 1 
            // то деактивируем текущую
            if (isActive && countActiveZone > 1)
                app.toggleZoneBtn(this, false);

            // если активна только текущая кнопка, то ничего делать не будем
            if (isActive && countActiveZone === 1) {
                // set big search
                app.bigSearch = true;
                // Advanced Search
                app.publ.toggleBigSearch(app.bigSearch);
                return false;
            }


            // если текущая кнопка не активна и есть другая активная кнопка
            // то активируем текущую кнопку (будет на одну активную кнопку больше)
            if (isActive === false)
                app.toggleZoneBtn(this, true);

            app.defineActiveZone();
            app.showPage();
            // Advanced Search
            app.publ.toggleBigSearch(app.bigSearch);
        },
        /**
         * Определяем активные зоны
         * @returns {undefined}
         */
        defineActiveZone: function() {
            var target = '.mark-active';
            var activeZone = [];
            $(target).each(function(a, b) {
                activeZone.push($(b).data('geo'));
            });
            app.page = activeZone;
//            console.log(app.page);
//            console.log(app.page.join(''));
        },
        /**
         * Активаровать / деактевировать кнопку
         * @param {$} el - кнопка
         * @param {bool} status - активировать или нет
         * @returns {undefined}
         */
        toggleZoneBtn: function(el, status) {
            var status = status === undefined ? true : status;
            var activeClass = 'mark-active';
            // set big search
            app.bigSearch = true;

            if (status) {
                $(el).addClass(activeClass);
                // checked current input
                $(el).siblings('[type="checkbox"]').attr('checked', 'checked');
            } else {
                $(el).removeClass(activeClass);
                $(el).siblings('[type="checkbox"]').removeAttr('checked');
            }

            app.toggleZoneClose();
        },
        /**
         * Показать / скрыть крестики на кнопках
         * @returns {undefined}
         */
        toggleZoneClose: function() {
            var countActiveZone = $('.mark-active').length;
            if (countActiveZone > 1)
                $('.mark i').show();
            else
                $('.mark i').hide();
        },
        /**
         * Определить страницу виджета (Москва / МО или вместе)
         * @returns {app.page}
         */
        getPage: function() {
            // смотрим по GET и определяем группу поиска + устанавливаем чекбокс
            app.page = FlHelper.arr.get(FlHelper.Get(), 'code[]', app.page);
            for (var k in app.page) {
                $('[value="' + app.page[k] + '"]').attr('checked', 'checked');
                var el = $('[value="' + app.page[k] + '"]').siblings('a');
                app.toggleZoneBtn(el, true);
            }
            return app.page;
        },
        showPage: function() {
            // init filters
            if (app.page !== app.filterZone) {
                app.filterZone = app.page;
                app.publ.clearFilters();
            }
            switch (app.page.join('')) {
                case 'MOS':
                    // hide & disable msk
                    $('.view_msk').addClass('hidden');
                    $('.view_general').addClass('hidden');
                    // show & activate mo
                    $('.view_mo').removeClass('hidden');
                    $('.filter_content').removeClass('hidden');
                    break;
                case 'MOW':
                    // hide & disable mo
                    $('.view_mo').addClass('hidden');
                    $('.view_general').addClass('hidden');
                    // show & activate msk
                    $('.view_msk').removeClass('hidden');
                    $('.filter_content').removeClass('hidden');
                    break;
                default : // general view
                    $('.view_msk').addClass('hidden');
                    $('.view_mo').addClass('hidden');
                    $('.view_general').removeClass('hidden');
                    $('.filter_content').removeClass('hidden');
            }
        },
        // autoload metro
        getMetroStation: function() {
            var autoCompelteElement = this;
            var hiddenElementID = $(autoCompelteElement).siblings('input[type="hidden"]');
//            var metroColor = $(autoCompelteElement).siblings('.input-group-addon').find('img');
            $(autoCompelteElement).autocomplete({
                source: function(request, response) {
                    // ajax load
                    var url = 'http://' + window.location.host + '/ajax/metro_station',
                            params = {metro_staition: request.term};
                    $.getJSON(url, params, function(data) {
                        // check success
                        if (data.success === false) {
                            hiddenElementID.val(request.term);
                            return false;
                        }

                        response($.map(data.data, function(item) {
                            return{
                                label: item.label,
                                value: item.value,
                                color: item.color
                            };
                        }));
                    });
                },
                // hide value on focus
                focus: function(event, ui) {
                    return false;
                },
                select: function(event, ui) {
                    var selectedObj = ui.item;
                    $(autoCompelteElement).val(selectedObj.label);
                    $(hiddenElementID).val(selectedObj.value);
//                            $(metroColor).css({'background':selectedObj.color});
                    return false;
                }
            });
        },
        // autocomplete object names
        getObjectNames: function() {
            var autoCompelteElement = this;
            var hiddenElementID = $(autoCompelteElement).siblings('input[type="hidden"]');
//            var metroColor = $(autoCompelteElement).siblings('.input-group-addon').find('img');
            $(autoCompelteElement).autocomplete({
                source: function(request, response) {
                    // ajax load
                    var url = 'http://' + window.location.host + '/ajax/object_names',
                            params = {name: request.term};
                    $.getJSON(url, params, function(data) {
                        // check success
                        if (data.success === false) {
                            return false;
                        }

                        response($.map(data.data, function(item) {
                            return{
                                label: item.label,
                                value: item.value
                            };
                        }));
                    });
                },
                // hide value on focus
                focus: function(event, ui) {
                    return false;
                },
                select: function(event, ui) {
                    var selectedObj = ui.item;
                    $(autoCompelteElement).val(selectedObj.label);
                    $(hiddenElementID).val(selectedObj.value);
                    return false;
                }
            });
        },
        _self: {
            /**
             * Стилизуем селекты
             * @returns {undefined}
             */
            stylingSelect: function() {
                $('.select-styling').each(function(a, b) {
                    var dataAttr = $(b).data();
                    $(b).selectmenu({
                        change: app._self.stylingSelectChange
                    });
                });
            },
            /**
             * Обработка события: изменение связанных селектов
             * @todo Разбить на несколько методов, усилить абстракцию + сохранять кеш в LS
             * @param {obj} e - event
             * @param {obj} el - ui obj
             * @returns {undefined}
             */
            stylingSelectChange: function(e, el) {
                var zone = $(el.item.element[0].parentNode).data('zone'),
                        value = el.item.index;
                if (zone !== undefined)
                    app.publ.getSelectData(zone, value);
            },
            createSelectOptions: function(zone, data) {
                switch (zone) {
                    case 'MOW': // msk
                        var target = '[name="square"]';
                        // clear select
                        app.publ.clearSelect(target);
                        // append options
                        for (var k in data) {
                            $(target).append(new Option(data[k]['name'], data[k]['square_id']));
                        }
                        break;
                    case 'MOS': // mo
                        var target = '[name="geo_area"]';
                        // clear select
                        app.publ.clearSelect(target);
                        // append options
                        for (var k in data) {
                            $(target).append(new Option(data[k]['name'], data[k]['geo_area_id']));
                        }
                        break;
                }
                // refresh select ui
                $(target + ' option[value="0"]').attr('selected', 'selected');
                $(target).selectmenu("refresh");
            }
        },
        publ: {
            search: function() {
                
                if (location.pathname === '/catalog/map/') {
                    // поиск по карте
                    $('#search_fiters').submit();
                } else {
                    // поиск по каталогу
                    if (app.bigSearch)
                        $('#search_fiters').submit();
                    else
                        location.href = 'http://' + location.host + '/catalog/search?name=' + FlHelper.stripTags($('[name="name"]').val());
                }
            },
            /**
             * Получаем данные для селектов (Ajax)
             * @param {type} zone
             * @param {type} value
             * @returns {undefined}
             */
            getSelectData: function(zone, value) {

                if (Number(value) !== 0) {
                    // get zone from cache
                    var cacheZone = FlCache.get(zone);
//                    console.log(cacheZone);

                    if (cacheZone !== false && cacheZone[value] !== undefined) {
                        // cache get data (LS)
                        app._self.createSelectOptions(zone, cacheZone[value]);
//                        console.log(cacheZone[value]);

                    } else {
                        // ajax get data
                        var url = 'http://' + window.location.host + '/ajax/subregion',
                                params = {z: zone, v: value};
                        $.getJSON(url, params, function(data) {
                            // check success
                            if (data.success === false) {
                                app._self.createSelectOptions(zone, []);
                                return false;
                            }

                            app._self.createSelectOptions(data.zone, data.data);
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
            clearSelect: function(target) {
                $(target + ' option[value!="0"]').remove();
            },
            /**
             * Фильтр: "Срок ввода"
             * Устанавливаем год начала и конца фильтра
             * @returns {undefined}
             */
            setCompliteDateLimits: function() {
                var date = new Date();
                var target = '.complite-slider';
                $(target).find('.current_year').text(date.getFullYear());
                $(target).find('.next_year').text(date.getFullYear() + 1);
                $(target).find('.pipe_grid_values div').each(function(a, b) {
                    $(b).text('');
                });
            },
            /**
             * Сброс всех фильров на значения по-умолчанию
             * @returns {undefined}
             */
            clearFilters: function(e) {
                // если событие и поиск по карте - редирект
                if(e !== undefined && location.pathname.split('/').join('') === 'catalogmap'){
                    location.href = 'http://' + location.host + '/catalog/map/';
                }
                
                // MOW transport_type
                $('[name="transport_type"][value="0"]').attr('checked', 'checked');

                // ползунки
                var filters = app.rangeSlider.getDefaultFilters();
                rangeSliderInit('.remoteness-slider', filters.remoteness.min, filters.remoteness.max, filters.remoteness.step);
//                rangeSliderInit('.remoteness-mkad-slider', filters.remoteness_mkad.min, filters.remoteness_mkad.max);
                rangeSliderInit('.floor-slider', filters.floor.min, filters.floor.max);
                rangeSliderInit('.floor-slider-map', filters.floor.min, filters.floor.max);
                rangeSliderInit('.space-slider', filters.space.min, filters.space.max);
                rangeSliderInit('.space-slider-map', filters.space.min, filters.space.max);
                rangeSliderInit('.cost-slider', filters.cost.min, filters.cost.max);
                rangeSliderInit('.cost-m-slider', filters.cost_m.min, filters.cost_m.max);
                rangeSliderInit('.cost-m-slider-map', filters.cost_m.min, filters.cost_m.max);
                app.publ.setCompliteDateLimits();
                rangeSliderInit('.complite-slider', filters.complite.min, filters.complite.max, 1, app.rangeSlider.setCompliteSliderValues);
                rangeSliderInit('.complite-slider-map', filters.complite.min, filters.complite.max, 1, app.rangeSlider.setCompliteSliderValues);

                // выставляем положение ползунка удаленность от метро
                app.publ.transportChange(0);

                // text inputs
                $('[name="name"]').val('');
                $('[name="metro"]').val('');
                // selects
                // MOW drop select & clear relations select
                $('[name="district"]').find('option[value="0"]').attr('selected', 'selected');
                $('[name="district"]').selectmenu("refresh");
                app.publ.clearSelect('[name="square"]');
                $('[name="square"]').find('option[value="0"]').attr('selected', 'selected');
                $('[name="square"]').selectmenu("refresh");
                // furnish
                $('[name="furnish"]').find('option[value="0"]').attr('selected', 'selected');
                $('[name="furnish"]').selectmenu("refresh");
                // MOS drop select & clear relations select
                $('[name="geo_direction"]').find('option[value="0"]').attr('selected', 'selected');
                $('[name="geo_direction"]').selectmenu("refresh");
                app.publ.clearSelect('[name="geo_area"]');
                $('[name="geo_area"]').find('option[value="0"]').attr('selected', 'selected');
                $('[name="geo_area"]').selectmenu("refresh");
                // drop checkbox rooms
                $('[name="rooms[]"]').removeAttr('checked');
                // MOS distance_to_mkad
                $('[name="distance_to_mkad"][value="0"]').attr('checked', 'checked');
                $('[name="metro_staition_id"]').val('');
                
                if(location.pathname === '/catalog/map'){
//                    $('.mark').not('.mark-active').trigger('click');
//                    app.defineActiveZone();
//                    app.showPage();
                }
            },
            // change MOW transport to metro
            transportChange: function(currentVal) {
                var filters = app.rangeSlider.getDefaultFilters();
                var target = '#transport_page input[type="radio"]:checked';
                var currentVal = currentVal === undefined ? $(target).val() : currentVal;
                var selector = '.remoteness-slider';

                switch (Number(currentVal)) {
                    case 1: // to foot
                        app.rangeSlider.disableToggle(selector, false);
                        rangeSliderInit(selector, filters.remoteness.min, filters.remoteness.max, filters.remoteness.step);
                        // активируем ползунок
                        rangeSliderEnable(selector);
                        break;
                    case 2: // by transport
                        app.rangeSlider.disableToggle(selector, false);
                        rangeSliderInit(selector, filters.remotenessByTransport.min, filters.remotenessByTransport.max);
                        // активируем ползунок
                        rangeSliderEnable(selector);
                        break;
                    case 0:   // no matter
                        rangeSliderInit(selector, filters.remotenessByTransport.min, filters.remotenessByTransport.max);
                        app.rangeSlider.disableToggle(selector);
                        // деактевируем ползунок
                        rangeSliderDisable(selector);
                        break;
                }
            },
            RS: {
                replaceAll: function() {
                    app.rangeSlider.replaceAll();
                },
                set: function(selector) {
                    app.rangeSlider.setPosition(selector);
                }
            },
            toggleBigSearch: function(bigSearch) {
                var bigSearch = bigSearch === undefined ? false : bigSearch;
                if (bigSearch === app.isBigSearch())
                    return false;

                if (bigSearch === false) {
                    // small search
                    app.bigSearch = false;
                    $('.pipe_layer_3').css('display', 'block');
                    // Поиск по названию
                    $('.search').animate({height: "79px"}, 200);

                    $('.first_column .layer_1').css('display', 'none');
                    $('.first_column .layer_2').css('display', 'none');
                    $('.first_column .layer_3').css('display', 'none');

                    $('.second_column .layer_1').css('display', 'none');
                    $('.second_column .layer_3').css('display', 'none');
                    $('.third_column .def_on_off').css('display', 'none');
                    $('.third_column .layer_1').css('display', 'none');
                    $('.third_column .button_bg').css('bottom', '16px');
                    $('.extends_search').children().attr('src', '/images/new/search_down.png');
                    $('.button_section').addClass('button_section_small');
                    $('.view_mo').css('display', 'none');
                    $('.view_msk').css('display', 'none');
                } else {
                    app.bigSearch = true;
                    $('.pipe_layer_3').css('display', 'block');
                    // Расширенный поиск
                    $('.search').animate({height: "368px"}, 200);
                    if ($('.select_face_1_1').css('display') === 'block') {
                        $('.first_column .layer_2').css('display', 'block');
                    } else {
                        $('.first_column .layer_1').css('display', 'block');
                        $('.first_column .layer_2').css('display', 'block');
                        $('.first_column .layer_3').css('display', 'block');
                        $('.third_column .def_on_off').css('display', 'block');
                    }
                    $('.second_column .layer_1').css('display', 'block');
                    $('.second_column .layer_3').css('display', 'block');
                    $('.third_column .layer_1').css('display', 'block');
                    $('.third_column .button_bg').css('bottom', '16px');
                    $('.extends_search').children().attr('src', '/images/new/search_up.png');
                    $('.button_section').removeClass('button_section_small');
                    $('.view_mo').css('display', 'block');
                    $('.view_msk').css('display', 'block');
                }
            }
        }

    };
    app.init();
    return app.publ;
};
var FlSearch = new SearchWidget();