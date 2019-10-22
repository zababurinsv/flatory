<header>
    <div class="banner_top"><?= element('content', element('top', $banners, array())) ?></div>
    <nav class="block_menu">
        <div><a href="/"><img src="/images/new/logo.png" alt="" /></a></div>
        <?= $nav ?>
    </nav>
    <?= $search ?>
    <?php if (isset($nav_catalog) && $nav_catalog) echo $nav_catalog; ?>
    <div class="general_banner"><?= element('content', element('middle', $banners, array()), '<img src="/images/general_banner.png"alt=""/>') ?></div>
</header>

