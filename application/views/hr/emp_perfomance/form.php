<form>
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" name="id" id="id" value="" />

           <div class="col-md-6 form-group">   
                <label for="month">Month</label>
                <select name="month" id="month" class="form-control select2">
                    <option value="">Month</option>
                    <?php   
                        foreach($monthList as $row):
                            $selected = ((!empty($dataRow->month) && date('M-Y', strtotime($dataRow->month)) == $row['label']) ? "selected" : ""); 
                            $disabled = ((!empty($dataRow->month) && date('M-Y', strtotime($dataRow->month)) != $row['label']) ? "disabled" : "");
                            echo '<option value="'. $row['label'] .'" '.$selected.' '.$disabled.'>'.$row['label'].'</option>';
                        endforeach; 
                    ?>
                </select>
			    <div class="error month"></div>
		    </div> 
			<div class="col-md-6 form-group">
                <label for="emp_id">Employee</label>
                <div class="input-group">
                    <div class="input-group-append" style="width:70%;">
                        <select name="emp_id" id="emp_id" class="form-control select2 req">
                            <option value="">Select Employee</option>
                            <?php
                                foreach($empList as $row){
                                    $selected = ((!empty($dataRow->emp_id) && $dataRow->emp_id == $row->id) ? "selected" : "");
                                    $disabled = ((!empty($dataRow->emp_id) && $dataRow->emp_id != $row->id) ? "disabled" : "");
                                    echo '<option value="'.$row->id.'" '.$selected.' '.$disabled.' data-dept_id="'.$row->dept_id.'" data-desi_id="'.$row->designation_id.'">'.$row->emp_name.'</option>';
                                }
                            ?>
                        </select>
                    </div>
                    <div class="input-group-append" style="width:30%;">
                        <button type="button" class="btn waves-effect waves-light btn-success float-right loadData" title="Load Data">
                            <i class="fas fa-sync-alt"></i> Load
                        </button>
                    </div>
                </div>
            </div>
			
            <hr>
			
            <div class="row">
                <div class="col-md-12">
                    <table id="empTblData" class="table table-bordered">
                        <thead id="theadData" class="thead-dark">
                            <tr>
                                <th style="width:5%;">#</th>
                                <th style="width:55%;">KPI</th>
                                <th style="width:20%;">Weightage</th>
                                <th style="width:20%;">Result(%)</th>
                            </tr>
                        </thead>
                        <tbody id="tbodyData">
                            <?php
                            $i=1;
                                if(!empty($perfomanceData)):
                                    $kpiList = array_reduce($perfomanceData, function($kpiList, $kpi) { $kpiList[$kpi->kpi_name][] = $kpi; return $kpiList; }, []);
                                    foreach ($kpiList as $kpi_name=>$rows):
                                        echo '<tr><th colspan="4">'.$kpi_name.'</th></tr>';
                                        foreach ($rows as $row) { 
                                        $m = $row->{'m' .(int)date('m', strtotime($dataRow->month))};
                                        echo '<tr>
                                                <td>'.$i.'</td>
                                                <td>'.$row->kpi_desc.'</td>
                                                <td>'.$row->req_per.'</td>
                                                <td>
                                                    <input type="hidden" name="id[]" value="'.$row->id.'">
                                                    <input type="hidden" name="kpi_id[]" value="'.$row->trans_id.'">
                                                    <input type="text" name="current_per[]" id="current_per_'.$i.'" value="'.$m.'" floatOnly>
                                                    <div class="error current_per'.$i.'"></div>
                                                </td>
                                            </tr>';
                                            $i++;
                                     }
                                    endforeach;
                                endif;
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</form>

<script>
$(document).ready(function(){
    $(document).on('click','.loadData',function(e){
        e.stopImmediatePropagation();e.preventDefault();
		$(".error").html("");
		var valid = 1;
        var dept_id = $("#emp_id :selected").data('dept_id');
        var desi_id = $("#emp_id :selected").data('desi_id'); 
        
        if($("#month").val() == ""){$(".month").html("Month is required.");valid=0;}      
        if($("#emp_id").val() == ""){$(".emp_id").html("Employee is required.");valid=0;}

		if(valid){
            $.ajax({
                url: base_url + controller + '/getEmpPerfomanceData',
                data: {dept_id:dept_id,desi_id:desi_id},
				type: "POST",
				dataType:'json',
				success:function(data){ 
                    $("#tbodyData").html('');
					$("#tbodyData").html(data.tbodyData);
                }
            });
        }
    }); 
}); 
</script> 