<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

/* get Pagewise Table Header */
function getStoreDtHeader($page){
    /* Location Master header */
    $data['storeLocation'][] = ["name"=>"Action","class"=>"text-center no_filter noExport","sortable"=>FALSE];
	$data['storeLocation'][] = ["name"=>"#","class"=>"text-center no_filter","sortable"=>FALSE];
    $data['storeLocation'][] = ["name"=>"Store Name"];
    $data['storeLocation'][] = ["name"=>"Location"];
    $data['storeLocation'][] = ["name"=>"Remark"];

    /* Gate Inward Pending/Compeleted Tab Header */
    $data['gateInward'][] = ["name"=>"Action","class"=>"text-center no_filter noExport","sortable"=>FALSE];
	$data['gateInward'][] = ["name"=>"#","class"=>"text-center no_filter","sortable"=>FALSE];
    $data['gateInward'][] = ["name"=> "GRN No.", "textAlign" => "center"];
    $data['gateInward'][] = ["name" => "GRN Date", "textAlign" => "center"];
    $data['gateInward'][] = ["name" => "Party Name"];
    $data['gateInward'][] = ["name" => "GRN Type"];
    $data['gateInward'][] = ["name" => "Item Name"];
	$data['gateInward'][] = ["name" => "Finish Goods"];
    $data['gateInward'][] = ["name" => "Location"];
    $data['gateInward'][] = ["name" => "Batch No."];
    $data['gateInward'][] = ["name" => "Ref./Heat No."];
    $data['gateInward'][] = ["name" => "Price"];
    $data['gateInward'][] = ["name" => "GRN Qty"];
    $data['gateInward'][] = ["name" => "UOM"];
    $data['gateInward'][] = ["name" => "Ok Qty"];
    $data['gateInward'][] = ["name" => "Reject Qty"];
    $data['gateInward'][] = ["name" => "Short Qty"];
    $data['gateInward'][] = ["name" => "PO. NO."];  
    $data['gateInward'][] = ["name" => "Remark"];
    
    /* Inward QC Pending/Compeleted Tab Header */
    $data['inwardQC'][] = ["name"=>"Action","class"=>"text-center no_filter noExport","sortable"=>FALSE];
	$data['inwardQC'][] = ["name"=>"#","class"=>"text-center no_filter","sortable"=>FALSE];
    $data['inwardQC'][] = ["name"=> "GRN No.", "textAlign" => "center"];
    $data['inwardQC'][] = ["name" => "GRN Date", "textAlign" => "center"];
    $data['inwardQC'][] = ["name" => "Party Name"];
    $data['inwardQC'][] = ["name" => "GRN Type"];
    $data['inwardQC'][] = ["name" => "Item Name"];
	$data['inwardQC'][] = ["name" => "Finish Goods"];
    $data['inwardQC'][] = ["name" => "Location"];
    $data['inwardQC'][] = ["name" => "Batch No."];
    $data['inwardQC'][] = ["name" => "Ref./Heat No."];
    $data['inwardQC'][] = ["name" => "Price"];
    $data['inwardQC'][] = ["name" => "Receive Qty"];
    $data['inwardQC'][] = ["name" => "UOM"];
    $data['inwardQC'][] = ["name" => "Ok Qty"];
    $data['inwardQC'][] = ["name" => "Reject Qty"];
    $data['inwardQC'][] = ["name" => "Short Qty"];
    $data['inwardQC'][] = ["name" => "PO. NO."]; 
    $data['inwardQC'][] = ["name" => "Remark"];
	
	/* Pending QC Header */
    $data['pendingQc'][] = ["name"=>"Action","class"=>"text-center no_filter noExport","sortable"=>FALSE];
	$data['pendingQc'][] = ["name"=>"#","class"=>"text-center no_filter","sortable"=>FALSE];
    $data['pendingQc'][] = ["name"=>"GRN No."];
    $data['pendingQc'][] = ["name"=>"Name Of Agency."];
    $data['pendingQc'][] = ["name"=>"Test Type"];
    $data['pendingQc'][] = ["name"=>"Item Name"];
    $data['pendingQc'][] = ["name"=>"Sample Qty"];
    $data['pendingQc'][] = ["name"=>"Batch No."];
    $data['pendingQc'][] = ["name"=>"Ref./Heat No."];
    $data['pendingQc'][] = ["name" =>"Remark"];

    /* FG Stock Inward Table Header */
    $data['stockTrans'][] = ["name"=>"Action","class"=>"text-center no_filter noExport","sortable"=>FALSE];
	$data['stockTrans'][] = ["name"=>"#","class"=>"text-center no_filter","sortable"=>FALSE];
    $data['stockTrans'][] = ["name" => "Date"];
    $data['stockTrans'][] = ["name" => "Item Name"];
    $data['stockTrans'][] = ["name" => "Qty"];
    $data['stockTrans'][] = ["name" => "Remark"];

    /* LIST OF STOCK VERIFICATION  */
    $data['stockVerification'][] = ["name"=>"#","textAlign"=>"center","srnoPosition"=>0];
    $data['stockVerification'][] = ["name"=>"Item Code."];
    $data['stockVerification'][] = ["name"=>"Item Name"];
    $data['stockVerification'][] = ["name"=>"Stock Register Qty."];
    $data['stockVerification'][] = ["name"=>"Action","textAlign"=>"center"];

    /* Return Requisition Table Header */
    $data['returnRequisition'][] = ["name"=>"Action","class"=>"text-center no_filter noExport","sortable"=>FALSE];
	$data['returnRequisition'][] = ["name"=>"#","class"=>"text-center no_filter","sortable"=>FALSE];
    $data['returnRequisition'][] = ["name" => "Issue Number"];
    $data['returnRequisition'][] = ["name" => "Issue Date"];
    $data['returnRequisition'][] = ["name" => "Item Name"];
    $data['returnRequisition'][] = ["name" => "Issue Qty"];
    $data['returnRequisition'][] = ["name" => "Return Qty"];
    $data['returnRequisition'][] = ["name" => "Pending Qty"];

    /* Issued Requisition Table Header */
    $data['issueRequisition'][] = ["name"=>"Action","class"=>"text-center no_filter noExport","sortable"=>FALSE];
	$data['issueRequisition'][] = ["name"=>"#","class"=>"text-center no_filter","sortable"=>FALSE];
    $data['issueRequisition'][] = ["name" => "Issued Number"];
    $data['issueRequisition'][] = ["name" => "Issue Date"];
    $data['issueRequisition'][] = ["name" => "PRC No"];
    $data['issueRequisition'][] = ["name" => "Item Name"];
    $data['issueRequisition'][] = ["name" => "Issue Qty"];
    $data['issueRequisition'][] = ["name" => "Batch No"];

    /* Inspection Table Header */
    $data['inspection'][] = ["name"=>"Action","class"=>"text-center no_filter noExport","sortable"=>FALSE];
	$data['inspection'][] = ["name"=>"#","class"=>"text-center no_filter","sortable"=>FALSE];
    $data['inspection'][] = ["name" => "Issued Number"];
    $data['inspection'][] = ["name" => "Trans Date"];
    $data['inspection'][] = ["name" => "Total Qty"];
    $data['inspection'][] = ["name" => "Batch No"];
    $data['inspection'][] = ["name" => "Remark"];

    /* PRC Material Issue Table Header */
    $data['prcMaterial'][] = ["name"=>"Action","class"=>"text-center no_filter noExport","sortable"=>FALSE];
	$data['prcMaterial'][] = ["name"=>"#","class"=>"text-center no_filter","sortable"=>FALSE];
    $data['prcMaterial'][] = ["name" => "PRC No."];
    $data['prcMaterial'][] = ["name" => "PRC Date"];
    $data['prcMaterial'][] = ["name" => "Finish Good"];
    $data['prcMaterial'][] = ["name" => "Required Qty"];
    $data['prcMaterial'][] = ["name" => "Issue Qty"];
	
    /* Die Material Request header */
    $data['dieMaterialReq'][] = ["name"=>"Action","class"=>"text-center no_filter noExport","sortable"=>FALSE];
	$data['dieMaterialReq'][] = ["name"=>"#","class"=>"text-center no_filter","sortable"=>FALSE];
    $data['dieMaterialReq'][] = ["name"=>"Job No."];
    $data['dieMaterialReq'][] = ["name"=>"Die"];
    $data['dieMaterialReq'][] = ["name"=>"Product"];
    $data['dieMaterialReq'][] = ["name"=>"Required Material"];
    $data['dieMaterialReq'][] = ["name"=>"Rquired Qty"];
    $data['dieMaterialReq'][] = ["name"=>"Issue Qty"];

    /* Manual Rejection header */
    $data['manualRejection'][] = ["name"=>"Action","class"=>"text-center no_filter noExport","sortable"=>FALSE];
	$data['manualRejection'][] = ["name"=>"#","class"=>"text-center no_filter","sortable"=>FALSE];
    $data['manualRejection'][] = ["name"=>"Rej. Date"];
    $data['manualRejection'][] = ["name"=>"Product"];
    $data['manualRejection'][] = ["name"=>"Qty"];
    $data['manualRejection'][] = ["name"=>"Location"];
    $data['manualRejection'][] = ["name"=>"Batch No."];
    $data['manualRejection'][] = ["name"=>"Remark"];
	
	/* Opening Stock Table Header */
    $data['openingStock'][] = ["name"=>"Action","class"=>"text-center no_filter noExport","sortable"=>FALSE];
    $data['openingStock'][] = ["name"=>"#","class"=>"text-center no_filter","sortable"=>FALSE];
    $data['openingStock'][] = ["name" => "Date"];
	$data['openingStock'][] = ["name" => "Item Name"];
    $data['openingStock'][] = ["name" => "Location"];
	$data['openingStock'][] = ["name" => "Batch No."];
    $data['openingStock'][] = ["name" => "Qty"];

    /** End Piece Data */
    $data['endPiece'][] = ["name"=>"Action","class"=>"text-center no_filter noExport","sortable"=>FALSE];
    $data['endPiece'][] = ["name"=>"#","class"=>"text-center no_filter","sortable"=>FALSE];
    $data['endPiece'][] = ["name"=>"Date"];
    $data['endPiece'][] = ["name"=>"PRC No"];
    $data['endPiece'][] = ["name"=>"Item Name"];
    $data['endPiece'][] = ["name"=>"End Piece"];
    $data['endPiece'][] = ["name"=>"Qty"];
    $data['endPiece'][] = ["name"=>"Review Qty"];
    $data['endPiece'][] = ["name"=>"Batch No."];
    $data['endPiece'][] = ["name"=>"Remark"];

    /* Out Challan Header */
    $data['outChallan'][] = ["name"=>"Action","class"=>"text-center no_filter noExport","sortable"=>FALSE];
    $data['outChallan'][] = ["name"=>"#","class"=>"text-center no_filter","sortable"=>FALSE];
    $data['outChallan'][] = ["name"=>"Challan No."];
    $data['outChallan'][] = ["name"=>"Challan Date"];
    $data['outChallan'][] = ["name"=>"Party Name"];
    $data['outChallan'][] = ["name"=>"Item Name"];
    $data['outChallan'][] = ["name"=>"Qty"];
    $data['outChallan'][] = ["name"=>"Remark"];
	
	/* Internal GRN Header */
    $data['internalGrn'][] = ["name"=>"Action","class"=>"text-center no_filter noExport","sortable"=>FALSE];
    $data['internalGrn'][] = ["name"=>"#","class"=>"text-center no_filter","sortable"=>FALSE];
    $data['internalGrn'][] = ["name"=>"GRN No.", "textAlign" => "center"];
    $data['internalGrn'][] = ["name"=>"GRN Date", "textAlign" => "center"];
    $data['internalGrn'][] = ["name"=>"Party Name"];
    $data['internalGrn'][] = ["name"=>"Item Name"];
    $data['internalGrn'][] = ["name"=>"Finish Goods"];
    $data['internalGrn'][] = ["name"=>"Use Batch"];
    $data['internalGrn'][] = ["name"=>"Batch No."];
    $data['internalGrn'][] = ["name"=>"Qty"];
    $data['internalGrn'][] = ["name"=>"UOM"];
    $data['internalGrn'][] = ["name"=>"Price"];
    $data['internalGrn'][] = ["name"=>"Remark"]; 
	
	/* Assets Stock Header */
    $data['assets_master'][] = ["name"=>"Action","class"=>"text-center no_filter noExport","sortable"=>FALSE];
    $data['assets_master'][] = ["name"=>"#","class"=>"text-center no_filter","sortable"=>FALSE];
    $data['assets_master'][] = ["name"=>"Code", "textAlign" => "center"];
    $data['assets_master'][] = ["name"=>"Item Name", "textAlign" => "center"];
    $data['assets_master'][] = ["name"=>"Price"];
    $data['assets_master'][] = ["name"=>"Location"];
    $data['assets_master'][] = ["name"=>"HSN Code"];
    $data['assets_master'][] = ["name"=>"Inward Date"];
    $data['assets_master'][] = ["name"=>"Supplier Name"];
	
	/* In Stock Assets Header */
	$masterAssetSelect = '<input type="checkbox" id="masterAssetSelect" class="filled-in chk-col-success BulkAssetChallan" value=""><label for="masterAssetSelect">ALL</label>';
	$data['assetsMasterChk'][] = ["name"=>"Action","class"=>"text-center no_filter noExport","sortable"=>FALSE];
    $data['assetsMasterChk'][] = ["name"=>"#","class"=>"text-center no_filter","sortable"=>FALSE];
	$data['assetsMasterChk'][] = ["name"=>$masterAssetSelect,"class"=>"no_filter","textAlign"=>"center","orderable"=>"false"];
    $data['assetsMasterChk'][] = ["name"=>"Code", "textAlign" => "center"];
    $data['assetsMasterChk'][] = ["name"=>"Item Name", "textAlign" => "center"];
    $data['assetsMasterChk'][] = ["name"=>"Price"];
    $data['assetsMasterChk'][] = ["name"=>"Location"];
    $data['assetsMasterChk'][] = ["name"=>"HSN Code"];
    $data['assetsMasterChk'][] = ["name"=>"Inward Date"];
    $data['assetsMasterChk'][] = ["name"=>"Supplier Name"];
	
	/* Assets Challan Header */
	$data['assetsChallan'][] = ["name"=>"Action","class"=>"text-center no_filter noExport","sortable"=>FALSE];
    $data['assetsChallan'][] = ["name"=>"#","class"=>"text-center no_filter","sortable"=>FALSE]; 
    $data['assetsChallan'][] = ["name"=>"Ch. No."];
    $data['assetsChallan'][] = ["name"=>"Ch. Date"];
    $data['assetsChallan'][] = ["name"=>"Ch. Type"];
    $data['assetsChallan'][] = ["name"=>"Issue To"];
    $data['assetsChallan'][] = ["name"=>"Code"];
    $data['assetsChallan'][] = ["name"=>"Description"];
    $data['assetsChallan'][] = ["name"=>"Remark"];

    return tableHeader($data[$page]);
}

/* Store Location Data */
function getStoreLocationData($data){
    $deleteParam = "{'postData':{'id' : ".$data->id."},'message' : 'Store Location'}";
    $editParam = "{'postData':{'id' : ".$data->id."},'modal_id' : 'bs-right-md-modal', 'form_id' : 'editStoreLocation', 'title' : 'Update Store Location','call_function':'edit'}";

    $editButton = ''; $deleteButton = '';$qrCode='';
    if(!empty($data->ref_id) && empty($data->store_type)):
        $editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="modalAction('.$editParam.');"><i class="mdi mdi-square-edit-outline"></i></a>';

        $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="mdi mdi-trash-can-outline"></i></a>';
    endif;

    if($data->final_location == 0):
        $editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="modalAction('.$editParam.');"><i class="mdi mdi-square-edit-outline"></i></a>';

        $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="mdi mdi-trash-can-outline"></i></a>';
        
        $locationName = '<a href="' . base_url("storeLocation/list/" . $data->id) . '">' . $data->location . '</a>';
    else:
        $locationName = $data->location;
        $qrCode = '<a href="'.base_url("storeLocation/locationQr/" . $data->id).'" class="btn btn-primary permission-write" target="_blank" datatip="Generate QR" flow="down"><i class="mdi mdi-qrcode"></i></a>';
    endif;
	
	$action = getActionButton($qrCode.$editButton.$deleteButton);
    return [$action,$data->sr_no,$data->store_name,$locationName,$data->remark];
}

/* Gate Inward Data */
function getGateInwardData($data){
    $action = '';$editButton='';$deleteButton='';$inspection = '';
	
	if($data->trans_status == 1):
		if($data->type != "Customer Return"):
			$editParam = "{'postData':{'id' : ".$data->id."},'modal_id' : 'bs-right-xl-modal', 'form_id' : 'editGateInward', 'title' : 'Update Gate Inward','call_function':'edit'}";
			$editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="modalAction('.$editParam.');"><i class="mdi mdi-square-edit-outline"></i></a>';
		endif;
		
		$deleteParam = "{'postData':{'id' : ".$data->id."},'message' : 'Gate Inward'}";
		$deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="mdi mdi-trash-can-outline"></i></a>';
         
        if(!empty($data->qc_by)):
            $insParam = "{'postData':{'id' : ".$data->mir_trans_id."},'modal_id' : 'bs-right-md-modal', 'form_id' : 'materialInspection', 'title' : 'Material Inspection','call_function':'materialInspection','fnsave':'saveInspectedMaterial'}";
            $inspection = '<a href="javascript:void(0);" type="button" class="btn btn-warning " datatip="Inspection" flow="down" onclick="modalAction('.$insParam.');"><i class="fas fa-search"></i></a>';
        endif;
	endif;
	
	$iirTagPrint = '<a href="'.base_url('gateInward/ir_print/'.$data->mir_trans_id).'" type="button" class="btn btn-primary" datatip="IIR Tag" flow="down" target="_blank"><i class="fas fa-print"></i></a>';

	$grnPrint = '<a class="btn btn-success btn-info" href="'.base_url('gateInward/printGRN/'.$data->id).'" target="_blank" datatip="GRN Print" flow="down"><i class="fas fa-print" ></i></a>';

	$action = getActionButton($iirTagPrint.$grnPrint.$inspection.$editButton.$deleteButton);

	$data->item_name = (!empty($data->item_code)) ? '['.$data->item_code.'] '.$data->item_name : $data->item_name;
    $data->fg_item_name = (!empty($data->fg_item_code)) ? '['.$data->fg_item_code.'] '.$data->fg_item_name : $data->fg_item_name;

	return [$action,$data->sr_no,$data->trans_number,formatDate($data->trans_date),$data->party_name,$data->type,$data->item_name,$data->fg_item_name,$data->location,$data->batch_no,$data->heat_no,floatval($data->price),floatval($data->qty),$data->uom,floatval($data->ok_qty),floatval($data->reject_qty),floatval($data->short_qty),$data->po_number,$data->item_remark];
}

/* Inward Qc Data  */
function getInwardQcData($data){
    $iirPrint = $iirInsp = $tcButton = $approveBtn = "";

    $iirParam = "{'postData':{'id' : ".$data->mir_trans_id."},'modal_id' : 'bs-right-xl-modal', 'form_id' : 'iirInsp', 'title' : 'Incoming Inspection','call_function':'getInwardQc','fnsave':'saveInwardQc'}";
    $iirInsp = '<a href="javascript:void(0)" type="button" class="btn btn-info permission-modify" datatip="Incoming Inspection" flow="down" onclick="modalAction('.$iirParam.');"><i class="fa fa-file-alt"></i></a>';

    $iirPrint = '<a class="btn btn-dribbble btn-edit permission-modify" href="'.base_url($data->controller.'/inInspection_pdf/'.$data->mir_trans_id).'" target="_blank" datatip="Inspection Print" flow="down"><i class="fas fa-print" ></i></a>'; 

    $testReport = "{'postData':{'id' : '".$data->mir_trans_id."','grn_id' : '".$data->id."'}, 'button' : 'close', 'modal_id' : 'bs-right-xl-modal', 'form_id' : 'testReport', 'title' : 'Test Report', 'call_function' : 'getTestReport'}";
    $tcButton = '<a class="btn btn-dark btn-salary " href="javascript:void(0)" datatip="Test Report" flow="down" onclick="modalAction('.$testReport.');"><i class="mdi mdi-file-multiple"></i></a>';

    $iirTagPrint = '<a href="'.base_url('gateInward/ir_print/'.$data->mir_trans_id).'" type="button" class="btn btn-primary" datatip="IIR Tag" flow="down" target="_blank"><i class="fas fa-print"></i></a>';
    $grnPrint = '<a class="btn btn-success btn-info" href="'.base_url('gateInward/printGRN/'.$data->id).'" target="_blank" datatip="GRN Print" flow="down"><i class="fas fa-print" ></i></a>';

    if($data->trans_status == 1){
        $approveParam = "{'postData':{'id' : ".$data->mir_trans_id.", 'trans_status' : 3,'is_approve':1,'msg':'Approved'},'fnsave':'approveInwardQc','message':'Are you sure want to Approve this Inward Qc?'}";
        $approveBtn = '<a class="btn btn-info permission-modify" href="javascript:void(0)" datatip="Approve Qc" flow="down" onclick="confirmStore('.$approveParam.');"><i class="mdi mdi-check"></i></a>'; 
    } 
	
    $action = getActionButton($approveBtn.$grnPrint.$iirTagPrint.$tcButton.$iirInsp.$iirPrint);

	$data->item_name = (!empty($data->item_code)) ? '['.$data->item_code.'] '.$data->item_name : $data->item_name;
    $data->fg_item_name = (!empty($data->fg_item_code)) ? '['.$data->fg_item_code.'] '.$data->fg_item_name : $data->fg_item_name;

    return [$action,$data->sr_no,$data->trans_number,formatDate($data->trans_date),$data->party_name,$data->type,$data->item_name,$data->fg_item_name,$data->location,$data->batch_no,$data->heat_no,floatval($data->price),floatval($data->qty),$data->uom,floatval($data->ok_qty),floatval($data->reject_qty),floatval($data->short_qty),$data->po_number,$data->item_remark];
}

/* Pending QC Table Data */
function getPendingQcData($data){
    $receiveParam = "{'postData':{'id' : ".$data->id.",'grn_trans_id' : ".$data->grn_trans_id."}, 'modal_id' : 'bs-right-xl-modal', 'form_id' : 'addReceiveTest', 'title' : 'Receive Test Report', 'call_function':'receiveTestReport', 'fnsave':'saveReceiveTestReport'}";
    $receiveBtn = '<a href="javascript:void(0)" type="button" class="btn btn-info permission-modify" datatip="Receive Test Report" flow="down" onclick="modalAction('.$receiveParam.');"><i class="fa fa-plus"></i></a>';       

    $printBtn = '<a href="'.base_url('gateInward/printReceiveTcReport/'.$data->id).'" type="button" class="btn btn-primary" datatip="Challan Print" flow="down" target="_blank"><i class="fas fa-print"></i></a>';
				
    $action = getActionButton($receiveBtn.$printBtn);
	
    $test_name = (!empty($data->test_type) ? $data->test_name: "");
	$data->item_name = (!empty($data->item_code)) ? '['.$data->item_code.'] '.$data->item_name : $data->item_name;
    
	return [$action,$data->sr_no,$data->trans_number,$data->name_of_agency,$test_name,$data->item_name,floatval($data->sample_qty),$data->batch_no,$data->heat_no,$data->item_remark];
}

/* FG Stock Inward Table Data */
function getStockTransData($data){
    $deleteParam = "{'postData':{'id' : ".$data->id."},'message' : 'Stock'}";
    $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="mdi mdi-trash-can-outline"></i></a>';

    $action = getActionButton($deleteButton);

    return [$action,$data->sr_no,formatDate($data->ref_date),$data->item_name,$data->qty,$data->remark];
}

/* Stock Verification Table Data */
function getStockVerificationData($data){
 
    $editParam = "{'postData':{'id' : ".$data->id.",'item_id': ".$data->item_id."},'modal_id' : 'bs-right-xl-modal', 'form_id' : 'editStock', 'title' : 'Update Stock','call_function':'editStock','fnsave':'save'}";
    $editButton = '<a href="javascript:void(0)" type="button" class="btn btn-sm btn-success permission-modify" datatip="Edit" flow="down" onclick="modalAction('.$editParam.');"><i class="mdi mdi-square-edit-outline"></i></a>';

    return [$data->sr_no,$data->item_code,$data->item_name,floatVal($data->stock_qty),$editButton];
}

/* Return Requisition Table Data */
function getReturnRequisitionData($data){
    $returnButton = '';
    $issue_qty = floatval($data->issue_qty);
    $return_qty = floatval($data->return_qty);
    if($issue_qty > $return_qty){
        $returnParam = "{'postData':{'id' : ".$data->id."},'modal_id' : 'modal-md', 'form_id' : 'closeRequisition', 'fnedit' : 'return', 'call_function' : 'return', 'title' : 'Return Material', 'fnsave' : 'saveReturnReq'}";
        $returnButton = '<a class="btn btn-warning btn-edit permission-modify" href="javascript:void(0)" datatip="Return" flow="down" onclick="modalAction('.$returnParam.');"><i class="fa fa-reply" ></i></a>';
    }

    $action = getActionButton($returnButton);
	return [$action,$data->sr_no,$data->issue_number,formatDate($data->issue_date),(($data->item_code) ? "[".$data->item_code."] " : "").$data->item_name,floatval($data->issue_qty),floatval($data->return_qty),floatval($data->pending_qty)];
}

/* Issue Requisition Table Data */
function getIssueRequisitionData($data){
    $deleteButton = "";
    $return_qty = floatval($data->return_qty);
    if(empty($return_qty)){
        $deleteParam = "{'postData':{'id' : ".$data->id."}, 'fndelete' : 'deleteIssueRequisition','message' : 'Stock'}";
        $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="mdi mdi-trash-can-outline"></i></a>';
    }
    $action = getActionButton($deleteButton);
	
    return [$action,$data->sr_no,$data->issue_number,formatDate($data->issue_date),$data->prc_number,(($data->item_code) ? "[".$data->item_code."] " : "").$data->item_name,abs($data->issue_qty),$data->batch_no];
}

/* Inspection Table Data */
function getInspectionData($data) {
    $inspectButton = "";
    if($data->trans_type == 1){
        $inspectParam = "{'postData':{'id' : ".$data->id.",'issue_id' : ".$data->issue_id."},'modal_id' : 'modal-md', 'fnedit' : 'addInspection', 'call_function':'addInspection','form_id' : 'addInspection', 'title' : 'Inspection', 'fnsave' : 'saveInspection'}";
        $inspectButton = '<a class="btn btn-warning btn-edit permission-modify" href="javascript:void(0)" datatip="Inspect" flow="down" onclick="modalAction('.$inspectParam.');"><i class="fa fa-search" ></i></a>';
    }
    $action = getActionButton($inspectButton);
    return [$action,$data->sr_no,$data->issue_number,formatDate($data->trans_date),$data->total_qty,$data->batch_no,$data->remark];
}

/* Prc Material Issue Table Data */
function getPrcMaterialData($data){
    $required_qty = round($data->ppc_qty * $data->prc_qty,3);
    $isuueParam = "{'postData':{'prc_id' : ".$data->prc_id.", 'item_id' : '".$data->item_id."', 'required_qty' : '".$required_qty."','issue_type':'2'}, 'modal_id' : 'bs-right-xl-modal', 'form_id' : 'addIssueRequisition', 'title' : 'Material Issue', 'call_function':'addIssueRequisition', 'fnsave':'saveIssueRequisition','js_store_fn':'storeIssueMaterial'}";
    $issueBtn = '<a href="javascript:void(0)" type="button" class="btn btn-info permission-modify" datatip="Material Issue" flow="down" onclick="modalAction('.$isuueParam.');"><i class="fa fa-plus"></i></a>';  
    $action = getActionButton($issueBtn);
    
    return [$action,$data->sr_no,$data->prc_number,formatDate($data->prc_date),$data->item_name,$required_qty,(!empty($data->issue_qty)?$data->issue_qty:0)];
}

/* Die Material Request Table Data */
function getDieMaterialRequest($data){
 
    $isuueParam = "{'postData':{'die_id' : ".$data->die_id.", 'item_id' : '".$data->item_id."', 'required_qty' : '".$data->bom_qty."','issue_type':'3'}, 'modal_id' : 'bs-right-xl-modal', 'form_id' : 'addIssueRequisition', 'title' : 'Material Issue', 'call_function':'addIssueRequisition', 'fnsave':'saveIssueRequisition'}";
    $issueBtn = '<a href="javascript:void(0)" type="button" class="btn btn-info permission-modify" datatip="Material Issue" flow="down" onclick="modalAction('.$isuueParam.');"><i class="fa fa-plus"></i></a>';       

    $action = getActionButton($issueBtn);
    return [$action,$data->sr_no,$data->trans_number,$data->category_name,(($data->fg_item_code) ? "[".$data->fg_item_code."] " : "").$data->fg_item_name,(($data->item_code) ? "[".$data->item_code."] " : "").$data->item_name,floatval($data->bom_qty),floatval($data->issue_qty)];
}

/* ManualRejection  Table Data */
function getManualRejectionData($data){
    $deleteParam = "{'postData':{'id' : ".$data->id."}, 'fndelete' : 'deleteRejection','message' : 'Rejection'}";
    $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="mdi mdi-trash-can-outline"></i></a>';   

    $action = getActionButton($deleteButton);
    return [$action,$data->sr_no,formatDate($data->trans_date),(($data->item_code) ? "[".$data->item_code."] " : "").$data->item_name,floatval($data->qty),$data->location,$data->batch_no,$data->remark];
}

/* Opening Stock Table Data */
function getStockInwardData($data){
    $deleteParam = "{'postData':{'id' : ".$data->id."},'message' : 'Stock'}";
    $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="mdi mdi-trash-can-outline"></i></a>';

    $action = getActionButton($deleteButton);
	return [$action,$data->sr_no,formatDate($data->trans_date),$data->item_name,$data->location,$data->batch_no,$data->qty];
}

/* End Piece Table Data */
function getEndPieceData($data){
    $stockParam = "{'postData':{'id' : ".$data->id."},'title' : 'Add to stock', 'modal_id' : 'bs-right-lg-modal', 'form_id' : 'addStock', 'call_function':'addStock', 'fnsave':'saveStock','button':'close'}";
    $stockButton = '<a class="btn btn-success permission-modify" href="javascript:void(0)" onclick="modalAction('.$stockParam.');" datatip="Add to stock" flow="down"><i class="fas fa-database"></i></a>';

    $action = getActionButton($stockButton);

    return [$action,$data->sr_no,formatDate($data->trans_date),$data->prc_number,$data->item_code.' '.$data->item_name,$data->end_pcs,$data->qty,$data->review_qty,$data->batch_no,$data->remark];
}

/* OutChallan Table Data */
function getOutChallanData($data){
    $editButton = $deleteButton = '';
    if(($data->qty - $data->receive_qty) > 0):
        $editButton = '<a class="btn btn-success permission-modify" href="'.base_url('outChallan/edit/'.$data->id).'" datatip="Edit" flow="down" ><i class="mdi mdi-square-edit-outline"></i></a>';

        $deleteParam = "{'postData':{'id' : ".$data->id."},'message' : 'Out Challan'}";
        $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="mdi mdi-trash-can-outline"></i></a>';  
    endif;      
        
    $receiveParam = "{'postData':{'id' : ".$data->id."}, 'modal_id' : 'modal-lg', 'form_id' : 'receiveItem', 'title' : 'Receive Item', 'call_function':'receiveItem', 'fnsave':'saveReceiveItem', 'button' : 'close'}";
    $receiveBtn = '<a href="javascript:void(0)" type="button" class="btn btn-primary permission-modify" datatip="Receive Item" flow="down" onclick="modalAction('.$receiveParam.');"><i class="mdi mdi-reply"></i></a>';    

    $printBtn = '<a class="btn btn-dribbble" href="'.base_url('outChallan/printChallan/'.$data->id).'" target="_blank" datatip="Print" flow="down"><i class="fas fa-print"></i></a>';

    if(!empty($data->item_remark)){$data->item_name .= '<br><small>(Item Remark : '.$data->item_remark.')</small>';} 

    $action = getActionButton($printBtn.$receiveBtn.$editButton.$deleteButton);
    return [$action,$data->sr_no,$data->trans_number,formatDate($data->trans_date),$data->party_name,$data->item_name,$data->qty,$data->remark];
}

/* Internal Grn Data Data  */
function getInternalGrnData($data){
    $action = '';$editButton='';$deleteButton="";$inspection = '';

	$deleteParam = "{'postData':{'id' : ".$data->id.",'mir_trans_id' : ".$data->mir_trans_id."},'message' : 'Gate Inward','controller':'internalGrn'}";	
	if($data->trans_status == 1):
		$deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="mdi mdi-trash-can-outline"></i></a>';
               
        if(empty($data->is_inspection)):
            $insParam = "{'postData':{'id' : ".$data->mir_trans_id."},'modal_id' : 'bs-right-md-modal', 'form_id' : 'materialInspection', 'title' : 'Material Inspection','call_function':'materialInspection','fnsave':'saveInspectedMaterial'}";
            $inspection = '<a href="javscript:voide(0);" type="button" class="btn btn-warning " datatip="Inspection" flow="down" onclick="modalAction('.$insParam.');"><i class="fas fa-search"></i></a>';
        endif;
	endif;
	
	$iirTagPrint = '<a href="'.base_url('gateInward/ir_print/'.$data->mir_trans_id).'" type="button" class="btn btn-primary" datatip="IIR Tag" flow="down" target="_blank"><i class="fas fa-print"></i></a>';

	$grnPrint = '<a class="btn btn-success btn-info" href="'.base_url('gateInward/printGRN/'.$data->id).'" target="_blank" datatip="GRN Print" flow="down"><i class="fas fa-print" ></i></a>';

	$action = getActionButton($iirTagPrint.$grnPrint.$inspection.$editButton.$deleteButton);

	$data->item_name = (!empty($data->item_code)) ? '['.$data->item_code.'] '.$data->item_name : $data->item_name;
    $data->fg_item_name = (!empty($data->fg_item_code)) ? '['.$data->fg_item_code.'] '.$data->fg_item_name : $data->fg_item_name;
	return [$action,$data->sr_no,$data->trans_number,formatDate($data->trans_date),$data->party_name,$data->item_name.(!empty($data->material_grade) ? ' '.$data->material_grade : ''),$data->fg_item_name,$data->stock_batch,$data->batch_no,floatval($data->qty),$data->uom,floatval($data->price),$data->item_remark];
}

/*Assets Stock Data*/
function getAssetsData($data = array()){
	$deleteParam = "{'postData':{'id' : ".$data->id."},'message' : 'Instrument'}";
    $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="mdi mdi-trash-can-outline"></i></a>';

    $inward=''; $reject=''; $editButton = '';
    if(empty($data->status)){
        $inwardParam = "{'postData':{'id' : ".$data->id.",'status' : '1'}, 'modal_id' : 'bs-right-lg-modal', 'form_id' : 'inwardAsset', 'title' : 'Inward Asset', 'call_function':'inwardAsset', 'fnsave' : 'save'}";
        $inward = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Inward Asset" flow="down" onclick="modalAction('.$inwardParam.');"><i class="mdi mdi-reply" ></i></a>';
    }elseif($data->status == 1){
        $reject = '<a href="javascript:void(0)" class="btn btn-dark rejectAssets permission-modify" data-id="'.$data->id.'" data-assets_code="'.$data->item_code.'" datatip="Reject" flow="down"><i class="fa fa-close" ></i></a>';
    
        $editParam = "{'postData':{'id' : ".$data->id."},'modal_id' : 'bs-right-lg-modal', 'form_id' : 'editGauge', 'title' : 'Update Gauge', 'fnsave' : 'save', 'call_function' : 'edit'}";
        $editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="modalAction('.$editParam.');"><i class="mdi mdi-square-edit-outline"></i></a>';
    }
    
	$selectBox = '<input type="checkbox" name="ref_id[]" id="ref_id_'.$data->id.'" class="filled-in chk-col-success BulkAssetChallan" value="'.$data->id.'"><label for="ref_id_'.$data->id.'"></label>';
    $action = getActionButton($reject.$inward.$editButton);
	
	if(in_array($data->status,[1])){		
		return [$action,$data->sr_no,$selectBox,$data->item_code,$data->item_name,$data->price,$data->location,$data->hsn_code,formatDate($data->inward_date),$data->party_name];
	}else{
		return [$action,$data->sr_no,$data->item_code,$data->item_name,$data->price,$data->location,$data->hsn_code,formatDate($data->inward_date),$data->party_name];
	}
}

/*Assets Challan Data*/
function getChallanData($data = array()){
	$receiveBtn=''; $editBtn=''; $deleteBtn='';
    
    if(empty($data->trans_status)){
        if(empty($data->receive_by)){
			$receiveParam = "{'postData':{'id' : ".$data->id."}, 'modal_id' : 'bs-right-md-modal', 'button':'both', 'form_id' : 'returnChallan', 'title' : 'Receive Challan', 'call_function' : 'returnChallan', 'fnsave' : 'saveReceiveChallan'}";
			$receiveBtn = '<a href="javascript:void(0)" class="btn btn-info permission-write" onclick="modalAction('.$receiveParam.');" datatip="Receive" flow="down"><i class="mdi mdi-reply"></i></a>';
			
            $editBtn = '<a href="'.base_url($data->controller.'/editChallan/'.$data->challan_id).'" class="btn btn-success btn-edit permission-modify" datatip="Edit" flow="down"><i class="fa fa-edit"></i></a>';

            $deleteParam = "{'postData':{'id' : ".$data->challan_id."},'message' : 'Challan','fndelete':'deleteChallan'}";
            $deleteBtn = '<a href="javascript:void(0)" class="btn btn-danger btn-delete permission-remove" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="mdi mdi-trash-can-outline"></i></a>';
        }
    }

    $data->challan_type = (($data->challan_type==1)? 'In-House Issue' : (($data->challan_type==2) ? 'Vendor Issue':'Employee Issue'));
        
    $printBtn = '<a class="btn btn-info btn-edit" href="'.base_url('qcChallan/printChallan/'.$data->challan_id).'" target="_blank" datatip="Print" flow="down"><i class="fas fa-print" ></i></a>';
    
    $action = getActionButton($receiveBtn.$editBtn.$deleteBtn);
    return [$action,$data->sr_no,$data->trans_number,formatDate($data->trans_date),$data->challan_type,$data->issue_to,$data->item_code,$data->item_name,$data->item_remark];
}
?>