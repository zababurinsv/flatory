<style>
.shadow:hover{
    box-shadow: 0 0 10px rgba(0,0,0,0.5);
}
.shadow{
    cursor:pointer;
}
.text_image{
    background-image: url(/images/news_bg.png);
    bottom: 0px;
    padding: 5px;
    margin: 0px;
    position: absolute;
    bottom: 0px;
    color: #000;
    font: 14px Arial, Tahoma, Geneva, Helvetica, sans-serif;
    text-align: left;
}
</style>

<div class="row">
        <div id="object_1" class="col-sm-6 col-md-4" style="width:180px;text-align: center;">
            <div class="thumbnail shadow" style="height:225px;width: 160px">
                <div style="height:215px;width: 140px;text-align: center;vertical-align: middle;display: table-cell;position:relative">
                    <img id="image_object_1" style="height:215px;width: 140px;" src="<?=(isset($data1['image']))?$data1['image']:'/images/news1.png'?>" alt=""/>
                    <div contenteditable="true" id="text1" style="max-height: 215px;width: 100%;overflow-y: auto;;" class="text_image"><?=(isset($data1['text']))?$data1['text']:'Введите текст карточки'?></div>
                </div>
            </div>
            <label>Формат (140x215)px</label>
        </div>
        <div id="object_2" class="col-sm-6 col-md-4" style="width:326px;text-align: center;">
            <div class="thumbnail shadow" style="height: 225px;width: 316px;">
                <div style="height: 215px;width: 286px;text-align: center;vertical-align: middle;display: table-cell;position:relative">
                    <img id="image_object_2" style="height: 215px;width: 286px;" src="<?=(isset($data2['image']))?$data2['image']:'/images/news2.png'?>" alt=""/>
                    <div contenteditable="true" id="text2" style="max-height: 215px;width: 100%;overflow-y: auto;;" class="text_image"><?=(isset($data2['text']))?$data2['text']:'Введите текст карточки'?></div>
                </div>
            </div>
            <label>Формат (286x215)px</label>
        </div>
        <div id="object_3" class="col-sm-6 col-md-4" style="width: 472px;text-align: center;">
            <div class="thumbnail shadow" style="height: 225px;width: 462px;">
                <div style="height: 215px;width: 432px;text-align: center;vertical-align: middle;display: table-cell;position:relative">
                    <img id="image_object_3" style="height: 215px;width: 432px;" src="<?=(isset($data3['image']))?$data3['image']:'/images/news3.png'?>" alt=""/>
                    <div contenteditable="true" id="text3" style="max-height: 215px;width: 100%;overflow-y: auto;" class="text_image"><?=(isset($data3['text']))?$data3['text']:'Введите текст карточки'?></div>
                </div>
            </div>
            <label>Формат (432x215)px</label>
        </div>
</div>
<div>
    <form method="POST" id="save_cart" action="/admin/cart/save_carts/<?=$id?>" class="form-horizontal"  enctype="multipart/form-data">
        <input type="hidden" name="id" value="<?=$id?>" />
        <input type="file" id="file_object_1" style="display: none;" name="file_1" accept="image/jpeg,image/png,image/gif"/>
        <input type="file" id="file_object_2" style="display: none;" name="file_2" accept="image/jpeg,image/png,image/gif"/>
        <input type="file" id="file_object_3" style="display: none;" name="file_3" accept="image/jpeg,image/png,image/gif"/>
        <input type="hidden" id="text_1" name="text_1" value="" />
        <input type="hidden" id="text_2" name="text_2" value="" />
        <input type="hidden" id="text_3" name="text_3" value="" />
        <a href="#" onclick="form_action();return false;" class="btn btn-primary" role="button">Сохранить карточки</a>
    </form>
</div>

<script>
$(function() {
    status = 'false';
    
    $(".text_image").click(function(){
        status = 'true';
        obj = $(this);
        $(this).keypress(function(){
            count = obj.text().length
            if(count>300){
                return false;
            }
        });
        $(this).focusout(function(){
            status = 'false';
        })
    });
    
    $(".col-sm-6").click(function(){
        if (status == 'false'){
            $("#file_"+$(this).attr('id')).click();
            id = $(this).attr('id')
            document.getElementById('file_'+$(this).attr('id')).addEventListener('change', handleFileSelect, false);
            $('#object_'+$(this).attr('id').substr(-1)).children('div').css('border','2px solid #dddddd');
        }
    });
});

function handleFileSelect(evt) {
    var files = evt.target.files;
    for (var i = 0, f; f = files[i]; i++) {
        if (!f.type.match('image.*')) {
            continue;
        }
        
        var reader = new FileReader();
        reader.onload = (function(theFile) {
            return function(e) {
                byte = (e.target.result.length - 814) / 1.37;
                $('#image_'+id).attr('src',e.target.result);
            };
        })(f);
        reader.readAsDataURL(f);
    }
}

function getImageSizeInBytes(imgURL) {
    var request = new XMLHttpRequest();
    request.open("HEAD", imgURL, false);
    request.send(null);
    var headerText = request.getAllResponseHeaders();
    var re = /Content\-Length\s*:\s*(\d+)/i;
    re.exec(headerText);
    return parseInt(RegExp.$1);
}

function form_action(){
    for(var i=1;i<4;i++){
        $('#text_'+i).val($('#text'+i).text());
    }
    $("#save_cart").submit();
}
</script>