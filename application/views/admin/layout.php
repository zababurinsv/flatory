<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8"/>
        <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
        <meta name="description" content=""/>
        <meta name="author" content=""/>
        <title>Flatory::Административная панель</title>
        <!--styles-->
        <?php if (!empty($styles)):
            foreach ($styles as $style):
                ?>
                <link href="/css/<?= $style ?>" rel="stylesheet"/>
            <?php endforeach;
        endif;
        ?>
        <!--scipts-->
        <?php if (!empty($scripts)):
            foreach ($scripts as $script):
                ?>
                <script src="/js/<?= $script ?>"></script>
            <?php endforeach;
        endif;
        ?>
    </head>
    <body>
<?= $header ?>
        <div id="error_msg_dialog" title="Ошибка"></div>
        <div id="wrapper">
            <!-- Sidebar -->
            <div id="sidebar-wrapper">
                <ul class="sidebar-nav list-unstyled">
                    <li><a href="/" target="blank">На сайт</a></li>
                            <?php if (!empty($handbks)): ?>
                        <!--handbks-->
                        <li>
                            <a href="/admin/handbk">Справочники</a>
                            <ul class="list-unstyled space_left">
                                <?php foreach ($handbks as $alias => $name): ?>
                                    <?php if (is_array($name)): ?>
                                        <li><a href="<?= element('url', $name, '#') ?>"><?= element('name', $name, '') ?></a></li>
        <?php else: ?>
                                        <li><a href="/admin/handbk/register/<?= $alias ?>"><?= $name ?></a></li>
        <?php endif; ?>
    <?php endforeach; ?>
                            </ul>
                            <script>
                                (function() {
                                    var hb = '<?= $this->uri->segment(4); ?>';
                                    var el = $('[href*="' + hb + '"]')[0];
                                    $(el).addClass('active');
                                }());
                            </script>

                        </li>
                        <!--\handbks-->
<?php endif; ?>
                    <li><a href="/admin/storage">Файлы</a></li>
                    <li>
                        <a href="#">Каталог</a>
                        <ul class="list-unstyled space_left">
                            <li><a href="/admin/objects/">Список объектов</a></li>
                            <li><a href="/admin/objects/general_info">Добавить объект</a></li>
                        </ul>
                    </li>
                    <li>
                        <a href="/admin/posts/">Записи</a>
                    </li>
                    <li>
                        <a href="/admin/contacts">Настройки</a>
                        <ul class="list-unstyled space_left">
                            <li><a href="javascript:FlCache.clear()">Очистить Кэш</a></li>
                        </ul>
                    </li>
                    <li><a href="/admin/console/exit_admin">Выход</a></li>
                </ul>
                <script>
                    (function() {
                        var el = $('[href^="' + window.location.pathname + '"]');
                        $(el).eq(0).addClass('active');
                    }());
                </script>
            </div>
            <!-- end Sidebar -->
            <!-- Page content -->
            <div id="page-content-wrapper">
                <div class="content-header">
                    <h1>
                        <a id="menu-toggle" href="#" class="btn btn-default"><i class="icon-reorder"></i></a>
                    <?= $title ?> 
                    </h1>
                </div>
                <?php if(isset($breadcrumbs) && is_array($breadcrumbs) && $breadcrumbs):  $_b_i = 0; $_b_count = count($breadcrumbs); ?>
                    <ol class="breadcrumb">
                        <?php foreach ($breadcrumbs as $it): $_b_i++; ?>
                        <?php if($_b_i !== $_b_count): ?>
                        <li><a href="<?= array_get($it, 'url') ?>"><?= array_get($it, 'title') ?></a></li>
                        <?php else: ?>
                        <li class="active"><?= array_get($it, 'title') ?></li>
                        <?php endif; ?>
                        <?php endforeach; ?>
                    </ol>
                <?php endif; ?>
                <!-- Keep all page content within the page-content inset div! -->
                <div class="page-content inset">
                    <?= $content ?>
                </div>
            </div>
            <!-- end Page content -->
        </div>
        <!--end wrapper-->
        <?= $footer ?>
        <?= $after_body ?>
        <!--templates -->
        <?php foreach ($html_tpls as $html_tpl): ?>
            <?= $html_tpl ?>
        <?php endforeach; ?>
        <!--/templates -->
        <!--bottom scripts-->
        <?php if (!empty($bottom_scripts)):
            foreach ($bottom_scripts as $script):
                ?>
                <script src="/js/<?= $script ?>"></script>
    <?php endforeach;
endif;
?>
        <script>
                    $("#menu-toggle").click(function(e) {
                        e.preventDefault();
                        $("#wrapper").toggleClass("active");
                    });
        </script>
    </body>
</html>

