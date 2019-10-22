<div class="geo-index">
    <h1 class="space_bottom"><?= $title ?></h1>
    <?php if(isset($sub_nav) && $sub_nav): ?>
    <ul class="nav-fast">
        <?php foreach ($sub_nav as $it): ?>
            <li><a href="<?= $path_url . array_get($it, 'alias', '') ?>"><?= array_get($it, 'label', '') ?></a></li>
        <?php endforeach; ?>
    </ul>
    <?php endif; ?>
    <div class="place space_top">
        <?php
        $col_count = 3;
        $col_limit = ceil(count($alphabet) / $col_count);
        for ($col = 0; $col < $col_count; $col++):
            ?>
            <div class="col-33" data-col="<?= $col ?>">
                <?php
                for ($l = 0; $l < $col_limit; $l++):
                    $letter = array_shift($alphabet);
                    ?>
                    <ul data-letter="<?= $_l = array_get($letter, 'letter', '') ?>">
                        <li class="letter"><?= $_l ?></li>
                        <?php foreach (array_get($letter, 'items', []) as $it): ?>
                            <li><a href="<?php if (isset($build_url) && is_callable($build_url)) echo $build_url($it);
                else echo $path_url . array_get($it, 'alias', ''); ?>"><?= array_get($it, 'label', '') ?><?php if(isset($it['count_objects'])) echo ' (' . $it['count_objects'] . ')'; ?></a></li>
                    <?php endforeach; ?>
                    </ul>
            <?php endfor; ?>
            </div>
<?php endfor; ?>
        <div class="clearfix"></div>
    </div>
</div>
