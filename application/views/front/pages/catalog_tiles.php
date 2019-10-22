<?php if (!empty($objects)): ?>
    <?php
    $i = 1;
    foreach ($objects as $object):
        // определяем какого размера картинку показывать
        // из каждых 5-ти - каждая 3-я картинка на 2 колонка, а 5-я на 3 колонки
        $i = $i > 5 ? 1 : $i;
        $postfix = $i === 3 ? 2 : ($i === 5 ? 3 : 1);
        $i++;
        ?>

        <a href="/catalog/<?= htmlspecialchars($object['alias'], ENT_QUOTES) ?>" title="<?= htmlspecialchars($object['name'], ENT_QUOTES) ?>">
            <div class="spec_praym<?= $postfix ?>">
                <img class="newsimg<?= $postfix ?>" src="/images/<?= !!$object['image_' . $postfix] ? 'original/'. $object['image_' . $postfix] : 'no_photo.jpg' ?>"/>
                <div class="img_review" style="width: 100%;"><?= $object['name'] ?></div>
            </div>
        </a>

    <?php endforeach; ?>
    <!--pagination-->
    <?php if (isset($pagination)): ?> 
        <div class="clearfix space_bottom"></div>
        <div class="flatory_pagination__center">
        <?= $pagination ?>
        </div>
    <?php endif; ?>
    <!--/pagination-->
<?php endif; ?>