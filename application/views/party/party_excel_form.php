<style>
    .bg-red {
        background-color: rgba(239, 77, 86, 0.2) !important;
    }
    .bg-success {
        background-color: rgba(113, 218, 201, 0.5) !important;
    }
</style>
<form>
    <div class="row">
        <input type="hidden" id="party_category" name="party_category" value="<?= $party_category?>">
        <input type="hidden" id="party_type" name="party_type" value="<?= $party_type?>">

        <div class="col-md-3 form-group">
			<label for="sales_executive">Sales Executives</label>
			<select name="sales_executive" id="sales_executive" class="form-control select2 req">
				<option value="">Not Assign</option>
				<?php
                if(!empty($executiveList)){
                    foreach($executiveList as $row){
                        echo '<option value="'.$row->id.'">'.$row->emp_name.'</option>';
                    }
                }
                ?>
			</select>

		</div>
        <div class="col-md-3">
            <a href="<?= base_url($headData->controller . '/createPartyMasterExcel' ) ?>" class="btn btn-block btn-info bg-info-dark mt-25" >
                <i class="fa fa-download"></i>&nbsp;&nbsp;
                <span class="btn-label">Download Excel</span>
            </a>
        </div>
      
        <div class="col-md-3">
            <input type="file" name="party_excel" id="party_excel" class="form-control float-left mt-25" accept=".xlsx, .xls" />
            <h6 class="col-md-12 msg text-primary text-center mt-1"></h6>
        </div>
       
        <div class="col-md-3">
            <a href="javascript:void(0);" class="btn btn-block btn-success bg-success-dark ml-2 importPartyExcel mt-25" type="button">
                <i class="fas fa-file-excel"></i>&nbsp;
                <span class="btn-label">Upload Excel</span>
            </a>
        </div>
        <div class="error general_error"></div>
    </div>
    <hr>
    <p class="font-bold">
        <span class="float-end text-primary">Can not save duplicate Party. Duplicate Party are shown with red color.</span><br>
    </p>
    <div class="table-responsive">
  
        <button type="button" class="btn waves-effect waves-light btn-primary" title="Load Data" style="padding: 0.3rem 0px;border-radius:0px;width:10%;"  onclick="downloadExcel();"><i class="fa fa-file-excel"></i> Excel</button><br><br>
        
        <div class="okCount"></div>
        <table class="table table-bordered jpExcelTable" id="excelTable">
            <thead class="thead-info">
                <?php
                    $html = '<tr class="text-center">
                            <th>gstin</th>
                            <th>Party Name</th>         
                            <th>Party Code</th>
                            <th>Source </th>
                            <th>Sales Region</th>
                            <th>Business Segment</th>
                            <th>Contact Person</th>
                            <th>Contact Designation</th>
                            <th>Contact No.</th>
                            <th>Whatsapp No.</th>
                            <th>Email</th>
                            <th>Credit Days</th>
                            <th>Business Cap.(Amt.)</th>
                            <th>Registration Type</th>
                            <th>Party PAN</th>
                            <th>Currency</th>
                            <th>Distance (Km)</th>
                            <th>Country</th>
                            <th>State</th>
                            <th>City </th>
                            <th>Address</th>
                            <th>PinCode</th>
                            <th>Delivery Address</th>

                   </tr>';
                    echo $html;
                ?>
            </thead>
            <tbody id="excelTbody">
                
            </tbody>
        </table>
    </div>
   
</form>

<script src="<?php echo base_url(); ?>assets/js/xlsx.full.min.js?v=<?=time()?>"></script>

<script>
$(document).ready(function(){


    $(document).on('click', '.importPartyExcel', function(e) {
        e.stopImmediatePropagation();e.preventDefault();
        $(".error").html("");
        var valid = 1;
        $(this).attr("disabled", "disabled");
        var fd = new FormData();
        fd.append("party_excel", $("#party_excel")[0].files[0]);
        fd.append("sales_executive", $("#sales_executive").val());
        fd.append("party_category", $("#party_category").val());
        fd.append("party_type", $("#party_type").val());
        if(valid){
            var ajaxParam = {
                url: base_url + controller + '/importPartyExcel',
                data:fd,
                type: "POST",
                processData:false,
                contentType:false,
                dataType:"json"
            };
            Swal.fire({
            title: 'Are you sure?',
            text: 'Are you sure Want to Upload Excel ?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, Do it!',
            }).then(function(result) {
                if (result.isConfirmed){
                    $.ajax(ajaxParam).done(function(response){
                        $(".msg").html(response.message);
                            $(this).removeAttr("disabled");
                            $("#party_excel").val(null);
                        if(response.status==1){
                            $('#excelTbody').html(response.tbodyData);
                            $('.okCount').val(response.okCount);
                            Swal.fire( 'Success', response.message, 'success' ).then(function(){ reloadTransaction() });
                        }else{
                            if(typeof response.message === "object"){
                                $(".error").html("");
                                $.each( response.message, function( key, value ) {$("."+key).html(value);});
                            }else{
                                Swal.fire( 'Sorry...!', response.message, 'error' );
                            }			
                        }			
                    });
                }
            });
        }
       
    });

    
});

function downloadExcel() {
    var longText = $("#excelTbody").html();
    
    var encodedText =encodeURIComponent(window.btoa(JSON.stringify(longText)));
    $.ajax({
        url : base_url + controller + '/downloadExcel',
        type:'post',
        data: {tbody : encodedText},
        dataType : 'json',
    }).done(function(response){
        window.location.href= response.excel_path;
    });
}
</script>