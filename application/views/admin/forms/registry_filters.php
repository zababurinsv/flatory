<div class="panel panel-default space_top_xs panel-body-cover">
    <div class="panel-heading">Фильтр</div>
    <div class="panel-body" style="padding: 0;">
        <form action="" class="form-horizontal fl-filter">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="name" class="col-sm-3 control-label">Поиск</label>
                        <div class="col-sm-9">
                            <input type="text" name="name" class="form-control" placeholder="Поиск"/>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <?php if(isset($categories) && $categories): ?>
                    <div class="form-group">
                        <label for="handbk_id" class="col-sm-3 control-label">Справочник</label>
                        <div class="col-sm-9">
                            <select name="handbk_id" class="form-control">
                                <option value="">Все</option>
                                <?php foreach ($categories as $cat): ?>
                                <option value="<?= array_get($cat, 'handbk_id') ?>"><?= array_get($cat, 'name') ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            <div class="row more_filters" style="display: none;">
                <div class="col-md-6">

                </div>
                <div class="col-md-6">

                </div>
            </div>
            <div class="row">
                <div class="col-md-6">

                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <div class="col-sm-12">
                            <button type="submit" class="btn btn-primary pull-right submit-form"><span class="glyphicon glyphicon-search"></span> Фильтр</button>
                            <a href="javascript:void(0)" class="btn btn-default pull-right space_right drop-form">Сброс</a>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

