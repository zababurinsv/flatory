<?php if ($list): ?>
    <ul class="nav nav-pills">
        <?php foreach ($list as $item): ?>
            <li<?= array_get($item, 'active') ? ' class="active"' : '' ?>><a href="<?= $item['url'] ?>"><?= $item['title'] ?></a></li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>