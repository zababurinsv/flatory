<div class="page-header">
    <h2>Статьи</h2>
</div>
<div class="row">
    <div class="col-md-6">
        <a href="/admin/add_article/" class="btn btn-primary">Добавить статью +</a>
    </div>
</div>
<div class="row">
    <br/>
    <? foreach ($article->result() as $row){?>
    <div class="col-sm-12">
        <div class="col-sm-2">
            <img height="120px" src="<?=$row->image?>" alt="..."/>
        </div>
        <div class="col-sm-9">
            <h3><?=$row->name?></h3>
            <small class="text-muted">Опубликовано: <?=date("d.m.Y H:i:s", strtotime($row->date))?></small>
            <p><?=$row->anons?></p>  
            
            <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                            <h4 class="modal-title" id="myModalLabel">Подтверждение действия</h4>
                        </div>
                        <div class="modal-body">
                            Вы действительно хотите удалить эту статью?
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Отменить</button>
                            <a href="/admin/delete_article/<?=$row->id?>" class="btn btn-danger">Удалить</a>
                        </div>
                    </div><!-- /.modal-content -->
                </div><!-- /.modal-dialog -->
            </div><!-- /.modal -->
            <p>
            <a href="/admin/article/edit/<?=$row->id?>" class="btn btn-primary" role="button">Изменить</a>
            <a href="#" onclick="$('.modal-footer a').attr('href','/admin/delete_article/<?=$row->id?>')" class="btn btn-danger" role="button" data-toggle="modal" data-target="#myModal">Удалить</a> 
            </p>  
        </div>     
    </div>
    <?}?>
</div>
</div>
</div>