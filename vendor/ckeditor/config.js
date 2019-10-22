CKEDITOR.editorConfig = function (config) {
    config.toolbarGroups = [
        {name: 'clipboard', groups: ['clipboard', 'undo']},
        {name: 'forms', groups: ['forms']},
        {name: 'basicstyles', groups: ['basicstyles', 'cleanup']},
        {name: 'colors', groups: ['colors']},
        {name: 'styles', groups: ['styles']},
        {name: 'editing', groups: ['selection', 'spellchecker', 'find', 'editing']},
        {name: 'paragraph', groups: ['list', 'indent', 'blocks', 'align', 'bidi', 'paragraph']},
        {name: 'insert', groups: ['insert']},
        {name: 'links', groups: ['links']},
        {name: 'document', groups: ['document', 'mode', 'doctools']},
        {name: 'tools', groups: ['tools']},
        {name: 'others', groups: ['others']},
        {name: 'about', groups: ['about']}
    ];

    config.removeButtons = 'Save,NewPage,Print,Undo,Redo,Replace,Form,Checkbox,Radio,TextField,Textarea,Select,Button,ImageButton,HiddenField,Subscript,CreateDiv,JustifyLeft,JustifyCenter,JustifyRight,JustifyBlock,BidiLtr,BidiRtl,Language,Image,Flash,Smiley,PageBreak,ShowBlocks,About,Templates,Styles,Format,Font,FontSize,SelectAll,Find,BGColor,SpecialChar,Anchor';

    // Make dialogs simpler.
    config.removeDialogTabs = 'image:advanced;link:upload';
    // rm bottom toolbar
    config.removePlugins = 'elementspath';
    config.resize_enabled = false;
    config.height = 300;

    config.filebrowserUploadUrl = '/js/ckeditor/ckupload.php';
    config.protectedSource.push(/<script[\s\S]*?script>/g); /* script tags */
    config.allowedContent = true;
    config.pasteFromWordPromptCleanup = true;

    config.extraPlugins = 'flimage';
};
CKEDITOR.config.forcePasteAsPlainText = true;