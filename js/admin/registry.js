;
(function () {
    var app = {
        targets: {
            delete: '.d_it'
        },
        init: function () {
            this.setUpListeners();
        },
        setUpListeners: function () {
            $(this.targets.delete).off('click').on('click', app.delete);
        },
        delete: function (e) {
            var rel = Number($(this).parents('tr').find('[data-col="objects_relations"]').text().trim()),
                    self = this,
                    n = $(this).data('name'),
                    msg = !isNaN(rel) && !!rel ? 'Внимание! Элемент "' + n + '" имеет связи с объектами, при удалении "' + n + '" связи будут безвозвратно утеряны. Продолжить?' :
                    'Вы действительно хотите удалить "' + n + '"';

            if (confirm(msg)) {
                $.post(location.protocol + '//' + location.hostname + '/admin/registry/delete/', {registry_id: $(this).data('id')}, function (response) {
                    if (response.success) {
                        $(self).parents('tr').remove();
                    } else {
                        if (typeof response.error === 'string')
                            alert(response.error);
                        else
                            alert('Извините. Не удалось удалить "'+ n +'".');
                    }
                }, 'json');
            }

        },
        publ: {
        }
    };
    app.init();
}());