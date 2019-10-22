<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8"/>
        <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
        <meta name="description" content=""/>
        <meta name="author" content=""/>
        <title>Flatory::Административная панель</title>

        <!-- Bootstrap core CSS -->
        <link href="/css/bootstrap.css" rel="stylesheet"/>
        <!--<script src="/js/jquery.js"></script>-->
        <script src="/js/jquery-1.8.3.js"></script>
        <script src="/js/jqueryui.custom.js"></script>
        <script src="/js/localization.js"></script>
        <script src="/js/functions.js"></script>
        <script src="/js/front/forms.js"></script>
        <!-- Add custom CSS here -->
        <link href="/css/simple-sidebar.css" rel="stylesheet"/>
        <link href="/css/font-awesome.min.css" rel="stylesheet"/>

    </head>

    <body>
        <div id="error_msg_dialog" title="Ошибка">
        </div>

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
                                <?php if(is_array($name)): ?>
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
                        <a href="/admin/objects">Каталог</a>
                        <ul  class="list-unstyled space_left">
                            <li><a <?if($menu == 'object') echo 'class="active"'?> href="/admin/objects/">Список объектов</a></li>
                            <li><a <?if($menu == 'add_object') echo 'class="active"'?> href="/admin/objects/general_info">Добавить объект</a></li>
                        </ul>
                    </li>
                    <li>
                        <a href="/admin/posts/">Записи</a>
                    </li>
                    <li>
                        <a <?if($menu == 'contacts') echo 'class="active"'?> href="/admin/contacts">Настройки</a>
                        <ul  class="list-unstyled space_left">
                            <li><a href="javascript:FlCache.clear()">Очистить Кэш</a></li>
                        </ul>
                    </li>
                    <li><a href="/admin/console/exit_admin">Выход</a></li>
                </ul>
            </div>

            <!-- Page content -->
            <div id="page-content-wrapper">
                <div class="content-header">
                    <h1>
                        <a id="menu-toggle" href="#" class="btn btn-default"><i class="icon-reorder"></i></a>
                        <?php if (isset($title)): ?>
                            <?= $title ?> 
                        <?php endif; ?>
                    </h1>
                </div>
                <!-- Keep all page content within the page-content inset div! -->
                <div class="page-content inset">