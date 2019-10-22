<aside class="sidebar_left">
    <?= $sidebar_left ?>
    <div class="banner_left"><?= element('content', element('left', $banners, array())) ?></div>
</aside>
<section class="content" role="main">
    <?= $breadcrumbs ?>
    <?= $body ?>
</section>
<aside class="sidebar_right">
    <div class="banner_right"><?= element('content', element('right', $banners, array())) ?></div>
    <?= $sidebar_right ?>
</aside>
<div class="clearfix"></div>

