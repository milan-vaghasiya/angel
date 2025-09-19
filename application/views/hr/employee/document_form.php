<form autocomplete="off" enctype="multipart/form-data">
    <div class="col-md-12">
        <div class="row">
			<input type="hidden" name="id" id="id" class="id" value="<?=(!empty($dataRow->id))?$dataRow->id:""; ?>" />
            <input type="hidden" name="emp_id" value="<?=(!empty($dataRow->id))?$dataRow->id:""; ?>" />
            <input type="hidden" name="emp_code" value="<?=(!empty($dataRow->emp_code))?$dataRow->emp_code:""; ?>" />
			<input type="hidden" name="form_type" value="updateDocumnet" />
			
			<div class="col-md-3 form-group">
				<label for="aadhar_no">Aadhar No</label>
				<input type="text" name="empDetails[aadhar_no]" id="aadhar_no" class="form-control" value="<?=(!empty($dataRow->aadhar_no))?$dataRow->aadhar_no:""; ?>" />
			</div>
			<div class="col-md-3 form-group">
				<label for="aadhar_file">Aadhar File</label>
				<div class="input-group">
					<input type="file" name="aadhar_file" class="form-control" value="" />
					<?php if(!empty($dataRow->aadhar_file)): ?>
						<div class="input-group-append">
							<a href="<?=base_url('assets/uploads/emp_documents/'.$dataRow->aadhar_file)?>" class="btn btn-outline-primary" target="_blank"><i class="fas fa-download"></i></a>
						</div>
					<?php endif; ?>
				</div>
			</div>
			<div class="col-md-3 form-group">
				<label for="pan_no">Pan No.</label>
				<input type="text" name="empDetails[pan_no]" id="pan_no" class="form-control" value="<?=(!empty($dataRow->pan_no))?$dataRow->pan_no:""; ?>" />
			</div>
			
			<div class="col-md-3 form-group">
				<label for="pan_file">Pan File</label>
				<div class="input-group">
					<input type="file" name="pan_file" class="form-control" value="" />
					<?php if(!empty($dataRow->pan_file)): ?>
						<div class="input-group-append">
							<a href="<?=base_url('assets/uploads/emp_documents/'.$dataRow->pan_file)?>" class="btn btn-outline-primary" target="_blank"><i class="fas fa-download"></i></a>
						</div>
					<?php endif; ?>
				</div>
			</div>
		
        </div>
    </div>
</form>
<!-- <hr>
<div class="row">
	<div class="col-md-12 form-group">
		<table id="inspection" class="table table-bordered align-items-center">
			<thead class="thead-info">
				<tr>
					<th class="text-center" style="width:5%;">#</th>
					<th class="text-center">Document Name</th>
					<th class="text-center">Document No.</th>
					<th class="text-center">Document File</th>
					<th class="text-center" style="width:10%;">Action</th>
				</tr>
			</thead>
			<tbody id="docBody">
				<tr>
					<?php
						if(!empty($empDocs)):
							$i=1;
							foreach($empDocs as $row):
								$deleteParam = "{'postData':{'id' : ".$row->id.",'emp_id' : ".$row->emp_id.",'form_type' : 'empDocs'}, 'fndelete' : 'deleteEmpDocs','message' : 'Employee'}";
								echo '<tr>
											<td class="text-center">'.$i++.'</td>
											<td class="text-center">'.$row->doc_name.'</td>
											<td class="text-center">'.$row->doc_no.'</td>
											<td class="text-center">'.((!empty($row->doc_file))?'<a href="'.base_url('assets/uploads/emp_documents/'.$row->doc_file).'" target="_blank"><i class="fa fa-download"></i></a>':"") .'</td>
											<td class="text-center">
												<button type="button" onclick="trashEmpProfile('.$deleteParam.');" class="btn btn-sm btn-outline-danger waves-effect waves-light btn-delete permission-remove"><i class="mdi mdi-trash-can-outline"></i></button>
											</td>
										</tr>';
							endforeach;
						else:
							echo '<tr><td colspan="5" style="text-align:center;">No Data Found</td></tr>';
						endif;
					?>
				</tr>
			</tbody>
		</table>
	</div>
</div>
<script>
	function saveEmpFormData(formId,fnsave){
		setPlaceHolder();
		if(fnsave == "" || fnsave == null){fnsave="save";}
		var form = $('#'+formId)[0];
		var fd = new FormData(form); 
		$.ajax({
			url: base_url + controller + '/' + fnsave,
			data:fd,
			type: "POST",
			processData:false,
			contentType:false,
			dataType:"json",
		}).done(function(data){
			if(data.status===0){
				$(".error").html("");
				$.each( data.message, function( key, value ) {$("."+key).html(value);});
			}else if(data.status==1){
				initTable();$('#'+formId)[0].reset();
				Swal.fire({ icon: 'success', title: data.message});
				$("#docBody").html(data.tbodyData);
			}else{
				$('#'+formId)[0].reset();
				Swal.fire({ icon: 'error', title: data.message });
			}
					
		});
	}
	
	function trashEmpProfile(data){
		var controllerName = data.controller || controller;
		var fnName = data.fndelete || "delete";
		var msg = data.message || "Record";
		var send_data = data.postData;
		
		Swal.fire({
			title: 'Are you sure?',
			text: "You won't be able to revert this!",
			icon: 'warning',
			showCancelButton: true,
			confirmButtonColor: '#3085d6',
			cancelButtonColor: '#d33',
			confirmButtonText: 'Yes, delete it!',
		}).then(function(result) {
			if (result.isConfirmed){
				$.ajax({
					url: base_url + controllerName + '/' + fnName,
					data: send_data,
					type: "POST",
					dataType:"json",
				}).done(function(response){
					if(response.status == 1){
						initTable();
						Swal.fire({ icon: 'success', title: response.message});
						$("#docBody").html(response.tbodyData);
					}else{
						if(response.status==0){
							Swal.fire( 'Sorry...!', response.message, 'error' );
						}else{
							$("#docBody").html(response.tbodyData);
							Swal.fire( 'Deleted!', response.message, 'success' );
						}	
					}
				});
			}
		});
	}
</script> -->