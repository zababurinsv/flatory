<div class="hpanel panel-body-cover">
    <div class="panel-heading hbuilt">
        Фильтр
    </div>
    <div class="panel-body">
        <form action="" class="fl-filter">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="control-label">Поиск по названию или id</label>
                        <input type="text" name="name_like" class="form-control" data-autocomplete="registry" autocomplete="off" placeholder="Поиск по названию или id">
                    </div>
                    <?php if ($status): ?>
                        <div class="form-group">
                            <div>
                                <label for="status" class="control-label">Статус</label>
                            </div>
                            <div class="space_left" style="min-height: 34px;">
                                <?php foreach ($status as $val => $it): ?>
                                    <label class="checkbox-inline">
                                        <input type="checkbox" name="status[]" value="<?= $val ?>">
                                        <span class="status status-<?= array_get($it, 'alias', 'danger') ?>" title="<?= $_title = array_get($it, 'title', 'Неизвестный статус') ?>"></span>
                                        <span><?= $_title ?></span>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="col-md-6">
                    <?php if (isset($categories) && $categories): ?>
                        <div class="form-group">
                            <label for="handbk_id" class="control-label">Справочник</label>
                            <select name="handbk_id" class="form-control">
                                <option value="">Все</option>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?= array_get($cat, 'handbk_id') ?>"><?= array_get($cat, 'name') ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">

                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary pull-right submit-form"><span class="glyphicon glyphicon-search"></span> Фильтр</button>
                        <a href="javascript:void(0)" class="btn btn-default pull-right space_right drop-form">Сброс</a>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

