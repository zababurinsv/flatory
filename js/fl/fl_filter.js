var FlFilter = (function () {
    var app = {
        extends: {},
        init: function () {
            this.setUpListeners();
            // configure datarangepicker
            var options = {
                singleDatePicker: true,
                startDate: moment(),
                format: 'DD.MM.YYYY',
                locale: {
                    firstDay: 1,
                    daysOfWeek: ['Вс', 'Пн', 'Вт', 'Ср', 'Чт', 'Пт', 'Сб'],
                    monthNames: ['Январь', 'Февраль', 'Март', 'Апрель', 'Май', 'Июнь', 'Июль', 'Август', 'Сентябрь', 'Октябрь', 'Ноябрь', 'Декабрь']
                }
            };
            // set datepicker
            $('[name="date_begin"]').daterangepicker(options);
            $('[name="date_end"]').daterangepicker(options);

            // set values
            this.setValues();
        },
        setUpListeners: function () {
            $('.fl-filter .drop-form').off('click').on('click', app.drop);
            $('.fl-filter .submit-form').off('click').on('click', app.submit);
            $('.fl-filter .toggle_more_filters').off('click').on('click', app.toggleMoreFilters);
            $('.fl-filter [name="image_width"]').off('change').on('change', app.changeImageWidth);
        },
        /**
         * set form values
         * @param {object} data - {field_name: value}
         * @param {string/$} target - wrapper for filter
         * @returns {undefined}
         */
        setValues: function (data, target) {
            var form, tags;
            form = target === undefined ? $('.fl-filter') : $(target).find('.fl-filter');
            data = typeof data !== 'object' ? FlHelper.GET() : data;

            for (var k in data) {
                switch (k) {
                    case 'search_type[]':
                        form.find('[name="search_type[]"][value="' + data[k] + '"]').prop('checked', 'checked');
                        break;
                    case 'status[]':
                        if (typeof data[k] === 'object') {
                            for (var sk in data[k])
                                form.find('[name="status[]"][value="' + data[k][sk] + '"]').prop('checked', 'checked');
                        } else {
                            form.find('[name="status[]"][value="' + data[k] + '"]').prop('checked', 'checked');
                        }
                        break;
                    case 'tags':
                        tags = data[k].split('|');
                        $.each(tags, function () {
                            form.find('[name="' + k + '"]').siblings('.methodTags').tagit("createTag", this.split('+').join(' '));
                        });
                        break;
                    case 'is_square':
                        form.find('[name="' + k + '"][value="' + data[k] + '"]').prop('checked', 'checked');
                        break;
                    default :
                        form.find('[name="' + k + '"]').val(typeof data[k] === 'string' ? data[k].split('+').join(' ') : data[k]);
                }
            }
        },
        /**
         * drop form
         * @param {object} e
         * @extends FlFilter.extends.drop('callback');
         * @returns {undefined}
         */
        drop: function (e) {
            var form = $(this).parents('.fl-filter');
            form.find(':input')
                    .not('[name="search_type[]"]')
                    .not('[name="organization_type_id[]"]')
                    .not('[type="checkbox"]')
                    .not('[type="radio"]')
                    .val('');
            form.find('[type="checkbox"],[type="radio"]').prop('checked', false);
            form.find('.methodTags').tagit("removeAll");
            form.find('[name="search_type[]"][value="and"]').prop('checked', 'checked');
            form.find('[name="organization_type_id[]"]').prop('checked', false);


            if (typeof app.extends.drop === 'function') {
                app.extends.drop.call(this);
                return false;
            }
            form.submit();
        },
        /**
         * submit form
         * @todo define call target
         * @param {object} e
         * @extends FlFilter.extends.submit('callback');
         * @returns {Boolean}
         */
        submit: function (e) {
            var form = $(this).parents('.fl-filter');

            if (typeof app.extends.submit === 'function') {
                e.preventDefault();
                app.extends.submit.call(this, form);
                return false;
            }

            form.attr('action', location.pathname);
            form.submit();
        },
        toggleMoreFilters: function (e) {
            $(this).parents('.fl-filter').find('.more_filters').toggle(300);
        },
        /**
         * Change filter image_width
         * auto select image_width_eq "="
         * @param {object} e - event
         * @returns {undefined}
         */
        changeImageWidth: function (e) {
            if ($(this).val() && !$('.fl-filter [name="image_width_eq"]').val()) {
                $('.fl-filter [name="image_width_eq"] [value="="]').attr('selected', 'selected');
            }
        },
        publ: {
            extends: {
                drop: function (callback) {
                    if (typeof callback === 'function')
                        app.extends.drop = callback;
                },
                submit: function (callback) {
                    if (typeof callback === 'function')
                        app.extends.submit = callback;
                }
            },
            setValues: function (data, target) {
                app.setValues(data, target);
            },
            submit: function () {
                $('.fl-filter .submit-form').trigger('click');
            },
            refreshEventListener: function () {
                app.setUpListeners();
            }
        }
    };
    app.init();
    return app.publ;
}());