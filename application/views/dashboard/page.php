<?php
if (isset($breadcrumbs) && is_array($breadcrumbs) && $breadcrumbs): $_b_i = 0;
    $_b_count = count($breadcrumbs);
    ?>
    <ol class="breadcrumb">
        <?php foreach ($breadcrumbs as $it): $_b_i++; ?>
            <?php if ($_b_i !== $_b_count): ?>
                <li><a href="<?= array_get($it, 'url') ?>"><?= array_get($it, 'title') ?></a></li>
            <?php else: ?>
                <li class="active"><?= array_get($it, 'title') ?></li>
            <?php endif; ?>
    <?php endforeach; ?>
    </ol>
<?php endif; ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <h1><?= $title ?></h1>
        </div>
    </div>
    <?= $content ?>
</div>


