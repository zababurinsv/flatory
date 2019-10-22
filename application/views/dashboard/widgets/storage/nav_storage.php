<ul class="nav nav-tabs widget_storage__storage_nav">
    <?php foreach ($list as $item): ?>
    <li><a href="javascript:void(0)" data-section="<?= $item['alias'] ?>"><?= $item['name'] ?></a></li>
    <?php endforeach; ?>
</ul>