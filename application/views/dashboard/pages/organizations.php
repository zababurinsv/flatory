<div class="row">
    <div class="col-md-12">
        <?= $filters ?>
    </div>
</div>
<div class="row">
    <div class="col-md-6"><a href="<?= $path ?>add/" class="btn btn-sm btn-success"><span class="glyphicon glyphicon-plus"></span> Добавить</a></div>
    <div class="col-md-6"><div class="pull-right"><?= $pagination ?></div></div>
</div>
<div class="row">
    <div class="col-md-12">
        <?php if (!empty($list)): ?>
            <table class="table">
                <thead>
                    <tr>
                        <th><a href="javascript:void(0)" class="sort_link" data-by="organization_id">ID</a></th>
                        <th><a href="javascript:void(0)" class="sort_link" data-by="name">Название</a></th>
                        <th>Тип организации</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($list as $item): ?>
                        <tr>
                            <td><?= element('organization_id', $item) ?></td>
                            <td><a href="/admin/organizations/edit/<?= element('organization_id', $item) ?>"><?= element('name', $item) ?></a></td>
                            <td><?= element('organization_types', $item, '-') ?></td>
                            <td>
                                <button type="button" title="Удалить" class="btn btn-sm btn-danger d_it pull-right" data-id="<?= element('organization_id', $item) ?>" data-name="<?= element('name', $item) ?>">
                                    <span class="glyphicon glyphicon-trash"></span>
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="alert alert-info">Упс! Ничего не найдено.</div>
        <?php endif; ?>
    </div>
</div>
<script>
    (function() {
        var app = {
            targets: {
                delete: '.d_it'
            },
            init: function() {
                app.setUpListeners();
            },
            setUpListeners: function() {
                $(this.targets.delete).off('click').on('click', app.delete);
            },
            delete: function(e) {
                if (confirm('Уверены, что хотите удалить безвозвратно "' + $(this).data('name') + '"?'))
                    location.href = location.protocol + '//' + location.hostname + '/admin/organizations/delete/' + $(this).data('id');
            },
            publ: {
            }
        };
        app.init();
        return app.publ;
    }());
</script>