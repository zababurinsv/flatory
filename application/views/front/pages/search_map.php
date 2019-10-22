<img src="/images/loader.gif" id="loader" style="position: absolute; top: 42%; left: 47%; opacity: 0.5">
<div id="y-map" class="y-map-fs"></div>
<form class="search-panel search-panel-map">
    <div>
        <a href="/"><img src="/images/new/logo.png" class="search-panel-map--logo"></a>
    </div>
    <div class="form-group">
        <div>
            <label>Цена, руб</label>
            <div class="radio radio-link active" style="margin-left: 30px;">
                <label><input type="radio" name="price_type" value="0" checked="checked"> за квартиру</label>
            </div>
            <div class="radio radio-link" style="margin: 0; float: right;">
                <label><input type="radio" name="price_type" value="1"> за м²</label>
            </div>
        </div>
        <div class="form-group width-50 padding_none">
            <input type="text" name="cost_min" class="form-control clear-input" placeholder="от" maxlength="11">
        </div>
        <div class="form-group width-50 padding_none">
            <input type="text" name="cost_max" class="form-control clear-input" placeholder="до" maxlength="11">
        </div>
    </div>
    <div class="form-group">
        <label>Комнат</label>
        <div class="list-checkbox-btn">
            <div class="checkbox checkbox-btn" style="width: 70px;">
                <label><input type="checkbox" name="rooms[]" value="11"> студия</label>
            </div>
            <div class="checkbox checkbox-btn">
                <label><input type="checkbox" name="rooms[]" value="1"> 1</label>
            </div>
            <div class="checkbox checkbox-btn">
                <label><input type="checkbox" name="rooms[]" value="2"> 2</label>
            </div>
            <div class="checkbox checkbox-btn">
                <label><input type="checkbox" name="rooms[]" value="3"> 3</label>
            </div>
            <div class="checkbox checkbox-btn">
                <label><input type="checkbox" name="rooms[]" value="4"> 4+</label>
            </div>
            <div class="checkbox checkbox-btn">
                <label><input type="checkbox" name="rooms[]" value="12"> СП</label>
            </div>
        </div>
    </div>
    <div class="form-group">
        <label>Срок сдачи</label>
        <div class="list-checkbox-btn">
            <div class="checkbox checkbox-btn" style="width: 80px;">
                <label><input type="checkbox" name="complite[]" value="1"> дом сдан</label>
            </div>
            <div class="checkbox checkbox-btn">
                <label><input type="checkbox" name="complite[]" value="2016"> 2016</label>
            </div>
            <div class="checkbox checkbox-btn">
                <label><input type="checkbox" name="complite[]" value="2017"> 2017</label>
            </div>
            <div class="checkbox checkbox-btn">
                <label><input type="checkbox" name="complite[]" value="2018"> 2018+</label>
            </div>
        </div>
    </div>
    <div class="progress" style="display: none;">
        <div class="progress-bar progress-bar-warning progress-bar-striped active" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100"> 
            <span class="sr-only"></span> 
        </div>
    </div>
</form>
<script type="text/javascript" src="/js/search.js"></script>