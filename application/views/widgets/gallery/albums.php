<?php $carousel_height = count($albums) < 3 ? 'style="height:200px"' : ''; $is_one_album = count($albums) === 1; ?>
<div class="ficha">
    <?php if(!$is_one_album): ?>
    <div class="jcarousel-wrapper simple_carusel" <?= $carousel_height ?> >
        <div class="jcarousel" <?= $carousel_height ?> >
            <?php $i = 0; ?>
            <ul>
                <?php foreach ($albums as $album_id => $album): $i++; ?>
                    <?php if ($i == 1): ?>
                        <li>
                            <div class="albums">
                            <?php endif; ?>
                            <a href="javascript:void(0)" class="album" data-id="<?= $album_id ?>" data-name="<?= $album['album_name'] ?>">
                                <?php if (isset($album['images'][0]['file_name'])): ?>
                                    <img src="<?= $path_small . $album['images'][0]['file_name'] ?>"/>
                                <?php else: ?>
                                    <img src="/images/no_photo.jpg"/>
                                <?php endif; ?>
                                <div class="album_name">
                                    <span><?= $album['album_name'] ?></span>
                                </div>
                            </a>
                            <?php if ($i == 4): $i = 0; ?>
                            </div>
                        </li>
                    <?php endif; ?>
                <?php endforeach; ?>
            </ul>
        </div>
        <i class="jcarousel-pagination"></i>
    </div>
    <div class="albums_controls" style="display: none;">
        <div class="album_date"></div>
        <a href="javascript:void(0)" class="album_submit"><img src="/images/left_text_arrow.png"><span>Все альбомы</span></a>
    </div>
    <?php endif; ?> 
    <?php foreach ($albums as $album_id => $album) : ?>
    <div class="album_content" data-id="<?= $album_id ?>" data-name="<?= $album['album_name'] ?>"<?php if($is_one_album):?> style="display: block;"<?php endif; ?>>
            <div class="clear"></div>
            <?= $widget->render(element('images', $album, array())); ?>
        </div>
    <?php endforeach; ?>
</div>