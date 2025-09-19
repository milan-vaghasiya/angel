<?php
class Lead extends MY_Controller
{
    private $indexPage = "lead/index";
    private $leadForm = "lead/lead_form";

	public function __construct(){
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "Lead";
		$this->data['headData']->controller = "lead";
		$this->data['headData']->pageUrl = "lead";
	}

    public function crmDesk(){
		$this->data['headData']->pageUrl = "lead/crmDesk";
        $this->data['rec_per_page'] = 25; // Records Per Page
        $this->data['sourceList'] = $this->selectOption->getSelectOptionList(['type'=>1]);
        $this->data['stageList'] = $this->leadStages->getLeadStagesList();
        $this->data['companyData'] = $this->leads->getCompanyInfo();
        $this->load->view('lead/crm_desk',$this->data);
    }

	public function getLeadData($party_type="",$fnCall = "Ajax"){
        $postData = $this->input->post();
        $leadStages = $this->leadStages->getLeadStagesList(['not_in'=>'1,2,3']);
		if(empty($postData)){$fnCall = 'Outside';$postData['party_type']=$party_type;}
        $next_page = 0;
		$leadData = Array();
		if(!empty($postData['party_type']))
		{
			if(isset($postData['page']) AND isset($postData['start']) AND isset($postData['length']))
			{
				$leadData = $this->leads->getLeadList($postData);
				$next_page = intval($postData['page']) + 1;
				
			}
			else{ $leadData = $this->leads->getLeadList($postData); }
		}
		else
		{
			$postData['crmDesk'] = 1;$postData['status'] = 1;$postData['group_by'] = 'sales_logs.party_id';
			if(isset($postData['page']) AND isset($postData['start']) AND isset($postData['length']))
			{
				$leadData = $this->leads->getReminders($postData);
				$next_page = intval($postData['page']) + 1;
			}
			else{ $leadData = $this->leads->getReminders($postData); }
		}
		
		$leadList['allLead'] = '';$leadList['pendingLead'] = '';$leadList['wonLead'] = '';$leadList['lostLead'] = '';$leadDetail ='';
		if(!empty($leadData))
		{
			foreach($leadData as $row)
			{
				$lostBtn='';$editButton='';;$reOpenBtn="";$inActiveBtn='';
				$userImg = base_url('assets/images/users/user_default.png');
                
                if($row->party_type != 3){
                    $lostParam = "{'postData':{'id':".$row->id.",'executive_id':".$row->sales_executive.",'party_type':3,'log_type':7},'message':'Are you sure want to Change Status to Lost?','fnsave':'changeLeadStatus','modal_id':'modal-md','form_id':'leadLost','fnedit':'leadLost'}";
                    $lostBtn = '<a href="javascript:void(0)" class="dropdown-item btn-edit btn-danger permission-modify" style="justify-content: flex-start;" onclick="leadEdit('.$lostParam.');" data-msg="Lost Status" flow="down"><i class="mdi mdi-close-circle"></i> Lost Lead</a>';

                    $editParam = "{'postData':{'id' : ".$row->id."}, 'modal_id' : 'bs-right-lg-modal', 'form_id' : 'editLead', 'call_function' : 'edit', 'title' : 'Update Lead', 'controller' : 'parties', 'js_store_fn' : 'saveLead'}"; 
                    $editButton = '<a class="dropdown-item btn-success btn-edit permission-modify" href="javascript:void(0)" flow="down" onclick="modalAction('.$editParam.');"><i class="mdi mdi-square-edit-outline"></i> Edit</a>';
                }elseif($row->party_type == 3){
                    $reOpenParam = "{'postData':{'id':".$row->id.",'executive_id':".$row->sales_executive.",'party_type':2,'log_type':11},'message':'Are you sure want to Reopen Lead?','fnsave':'changeLeadStatus','modal_id':'modal-md','formId':'reopenLead','title':'Reopen Lead','fnedit':'leadLost'}";
                    $reOpenBtn = '<a href="javascript:void(0)" class="dropdown-item btn1 btn-danger permission-modify" style="justify-content: flex-start;" onclick="leadEdit('.$reOpenParam.');" data-msg="Reopen" flow="down"><i class="mdi mdi-close-circle"></i> Reopen</a>';

                }elseif($row->party_type == 1 && $row->is_active == 1){
                    $inActiveParam = "{'postData':{'id':".$row->id.",'executive_id':".$row->sales_executive.",'party_type':1,'log_type':12,'is_active':2},'message':'Are you sure want to Inactive Party?','fnsave':'changeLeadStatus','modal_id':'modal-md','formId':'Inactive','title':'Inactive Party','fnedit':'leadLost'}";
                    $inActiveBtn = '<a href="javascript:void(0)" class="dropdown-item btn1 btn-danger permission-modify" style="justify-content: flex-start;" onclick="leadEdit('.$inActiveParam.');" data-msg="Inactive" flow="down"><i class="mdi mdi-close-circle"></i> Inactive</a>';

                }elseif($row->party_type == 1 && $row->is_active == 2){
                    $inActiveParam = "{'postData':{'id':".$row->id.",'executive_id':".$row->sales_executive.",'party_type':1,'log_type':13,'is_active':1},'message':'Are you sure want to Active Party?','fnsave':'changeLeadStatus','modal_id':'modal-md','formId':'Inactive','title':'Active Party','fnedit':'leadLost'}";
                    $inActiveBtn = '<a href="javascript:void(0)" class="dropdown-item btn1 btn-danger permission-modify" style="justify-content: flex-start;" onclick="leadEdit('.$inActiveParam.');" data-msg="Active" flow="down"><i class="mdi mdi-close-circle"></i> Active</a>';
                }
                $stageBtn='';
                foreach($leadStages as $stg){
                    if($stg->id != $row->party_type){
                        $stageParam = "{'postData':{'id':".$row->id.",'executive_id':".$row->sales_executive.",'party_type':'".$stg->id."','log_type':".$stg->log_type.",'is_active':".$row->is_active.",'notes':'".$stg->stage_type."'},'message':'Are you sure want to Change Status to ".$stg->stage_type."?','fnsave':'changeLeadStatus','modal_id':'modal-md','form_id':'','fnedit':'leadLost','title':'".$stg->stage_type."' ,'confirm' :'1'}";
                        $stageBtn .= '<a href="javascript:void(0)" class="dropdown-item btn-edit btn-danger permission-modify" style="justify-content: flex-start;" onclick="leadEdit('.$stageParam.');" data-msg="Lost Status" flow="down"><i class="fas fa-dot-circle"></i> '.$stg->stage_type.'</a>';
                    }
                }

				$deleteParam = "{'postData':{'id' : ".$row->id.", 'paty_type': 'Lead'}, 'message' : 'Lead', 'controller' : 'parties', 'fndelete' : 'delete'}";
				$deleteButton = '<a class="dropdown-item btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trashLead('.$deleteParam.');" flow="down"><i class="mdi mdi-trash-can-outline"></i> Remove</a>';

				$filterCls = $row->party_type.'_lead';$cls="";
                $rmdClass=!empty($row->reminder_date)?'pending_response':'';
                if(empty($row->sales_executive)){$cls = "text-danger";}
                $maxChars = 30;
                $wa_text = urlencode('Hello');
				$wa_number = (!empty($row->whatsapp_no) ? str_replace('-','',str_replace('+','',$row->whatsapp_no)) : '');
				$partyName = ((strlen($row->party_name) > $maxChars) ? substr($row->party_name, 0, $maxChars).'...' : $row->party_name);
				$contactPerson = "";
				if(!empty($row->contact_person))
				{
				    $contactPerson = ((strlen($row->contact_person) > $maxChars) ? substr($row->contact_person, 0, $maxChars).'...' : $row->contact_person);
				}
				
				$viewLeadParam = "{'postData':{'id':".$row->id."},'modal_id':'modal-md','form_id':'viewLead','fnedit':'viewLeadDetails','button':'close','title':'Lead Details'}";
                $viewLeadBtn = '<a href="javascript:void(0)" class="stage-btn view-btn m-0" onclick="leadEdit('.$viewLeadParam.');" data-msg="View Lead Details" flow="down"><i class="fas fa-eye fs-13"></i> <span class="lable">View</span></a>';	
                
				$leadDetail .= '<div class="grid_item '.$filterCls.' '.$rmdClass.'" style="width:24%;">
									<div class="card stage-item transition" data-category="transition">
										<div class="stage-title">
											<span>'.$row->source.'</span>
                                            <div class="dropdown d-inline-block float-end">
												<div class="time float-start">'.formatDate($row->created_at,"d M Y H:i:s").'</div>
												<a class="dropdown-toggle item-stage-icon" data-bs-toggle="dropdown" href="#" role="button" aria-expanded="false">
													<i class="las la-ellipsis-v"></i>
												</a>
												<div class="dropdown-menu dropdown-menu-end" aria-labelledby="drop2" style="">
													'.$inActiveBtn.$reOpenBtn.$lostBtn.$stageBtn.$editButton.$deleteButton.'
												</div>
											</div>
										</div>
										<div class="stage-body">
											<a href="javascript:void(0)" class="mt-0 font-13 partyData fw-bold '.$cls.'" data-party_id="'.$row->id.'">'.$partyName.'</a>
											<p class="text-muted mb-0 font-13"><i class="fas fa-user font-12"></i> '.$contactPerson.'</p>
										</div>
										<div class="stage-footer">
											'.(!empty($wa_number)?'<a role="button" href="https://wa.me/'.$wa_number.'/?text='.$wa_text.'" target="_blank" class="stage-btn wp-btn m-0" ><i class="fab fa-whatsapp fs-13"></i> <span class="lable">Share</span></a>':'').' '.$viewLeadBtn.'
										</div>
									</div>
								</div>';
			}
		}
		if($fnCall == 'Ajax'){$this->printJson(['leadDetail'=>$leadDetail,'next_page'=>$next_page]);}
		else{return $leadDetail;}
    }

	public function leadLost(){
        $data = $this->input->post();
        $this->data['data'] = $data;
        if($data['party_type'] == 3){
            $this->data['reasonList'] = $this->selectOption->getSelectOptionList(['type'=>2]);
        }
        $this->load->view('lead/lead_lost',$this->data);
    }
  
    public function changeLeadStatus(){
        $data = $this->input->post();
        if (empty($data['id'])){ $errorMessage['id'] = "Lead is required.";}
        if ($data['party_type'] == 3 && empty($data['notes'])){ $errorMessage['notes'] = "Reason is required.";}
        if ($data['party_type'] == 2 && empty($data['notes']) ){ $errorMessage['notes'] = "Note is required.";}

        if (!empty($errorMessage)) :
            $this->printJson(['status' => 0, 'message' => $errorMessage]);
        else :
			$leadData=[
				'id'=>$data['id'], 
				'party_type'=>$data['party_type'] 
			];
            $result = $this->leads->changeLeadStatus($data);
            $result['leadList'] = $this->getLeadData($data,'Return');

            $this->printJson($result);
        endif;
    }

	public function viewLeadDetails(){
        $data = $this->input->post();
        $this->data['partyData'] = $this->party->getParty(['id'=>$data['id']]);
        $this->load->view('lead/view_lead_detail',$this->data);        
    }

	public function getLeadDetails(){
        $data = $this->input->post();     
        $partyData = $this->party->getParty(['id'=>$data['party_id']]);
        $salesLog = $this->getSalesLog($data);
		$imgFile = '';
        if(!empty($partyData->party_image)){
		    $imageArray = explode(",",$partyData->party_image);
		    $i=1;
		    foreach($imageArray as $row){
		        $imgPath = base_url('assets/uploads/party/'.$row);
	            if($i == 1){ $imgFile .= '<a href="'.$imgPath.'" class="lightbox" > <img src="'.$imgPath.'" alt="" class="img-fluid thumb-sm" /> </a> '; }
	            else{ $imgFile .='<div class="picture-item" style="display:none"> <a href="'.$imgPath.'" class="lightbox" > <img src="'.$imgPath.'" alt="" class="img-fluid thumb-sm"  />  </a>  </div> '; }
                $i++;
		    }
		}
        $this->printJson(['partyData'=>$partyData, 'salesLog'=>$salesLog, 'imgFile'=>$imgFile]);
    }

	public function saveSalesLog(){
        $postData = $this->input->post();
		$errorMessage = array();

        if (!empty($postData['log_type']) && $postData['log_type'] == 3) {
            if (empty($postData['ref_date'])) {
                $errorMessage['ref_date'] = "Date is required.";
			}
            if (empty($postData['reminder_time'])) {
                $errorMessage['reminder_time'] = "Time is required.";
			}
            if (empty($postData['notes'])) {
                $errorMessage['notes'] = "Notes is required.";
			}
        }
		if (empty($postData['remark']) && !empty($postData['id'])) {
			$errorMessage['remark'] = "Response is required.";
		}
        
        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
			$result = $this->leads->saveSalesLogs($postData);
			$result['salesLog'] = $this->getSalesLog(['party_id'=>$postData['party_id']]);
			$this->printJson($result);
		endif;
    }

	public function getSalesLog($param = [],$fnCall = "Ajax"){
        $postData = $this->input->post();
		if(!empty($param)){$fnCall = 'Outside';$postData = $param;} 
        $slData = $this->leads->getSalesLog($postData);
        
		$salesLog = '';
		if(!empty($slData))
		{
			foreach($slData as $row)
			{
				$msgSide = ($row->created_by == $this->loginId) ? 'reverse' : '';
				$userImg = base_url('assets/images/users/user_default.png');
                $link = '';
                if($row->log_type == 3){
                    $link = '<p class="text-muted fs-11"><strong>'.$row->mode.' : </strong> '.date("d M Y H:i A",strtotime($row->ref_date." ".$row->reminder_time)).'</p>';
                }
                elseif($row->log_type == 4){
                    $link = '<a href="'.base_url('lead/printEnquiry/'.$row->ref_id).'" class="fw-bold text-primary" target="_blank">'.$row->ref_no.'</a>';
                }
                elseif($row->log_type == 5){
                    $link = '<a href="'.base_url('lead/printQuotation/'.$row->ref_id).'" class="fw-bold text-primary" target="_blank">'.$row->ref_no.'</a>';
                }
                elseif($row->log_type == 6){
                    $link = '<a href="'.base_url('lead/printOrder/'.$row->ref_id).'" class="fw-bold text-primary" target="_blank">'.$row->ref_no.'</a>';
                }
                elseif($row->log_type == 7){
                    $link = "Lost Lead";
                }
                elseif($row->log_type == 9){
                    $link = '<a href="'.base_url('lead/printEnquiry/'.$row->ref_id).'" class="fw-bold text-primary" target="_blank">'.$row->ref_no.'</a>';
                }
                if($row->log_type == 26){
                    $link = $row->remark;
                }
				$orderBtn='';$quoteBtn="";$editEnq="";$editQuot="";$editOrd="";$deleteOrd="";$responseBtn="";
				$btn="";
				if(in_array($row->log_type,[4,9])){
				    $btn .= '<a href="javascript:void(0)" class="dropdown-item btn btn-danger permission-modify addCrmForm"  data-button="both" data-modal_id="right_modal_lg" data-function="addSalesData" data-fnsave="saveSalesData" data-form_title="Add Quotation [ '.$row->party_name.' ]" datatip="Add Quotation" data-module_type="2" data-entry_type="'.$row->log_type.'" data-ref_id="'.$row->ref_id.'" data-party_id="'.$row->party_id.'" flow="down"><i class="mdi mdi-close-circle"></i> Create Quotation</a>';
				}
				
				if(in_array($row->log_type,[4,5])){
    				$btn .= '<a href="javascript:void(0)" data-module_type="3" class="dropdown-item btn btn-danger permission-modify addCrmForm"  data-button="both" data-modal_id="right_modal_lg" data-function="addSalesData" data-fnsave="saveSalesData" data-form_title="Add Order" datatip="Add Order" data-ref_id="'.$row->ref_id.'" data-entry_type="'.$row->log_type.'" data-party_id="'.$row->party_id.'" flow="down"><i class="mdi mdi-close-circle"></i> Create Order</a>';
				}

                if($row->log_type == 3 && ($row->remark == null)){
                    $responseParam = "{'postData':{'id' : ".$row->id.",'party_id' : ".$row->party_id."},'modal_id' : 'modal-md', 'form_id' : 'response', 'title' : 'Reminder Response', 'call_function' : 'reminderResponse', 'fnsave' : 'saveSalesLog', 'js_store_fn' : 'saveSalesLog'}";
                    $btn .= '<a class="dropdown-item btn text-dark permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="modalAction('.$responseParam.');"><i class="mdi mdi-square-edit-outline fs-12"></i> Response</a>';
                }
				$reminderRes="";
                if($row->log_type == 3 && !empty($row->remark)){
                    $reminderRes = '<p class="text-muted font-11">Res : '.$row->remark.'</p>';
                }
				$dropDown = "";
				if(!in_array($row->log_type,[1,2,7,8,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26]) && !empty($btn)){
				    $dropDown='<a class="text-dark lead-action" data-toggle="dropdown" href="#" role="button"><i class="fas fa-ellipsis-v"></i></a>
								<div class="dropdown-menu">'.$btn.'</div>';
				}
				$salesLog.= '<div class="activity-info">
								<div class="icon-info-activity"><i class="'.$this->iconClass[$row->log_type].'"></i></div>
								<div class="activity-info-text">
									<div class="d-flex justify-content-between align-items-center">
										<h6 class="m-0 fs-13">'.$this->logTitle[$row->log_type].'</h6>
                                       
										<span class="text-muted w-30 d-block font-12">
										'.date("d F",strtotime($row->created_at)).$dropDown.'</span>
									</div>
									<p class=" m-1 font-12"><i class="fa fa-user"></i> '.$row->creator.'</p>
									<p class="text-muted m-1 font-12">'.$row->notes.$link.'</p>
                                    '.$reminderRes.'
								</div>
							</div>';
			}
		}
		if($fnCall == 'Ajax'){$this->printJson(['salesLog'=>$salesLog]);}
		else{return $salesLog;}
    }

    public function reminderResponse(){
        $data = $this->input->post();
        $this->data['party_id'] = $data['party_id'];
        $this->data['id'] = $data['id'];
        $this->load->view('lead/reminder_response',$this->data);
    }
}
?>