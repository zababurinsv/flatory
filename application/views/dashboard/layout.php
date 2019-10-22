<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8"/>
        <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
        <title>Панель администратора</title>
        <!--styles-->
        <?php foreach ($styles as $style): ?>
            <link href="<?= $style ?>" rel="stylesheet"/>
        <?php endforeach; ?>
        <!--scipts-->
        <?php foreach ($scripts as $script): ?>
            <script src="<?= $script ?>"></script>
        <?php endforeach; ?>
    </head>
    <body class="fixed-sidebar">
        <div id="error_msg_dialog" title="Ошибка"></div>
        <!-- Header -->
        <?= $header ?>
        <!-- \Header -->
        <!-- Navigation -->
        <aside id="menu">
            <?= $menu ?>
        </aside>
        <!-- \Navigation -->
        <!-- Main Wrapper -->
        <div id="wrapper">
            <?= $content ?>
        </div>
        <!-- Footer -->
        <?= $footer ?>
        <!-- After body -->
        <?= $after_body ?>
        <!-- templates -->
        <?php foreach ($html_tpls as $html_tpl): ?>
            <?= $html_tpl ?>
        <?php endforeach; ?>
        <!-- bottom scripts-->
        <?php foreach ($bottom_scripts as $script): ?>
            <script src="<?= $script ?>"></script>
        <?php endforeach; ?>
    </body>
</html>

