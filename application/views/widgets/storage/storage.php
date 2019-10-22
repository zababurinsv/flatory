<div class="storage_controls">
    <div class="pull-right">
        <?php if ($total_rows): ?>
            <script>FlRegister.set('total_storage', <?= $total_rows ?>);</script>
        <?php endif; ?>
        <nav class="pagination-ajax"></nav>
    </div>
    <ul class="list-inline">
        <li><a href="javascript:void(0)" class="btn btn-default view-type active" data-view="tile"><span class="glyphicon glyphicon-th"></span></a></li>
        <li><a href="javascript:void(0)" class="btn btn-default view-type" data-view="list"><span class="glyphicon glyphicon-th-list"></span></a></li>
        <li data-view-type="tile"><label><input type="checkbox" name="check_all_files">Выделить все</label></li>
    </ul>
</div>
<!--view list-->
<?php if (!empty($list)): ?>
    <table class="table storage_list" data-view-type="list" style="display: none;"> 
        <thead>
            <tr>
                <th><input type="checkbox" name="check_all_files"></th>
                <th></th>
                <th>Файл</th>
                <th>Дата</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($list as $item): ?>
                <tr>
                    <td style="width:20px"><input type="checkbox" name="file_id[]" value="<?= element('file_id', $item, 0) ?>"></td>
                    <td style="width:100px"><img src="/images/thumbs/<?= element('file_name', $item, 'plug.jpg') ?>" class="thumbnail"></td>
                    <td><a href="<?= '/admin/storage/card/' . element('name', $item, '') ?>"><?= element('original_name', $item, '') ?></a></td>
                    <td><?= date("d.m.Y H:i:s", strtotime(element('created', $item))) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?> 
<!--/view list-->
<!--view tile-->
<ul class="tile_galery storage_tile" data-view-type="tile">
    <?php foreach ($list as $item): ?>
        <li>
            <img src="/images/thumbs/<?= element('file_name', $item, 'plug.jpg') ?>" class="thumbnail">
            <input type="checkbox" name="file_id[]" value="<?= element('file_id', $item, 0) ?>">
            <a href="<?= '/admin/storage/card/' . element('name', $item, '') ?>" class="tile_galery__label" target="_blank"><?= element('original_name', $item, '') ?></a>
        </li>
    <?php endforeach; ?>
</ul>
<!--/view tile-->
<script type="text/template" id="item_view__list">
    <tr>
        <td style="width:20px"><input type="checkbox" name="file_id[]" value="{{=it.file_id}}"></td>
        <td style="width:100px"><img src="/images/thumbs/{{=it.file_name}}" class="thumbnail"></td>
        <td><a href="/admin/storage/card/{{=it.name}}">{{=it.original_name}}</a></td>
        <td>{{=it.created}}</td>
    </tr>
</script>
<script type="text/template" id="item_view__tile">
    <li>
        <img src="/images/thumbs/{{=it.file_name}}" class="thumbnail">
        <input type="checkbox" name="file_id[]" value="{{=it.file_id}}">
        <a href="/admin/storage/card/{{=it.name}}" class="tile_galery__label" target="_blank">{{=it.original_name}}</a>
    </li>
</script>
<!--docs template-->
<script type="text/template" id="item_view__list_docs">
    <tr>
    <td style="width:20px"><input type="checkbox" name="file_id[]" value="{{=it.file_id}}"></td>
    <td style="width:100px">
        <div class="file_icon">
            <img src="/images/document-icon.png" class="thumbnail">
            <i class="ext_{{=it.ext}}">{{=it.ext}}</i>
        </div>
    </td>
    <td><a href="/admin/storage/card/{{=it.name}}">{{=it.original_name}}</a></td>
    <td>{{=it.created}}</td>
    </tr>
</script>
<script type="text/template" id="item_view__tile_docs">
    <li>
        <div class="file_icon">
            <img src="/images/document-icon.png" class="thumbnail">
            <i class="ext_{{=it.ext}}">{{=it.ext}}</i>
        </div>
        <input type="checkbox" name="file_id[]" value="{{=it.file_id}}">
        <a href="/admin/storage/card/{{=it.name}}" class="tile_galery__label" target="_blank">{{=it.original_name}}</a>
    </li>
</script>
<!--/docs template-->