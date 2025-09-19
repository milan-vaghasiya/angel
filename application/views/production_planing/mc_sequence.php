<style>
	#itemProcess th,#itemProcess td{font-size:12px!important;}
</style>
<form>
	<div class="col-md-12">
		<div class="row">
			<div class="col-md-12">
				<input type="hidden" id="machine_id" value="<?=$machine_id?>">
				<div class="table-responsive" data-simplebar style="height:78vh;">
                    <table class="table jpExcelTable" id="itemProcess" >
                        <thead class="thead-info">
                            <tr>
                                <th>Sequence</th>
                                <th>Item</th>
                                <th>PRC</th>
                                <th>Process</th>
                            </tr>
                        </thead>
                        <tbody id="processData">
                        <?php
                        if(!empty($planningData)){
                            foreach($planningData AS $row){
                                echo  '<tr id="'.$row->id.'">
                                            <td>'.$row->sequence.'</td>
                                            <td>'.$row->item_name.'</td>
                                            <td>'.$row->prc_number.'</td>
                                            <td>'.$row->process_name.'</td>
                                        </tr>';
                            }
                        }
                        ?>
                        </tbody>
                    </table>
                </div>
			</div>
		</div>
	</div>
</form>
<script>
    $(document).ready(function() {

        $("#itemProcess tbody").sortable({
            items: 'tr',
            cursor: 'pointer',
            axis: 'y',
            dropOnEmpty: false,
            helper: fixWidthHelper,
            start: function(e, ui) {ui.item.addClass("selected");},
            stop: function(e, ui) {
                ui.item.removeClass("selected");
                $(this).find("tr").each(function(index) {$(this).find("td").eq(0).html(index + 1);});
            },
            update: function() {
                var ids = '';
                var machine_id=$("#machine_id").val();
                $(this).find("tr").each(function(index) {ids += $(this).attr("id") + ",";});
                var lastChar = ids.slice(-1);
                if (lastChar == ',') {ids = ids.slice(0, -1);}

                $.ajax({
                    url: base_url + 'productionPlanning/saveMachineSequence',
                    type: 'post',
                    data: {id: ids,machine_id:machine_id},
                    dataType: 'json',
                    global: false,
                    success: function(data) {}
                });
            }
        });

    });


    function fixWidthHelper(e, ui) {
        ui.children().each(function() {
            $(this).width($(this).width());
        });
        return ui;
    }
</script>