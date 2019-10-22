<?php if ($breadcrumbs): ?>
    <ol class="breadcrumb">
        <?php foreach ($breadcrumbs as $it): ?>
            <?php if (element('url', $it)): ?>
                <li><a href="<?= $it['url'] ?>"><?= element('name', $it) ?></a></li>
            <?php else: ?>
                <li><?= element('name', $it) ?></li>
            <?php endif; ?>
        <?php endforeach; ?>
    </ol>
    <script>
        $('.breadcrumb li').last().addClass('active');
    </script>
<?php endif; ?>