<h4>Создание нового альбома</h4>
<form action="/admin/objects/post_album_create/<?= element('object_id', $form, '') ?>" method="post" class="form-inline space_bottom">
    <div class="form-group" style="min-width: 40%;">
        <input type="text" name="album_name" class="form-control" value="<?= element('name', $form, '') ?>" placeholder="Название альбома">
    </div>
    <div class="form-group">
        <input type="hidden" name="object_id" value="<?= element('object_id', $form, '') ?>">
        <input type="hidden" name="file_category_id" value="<?= element('file_category_id', $form, 0) ?>">
        <input type="hidden" name="album_create" value="1">
        <input type="hidden" name="route" value="<?= element('route', $form, '') ?>">
        <button type="submit" class="btn btn-default">Создать</button>
    </div>
</form>

