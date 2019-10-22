function edit_album_name(this_is,id,table){
    old_name = this_is.prev().prev();
    name = this_is.next().val();
    span = old_name.find('span').html();
    old = old_name.html().substr(0,old_name.html().indexOf('<span>')-1);
    $.post('/admin/objects/rename_album',{album_name:name,id:id,table:table}).success(function(){
        old_name.html(name+' <span>'+span+'</span>');
    })
}

function add_new_upload(i){
    d=i+1;
    
    var new_object = $("#upl_"+i).clone().attr("id", "upl_"+d);
    new_object.find('input[name=file_'+i+']').attr('name','file_'+d);
    new_object.find('input[name=comment_'+i+']').attr('name','comment_'+d);
    new_object.find('input[name=comment_'+d+']').val('');
    new_object.find('#comment_'+i).attr('id','comment_'+d);
    new_object.find('#label_'+i).attr('id','label_'+d);
    new_object.find('#label_'+d).attr('for','file_'+d).text('Фото '+d);
    new_object.find('#file_'+i).attr('id','file_'+d);
    new_object.find('button').attr('onclick','add_new_upload('+d+')');
    new_object.appendTo("#uploader");
    
    $('#upl_'+i).find('button').attr('onclick','$("#upl_'+i+'").remove()');
    $('#upl_'+i).find('.glyphicon.glyphicon-plus').attr('class','glyphicon glyphicon-minus');
}

function add_gallery_upload(i,album){
    d=i+1;
    
    var new_object = $("#upl_"+album+"_"+i).clone().attr("id", "upl_"+album+'_'+d);
    new_object.find('input[name=comment_'+album+'_'+i+']').attr('name','comment_'+album+'_'+d);
    new_object.find('input[name=file_'+album+'_'+i+']').attr('name','file_'+album+'_'+d);
    new_object.find('#label_'+album+'_'+i).attr('id','label_'+album+'_'+d);
    new_object.find('#label_'+album+'_'+d).attr('for','file_'+album+'_'+d).text('Фото '+d);
    new_object.find('#comment_'+album+'_'+i).attr('id','comment_'+album+'_'+d);
    new_object.find('#file_'+album+'_'+i).attr('id','file_'+album+'_'+d);
    new_object.find('button').attr('onclick','add_gallery_upload('+d+','+album+')');
    new_object.appendTo("#uploader_"+album);
    
    $('#upl_'+album+'_'+i).find('button').attr('onclick','$("#upl_'+album+'_'+i+'").remove()');
    $('#upl_'+album+'_'+i).find('.glyphicon.glyphicon-plus').attr('class','glyphicon glyphicon-minus');
}

function add_new_floor(i){
    d=i+1;
    
    var new_object = $("#floor_"+i).clone().attr("id", "floor_"+d);
    new_object.find('select[name=floor_'+i+']').attr('name','floor_'+d);
    new_object.find('select[name=sec_'+i+']').attr('name','sec_'+d);
    new_object.find('input[name=file_'+i+']').attr('name','file_'+d);
    new_object.find('input[type=button]').attr('onclick','add_new_floor('+d+')');
    new_object.appendTo("#floors");
    
    $('#floor_'+i).find('input[type=button]').attr('onclick','$("#floor_'+i+'").remove()').val('-');
}

function add_metro(i){
    d=i+1;
    var new_object = $("#mg_metro_"+i).clone().attr("id", "mg_metro_"+d);
    new_object.find('#metro_'+i).attr('id','metro_'+d);
    new_object.find('#metro_'+d).val('');
    new_object.find('button[type=button]').attr('onclick','add_metro('+d+')');
    new_object.appendTo("#m_metro_all");

    var new_metro = $("#mg_type_metro_"+i).clone().attr("id", "mg_type_metro_"+d);
    new_metro.find('.logo').attr('src','/images/metro/no_metro.png');

    new_metro.find("#m_distance_"+i).attr('id',"m_distance_"+d);
    new_metro.find("#m_distance_to_metro_"+i).attr('id',"m_distance_to_metro_"+d);
    new_metro.find("#m_distance_to_metro_"+d).val('0');

    new_metro.find("#m_distance_min_"+i).attr('id',"m_distance_min_"+d);
    new_metro.find("#m_distance_to_metro_min_"+i).attr('id',"m_distance_to_metro_min_"+d);
    new_metro.find("#m_distance_to_metro_min_"+d).val('0');

    new_metro.find("#m_distance_car_"+i).attr('id',"m_distance_car_"+d);
    new_metro.find("#m_distance_to_metro_car_"+i).attr('id',"m_distance_to_metro_car_"+d);
    new_metro.find("#m_distance_to_metro_car_"+d).val('0');

    new_metro.appendTo("#type_metro_m");

    $('#mg_metro_'+i).find('button').attr('onclick','$("#mg_metro_'+i+'").remove();$("#m_distance_'+i+'").remove();$("#m_distance_min_'+i+'").remove();$("#mg_type_metro_'+i+'").remove()');
    $('#mg_metro_min'+i).find('button').attr('onclick','$("#m_metro_min'+i+'").remove();$("#m_distance_min'+i+'").remove()');
    $('#mg_metro_'+i).find('.glyphicon.glyphicon-plus').attr('class','glyphicon glyphicon-minus');
}

function insert_icon(el){
    var metro = $(el).children('input').val();
    var menu_metro = $(el).parent('.all_metro').prev('.menu_metro');
    menu_metro.children('input').val(metro);
    menu_metro.children('img').attr('src','/images/metro/'+metro+'.png');
    $(el).parent('.all_metro').hide();
}
function add_icons_metro(el){
    $('.all_metro').hide();
    $(el).next('div').show();
}
function add_building(i){
    d=i+1;
    var new_object = $("#building_"+i).clone().attr("id", "building_"+d);
    new_object.find('#building_'+i).attr('id','building_'+d);
    new_object.find('#building_'+d+' :first').attr("selected", "selected");
    new_object.find('button[type=button]').attr('onclick','add_building('+d+')');
    new_object.appendTo("#building_all");
    
    $('#building_'+i).find('button').attr('onclick','$("#building_'+i+'").remove()');
    $('#building_'+i).find('.glyphicon.glyphicon-plus').attr('class','glyphicon glyphicon-minus');
}

function find_icon_metro(el){
    var city = $(el).parents('.input-group').attr('id').slice(0,2);
    var num = $(el).parents('.input-group').attr('id').slice(14);
    var metro_name;
    var metro_img;
    if(city == 'mg'){
        metro_name = $('#mg_metro_'+num).find('input').val();
        metro_img = $("#mg_type_metro_"+num).find('.logo');
    } else {
        metro_name = $('#mr_metro_'+num).find('input').val();
        metro_img = $("#mr_type_metro_"+num).find('.logo');
    }
    $.post('/admin/objects/find_icon_metro',{
            'metro' : metro_name
        }
    ).success(function(data){
        if(data != ''){
            metro_img.attr('src','/images/metro/'+data+'.png');
            $(el).next('div').children('.menu_metro').find('input').val(data);
        }else{
            metro_img.attr('src','/images/metro/no_image.png');
            $(el).next('div').children('.menu_metro').find('input').val('');
        }
    });
}

function add_building_lot(i){
    d=i+1;
    var new_object = $("#building_lot_"+i).clone().attr("id", "building_lot_"+d);
    new_object.find('#building_lot_'+i).attr('id','building_lot_'+d);
    new_object.find('#building_lot_'+d+' :first').attr("selected", "selected");
    new_object.find('button[type=button]').attr('onclick','add_building_lot('+d+')');
    new_object.appendTo("#building_lot_all");
    
    $('#building_lot_'+i).find('button').attr('onclick','$("#building_lot_'+i+'").remove()');
    $('#building_lot_'+i).find('.glyphicon.glyphicon-plus').attr('class','glyphicon glyphicon-minus');
}

function add_mr_metro(i){
    d=i+1;
    var new_object = $("#mr_metro_"+i).clone().attr("id", "mr_metro_"+d);
    new_object.find('#mr_metro_'+i).attr('id','mr_metro_'+d);
    new_object.find('#mr_metro_'+d).val('');
    new_object.find('button[type=button]').attr('onclick','add_mr_metro('+d+')');
    new_object.appendTo("#mr_metro_all");

    var new_metro = $("#mr_type_metro_"+i).clone().attr("id", "mr_type_metro_"+d);
    new_metro.find('.logo').attr('src','/images/metro/no_metro.png');

    new_metro.find("#mr_distance_"+i).attr('id',"mr_distance_"+d);
    new_metro.find("#mr_distance_to_metro_"+i).attr('id',"mr_distance_to_metro_"+d);
    new_metro.find("#mr_distance_to_metro_"+d).val('0');

    new_metro.find("#mr_distance_min_"+i).attr('id',"mr_distance_min_"+d);
    new_metro.find("#mr_distance_to_metro_min_"+i).attr('id',"mr_distance_to_metro_min_"+d);
    new_metro.find("#mr_distance_to_metro_min_"+d).val('0');

    new_metro.find("#mr_distance_car_"+i).attr('id',"mr_distance_car_"+d);
    new_metro.find("#mr_distance_to_metro_car_"+i).attr('id',"mr_distance_to_metro_car_"+d);
    new_metro.find("#mr_distance_to_metro_car_"+d).val('0');

    new_metro.appendTo("#type_metro_mr");
    
    $('#mr_metro_'+i).find('button').attr('onclick','$("#mr_metro_'+i+'").remove();$("#mr_distance_'+i+'").remove();$("#mr_distance_min_'+i+'").remove();$("#mr_type_metro_'+i+'").remove()');
    $('#mr_metro_'+i).find('.glyphicon.glyphicon-plus').attr('class','glyphicon glyphicon-minus');
}

function add_sellers(i){
    d=i+1;
    
    var new_object = $("#sel_"+i).clone().attr("id", "sel_"+d);
    new_object.find('#seller_'+i).attr('name','seller_'+d);
    new_object.find('#seller_'+i).attr('id','seller_'+d);
    new_object.find('button').attr('onclick','add_sellers('+d+')');
    new_object.appendTo("#sellers");
    
    $('#sel_'+i).find('button').attr('onclick','$("#sel_'+i+'").remove()');
    $('#sel_'+i).find('.glyphicon.glyphicon-plus').attr('class','glyphicon glyphicon-minus');
}

function delete_seller(id,object_id){
    $.post('/admin/objects/delete_seller',{
        id:id,
        object_id:object_id,
    }).success(function(){
        $('#td_'+id).remove();
    })
}

function add_builders(i){
    d=i+1;

    var new_object = $("#sel_"+i).clone().attr("id", "sel_"+d);
    new_object.find('#builder_'+i).attr('name','builder_'+d);
    new_object.find('#builder_'+i).attr('id','builder_'+d);
    new_object.find('button').attr('onclick','add_builders('+d+')');
    new_object.appendTo("#builders");

    $('#sel_'+i).find('button').attr('onclick','$("#sel_'+i+'").remove()');
    $('#sel_'+i).find('.glyphicon.glyphicon-plus').attr('class','glyphicon glyphicon-minus');
}

function delete_builder(id,object_id){
    $.post('/admin/objects/delete_builder',{
        id:id,
        object_id:object_id,
    }).success(function(){
        $('#td_'+id).remove();
    })
}

function showErrorMsg( message )
{
    $( "#error_msg_dialog").html( message );
    $( "#error_msg_dialog" ).dialog( "open" );
}

function save_general( message ){
    var name = $('#name').val();
    var adres = $('#adres').val();
    $('#name,#adres').removeClass('error');
    if (name!=''&&adres!=''){
        return true;
    } else {
        showErrorMsg( message || 'Обязательные поля должны быть заполнены!' );

        if (name==''){
            $('#name').addClass('error');
        }
        if (adres==''){
            $('#adres').addClass('error');
        }
        $('html,body').animate({scrollTop: 0});
        return false;
    }
}

function save_news(){
    var name = $('#name').val();
    var anons = $('#anons').val();
    $('#name,#anons').removeClass('error');
    if (name!=''&&anons!=''){
        return true;
    } else {
        var error_div = $('<div>', {
        	id: 'error_message',
        	class: 'error_message',
            text: 'Error: Обязательные поля должны быть заполненны.'
        });
        $("html").append(error_div)
        //setTimeout('error_window()',1500);
        if (name==''){
            $('#name').addClass('error');
        }
        if (anons==''){
            $('#anons').addClass('error');
        }
        $('html,body').animate({scrollTop: 0});
        return false;
    }
}

function save_location(){
		var val = $('#region option:selected').val();

		if(val == 'moscow_region') {
          return true;
		} else {
          return true;
		}
}

function error_window(){
    $('#error_message').remove();
}

function delete_images(type){
    var selected = new Array();
    $('#checkboxes input:checked').each(function() {
        selected.push($(this).attr('value'));
    });
    $.post('/admin/objects/delete_'+type+'_images',{ids:selected}).success(function(){
        for(var i = 0;i<selected.length;i++){
            $('#check_'+selected[i]).remove();
        }
    });
}

function delete_album(id){
    $.post('/admin/objects/delete_album',{id:id}).success(function(){
        $('#album_'+id).remove();
    })
}

function delete_plan_album(id){
    $.post('/admin/objects/delete_plan',{id:id}).success(function(){
        $('#album_'+id).remove();
    })
}

function delete_object(object_id,form){
    $.post('/admin/objects/delete_object',{id:object_id}).success(function(){
        $('#'+form).css('display','none');
        $('#object_'+object_id).remove();
    });
}

function update_comments(status){
    var comment_inputs = $('input[name=comment]');
    var array_data;
    array_data = new Object();
    comment_inputs.each(function(key){
        array_data['id'+key] = $(this).attr('id');
        array_data['comment'+key] = $(this).val();
    });
    $.post("/admin/objects/update_comments",{comment:array_data,status:status,}).success(function(){
        $('#comment_link').after('<span id="success" style="color:green">Успешно сохранено</span>');
        setTimeout('$("#success").remove()', 3000);

    });
}

$(document).ready( function() {
    $( "#error_msg_dialog" ).dialog({
        width: 500,
        modal: true,
        resizable: false,
        draggable: false,
        autoOpen: false,
        buttons: {
            Ok: function() {
            $( this ).dialog( "close" );
            }
        },
        hide: {
            effect: 'fade',
            duration: 500
        }
    });
});

/**
 * Form builder
 * @type Function|@exp;app@pro;publ
 */
var FlForm = (function (){
    var app = {
        init: function(){
            app.setUpListeners();
        },
        setUpListeners: function(){},
        publ: {
            /**
             * Отобразить ошибки формы
             * @param {object} fieldErrors - объект полей с ошибками {field_name: "error text"}
             * @returns {undefined}
             */
            errors: function (fieldErrors){
                if(typeof fieldErrors === 'object'){
                    for(var k in fieldErrors){
                        var field = $('[name="'+ k +'"]');
                        field.parents('.form-group').addClass('has-error');
                        
                        if(field.parent('div').hasClass('input-group'))
                            field = field.parent('.input-group');
                        
                        field.after($('<small>', {
                            class: 'text-danger form__errors_field',
                            text: fieldErrors[k]
                        }))
                    }
                }
            },
            /**
             * Сбросить все ошибки
             * @returns {undefined}
             */
            dropErrors: function (){
                $('.form__errors_field').parents('.form-group').removeClass('has-error');
                $('.form__errors_field').remove();
            },
            /**
             * Получить данные формы
             * @param {string / $} selector - $
             * @returns {object}
             */
            getFormData: function (selector){
                var arr = $(selector).serializeArray(), result = {};
                for(var k in arr){
                    result[arr[k].name] = arr[k].value;
                }
                return result;
            } 
        }
    };
    app.init();
    return app.publ;
}());