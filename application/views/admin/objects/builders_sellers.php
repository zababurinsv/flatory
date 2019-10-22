<div class="row">
    <div class="col-sm-12">
        <form action="" method="post" class="form-horizontal">
            <div class="form-group" data-name="organization">
                <div class="input-group">
                    <select name="organization_id[]" class="form-control">
                        <option value="">Не выбрано</option>
                        <?php foreach ($organizations as $o): ?>
                        <option value="<?= element('organization_id', $o) ?>"><?= element('name', $o) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <div class="input-group-btn">
                        <button type="button" class="btn btn-danger del_item" style="display: none;"><span class="glyphicon glyphicon-minus"></span></button>
                        <button type="button" class="btn btn-default add_item"><span class="glyphicon glyphicon-plus"></span></button>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <button type="submit" class="btn btn-sm btn-success">Сохранить</button>
            </div>
        </form>
        <?php if($object_organizations): ?>
        <script>
            FlRegister.set('object_organizations', <?= json_encode($object_organizations) ?>);
        </script>
        <?php endif; ?>
    </div>
</div>

