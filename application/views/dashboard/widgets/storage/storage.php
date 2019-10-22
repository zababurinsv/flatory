<div class="storage_controls">
    <nav class="pagination-ajax"></nav>
    <ul class="list-inline ws-nav-view-types">
        <li><label><input type="checkbox" name="check_all_files" title="Выделить все"></label></li>
        <li><a href="javascript:void(0)" class="glyphicon glyphicon-th js-ws-view-type active" data-view="tile"></a></li>
        <li><a href="javascript:void(0)" class="glyphicon glyphicon-th-list js-ws-view-type" data-view="list"></a></li>
    </ul>
    <div class="clearfix space_bottom"></div>
</div>
<!--view list-->
<table class="table storage_list" data-view-type="list" style="display: none;"> 
    <thead>
        <tr>
            <th><input type="checkbox" name="check_all_files"></th>
            <th></th>
            <th>Файл</th>
            <th>Дата</th>
        </tr>
    </thead>
    <tbody class="js-ws-it-place">
    </tbody>
</table>
<!--/view list-->
<!--view tile-->
<ul class="tile_galery storage_tile js-ws-it-place" data-view-type="tile" style="display: none;">
</ul>