<script><?= isset($errors) ? "$(document).ready( function() { showErrorMsg('" . $errors . "') })" : '' ?></script>
<?php $object = isset($object) && is_array($object) ? $object : []; ?>
<div class="row">
    <div class="col-md-10">
        <form class="form space_bottom" role="form" method="POST" action="/admin/objects/general_info/<?= (isset($object)) ? $object['id'] : '' ?>" >
            <div class="tab-pane active" id="tab1">
                <br/>
                <div class="row">
                    <div class="form-group">
                        <label for="name" class="control-label">Название объекта *</label>
                        <input type="text" name="name" class="form-control" id="name" oninput="$('#alias').val(FlDashboardForm.prepareAlias($(this).val().translit()))" value="<?= htmlspecialchars(array_get($object, 'name', ''), ENT_QUOTES) ?>">
                    </div>
                    <div class="form-group">
                        <label for="alias" class="control-label">Алиас</label>
                        <input type="text" name="alias" class="form-control" id="alias" value="<?= array_get($object, 'alias') ?>">
                    </div>
                    <div class="form-group">
                        <label for="adres" class="control-label">Адрес *</label>
                        <input type="text" name="adres" class="form-control" id="adres" value="<?= array_get($object, 'adres') ?>">
                    </div>
                    <div class="form-group">
                        <label for="anons" class="control-label">Анонс</label>
                        <textarea class="form-control" name="anons" rows="3"><?= array_get($object, 'anons') ?></textarea>
                    </div>
                    <div class="form-group">
                        <label for="description" class="control-label">Описание</label>
                        <textarea class="form-control ckeditor" id="description" name="description" rows="3"><?= array_get($object, 'description') ?></textarea>
                    </div>
                    <div class="form-group">
                        <label class="control-label">Фото</label>
                        <div class="panel panel-default space_none">
                            <div class="panel-body"><?= $view_images ?></div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label">Теги</label>
                        <ul id="methodTags"></ul>
                        <input type="hidden" name="tags" id="mySingleFieldNode" value="<?= element('tags', $object, '') ?>">
                        <?php if ($tags): ?>
                            <!--set tags-->
                            <script>FlRegister.set('tags', <?= $tags ?>);</script>
                            <!--/set tags-->
                        <?php endif; ?>
                    </div>
                    <div class="form-group">
                        <input type="submit" class="btn btn-success" onclick="if (save_general()) {
                                    return true;
                                } else {
                                    return false;
                                }" value="Сохранить"/>
                    </div>
                </div>
            </div>
        </form>
        <?php if (isset($col_1) && $col_1) echo $col_1; ?>
    </div>
    <div class="col-md-2">
        <?php if (isset($col_2) && $col_2) echo $col_2; ?>
    </div>
</div>

<script>
    (function (){
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
    }());

    String.prototype.translit = (function () {
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
        k = function (a) {
            return a in L ? L[a] : a;
        };
        return function () {
            return this.replace(r, k);
        };
    })();
</script>
