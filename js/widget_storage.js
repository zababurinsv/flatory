/**
 * Storage widget
 * @type Function|@exp;app@pro;publ
 */
var FlWidgetStorage = (function () {
    var app = {
        section: '',
        massEdit: null,
        viewType: 'tile',
        targets: {
            init: '#init_widget_storage',
            destroy: '#destroy_widget_storage',
            widget: '.js-ws',
            nav: '.widget_storage__nav',
            content: '.widget_storage__content',
            add: '#widget_storage__add',
            popupSelect: '.select_from_srorage'
        },
        init: function () {
            this.setUpListeners();
            this.section = 'upload';
            this.renderSection();
            if (typeof FlWidgetMassEditImage !== 'undefined')
                this.massEdit = FlWidgetMassEditImage.hideApply();
        },
        setUpListeners: function () {
            // init / destroy widget
            $(this.targets.init).off('click').on('click', app.toggleWidget);
            $(this.targets.nav).find('a').off('click').on('click', app.clickNav);
            $(this.targets.destroy).off('click').on('click', app.toggleWidget);
            // view type
            $(this.targets.widget).find('.view-type').off('click').on('click', app.clickViewType);
            // click add 
            $(this.targets.add).off('click').on('click', app.clickAdd);

            // popup storage
            $('.select_from_srorage').off('click').on('click', app.modalWidget);
        },
        modalWidget: function (e) {
            var modal = $('.modal__storage'), self = this, filters;
            // set current filters
            filters = $(this).data('filters');

            if ($(this).data('view-type') === 0)
                modal.find('.view-type').hide();

            if ($(this).data('select-type') === 'radio') {
                modal.find('[name="check_all_files"]').parents('li').hide();
                modal.find('[type="checkbox"]').prop('type', 'radio');
                FlWidgetStorageStorage.extends.renderTile(function () {
                    modal.find('[type="checkbox"]').prop('type', 'radio');
                });
                // set event submit popup
                modal.find('[type="submit"]').off('click').on('click', function (e) {

                    var items = modal.find('.storage_' + app.viewType).find(':checked'),
                            fileId = items.val(), image;

                    if (!fileId) {
                        console.log('file_id not found');
                        console.log(app);
                        return false;
                    }

                    image = items.parents('.js-ws-list-item').find('.thumbnail').attr('src').split('/').pop();

                    $(self).parents('.image_simple_upload').find('.uploaded_image').attr('src', '/images/original/' + image);
                    $(self).parents('.image_simple_upload').find('[name="file_id"]').val(fileId);
                    modal.modal('hide');
                });
            }
            modal.modal();
            // drop pagination
            modal.find('[data-pg="1"] a').trigger('click');

            if (typeof FlFilter === 'object') {
                // events come back
                FlFilter.refreshEventListener();

                if (typeof filters === 'object') {
                    // set filters values
                    FlFilter.setValues(filters, modal);
                    // get data by filters
                    FlFilter.submit();
                }
            }
        },
        toggleWidget: function (e) {
            $(app.targets.widget).not('.modal').toggle(500);
            console.log($(app.targets.widget).not('.modal').find('.js-ws-nav .active a'));
            $(app.targets.widget).not('.modal').find('.js-ws-nav .active a').trigger('click', {force_load_data: true});
        },
        clickNav: function (e) {
            $(app.targets.nav).find('li').removeClass('active');
            $(this).parent('li').addClass('active');
            app.section = $(this).data('section');
            app.renderSection();
        },
        clickAdd: function (e) {
            // check proportions
            if (!app.checkProportions()) {
                alert('Операция прервана! Должны быть отмечены все пропорции изображений!');
                return false;
            }

            // edit images
            if (typeof app.massEdit === 'function') {
                app.massEdit({}, app.successEditImage, function (response) {
                    console.log(response);
                });
            } else {

            }
        },
        successEditImage: function (response) {
            var files = response.data.files;

            if (typeof files !== 'object') {
                alert('Ошибка. Не удалось получить ответ при преобразовании изображений!');
                return false;
            }
            FlObjectCard.renderTile(files);
            $(app.targets.widget).find('[name="file_id[]"][type="checkbox"]:checked').prop('checked', false);
        },
        /**
         * Check proportions
         * @returns {Boolean}
         */
        checkProportions: function () {
            var isAllChecked = true;
            $(this.targets.widget).find('[name="proportion[]"]').each(function () {
                if (!$(this).prop('checked'))
                    isAllChecked = false;
            });
            return isAllChecked;
        },
        clickViewType: function (e) {
            var prevType = $(app.targets.widget).find('.view-type.active').data('view');
            var type = $(this).data('view'), selectedFiles;

            if (prevType === type)
                return false;

            if (typeof FlWidgetMassEditImage === 'object') {
                // drop prevType checks
                $(app.targets.widget).find('[data-view-type="' + prevType + '"]').find('[name="file_id[]"]').prop('checked', false);
                // get checked and check it in new view type
                selectedFiles = FlWidgetMassEditImage.getEditFiles();
                for (var k in selectedFiles)
                    $(app.targets.widget).find('[data-view-type="' + type + '"]').find('[name="file_id[]"][value="' + selectedFiles[k] + '"]').prop('checked', true);
            }

            // set active class
            $(app.targets.widget).find('.view-type').removeClass('active');
            $(this).addClass('active');
            // change view
            $(app.targets.widget).find('[data-view-type]').hide();
            $(app.targets.widget).find('[data-view-type="' + type + '"]').show();
        },
        renderSection: function () {
            $(this.targets.content + '> div').hide();
            $(this.targets.widget + '__' + this.section).show();
        },
        publ: {
            setViewType: function (type) {
                app.viewType = type;
            }
        }
    };
    app.init();
    return app.publ;
}());
/**
 * Section storage in Widget Storage
 * @type Function|@exp;app@pro;publ
 */
var FlWidgetStorageStorage = (function () {

    var tpl = {
        /**
         * Current tpl driver
         * @param {string} tpl - current tpl
         * @return {function} (doT)
         */
        _driver: function (tpl) {
            return doT.template(tpl);
        },
        defaultType: 'checkbox',
        images: {
            type: 'checkbox',
            /**
             * tpl item of list
             * @return {function} (doT)
             */
            listItem: function (d) {
                return tpl._driver(
                        '<tr class="js-ws-list-item">'
                        + '<td style="width:20px"><input type="' + this.type + '" name="file_id[]" value="{{=it.file_id}}"></td>'
                        + '<td style="width:100px"><img src="/images/thumbs/{{=it.file_name}}" class="thumbnail"></td>'
                        + '<td><a href="/admin/storage/card/{{=it.name}}">{{=it.original_name}}</a></td>'
                        + '<td>{{=it.created}}</td>'
                        + '</tr>')(d);
            },
            /**
             * tpl item of tile
             * @return {function} (doT)
             */
            tileItem: function (d) {
                return tpl._driver(
                        '<li class="js-ws-list-item">'
                        + '<a href="/admin/storage/card/{{=it.name}}" target="_blank"><img src="/images/thumbs/{{=it.file_name}}" class="thumbnail"></a>'
                        + '<label class="tile_galery__label"><input type="' + this.type + '" name="file_id[]" value="{{=it.file_id}}">{{=it.original_name}}</label>'
                        + '</li>')(d);
            }
        },
        docs: {
            type: 'checkbox',
            /**
             * tpl item of list
             * @return {function} (doT)
             */
            listItem: function (d) {
                return tpl._driver(
                        '<tr>'
                        + '<td style="width:20px"><input type="' + this.type + '" name="file_id[]" value="{{=it.file_id}}"></td>'
                        + '<td style="width:100px">'
                        + '<div class="file_icon">'
                        + '<i class="icon-ext icon-ext-{{=it.ext}}"></i>'
                        + '</div>'
                        + '</td>'
                        + '<td><a href="/admin/storage/card/{{=it.name}}" target="_blank">{{=it.original_name}}</a></td>'
                        + '<td>{{=it.created}}</td>'
                        + '</tr>')(d);
            },
            /**
             * tpl item of tile
             * @return {function} (doT)
             */
            tileItem: function (d) {
                return tpl._driver(
                        '<li>'
                        + '<div class="file_icon"><a href="/admin/storage/card/{{=it.name}}" target="_blank">'
                        + '<i class="icon-ext icon-ext-{{=it.ext}}"></i><?a>'
                        + '</div>'
                        + '<label class="tile_galery__label"><input type="' + this.type + '" name="file_id[]" value="{{=it.file_id}}">{{=it.original_name}}</label>'
                        + '</li>')(d);
            }
        }
    };

    var Model = function (name) {

        this.name = name;
        this._data = {};
        this.setData = function (hash, data) {
            if (typeof hash !== 'string' || typeof this._data !== 'object')
                return false;

            this._data[hash] = data;
            return  true;
        };
        this.getData = function (hash) {
            return this._data[hash];
        };
    };

    var app = {
        extends: {},
        limit: 25,
        page: 1,
        section: '',
        sectionTypes: ['images', 'docs'],
        models: {},
        viewType: 'tile',
        lastHash: '',
        targets: {
            widget: '.js-ws',
            alert: '.js-ws-alert',
            loader: '.js-ws-loader',
            nav: '.js-ws-nav',
            navModTab: '.ws-nav-pills'
        },
        init: function () {

            for (var k in this.sectionTypes) {
                this.models[this.sectionTypes[k]] = new Model(this.sectionTypes[k]);
            }

            app.setUpListeners();
            // select default section
            $(this.targets.nav + ' .active a').each(function () {                
                app.clickTabNav.call($(this));
            });


            FlFilter.extends.submit(this.loadData);
            FlFilter.extends.drop(this.loadData);

            // ext tab panel nav
            $(this.targets.nav).find('a').on('click', app.clickTabNav);

            // подписываемся на событие Загрузка файла - если редактор не был показан, показываем
            $.subscribe('FlUpload.uploadSuccess', function (e) {

                $('[data-tab-content="mass_editor"]').show();
            });
            // подписываемся на событие отмена загрузки файла произошла
            $.subscribe('FlUpload.uploadAbort', function (e) {
                // если все удалено, возвращаем загрузчик и прячем массовый редактор
                if (!$('.upload_more .uploaded_list tbody tr').length) {
                    // редактор
                    $('[data-tab-content="mass_editor"]').hide();
                    // 
                    $('.upload_more').hide();
                    $('.upload_more').siblings('.upload-box').show();
                }

            });
        },
        setUpListeners: function () {

            $('.js-ws-view-type').off('click').on('click', app.clickViewType);
            $('.pagination-ajax a').off('click').on('click', app.paginationClick);
            $(this.targets.widget).find('[name="pagination_limit"]').off('change').on('change', app.changeLimit);


        },
        clickViewType: function (e) {
            if ($.inArray($(this).data('view'), ['tile', 'list']) === -1)
                return;

            $(this).parents('ul').find('.active').removeClass('active');
            $(this).addClass('active');

            app.viewType = $(this).data('view');
            FlWidgetStorage.setViewType(app.viewType);
            app.render($(this).parents(app.targets.widget));
        },
        clickTabNav: function (e, data) {

            var isLoad = app.section !== $(this).data('section') || (typeof data === 'object' && data.force_load_data);

            $(app.targets.nav).find('li').removeClass('active');
            $(this).parent('li').addClass('active');

            app.section = $(this).data('section');
            app.navModTabInit($(this).parents(app.targets.widget));

            if (isLoad) {

                if ($.inArray(app.section, app.sectionTypes) === -1) {
                    console.log('incorrect section type!');
                    console.log(app.section);
                    return false;
                }

                app.loadData($(this).parents(app.targets.widget));
            }
        },
        navModTabInit: function (widget) {

            function _render() {
                $(widget).find(app.targets.navModTab).find('[data-tab]').each(function () {
                    if ($(this).parents('li').hasClass('active'))
                        $(widget).find('[data-section-content="' + app.section + '"][data-tab-content="' + $(this).data('tab') + '"]').show();
                    else
                        $(widget).find('[data-section-content="' + app.section + '"][data-tab-content="' + $(this).data('tab') + '"]').hide();
                });
            }

            $(widget).find('[data-section-content]').hide();

            if (app.section === 'images' || app.section === 'docs') {
                $(widget).find(app.targets.navModTab).show();
                _render();

                $(widget).find(app.targets.navModTab).find('li').off('click').on('click', function (e) {

                    $(widget).find('[data-section-content]').hide();

                    if ($(this).hasClass('active'))
                        return false;

                    $(widget).find(app.targets.navModTab).find('li').removeClass('active');
                    $(this).addClass('active');

                    _render();
                });
            } else {
                $(widget).find(app.targets.navModTab).hide();
            }


        },
        changeLimit: function (e) {
            app.limit = $(this).val();
            app.loadData();
        },
        renderPagination: function (config, target) {
            var target = target === undefined ? $(app.targets.widget).find('.pagination-ajax') : $(target).find('.pagination-ajax');

            if (config === false) {
                target.empty();
                return false;
            }
            if (config === undefined)
                config = {total: FlRegister.get('total_storage')};

            target.empty().append(FlPagination.render(config));
            this.setUpListeners();
        },
        paginationClick: function (e) {
            app.page = $(this).parent('li').data('pg');
            app.loadData($(this).parents(app.targets.widget));
        },
        loadData: function (target) {
            var url = location.protocol + '//' + location.hostname + '/ajax/storage/',
                    request = {}, hash,
                    target = target === undefined ? $(app.targets.widget) : $(target);


            if (!!target && target.hasClass('fl-filter'))
                request = FlForm.getFormData(target);

            request.alias = app.section;
            request.page = app.page;
            request.limit = app.limit;

            hash = request.alias + '_' + request.page + '_' + request.limit;
            app.lastHash = hash;
            app.alert();
            $(app.targets.widget).find(app.targets.loader).show();

            $.getJSON(url, request, function (response) {

                $(app.targets.widget).find(app.targets.loader).hide();

                if (response.success) {
                    app.models[app.section].setData(hash, response.data);
                    app.render(target);
                } else {
                    //@todo
                    app.models[app.section].setData(hash, response.data);
                    app.render(target);
                }

                if (typeof FlWidgetMassEditImage !== 'undefined')
                    FlWidgetMassEditImage.refreshEventListener();

            }).error(function (er) {
                alert('Что-то пошло не так!');
            });
        },
        alert: function (message) {
            var a = $(this.targets.widget).find(this.targets.alert);

            if (typeof message === 'string') {
                a.text(message);
                a.show(200);
            } else {
                a.text('');
                a.hide();
            }


        },
        render: function (target) {
            
            console.log(target);
            
            if(!$(target).hasClass((app.targets.widget).split('.').join('')))
                target = $(target).parents(app.targets.widget);
            
            console.log(target);
            
            var hash = this.lastHash,
                    d = this.models[this.section].getData(hash),
                    s = target.find('[data-tab-content="' + app.section + '"]'),
                    v = s.find('[data-view-type="' + this.viewType + '"]'),
                    p = v.find('.js-ws-it-place').length ? v.find('.js-ws-it-place') : v;

                    console.log(s);
                    console.log(v);

            if (!p.length) {
                console.log('View place not found!');
                console.log(app);
                console.log(v);
                this.alert('Не найдено место для отображения');
                return;
            }

            if (!d || typeof d.files !== 'object') {
                console.log('no data!');
                console.log(app);
                this.alert('Ничего не найдено');
                return;
            }

            s.find('[name="check_all_files"]').prop('checked', false);
            s.find('[data-view-type]').hide();

            // clear place
            p.empty();

            if (typeof tpl[this.section] === 'object') {
                // определяем тип инпутов по атрибуту виджета data-{section}-input-type
                console.log('input-type');
                console.log($(p).parents(app.targets.widget).data(this.section + '-input-type'));
                
                tpl[this.section].type = $(p).parents(app.targets.widget).data(this.section + '-input-type') || tpl.defaultType;

                for (var k in d.files) {
                    $(p).append(tpl[this.section][this.viewType + 'Item'](d.files[k]));
                }
            }



            v.show();
            this.renderPagination(d.pagination, s);
        },
        view: {
            renderTile: function (data, target) {
                var target = target === undefined ? $(app.targets.widget).find('.storage_tile') : $(target).find('.storage_tile');
//                target = $(app.targets.widget).find('.storage_tile');
                target.empty();
                var tpl = doT.template($(app.targets.tpl_tile_item).html()), el;
                var tpl_docs = doT.template($(app.targets.tpl_tile_item + '_docs').html());
                for (var k in data) {
                    el = $(tpl(data[k]));

                    if (data[k].file_type_alias === 'docs')
                        el = $(tpl_docs(data[k]));

                    target.append(el);
                }
                // call extends
                if (typeof app.extends.renderTile === 'function')
                    app.extends.renderTile();
            },
            renderList: function (data) {
                var target = $(app.targets.widget).find('.storage_list tbody'), tpl;
                target.empty();
                var tpl = doT.template($(app.targets.tpl_list_item).html()), el;
                var tpl_docs = doT.template($(app.targets.tpl_list_item + '_docs').html());
                for (var k in data) {
                    el = $(tpl(data[k]));

                    if (data[k].file_type_alias === 'docs')
                        el = $(tpl_docs(data[k]));

                    target.append(el);
                }
            }
        },
        publ: {
            extends: {
                renderTile: function (callback) {
                    if (typeof callback === 'function')
                        app.extends.renderTile = callback;
                }
            },
            renderTile: function (data, target) {
                app.view.renderTile(data, target);
            }
        }
    };
    app.init();
    return app.publ;
}());

