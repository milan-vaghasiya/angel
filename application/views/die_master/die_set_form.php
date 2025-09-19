<form>
    <div class="col-md-12">        
        <div class="row">
            <input type="hidden" name="id" id="id" value="">
            <input type="hidden" name="qty" id="qty" value="1" />

            <div class="col-md-6 form-group">
                <label for="item_id">Product</label>
                <select name="item_id" id="item_id" class="form-control select2 req">
                    <option value="">Select Product</option>
                    <?php
                    if (!empty($itemList)) :
                        foreach ($itemList as $row):
                            echo '<option value="'.$row->id.'">'.(!empty($row->item_code) ? '['.$row->item_code.'] ' : '').$row->item_name.'</option>';
                        endforeach;
                    endif;
                    ?>
                </select>
            </div>
        </div>

        <hr>
        <div class="row">
            <div class="error general_error"></div>
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead class="thead-info text-center">
                        <tr>
                            <th style="width:5%;">#</th>
                            <th style="width:45%">Category</th>
                            <th style="width:50%">Die List</th>
                        </tr>
                    </thead>
                    <tbody id="tbodyData">
                        <tr>
                            <td colspan="3" class="text-center">No data found.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</form>
<script>
$(document).ready(function(){
    $(document).on('change',"#item_id",function(){
		var item_id = $(this).val();

		if(item_id){
			$.ajax({
				url : base_url + controller + '/getCategoryWiseSet',
				type: 'post',
				data: { item_id:item_id },
				dataType : 'json',
			}).done(function(response){
				$("#tbodyData").html(response.tbody);
                initSelect2();
			});
		}
	});
});
function dieStore(postData){
	setPlaceHolder();
	
	var formId = postData.formId;
	var fnsave = postData.fnsave || "save";
	var controllerName = postData.controller || controller;
	var formClose = postData.form_close || "";

	var form = $('#'+formId)[0];
	var fd = new FormData(form);	

	$.ajax({
		url: base_url + controllerName + '/' + fnsave,
		data:fd,
		type: "POST",
		processData:false,
		contentType:false,
		dataType:"json",
	}).done(function(data){		
		if(data.status==1){
            initTable(); $(".modal-select2").select2();
            Swal.fire({ icon: 'success', title: data.message}).
            then(function(result) {
                window.location.reload();
		    });            
        }else{
            if(typeof data.message === "object"){
                $(".error").html("");
                $.each( data.message, function( key, value ) {$('#'+formId+" "+"."+key).html(value);});
            }else{
                initTable();
                Swal.fire({ icon: 'error', title: data.message });
            }			
        }			
	});
}
</script>