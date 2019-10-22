<div class="image_simple_upload <?= element('class', $attr, '') ?>">
    <div class="upload_place">
        <a href="javascript:void(0)" class="btn btn-xs btn-danger abort_upload" data-fid="" data-fon="">Отменить загрузку</a>
        <a href="javascript:void(0)">
            <img src="<?= element('path', $image, '/images/') . element('file_name', $image, 'no_photo.jpg') ?>" class="uploaded_image <?= element('image_class', $attr, '') ?>" />
        </a>
        <input type="file" name="file" class="simple_upload"/>
        <input type="hidden" name="file_id" value="<?= element('file_id', $image, '') ?>"/>
        <img src="/images/loader_horizontal.gif" class="loader"/>
        <?php if($upload_place_content): echo $upload_place_content; endif; ?>
    </div>
    <i class="space_bottom_xs" style="display: block;"><?= !!$title ? $title : 'Чтобы загрузить новое изорбажение<br>- кликните по текущему.' ?></i>
    <a href="javascript:void(0)" class="btn btn-sm btn-primary select_from_srorage space_right" data-view-type="0" data-select-type="radio" data-filters='<?= $filters ?>'>Выбрать</a>
</div>