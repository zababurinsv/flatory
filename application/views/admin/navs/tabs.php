<ul class="nav nav-tabs">
    <?php foreach ($list as $item): ?>
    <li><a href="<?= $path . $item['alias']  ?>/"><?= $item['name'] ?></a></li>
    <?php endforeach; ?>
</ul>
<script>
    (function (){
        var p = location.pathname;
        p = p.match(/admin\/storage\/?$/) ? '/admin/storage/images' : p;
        $('.nav-tabs').find('[href^="'+ p +'"]').parent('li').addClass('active');
    }());
</script>