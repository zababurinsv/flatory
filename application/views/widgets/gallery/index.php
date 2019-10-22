<?php $first = isset($list[0]) ? $list[0] : array(); ?>
<div class="jcarousel-wrapper fl-gallery">
    <div class="jcarousel" data-jcarousel="true">
        <ul>
            <?php foreach ($list as $i => $image): ?>
            <li data-caption="<?= element('alt', $image, '') ?>" data-credits="<?= html_escape(element('description', $image, '')) ?>" data-index="<?= ++$i ?>">
                    <a href="<?= $path_full . element('file_name', $image, 'no_photo.jpg') ?>" class="fancybox-thumbs" rel="album_<?= element('image_album_id', $image, 'x') ?>" title="<?= element('alt', $image, '') ?>">
                        <img src="<?= $path_middle . element('file_name', $image, 'no_photo.jpg') ?>" alt="<?= element('alt', $image, '') ?>">
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
    <div class="album_meta">
        <?php if(count($list) > 1): ?>
        <div class="carusel-counter"><?= (int)!empty($first) ?> / <?= count($list) ?></div>
        <?php endif; ?>
        <div class="caption"><?= element('alt', $first, '') ?></div>
        <div class="credits" <?php if(!element('description', $first, '')): ?>style="display: none;" <?php endif; ?>><?= element('description', $first, '') ?></div>
    </div>
    <?php if(count($list) > 1): ?>
    <a href="javascript:void(0)" class="jcarousel-control-prev inactive" data-jcarouselcontrol="true"><span></span></a>
    <a href="javascript:void(0)" class="jcarousel-control-next" data-jcarouselcontrol="true"><span></span></a>
    <?php endif; ?>
</div>
