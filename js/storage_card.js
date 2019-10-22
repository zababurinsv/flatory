var FlStorageCard = (function () {
    var app = {
        isChangeCard: false,
        card: {
            'original_name': '',
            'description': '',
            'alt': '',
            'tags': FlRegister.get('tags')
        },
        targets: {
            addSize: '#image_add_size',
            isWatermark: '[name="is_watermark"]',
            saveProportion: '.save_proportion',
            deleteProportion: '.delete_proportion'
        },
        init: function () {
            // init tags
            $('#methodTags').tagit({
                availableTags: FlRegister.get('tags'),
                fieldName: 'tags',
                caseSensitive: false,
                singleField: true,
                singleFieldDelimiter: '|',
                singleFieldNode: $('#mySingleFieldNode'),
                allowSpaces: true
            });
            // listeners
            app.setUpListeners();
        },
        setUpListeners: function () {
            for (var k in app.card)
                $('[name="' + k + '"]').off('change').on('change', app.changeCard);

            $(app.targets.addSize + ' a').off('click').on('click', app.addSize);
            $('#storage_card').off('submit').on('submit', app.saveCard);
            // change watermark
            $(app.targets.isWatermark).off('change').on('change', app.changeWatermark);
            // save proportion
            $(app.targets.saveProportion).off('click').on('click', app.saveProportion);
            // delete proportion
            $(app.targets.deleteProportion).off('click').on('click', app.deleteProportion);
            // delete current file
            $('.delete_item').off('click').on('click', app.deleteCard);

            // сохраняем форму из шапки
            $('[name="save_page_form"]').off('click').on('click', function (e) {
                $('form#storage_card').submit();
            });
            // показать / скрыть форму добавления ресайза (если ресайзы доступны)
            $('#toggle__image_add_size').off('click').on('click', function (e) {
                $('#image_add_size').toggle(300);
            });
        },
        /**
         * Delete proportion
         * @param {type} e
         * @returns {undefined}
         */
        deleteProportion: function (e) {
            var row = $(this).parents('tr'), url, data;
            data = {
                'proportion_id': row.data('proportion'),
                'file_id': $('[name="file_id"]').val()
            };
            url = location.protocol + '//' + location.hostname + '/admin/storage/del_resize/';
            // show loader
            app.publ.toggleLoader();
            $.post(url, data, function (data) {
                // hide loader
                app.publ.toggleLoader();
                if (data.success) {

                    // show success
                    app.publ.toggleGlobalMessage('Успешно удалено!');
                    setTimeout(function () {
                        // refresh
                        location.reload();
                    }, 2000);
                } else {
                    // show errors
                    if (data.error) {
                        app.publ.toggleGlobalMessage(data.error, app.publ.globalMessageTypes.danger);
                        setTimeout(function () {
                            // refresh
                            location.reload();
                        }, 3000);
                    }

                }
            }, 'json');
        },
        /**
         * Change watermark on proportion
         * enable / disable save proportion button
         * @param {object} e
         * @returns {undefined}
         */
        changeWatermark: function (e) {
            var row = $(this).parents('tr');
            var isWatermark = !!Number($(this).data('watermark'));
            if (isWatermark !== $(this).prop('checked'))
                row.find(app.targets.saveProportion).removeClass('disabled');
            else
                row.find(app.targets.saveProportion).addClass('disabled');
        },
        /**
         * Save proportion change
         * @param {object} e
         * @returns {undefined}
         */
        saveProportion: function (e) {
            var row = $(this).parents('tr');
            var data = {
                'proportion_id': row.data('proportion'),
                'file_id': $('[name="file_id"]').val()
            };
            if (row.find('[name="is_watermark"]').prop('checked'))
                data.is_watermark = 1;
            app.addSize({}, data);
        },
        /**
         * Ajax save card method
         * @param {object} e
         * @returns {Boolean}
         */
        saveCard: function (e) {
            e.preventDefault;
            var form = $(this), data, url;
            data = FlForm.getFormData(form);
            if (CKEDITOR.instances.description !== undefined)
                data.description = CKEDITOR.instances.description.getData();
            url = location.protocol + '//' + location.hostname + '/admin/storage/save_card/';
            // show loader
            app.publ.toggleLoader();
            $.post(url, data, function (data) {
                // hide loader
                app.publ.toggleLoader();
                if (data.success) {

                    // show success
                    app.publ.toggleGlobalMessage('Успешно сохранено!');
                    setTimeout(function () {
                        // refresh
                        location.reload();
                    }, 2000);
                } else {
                    // show errors
                    if (typeof data.errors === 'object' && !$.isEmptyObject(data.errors)) {
                        FlForm.errors(data.errors);
                    } else {
                        app.publ.toggleGlobalMessage('Сохранить не удалась, что-то пошло не так.', app.publ.globalMessageTypes.danger);
                        setTimeout(function () {
                            // refresh
                            location.reload();
                        }, 3000);
                    }

                }
            }, 'json');

            return false;
        },
        /**
         * Add size for image
         * @param {object} e
         * @param {object} data - form data
         * @returns {undefined}
         */
        addSize: function (e, data) {

            var form = $(this).parents(app.targets.addSize), url;
            if (data === undefined) {
                data = FlForm.getFormData(form);
            }

            url = location.protocol + '//' + location.hostname + '/admin/storage/add_resize/';
            if (app.notSaveWarning())
                $.post(url, data, function (data) {
                    if (data.success) {
                        // refresh
                        location.reload();
                    } else {
                        // @todo
                    }
                }, 'json');
        },
        /**
         * Not save warning
         * @returns {Boolean}
         */
        notSaveWarning: function () {
            if (app.isChangeCard)
                return confirm('Есть несохраненные данные. Хотите продолжить?');

            return true;
        },
        /**
         * Change card
         * @param {object} e
         * @returns {undefined}
         */
        changeCard: function (e) {
            app.isChangeCard = true;
        },
        /**
         * Delete card
         * @param {object} e
         * @returns {undefined}
         */
        deleteCard: function (e) {
            var fileId = $(this).data('file_id');
            if (!fileId)
                return false;
            if (confirm('Вы уверены? Файл будет удален безвозвратно.')) {
                var url = location.protocol + '//' + location.hostname + '/admin/ajax/storage_delete/';
                $.post(url, {file_id: fileId}, function (response) {
                    if (response.success) {
                        app.publ.toggleGlobalMessage('Файл успешно удален.', app.publ.globalMessageTypes.info);
                        location.href = '/admin/storage/';
                    } else {
                        app.publ.toggleGlobalMessage(FlHelper.arr.get(response, 'error', 'Что-то пошло не так. Не удалось удалить файл.'), app.publ.globalMessageTypes.danger);
                    }
                }, 'json');
            }
        },
        publ: {
            isChangeCard: function () {
                return app.isChangeCard;
            },
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
                            text: message
                        })
                    }));
                }
                if (message !== undefined)
                    $('.global__plug .global__message').text(message);
                $('.global__plug').toggle();
            }
        }
    };
    app.init();
    return app.publ;
}());