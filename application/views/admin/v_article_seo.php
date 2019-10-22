<script src="/js/ckeditor/ckeditor.js" type="text/javascript"></script>
<script src="/js/ckeditor/config.js" type="text/javascript"></script>
<script src="/js/ckeditor/styles.js" type="text/javascript"></script>

<div class="row">
    <div class="page-header">
        <h2>Редактирование статьи</h2>
    </div>

    <div class="tabbable"> <!-- Only required for left/right tabs -->
        <ul class="nav nav-tabs">
           <li <?if($this->uri->segment(3)=='edit'){?> class="active"<?}?>><a <?='href="/admin/article/edit/'.$object_id.'"'?> >Статья</a></li>
           <li <?if($this->uri->segment(3)=='seo'){?> class="active"<?}?>><a <?='href="/admin/article/seo/'.$object_id.'"'?> >Мета-теги</a></li>
        </ul>
        <div class="tab-content">
            <div class="tab-pane active" id="tab2">
                <br/>
                <form class="form-horizontal" action="/admin/article/seo/<?=$object_id?>" enctype="multipart/form-data" method="POST" >
                    <div class="form-group">
                        <label for="name" class="col-sm-2 control-label">Заголовок<br/>(title)</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" id="name" placeholder="" name="title" value="<?=(isset($title)) ? htmlspecialchars($title,ENT_QUOTES):''?>" />
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="inputText3" class="col-sm-2 control-label">Описание<br/>(description)</label>
                        <div class="col-sm-10">
                        <textarea class="form-control" name="description" rows="3"><?=(isset($description))?$description:''?></textarea>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="inputText3" class="col-sm-2 control-label">Ключевые слова<br/>(keywords)</label>
                        <div class="col-sm-10">
                        <textarea class="form-control" name="keywords" rows="3"><?=(isset($keywords))?$keywords:''?></textarea>
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
</div>