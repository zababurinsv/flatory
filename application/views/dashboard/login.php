<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8"/>
        <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
        <meta name="description" content=""/>
        <meta name="author" content=""/>
        <title><?= $title ?></title>
        <link href="/vendor/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet"/>
        <link href="/css/dashboard/main.css" rel="stylesheet"/>
    </head>
    <body>
        <div class="container">
            <div class="no-auth-wrapper">
                <a href="/" class="no-auth-wrapper--logo">
                    <img src="/images/new/logo.png" alt="logo">
                </a>
                <form action="" method="post" class="form" role="form">
                    <h3 class="space_top_xs space_bottom">Вход</h3>
                    <?php if (isset($error) && isset($error)): ?>
                        <div class="space_bottom">
                            <b class="text-danger"><?= $error ?></b>
                        </div>
                    <?php endif; ?>
                    <div class="form-group">
                        <input class="form-control" name="login" placeholder="Логин" type="login" />
                    </div>
                    <div class="form-group">
                        <input class="form-control" name="password" placeholder="Пароль" type="password" />
                    </div>
                    <div class="form-group">
                        <button class="btn btn-lg btn-primary" type="submit">Войти</button>
                    </div>
                </form>
            </div>
        </div>
    </body>
</html>