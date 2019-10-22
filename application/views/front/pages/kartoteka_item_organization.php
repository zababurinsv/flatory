<?php
//vdump($item, 1);
//vdump($objects, 1);
//vdump($tags, 1);
?>
<div class="content_space">
    <h1 class="news_name space_bottom_l"><?= element('name', $item, '') ?></h1>
    <div class="space_bottom_xs">
        <?php if (($image = element('image', $item))): ?>
            <img src="<?= $image ?>" alt="<?= element('name', $item, '') ?>" class="pull-left space_right" style="max-width: 50%;">
        <?php endif; ?>
        <?php if (is_array($p = element('params', $item))): ?>
            <address class="pull-left" style="max-width: 50%;">
                <?php if (($address = element('address', $p))): ?>
                    <span><?= $address ?></span><br>
                <?php endif; ?>
                <?php foreach (element('phone', $p, array()) as $phone): ?>
                    <span><?= $phone ?></span><br>
                <?php endforeach; ?>
                <?php if (($site = element('site', $p))): ?>
                    <span><a href="<?= $site ?>" target="_blank"><?= $site ?></a></span><br>
                <?php endif; ?>
            </address>
        <?php endif; ?>
        <div class="clearfix"></div>
    </div>
    <div class="space_bottom"><?= element('description', $item, '') ?></div>
    <?php if ($tags): ?>
        <ul class="list-inline space_bottom">
            <li><b>Теги:</b></li>
            <?php foreach ($tags as $it): ?>
                <li><a href="/tags/<?= element('alias', $it, '') ?>" class="content-limk text-green"><?= element('name', $it, '') ?></a></li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
    <?php if ($objects): ?>
        <div class="space_bottom">
            <h3 class="space_bottom_xs">Новостройки <?= element('name', $item, '') ?>:</h3>
            <?= $objects; ?>
            <?php if ($is_show_more_objects): ?>
                <center class="space_top_xs" id="show_more">
                    <a href="javascript:void(0)" style="font-weight: bold;">Показать еще</a>    
                    <img src="/images/loader_sm.gif" style="display: none;">
                </center>
            <?php endif; ?>
        </div>
    <?php endif; ?>
    <?php if ($read_more) echo $read_more; ?>
</div>
