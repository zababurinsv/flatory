var Objectcard = (function () {
    var app = {
        init: function () {
            this.setUpListeners();
        },
        setUpListeners: function () {
            $('[data-object-action]').off('click').on('click', app.doAction);
            $('#form-objecct-sections').off('submit').on('submit', app.submitObjectSections);
            $('#init_widget_storage').on('click', function (e) {
                $('html, body').animate({
                    scrollTop: $(".js-ws").offset().top - $('#objectcard-header').height() - 165
                }, 500);
            });

            $(window).off('scroll').on('scroll', function (e) {                
                if($(document).scrollTop() > 1)
                    $('#objectcard-header').css({'border-bottom': '1px solid #e4e5e7'});
                else
                    $('#objectcard-header').css({'border-bottom': 'none'});
            });
        },
        /**
         * do some action
         * @param {object} e - event
         * @returns {undefined}
         */
        doAction: function (e) {
            var action = $(this).data('object-action');
            if (typeof app.actions[action] === 'function')
                app.actions[action].call(this, e);
            else
                console.log('Action: ' + action + ' not found!');
        },
        actions: {
            open_modal_sections: function (e) {
                $('#modal-object-sections').modal();
            },
            save_page_form: function (e) {
                $('#object-form').submit();
            },
            save_modal_form: function (e) {
                $(this).parents('.modal').find('form').submit();
            }
        },
        submitObjectSections: function (e) {
            e.preventDefault();

            var url = '/admin/ajax/object_sections', d = FlForm.getFormData($(this));

            $.post(url, d, function (response) {
                var msg;
                if (response.success) {
                    if (confirm('Успешно изменено! Обновим страницу?'))
                        location.reload();
                } else {
                    if (typeof response.errors === 'object') {
                        msg = 'Ошибка! ';
                        for (var k in response.errors) {
                            msg += response.errors[k] + '\n';
                        }
                        alert(msg);
                    } else {
                        alert('Что-то пошло не так!');
                    }
                }
            }, 'json').error(function (er) {
                alert('Что-то пошло не так!');
            });


            return false;
        },
        publ: {

        }
    };
    app.init();
    return app.publ;
}());