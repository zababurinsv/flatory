// import pagination from './pagination.js'

let upload = `
<div class="content_space">
    <article>
        <div>
            <div style="float:left">
                <i class="news_date"></i>
            </div>
            <div style="float:right;height: 20px;">
                <a class="news_read_more" style="position: relative;top:10px;font-size: 14px;" href=""><img style="margin-top: -2px;" src="/images/arrow.png" alt=""/></a>
            </div>
            <div class="clearfix"></div>
        </div>
        <h1 class="news_name"></h1>
        <div class="news_content"></div>
        <div class="clearfix"></div>
    </article>
    <?php if ($tags): ?>
        <nav>
            <ul class="list-inline space_bottom">
                <li><b class="text-2">Теги:</b></li>
                <?php foreach ($tags as $it): ?>
                    <li><a href="/tags" class="content-limk text-green"></a></li>
                <?php endforeach; ?>
            </ul>
        </nav>
        <b class="text-2">Читайте также:</b>
        <ul class="list-unstyled">
                <li class="news_page_item">
                    <div class="news_page_img">
                        <img src="" />
                    </div>
                    <div class="news_page_anons">
                        <h4><a href="/" title=""></a></h4>
                        <span class="news_date"></span>
                        <p class="news_preview"> 
                            <a href="/" title="">Читать дальше</a>
                        </p>
                        <b class="pull-right"></b>
                    </div>
                </li>
            </ul>
        </div>
`


class news extends HTMLElement {
    constructor() {
        super();

        console.assert(false);
    }
    connectedCallback() {

        const toBase64 = file => new Promise((resolve, reject) => {
            const reader = new FileReader();
            reader.readAsDataURL(file);
            reader.onload = () => resolve(reader.result);
            reader.onerror = error => reject(error);
        });
        let self = this;
        (async ()=>{
            let  itemsObject = {}
            try {
                itemsObject =    await indexedDB['get']({type:'getAll'})
            }catch (e) {

            }
            // if(!itemsObject || itemsObject.length === 0){
            itemsObject =    await  fetch('http://dev.work/api/item',{ method: 'GET' })
            itemsObject =    await itemsObject.json()


            // for(let i =0; i < itemsObject.length;i++){
            //     await indexedDB['put']({type:'item', value:itemsObject[i]})
            // }

            // }
            // self.querySelector('.speech').addEventListener('click',async ()=>{
            //     await speech['get']({type:'speech', this: self, language: self.querySelector('#select_language'), dialect: self.querySelector('#select_dialect'), self: self.querySelector('#webSpeech')})
            // })
            // await  indexedDB['get']({type:'open', self: indexedDB})
            // await  indexedDB['get']({type:'cursor', self: indexedDB})
            // await  indexedDB['put']({type:'data', self: indexedDB})
            // await  indexedDB['get']({type:'cursor', self: indexedDB})
            // await  indexedDB['get']({type:'index', self: indexedDB})


            /*
            for(let i =0; i < itemsObject.length;i++){
                let itemObject =  await templateItem(itemsObject[i])
                self.querySelector('tbody').insertAdjacentHTML('beforeend', itemObject);
            }
            let del = self.querySelectorAll('.delete')
            let edit = self.querySelectorAll('.edit')
            let plan = self.querySelectorAll('.search_input_plan')

            // console.assert(false)
            for(let i =0; i < del.length;i++){ await addEventListener(del[i], self) }

            for(let i =0; i < edit.length;i++){
                edit[i].addEventListener('click', async (e)=>{

                    let verify = true
                    let item =  e.target.parentNode
                    while(verify){
                        if(item.tagName === 'TR'){
                            verify = false
                        }else{
                            item =  item.parentNode
                        }
                    }

                    verify = true
                    let mainTable = item
                    while(verify){
                        if(mainTable.tagName === 'MAIN-TABLE'){
                            verify = false
                        }else{
                            mainTable =  mainTable.parentNode
                        }
                    }

                    // console.assert(false, mainTable.querySelector('.close'))
                    mainTable.querySelector('.close').addEventListener('click', async (e)=>{
                        e.target.parentNode.parentNode.querySelector('.btn-primary').style.display = 'flex'
                        e.target.parentNode.parentNode.querySelector('#formConteiner').innerHTML = ''
                        sessionStorage.clear()
                    })


                    mainTable.querySelector('.btn-primary').style.display="none"
                    item = item.querySelectorAll('td')
                    let editObject = {}
                    let selectedIndex = 0
                    let tempPrice = {}
                    for(let i =0; i < item.length -1; i++){
                        if(i === 0){
                            editObject['id'] = item[i].innerText
                            sessionStorage.setItem('id',item[i].innerText);
                        }else{
                            switch (i) {
                                case 1:
                                    switch (item[i].innerText) {
                                        case 'Санкт-Петербург':
                                            sessionStorage.setItem('city',2);
                                            editObject['city'] = 2
                                            break
                                        case 'Москва':
                                            sessionStorage.setItem('city', 1);
                                            editObject['city'] = 1
                                            break
                                        default:
                                            sessionStorage.setItem('city', 0);
                                            editObject['city'] = 0
                                            break
                                    }
                                    break
                                case 2:
                                    sessionStorage.setItem('name',item[i].innerText);
                                    editObject['name'] = item[i].innerText
                                    break
                                case 3:
                                    sessionStorage.setItem('object',item[i].innerText);
                                    editObject['object'] = item[i].innerText
                                    break
                                case 4:
                                    sessionStorage.setItem('dateExperiences',item[i].innerText);
                                    editObject['dateExperiences'] = item[i].innerText
                                    break
                                case 5:
                                    sessionStorage.setItem('class',item[i].innerText);
                                    editObject['class'] = item[i].innerText
                                    break
                                case 6:
                                    sessionStorage.setItem('type',item[i].innerText);
                                    editObject['type'] = item[i].innerText
                                    break
                                case 7:
                                    sessionStorage.setItem('metro',item[i].innerText);
                                    editObject['metro'] = item[i].innerText
                                    break
                                case 8:
                                    sessionStorage.setItem('geo',item[i].innerText);
                                    editObject['geo'] = item[i].innerText
                                    break
                                case 9:
                                    let plan = ''
                                    let tempPlan = item[i].querySelectorAll('option')
                                    for(let i =0; i < tempPlan.length; i++){
                                        if(i === 0){
                                            plan = `${tempPlan[i].value}`
                                        }else{
                                            plan = `${plan},${tempPlan[i].value}`
                                        }

                                    }
                                    selectedIndex = parseInt(item[i].querySelector('select').selectedIndex, 10)
                                    sessionStorage.setItem('plans',plan);
                                    editObject['plans'] = plan
                                    break
                                case 10:
                                    let price = ''
                                    let tempPrice = item[i].querySelectorAll('option')

                                    for(let i =0; i < tempPrice.length; i++){
                                        if(i === 0){
                                            price = `${tempPrice[i].value}`
                                        }else{
                                            price = `${price},${tempPrice[i].value}`
                                        }

                                    }
                                    sessionStorage.setItem('price',price);
                                    editObject['price'] = price
                                    break
                                case 11:
                                    sessionStorage.setItem('finish',item[i].innerText);
                                    editObject['finish'] = item[i].innerText
                                    break
                                case 12:
                                    sessionStorage.setItem('description',item[i].innerText);
                                    editObject['description'] = item[i].innerText
                                    break
                                default:
                                    console.assert(false, `новое свойство ${i}`, item[i])
                                    break

                            }
                        }
                    }
                    let updateObject = await form(editObject, tempPrice[selectedIndex])
                    // console.assert(false, updateObject)
                    document.querySelector('#formConteiner').innerHTML = ''
                    document.querySelector('#formConteiner').insertAdjacentHTML('beforeend', updateObject)
                    document.querySelector('#city').selectedIndex =  sessionStorage.getItem('city');
                    // console.assert(false, document.querySelector('#city'))
                    document.querySelector('#city').addEventListener("change",  async (event)=>{
                        sessionStorage.setItem('city',event.target.selectedIndex);
                    })
                    let itemObject = document.querySelector('#object-form')
                    for(let i = 0; i < itemObject.querySelectorAll('input').length; i++){
                        itemObject.querySelectorAll('input')[i].oninput = function(event) {
                            sessionStorage.setItem(`${event.target.name}`,event.target.value)
                        };
                    }
                    for(let i = 0; i < itemObject.querySelectorAll('textarea').length; i++){
                        itemObject.querySelectorAll('textarea')[i].addEventListener('input', (event) => {
                            sessionStorage.setItem(`${event.target.name}`,event.target.value);
                        });
                    }
                    document.querySelector('.btn-success').addEventListener('click', async (e)=>{
                        // e.target.parentNode.querySelector('#file-upload').files[0])
                        let verify = true
                        let item =  e.target.parentNode
                        while(verify){
                            if(item.tagName === 'MAIN-TABLE'){
                                verify = false
                            }else{
                                item =  item.parentNode
                            }
                        }
                        item.querySelector('.btn-primary').style.display = 'flex'
                        // console.assert(false,  )
                        if(e.target.parentNode.querySelector('#file-upload').files[0] === 'undefined' ||e.target.parentNode.querySelector('#file-upload').files[0] === undefined ){

                            let putItem = JSON.stringify({
                                "city":  sessionStorage.getItem('city'),
                                "name":  sessionStorage.getItem('name'),
                                "object":  sessionStorage.getItem('object'),
                                "dateExperiences":  sessionStorage.getItem('dateExperiences'),
                                "class": sessionStorage.getItem('class'),
                                "type": sessionStorage.getItem('type'),
                                "metro": sessionStorage.getItem('metro'),
                                "geo":sessionStorage.getItem('geo'),
                                "plans": sessionStorage.getItem('plans'),
                                "price": sessionStorage.getItem('price'),
                                "finish": sessionStorage.getItem('finish'),
                                "image":sessionStorage.getItem('fileUpload'),
                                "description":sessionStorage.getItem('description'),
                                "timestampUpdate":(Date.now()).toString()
                            });

                            let xhr = new XMLHttpRequest();
                            xhr.withCredentials = true;
                            xhr.addEventListener("readystatechange", async function () {
                                if (this.readyState === 4) {
                                    self.querySelector('tbody').innerHTML = ''
                                    self.querySelector('#formConteiner').innerHTML = ''
                                    sessionStorage.clear()
                                    await update(self)

                                }
                            });
                            xhr.open("PUT", `http://dev.work/api/item/${sessionStorage.getItem('id')}`);
                            xhr.setRequestHeader("Content-Type", "application/json");
                            xhr.setRequestHeader("cache-control", "no-cache");
                            xhr.send(putItem);

                        }else{
                            let putItem = JSON.stringify({
                                "city":  sessionStorage.getItem('city'),
                                "name":  sessionStorage.getItem('name'),
                                "object":  sessionStorage.getItem('object'),
                                "dateExperiences":  sessionStorage.getItem('dateExperiences'),
                                "class": sessionStorage.getItem('class'),
                                "type": sessionStorage.getItem('type'),
                                "metro": sessionStorage.getItem('metro'),
                                "geo":sessionStorage.getItem('geo'),
                                "plans": sessionStorage.getItem('plans'),
                                "price": sessionStorage.getItem('price'),
                                "finish": sessionStorage.getItem('finish'),
                                "description":sessionStorage.getItem('description'),
                                "image":await toBase64(e.target.parentNode.querySelector('#file-upload').files[0]),
                                "timestampUpdate":(Date.now()).toString()
                            });


                            let xhr = new XMLHttpRequest();
                            xhr.withCredentials = true;
                            xhr.addEventListener("readystatechange", async function () {
                                if (this.readyState === 4) {
                                    self.querySelector('tbody').innerHTML = ''
                                    self.querySelector('#formConteiner').innerHTML = ''
                                    sessionStorage.clear()
                                    await update(self)

                                }
                            });
                            xhr.open("PUT", `http://dev.work/api/item/${sessionStorage.getItem('id')}`);
                            xhr.setRequestHeader("Content-Type", "application/json");
                            xhr.setRequestHeader("cache-control", "no-cache");
                            xhr.send(putItem);
                        }


                    })
                })
            }
            for(let i =0; i < plan.length; i++){
                plan[i].addEventListener('click', async (e)=>{
                    let verify = true
                    let item =  e.target.parentNode
                    while(verify){
                        if(item.tagName === 'TR'){
                            verify = false
                        }else{
                            item =  item.parentNode
                        }
                    }
                    switch (e.target.name) {
                        case 'plan':
                            let price = item.querySelector('.price')
                            price = price.querySelector('select')
                            price.selectedIndex = e.target.selectedIndex
                            break
                        case 'price':
                            let plan = item.querySelector('.plan')
                            plan = plan.querySelector('select')
                            plan.selectedIndex = e.target.selectedIndex
                            break
                        default:
                            break
                    }
                })
            }
            self.querySelector('.close').addEventListener('click', async (e)=>{
                e.target.parentNode.parentNode.querySelector('.btn-primary').style.display = 'flex'
                e.target.parentNode.parentNode.querySelector('#formConteiner').innerHTML = ''
                sessionStorage.clear()
            })
            self.querySelector('.btn-primary').addEventListener('click', async (e)=>{
                let verify = true
                let button =  e.target.parentNode
                while(verify){
                    if(button.tagName === 'MAIN-TABLE'){
                        verify = false
                    }else{
                        button =  button.parentNode
                    }
                }
                button.querySelector('.btn-primary').style.display = 'none'

                let object = e.target.parentNode.parentNode
                verify = true
                let item =  e.target.parentNode

                let itemObject = await form()
                object.querySelector('#formConteiner').innerHTML = ''
                object.querySelector('#formConteiner').insertAdjacentHTML('beforeend', itemObject)

                sessionStorage.setItem('city',0);
                self.querySelector('#city').addEventListener("change",  async (event)=>{
                    sessionStorage.setItem('city',event.target.selectedIndex);
                })
                itemObject = object.querySelector('#object-form')
                for(let i = 0; i < itemObject.querySelectorAll('input').length; i++){
                    itemObject.querySelectorAll('input')[i].addEventListener('input', (event) => {
                        sessionStorage.setItem(`${event.target.name}`,event.target.value);
                    });
                }
                for(let i = 0; i < itemObject.querySelectorAll('textarea').length; i++){
                    itemObject.querySelectorAll('textarea')[i].addEventListener('input', (event) => {
                        sessionStorage.setItem(`${event.target.name}`,event.target.value);
                    });
                }
                object.querySelector('.btn-success').addEventListener('click', async (e)=>{

                    // console.assert(false, e.target.parentNode.querySelector('#file-upload').files[0]['name'])
                    let data = new FormData();
                    let outItem = {}
                    for(let i=0; i < object.querySelectorAll('input').length; i++){
                        data.append(`${object.querySelectorAll('input')[i].name}`, sessionStorage.getItem(`${object.querySelectorAll('input')[i].name}`));
                        sessionStorage.removeItem(`${object.querySelectorAll('input')[i].name}`)
                    }

                    for(let i=0; i < object.querySelectorAll('textarea').length; i++){
                        data.append(`${object.querySelectorAll('textarea')[i].name}`, sessionStorage.getItem(`${object.querySelectorAll('textarea')[i].name}`));
                        sessionStorage.removeItem(`${object.querySelectorAll('textarea')[i].name}`)
                    }

                    data.append('city', sessionStorage.getItem('city'))
                    data.append('timestampUpdate', [])


                    if (object.querySelector('#file-upload').files[0] === undefined){
                        data.append('image', [])
                    }else{
                        let file = await  toBase64(object.querySelector('#file-upload').files[0])
                        if( object.querySelector('#file-upload').files[0]){
                            data.append('image', file)
                        }
                    }




                    // console.assert(false,e.target.parentNode.querySelector('#file-upload').files[0]['name'].split('.')[0] )

                    let timestamp = Date.now()
                    data.append('timestamp', timestamp)
                    fetch(`http://dev.work/api/item/`,{
                        method: 'POST',
                        body:data
                    })
                        .then(function(response) {
                            return response.text();
                        })
                        .then(async function(myJson) {
                            console.log(myJson);
                            self.querySelector('tbody').innerHTML = ''
                            self.querySelector('#formConteiner').innerHTML = ''
                            sessionStorage.clear()
                            await update(self)
                        });
                })
            })
            */
            // pagination(self, {pagination: pagination});
        })()
    }
    disconnectedCallback() {}
    componentWillMount() {}
    componentDidMount() {}
    componentWillUnmount() {}
    componentDidUnmount() {}
}
window.customElements.define('main-table', news);