<script src="/js/ckeditor/ckeditor.js" type="text/javascript"></script>
<script src="/js/ckeditor/config.js" type="text/javascript"></script>
<script src="/js/ckeditor/styles.js" type="text/javascript"></script>

<script src="/js/switch.js" type="text/javascript" charset="utf-8"></script>
<link rel="stylesheet" href="/css/switch.css" type="text/css" media="screen" charset="utf-8" />

<div class="tab-pane active" id="tab2">
    <div class="row">
        <br/>
        <form class="form-horizontal" role="form"  method="POST" action="/admin/objects/publish/<?=$object_id?>">
            <?foreach($categories as $key=>$val){?>
            <div class="form-group">
                <label for="inputEmail3" class="col-sm-2 control-label" <?if($meta['fullness'] && isset( $meta['fullness'][$key] ) && !$meta['fullness'][$key]){?>style="color: darkgray"<?}?>><?=$val?></label>
                <div class="col-sm-3">
                    <div style="margin: 3px 0 0 0;"><input type="checkbox" name="category_switch[<?=$key?>]" <?if($meta['publish'] && isset( $meta['publish'][$key] ) && $meta['publish'][$key]){?>checked="checked"<?}?> <?if($meta['fullness'] && isset( $meta['fullness'][$key] ) && !$meta['fullness'][$key]){?>disabled<?}?> /></div>
                </div>
            </div>
            <?}?>
            <div class="form-group">
                <div class="col-sm-offset-12 col-sm-10">
                    <input type="submit" class="btn btn-success" onclick="if(true) {return true} else {return false};" value="Сохранить"/>
                    <a class="btn btn-success" id="compite">Сохранить и опубликовать</a>
                    <input type="hidden" name="is_compited" value="" />
                </div>
            </div>
        </form>
    </div>
<script>
$(document).ready(function (){
    $(':checkbox').iphoneStyle( { checkedLabel: 'ДА', uncheckedLabel: 'НЕТ', width: 84 } );

    $("#compite").click(function() {
        $("input[name='is_compited']").val( '1' );
        $(".form-horizontal").submit();
    })
});
</script>

</div>
</div>
</div>
</div>