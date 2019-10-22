<form action="/admin/objects/post_album_create/<?= element('object_id', $form, '') ?>" method="post" class="space_bottom">
    <div class="form-group">
        <div class="input-group">
            <input type="text" name="album_name" class="form-control" value="<?= element('name', $form, '') ?>" placeholder="Название альбома">
            <div class="input-group-btn">
                <button type="submit" class="btn btn-default">Создать</button>
            </div>
        </div>
        <input type="hidden" name="object_id" value="<?= element('object_id', $form, '') ?>">
        <input type="hidden" name="file_category_id" value="<?= element('file_category_id', $form, 0) ?>">
        <input type="hidden" name="album_create" value="1">
        <input type="hidden" name="route" value="<?= element('route', $form, '') ?>">
    </div>
</form>

