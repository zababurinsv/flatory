<script src="/js/ckeditor/ckeditor.js" type="text/javascript"></script>
<script src="/js/ckeditor/config.js" type="text/javascript"></script>
<script src="/js/ckeditor/styles.js" type="text/javascript"></script>

<div class="row">
    <div class="page-header">
        <h2>Добавление статьи</h2>
    </div>

    <div class="tabbable"> <!-- Only required for left/right tabs -->
        <ul class="nav nav-tabs">
           <li <?if($this->uri->segment(3)=='add'){?> class="active"<?}?>><a>Статья</a></li>
           <li <?if($this->uri->segment(3)=='seo'){?> class="active"<?}?>><a>Мета-теги</a></li>
        </ul>
        <div class="tab-content">
            <div class="tab-pane active" id="tab1">
                <br/>
                <form class="form-horizontal" action="/admin/add_article/" enctype="multipart/form-data" method="POST" >
        <div class="form-group">
            <label for="inputEmail3" class="col-sm-2 control-label">Заголовок</label>
            <div class="col-sm-10">
                <input class="form-control" name="name" id="name" type="text" class="inp-form s_name" value="" />
            </div>
        </div>
        <div class="form-group">
            <label for="inputEmail3" class="col-sm-2 control-label">Анонс</label>
            <div class="col-sm-10">
                <textarea id="anons" name="anons" class="form-control" rows="3"></textarea>
            </div>
        </div>
        <div class="form-group">
            <label for="inputPassword3" class="col-sm-2 control-label">Текст новости</label>
            <div class="col-sm-10">
                <textarea id="description" class="ckeditor" name="content" cols="50" rows="15"></textarea>
            </div>
        </div>
        <div class="form-group">
            <label for="inputPassword3" class="col-sm-2 control-label">Фото</label>
            <div class="col-sm-10">
                <div class="input-group">
                    <input type="file" name="file" class="form-control" id="inputPassword3" placeholder=""/>
                </div>
            </div>
        </div>
        <div class="form-group">
            <div class="col-sm-offset-12 col-sm-10">
                <input type="submit" class="btn btn-success" onclick="if(save_news()){return true} else {return false}" value="Опубликовать"/>
            </div>
        </div>
    </form>
            </div>
        </div>
    </div>
</div>
