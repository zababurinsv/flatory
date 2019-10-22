<div class="jcarousel-wrapper fl-gallery">
    <div class="jcarousel" data-jcarousel="false">
        <ul>
            <li data-caption="<?= element('alt', $image, '') ?>" data-credits="<?= html_escape(element('description', $image, '')) ?>" data-index="0">
                <a href="<?= $path_full . element('file_name', $image, 'no_photo.jpg') ?>" class="fancybox-thumbs" rel="album_<?= element('image_album_id', $image, 'x') ?>" title="<?= element('alt', $image, '') ?>">
                    <img src="<?= $path_middle . element('file_name', $image, 'no_photo.jpg') ?>" alt="<?= element('alt', $image, '') ?>">
                </a>
            </li>
        </ul>
    </div>
    <div class="album_meta">
        <div class="caption"><?= element('alt', $image, '') ?></div>
        <div class="credits" <?php if (!element('description', $image, '')): ?>style="display: none;" <?php endif; ?>><?= element('description', $image, '') ?></div>
    </div>
</div>