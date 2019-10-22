<div class="container-fluid">
    <div class="objectcard-header-wrapp">
        <header id="objectcard-header">
            <div class="row">
                <?php
                if (isset($breadcrumbs) && is_array($breadcrumbs) && $breadcrumbs): $_b_i = 0;
                    $_b_count = count($breadcrumbs);
                    ?>
                    <ol class="breadcrumb">
                        <?php foreach ($breadcrumbs as $it): $_b_i++; ?>
                            <?php if ($_b_i !== $_b_count): ?>
                                <li><a href="<?= array_get($it, 'url') ?>"><?= array_get($it, 'title') ?></a></li>
                            <?php else: ?>
                                <li class="active"><?= array_get($it, 'title') ?></li>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </ol>
                <?php endif; ?>
                <div class="col-md-6">
                    <h1><?= $title ?></h1>
                </div>
                <div class="col-md-6">
                    <nav>  
                        <button type="button" name="save_page_form" data-post-action="save_page_form" class="btn btn-success">Сохранить</button>
                    </nav>
                </div>
            </div>
        </header>
    </div>
    <div class="hpanel">
        <div class="panel-body">
            <form action="/admin/contacts/personal_infomation" method="POST" id="page-form">
                <div class="form-group">
                    <label class="control-label">Имя</label>
                    <input type="text" name="name" class="form-control" id="inputEmail3" placeholder="Ввести имя" value="<?= $exicute['name'] ?>"/>                
                </div>
                <div class="form-group">
                    <label class="control-label">Логин</label>
                    <input type="text" name="login" class="form-control" id="inputEmail3" placeholder="Ввести логин" value="<?= $exicute['login'] ?>"/>                
                </div>
                <div class="form-group">
                    <label class="control-label">Введите пароль</label>
                    <input class="form-control" onkeyup="check_password();" type="password" name="password" />
                </div>
                <div class="form-group">
                    <label class="control-label">Повторите пароль</label>
                    <input class="form-control" onkeyup="check_password();" type="password" name="password_2" />
                </div>
                <div class="form-group">
                    <label class="control-label">E-mail</label>
                    <input class="form-control" placeholder="Ввести e-mail" id="e_mail_repeat" onkeyup="email_check('e_mail_repeat', 'publick_personal_data');" type="text" name="e_mail_repeat" value="<?= $exicute['e_mail_repeat'] ?>" />
                </div>
                <div class="form-group">
                    <label class="control-label">Дополнительный E-mail</label>
                    <input class="form-control" placeholder="Ввести e-mail" id="e_mail_2" type="text" onkeyup="email_check('e_mail_2', 'publick_personal_data');" name="e_mail_2"  value="<?= $exicute['e_mail_2'] ?>" />
                </div>
                <div class="form-group">
                    <label class="control-label">Телефон</label>
                    <input type="phone" name="phone3" class="form-control" placeholder="телефон_1" value="<?= $exicute['phone3'] ?>"/>
                </div>
            </form>
        </div>
    </div>
    <script>
        (function () {
            $('[data-post-action="save_page_form"]').on('click', function (e) {
                console.assert(false, e)
                $('#page-form').submit();
            });
        }());
    </script>
</div>