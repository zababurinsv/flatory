<div class="row">
    <div class="col-md-8">
        <form action="" accept-charset="utf-8" method="get" class="form-horizontal form_filter" role="form">
            <div class="form-group">
                <label for="name" class="col-sm-2 control-label">Название</label>
                <div class="col-sm-10">
                    <input type="text" name="name" class="form-control" value="">
                    <script>
                        (function ($, FlHelper){
                            var f = 'name';
                            var v = FlHelper.arr.get(FlHelper.Get(), f);
                            $('[name="'+ f +'"]').val(v);
                        }(jQuery, FlHelper));
                    </script>
                </div>
            </div>
            <div class="form-group">
                <label for="status_label" class="col-sm-2 control-label">Статус</label>
                <div class="col-sm-10">
                    <select name="status" class="form-control">
                        <option value="" selected="selected">Не выбрано</option>
                        <option value="<?= MY_Model::STATUS_ACTIVE ?>">Опубликовано</option>
                        <option value="<?= MY_Model::STATUS_NOT_PUBLISHED ?>">Черновик</option>
                    </select>
                    <script>
                        (function ($, FlHelper){
                            var f = 'status';
                            var v = FlHelper.arr.get(FlHelper.Get(), f);
                            $('[name="'+ f +'"]').find('[value="'+ v +'"]').attr('selected', 'selected');
                        }(jQuery, FlHelper));
                    </script>
                </div>
            </div>
            <div class="form-group">
                <label for="handbk_related" class="col-sm-2 control-label">Справочник</label>
                <div class="col-sm-10">
                    <select name="handbk_related" class="form-control">
                        <option value="" selected="selected">Не выбрано</option>
                        <option value="<?= MY_Model::STATUS_ACTIVE ?>">Да</option>
                        <option value="<?= MY_Model::STATUS_DELETED?>">Нет</option>
                    </select>
                    <script>
                        (function ($, FlHelper){
                            var f = 'handbk_related';
                            var v = FlHelper.arr.get(FlHelper.Get(), f);
                            $('[name="'+ f +'"]').find('[value="'+ v +'"]').attr('selected', 'selected');
                        }(jQuery, FlHelper));
                    </script>
                </div>
            </div>
            <div class="form-group">
                <div class="col-sm-offset-2 col-sm-11">
                    <input type="submit" name="" value="Фильтр" class="btn btn-sm btn-primary space_left">
                </div>
            </div>
        </form>
        <hr>
        <a href="/admin/glossary/add/" class="btn btn-sm btn-success"><span class="glyphicon glyphicon-plus"></span> Добавить</a>
    </div>
    <div class="col-md-4">
        <?php if (isset($pagination)): ?> 
            <div class="pull-right">
                <?= $pagination ?>
            </div>
        <?php endif; ?>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <?php if (!empty($list)): ?>
            <table class="table">
                <thead>
                    <tr>
                        <th><a href="javascript:void(0)" class="sort_link" data-by="glossary_id">ID</a></th>
                        <th><a href="javascript:void(0)" class="sort_link" data-by="name">Название</a></th>
                        <th><a href="javascript:void(0)" class="sort_link" data-by="status">Статус</a></th>
                        <th><a href="javascript:void(0)" class="sort_link" data-by="parent_name">Родитель</a></th>
                        <th>Связь со справочником</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($list as $item): ?>
                        <tr>
                            <td><?= element('glossary_id', $item) ?></td>
                            <td><a href="/admin/glossary/edit/<?= element('glossary_id', $item) ?>"><?= element('name', $item) ?></a></td>
                            <td><?= (int) element('status', $item) === MY_Model::STATUS_NOT_PUBLISHED ? 'Черновик' : 'Опубликовано' ?></td>
                            <td><?= element('parent_name', $item, '-') ?></td>
                            <td><?= element('relation', $item, '-') ?></td>
                            <td>
                                <button type="button" title="Удалить" class="btn btn-sm btn-danger d_it pull-right" data-id="<?= element('glossary_id', $item) ?>" data-name="<?= element('name', $item) ?>">
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



