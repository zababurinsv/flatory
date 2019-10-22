CKEDITOR.dialog.add('flimageDialog', function(editor) {
    return {
        title: 'Вставка изображения по коду',
        minWidth: 300,
        minHeight: 200,
        contents: [
            {
                id: 'tab-basic',
                label: 'First Tab',
                title: 'album',
                elements: [
                    {
                        type: 'html',
                        id: 'image_code_exzmple',
                        html: '<b>Пример кода изображения</b><br><i>{"file_id":11,"proportions":[1,2],"file_category_id":2,"parent_id":1}</i>'
                    },
                    {
                        type: 'text',
                        label: 'Код изображения',
                        id: 'image_code',
                        validate: CKEDITOR.dialog.validate.notEmpty("Неверный код.")
                    }
                ]
            }
        ],
        onOk: function() {
            var dialog = this, url, code, error, el;
            code = dialog.getValueOf('tab-basic', 'image_code');

            try {
                code = JSON.parse(code);
            } catch (e) {
                alert('Неверный код.');
                return false;
            }

            // check file involve
            url = location.protocol + '//' + location.hostname + '/admin/ajax/flimage';
            $.post(url, code, function(response) {
                if (response.success) {
                    el = response.data.flimage;
                    editor.insertHtml( el );
                } else {
                    error = response.error ? response.error : 'Не удалось получить информацию по изображению.';
                    alert(error);
                    return false;
                }
            }, 'json');
        }
    };
});