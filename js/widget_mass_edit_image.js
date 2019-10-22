var FlWidgetMassEditImage = (function() {
    var app = {
        changeable: 1,
        addition: 2,
        editFiles: [],
        targets: {
            widget: '.mass_edit_image',
            widgetHead: '.mass_edit_image .panel-heading',
            widgetBody: '.mass_edit_image .panel-body',
            file: '[name="file_id[]"]',
            editCounter: '.mass_edit_image__count',
            applyChange: '.mass_edit_image__apply',
            dropForm: '.mass_edit_image__drop',
            form: '.mass_edit_image form',
            checkAllFiles: '[name="check_all_files"]'
        },
        init: function() {
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
            // listeners
            app.setUpListeners();
        },
        setUpListeners: function() {
            // add file in mass edit
            $(this.targets.file).off('change').on('change', app.checkFile);
            // check all
            $(this.targets.checkAllFiles).off('change').on('change', app.checkAll);
            // apply change
            $(this.targets.applyChange).off('click').on('click', app.applyChange);
            // drop form
            $(this.targets.dropForm).off('click').on('click', app.dropForm);
        },
        dropForm: function (e){
            $(app.targets.widget).find('textarea').val('');
            $(app.targets.widget).find('[type="radio"][value="0"]').prop('checked', true);
            $('.methodTags').tagit('removeAll');
        },
        /**
         * Check files
         * @param {object} e
         * @returns {undefined}
         */
        checkFile: function(e) {
            app.editFiles = [];
            $(app.targets.file + ':checked').each(function(a, b) {
                app.editFiles.push($(this).val());
            });
            app.view.updateCountEditfiles();
        },
        /**
         * Check all
         * @param {object} e
         * @returns {undefined}
         */
        checkAll: function(e) {
            var table = $(this).parents('table'), dublView;
            if (table.length === 0)
                table = $('.storage_tile');

            if (table.length === 0)
                table = $('.storage_list');
            dublView = table.siblings('.storage_tile');

            var self = this;
            table.find('[name="file_id[]"]:checkbox').each(function() {
                $(this).prop('checked', $(self).prop('checked'));
                $(this).trigger('change');
            });
        },
        /**
         * Apply changes
         * @param {object} e
         * @returns {undefined}
         */
        applyChange: function(e, successCallback, errorCallback) {
            var data = app.getWidgetData(), url, msg = '';
            data.files = app.editFiles;
            url = location.protocol + '//' + location.hostname + '/admin/storage/mass_edit/';
            FlDashboardForm.toggleLoader();
            $.post(url, data, function(response) {

                FlDashboardForm.toggleLoader();
                
                if (response.success) {
                    msg = 'Файлы успешно изменены!';
                    if (response.data.changed_files)
                        msg += ' Изменено файлов: ' + response.data.changed_files + '.'
                    if (response.data.changed_file_proportions)
                        msg += ' Изменено пропорций изображений: ' + response.data.changed_file_proportions + '.'
                    FlDashboardForm.toggleGlobalMessage(msg);
                    setTimeout(function() {
                        // refresh
                        FlDashboardForm.toggleGlobalMessage(msg);
                    }, 2000);
                    // some actions
                    if (typeof successCallback === 'function')
                        successCallback(response);
                } else {
                    for (var k in response.errors)
                        msg += ' ' + response.errors[k];
                    msg = !msg ? 'Что-то пошло не так.' : msg;

                    FlDashboardForm.toggleGlobalMessage(msg, FlDashboardForm.globalMessageTypes.danger);
                    setTimeout(function() {
                        // refresh
                        FlDashboardForm.toggleGlobalMessage(msg, FlDashboardForm.globalMessageTypes.danger);
                    }, 2000);
                    // some actions
                    if (typeof errorCallback === 'function')
                        errorCallback(response);
                }
            }, 'json');
        },
        /**
         * Get widget data
         * @returns {_L1.app.getWidgetData.data}
         */
        getWidgetData: function() {
            var prop, widget = $(app.targets.widget);
            var data = {
                proportions: []
            };
            // check is editable field
            if (Number(widget.find('[name="alt_edit"]:checked').val()) === app.changeable)
                data.alt = $(app.targets.widget).find('[name="alt"]').val();
            if (Number(widget.find('[name="description_edit"]:checked').val()) === app.changeable)
                data.description = $(app.targets.widget).find('[name="description"]').val();
            if (Number(widget.find('[name="tags_edit"]:checked').val()) === app.changeable)
                data.tags = $(app.targets.widget).find('[name="tags"]').val();
            if (Number(widget.find('[name="tags_edit"]:checked').val()) === app.addition)
                data.tags_add = $(app.targets.widget).find('[name="tags"]').val();
            // add proportions
            $('[name="proportion[]"]').each(function() {
                if ($(this).prop('checked')) {
                    prop = {
                        proportion_id: $(this).val(),
                        is_watermark: $(this).parents('tr').find('[name="is_watermark"]').prop('checked') ? 1 : 0
                    }
                    data.proportions.push(prop);
                }
            });
            return data;
        },
        view: {
            /**
             * update view count edit files
             * @returns {undefined}
             */
            updateCountEditfiles: function() {
                $(app.targets.editCounter).text(app.editFiles.length);
            }
        },
        publ: {
            getEditFiles: function() {
                return app.editFiles;
            },
            /**
             * Refresh this event listener
             * @returns {undefined}
             */
            refreshEventListener: function() {
                app.setUpListeners();
            },
            /**
             * Set proportions manualy
             * @param {object} data
             * @returns {undefined}
             */
            setProportions: function(data) {
                // @todo
            },
            /**
             * hide apply btn
             * @returns {_L1.app.applyChange|app.applyChange} - apply callback
             */
            hideApply: function() {
                $(app.targets.applyChange).hide();
                return app.applyChange;
            },
            /**
             * Drop all chacked files
             * @returns {undefined}
             */
            dropAllCheckFiles: function() {
                $(app.targets.file).prop('checked', false);
                $(app.targets.checkAllFiles).prop('checked', false);
                app.editFiles = [];
                app.view.updateCountEditfiles();
            }
        }
    };
    app.init();
    return app.publ;
}());


