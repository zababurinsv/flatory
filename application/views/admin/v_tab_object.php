          <div class="page-header">
            <h2><?if($_SESSION['status_object']=='add'){?>Добавление<?} else {?>Изменение<?}?> объекта <?=$_SESSION['object_name']?></h2>
          </div>
          <?$id_object = @$this->db->where('id_object',$object_id)->get('meta')->row()->id_object;?>
          <div class="row">
            <div class="tabbable"> <!-- Only required for left/right tabs -->
             <ul class="nav nav-tabs">
               <li <?if($this->uri->segment(3)=='general_info'){?> class="active"<?}?>><a <?='href="/admin/objects/general_info/'.$object_id.'"'?> >Описание</a></li>
               <li <?if($this->uri->segment(3)=='object_location'){?> class="active"<?}?>><a <?if(!empty($id_object)){?><?='href="/admin/objects/object_location/'.$object_id.'"'?><?}?> >Местонахождение</a></li>
               <li <?if($this->uri->segment(3)=='technical_characteristics'){?> class="active"<?}?>><a <?if(!empty($id_object)){?><?='href="/admin/objects/technical_characteristics/'.$object_id.'"';?><?}?> >Тех. характеристики</a></li>
               <li <?if($this->uri->segment(3)=='cost'){?> class="active"<?}?>><a <?if(!empty($id_object)){?><?='href="/admin/objects/cost/'.$object_id.'"';?><?}?> >Стоимость</a></li>
               <li <?if($this->uri->segment(3)=='plan'){?> class="active"<?}?>><a <?if(!empty($id_object)){?><?='href="/admin/objects/plan/'.$object_id.'"';?><?}?> >Планировки</a></li>
               <li <?if($this->uri->segment(3)=='gallery'){?> class="active"<?}?>><a <?if(!empty($id_object)){?><?='href="/admin/objects/gallery/'.$object_id.'"';?><?}?> >Фото строительства</a></li>
               <li <?if($this->uri->segment(3)=='video'){?> class="active"<?}?>><a <?if(!empty($id_object)){?><?='href="/admin/objects/video/'.$object_id.'"';?><?}?> >Видео</a></li>
               <li <?if($this->uri->segment(3)=='documents'){?> class="active"<?}?>><a <?if(!empty($id_object)){?><?='href="/admin/objects/documents/'.$object_id.'"';?><?}?> >Документация</a></li>
               <li <?if($this->uri->segment(3)=='infrastructure'){?> class="active"<?}?>><a <?if(!empty($id_object)){?><?='href="/admin/objects/infrastructure/'.$object_id.'"';?><?}?> >Инфраструктура</a></li>
               <li <?if($this->uri->segment(3)=='builders'){?> class="active"<?}?>><a <?if(!empty($id_object)){?><?='href="/admin/objects/builders/'.$object_id.'"';?><?}?> >Застройщики</a></li>
               <li <?if($this->uri->segment(3)=='sellers'){?> class="active"<?}?>><a <?if(!empty($id_object)){?><?='href="/admin/objects/sellers/'.$object_id.'"';?><?}?> >Продавцы</a></li>
               <li <?if($this->uri->segment(2)=='cart'){?> class="active"<?}?>><a <?if(!empty($id_object)){?><?='href="/admin/cart/'.$object_id.'"';?><?}?> >Карточка</a></li>
               <li <?if($this->uri->segment(3)=='seo'){?> class="active"<?}?>><a <?if(!empty($id_object)){?><?='href="/admin/objects/seo/'.$object_id.'"';?><?}?> >Мета-теги</a></li>
               <li <?if($this->uri->segment(3)=='panorama'){?> class="active"<?}?>><a <?if(!empty($id_object)){?><?='href="/admin/objects/panorama/'.$object_id.'"';?><?}?> >Панорамы</a></li>
               <li <?if($this->uri->segment(3)=='publish'){?> class="active"<?}?>><a <?if(!empty($id_object)){?><?='href="/admin/objects/publish/'.$object_id.'"';?><?}?> >Публикация</a></li>
             </ul>
              <div class="tab-content">