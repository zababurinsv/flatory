CKEDITOR.plugins.add('flimage', {
    icons: 'flimage',
    init: function(editor) {

        // add comand
        editor.addCommand('flimage', new CKEDITOR.dialogCommand('flimageDialog'));
        // add button
        editor.ui.addButton('Abbr', {
            label: 'Вставка изображения по коду',
            command: 'flimage',
            icon: this.path+'icons/flimage.png',
            toolbar: 'insert,100'
        });
        
        CKEDITOR.dialog.add( 'flimageDialog', this.path + 'dialogs/flimage.js' );
    }
});