<!--style="display: none;"-->
<form action="" role="form" class="mass_edit_image space_bottom">
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label>Источник</label>
                <div class="pull-right">
                    <label class="radio-inline">
                        <input type="radio" name="description_edit" value="1"> Заменить
                    </label>
                    <label class="radio-inline">
                        <input type="radio" name="description_edit" value="0" checked="checked"> Не изменять
                    </label>
                </div>
                <textarea name="description" class="form-control"></textarea>
            </div>

        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label>Теги</label>
                <div class="pull-right">
                    <label class="radio-inline">
                        <input type="radio" name="tags_edit" value="2"> Добавить
                    </label>
                    <label class="radio-inline">
                        <input type="radio" name="tags_edit" value="1"> Заменить
                    </label>
                    <label class="radio-inline">
                        <input type="radio" name="tags_edit" value="0" checked="checked"> Не изменять
                    </label>
                </div>

                <ul class="methodTags"></ul>
                <input type="hidden" name="tags" class="mySingleFieldNode" value="">
            </div>
            <!--set tags-->
            <script>
<?php if ($tags): ?>
                    FlRegister.set('tags', <?= $tags ?>);
<?php endif; ?>
            </script>
            <!--/set tags-->
        </div>
    </div>
    <ul class="fl-mass-edit-controls">
        <li><span><b>Количество изменяемых файлов:</b> <span class="mass_edit_image__count">0</span></span></li>
        <li class="pull-right"><a href="javascript:void(0)" class="btn btn-warning mass_edit_image__apply space_left">Применить</a></li>
        <li class="pull-right"><a href="javascript:void(0)" class="btn btn-default mass_edit_image__drop">Сброс</a></li>
    </ul>
</form>
