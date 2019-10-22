/* global FlCache, FlSearch */

;
var FlGeoIndex = (function () {

    var module = 'FlGeoIndex';

    var app = {
        it: $,
        tab: 'msk|regions',
        separator: '__',
        searchItems: {},
        listLabels: [],
        init: function () {
            this.it = $('.geo-index');
            this.getlistLabels();
            this.setUpListeners();

            $.subscribe('FlGeoIndex_loadTabData', function (e, response) {
                app.updateTab(response.tab, response.data, response.data_map, response.sub_nav);
                app.getlistLabels();
                app.setUpListeners();
                // вызываем функцию постобработки - (для отображения текущего состояния формы)
                if (typeof response.responseCall === 'function')
                    response.responseCall();
            });
            // @todo
//            model.loadTabData();
        },
        setUpListeners: function () {

            this.it.find('[data-tab]').off('click').on('click', app.navTabClick);
            this.it.find('.place ul span, .js-add-search-item').off('click').on('click', app.addSearchItem);
            // search-container
            this.it.find('.search-container .search-item-rm').off('click').on('click', app.rmSearchItem);
            this.it.find('.js-tab-sub-nav a').off('click').on('click', app.clickSubNav);
            this.it.off('submit').on('submit', app.submit);
            this.it.find('.js-geo-index-autocomplete').off('keypress').on('keypress', app.autocomplete);

        },
        clickSubNav: function (e) {
            var n = $(this).parents('.js-tab-sub-nav'), f = $(this).data('field'), v = $(this).data('value');

            if ($(this).hasClass('active')) {
                // rm items
                app.it.find('.search-container .search-item[data-' + f + '="' + v + '"] .search-item-rm').trigger('click');
                $(this).removeClass('active');
            } else {
                // add items
                n.siblings('.js-tab-data-list').find('[data-' + f + '="' + v + '"]').trigger('click');
                $(this).addClass('active');
            }
        },
        navTabClick: function (e) {
            var group, tab, current, subTab, placeholder = $(this).data('placeholder') || '';
            tab = $(this).data('tab');
            group = app.it.find('[data-tab_content][data-tab_group="' + $(this).data('tab_group') + '"]');

            if (!tab || !group.length)
                return;

            current = group.filter('[data-tab_content="' + tab + '"]');

            // change content 
            group.hide();
            current.show();

            subTab = $(this).data('sub_tab');

            // set current tab
            app.tab = !!subTab ? subTab : tab;

            if (!current.data('loaded'))
                model.loadTabData(app.tab);


            // clear search items
            app.clearSearchItems();
            app.getlistLabels();

            // set active 
            $(this).parents('.head-left').find('.js-tabs').removeClass('active');
            $(this).parents('li').addClass('active');
            // set placeholder
            $('.js-geo-index-autocomplete').attr('placeholder', placeholder);
            $('.js-geo-index-autocomplete').val('');
        },
        addSearchItem: function (e) {
            var d = $(this).data(), alias = d.field + app.separator + d.value, filters = {}, dfFields = ['label', 'field', 'value'];

            // object was added - remove it
            if (typeof app.searchItems[alias] === 'object') {
                delete(app.searchItems[alias]);
                app.it.find('.search-container [data-alias="' + alias + '"]').remove();
                $(this).removeClass('active');
                return;
            }
            d.label = d.label || $(this).text();

            for (var k in d)
                if ($.inArray(k, dfFields) === -1)
                    filters[k] = d[k];

            d.filters = filters;
            d.filters.alias = alias;

            app.searchItems[alias] = d;
            app.it.find('.search-container').prepend(tpl.searchItem(d));
            $(this).addClass('active');
            app.setUpListeners();
        },
        rmSearchItem: function (e) {
            var i = $(this).siblings('input'), f = i.attr('name'), v = i.val(), alias = f + app.separator + v;
            // nothing remove
            if (typeof app.searchItems[alias] !== 'object')
                return;

            delete(app.searchItems[alias]);

            $(this).parents('.search-item').remove();
            app.it.find('.place [data-field="' + f + '"][data-value="' + v + '"], .js-add-search-item').removeClass('active');
        },
        clearSearchItems: function () {
            this.it.find('.search-container .search-item').remove();
            this.it.find('.place [data-letter] span').removeClass('active');
            this.it.find('.js-tab-sub-nav .active').removeClass('active');
            this.it.find('.js-add-search-item').removeClass('active');
            this.searchItems = {};
        },
        /**
         * обновление контента в tab
         * @param {string} tab
         * @param {object} data
         * @param {object} dataMap
         * @param {array} sub_nav
         * @return {undefined}
         */
        updateTab: function (tab, data, dataMap, sub_nav) {

            var t = $('[data-tab_content="' + tab + '"]'), view = '';

            if (!t || t.data('loaded'))
                return;

            if (typeof tpl[t.data('tpl')] !== 'function') {
                if ($.isArray(sub_nav) && sub_nav.length) {
                    // render subnav
                    view += tpl.tabSubNav({data: sub_nav, data_map: dataMap});
                }

                view += tpl.tabAlphabet({data: data, data_map: dataMap});

                t.html(view);
            } else {
                t.append(tpl[t.data('tpl')](data));
            }


            t.data('loaded', 1);
        },
        submit: function (e) {
            e.preventDefault();
            $(this).parents('.modal').modal('hide');
            FlSearch.setGeoIndex(app.searchItems, app.tab);
            return false;
        },
        autocomplete: function (e) {
            var self = this;

            $(this).autocomplete({
                source: app.listLabels,
                // hide value on focus
                focus: function (event, ui) {
                    return false;
                },
                select: function (event, ui) {
                    var it = $(app.getCurrentTab()).find('span:contains("' + ui.item.label + '")');

                    it = it.length ? it : $(app.getCurrentTab()).find('.metro-station[data-label="' + ui.item.label + '"]');
                    if (!it.hasClass('active'))
                        it.trigger('click');
                    $(self).val('');
                    return false;
                }
            });
        },
        /**
         * get current tab
         * @returns {$}
         */
        getCurrentTab: function () {
            return this.it.find('[data-tab_content="' + this.tab + '"]');
        },
        /**
         * get list labels
         * @returns {array)
         */
        getlistLabels: function () {
            if ($(this.getCurrentTab()).find('[data-letter] span').length) {
                return this.listLabels = $(this.getCurrentTab()).find('[data-letter] span').map(function () {
                    return $.trim($(this).text());
                }).get();
            } else {
                if ($(this.getCurrentTab()).find('.metro-station').length) {
                    return this.listLabels = $(this.getCurrentTab()).find('.metro-station').map(function () {
                        return $.trim($(this).data('label'));
                    }).get();
                }
            }


        },
        publ: {
            /**
             * присвоить форме список элементов
             * @param {object} items - список гео элементов
             * @return {undefined}
             */
            setSearchItems: function (items) {
                app.clearSearchItems();
                app.searchItems = typeof items === 'object' ? items : {};
            },
            getSearchItems: function () {
                return app.searchItems;
            },
            getType: function () {
                return app.tab;
            },
            /**
             * присвоить тип группы (data-tab)
             * @param {string} type - тип контента (имя группы)
             * @return {undefined}
             */
            setType: function (type) {
                app.tab = type;
            },
            /**
             * заполняет форму текущими значениями
             * @return {undefined}
             */
            fill: function () {
                                
                if(!app.tab)
                    return;
                
                var tabBtn = !$('[data-tab_group="main"][data-tab="' + app.tab + '"]').length ? $('[data-tab_group="main"][data-tab="' + (app.tab.split('|')[0]) + '"]') : $('[data-tab_group="main"][data-tab="' + app.tab + '"]');
                var subTabBtn = $('[data-tab_content="' + tabBtn.data('tab') + '"]').find('[data-tab="' + app.tab + '"]');

                model.loadTabData(app.tab, function () {

                    var listPlace, list = Object.create(app.searchItems);
                    
                    app.searchItems = {};

                    if (!tabBtn.length)
                        return;

                    tabBtn.parents('.nav-pills').find('.active').removeClass('active');
                    tabBtn.parent('li').addClass('active');
                    $('[data-tab_group="main"][data-tab_content]').hide();
                    $('[data-tab_group="main"][data-tab_content="' + tabBtn.data('tab') + '"]').show();

                    listPlace = $('[data-tab_group="main"][data-tab_content="' + tabBtn.data('tab') + '"]');

                    if (subTabBtn.length) {
                        subTabBtn.parents('.nav-pills').find('.active').removeClass('active');
                        subTabBtn.parent('li').addClass('active');
                        $('[data-tab_group="' + tabBtn.data('tab') + '"][data-tab_content]').hide();
                        $('[data-tab_group="' + tabBtn.data('tab') + '"][data-tab_content="' + subTabBtn.data('tab') + '"]').show();

                        listPlace = $('[data-tab_group="' + tabBtn.data('tab') + '"][data-tab_content="' + subTabBtn.data('tab') + '"]');
                    }
                    // list el click - select current
                    for (var k in list) {

                        app.addSearchItem.call($(listPlace.find('[data-field="' + list[k].field + '"][data-value="' + list[k].value + '"]')));
                    }

                });
            },
            getCurrentTab: function () {
                return app.getCurrentTab();
            }
        }
    };

    var tpl = {
        /**
         * get tpl vars
         * @param {object} d - data
         * @param {object|string} v - name of var
         * @returns {object|string}
         */
        _getVars: function (d, v) {
            var isList = $.isArray(v) ? true : false;
            var r = isList ? {} : '';

            if (typeof d !== 'object')
                return isList ? {} : '';

            if (isList) {
                for (var k in v) {
                    r[v[k]] = typeof d[v[k]] !== 'undefined' ? d[v[k]] : '';
                }
            } else {
                r = typeof d[v] !== 'undefined' ? d[v] : '';
            }
            return r;
        },
        /**
         * tpl search item
         * @param {object} d
         * @returns {String}
         */
        searchItem: function (d) {
            var v = this._getVars(d, ['field', 'value', 'label', 'filters']), f = ' ';
            // define filters
            if (typeof v.filters === 'object')
                for (var k in v.filters)
                    f += 'data-' + k + '="' + v.filters[k] + '" ';
            return '<span class="search-item"' + f + '><input type="hidden" name="' + v.field + '" value="' + v.value + '"><span>' + v.label + '</span><i class="search-item-rm"></i></span>';
        },
        tabAlphabet: function (d) {
            var v = this._getVars(d, ['data_map', 'data']),
                    lists = '',
                    colCount = 4,
                    colLimit = 0,
                    letterString,
                    letterObject,
                    dataKeys,
                    items,
                    iterator = 0;

            if (typeof v.data_map !== 'object' || typeof v.data !== 'object')
                return '';

            colLimit = Math.ceil(v.data_map.count / colCount);

            dataKeys = Object.keys(v.data);

            for (var col = 0; col < colCount; col++) {
                lists += '<div class="col-25" data-col="' + col + '">';
                for (var l = 0; l < colLimit; l++) {
                    letterString = dataKeys[iterator++];

                    if (typeof letterString !== 'string')
                        continue;
                    letterObject = v.data[letterString];
                    items = letterObject.items;
                    if (typeof items !== 'object')
                        continue;
                    lists += '<ul data-letter="' + letterString + '"><li class="letter">' + letterString + '</li>';
                    for (var k in items) {
                        lists += '<li><span data-field="' + (items[k].field || '') + '"' + (v.data_map.parent_id && items[k][v.data_map.parent_id] ? ' data-' + v.data_map.parent_id + '="' + items[k][v.data_map.parent_id] + '"' : '') + ' data-value="' + (items[k].value || '') + '">' + (items[k].label || '') + '</span></li>';
                    }
                    lists += '</ul>';
                }
                lists += '</div>';
            }

            return '<div class="place js-tab-data-list">' + lists + '<div class="clearfix"></div></div>';
        },
        tabSubNav: function (d) {
            var v = this._getVars(d, ['data_map', 'data']), l = '';

            if (typeof v.data_map !== 'object' || typeof v.data !== 'object' || $.isEmptyObject(v.data))
                return '';

            for (var k in v.data) {
                l += '<li><a href="javascript:void(0)" data-field="' + (v.data[k].field || '') + '" data-value="' + (v.data[k].value || '') + '">' + (v.data[k].label || '') + '</a></li>';
            }

            return '<div class="row space_bottom_l js-tab-sub-nav">' +
                    '    <div class="head-left">' +
                    '        <ul class="nav-fast">' + l + '</ul>' +
                    '    </div>' +
                    '    <div class="head-right">' +
                    '    </div>' +
                    '</div>';
        },
        metro_item: function (d) {

            if (typeof d !== 'object' || d === null)
                return $();

            var st = $('<div>', {
                'class': 'metro-station js-add-search-item',
                'data-field': 'metro_station_id',
                'data-value': d.metro_station_id,
                'data-label': d.name}),
                    marker = $('<div>', {'class': 'metro-station-marker'}),
                    p;

            marker.css(d.params.marker);
            st.append(marker);

            p = d.params.points || [];

            for (var k in d.params.points) {
                st.append($('<div>', {'class': 'metro-station-point'}).css(d.params.points[k]));
            }

            return st;
        },
        metro: function (d) {
            var list = [];
            if (!$.isArray(d))
                return '';

            try {

                for (var k in d) {

                    if (typeof d[k].params === 'string') {
                        d[k].params = JSON.parse(d[k].params);
                    }

                    if (d[k].params)
                        list.push(this.metro_item(d[k]).prop('outerHTML'));
                }
            } catch (er) {
                // @todo
                console.log(er);
            }

            $('.metro-loader').hide();
            return '<div class="metro-page"><img src="/images/metro/mos.jpg" alt="Поиск новостроек по метро">' + list.join('') + '</div>';
        }
    };

    var model = {
        _tabData: {},
        loadTabData: function (tab, responseCall) {
            var t = tab, cachePrefix = 'g-alphabet--', m = module + '_' + 'loadTabData', url;

            // get from cache
            this._tabData[t] = FlCache.get(cachePrefix + t);

            if (typeof this._tabData[t] === 'object') {
                // callback для обработки результатов после рендеринга
                if (typeof responseCall === 'function')
                    model._tabData[t].responseCall = responseCall;
                $.publish(m, model._tabData[t]);
                return;
            }

            url = t === 'metro' ? '/ajax/metro_stations_list' : '/ajax/geo_alphabet/';

            $.getJSON(url, {t: t}, function (response) {
                if (response.success) {
                    model._tabData[t] = {tab: t, data: response.data, data_map: response.data_map, sub_nav: response.sub_nav};
                    // callback для обработки результатов после рендеринга
                    if (typeof responseCall === 'function')
                        model._tabData[t].responseCall = responseCall;
                    FlCache.set(cachePrefix + t, model._tabData[t], 60 * 120);
                    $.publish(m, model._tabData[t]);
                } else {
                    // @todo
                    console.log(response);
                    if (typeof responseCall === 'function')
                        responseCall();
                }
            });

        }
    };

    app.init();
    return app.publ;
}());

