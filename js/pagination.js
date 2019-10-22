export default ()=>{
    getPagination('#table-id');
    $('#maxRows').trigger('change');
    function getPagination (table){

        $('#maxRows').on('change',function(){
            $('.pagination').html('');						// reset pagination div
            var trnum = 0 ;									// reset tr counter
            var maxRows = parseInt($(this).val());			// get Max Rows from select option

            var totalRows = $(table+' tbody tr').length;		// numbers of rows
            $(table+' tr:gt(0)').each(function(){			// each TR in  table and not the header
                trnum++;									// Start Counter
                if (trnum > maxRows ){						// if tr number gt maxRows

                    $(this).hide();							// fade it out
                }if (trnum <= maxRows ){$(this).show();}// else fade in Important in case if it ..
            });											//  was fade out to fade it in
            if (totalRows > maxRows){						// if tr total rows gt max rows option
                var pagenum = Math.ceil(totalRows/maxRows);	// ceil total(rows/maxrows) to get ..
                //	numbers of pages
                for (var i = 1; i <= pagenum ;){			// for each page append pagination li
                    $('.pagination').append('<li data-page="'+i+'">\
								      <span>'+ i++ +'<span class="sr-only"></span></span>\
								    </li>').show();
                }											// end for i


            } 												// end if row count > max rows
            $('.pagination li:first-child').addClass('active'); // add active class to the first li


            //SHOWING ROWS NUMBER OUT OF TOTAL DEFAULT
            showig_rows_count(maxRows, 1, totalRows);
            //SHOWING ROWS NUMBER OUT OF TOTAL DEFAULT

            $('.pagination li').on('click',function(e){		// on click each page
                e.preventDefault();
                var pageNum = $(this).attr('data-page');	// get it's number
                var trIndex = 0 ;							// reset tr counter
                $('.pagination li').removeClass('active');	// remove active class from all li
                $(this).addClass('active');					// add active class to the clicked


                //SHOWING ROWS NUMBER OUT OF TOTAL
                showig_rows_count(maxRows, pageNum, totalRows);
                //SHOWING ROWS NUMBER OUT OF TOTAL



                $(table+' tr:gt(0)').each(function(){		// each tr in table not the header
                    trIndex++;								// tr index counter
                    // if tr index gt maxRows*pageNum or lt maxRows*pageNum-maxRows fade if out
                    if (trIndex > (maxRows*pageNum) || trIndex <= ((maxRows*pageNum)-maxRows)){
                        $(this).hide();
                    }else {
                        this.style.display = 'flex'
                    }
                });
            });
        });
        // end of on select change

        // END OF PAGINATION

    }

// SI SETTING
    $(function(){
        // Just to append id number for each row
        default_index();






    });

//ROWS SHOWING FUNCTION
    function showig_rows_count(maxRows, pageNum, totalRows) {
        //Default rows showing
        var end_index = maxRows*pageNum;
        var start_index = ((maxRows*pageNum)- maxRows) + parseFloat(1);
        var string = 'Showing '+ start_index + ' to ' + end_index +' of ' + totalRows + ' entries';
        $('.rows_count').html(string);
    }

// CREATING INDEX
    function default_index() {
        // $('table tr:eq(0)').prepend('<th class="search_input_id"><input class="search_input_id"type="text" id="search_input_id"  placeholder="id" class="form-control"></th>')

        var id = 0;

        $('table tr:gt(0)').each(function(){
            id++
            $(this).prepend('<td>'+id+'</td>');
        });
    }


    // console.assert(false, document)

    document.querySelector('#search_input_id').addEventListener('keyup',(event)=>{
        let count = $('.table').children('tbody').children('tr:first-child').children('td').length;
        // Declare variables
        let input, filter, table, tr, td, i;
        input = document.getElementById("search_input_id");
        let input_value = document.getElementById("search_input_id").value;
        filter = input.value.toLowerCase();

        if(input_value !=''){
            table = document.getElementById("table-id");
            tr = table.getElementsByTagName("tr");
            // Loop through all table rows, and hide those who don't match the search query
            for (let i = 1; i < tr.length; i++) {
                let flag = 0;
                for(let j = 0; j < count; j++){
                    if(j === 0){
                        td = tr[i].getElementsByTagName("td")[j];
                        if (td) {
                            let td_text = td.innerHTML;
                            if (td.innerHTML.toLowerCase().indexOf(filter) > -1) {
                                //var td_text = td.innerHTML;
                                //td.innerHTML = 'shaban';
                                flag = 1;
                            } else {
                                //DO NOTHING
                            }
                        }
                    }
                }
                if(flag === 1){
                    tr[i].style.display = "";
                }else {
                    tr[i].style.display = "none";
                }
            }
        }else {
            //RESET TABLE
            $('#maxRows').trigger('change');
        }


    })

    document.querySelector('#search_input_Developer').addEventListener('keyup',(event)=>{

        let count = $('.table').children('tbody').children('tr:first-child').children('td').length;


        // Declare variables
        let input, filter, table, tr, td, i;
        input = document.getElementById("search_input_Developer");
        let input_value = document.getElementById("search_input_Developer").value;
        filter = input.value.toLowerCase();

        if(input_value !=''){
            table = document.getElementById("table-id");
            tr = table.getElementsByTagName("tr");
            // Loop through all table rows, and hide those who don't match the search query
            for (let i = 1; i < tr.length; i++) {
                let flag = 0;
                for(let j = 0; j < count; j++){
                    if(j === 1){
                        td = tr[i].getElementsByTagName("td")[j];
                        if (td) {
                            let td_text = td.innerHTML;
                            if (td.innerHTML.toLowerCase().indexOf(filter) > -1) {
                                //var td_text = td.innerHTML;
                                //td.innerHTML = 'shaban';
                                flag = 1;
                            } else {
                                //DO NOTHING
                            }
                        }
                    }
                }
                if(flag === 1){
                    tr[i].style.display = "";
                }else {
                    tr[i].style.display = "none";
                }
            }
        }else {
            //RESET TABLE
            $('#maxRows').trigger('change');
        }
    })


    document.querySelector('#search_input_Deadline').addEventListener('keyup',(event)=>{

        let count = $('.table').children('tbody').children('tr:first-child').children('td').length;


        // Declare variables
        let input, filter, table, tr, td, i;
        input = document.getElementById("search_input_Deadline");
        let input_value = document.getElementById("search_input_Deadline").value;
        filter = input.value.toLowerCase();

        if(input_value !=''){
            table = document.getElementById("table-id");
            tr = table.getElementsByTagName("tr");
            // Loop through all table rows, and hide those who don't match the search query
            for (let i = 1; i < tr.length; i++) {
                let flag = 0;
                for(let j = 0; j < count; j++){
                    if(j === 2){
                        td = tr[i].getElementsByTagName("td")[j];
                        if (td) {
                            let td_text = td.innerHTML;
                            if (td.innerHTML.toLowerCase().indexOf(filter) > -1) {
                                //var td_text = td.innerHTML;
                                //td.innerHTML = 'shaban';
                                flag = 1;
                            } else {
                                //DO NOTHING
                            }
                        }
                    }
                }
                if(flag === 1){
                    tr[i].style.display = "";
                }else {
                    tr[i].style.display = "none";
                }
            }
        }else {
            //RESET TABLE
            $('#maxRows').trigger('change');
        }
    })
    document.querySelector('#search_input_houseClass').addEventListener('input',(event)=>{

        let count = $('.table').children('tbody').children('tr:first-child').children('td').length;


        // Declare variables
        let input, filter, table, tr, td, i;
        input = document.getElementById("search_input_houseClass");
        let input_value = document.getElementById("search_input_houseClass").value;
        filter = input.value.toLowerCase();

        if(input_value !=''){
            table = document.getElementById("table-id");
            tr = table.getElementsByTagName("tr");
            // Loop through all table rows, and hide those who don't match the search query
            for (let i = 1; i < tr.length; i++) {
                let flag = 0;
                for(let j = 0; j < count; j++){
                    if(j === 3){
                        td = tr[i].getElementsByTagName("td")[j];
                        if (td) {
                            let td_text = td.innerHTML;
                            if (td.innerHTML.toLowerCase().indexOf(filter) > -1) {
                                //var td_text = td.innerHTML;
                                //td.innerHTML = 'shaban';
                                flag = 1;
                            } else {
                                //DO NOTHING
                            }
                        }
                    }
                }
                if(flag === 1){
                    tr[i].style.display = "";
                }else {
                    tr[i].style.display = "none";
                }
            }
        }else {
            //RESET TABLE
            $('#maxRows').trigger('change');
        }
    })

    document.querySelector('#search_input_houseType').addEventListener('input',(event)=>{

        let count = $('.table').children('tbody').children('tr:first-child').children('td').length;


        // Declare variables
        let input, filter, table, tr, td, i;
        input = document.getElementById("search_input_houseType");
        let input_value = document.getElementById("search_input_houseType").value;
        filter = input.value.toLowerCase();

        if(input_value !=''){
            table = document.getElementById("table-id");
            tr = table.getElementsByTagName("tr");
            // Loop through all table rows, and hide those who don't match the search query
            for (let i = 1; i < tr.length; i++) {
                let flag = 0;
                for(let j = 0; j < count; j++){
                    if(j === 4){
                        td = tr[i].getElementsByTagName("td")[j];
                        if (td) {
                            let td_text = td.innerHTML;
                            if (td.innerHTML.toLowerCase().indexOf(filter) > -1) {
                                //var td_text = td.innerHTML;
                                //td.innerHTML = 'shaban';
                                flag = 1;
                            } else {
                                //DO NOTHING
                            }
                        }
                    }
                }
                if(flag === 1){
                    tr[i].style.display = "";
                }else {
                    tr[i].style.display = "none";
                }
            }
        }else {
            //RESET TABLE
            $('#maxRows').trigger('change');
        }
    })
    document.querySelector('#search_input_Metro').addEventListener('keyup',(event)=>{

        let count = $('.table').children('tbody').children('tr:first-child').children('td').length;


        // Declare variables
        let input, filter, table, tr, td, i;
        input = document.getElementById("search_input_Metro");
        let input_value = document.getElementById("search_input_Metro").value;
        filter = input.value.toLowerCase();

        if(input_value !=''){
            table = document.getElementById("table-id");
            tr = table.getElementsByTagName("tr");
            // Loop through all table rows, and hide those who don't match the search query
            for (let i = 1; i < tr.length; i++) {
                let flag = 0;
                for(let j = 0; j < count; j++){
                    if(j === 5){
                        td = tr[i].getElementsByTagName("td")[j];
                        if (td) {
                            let td_text = td.innerHTML;
                            if (td.innerHTML.toLowerCase().indexOf(filter) > -1) {
                                //var td_text = td.innerHTML;
                                //td.innerHTML = 'shaban';
                                flag = 1;
                            } else {
                                //DO NOTHING
                            }
                        }
                    }
                }
                if(flag === 1){
                    tr[i].style.display = "";
                }else {
                    tr[i].style.display = "none";
                }
            }
        }else {
            //RESET TABLE
            $('#maxRows').trigger('change');
        }
    })

    document.querySelector('#search_input_geo').addEventListener('keyup',(event)=>{

        let count = $('.table').children('tbody').children('tr:first-child').children('td').length;


        // Declare variables
        let input, filter, table, tr, td, i;
        input = document.getElementById("search_input_geo");
        let input_value = document.getElementById("search_input_geo").value;
        filter = input.value.toLowerCase();

        if(input_value !=''){
            table = document.getElementById("table-id");
            tr = table.getElementsByTagName("tr");
            // Loop through all table rows, and hide those who don't match the search query
            for (let i = 1; i < tr.length; i++) {
                let flag = 0;
                for(let j = 0; j < count; j++){
                    if(j === 6){
                        td = tr[i].getElementsByTagName("td")[j];
                        if (td) {
                            let td_text = td.innerHTML;
                            if (td.innerHTML.toLowerCase().indexOf(filter) > -1) {
                                //var td_text = td.innerHTML;
                                //td.innerHTML = 'shaban';
                                flag = 1;
                            } else {
                                //DO NOTHING
                            }
                        }
                    }
                }
                if(flag === 1){
                    tr[i].style.display = "";
                }else {
                    tr[i].style.display = "none";
                }
            }
        }else {
            //RESET TABLE
            $('#maxRows').trigger('change');
        }
    })

    console.log(document.querySelectorAll('#search_input_plan'))

    document.querySelector('#search_input_plan').addEventListener('keyup',(event)=>{

        let count = $('.table').children('tbody').children('tr:first-child').children('td').length;

///////////////////////////////////////////////////////////////////////
        // Declare variables
        let input, filter, table, tr, td, i;
        input = document.getElementById("search_input_plan");
        let input_value = document.getElementById("search_input_plan").value;
        filter = input.value.toLowerCase();

        if(input_value !=''){
            table = document.getElementById("table-id");
            tr = table.getElementsByTagName("tr");
            // Loop through all table rows, and hide those who don't match the search query
            for (let i = 1; i < tr.length; i++) {
                let flag = 0;
                for(let j = 0; j < count; j++){
                    if(j === 7){
                        td = tr[i].getElementsByTagName("td")[j];
                        if (td) {
                            let td_text = td.innerHTML;
                            if (td.innerHTML.toLowerCase().indexOf(filter) > -1) {
                                //var td_text = td.innerHTML;
                                //td.innerHTML = 'shaban';
                                flag = 1;
                            } else {
                                //DO NOTHING
                            }
                        }
                    }
                }
                if(flag === 1){
                    tr[i].style.display = "";
                }else {
                    tr[i].style.display = "none";
                }
            }
        }else {
            //RESET TABLE
            $('#maxRows').trigger('change');
        }
    })


    document.querySelector('#search_input_price').addEventListener('keyup',(event)=>{

        let count = $('.table').children('tbody').children('tr:first-child').children('td').length;


        // Declare variables
        let input, filter, table, tr, td, i;
        input = document.getElementById("search_input_price");
        let input_value = document.getElementById("search_input_price").value;
        filter = input.value.toLowerCase();

        if(input_value !=''){
            table = document.getElementById("table-id");
            tr = table.getElementsByTagName("tr");
            // Loop through all table rows, and hide those who don't match the search query
            for (let i = 1; i < tr.length; i++) {
                let flag = 0;
                for(let j = 0; j < count; j++){
                    if(j === 8){
                        td = tr[i].getElementsByTagName("td")[j];
                        if (td) {
                            let td_text = td.innerHTML;
                            if (td.innerHTML.toLowerCase().indexOf(filter) > -1) {
                                //var td_text = td.innerHTML;
                                //td.innerHTML = 'shaban';
                                flag = 1;
                            } else {
                                //DO NOTHING
                            }
                        }
                    }
                }
                if(flag === 1){
                    tr[i].style.display = "";
                }else {
                    tr[i].style.display = "none";
                }
            }
        }else {
            //RESET TABLE
            $('#maxRows').trigger('change');
        }
    })

    document.querySelector('#search_input_Finish').addEventListener('keyup',(event)=>{

        let count = $('.table').children('tbody').children('tr:first-child').children('td').length;


        // Declare variables
        let input, filter, table, tr, td, i;
        input = document.getElementById("search_input_Finish");
        let input_value = document.getElementById("search_input_Finish").value;
        filter = input.value.toLowerCase();

        if(input_value !=''){
            table = document.getElementById("table-id");
            tr = table.getElementsByTagName("tr");
            // Loop through all table rows, and hide those who don't match the search query
            for (let i = 1; i < tr.length; i++) {
                let flag = 0;
                for(let j = 0; j < count; j++){
                    if(j === 9){
                        td = tr[i].getElementsByTagName("td")[j];
                        if (td) {
                            let td_text = td.innerHTML;
                            if (td.innerHTML.toLowerCase().indexOf(filter) > -1) {
                                //var td_text = td.innerHTML;
                                //td.innerHTML = 'shaban';
                                flag = 1;
                            } else {
                                //DO NOTHING
                            }
                        }
                    }
                }
                if(flag === 1){
                    tr[i].style.display = "";
                }else {
                    tr[i].style.display = "none";
                }
            }
        }else {
            //RESET TABLE
            $('#maxRows').trigger('change');
        }
    })



    document.querySelector('#search_input_AdditionalInfo').addEventListener('keyup',(event)=>{

        let count = $('.table').children('tbody').children('tr:first-child').children('td').length;


        // Declare variables
        let input, filter, table, tr, td, i;
        input = document.getElementById("search_input_AdditionalInfo");
        let input_value = document.getElementById("search_input_AdditionalInfo").value;
        filter = input.value.toLowerCase();

        if(input_value !=''){
            table = document.getElementById("table-id");
            tr = table.getElementsByTagName("tr");
            // Loop through all table rows, and hide those who don't match the search query
            for (let i = 1; i < tr.length; i++) {
                let flag = 0;
                for(let j = 0; j < count; j++){
                    if(j === 10){
                        td = tr[i].getElementsByTagName("td")[j];
                        if (td) {
                            let td_text = td.innerHTML;
                            if (td.innerHTML.toLowerCase().indexOf(filter) > -1) {
                                //var td_text = td.innerHTML;
                                //td.innerHTML = 'shaban';
                                flag = 1;
                            } else {
                                //DO NOTHING
                            }
                        }
                    }
                }
                if(flag === 1){
                    tr[i].style.display = "";
                }else {
                    tr[i].style.display = "none";
                }
            }
        }else {
            //RESET TABLE
            $('#maxRows').trigger('change');
        }
    })
}