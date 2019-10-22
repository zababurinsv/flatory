<script src="/js/ckeditor/ckeditor.js" type="text/javascript"></script>
<script src="/js/ckeditor/config.js" type="text/javascript"></script>
<script src="/js/ckeditor/styles.js" type="text/javascript"></script>

<div class="row">
    <div class="page-header">
        <h2>Редактирование новости</h2>
    </div>

    <?foreach($news as $val){?>
    <div class="tabbable"> <!-- Only required for left/right tabs -->
        <ul class="nav nav-tabs">
           <li <?if($this->uri->segment(3)=='edit'){?> class="active"<?}?>><a <?='href="/admin/news/edit/'.$val->id.'"'?> >Новость</a></li>
           <li <?if($this->uri->segment(3)=='seo'){?> class="active"<?}?>><a <?='href="/admin/news/seo/'.$val->id.'"'?> >Мета-теги</a></li>
        </ul>
        <div class="tab-content">
            <div class="tab-pane active" id="tab1">
                <br/>
                <form class="form-horizontal" action="/admin/news/edit/<?=$val->id?>" enctype="multipart/form-data" method="POST" >
                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-2 control-label">Заголовок</label>
                        <div class="col-sm-10">
                            <input class="form-control" name="name" id="name" type="text" class="inp-form s_name" value="<?=htmlspecialchars($val->name,ENT_QUOTES)?>" />
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-2 control-label">Анонс</label>
                        <div class="col-sm-10">
                            <textarea id="anons" name="anons" class="form-control" rows="3"><?=$val->anons?></textarea>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="inputPassword3" class="col-sm-2 control-label">Текст новости</label>
                        <div class="col-sm-10">
                            <textarea id="description" class="ckeditor" name="content" cols="50" rows="15"><?=$val->content?></textarea>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="inputPassword3" class="col-sm-2 control-label">Загруженное фото</label>
                        <div class="col-sm-10">
                            <div class="input-group">
                                <img height="200px" src="<?=$val->image?>" />
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="inputPassword3" class="col-sm-2 control-label">Новое фото</label>
                        <div class="col-sm-10">
                            <div class="input-group">
                                <input type="file" name="file" class="form-control" id="inputPassword3" placeholder=""/>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-sm-offset-12 col-sm-10">
                            <input type="submit" class="btn btn-success" onclick="if(save_news()){return true} else {return false}" value="Сохранить"/>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <?}?>
</div>