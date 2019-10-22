/* global FlRegister, FlHelper, FlCache, doT */

if (typeof String.prototype.translit !== 'function') {
    String.prototype.translit = (function () {
        var L = {
            'A': 'A', 'B': 'B', 'C': 'C', 'D': 'D', 'E': 'E', 'F': 'F', 'G': 'G', 'H': 'H',
            'I': 'I', 'J': 'J', 'K': 'K', 'L': 'L', 'M': 'M', 'N': 'N', 'O': 'O', 'P': 'P',
            'Q': 'Q', 'R': 'R', 'S': 'S', 'T': 'T', 'U': 'U', 'V': 'V', 'W': 'W', 'X': 'X',
            'Y': 'Y', 'Z': 'Z', 'a': 'a', 'b': 'b', 'c': 'c', 'd': 'd', 'e': 'e', 'f': 'f',
            'g': 'g', 'h': 'h', 'i': 'i', 'j': 'j', 'k': 'k', 'l': 'l', 'm': 'm', 'n': 'n',
            'o': 'o', 'p': 'p', 'q': 'q', 'r': 'r', 's': 's', 't': 't', 'u': 'u', 'v': 'v',
            'w': 'w', 'x': 'x', 'y': 'y', 'z': 'z',
            'А': 'a', 'а': 'a', 'Б': 'b', 'б': 'b', 'В': 'v', 'в': 'v', 'Г': 'g', 'г': 'g',
            'Д': 'd', 'д': 'd', 'Е': 'e', 'е': 'e', 'Ё': 'yo', 'ё': 'yo', 'Ж': 'zh', 'ж': 'zh',
            'З': 'z', 'з': 'z', 'И': 'i', 'и': 'i', 'Й': 'j', 'й': 'j', 'К': 'k', 'к': 'k',
            'Л': 'l', 'л': 'l', 'М': 'm', 'м': 'm', 'Н': 'n', 'н': 'n', 'О': 'o', 'о': 'o',
            'П': 'p', 'п': 'p', 'Р': 'r', 'р': 'r', 'С': 's', 'с': 's', 'Т': 't', 'т': 't',
            'У': 'u', 'у': 'u', 'Ф': 'f', 'ф': 'f', 'Х': 'kh', 'х': 'kh', 'Ц': 'c', 'ц': 'c',
            'Ч': 'ch', 'ч': 'ch', 'Ш': 'sh', 'ш': 'sh', 'Щ': 'shch', 'щ': 'shch', 'Ъ': '', 'ъ': '',
            'Ы': 'y', 'ы': 'y', 'Ь': "", 'ь': "", 'Э': 'eh', 'э': 'eh', 'Ю': 'yu', 'ю': 'yu',
            'Я': 'ya', 'я': 'ya', ' ': '-',
            "-": "-", "—": "-", "(": "", ")": "", "«": "",
            "»": "", ",": "", "%": "", ".": "", "/": "", "\'": "",
            "*": "", "?": "", "&": "", "^": "", ":": "", ";": "", "#": "",
            "<": "", ">": ""
        },
                r = '',
                k;
        for (k in L)
            r += k;
        r = new RegExp('[' + r + ']', 'g');
        k = function (a) {
            return a in L ? L[a] : a;
        };
        return function () {
            return this.replace(r, k);
        };
    })();
}

;
var FlDashboardForm = function () {
    var app = {
        elements: {
            zone_id: {
                relation: {
                    child: ['geo_area_id', 'populated_locality_id']
                }
            },
            geo_direction_id: {
                relation: {
                    child: ['geo_area_id', 'populated_locality_id']
                }
            },
            geo_area_id: {
                relation: {
                    parent: ['zone_id', 'geo_direction_id']
                },
                loadPath: 'geo_area'
            },
            populated_locality_id: {
                relation: {
                    parent: ['zone_id', 'geo_direction_id', 'geo_area_id']
                },
                loadPath: 'populated_locality'
            },
            district_id: {
                relation: {
                    parent: ['zone_id']
                }
//                loadPath: 'subregion'
            },
            square_id: {
                relation: {
                    parent: ['district_id']
                },
                loadPath: 'square'
            }
        },
        init: function () {

            // init tags
            $('.methodTags').tagit({
                availableTags: FlRegister.get('tags'),
                fieldName: 'tags',
                caseSensitive: false,
                singleField: true,
                singleFieldDelimiter: '|',
                singleFieldNode: $('.mySingleFieldNode'),
                allowSpaces: true
            });

            // init panel tabs
            $('.panel-tabs').panelTabs();

            $('.js-select2').select2();

            app.setUpListeners();
            app.paginationButtonActive();
            app.addDropFilterButton();
            this.setActiveSortBy();
        },
        setUpListeners: function () {
            // @deprecated
//            $('[name="zone_id"]').off('change').on('change', app.publ.changeSelect);
            $('[name="geo_direction_id"]').off('change').on('change', app.publ.changeSelect);
            // click on sort link (sort by column)
            $('.sort_link').off('click').on('click', app.sortBy);
            // select current text 
            $('.is_selected').off('click').on('click', app._textSelectToggle);
            // toggle panel body
            $('.panel-body-cover').find('.panel-heading').off('click').on('click', app.togglePanelBody);
            // save & close
            $('.sv_cl').off('click').on('click', app.saveClose);

            // data-autocomplete
            $('[data-autocomplete]').off('keypress').on('keypress', function (e) {
                if (typeof app.autocomplete[$(this).data('autocomplete')] === 'function')
                    app.autocomplete[$(this).data('autocomplete')].call(this);
                else
                    console.log('app.autocomplete[' + $(this).data('autocomplete') + '] is not a function!');
            });

            // copy form group by template
            $('[data-copy-fg]').off('click').on('click', function (e) {
                var fg = $(this).parents('[data-fg]');
                fg.parent('div').append(app.publ.renderTemplate($(this).data('copy-fg'), {index: fg.siblings('.form-group').length}));
                app.setUpListeners();
            });
            // rm form group
            $('.rm-form-group').off('click').on('click', function (e) {
                var fg = $(this).parents('.form-group');
                if (fg.length && confirm('Вы уверены?')) {
                    // rm or clear
                    if ($('.rm-form-group[data-type="' + $(this).data('type') + '"]').length > 1)
                        fg.remove();
                    else
                        fg.find(':input').each(function () {
//                            if ($(this).prop('tagName') === 'SELECT')
//                                $(this).find('[valie=""]').attr('selected', 'selected');
//                            else
                            $(this).val('').trigger("change");
                            ;
                        });
                }
            });

            // decorate input values
            $('.js-input-number').off('blur').on('blur', function (e) {
                e.preventDefault();
                var v = $(this).val().split(' ').join('');
                if (v)
                    $(this).val(FlHelper.num.numberFormat(v, $(this).data('decimal') || 0));

                return false;
            });

            // global actions
            $('[data-action_global]').off('click').on('click', app.doAction);

            // скрыть / показать месседж
            $('.global__plug').off('click').on('click', function (e) {
                $(this).toggle(300);
            });
        },
        autocomplete: {
            metro_stations: function () {
                var autoCompelteElement = this;
                var hiddenElementID = $(autoCompelteElement).siblings('input[type="hidden"]');

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
                                $(autoCompelteElement).val(ui.item.label);
                                $(hiddenElementID).val(ui.item.value);
                                return false;
                            }
                        }
                );
            },
            /**
             * autocomplete request + render
             * @param {$} el - jQuery element (input)
             * @param {string} uriPath - uri path (/admin/ajax/...)
             * @param {string} dataLabel - label for data response
             * @param {string} dataValue - value for data response
             * @param {object} requestParams - params for request (optional)
             * @return {undefined}
             */
            _exec: function (el, uriPath, dataLabel, dataValue, requestParams) {

                requestParams = typeof requestParams === 'object' ? requestParams : {};

                $(el).autocomplete({
                    source: function (request, response) {

                        requestParams.name_like = request.term;
                        // ajax load
                        var url = location.protocol + '//' + location.host + uriPath;
                        $.getJSON(url, requestParams, function (data) {
                            // check success
                            if (data.success === false) {
                                return false;
                            }

                            response($.map(data.data, function (item) {
                                return{
                                    label: item[dataLabel] || '',
                                    value: item[dataValue] || ''
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
                        $(el).val(selectedObj.label);
                        return false;
                    }
                });
            },
            geo_area: function () {
                app.autocomplete._exec($(this), '/admin/ajax/search', 'name', 'geo_area_id', {model: 'geo_area_model'});
            },
            populated_locality: function () {
                app.autocomplete._exec($(this), '/admin/ajax/search', 'name', 'populated_locality_id', {model: 'populated_locality_model'});
            },
            district: function () {
                app.autocomplete._exec($(this), '/admin/ajax/search', 'name', 'district_id', {model: 'district_model'});
            },
            square: function () {
                app.autocomplete._exec($(this), '/admin/ajax/search', 'name', 'square_id', {model: 'square_model'});
            },
            organizations: function () {
                app.autocomplete._exec($(this), '/admin/ajax/search', 'name', 'organization_id', {model: 'organizations_model'});
            },
            tag: function () {
                app.autocomplete._exec($(this), '/admin/ajax/search', 'name', 'tag_id', {model: 'tags_model'});
            },
            metro_line: function () {
                app.autocomplete._exec($(this), '/admin/ajax/search', 'name', 'metro_line_id', {model: 'metro_line_model'});
            },
            metro_station: function () {
                app.autocomplete._exec($(this), '/admin/ajax/search', 'name', 'metro_station_id', {model: 'metro_station_model'});
            },
            registry: function () {
                app.autocomplete._exec($(this), '/admin/ajax/search', 'name', 'registry_id', {model: 'registry_model'});
            }

        },
        /**
         * On click - select/unselect text
         * @param {type} e
         * @returns {undefined}
         */
        _textSelectToggle: function (e) {
            $(this).select();
        },
        // обработка события: сортировка по полю
        sortBy: function () {
            var field = $(this).data('by'),
                    get = FlHelper.GET(),
                    cls_prefix = 'sort_link__',
                    clss = $(this).attr('class');

            // определяем было ли это поле использованно для сортировки до этого
            if (clss.search(cls_prefix) !== -1) {
                // в каком направлении была сортировка
                if (clss.search('__asc') !== -1) {
                    // в прямом порядке, значит будет сортировать в обратном
                    get.sort_by = field;
                    get.sort_direction = 'desc';
                } else {
                    // в обратном порядке , значит будем сортировать в прямом порядке
                    get.sort_by = field;
                    get.sort_direction = 'asc';
                }
            } else {
                // сортировка по текущему полю не применялась
                // значит будем сортировать в прямом порядке
                get.sort_by = field;
                get.sort_direction = 'asc';
            }
            window.location = 'http://' + window.location.host + window.location.pathname + FlHelper.arr.toUri(get);
        },
        // устанавливаем класс для активного столбца сортировки
        setActiveSortBy: function (get) {
            get = typeof get === 'object' ? get : FlHelper.GET();
            var field = FlHelper.arr.get(get, 'sort_by', false),
                    direction = FlHelper.arr.get(get, 'sort_direction', false),
                    cls_prefix = 'sort_link__',
                    directionVariant = ['asc', 'desc'];

            // удаляем все отметки (классы) о сортировках
            $('[class^="' + cls_prefix + '"]').removeClass(cls_prefix + 'asc').removeClass(cls_prefix + 'desc');
            // если есть поле сортировки и порядок сортировки входит в массив заранее определенных 
            // то находим этот столбец и задаем класс
            if (field && $.inArray(direction, directionVariant) !== -1) {
                var el = $('[data-by="' + field + '"]');
                $(el).addClass(cls_prefix + direction);

                $(el).parents('.header').addClass('header-sort-' + (direction === 'asc' ? 'up' : 'down'));
            }
        },
        dropSelect: function (target) {
            $('[name="' + target + '"] option[value="0"]').attr('selected', 'selected');
        },
        createSelectOptions: function (target, data) {
            app.publ.clearSelect(target);
            // append options
            for (var k in data) {
                $('[name="' + target + '"]').append(new Option(data[k], k));
            }
        },
        // делает кнопку пагинации ВСЕ активной
        paginationButtonActive: function () {
            if (FlHelper.arr.get(FlHelper.GET(), 'per_page', false) === '-1')
                $('.pagination_btn').addClass('pagination_btn__active');
        },
        // добавляем во все формы-фильтры кнопку сбросить | Сброс
        addDropFilterButton: function () {
            var btn = $('<a>', {
                text: 'Сброс',
                title: 'Сброс',
                href: window.location.pathname,
                class: 'btn btn-default pull-right space_right'
            });
            $('.form_filter [type="submit"]').after(btn);
        },
        /**
         * Toggle panel body
         * @param {object} e
         * @returns {undefined}
         */
        togglePanelBody: function (e) {
            $(this).siblings('.panel-body').toggle(500);
        },
        /**
         * Save & close form
         * @param {type} e
         * @returns {undefined}
         */
        saveClose: function (e) {
            var form = $(this).parents('form');
            var url = location.href.indexOf('?') !== -1 ? location.href + '&close=1' : location.href + '?close=1';
            form.attr('action', url);
            form.submit();
        },
        /**
         * do some action
         * @param {object} e - event
         * @returns {undefined}
         */
        doAction: function (e) {
            var action = $(this).data('action_global');
            if (typeof app.actions[action] === 'function')
                app.actions[action].call(this, e);
            else
                console.log('Action: ' + action + ' not found!');
        },
        actions: {
            /**
             * Событие - общий action delete
             * @param {object} e - event
             * @returns {undefined}
             */
            delete: function (e) {
                // удаление данных из таблиц
                var dataSource = $(this).parent('td'), type = dataSource.data('object_type'), id = dataSource.data('id');

                if (!type || !id) {
                    app.publ.toggleGlobalMessage('Недостаточно данных для удаления.', app.publ.globalMessageTypes.danger);
                    console.log(dataSource.data());
                    return;
                }

                if (!confirm('Вы уверены что хотите удалить элемент ' + id + '?'))
                    return;

                // подписываемся на собятия удаления
                $.subscribe('onSuccessDelete.' + type + '.' + id, function (ev) {
                    $(dataSource).parents('tr').remove();
                });

                $.subscribe('onErrorDelete.' + type + '.' + id, function (ev, err) {
                    app.publ.toggleGlobalMessage(err || 'Неизвестная ошибка', app.publ.globalMessageTypes.danger);
                });
                // удаляем
                app._delete(type, id);
            },
            do_alias: function (e) {
                var d = $(this).data(), changeClass = 'bg-warning', val;

                if (!d.source || !$(d.source).length) {
                    console.error('source not found!');
                    console.error(d.source);
                    return;
                }
                if (!d.target || !$(d.target).length) {
                    console.error('target not found!');
                    console.error(d.target);
                    return;
                }
                // define value
                val = typeof $(d.source).val() === 'string' ? $(d.source).val() : '';
                // decorate target input
                $(d.target).addClass(changeClass);
                // set new value in target input
                $(d.target).val(app.publ.prepareAlias($(d.source).val().translit()));
                // rm class after interval
                setTimeout(function () {
                    $(d.target).removeClass(changeClass);
                }, 1000);
            }
        },
        /**
         * Удаление объекта
         * @param {string} type
         * @param {int|string} id
         * @events - события метода: <br>
         * <b>onSuccessDelete.{type}.{id}</b> - успешное удаление объекта <br>
         * <b>onErrorDelete.{type}.{id}</b> - ошибка удаления <br>
         * @return {undefined}
         */
        _delete: function (type, id) {

            var eventIndex = 'Delete.' + type + '.' + id;

            $.post('/admin/ajax/delete', {type: type, id: id}, function (response) {
                if (response.success) {
                    $.publish('onSuccess' + eventIndex);
                } else {
                    $.publish('onError' + eventIndex, response.error || 'Неизвестная ошибка.');
                }
            }, 'json').error(function (er) {
                console.log(er);
                $.publish('onError' + eventIndex, 'Что-то пошло не так.');
            });
        },
        publ: {
            /**
             * Обновляем список option
             * @param {type} target
             * @returns {Boolean}
             */
            updateSelect: function (target) {
                // собираем значение родительских селектов
                var el = app.elements[target],
                        parent = typeof el === 'object' ? el.relation.parent : false,
                        params = {},
                        cacheIndex = '';

                if (typeof el !== 'object' || el.loadPath === undefined)
                    return false;

                for (var k in parent) {
                    var vl = $('[name="' + parent[k] + '"]').val();
                    params[parent[k]] = vl;
                    cacheIndex += vl;
                }
                // запрашиваем в кеше
                var cacheData = FlCache.get(cacheIndex);

                // select current 
                function _select(target) {
                    // если нет полей формы и нет данных по текущему полю вернем false 
                    var fields = FlRegister.get('fields');
                    if (!fields)
                        return false;
                    if (fields[target] === undefined)
                        return false;
                    app.publ.selectSelected(target, fields[target]);
                }

                if (cacheData !== false) {
                    app.createSelectOptions(target, cacheData);
                    _select(target);

                } else {
                    // ajax load
                    var url = 'http://' + window.location.host + '/ajax/' + el.loadPath;

                    $.getJSON(url, params, function (data) {
                        // check success
                        if (data.success === false) {
                            app.publ.clearSelect(target);
                            return false;
                        }
                        app.createSelectOptions(target, data.data);
                        // если в кеше такая зона существует добавляем в нее данные и сохраняем
                        FlCache.set(cacheIndex, data.data);

                        // select current
                        _select(target);
                    });
                }
            },
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
            /**
             * Обрпаботка события изменение селекта (setUpListeners)
             * @deprecated
             * @returns {undefined}
             */
            changeSelect: function () {
                var child = app.elements[$(this).attr('name')].relation.child;
                if (child !== undefined) {
                    for (var k in child) {
                        app.dropSelect(child[k]);
                        app.publ.updateSelect(child[k]);
                    }
                }

            },
            /**
             * Удаляем лишние option в селекте
             * @param {type} target
             * @returns {undefined}
             */
            clearSelect: function (target) {
                $('[name="' + target + '"] option[value!="0"]').remove();
            },
            /**
             * prepare alias
             * @param {string} str
             * @return {string}
             */
            prepareAlias: function (str) {
                str = str.toLowerCase();
                return str.replace(/[^A-Za-z0-9-]+/g, '-').replace(/[-]{2,}/gim, '-').replace(/^\-+/g, '').replace(/\-+$/g, '');
            },
            /**
             * Type of message
             */
            globalMessageTypes: {
                success: 1,
                danger: 2,
                info: 3
            },
            /**
             * Show global loader
             * @returns {undefined}
             */
            toggleLoader: function () {
                // create loader
                if ($('.global_loader__plug').length === 0) {
                    $('body').append($('<div>', {
                        class: 'global_loader__plug'
                    }));
                }
                $('.global_loader__plug').toggle();
            },
            /**
             * Show / hide global message
             * @param {string} message
             * @returns {undefined}
             */
            toggleGlobalMessage: function (message, type) {
                type = type === undefined ? this.globalMessageTypes.success : type;
                var strType = FlHelper.arr.getKeyByValue(this.globalMessageTypes, type);
                if ($('.global__plug .alert-' + strType).length === 0) {
                    $('body').append($('<div>', {
                        class: 'global__plug',
                        html: $('<div>', {
                            class: 'global__message alert alert-' + strType,
                            html: message
                        })
                    }));
                }
                if (message !== undefined)
                    $('.global__plug .global__message').html(message);
                $('.global__plug').toggle();
                app.setUpListeners();
            },
            setActiveSortBy: function (get) {
                app.setActiveSortBy(get);
            },
            /**
             * Render template
             * @todo checks
             * @param {string} tplSelector - jquery selector
             * @param {object} data - object for template
             * @return {html}
             */
            renderTemplate: function (tplSelector, data) {
                var tpl = doT.template($(tplSelector).html());
                return tpl(data);
            }
        }
    };
    app.init();
    return app.publ;
}();



