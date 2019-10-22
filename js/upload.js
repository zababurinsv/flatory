var FlUpload = (function () {
    var app = {
        extends: {},
        type: 'mass', // or simple
        access: {
            type: [
                "image/.*",
                "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet", // xlsx
                "application/vnd.openxmlformats-officedocument.wordprocessingml.document", // docx
                "application/vnd.ms-excel", // xls , csv
                "application/msword", // doc
                "application/pdf" // pdf
            ]
        },
        files: [],
        notValid: [],
        fileSelected: {},
        isUpload: false,
        init: function () {
            app.setUpListeners();
        },
        setUpListeners: function () {
            $('[type="file"]').off('change').on('change', app.selectFile);
            // abort upload
            $('.abort_upload').off('click').on('click', app.uploadAbort);
        },
        // check access for current uploading file
        isAccess: function (e) {
            if (e.target.files.length === 0)
                return  false;

            var files = e.target.files, type, name, isValid;
            app.notValid = [];

            for (var k in files) {

                if (typeof files[k] !== 'object')
                    continue;

                type = files[k].type;
                name = FlHelper.stripTags(files[k].name);
                isValid = false;
                for (var rule in app.access.type) {
                    if (type.match(app.access.type[rule]) !== null)
                        isValid = true;
                }
                if (!isValid)
                    app.notValid.push(name);
            }

            return !app.notValid.length ? true : false;
        },
        selectFile: function (e) {
            $('.upload_type_error').remove();
            // check access
            if (!app.isAccess(e)) {
                app.errorType(app.notValid);
                return false;
            }

            app.type = $(this).hasClass('simple_upload') ? 'simple' : 'mass';

            if (app.type === 'mass') {
                $('.upload-box').hide();
                $('.upload_more').show();
            } else {
                $(this).siblings('.loader').show();
            }


            app.files = e.target.files;
            // перебираем все файлы и загружаем по одному
            for (var k in app.files) {
                if (typeof app.files[k] === 'object')
                    app.createFile(app.files[k], $(this));
            }


        },
        // create video & get video_id
        createFile: function (file, target) {
            var url = 'http://' + window.location.host + '/admin/upload/create/';
            var callbacks = {target: target};
            var currentFile = {};
            // name of file
            var name = file.name.split('.');
            name.pop();
            currentFile.name = name.join('');
            currentFile.type = file.type;
            currentFile.size = file.size;

            if (app.type === 'mass') {
                $('.upload-box').hide();
                $('.upload_more').show();
                callbacks.success = app.uploadSuccess;
                callbacks.progress = app.uploadProgress;
            } else {
                $(this).siblings('.loader').show();
                callbacks.success = app.simple.uploadSuccess;
            }

            $.post(url, currentFile, function (data) {

                // check success
                if (data.success === false) {
                    return false;
                }
                file.uploadid = data.data.name;
                if (app.type === 'mass') {
                    // отображаем процесс загрузки текущего файла
                    var uploadedRow = doT.template($('#tpl__upload_file').html());
                    uploadedRow = uploadedRow(data.data);
                    $('.uploaded_list tbody').append(uploadedRow);
                }
                // upload !
                app.uploadFile(file, callbacks);


            }, "json");
        },
        /**
         * Upload file
         * @param {object} currentFile
         * @param {object} callbacks - {success: function, progress: function}
         * @returns {undefined}
         */
        uploadFile: function (currentFile, callbacks) {
            var uploader = new FileUploader({
                message_error: 'Ошибка при загрузке файла',
                form: 'uploadform',
                formfiles: 'files',
                uploadid: currentFile.uploadid,
                uploadscript: '/admin/upload/process',
                callback_success: callbacks.success,
                callback_abort: '',
                portion: 1024 * 1024 * 2,
                file_selected: currentFile, // curent file
                callback_progress: callbacks.progress, // callback for progress render
                target_place: callbacks.target
            });

            // @todo call methodds! delete alert!!!
            if (!uploader)
                alert('Uploader not working');
            else {
                if (!uploader.CheckBrowser())
                    alert('Uploader: Browser not access');
                else {
                    var e = document.getElementById('uploadform');
                    if (e)
                        e.style.display = 'block';

                }
            }
        },
        // callback for progress render
        uploadProgress: function (progress, config) {
            var row = $('[data-file="' + config.uploadid + '"]');
            var progressBar = row.find('.progress-bar');
            $(progressBar).attr('aria-valuenow', progress).width(progress + '%');
            $(progressBar).find('span').html(progress + '%');
        },
        // uploade complite!!
        uploadSuccess: function (evt, config) {
            app.isUpload = true;
            // get preview
            var data = JSON.parse(evt.target.responseText);

            if (data.success) {
                var file = data.data, tpl;
                file.src_preview = (file.file_type_alias !== 'images') ? 'document-icon.png' : 'thumbs/' + file.file_name;
                tpl = doT.template($('#tpl__upload_file_done').html());
                tpl = tpl(file);

                $('[data-file="' + file.name + '"]').html(tpl);
                $.publish('FlUpload.uploadSuccess');
                // refresh listeners
                FlWidgetMassEditImage.refreshEventListener();
                app.setUpListeners();

            }
        },
        // Abort upload by user
        uploadAbort: function (e) {
            var fileId, fileOriginalName, url, self;
            fileId = $(this).data('fid');
            fileOriginalName = $(this).data('fon');
            self = this;

            if (confirm('Вы уверены что хотите отменить загрузку файла ' + fileOriginalName + '?')) {
                url = location.protocol + '//' + location.hostname + '/admin/upload/delete/';
                $.post(url, {fid: fileId}, function (data) {
                    if (data.success) {
                        if (app.type === 'mass') {
                            $(self).parents('tr').remove();
                        } else {
                            $(self).parents('.image_simple_upload').find('.uploaded_image').attr('src', '/images/no_photo.jpg');
                            $(self).parents('.image_simple_upload').find('[name="file_id"]').val('');
                            $(self).hide();
                        }

                        // объявляем событие - отмена загрузки файла произошла
                        $.publish('FlUpload.uploadAbort');

                        if (typeof app.extends.uploadAbortSuccess === 'function')
                            app.extends.uploadAbortSuccess(fileId);
                    } else {
                        alert(data.error);
                    }
                }, 'json');
            }

        },
        /**
         * Render error type
         * @todo error view
         * @param {array} files - list of files
         * @returns {Boolean}
         */
        errorType: function (files) {
            $('.upload-box form').append($('<label>', {
                class: 'text-danger upload_type_error',
                text: 'Извините, типы некоторых файлов мы не принимаем. Некорректные файлы: ' + files.join(', ')
            }));
            return false;
        },
        simple: {
            uploadSuccess: function (evt, config) {
                app.isUpload = true;
                // get preview
                var data = JSON.parse(evt.target.responseText);
                if (data.success) {
                    var file = data.data, form;
                    file.src_preview = (file.file_type_alias !== 'images') ? 'document-icon.png' : 'original/' + file.file_name;
                    form = $(config.target_place).parents('.image_simple_upload');

                    // show btn abort
                    form.find('.abort_upload').data('fid', file.file_id);
                    form.find('.abort_upload').data('fon', file.original_name);
                    form.find('.abort_upload').show();

                    form.find('.uploaded_image').attr('src', '/images/' + file.src_preview);
                    form.find('[name="file_id"]').val(file.file_id);
                    form.find('.loader').hide();
                }
            }
        },
        publ: {
            getFile: function () {
                return app.file;
            },
            getFileSelected: function () {
                return app.fileSelected;
            },
            extends: {
                uploadAbortSuccess: function (callback) {
                    if (typeof callback === 'function')
                        app.extends.uploadAbortSuccess = callback;
                },
                get: function (method) {
                    if (method === undefined)
                        return app.extends;
                }
            },
            /**
             * set access by type
             * @param {string/object} type - images|docs (list of types)
             * @return {undefined}
             */
            setAccessByType: function (type) {

                var rules = [];

                function _defineRules(t) {
                    switch (t) {
                        case 'images':
                            rules = rules.concat(["image/.*"]);
                            break;
                        case 'docs' :
                            rules = rules.concat([
                                "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet", // xlsx
                                "application/vnd.openxmlformats-officedocument.wordprocessingml.document", // docx
                                "application/vnd.ms-excel", // xls , csv
                                "application/msword", // doc
                                "application/pdf" // pdf
                            ]);
                            break;
                    }
                }

                if (typeof type === 'string') {
                    _defineRules(type);
                } else {
                    if (typeof type === 'object') {
                        for (var k in type) {
                            _defineRules(type[k]);
                        }
                    }
                }

                app.access.type = rules;

            },
            debug: function () {
                return  app;
            }
        }
    };
    app.init();
    return app.publ;
}());