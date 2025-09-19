<?php $this->load->view('includes/header');?>
<link href="<?=base_url()?>assets/plugins/tobii/tobii.min.css" rel="stylesheet" type="text/css" />
<style>
    .nav-pills .select2-container--default .select2-selection--single .select2-selection__rendered{line-height:32px!important;}.nav-pills .select2-container .select2-selection--single{height:32px!important;border-color: #4798e8!important;}
</style>
<div class="page-content-tab">
	<div class="container-fluid">
		<div class="row">
			<div class="col-12">
				<div class="card">
					<div class="card-header">
						<div class="float-end">
							<?php
								$addParam = "{'postData' : {'party_category' : '1', 'party_type' : '2'}, 'modal_id' : 'bs-right-lg-modal', 'call_function' : 'addParty', 'form_id' : 'addLead', 'title' : 'Add Lead', 'controller' : 'parties', 'js_store_fn' : 'saveLead'}";
							?>
							<button type="button" class="btn waves-effect waves-light btn-outline-dark float-right permission-write press-add-btn" onclick="modalAction(<?=$addParam?>);"><i class="fa fa-plus"></i> Add Lead</button>
						</div>
						<ul class="nav nav-pills" id="buttonFilter" style="width:60%;">
							<li class="nav-item"> <button data-filter=".pending_response" class="buttonFilter stageFilter btn btn-outline-info active activeStage mr-1" data-postdata='{"party_type":""}' data-party_type="" data-toggle="tab">Pending Response</button> </li>
							<?php
								if(!empty($stageList)){
									foreach($stageList as $row) {
										if($row->sequence != 1){
											?>
											<li class="nav-item"> <button data-filter=".<?=$row->id?>_lead" class="buttonFilter stageFilter mr-1 btn btn-outline-info" data-party_type="<?=$row->id?>" data-postdata='{"party_type":"<?=$row->id?>"}' data-toggle="tab"><?=$row->stage_type?></button> </li>
											<?php
										}
									}
								}
							?>
							<li style="width:150px;">
								<select name="lead_source" id="lead_source" class="form-control select2">
									<option value="All">All source</option>
									<?php
									if(!empty($sourceList)):
										foreach($sourceList as $row):
											echo '<option value="'.$row->label.'" >'.$row->label.'</option>';
										endforeach;
									endif;
									?>
								</select>
							</li>
						</ul>
					</div>
					<div class="card-body">
						<div class="row">
							<div class="col-lg-9">
								<div class="crm-desk-left" id="leadBoard">
									<div class="cd-search mb-1">
										<div class="form-group"> 
											<div class="jpsearch" id="qs">                                                
											<input type="text" id="quick_search" class="form-control qs quicksearch" placeholder="Search Here...">
											</div>                                                    
										</div>
									</div>
									<div class="cd-body-left" data-simplebar>
										<div class="cd-list jpPanel-widget">
											<div class="card-body row grid leadData"></div>
										</div>
									</div>
								</div>
							</div>
							<div class="col-lg-3">
								<div class="crm-desk-right" id="partyData">
									<div class="cd-header">
										<div class="media">
											<div class="media-left party_image_view">
												<img src="<?=base_url()?>assets/images/users/user_default.png" alt="user" class="rounded-circle thumb-sm party_image">
											</div>
											<div class="media-body">
												<!-- <div class="row"> -->
													<h6 class="m-0 partyName text-uppercase"><?=(!empty($companyData->company_name) ? $companyData->company_name : 'ANGEL')?></h6>
													<p class="mb-0 lastSeen">Welcomes You</p>
												<!-- </div> -->
											</div>
										</div>
										<div class="cd-features visually-hidden">
											<div class="d-none d-sm-inline-block">
												<a type="button" class="text-danger permission-write btn-remind dropdown-toggle" data-bs-toggle="dropdown" data-bs-auto-close="outside"   data-form_title="Add Reminder" datatip="Add Reminder" data-bind="enable: !noResults()"><i class="far fa-bell fs-22"></i></a>
												
												<div class="dropdown-menu dropdown-lg" id="remind_dd">
													<form id="reminderForm">
														<div class="col-md-12">
															<div class="row">
																<input type="hidden" name="id" id="id" value="" />
																<input type="hidden" name="party_id" id="party_id" value="" />
																<input type="hidden" name="log_type" id="log_type" value="3" />
																<div class="col-md-6 form-group">
																	<label for="ref_date">Date</label>
																	<input type="date" name="ref_date" id="ref_date" class="form-control req" value="<?=(!empty($dataRow->ref_date ))?$dataRow->ref_date :date("Y-m-d")?>" min="<?=date("Y-m-d")?>" />
																</div>
																<div class="col-md-6 form-group">
																	<label for="reminder_time">Time</label>
																	<input type="time" name="reminder_time" id="reminder_time" class="form-control req" value="<?=(!empty($dataRow->reminder_time))?date("h:i:s",strtotime($dataRow->reminder_time)):date("h:i:s")?>" min="<?=date("h:i:s")?>" />
																</div>
																<div class="col-md-12 form-group">
																	<label for="mode">mode</label>
																	<select name="mode" id="mode" class="form-control select2 req">
																		<?php
																			foreach($this->appointmentMode as $key=>$mode):
																				$selected = (!empty($dataRow->mode) and $dataRow->mode == $mode)?"selected":"";
																				echo '<option value="'.$mode.'" '.$selected .'>'.$mode.'</option>';
																			endforeach;
																		?>
																	</select>
																	<div class="error mode"></div>
																</div>
																<div class="col-md-12 form-group">
																	<label for="notes">Notes</label>
																	<textarea name="notes" class="form-control" rows="3"><?=(!empty($dataRow->notes))?$dataRow->notes:""?></textarea>
																</div>
																<div class="col-md-12">
																	<button type="button" class="btn btn-success btn-round btn-outline-dashed btn-block saveReminder" >Save Reminder</button>
																</div>
															</div>        
														</div>
													</form>
												</div>
											</div>
										</div>
									</div>
									<div class="cd-body cd_body" id="cd_body" data-simplebar style="overflow1:scroll;" >
										<div class="cd-detail salesLog1 slimscroll activity-scroll" id="salesLog" >
											<div class="activity salesLog">
												<img src="<?=base_url('assets/images/background/crm_desk_bg.png')?>" style="width:90%;position:absolute;bottom:28px;left:5%;">
											</div>
										</div>                                              
									</div>
									<div class="cd-footer visually-hidden">
										<textarea type="text" rows="1" name="msg_content" id="msg_content" class="form-control" placeholder="Type a Message..." style="width:90%"></textarea>
										<a type="button" class="text-secondary saveFollowups"><i class="la la-send"></i></a>
									</div>
								</div>			
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
    </div>
</div>
<input type="hidden" id="next_page" value="0" />
<a href="#" class="next_page" type="button" data-next_page="0" ></a>
<?php $this->load->view('includes/footer'); ?>
<script src="<?=base_url()?>assets/plugins/tobii/tobii.min.js?v=<?=time()?>"></script>
<script>
$(document).ready(function(){
	setTimeout(function(){ $(".stageFilter.active").trigger("click"); }, 50);	
	
	initSelect2();setPlaceHolder();
	var rec_per_page = "<?=$rec_per_page?>";
	$("#msg_content").keypress(function (e) {
		if(e.which === 13 && !e.shiftKey) {
			e.preventDefault();
			$(".saveFollowups").trigger("click");
		}
	});

    $(document).on('change','#lead_source',function(){ $(".stageFilter.active").trigger("click"); });

    $(document).on('click','.stageFilter',function(){
        var lead_source = $("#lead_source").val() || "";
        var postdata = $(this).data('postdata') || {};
		var np = parseFloat($('#next_page').val()) || 0;
		postdata.start = 0;
		postdata.length = parseFloat(rec_per_page);
		postdata.page = 0;
		postdata.lead_source = lead_source;
		
		loadHtmlData({'fnget':'getLeadData','rescls':'leadData','postdata':postdata});
	});
	
	$('.quicksearch').keyup(delay(function (e) {
		//if(e.which === 13 && !e.shiftKey) {
			e.preventDefault();
			var lead_source = $("#lead_source").val() || "";
			$('#next_page').val('0');
			var postdata = $('.stageFilter.active').data('postdata') || {};
			delete postdata.page;delete postdata.start;delete postdata.length;
			postdata.limit = parseFloat(rec_per_page);
		    postdata.lead_source = lead_source;
			postdata.skey = $(this).val();
			loadHtmlData({'fnget':'getLeadData','rescls':'leadData','postdata':postdata});
		//}
	}));
	
	const scrollEle = $('#leadBoard .simplebar-content-wrapper');
	var ScrollDebounce = true;
	$(scrollEle).scroll(function() {
		if($(this).scrollTop() + $(this).innerHeight() >= ($(this)[0].scrollHeight - 10)) {
		    if(ScrollDebounce){
    			ScrollDebounce = false;
    			var lead_source = $("#lead_source").val() || "";
    			var postdata = $('.stageFilter.active').data('postdata') || {};
    			var np = parseFloat($('#next_page').val()) || 0;
    			postdata.start = np * parseFloat(rec_per_page);
    			postdata.length = rec_per_page;
    			postdata.page = np;
    			postdata.lead_source = lead_source;
    			loadHtmlData({'fnget':'getLeadData','rescls':'leadData','postdata':postdata,'scroll_type':1});
    			setTimeout(function () { ScrollDebounce = true; }, 500);
		    }
		}
	});
	
    $(document).on('click','.partyData',function(){
        var party_id = $(this).data('party_id');
        var lead_id = $(this).data('lead_id');
        $("#party_id").val(party_id);
        $("#lead_id").val(lead_id);

        $.ajax({
            url: base_url + controller + '/getLeadDetails',
            data: { party_id:party_id, lead_id:lead_id },
            global:false,
            type: "POST",
            dataType:"json",
        }).done(function(data){
            if(data.imgFile != null && data.imgFile != undefined && data.imgFile != ""){
                $('.party_image_view').html(data.imgFile);
                	const tobii = new Tobii({
									captions: false,
									zoom: false,
								});
            }else{
                var imageFile = base_url + 'assets/images/users/user_default.png';
                 $('.party_image_view').html('<img src="'+imageFile+'" class="thumb-sm">');
            }

            $(".partyName").html(data.partyData.party_name);
            $(".salesLog").html(data.salesLog);
            if(data.partyData.executive_id == 0){
                $(".salesOption").hide();
            }else{
                $(".salesOption").show();
            }
            $(".cd-features").removeClass("visually-hidden");
            $(".cd-footer").removeClass("visually-hidden");
			scrollBottom();
        });
		
		
	});

    $(document).on('click','.saveFollowups',function(){
        var party_id = $("#party_id").val();
        var lead_id = $("#lead_id").val();
        var notes = $("#msg_content").val();

		if(notes != ''){
			$.ajax({
				url: base_url + controller + '/saveSalesLog',
				data: {party_id:party_id, lead_id:lead_id, notes:notes,log_type:2,id:''},
				type: "POST",
				global:false,
				dataType:"json",
			}).done(function(response){
				if(response.status==1){$("#msg_content").val('');$(".salesLog").html(response.salesLog);}
				scrollBottom();
			});
		}
        
	});
	
    $(document).on('click','.saveReminder',function(){
        var formId = "reminderForm";
		var form = $('#'+formId)[0];
		var fd = new FormData(form);

		$.ajax({
			url: base_url + controller + '/saveSalesLog',
			data:fd,
			type: "POST",
			global:false,
			processData:false,
			contentType:false,
			dataType:"json",
		}).done(function(response){
			if(response.status==1)
			{
				$(".salesLog").html(response.salesLog);
				$("#reminderForm")[0].reset();
				$('#remind_dd').toggleClass('show');
				// getLeadData();
			}
			else{$(".error").html("");$.each( response.message, function( key, value ) {$("."+key).html(value);});}
			scrollBottom();
		});
	});

    $(document).on('click',".addCrmForm",function(){
        var functionName = $(this).data("function");
        var modalId = $(this).data('modal_id');
        var button = $(this).data('button');
		var title = $(this).data('form_title');
		var formId = $(this).data('form_id') || functionName.split('/')[0];
		var controllerName = $(this).data('controller') || controller;
		var party_id = $("#party_id").val() || {};
		var lead_id = $("#lead_id").val() || {};
		var fnsave = $(this).data("fnsave") || "save";
		var ref_id= $(this).data("ref_id") || "";
		var entry_type= $(this).data("entry_type") || "";
		var module_type= $(this).data("module_type") || "";
		var jsStoreFn = 'storeCrm';
		var fnJson = "{'formId':'"+formId+"','controller':'"+controllerName+"','fnsave':'"+fnsave+"'}";

        $.ajax({ 
            type: "post",   
            url: base_url + controllerName + '/' + functionName,   
            data: {party_id:party_id,lead_id:lead_id,ref_id:ref_id,entry_type:entry_type,module_type:module_type}
        }).done(function(response){
            $("#"+modalId).modal('show');
            $("#"+modalId).css({'z-index':9999,'overflow':'auto'});
			$("#"+modalId+'').addClass(formId+"Modal");
			$("#"+modalId+' .modal-title').html(title);
			$("#"+modalId+' .modal-body').html("");
            $("#"+modalId+' .modal-body').html(response);
            $("#"+modalId+" .modal-body form").attr('id',formId);
			// $("#"+modalId+" .modal-footer .btn-save").attr('onclick',"store("+fnJson+");");
		    $("#"+modalId+" .modal-footer .btn-save").attr('onclick',jsStoreFn+"("+fnJson+");");
			$("#"+modalId+" .modal-header .close").attr('data-modal_id',modalId);
			$("#"+modalId+" .modal-header .close").attr('data-modal_class',formId+"Modal");
			$("#"+modalId+" .modal-footer .btn-close").attr('data-modal_id',modalId);
			$("#"+modalId+" .modal-footer .btn-close").attr('data-modal_class',formId+"Modal");

            $("#"+modalId+" .modal-footer .btn-close").show();
            $("#"+modalId+" .modal-footer .btn-save").show();
			
			setTimeout(function(){ setPlaceHolder(); initSelect2(modalId); }, 5);
        });
    });	

});

function scrollBottom(){
	$("#cd_body .simplebar-content-wrapper").animate({ scrollTop: $('#cd_body .simplebar-content-wrapper').prop("scrollHeight")}, 500);
}

function saveLead(postData){
	setPlaceHolder();
	$(".btn-save").attr("disabled", true);	
	var formId = postData.formId;
	var fnsave = postData.fnsave || "save";
	var controllerName = postData.controller || controller;

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
		$(".btn-save").removeAttr("disabled");
	    if(data.status==1){
			initTable(); $('#'+formId)[0].reset(); closeModal(formId);
			Swal.fire({ icon: 'success', title: data.message});
			getLeadData(); 
		}else{
			if(typeof data.message === "object"){
				$(".error").html("");
				$.each( data.message, function( key, value ) {$("."+key).html(value);});
			}else{
				Swal.fire({ icon: 'error', title: data.message });
			}			
		}	
        
	});
}

function leadEdit(data){
	var button = data.button;if(button == "" || button == null){button="both";};
	var fnedit = data.fnedit;if(fnedit == "" || fnedit == null){fnedit="edit";}
	var fnsave = data.fnsave;if(fnsave == "" || fnsave == null){fnsave="save";}
	var form_id = data.form_id;if(form_id == "" || form_id == null){form_id="saveLead";}
	var title = data.title;if(title == "" || title == null){title="";}
	var is_confirm = data.confirm || '';
	if(is_confirm == 1){
		Swal.fire({
			title: 'Are you sure?',
			text: data.message,
			icon: 'warning',
			showCancelButton: true,
			confirmButtonColor: '#3085d6',
			cancelButtonColor: '#d33',
			confirmButtonText: 'Yes, delete it!',
		}).then(function(result) {
			if (result.isConfirmed)
			{
				$.ajax({
					url: base_url + controller + '/' + fnsave,
					data: data.postData,
					type: "POST",
					dataType:"json",
				}).done(function(response){
					if(response.status==1){
						Swal.fire({ icon: 'success', title: response.message});
						getLeadData();
					}else{
						Swal.fire({ icon: 'error', title: response.message });			
					}	
				});
			}
		});
		
	}else{
		$.ajax({ 
			type: "POST",   
			url: base_url + controller + '/'+fnedit,   
			data: data.postData,
		}).done(function(response){
			$("#"+data.modal_id).css({'z-index':1059});
			$('#'+data.modal_id+' .modal-title').html(title);
			$('#'+data.modal_id+' .modal-body').html(response);
			$("#"+data.modal_id+" .modal-body form").attr('id',form_id);
			$("#"+data.modal_id).addClass(form_id+"Modal");
			$("#"+data.modal_id+" .modal-footer .btn-save").attr('onclick',"leadStore('"+form_id+"','"+fnsave+"');");
			$('#'+data.modal_id).modal('show');
			if(button == "close"){
				$("#"+data.modal_id+" .modal-footer .btn-close").show();
				$("#"+data.modal_id+" .modal-footer .btn-save").hide();
			}else if(button == "save"){
				$("#"+data.modal_id+" .modal-footer .btn-close").hide();
				$("#"+data.modal_id+" .modal-footer .btn-save").show();
			}else{
				$("#"+data.modal_id+" .modal-footer .btn-close").show();
				$("#"+data.modal_id+" .modal-footer .btn-save").show();
			}
			$(".select2").select2();
			setPlaceHolder();
		});
	}
	
}

function leadStore(formId,fnsave){
	$(".btn-save").attr("disabled", true);
	setPlaceHolder();
	var form = $('#'+formId)[0];
	var fd = new FormData(form);
	$.ajax({
		url: base_url + controller + '/' + fnsave,
		data:fd,
		type: "POST",
		processData:false,
		contentType:false,
		dataType:"json",
	}).done(function(response){
		$(".btn-save").removeAttr("disabled");
		if(response.status==1){
			closeModal(formId);
			Swal.fire({ icon: 'success', title: response.message});
			getLeadData();
		}else{
			if(typeof response.message === "object"){
				$(".error").html("");
				$.each( response.message, function( key, value ) {$("."+key).html(value);});
			}else{
				Swal.fire({ icon: 'error', title: response.message });
			}			
		}				
	});
}

function storeCrm(postData){
	setPlaceHolder();
		
	var formId = postData.formId;
	var fnsave = postData.fnsave || "save";
	var controllerName = postData.controller || controller;

	var form = $('#'+formId)[0];
	var fd = new FormData(form);
	$(".btn-save").attr("disabled", true);	
	$.ajax({
		url: base_url + controllerName + '/' + fnsave,
		data:fd,
		type: "POST",
		processData:false,
		contentType:false,
		dataType:"json",
	}).done(function(data){
		$(".btn-save").removeAttr("disabled");
		if(data.status==1){
			closeModal(formId);
			$(".salesLog").html(data.salesLog);
			Swal.fire({ icon: 'success', title: data.message});
			// getLeadData();
		}else{
			if(typeof data.message === "object"){
				$(".error").html("");
				$.each( data.message, function( key, value ) {$("."+key).html(value);});
			}else{
				Swal.fire({ icon: 'error', title: data.message });
			}			
		}	
	});
}

function getLeadData(){
	$(".stageFilter.active").trigger("click");
}

function trashLead(data){
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
		if (result.isConfirmed)
		{
			$.ajax({
				url: base_url + controllerName + '/' + fnName,
				data: send_data,
				type: "POST",
				dataType:"json",
			}).done(function(response){
				if(response.status==0){
					Swal.fire( 'Sorry...!', response.message, 'error' );
				}else{
					Swal.fire( 'Deleted!', response.message, 'success' ).then((result) => { getLeadData(); });
				}	
			});
			Swal.fire( 'Deleted!', 'Your file has been deleted.', 'success' );
		}
	});	
}

function saveSalesLog(postData){
	setPlaceHolder();
	$(".btn-save").attr("disabled", true);	
	var formId = postData.formId;
	var fnsave = postData.fnsave || "save";
	var controllerName = postData.controller || controller;

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
		$(".btn-save").removeAttr("disabled");
	    if(data.status==1){
			$('#'+formId)[0].reset(); closeModal(formId);
			$(".salesLog").html(data.salesLog);
			Swal.fire({ icon: 'success', title: data.message});
		}else{
			if(typeof data.message === "object"){
				$(".error").html("");
				$.each( data.message, function( key, value ) {$("."+key).html(value);});
			}else{
				Swal.fire({ icon: 'error', title: data.message });
			}			
		}	        
	});
}

function loadHtmlData(data){
	
	var postData = data.postdata || {};
	var fnget = data.fnget || "";
	var controllerName = data.controller || controller;
	var rescls = data.rescls || "dynamicData";
	var scrollType = data.scroll_type || "";
	
	$.ajax({
		url: base_url + controllerName + '/' + fnget,
		data:postData,
		type: "POST",
		dataType:"json",
		global:false,
	}).done(function(res){
		$("#next_page").val(res.next_page);
		if(!scrollType){$("."+rescls).html(res.leadDetail);}else{$("."+rescls).append(res.leadDetail);}
		loading = true;
	});
}

function delay(callback, ms) {
	var timer = 0;
	return function() {
		var context = this, args = arguments;
		clearTimeout(timer);
		timer = setTimeout(function () {
			callback.apply(context, args);
		}, ms || 0);
	};
}
</script>