<script type="text/javascript" src="/js/scripts.js"></script>
<script type="text/javascript" src="/js/jquery.inputmask.js"></script>
<!---->
<!--<div class="row">-->
<!--    <div class="page-header">-->
<!--        <h2>Контакты</h2>-->
<!--    </div>-->
<!---->
<!--    <form class="form-horizontal" action="/admin/contacts/contacts_edit" enctype="multipart/form-data" method="POST" >-->
<!--        <div class="form-group">-->
<!--            <label for="inputEmail3" class="col-sm-2 control-label">Название организации</label>-->
<!--            <div class="col-sm-10">-->
<!--                <input type="text" name="org_name" class="form-control" id="inputEmail3" placeholder="Ввести название организации" value="--><?//=$exicute['org_name']?><!--"/>-->
<!--            </div>-->
<!--        </div>-->
<!--        <div class="form-group">-->
<!--            <label for="inputEmail3" class="col-sm-2 control-label">Адрес</label>-->
<!--            <div class="col-sm-10">-->
<!--                <input type="text" name="adres" class="form-control" id="inputEmail3" placeholder="Ввести адрес" value="--><?//=$exicute['adres']?><!--"/>-->
<!--            </div>-->
<!--        </div>-->
<!--        <div class="form-group">-->
<!--            <label for="inputEmail3" class="col-sm-2 control-label">E-mail</label>-->
<!--            <div class="col-sm-10">-->
<!--                <input class="form-control" placeholder="Ввести e-mail" id="e_mail" onkeyup="$('#e_mail_repeat').val($(this).val()); email_check('e_mail','publick');" type="text" name="e_mail"  value="--><?//=$exicute['e_mail']?><!--" />-->
<!--            </div>-->
<!--        </div>-->
<!--        <div class="form-group">-->
<!--            <label for="inputEmail3" class="col-sm-2 control-label">Телефон</label>-->
<!--            <div class="col-sm-5">-->
<!--                <input type="phone" name="phone1" class="form-control" placeholder="телефон_1" value="--><?//=$exicute['phone1']?><!--"/>-->
<!--            </div>-->
<!--            <div class="col-sm-5">-->
<!--                <input type="phone" name="phone2" class="form-control" placeholder="телефон_2" value="--><?//=$exicute['phone2']?><!--"/>-->
<!--            </div>-->
<!--        </div>-->
<!--        <div class="form-group">-->
<!--            <label for="inputPassword3" class="col-sm-2 control-label">Реквизиты</label>-->
<!--            <div class="col-sm-10">-->
<!--                <textarea class="form-control" name="details" rows="3" placeholder="Дополнительная информация">--><?//=$exicute['details']?><!--</textarea>-->
<!--            </div>-->
<!--        </div>-->
<!--        <div class="form-group">-->
<!--            <div class="col-sm-offset-12 col-sm-10">-->
<!--                <input type="submit" class="btn btn-success" onclick="if(true){return true} else {return false}" value="Сохранить"/>-->
<!--            </div>-->
<!--        </div>-->
<!--    </form>-->
<!--</div>-->

<div class="row">
    <div class="page-header">
        <h2>Личные данные</h2>
    </div>

    <form class="form-horizontal" action="/admin/contacts/personal_infomation" enctype="multipart/form-data" method="POST" >
        <div class="form-group">
            <label for="inputEmail3" class="col-sm-2 control-label">Имя</label>
            <div class="col-sm-10">
                <input type="text" name="name" class="form-control" id="inputEmail3" placeholder="Ввести имя" value="<?=$exicute['name']?>"/>
            </div>
        </div>
        <div class="form-group">
            <label for="inputEmail3" class="col-sm-2 control-label">Логин</label>
            <div class="col-sm-10">
                <input type="text" name="login" class="form-control" id="inputEmail3" placeholder="Ввести логин" value="<?=$exicute['login']?>"/>
            </div>
        </div>
        <div class="form-group">
            <label for="inputEmail3" class="col-sm-2 control-label">Введите пароль</label>
            <div class="col-sm-10">
                <input class="form-control" onkeyup="check_password();" type="password" name="password" />
            </div>
        </div>
        <div class="form-group">
            <label for="inputEmail3" class="col-sm-2 control-label">Повторите пароль</label>
            <div class="col-sm-10">
                <input class="form-control" onkeyup="check_password();" type="password" name="password_2" />
            </div>
        </div>

        <div class="form-group">
            <label for="inputEmail3" class="col-sm-2 control-label">E-mail</label>
            <div class="col-sm-10">
                <input class="form-control" placeholder="Ввести e-mail" id="e_mail_repeat" onkeyup="email_check('e_mail_repeat','publick_personal_data');" type="text" name="e_mail_repeat" value="<?=$exicute['e_mail_repeat']?>" />
            </div>
        </div>
        <div class="form-group">
            <label for="inputEmail3" class="col-sm-2 control-label">Дополнительный E-mail</label>
            <div class="col-sm-10">
                <input class="form-control" placeholder="Ввести e-mail" id="e_mail_2" type="text" onkeyup="email_check('e_mail_2','publick_personal_data');" name="e_mail_2"  value="<?=$exicute['e_mail_2']?>" />
            </div>
        </div>

        <div class="form-group">
            <label for="inputEmail3" class="col-sm-2 control-label">Телефон</label>
            <div class="col-sm-5">
                <input type="phone" name="phone3" class="form-control" placeholder="телефон_1" value="<?=$exicute['phone3']?>"/>
            </div>
        </div>
        <div class="form-group">
            <div class="col-sm-offset-12 col-sm-10">
                <input type="submit" class="btn btn-success" onclick="if(true){return true} else {return false}" value="Сохранить"/>
            </div>
        </div>
    </form>
</div>