<form enctype="multipart/form-data" id="attendForm">
    <div class="col-md-12">
        <div class="row">
            <div class="col-md-5 form-group">
                <label for="attendance_date">Attendance Date </label>
                <input type="date" name="attendance_date" id="attendance_date" class="form-control" value="<?=date("Y-m-d")?>" />
            </div>
            <div class="col-md-12 form-group">
                <label for="attendance_excel_label">Upload Attendance Excel</label>

                <div class="input-group">
                    <div class="input-group-append">
                        <a href="<?=base_url("hr/attendance/createAttendanceExcel")?>" class="btn btn-info" title="Download" target="_balnk"><i class="fa fa-arrow-down"></i> Download</a>
                    </div>
                    <div class="input-group-append" style="width:55%;">
                        <div class="custom-file">
                            <input type="file" class="form-control custom-file-input" name="attendance_excel" id="attendance_excel" accept=".xlsx, .xls, .csv" />
                        </div>
                    </div>
                    <div class="input-group-append">
                        <button type="button" class="btn btn-success btn-custom-save" title="Upload"><i class="fa fa-arrow-up"></i> Upload</button>
                    </div>
                 
                </div>
            </div>

            <div class="col-md-12 form-group excelUploadResponse">

            </div>
        </div>
    </div>
</form>
<script>
function resAttendanceUpload(response,formId){
    if(response.status==1){
        initTable();
        $(".excelUploadResponse").html("");
        $(".excelUploadResponse").html(response.message);
        $("#"+formId)[0].reset();
    }else{
        if(typeof response.message === "object"){
            $(".error").html("");
            $.each( response.message, function( key, value ) {$("."+key).html(value);});
        }else{
            initTable();
            Swal.fire( 'Sorry...!', response.message, 'error' );
        }			
    }	
}
</script>