<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8"/>
        <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
        <meta name="description" content=""/>
        <meta name="author" content=""/>
    
        <title>Flatory-login-admin</title>
    
        <!-- Bootstrap core CSS -->
        <link href="/css/bootstrap.css" rel="stylesheet"/>
        <link href="/css/style.css" rel="stylesheet"/>
        <!-- Add custom CSS here -->
        <link href="/css/simple-sidebar.css" rel="stylesheet"/>
        <link href="/font-awesome/css/font-awesome.min.css" rel="stylesheet"/>
    </head>
    <body>
        <div class="container">
            <div class="row">
                <div class="col-xs-12 col-sm-12 col-md-4 well well-sm">
                    <legend> Вход</legend>
                    <form action="" method="post" class="form" role="form">
                        <label style="color: red;"><?=(isset($error)&&isset($error))?$error:''?></label>
                        <input class="form-control" name="login" placeholder="E-mail*" type="login" />
                        <input class="form-control" name="password" placeholder="Пароль*" type="password" />
                        <button class="btn btn-lg btn-primary btn-block" type="submit">Войти</button>
                    </form>
                </div>
            </div>
        </div>
    </body>
</html>