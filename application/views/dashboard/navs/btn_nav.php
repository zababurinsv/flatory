<ul class="list-inline space_none">
    <?php foreach ($list as $item):?>
        <li>
            <?php if($item.html_type === 'button'): ?>
            <button type="button" class="btn btn-<?= array_get($item, 'type', 'default') ?>" <?php foreach (array_get($item, 'data', []) as $data_k  => $data_v){ echo 'data-'. $data_k .'="'. $data_v . '"';} ?>>
                    <?php if (array_get($item, 'glyphicon', '')): ?><span class="glyphicon <?= $item['glyphicon'] ?>"></span> <?php endif; ?>
                    <?= array_get($item, 'title', '') ?>
                </button>
            <?php else: ?>
                <a href="<?= array_get($item, 'url', 'javascript:void(0)') ?>" class="btn btn-<?= array_get($item, 'type', 'default') ?>" <?php foreach (array_get($item, 'data', []) as $data_k  => $data_v){ echo 'data-'. $data_k .'="'. $data_v . '"';} ?>>
                    <?php if (array_get($item, 'glyphicon', '')): ?><span class="glyphicon <?= $item['glyphicon'] ?>"></span> <?php endif; ?>
                    <?= array_get($item, 'title', '') ?>
                </a>
            <?php endif; ?>
        </li>
   <?php endforeach; ?>
</ul>