/* global FlGeoIndex, FlHelper, FlCache */

var FlSearch = (function () {
    var componentTypes = {
        checkbox: function (name) {
            return Object.create({
                item: null,
                v: [],
                init: function () {
                    var self = this;

                    this.item = app.form.find('[name="' + name + '"]');

                    if (!this.item || !this.item.length)
                        return;

                    $(this.item).parents('.checkbox-btn').off('click').on('click', function (e) {

                        var ch = $(this).find(':checkbox');
                        ch.prop('checked', (ch.prop('checked') === true ? false : true));

                        self.getValues();
                        self.update();
                    });
                },
                setValues: function (v) {
                    var vs = FlHelper.arr.get(v, name);

                    if (this.item === null || typeof vs !== typeof this.v)
                        return;

                    for (var k in vs)
                        this.item.filter('[value="' + vs[k] + '"]').prop('checked', true);

                    this.v = vs;

                    this.update();
                },
                getValues: function () {
                    var self = this;
                    this.v = [];

                    if (this.item === null)
                        return this.v;

                    this.item.each(function () {
                        if ($(this).prop('checked') === true)
                            self.v.push($(this).val());
                    });

                    return this.v;
                },
                update: function () {
                    var self = this;
                    var parents = $(this.item).parents('.checkbox-btn');

                    parents.removeClass('active');

                    for (var k in this.v) {
                        $(this.item).filter('[value="' + this.v[k] + '"]').parents('.checkbox-btn').addClass('active');
                    }

                }
            });
        },
        radio_inputs: function (config) {
            return Object.create({
                config: {
                    radioName: '',
                    inputNames: []
                },
                nav: null,
                input: null,
                inputLastValue: {},
                v: 0,
                init: function () {
                    var self = this;

                    // apply config
                    if (typeof config === 'object') {
                        this.config.radioName = !!config.radioName ? config.radioName : '';
                        this.config.inputNames = $.isArray(config.inputNames) ? config.inputNames : [];
                    }

                    // init elements
                    this.nav = app.form.find('[name="' + this.config.radioName + '"]');
                    // @todo
                    this.input = app.form.find('[name="' + this.config.radioName + '"]')
                            .parents('.form-group').find('input[type=text]');

                    if (!this.nav || !this.nav.length || !this.input || !this.input.length)
                        return;

                    // set last input values
                    function setInputLastValue() {
                        self.input.each(function () {
                            self.inputLastValue[$(this).attr('name')] = $(this).val();
                        });
                    }
                    setInputLastValue();

                    // set up listener
                    $(this.nav).parents('.radio-link').off('click').on('click', function (e) {
                        e.preventDefault();

                        // stop event on active radio
                        if ($(this).hasClass('active'))
                            return false;

                        // find radio & set state
                        var it = $(this).find(':radio');
                        it.prop('checked', (it.prop('checked') === true ? false : true));

                        self.getValues();
                        self.update();
                        // publish event only when inputs has data
                        self.input.each(function () {
                            if ($(this).val()) {
                                $.publish('search_form_change');
                                return false;
                            }
                        });

                        return false;
                    });

                    if (typeof config.decorateValues === 'function') {
                        this.input.off('keyup').on('keyup', function (e) {
                            e.preventDefault();
                            var v = $(this).val().split(' ').join('');
                            if (v)
                                $(this).val(config.decorateValues(v));
                            return;
                        });
                    }

                    // blur focus from inputs
                    this.input.off('blur').on('blur', function (e) {
                        // publish event if input was changed
                        if ($(this).val() !== self.inputLastValue[$(this).attr('name')]) {
                            $.publish('search_form_change');
                            setInputLastValue();
                        }

                    });
                },
                setValues: function (v) {

                    var vi, i;

                    if (this.item === null)
                        return {};

                    this.v = Number(FlHelper.arr.get(v, config.radioName));
                    this.v = isNaN(this.v) ? config.defaultRadioActive : this.v;

                    this.nav.filter('[value="' + this.v + '"]').prop('checked', true);
                    this.update();

                    for (var k in config.inputNames) {
                        i = config.inputNames[k];
                        vi = FlHelper.arr.get(v, i);
                        if (vi) {
                            i = this.input.filter('[name="' + i + '"]');
                            if (i.length) {
                                i.val(vi.split('+').join(''));
                                i.trigger('keyup');
                            }
                        }

                    }
                },
                getValues: function () {
                    var self = this;

                    if (this.item === null)
                        return this.v;

                    this.nav.each(function () {
                        if ($(this).prop('checked') === true)
                            self.v = Number($(this).val());
                    });

                    return this.v;
                },
                // update view
                update: function () {
                    var self = this;
                    var parents = $(this.nav).parents('.radio-link');
                    parents.removeClass('active');
                    $(this.nav).filter('[value="' + this.v + '"]').parents('.radio-link').addClass('active');
                }
            });
        },
        search_input: function (name) {
            return Object.create({
                item: null,
                v: '',
                init: function () {
                    var self = this;

                    this.item = app.form.find('[name="' + name + '"]');

                    if (!this.item || !this.item.length)
                        return;

                    this.item.off('keypress').on('keypress', self.getObjectNames);

                },
                setValues: function (v) {
                    var vs = FlHelper.arr.get(v, name);

                    if (this.item === null || typeof vs !== typeof this.v)
                        return;

                    this.item.val(this.v = vs.split('+').join(' '));
                },
                // autocomplete object names
                getObjectNames: function (e) {
                    var autoCompelteElement = this;

                    $(autoCompelteElement).autocomplete({
                        source: function (request, response) {
                            // ajax load
                            var url = location.protocol + '//' + location.host + '/ajax/object_names';
                            $.getJSON(url, {name: request.term}, function (data) {
                                // check success
                                if (data.success === false) {
                                    return false;
                                }

                                response($.map(data.data, function (item) {
                                    return{
                                        label: item.label,
                                        value: item.value
                                    };
                                }));
                            });
                        },
                        // hide value on focus
                        focus: function (event, ui) {
                            return false;
                        },
                        select: function (event, ui) {
                            var selectedObj = ui.item;
                            $(autoCompelteElement).val(selectedObj.label);
                            return false;
                        }
                    });
                }
            });
        },
        geo_index: function (config) {
            return Object.create({
                init: function () {
                    var self = this;
                },
                setValues: function (v) {
                    var t = FlCache.get('g-index--type'), it = FlCache.get('g-index--items');

                    if (FlHelper.arr.get(v, 'geo_index[type]') === t && Object.keys(it).length) {
                        app._setGeoIndex(it, t);
                        this._updGeoIndex(t, it);
                    } else {
                        // only get
                        var type = FlHelper.arr.get(v, 'geo_index[type]'), list, field;

                        if (!type)
                            return;

                        list = {};

                        for (var k in v) {
                            field = k.match(/geo_index\[([a-z_]*)\]\[\]/i);
                            if (!field)
                                continue;
                            field = field[1];

                            for (var kk in v[k]) {
                                list[field + '__' + v[k][kk]] = {
                                    field: field,
                                    value: v[k][kk]
                                };
                            }
                        }

                        app._setGeoIndex(list, type);
                        this._updGeoIndex(type, list);

                    }
                },
                /**
                 * обновить содержимое popup Geo index
                 * - выделяет в модальном окне текущие позиции
                 * @param {string} type - тип списка (группа в модальном окне)
                 * @param {object} list - список элементов (geo объекты)
                 * @return {undefined}
                 */
                _updGeoIndex: function (type, list) {
                    $(document).on('ready', function () {

                        if (typeof FlGeoIndex !== 'object')
                            return;

                        FlGeoIndex.setSearchItems(list);
                        FlGeoIndex.setType(type);
                        FlGeoIndex.fill();
                    });
                },
                beforeSubmit: function () {
                    var cachePrefix = 'g-index';

                    if (typeof FlGeoIndex !== 'object')
                        return;

                    FlCache.set(cachePrefix + '--items', FlGeoIndex.getSearchItems());
                    FlCache.set(cachePrefix + '--type', FlGeoIndex.getType());
                }
            });
        }
    };

    var app = {
        formChangeEventName: 'search_form_change',
        isMap: false,
        componentsList: {
            rooms: {type: 'checkbox', config: 'rooms[]'},
            complite: {type: 'checkbox', config: 'complite[]'},
            price: {type: 'radio_inputs', config: {radioName: 'price_type', inputNames: ['cost_min', 'cost_max'], defaultRadioActive: 0, decorateValues: FlHelper.num.numberFormat}},
            name: {type: 'search_input', config: 'name'},
            geo: {type: 'geo_index', config: {}}
        },
        form: $('.search-panel').first(),
        components: {},
        geoIndexTypes: {
            'msk|regions': 'Районы Москвы',
            'metro': 'Метро',
            'mo|regions': 'Районы МО',
            'mo|cities': 'Города МО',
            'new_msk|regions': 'Районы Новой Москвы'
        },
        init: function () {

            this.isMap = $(this.form).hasClass('search-panel-map');
            // prepare for map
            if (this.isMap) {
                this.loader = this.form.find('.progress');

                $.subscribe('fl_map_ready', function (e) {
                    FlMap.searchObjects();
                });
            }


            this.initComponents();
            this.setUpListeners();

        },
        setUpListeners: function () {
            this.form.find('.clear-input input').off('focus').on('focus', app.modalOpen);
            this.form.find('.clear-input--clear').off('click').on('click', app.clearGeoIndex);
            this.form.off('submit').on('submit', app.submit);

            if (this.isMap) {
                this.form.find(':input').on('change', function (e) {
                    app.form.submit();
                });
                // 'search_form_change'
                $.subscribe(app.formChangeEventName, function (e) {
                    app.form.submit();
                });

                $.subscribe('map_objects_loaded', function (e, data) {
                    if (app.loader)
                        app.loader.hide();
                });
            }

        },
        modalOpen: function (e) {
            $('#modal__search_panel').modal();
        },
        clearGeoIndex: function (e) {
            app._setGeoIndex();
        },
        submit: function (e) {
            e.preventDefault();
            var d = FlForm.getFormData(app.form), uri = '';

            // map search
            if (app.isMap) {

                if (typeof FlMap !== 'object')
                    return;

                if (app.loader)
                    app.loader.show();
                FlMap.searchObjects(d);

                return;
            }

            for (var k in d) {
                if (d[k] === '') {
                    delete(d[k]);
                } else {
                    if (typeof d[k] === 'object')
                        for (var kk in d[k])
                            uri += '&' + k + '=' + d[k][kk];
                    else
                        uri += '&' + k + '=' + d[k];
                }
            }

            for (var c in app.components) {
                if (typeof app.components[c].beforeSubmit === 'function')
                    app.components[c].beforeSubmit();
            }

            location.href = '/catalog/search/?' + uri;
            return false;
        },
        initComponents: function () {
            var cMeta, c, get = FlHelper.GET();

            for (var k in this.componentsList) {
                cMeta = this.componentsList[k];

                // create by type
                if (typeof componentTypes[cMeta.type] !== 'function')
                    continue;

                c = componentTypes[cMeta.type](cMeta.config);

                if (typeof c.init === 'function')
                    c.init();

                if (typeof c.setValues === 'function')
                    c.setValues(get);

                this.components[k] = c;
            }
        },
        /**
         * Заполняет список фильтра Расположение
         * @param {object} gi - object of geo_index
         * @param {string} t - type of geo index
         * @return {undefined}
         */
        _setGeoIndex: function (gi, t) {

            var l;

            this.form.find('.js-clear-input-values').empty();

            if (!gi || (typeof gi === 'object' && !Object.keys(gi).length)) {
                // clear
                this.form.find('.clear-input input').val('');
                this.form.find('.clear-input--clear').hide();
                return;
            }

            if (typeof gi === 'object') {
                // set type
                this.form.find('.js-clear-input-values').append($('<input>', {
                    type: 'hidden',
                    name: 'geo_index[type]',
                    value: t
                }));

                // set label
                l = (this.geoIndexTypes[t] || 'Районы') + ': ' + Object.keys(gi).length;
                this.form.find('.clear-input input.form-control').val(l);
                // set hidden values
                for (var k in gi) {
                    this.form.find('.js-clear-input-values').append($('<input>', {
                        type: 'hidden',
                        name: 'geo_index[' + gi[k].field + '][]',
                        value: gi[k].value
                    }));
                }

                this.form.find('.clear-input--clear').show();
            }

            this.setUpListeners();
        },
        publ: {
            setGeoIndex: function (geoIndexSearchItems, type) {
                app._setGeoIndex(geoIndexSearchItems, type);
            }
        }
    };

    app.init();
    return app.publ;
}());