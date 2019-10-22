<div class="panel panel-default space_top_xs mass_edit_image">
    <div class="panel-heading" style="cursor: pointer;">Массовое редактирование изображений</div>

    <div class="panel-body" style="display: none; padding: 0 15px 15px 0;">
        <form action="" role="form">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Подпись</label>
                        <div class="pull-right">
                            <label class="radio-inline">
                                <input type="radio" name="alt_edit" value="1"> Заменить
                            </label>
                            <label class="radio-inline">
                                <input type="radio" name="alt_edit" value="0" checked="checked"> Не изменять
                            </label>
                        </div>
                        <textarea name="alt" class="form-control"></textarea>
                    </div>
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
                    <b>Количество изменяемых файлов:</b> <span class="mass_edit_image__count">0</span>
                </div>
                <div class="col-md-6">
                    <table class="table table-hover mass_edit_image__proportions">
                        <thead>
                            <tr>
                                <th>Размер</th>
                                <th>Водяной знак</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($proportions as $item): ?>
                                <tr>
                                    <td>
                                        <div class="checkbox" style="margin: 0;">
                                            <label>
                                                <?php if(isset($require_proportions) && $require_proportions): ?>
                                                <input type="checkbox" name="proportion[]" value="<?= $item['proportion_id'] ?>" checked="checked"> <?= $item['name'] ?>
                                                <?php else: ?>
                                                <input type="checkbox" name="proportion[]" value="<?= $item['proportion_id'] ?>"> <?= $item['name'] ?>
                                                <?php endif; ?>
                                            </label>
                                        </div>
                                    </td>
                                    <?php if(isset($require_proportions) && $require_proportions): ?>
                                    <td><input type="checkbox" name="is_watermark" value="1" <?php if(element('is_watermark', $item, 0)): ?>checked="checked"<?php endif; ?>></td>
                                    <?php else: ?>
                                    <td><input type="checkbox" name="is_watermark" value="1"></td>
                                    <?php endif; ?>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <a href="javascript:void(0)" class="btn btn-warning pull-right mass_edit_image__apply">Применить</a>
            <a href="javascript:void(0)" class="btn btn-default pull-right mass_edit_image__drop space_right">Сброс</a>
        </form>
    </div>
</div>
<script type="text/template" class="mass_edit_image__proportions_item">
    <tr>
        <td>
            <div class="checkbox" style="margin: 0;">
                <label>
                    <input type="checkbox" name="proportion[]" value="{{=it.proportion_id}}" checked="checked"> {{=it.name}}
                </label>
            </div>
        </td>
        <td><input type="checkbox" name="is_watermark" value="1" checked="checked"></td>
    </tr>
</script>
