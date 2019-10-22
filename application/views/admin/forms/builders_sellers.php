<script src="/js/ckeditor/ckeditor.js" type="text/javascript"></script>
<script src="/js/ckeditor/config.js" type="text/javascript"></script>
<script src="/js/ckeditor/styles.js" type="text/javascript"></script>
<div class="row">
    <form class="form-horizontal" action="/admin/users/<?= $action_suffix ?>/<?= $type ?>/<?= element('id', $item, '') ?>" method="POST" >
        <div class="form-group">
            <label for="inputEmail3" class="col-sm-2 control-label">Название компании <span class="text-danger">*</span></label>
            <div class="col-sm-10">
                <input type="text" name="company_name" class="form-control" id="inputEmail3" placeholder="Ввести название организации" value="<?= element('company_name', $item, '') ?>"/>
            </div>
        </div>
        <div class="form-group">
            <label for="inputEmail3" class="col-sm-2 control-label">Адрес</label>
            <div class="col-sm-10">
                <input type="text" name="adres" class="form-control" id="inputEmail3" placeholder="Ввести адрес" value="<?= element('adres', $item, '') ?>"/>
            </div>
        </div>
        <div class="form-group">
            <label for="inputEmail3" class="col-sm-2 control-label">Веб-сайт</label>
            <div class="col-sm-10">
                <input type="url" name="sait" class="form-control" id="inputEmail3" placeholder="http://" value="<?= element('sait', $item, '') ?>"/>
            </div>
        </div>
        <div class="form-group">
            <label for="inputEmail3" class="col-sm-2 control-label">Телефоны</label>
            <div class="col-sm-5">
                <input type="phone" name="phone1" class="form-control" placeholder="телефон_1" value="<?= element('phone1', $item, '') ?>"/>
            </div>
            <div class="col-sm-5">
                <input type="phone" name="phone2" class="form-control" placeholder="телефон_2" value="<?= element('phone2', $item, '') ?>"/>
            </div>
        </div>
        <div class="form-group">
            <label for="inputPassword3" class="col-sm-2 control-label">Инфо</label>
            <div class="col-sm-10">
                <textarea class="form-control" name="info" rows="3" placeholder="Дополнительная информация о продавце"><?= element('info', $item, '') ?></textarea>
            </div>
        </div>
        <div class="form-group">
            <label for="inputPassword3" class="col-sm-2 control-label">Логотип</label>
            <div class="col-sm-10">
                <?= $image_simple_upload ?>
            </div>
        </div>
        <div class="form-group">
            <div class="col-sm-offset-12 col-sm-10">
                <input type="submit" class="btn btn-success" value="Сохранить"/>
            </div>
        </div>
    </form>
</div>
</div>