<div id="navigation">
    <div class="profile-picture">
        <a href="/admin">
            <img src="/images/new/logo.png" alt="logo">
        </a>
    </div>
    <ul class="nav" id="side-menu">
        <li>
            <a href="/" target="_blank"> <span class="nav-label">На сайт</span> </a>
        </li>
        <li>
            <a href="/admin/objects/"> <span class="nav-label">Каталог</span> </a>
        </li>
        <li>
            <a href="/admin/posts/"> <span class="nav-label">Записи</span> </a>
        </li>
        <li>
            <a href="/admin/storage/"> <span class="nav-label">Файлы</span> </a>
        </li>
        <li>
            <a href="javascript:void(0)"><span class="nav-label">Справочники</span><span class="fa arrow"></span> </a>
            <ul class="nav nav-second-level">
                <li><a href="/admin/glossary/">Картотека</a></li>
                <!--<li><a href="/admin/handbk/register/file_catigories/">Категории</a></li>-->
                <li>
                    <a href="javascript:void(0)"><span class="nav-label">Метро</span><span class="fa arrow"></span> </a>
                    <ul class="nav nav-third-level">
                        <li><a href="/admin/handbk/register/metro_line/">Линии</a></li>
                        <li><a href="/admin/handbk/register/metro_station/">Станции</a></li>
                    </ul>
                </li>
                <!--<li><a href="/admin/ghandbks/">Общие справочники</a></li>-->
                <li><a href="/admin/organizations/">Организации</a></li>
                <!--<li><a href="/admin/handbk/register/proportion">Пропорции</a></li>-->
                <li>
                    <a href="javascript:void(0)"><span class="nav-label">Регионы</span><span class="fa arrow"></span> </a>
                    <ul class="nav nav-third-level">
                        <li><a href="/admin/handbk/register/geo_area/">МО: Район / Округ</a></li>
                        <li><a href="/admin/handbk/register/populated_locality/">МО: Нас. пункт</a></li>
                        <li><a href="/admin/handbk/register/district/">Москва: Округ</a></li>
                        <li><a href="/admin/handbk/register/square/">Москва: Район</a></li>
                        <li><a href="/admin/handbk/register/populated_locality_type/">Тип нас. пункта</a></li>
                    </ul>
                </li>
                <li><a href="/admin/registry/">Реестр</a></li>
                <li><a href="/admin/handbk/register/tag/">Теги</a></li>
            </ul>
        </li>
        <li>
            <a href="/admin/contacts/"> <span class="nav-label">Настройки</span></a>
        </li>
        <li>
            <a href="javascript:FlCache.dropAll()"> <span class="nav-label">Очистить Кэш</span></a>
        </li>
        <li>
            <a href="/admin/console/exit_admin"> <span class="nav-label">Выход</span></a>
        </li>
    </ul>
</div>
<script>
    (function () {
        var forceCurrentPath = '<?= isset($force_current_path) ? $force_current_path : '' ?>';
        var el = $('#side-menu [href="' + (!!forceCurrentPath ? forceCurrentPath : location.pathname) + '"]');

        if (!el.length)
            return;

        el.parent('li').addClass('active');
        el.parents('.nav-third-level').parent('li').addClass('active');
        el.parents('.nav-second-level').parent('li').addClass('active');

    }());
</script>

