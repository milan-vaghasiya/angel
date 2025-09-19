<form>
    <div class="col-md-12">

        <div class="row">
            <div class="col-md-6 form-group">
                <label for="executive">Executive</label>
                <select id="executive" class="form-control select2">
                    <option value="">Select Executive</option>
                    <option value="0">Not Assign</option>
                    <?php
                    if(!empty($empList)){
                        foreach($empList as $row){
                            echo '<option value="'.$row->id.'">'.$row->emp_name.'</option>';
                        }
                    }
                    ?>
                </select>
            </div>

            <div class="col-md-6 form-group">
                <label for="executive_id">Assign Executive</label>
                <select name="executive_id" id="executive_id" class="form-control select2 req">
                    <option value="">Select Executive</option>
                    <?php
                    if(!empty($empList)){
                        foreach($empList as $row){
                            echo '<option value="'.$row->id.'">'.$row->emp_name.'</option>';
                        }
                    }
                    ?>
                </select>
            </div>
        </div>
        <hr>
        <div class="row">
            <div class="error general_error"></div>
            <div class="table-responsive">
                <table class="table table-bordered table-striped jpExcelTable" id="executiveTable">
                    <thead class="gradient-theme">
                        <tr class="text-center">
                            <th style="width:10%;">
                                <input type="checkbox" id="masterSelect" class="filled-in chk-col-success BulkExecutive" value=""><label for="masterSelect" class="text-white"> ALL</label>
                            </th>
                            <th style="width:20%;">Party Name</th>
                            <th style="width:20%;">Country</th>
                            <th style="width:20%;">Address</th>
                        </tr>
                    </thead>
                    <tbody id="tbodyData">
                   
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</form>

<script>
$(document).ready(function() {

    setTimeout(function(){
        executiveList('executiveTable');
	},5);
    
    $(document).on('click', '.BulkExecutive', function() {
        if ($(this).attr('id') == "masterSelect") {
            if ($(this).prop('checked') == true) {
                $("input[name='ref_id[]']").prop('checked', true);
            } else {
                $("input[name='ref_id[]']").prop('checked', false);
            }
        } else {
            if ($("input[name='ref_id[]']").not(':checked').length != $("input[name='ref_id[]']").length) {
                $("#masterSelect").prop('checked', false);
            } else {                
            }
            if ($("input[name='ref_id[]']:checked").length == $("input[name='ref_id[]']").length) {
                $("#masterSelect").prop('checked', true);
            }
            else{$("#masterSelect").prop('checked', false);}
        }
    });   
    
    $(document).on('change', '#executive', function (e){
    e.stopImmediatePropagation();e.preventDefault();
        var executive = $('#executive').val();

        if(executive){
            $.ajax({
                url:base_url + controller + "/getPartyData",
                type:'post',
                data:{executive:executive},
                dataType:'json',
                success:function(data){
                    if(data.status == 1){
				        $("#executiveTable").DataTable().clear().destroy();
                        $('#tbodyData').html('');
                        $('#tbodyData').html(data.tbodyData);
				        executiveList('executiveTable');
                    }
                }
            });
        }
    });
    
});

function executiveList(tableId = "executiveTable"){
    var tableOptions = {
        responsive: true,
        "autoWidth" : false,
        'ordering':false,
        "columnDefs": [
            { type: 'natural', targets: 0 },
            { orderable: false, targets: "_all" }, 
            { className: "text-left", targets: [0] }, 
            { className: "text-center", "targets": "_all" } 
        ],
        "paging": false,
        "bInfo": false,
        buttons: {
            dom: { button: { className: "btn btn-outline-dark" } },
            buttons:[  ]
        },
        language: { search: "",searchPlaceholder: "Search...","emptyTable": "No Data available..."}
    };
    var reportTable = $('#'+tableId).DataTable(tableOptions);
    $('.dataTables_filter .form-control-sm').css("width","100%");
    $('.dataTables_filter .form-control-sm').addClass("csearch");
    $('.dataTables_filter .form-control-sm').css("margin-bottom","5px");
    $('.dataTables_filter .form-control-sm').attr("placeholder","Search.....");
    $('.dataTables_filter').css("text-align","right");
    return reportTable;
}

</script>