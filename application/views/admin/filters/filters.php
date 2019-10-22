<?php
    // собираем фильтры по колонкам
    $filters_left = $filters_right = $filters_more_left = $filters_more_right = array();
    if(isset($filters)){
        foreach ($filters as $key => $value){
            if($key % 2 === 0)
                $filters_left[] = $value;
            else
                $filters_right[] = $value;
        }
    }
    
    if(isset($filters_more)){
        foreach ($filters_more as $key => $value){
            if($key % 2 === 0)
                $filters_more_left[] = $value;
            else
                $filters_more_right[] = $value;
        }
    }
?>
<div class="panel panel-default space_top_xs panel-body-cover">
    <div class="panel-heading">Фильтр</div>
    <div class="panel-body" style="padding: 0;">
        <form action="" class="form-horizontal fl-filter">
            <div class="row">
                <div class="col-md-6">
                    <?php  foreach ($filters_left as $f) echo $f; ?>
                </div>
                <div class="col-md-6">
                    <?php  foreach ($filters_right as $f) echo $f; ?>
                </div>
            </div>
            <div class="row more_filters" style="display: none;">
                <div class="col-md-6">
                    <?php  foreach ($filters_more_left as $f) echo $f; ?>
                </div>
                <div class="col-md-6">
                    <?php  foreach ($filters_more_right as $f) echo $f; ?>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <div class="col-sm-12">
                            <button type="submit" class="btn btn-primary pull-right submit-form"><span class="glyphicon glyphicon-search"></span> Фильтр</button>
                            <a href="javascript:void(0)" class="btn btn-default pull-right space_right drop-form">Сброс</a>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

