<?php
// uri path
$_uri = http_build_query(array_except($get, ['vt', 'sf', 'sd'])); 
// sort field
$_sf = array_get($get, 'sf', array_get($default_order, 'order', ''));
// sort direction
$_sd = array_get($get, 'sd', array_get($default_order, 'order_direction', '')) === 'asc' ? 'asc' : 'desc';
$_sd_reverse = $_sd === 'asc' ? 'desc' : 'asc';
?>
<!--list controls-->
<div class="content_space">
    <?php if ($is_show_controls): ?>
        <!--result panel-->
        <div class="result_panel">
            <ul class="list-inline pull-left result_panel__view_nav">
                <li><a href="<?= $base_url ?>/?<?= $_uri ?>&vt=tiles" title="Плитка" data-type="tiles" class="glyphicon glyphicon-th"></a></li>
                <li><a href="<?= $base_url ?>/?<?= $_uri ?>&vt=list" title="Список" data-type="list" class="glyphicon glyphicon-th-list"></a></li>
                <li><a href="<?= $base_url ?>/?<?= $_uri ?>&vt=map" title="Карта" data-type="map"><span class="glyphicon glyphicon-map-marker"></span> <span class="result_panel__view_nav_label">На карте</span></a></li>
            </ul>
            <?php if ($view_type !== 'map'): ?>
                <ul class="list-inline pull-right result_panel__sort_nav">
                    <li><b>Сортировать:</b></li>
                    <li><a href="<?= $base_url ?>/?<?= $_uri ?>&sf=cost&sd=<?= $_sd_reverse ?>" data-field="cost">по цене</a></li>
                    <li><a href="<?= $base_url ?>/?<?= $_uri ?>&sf=delivery&sd=<?= $_sd_reverse ?>" data-field="delivery">по сроку сдачи</a></li>
                </ul>
            <?php endif; ?>
            <script>
                (function (){
                    var f = '<?= $_sf ?>', d = '<?= $_sd ?>', vt = '<?= $view_type ?>';
                    // set active sort
                    $('.result_panel__sort_nav').find('[data-field="'+ f +'"]').addClass('active').prepend($('<span>', {
                        class: 'glyphicon glyphicon-sort-by-attributes' + (d === 'desc' ? '-alt' : '')
                    }));
                    // set active view type
                    $('.result_panel__view_nav').find('[data-type="'+ vt +'"]').addClass('active');
                    // prevent click on active view type
                    $('.result_panel__view_nav .active').on('click', function (e){
                        e.preventDefault();
                        return false;
                    });
                }());
            </script>
        </div>
        <!--/result panel-->
    <?php endif; ?>
    <?php if (isset($message)): ?>
        <?= $message; ?>
    <?php endif; ?>
</div>
<!--/list controls-->
<?php if ($view_type === 'catalog_list'): ?>
    <div class="content_space">
        <?= $view_objects ?>
    </div>
<?php else: ?>
    <?= $view_objects ?>
<?php endif; ?>
