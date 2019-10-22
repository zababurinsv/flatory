<script src="/js/ckeditor/ckeditor.js" type="text/javascript"></script>
<script src="/js/ckeditor/config.js" type="text/javascript"></script>
<script src="/js/ckeditor/styles.js" type="text/javascript"></script>
<script><?= isset($errors) ? "$(document).ready( function() { showErrorMsg('" . $errors . "') })" : '' ?></script>

<form class="form-horizontal" role="form" method="POST" action="/admin/objects/general_info/<?= (isset($object)) ? $object['id'] : '' ?>" >
    <div class="tab-pane active" id="tab1">
        <br/>
        <div class="row">
            <div class="form-group">
                <label for="name" class="col-sm-2 control-label">Название объекта *</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="name" oninput="$('#alias').val(FlDashboardForm.prepareAlias($(this).val().translit()))" placeholder="" name="name" value="<?= (isset($object)) ? htmlspecialchars($object['name'], ENT_QUOTES) : '' ?>" />
                </div>
            </div>
            <div class="form-group">
                <label for="alias" class="col-sm-2 control-label">Алиас</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="alias" placeholder="" name="alias" value="<?= (isset($object)) ? $object['alias'] : '' ?>" />
                </div>
            </div>
            <div class="form-group">
                <label for="adres" class="col-sm-2 control-label">Адрес *</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="adres" placeholder="" value="<?= (isset($object)) ? $object['adres'] : '' ?>" name="adres" />
                </div>
            </div>
            <div class="form-group">
                <label for="description" class="col-sm-2 control-label">Описание</label>
                <div class="col-sm-10">
                    <textarea class="form-control ckeditor" id="description" name="description" rows="3"><?= (isset($object)) ? $object['description'] : '' ?></textarea>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-2 control-label">Фото</label>
                <div class="col-sm-10">
                    <div class="panel panel-default space_none">
                        <div class="panel-body"><?= $view_images ?></div>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <div class="col-sm-12 col-sm-offset-2">
                    <input type="submit" class="btn btn-success" onclick="if (save_general()) {return true;} else {return false;}" value="Сохранить"/>
                </div>
            </div>
        </div>
    </div>
</form>
<script>
    String.prototype.translit = (function() {
        var L = {
            'A': 'A', 'B': 'B', 'C': 'C', 'D': 'D', 'E': 'E', 'F': 'F', 'G': 'G', 'H': 'H',
            'I': 'I', 'J': 'J', 'K': 'K', 'L': 'L', 'M': 'M', 'N': 'N', 'O': 'O', 'P': 'P',
            'Q': 'Q', 'R': 'R', 'S': 'S', 'T': 'T', 'U': 'U', 'V': 'V', 'W': 'W', 'X': 'X',
            'Y': 'Y', 'Z': 'Z', 'a': 'a', 'b': 'b', 'c': 'c', 'd': 'd', 'e': 'e', 'f': 'f',
            'g': 'g', 'h': 'h', 'i': 'i', 'j': 'j', 'k': 'k', 'l': 'l', 'm': 'm', 'n': 'n',
            'o': 'o', 'p': 'p', 'q': 'q', 'r': 'r', 's': 's', 't': 't', 'u': 'u', 'v': 'v',
            'w': 'w', 'x': 'x', 'y': 'y', 'z': 'z',
            'А': 'a', 'а': 'a', 'Б': 'b', 'б': 'b', 'В': 'v', 'в': 'v', 'Г': 'g', 'г': 'g',
            'Д': 'd', 'д': 'd', 'Е': 'e', 'е': 'e', 'Ё': 'yo', 'ё': 'yo', 'Ж': 'zh', 'ж': 'zh',
            'З': 'z', 'з': 'z', 'И': 'i', 'и': 'i', 'Й': 'j', 'й': 'j', 'К': 'k', 'к': 'k',
            'Л': 'l', 'л': 'l', 'М': 'm', 'м': 'm', 'Н': 'n', 'н': 'n', 'О': 'o', 'о': 'o',
            'П': 'p', 'п': 'p', 'Р': 'r', 'р': 'r', 'С': 's', 'с': 's', 'Т': 't', 'т': 't',
            'У': 'u', 'у': 'u', 'Ф': 'f', 'ф': 'f', 'Х': 'kh', 'х': 'kh', 'Ц': 'c', 'ц': 'c',
            'Ч': 'ch', 'ч': 'ch', 'Ш': 'sh', 'ш': 'sh', 'Щ': 'shch', 'щ': 'shch', 'Ъ': '', 'ъ': '',
            'Ы': 'y', 'ы': 'y', 'Ь': "", 'ь': "", 'Э': 'eh', 'э': 'eh', 'Ю': 'yu', 'ю': 'yu',
            'Я': 'ya', 'я': 'ya', ' ': '-',
            "-": "-", "—": "-", "(": "", ")": "", "«": "",
            "»": "", ",": "", "%": "", ".": "", "/": "", "\'": "",
            "*": "", "?": "", "&": "", "^": "", ":": "", ";": "", "#": "",
            "<": "", ">": ""
        },
        r = '',
                k;
        for (k in L)
            r += k;
        r = new RegExp('[' + r + ']', 'g');
        k = function(a) {
            return a in L ? L[a] : a;
        };
        return function() {
            return this.replace(r, k);
        };
    })();
</script>
