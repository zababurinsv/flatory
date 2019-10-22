<?php
$_parent_id = array_pop($this->uri->segment_array());
?>
<ul class="tile_galery cart_image_added <?= $list_class ?>">
    <?php if (!empty($images)): ?>
        <?php foreach ($images as $key => $it): ?>
            <li>
                <img src="/images/thumbs/<?= $it['file_name'] ?>" class="thumbnail">
                <div class="item_control">
                    <i role="button" class="glyphicon glyphicon-edit tile_galery__edit_item"></i>
                    <i role="button" class="glyphicon glyphicon-remove text-danger tile_galery__remove_item pull-right"></i>
                </div>
                <input type="hidden" name="files[]" value="<?= $it['file_id'] ?>">
                <div class="item_settings">
                    <div class="form-group">
                        <label>Сортировка</label>
                        <input type="text" name="sort[<?= $it['file_id'] ?>]" value="<?= $it['sort'] ?>">
                    </div>
                    <div class="form-group">
                        <label>Код</label>
                        <input type="text" value='{"file_id":<?= $it['file_id'] ?>,"file_category_id":<?= $it['file_category_id'] ?>,"parent_id":<?= $_parent_id ?>}'>
                    </div>
                </div>
                <a href="/admin/storage/card/<?= $it['name'] ?>" class="tile_galery__label" target="_blank"><?= $it['original_name'] ?></a>
            </li>
        <?php endforeach; ?>
    <?php endif; ?>
</ul>

