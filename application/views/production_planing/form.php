<form data-res_function = 'getPlanningResponse'>
    <div class="col-md-12">
        <div class="row">
            <div class="col-md-4 form-group">
                <label for="plan_date">Plan Date</label>
                <input type="date" class="form-control" name="plan_date" id="plan_date" value="<?=date("Y-m-d")?>">
            </div>
            <div class="col-md-4 form-group">
                <label for="plan_number">Plan Number</label>
                <input type="text" class="form-control" name="plan_number" id="plan_number" value="<?=$plan_number?>" readonly>
            </div>
        </div>
        <div class="row">
            <div class="table-responsive">
                <div class="error general_error"></div>
                <table class="table jpExcelTable">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>So No</th>
                            <th>So Date</th>
                            <th>Item</th>
                            <th>Order Qty</th>
                            <th>Dispatch Qty</th>
                            <th>Pending Dispatch</th>
                            <th>Plan Qty</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if(!empty($soData)){
                            $i=1;
                            foreach($soData As $row){
                                $pending_qty = $row->qty - $row->dispatch_qty;
                                ?>
                                <tr>
                                    <td><?=$i++?></td>
                                    <td><?=$row->trans_number?></td>
                                    <td><?=formatDate($row->trans_date)?></td>
                                    <td><?=$row->item_name?></td>
                                    <td><?=$row->qty?></td>
                                    <td><?=$row->dispatch_qty?></td>
                                    <td><?=$pending_qty?></td>
                                    <td>
                                        <input type="hidden" name="so_trans_id[]" value="<?=$row->id?>">
                                        <input type="hidden" name="so_id[]" value="<?=$row->trans_main_id?>">
                                        <input type="hidden" name="item_id[]" value="<?=$row->item_id?>">
                                        <input type="text" class="form-control numericOnly" name="plan_qty[]">
                                    </td>
                                </tr>
                                <?php
                            }
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</form>
