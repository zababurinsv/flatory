<?php
//vdump($object);
?>
<ul class="nav nav-tabs space_bottom">
    <?php foreach ($list as $item): ?>
    <li><a href="<?= $item['path'] . element('object_id', $object, element('id', $object, '')) ?>"><?= $item['name'] ?></a></li>
    <?php endforeach; ?>
</ul>
<script>
    (function (){
        var p = location.pathname;
        $('.nav-tabs').find('[href^="'+ p +'"]').parent('li').addClass('active');
    }());
</script>