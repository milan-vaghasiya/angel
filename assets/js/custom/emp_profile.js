$(document).ready(function(){
	
	
	$(document).on("change", ".uploadProfileInput", function () {
		var triggerInput = this;
		var currentImg = $(this).closest(".pic-holder").find(".pic").attr("src");
		var holder = $(this).closest(".pic-holder");
		var wrapper = $(this).closest(".profile-pic-wrapper");
		$(wrapper).find('[role="alert"]').remove();
		triggerInput.blur();
		var files = !!this.files ? this.files : [];
		if (!files.length || !window.FileReader) {return;}
		var emp_id = $("#profileForm #emp_id").val();
		var old_profile = $("#profileForm #old_profile").val();
		if (/^image/.test(files[0].type)) {
			// only image file
			var reader = new FileReader(); // instance of the FileReader
			reader.readAsDataURL(files[0]); // read the local file

			reader.onloadend = function () {
				$(holder).addClass("uploadInProgress");
				$(holder).find(".pic").attr("src", this.result);
				$(holder).append('<div class="upload-loader"><div class="spinner-border text-primary" role="status"><span class="sr-only">Loading...</span></div></div>');
				
				var fd = new FormData();
				var files_pics = $('#newProfilePhoto')[0].files;
				if(files_pics.length > 0 ){
					fd.append('emp_profile',files_pics[0]);
					fd.append('id',emp_id);
					fd.append('form_type',"updateProfilePic");
					fd.append('old_profile',old_profile);

					$.ajax({
						url: base_url + controller + '/editProfile',
						data:fd,
						type: "POST",
						processData:false,
						contentType:false,
						cache: false,
						global:false,
						dataType:"json",
					}).done(function(data){
						if(data.status===0){
							Swal.fire({ icon: 'error', title: data.message });
							window.location.reload();
						}else if(data.status==1){ 
							Swal.fire({ icon: 'success', title: data.message});
							$("#profilePic").attr('src',data.filePath);
							window.location.reload();
						}
						$(holder).removeClass("uploadInProgress");
						$(holder).find(".upload-loader").remove();
						$(triggerInput).val("");
					});
				}
			};
		}
		else{
			Swal.fire({ icon: 'error', title: "Please choose the valid image." });
			$(wrapper).find('role="alert"').remove();
		}
	});
});
	
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
			Swal.fire({ icon: 'success', title: data.message});
			if(data.form_type == "empExperience"){					
				$('#'+formId)[0].reset();
				$("#expBody").html(data.tbodyData);
			}
			if(data.form_type == "empNomination"){
				$('#'+formId)[0].reset();
				$("#empNomBody").html(data.tbodyData);
			}
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
	var resFunctionName = data.res_function || "";
	
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
				if(resFunctionName != ""){
					window[resFunctionName](response);
				}else{
					if(response.status==0){
						Swal.fire( 'Sorry...!', response.message, 'error' );
					}else{
						if(response.form_type == "empExperience"){					
							$("#expBody").html(response.tbodyData);
						}
						if(response.form_type == "empNomination"){
							$("#empNomBody").html(response.tbodyData);
						}
						
						Swal.fire( 'Deleted!', response.message, 'success' );
					}	
				}
			});
		}
	});
}


function resPersonalDetail(data,formId){
    if(data.status==1){
        Swal.fire({ icon: 'success', title: data.message});
        window.location.reload();
    }else{
        if(typeof data.message === "object"){
            $(".error").html("");
            $.each( data.message, function( key, value ) { $("."+key).html(value); });
        }else{
            Swal.fire({ icon: 'error', title: data.message });
        }			
    }
}

function editEmpFacility(data) { 
    $.each(data, function (key, value) {
        $("#getEmpFacility #" + key).val(value);
	});
    $("#item_id").select2();
}
