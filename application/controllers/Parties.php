<?php
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
class Parties extends MY_Controller{
    private $index = "party/index";
    private $form = "party/form";
    private $ledgerForm = "party/ledger_form";
    private $gstFrom = "party/gst_form";
    private $opbal_index = "party/opbal_index";
	private $party_setting_form = "party/party_setting_form";
	private $party_contact = "party/party_contact";
	private $lead_lost = "party/lead_lost";
    private $bulk_executive = "party/bulk_executive";

    public function __construct(){
        parent::__construct();
		$this->data['headData']->pageTitle = "Party Master";
		$this->data['headData']->controller = "parties";        
    }

    public function list($type="customer"){
        $this->data['headData']->pageUrl = "parties/list/".$type;
        $this->data['type'] = $type;
        $this->data['party_category'] = $party_category = array_search(ucwords($type),$this->partyCategory);
		$this->data['headData']->pageTitle = $this->partyCategory[$party_category];
        $this->data['tableHeader'] = getMasterDtHeader($type);
        $this->load->view($this->index,$this->data);
    }

    public function getDTRows($party_category,$party_type = 1){
        $data=$this->input->post();
		$data['party_category'] = $party_category;
		$data['party_type'] = $party_type;
        $result = $this->party->getDTRows($data);
        $sendData = array();
        $i = ($data['start']+1);
        foreach ($result['data'] as $row) :
            $row->sr_no = $i++;
            $row->table_status = $party_category;
            $row->party_category_name = $this->partyCategory[$row->party_category];
            $sendData[] = getPartyData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function addParty(){
        $data = $this->input->post();
        $this->data['party_category'] = $data['party_category'];
        $this->data['party_type'] = (!empty($data['party_type']) ? $data['party_type'] : '1');
        $this->data['ledgerList'] = $this->party->getPartyList(['tax_type'=>"'TDS'"]);
        $this->data['tdsClassList'] = $this->party->getTDSClassList();

        if($data['party_category'] != 4):            
            $this->data['currencyData'] = $this->party->getCurrencyList();
            $this->data['countryData'] = $this->party->getCountries();
            $this->data['salesExecutives'] = $this->employee->getEmployeeList();
            $this->data['groupList'] = $this->group->getGroupList(['group_code'=>(($data['party_category'] == 1)?"'SD'":"'SC'")]);
            $this->data['priceStructureList'] = $this->itemPriceStructure->getPriceStructureList();
            $this->data['sourceList'] = $this->selectOption->getSelectOptionList(['type'=>1]);
            $this->data['businessTypeList'] = $this->selectOption->getSelectOptionList(['type'=>7]);
            $this->data['salesZoneList'] = $this->selectOption->getSelectOptionList(['type'=>4]);
			
			$code = $this->party->getPartyCode(['category'=>$data['party_category'],'party_type'=>$this->data['party_type']]);
            if($this->data['party_category'] == 1 && $this->data['party_type'] == 1):
                $this->data['party_code'] = 'AI-C'.sprintf("%05d",$code);
            elseif($this->data['party_category'] == 2):
                $this->data['party_code'] = 'AI-S'.sprintf("%05d",$code);
            elseif($this->data['party_category'] == 3):
                $this->data['party_code'] = 'AI-V'.sprintf("%05d",$code);  
            elseif($this->data['party_category'] == 1 && $this->data['party_type'] == 2):
                $this->data['party_code'] = 'AI-L'.sprintf("%05d",$code);
            endif;
			
            $this->load->view($this->form, $this->data);
        else:
            $this->data['groupList'] = $this->group->getGroupList(['not_group_code'=>"'SD','SC'"]);
            $this->data['hsnList'] = $this->hsnModel->getHSNList();
            $this->load->view($this->ledgerForm,$this->data);
        endif;
    }

	/* UPDATED BY : AVT DATE : 13-12-2024 */
	public function save(){
        $data = $this->input->post();
        $errorMessage = array();
        if(isset($data['form_type'])):
            if(empty($data['group_id'])):
                $errorMessage['group_id'] = "Group Name is required.";
            endif;
        else:
            if (empty($data['party_name']))
                $errorMessage['party_name'] = "Company name is required.";

            if (empty($data['party_category']))
                $errorMessage['party_category'] = "Party Category is required.";
			
			if ($data['party_type'] == 1 && empty($data['party_code']))
                $errorMessage['party_code'] = "Party code is required.";

            if($data['party_category'] != 4):
        
                if (empty($data['gstin']) && in_array($data['registration_type'],[1,2]))
                    $errorMessage['gstin'] = 'Gstin is required.';

                if (empty($data['country_id']))
                    $errorMessage['country_id'] = 'Country is required.';

                if (empty($data['state_id']))
                    $errorMessage['state_id'] = 'State is required.';

                if (empty($data['city_name']))
                    $errorMessage['city_name'] = 'City is required.';

                if (empty($data['party_address']))
                    $errorMessage['party_address'] = "Address is required.";

                if (empty($data['party_pincode']))
                    $errorMessage['party_pincode'] = "Pincode is required.";
                    
            endif;
        endif;

        if (!empty($errorMessage)) :
            $this->printJson(['status' => 0, 'message' => $errorMessage]);
        else :
            if(!isset($data['form_type'])):
                $data['party_name'] = ucwords($data['party_name']);
                $data['gstin'] = (!empty($data['gstin']))?strtoupper($data['gstin']):"";
                $data['gst_reg_date'] = (!empty($data['gst_reg_date']))?$data['gst_reg_date']:NULL;
                $data['price_structure_id'] = (!empty($data['price_structure_id']))?$data['price_structure_id']:0;
            endif;

            $this->printJson($this->party->save($data));
        endif;
    }
	
    public function edit(){
        $data = $this->input->post();
        $result = $this->party->getParty($data);
        $this->data['dataRow'] = $result;

        $this->data['ledgerList'] = $this->party->getPartyList(['tax_type'=>"'TDS'"]);
        $this->data['tdsClassList'] = $this->party->getTDSClassList();

        if($result->party_category != 4):
            $this->data['currencyData'] = $this->party->getCurrencyList();
            $this->data['countryData'] = $this->party->getCountries();
            $this->data['salesExecutives'] = $this->employee->getEmployeeList();
            $this->data['groupList'] = $this->group->getGroupList(['group_code'=>(($result->party_category == 1)?"'SD'":"'SC'")]);      
            $this->data['priceStructureList'] = $this->itemPriceStructure->getPriceStructureList();
            $this->data['sourceList'] = $this->selectOption->getSelectOptionList(['type'=>1]); 
            $this->data['businessTypeList'] = $this->selectOption->getSelectOptionList(['type'=>7]);
            $this->data['salesZoneList'] = $this->selectOption->getSelectOptionList(['type'=>4]);
            $this->load->view($this->form, $this->data);
        else:
            $this->data['groupList'] = $this->group->getGroupList(['not_group_code'=>"'SD','SC'"]);
            $this->data['hsnList'] = $this->hsnModel->getHSNList();
            $this->load->view($this->ledgerForm,$this->data);
        endif;
    }

    public function delete(){
        $id = $this->input->post('id');
        if (empty($id)) :
            $this->printJson(['status' => 0, 'message' => 'Somthing went wrong...Please try again.']);
        else :
            $this->printJson($this->party->delete($id));
        endif;
    }

    public function gstDetail(){
        $data = $this->input->post();
        $this->data['party_id'] = $data['id'];
        $this->load->view($this->gstFrom,$this->data);
    }

    public function getPartyGSTDetailHtml(){
        $data = $this->input->post();
        $result = $this->party->getPartyGSTDetail($data);

        $tbodyData = "";$i = 1;        
        if (!empty($result)) :
            foreach ($result as $row) :
                $deleteParam = "{'postData':{'id' : ".$row->id.",'party_id':".$row->party_id."},'message' : 'GST Detail','fndelete':'deleteGstDetail','res_function':'resTrashPartyGstDetail'}";
                $tbodyData .= '<tr>
                    <td>' .  $i++ . '</td>
                    <td>' . $row->gstin . '</td>
                    <td>' . $row->party_address . '</td>
                    <td>' . $row->party_pincode . '</td>
                    <td>' . $row->delivery_address . '</td>
                    <td>' . $row->delivery_pincode . '</td>
                    <td class="text-center">
                        <button type="button" onclick="trash('.$deleteParam.');" class="btn btn-outline-danger waves-effect waves-light btn-delete permission-remove"><i class="ti-trash"></i></button>
                    </td>
                </tr> ';
            endforeach;
        else :
            $tbodyData .= '<tr><td colspan="7" style="text-align:center;">No data available in table</td></tr>';
        endif;
        $this->printJson(['status'=>1,"tbodyData"=>$tbodyData]);
    }

    public function saveGstDetail(){
        $data = $this->input->post();
        $errorMessage = array();

        if (empty($data['gstin']))
            $errorMessage['gstin'] = "GST is required.";
		if (empty($data['party_address']))
            $errorMessage['party_address'] = "Party Address is required.";
        if (empty($data['party_pincode']))
            $errorMessage['party_pincode'] = "Party Pincode is required.";
        if (empty($data['delivery_address']))
            $errorMessage['delivery_address'] = "Delivery Address is required.";
        if (empty($data['delivery_pincode']))
            $errorMessage['delivery_pincode'] = "Delivery Pincode is required.";

        if (!empty($errorMessage)) :
            $this->printJson(['status' => 0, 'message' => $errorMessage]);
        else :
            $this->printJson($this->party->saveGstDetail($data));
        endif;
    }

    public function deleteGstDetail(){
        $id = $this->input->post('id');
        if (empty($id)) :
            $this->printJson(['status' => 0, 'message' => 'Somthing went wrong...Please try again.']);
        else :
            $this->printJson($this->party->deleteGstDetail($id));
        endif;
    }

    public function getPartyList(){
        $data = $this->input->post();
        $partyList = $this->party->getPartyList($data);
        $this->printJson(['status'=>1,'data'=>['partyList'=>$partyList]]);
    }

    /* Party Opening Balance Start */
    public function opBalIndex(){
        $this->data['grpData'] = $this->group->getGroupList();
        $this->load->view($this->opbal_index,$this->data);
    }

    public function getGroupWiseLedger(){
        $data = $this->input->post();
        $ledgerData = $this->party->getPartyOpBalance(['group_id'=>$data['group_id']]);

        $tbody="";$i=1;
        if(!empty($ledgerData)):
            foreach($ledgerData as $row):         
                $row->opb = $row->op_balance;       
                $crSelected = (!empty($row->op_balance_type) && $row->op_balance_type == "1")?"selected":"";
                $drSelected = (!empty($row->op_balance_type) && $row->op_balance_type == "-1")?"selected":"";

                $row->opBalanceInput = '<div class="input-group">
                    <select name="balance_type[]" id="balance_type_'.$row->id.'" class="form-control" style="width: 20%;">
                        <option value="1" '.$crSelected.'>CR</option>
                        <option value="-1" '.$drSelected.'>DR</option>
                    </select>
                    <input type="text" id="op_balance_'.$row->id.'" name="op_balance[]" class="form-control floatOnly" value="'.floatVal(abs($row->opb)).'" style="width: 40%;" />
                </div>
                <input type = "hidden"  id="party_id_'.$row->id.'" name="party_id[]" value="'.$row->id.'" >' ;                

                $tbody .= '<tr>
                    <td style="width: 5%;">'.$i++.'</td>
                    <td style="width: 25%;">'.$row->account_name.'</td>
                    <td class="text-right" style="width: 10%;" id="cur_op_'.$row->id.'">'.$row->opb.'</td>
                    <td style="width: 20%;">' .$row->opBalanceInput. '</td>
                    <td style="width: 5%;">
                        <button type="button" class="btn btn-success saveOp" datatip="Save" flow="left" data-id="'.$row->id.'"><i class="fa fa-check"></i></button>
                    </td>
                </tr>';
            endforeach;
        endif;
        $this->printJson(['status'=>1, 'count'=>$i, 'tbody'=>$tbody]);
    }

    public function saveOpeningBalance(){
        $data = $this->input->post();
        $this->printJson($this->party->saveOpeningBalance($data));
    }

    /* Party Opening Balance End */
   
	/* ACCOUNT SETTTING CREATED BY : AVT DATE:13-12-2024 */
	public function editPartySettings(){
        $data = $this->input->post();
        $result = $this->party->getParty($data);
        $this->data['dataRow'] = $result;
        $this->data['groupList'] = $this->group->getGroupList(['group_code'=>((in_array($result->party_category,[1]))?"'SD'":"'SC'")]);
        $this->data['tdsClassList'] = $this->party->getTDSClassList();
		$this->data['ledgerList'] = $this->party->getPartyList(['tax_type'=>"'TDS'"]);
        $this->load->view($this->party_setting_form,$this->data);
    }

	/* CREATED BY : AVT DATE:13-12-2024 */
    public function updatePartyContact(){
        $data = $this->input->post();
        $this->data['party_id'] = $data['party_id'];
        $this->load->view($this->party_contact,$this->data);
    }

	/* CREATED BY : AVT DATE:13-12-2024 */
    public function savePartyContact(){
        $data = $this->input->post();
        $errorMessage = array();
        
		if(empty($data['contact_person'])){
            $errorMessage['contact_person'] = "Contact Person is required.";
        }
		if(empty($data['designation'])){
            $errorMessage['designation'] = "Designation is required.";
        }
		if(empty($data['party_mobile'])){
            $errorMessage['party_mobile'] = "Mobile No is required.";
        }   
        if(empty($data['party_email'])){
            $errorMessage['party_email'] = "Email is required.";
        } 
        
        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $this->printJson($this->party->savePartyContact($data));
        endif;
    }

	/* CREATED BY : AVT DATE:13-12-2024 */
    public function partyContactHtml(){
        $data = $this->input->post();
        $partyData = $this->party->getPartyContact(['party_id'=>$data['party_id']]);
        $tbodyData="";$i=1; 
        if(!empty($partyData)):
            $i=1;$deleteButton="";
            foreach($partyData as $row):

                if(empty($row->is_default)){
                    $deleteParam = "{'postData':{'id' : ".$row->id."},'res_function':'partyContactHtml','fndelete':'deletePartyContact'}";    
                    $deleteButton = '<button type="button" class="btn btn-outline-danger btn-sm waves-effect waves-light permission-remove" onclick="trash('.$deleteParam.');"><i class="mdi mdi-trash-can-outline"></i></button>';
                }
              
                $tbodyData.= '<tr>
                            <td>'.$i++.'</td>
                            <td>'.$row->contact_person.'</td>
                            <td>'.$row->designation.'</td>
                            <td>'.$row->party_mobile.'</td>
                            <td>'.$row->party_email.'</td>
                            <td class="text-center">'.$deleteButton.'</td>
                        </tr>';
            endforeach;
        else:
            $tbodyData.= '<tr><td colspan="6" style="text-align:center;">No Data Found</td></tr>';
        endif;

        $this->printJson(['status'=>1,'tbodyData'=>$tbodyData]);
    }

	/* CREATED BY : AVT DATE:13-12-2024 */
    public function deletePartyContact(){
        $data=$this->input->post();
        if(empty($data['id'])):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->party->deletePartyContact($data['id']));
        endif;
    }

	/* CREATED BY : AVT DATE:13-12-2024 */
    public function getCustomerList(){
		$data = $this->input->post();
		$result = $this->party->getPartyList($data);
		$this->printJson($result);
	}

	   /* Start Party Excel Upload */ 
	public function uploadPartyExcel(){
        $data = $this->input->post();
        $this->data['party_category'] = $data['party_category'];
        $this->data['party_type'] = $data['party_type'];
		$this->data['executiveList'] = $this->employee->getEmployeeList(); 
        $this->load->view('party/party_excel_form',$this->data);
    }

	public function createPartyMasterExcel(){
        $spreadsheet = new Spreadsheet();
        $reader = new \PhpOffice\PhpSpreadsheet\Reader\Html();
        $html = '<tr class="text-center">
          	<th>gstin</th>
			<th>party_name</th>
			<th>party_code</th>
			<th>source</th>
			<th>sales_zone</th>
			<th>business_type</th>
			<th>contact_person</th>
			<th>designation</th>
			<th>party_mobile</th>
			<th>whatsapp_no</th>
			<th>party_email</th>
			<th>credit_days</th>
            <th>business_capacity</th>
            <th>registration_type</th>
            <th>pan_no</th>
            <th>currency</th>
			<th>distance</th>
			<th>country</th>
			<th>state</th>
			<th>city_name</th>
			<th>party_address</th>
			<th>party_pincode</th>
			<th>delivery_address</th>';
                    
        $html .= '</tr>';

        $exlData = '<table>' . $html . '</table>';
        $spreadsheet = $reader->loadFromString($exlData);
        $excelSheet = $spreadsheet->getActiveSheet();
        $excelSheet = $excelSheet->setTitle('party');
        
        $hcol = $excelSheet->getHighestColumn();
        $hrow = $excelSheet->getHighestRow();
        $packFullRange = 'A1:' . $hcol . $hrow;
        foreach (range('A', $hcol) as $col) :
            $excelSheet->getColumnDimension($col)->setAutoSize(true);
        endforeach;
      

        $fileDirectory = realpath(APPPATH . '../assets/uploads/party_excel');
        $fileName = '/'.str_replace('_', ' ', 'party').'.xlsx';

        $writer = new Xlsx($spreadsheet);
        $writer->save($fileDirectory . $fileName);
        header("Content-Type: application/vnd.ms-excel");
        redirect(base_url('assets/uploads/party_excel') . $fileName);
    }
	
    public function importPartyExcel(){
        $postData = $this->input->post();
       
        $party_excel = '';
        if (isset($_FILES['party_excel']['name']) || !empty($_FILES['party_excel']['name'])) :
            $this->load->library('upload');
            $_FILES['userfile']['name']     = $_FILES['party_excel']['name'];
            $_FILES['userfile']['type']     = $_FILES['party_excel']['type'];
            $_FILES['userfile']['tmp_name'] = $_FILES['party_excel']['tmp_name'];
            $_FILES['userfile']['error']    = $_FILES['party_excel']['error'];
            $_FILES['userfile']['size']     = $_FILES['party_excel']['size'];

            $imagePath = realpath(APPPATH . '../assets/uploads/party_excel');
            $config = ['file_name' => $_FILES['userfile']['name'], 'allowed_types' => '*', 'max_size' => 10240, 'overwrite' => TRUE, 'upload_path' => $imagePath];

            $this->upload->initialize($config);
            if (!$this->upload->do_upload()) :
                $errorMessage['party_excel'] = $this->upload->display_errors();
                $this->printJson(["status" => 0, "message" => $errorMessage]);
            else :
                $uploadData = $this->upload->data();
                $party_excel = $uploadData['file_name'];
            endif;
            if (!empty($party_excel)) {
                
                $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($imagePath . '/' . $party_excel);
                $exl_sheet = $spreadsheet->getSheet(0);
                $fileData = (!empty($exl_sheet) ? array($exl_sheet->toArray(null, true, true, true)) : []);
            
                $fieldArray = array();$tbodyData = ""; $validData = Array();

                $sourceList = $this->selectOption->getSelectOptionList(['type'=>1]);
                $bTypeList = $this->selectOption->getSelectOptionList(['type'=>7]);
                $zoneList = $this->selectOption->getSelectOptionList(['type'=>4]);

                $salesZoneList = array_reduce($zoneList, function($salesZoneList, $zone) { $salesZoneList[strtoupper($zone->label)] = $zone->id; return $salesZoneList; }, []);

                if (!empty($fileData)) {

                    $fieldArray = $fileData[0][1];$okCount=0;$j=1;
                    for ($i = 2; $i <= count($fileData[0]); $i++) {
                        $rowData = array();
                        $c = 'A';
                        foreach ($fileData[0][$i] as $key => $colData) :
                                $field_val = strtolower($fieldArray[$c]);
                                $rowData[$field_val] = $colData;
                                $c++;
                        endforeach;

                        if(!empty($rowData)):
                            $addData = [];
                            $addCls1= "";$addCls2= "";$cnoCls = "";$bTypeCls ="";$sourceCls=""; $zoneCls = "";$stageCls = "";$codeCls ="";

                            $partyData = $this->party->getParty(['party_mobile'=>$rowData['party_mobile'],'party_name'=>$rowData['party_name'],'party_category'=>$postData['party_category']]);

                            if(!empty($partyData) || empty($rowData['party_name'])){
                                $cnoCls = "bg-danger text-white";
                            }

                            if(!empty($postData['party_category']) && $postData['party_type'] == 1){
                                   $partyCodeData = $this->party->getParty(['party_code'=>$rowData['party_code'],'party_category'=>$postData['party_category'],'party_type'=>$postData['party_type']]);
                                    if(!empty($partyCodeData) || empty($rowData['party_code'])){
                                        $codeCls = "bg-danger text-white";
                                    }
                            }

                            if(!empty($rowData['city_name']) AND !empty($rowData['country']) AND !empty($rowData['state']))
                            {
								$countryData = $this->party->getCountry(['name'=>$rowData['country']]);
								$stateData = $this->party->getState(['name'=>$rowData['state']]);

                                if(empty($countryData)){
									$addCls1 = "bg-danger text-white";
								}
								if( empty($stateData)){
									$addCls2 = "bg-danger text-white";
								}

                            }
                            
                            if (!empty($rowData['source']) && in_array(strtoupper($rowData['source']), array_map('strtoupper', array_column($sourceList, 'label')))) {
                                $rowData['source'] = strtoupper($rowData['source']); 
                            }else{
                                $sourceCls = "bg-danger text-white";
                            }

                            if (!empty($rowData['business_type']) && in_array(strtoupper($rowData['business_type']), array_map('strtoupper',array_column($bTypeList, 'label')))) {
                                $rowData['business_type'] = strtoupper($rowData['business_type']); 
                            }else{
                                $bTypeCls = "bg-danger text-white";
                            }
                         
                            if (!empty($rowData['sales_zone']) && !empty($salesZoneList[strtoupper($rowData['sales_zone'])])) {
                                $rowData['sales_zone'] = $rowData['sales_zone']; 
                            }
                            else{
                                $zoneCls = "bg-danger text-white";
                            }
                          
                            if(!empty($partyData) OR empty($countryData) OR empty($stateData) OR empty($rowData['party_name']) OR !empty($bTypeCls) OR !empty($sourceCls) OR !empty($zoneCls) ){
                                $tbodyData .= '<tr>
									<th>'.(!empty($rowData['gstin']) ? $rowData['gstin'] : "").'</th>
									<td class="'.$cnoCls.'">'.(!empty($rowData['party_name']) ? $rowData['party_name'] : "").'</td>
									<td class="'.$codeCls.'">'.(!empty($rowData['party_code']) ? $rowData['party_code'] : "").'</td>
									<td class="'.$sourceCls.'">'.(!empty($rowData['source']) ? $rowData['source'] : "").'</td>
									<td class="'.$zoneCls.'">'.(!empty($rowData['sales_zone']) ? $rowData['sales_zone'] : "").'</td>
									<td class="'.$bTypeCls.'">'.(!empty($rowData['business_type']) ? $rowData['business_type'] : "").'</td>
									<td>'.(!empty($rowData['contact_person']) ? $rowData['contact_person'] : "").'</td>
									<td>'.(!empty($rowData['designation']) ? $rowData['designation'] : "").'</td>
									<td class="'.$cnoCls.'">'.(!empty($rowData['party_mobile']) ? $rowData['party_mobile'] : "").'</td>
									<td>'.(!empty($rowData['whatsapp_no']) ? $rowData['whatsapp_no'] : "").'</td>
									<td>'.(!empty($rowData['party_email']) ? $rowData['party_email'] : "").'</td>
									<td>'.(!empty($rowData['credit_days']) ? $rowData['credit_days'] : "").'</td>
									<td>'.(!empty($rowData['business_capacity']) ? $rowData['business_capacity'] : "").'</td>
									<td>'.(!empty($rowData['registration_type']) ? $rowData['registration_type'] : "").'</td>
									<td>'.(!empty($rowData['pan_no']) ? $rowData['pan_no'] : "").'</td>
									<td>'.(!empty($rowData['currency']) ? $rowData['currency'] : "").'</td>
									<td>'.(!empty($rowData['distance']) ? $rowData['distance'] : "").'</td>
									<td class="'.$addCls1.'">'.(!empty($rowData['country']) ? $rowData['country'] : "").'</td>
									<td class="'.$addCls2.'">'.(!empty($rowData['state']) ? $rowData['state'] : "").'</td>
									<td >'.(!empty($rowData['city_name']) ? $rowData['city_name'] : "").'</td>
									<td>'.(!empty($rowData['party_address']) ? $rowData['party_address'] : "").'</td>
									<td>'.(!empty($rowData['party_pincode']) ? $rowData['party_pincode'] : "").'</td>
									<td>'.(!empty($rowData['delivery_address']) ? $rowData['delivery_address'] : "").'</td>
								</tr>';
                            }else{
                                $postData['id'] = '';
                                $postData['party_type'] = (!empty($postData['party_type']) ? $postData['party_type'] : 1);
                                $postData['party_category'] = (!empty($postData['party_category']) ? $postData['party_category'] : 0);
                                $postData['lead_stage'] = 1;
                                $postData['sales_executive'] = (!empty($postData['sales_executive']) ? $postData['sales_executive'] : 0);
                                $postData['source'] = (!empty($rowData['source']) ? $rowData['source'] : NULL);
                                $postData['sales_zone'] =(!empty($salesZoneList[strtoupper($rowData['sales_zone'])]) ? $salesZoneList[strtoupper($rowData['sales_zone'])] : NULL);
                                $postData['business_type'] = (!empty($rowData['business_type']) ? $rowData['business_type'] : NULL);
                                $postData['party_name'] = (!empty($rowData['party_name']) ? $rowData['party_name'] : NULL);
                                $postData['party_code'] = (!empty($rowData['party_code']) ? $rowData['party_code'] : NULL);
                                $postData['party_mobile'] = (!empty($rowData['party_mobile']) ? $rowData['party_mobile'] : NULL);
                                $postData['whatsapp_no'] = (!empty($rowData['whatsapp_no']) ? $rowData['whatsapp_no'] : NULL);
                                $postData['gstin']  = (!empty($rowData['gstin']) ? $rowData['gstin'] : NULL);
                                $postData['contact_person'] =(!empty($rowData['contact_person']) ? $rowData['contact_person'] : NULL);
                                $postData['designation'] = (!empty($rowData['designation']) ? $rowData['designation'] : NULL);
                                $postData['party_email'] = (!empty($rowData['party_email']) ? $rowData['party_email'] : NULL);
                                $postData['credit_days'] = (!empty($rowData['credit_days']) ? $rowData['credit_days'] : 0);
                                $postData['business_capacity'] =  (!empty($rowData['business_capacity']) ? $rowData['business_capacity'] : 0);
                                $postData['registration_type'] = (!empty($rowData['registration_type']) ? $rowData['registration_type'] : NULL);
                                $postData['pan_no'] = (!empty($rowData['pan_no']) ? $rowData['pan_no'] : NULL);
                                $postData['currency'] = (!empty($rowData['currency']) ? $rowData['currency'] : 'INR');
                                $postData['distance'] = (!empty($rowData['distance']) ? $rowData['distance'] : 0);
                                $postData['delivery_address'] = (!empty($rowData['delivery_address']) ? $rowData['delivery_address'] : NULL);
                                $postData['country_id'] = (!empty($countryData->id)?$countryData->id:0);
                                $postData['state_id'] = (!empty($stateData->id)?$stateData->id:0);
                                $postData['city_name']  =(!empty($rowData['city_name']) ? $rowData['city_name'] : NULL);
                                $postData['party_address'] = (!empty($rowData['party_address']) ? $rowData['party_address'] : NULL);
                                $postData['party_pincode'] = (!empty($rowData['party_pincode']) ? $rowData['party_pincode'] : NULL);
                                $this->party->save($postData);
                                $okCount++;
                            }
                        endif;
                    }
                    $this->printJson(['status' => 1, 'tbodyData' => $tbodyData, 'okCount' => $okCount ,'message'=>$okCount.' Record updated successfully.']);
                }
                            
            } else {
                $this->printJson(['status' => 0, 'message' => 'Data not found...!']);
            }
        else :
            $this->printJson(['status' => 0, 'message' => 'Please Select File!']);
        endif;
    }
   
    public function downloadExcel($jsonData=""){
        $postData = $this->input->post();
        $tbodyData = decodeUrl($postData['tbody']);

        $spreadsheet = new Spreadsheet();
        $reader = new \PhpOffice\PhpSpreadsheet\Reader\Html();

        $response = '<table class="table-bordered itemList" border="1" repeat_header="1">';
        $response .= '<thead>
                    <tr>
                        <th>gstin</th>
                        <th>party_name</th>
                        <th>party_code</th>
                        <th>source</th>
                        <th>sales_zone</th>
                        <th>business_type</th>
                        <th>contact_person</th>
                        <th>designation</th>
                        <th>party_mobile</th>
                        <th>whatsapp_no</th>
                        <th>party_email</th>
                        <th>credit_days</th>
                        <th>business_capacity</th>
                        <th>registration_type</th>
                        <th>pan_no</th>
                        <th>currency</th>
                        <th>distance</th>
                        <th>country</th>
                        <th>state</th>
                        <th>city_name</th>
                        <th>party_address</th>
                        <th>party_pincode</th>
                        <th>delivery_address</th>
                    </tr>
                    </thead>';
        $response .= '<tbody>'.$tbodyData.'</tbody>
                    </table>';
                    
       $spreadsheet = $reader->loadFromString($response);
       $fileDirectory = realpath(APPPATH . '../assets/uploads/party_excel');
       $fileName = '/'.str_replace('_', ' ','mismatched_party').'.xlsx';


       $writer = new Xlsx($spreadsheet);
       $writer->save($fileDirectory . $fileName);
       header("Content-Type: application/vnd.ms-excel");
       $filePath = base_url('assets/uploads/party_excel') . $fileName;
        $this->printJson(['status'=>1,'excel_path'=> $filePath]);
    }

    /* End Party Excel Upload */

    // CRM DESK START (12-06-25)
    public function crmDesk(){
        $this->data['headData']->pageTitle = "CRM DESK";
		$this->data['headData']->pageUrl = "parties/crmDesk";
        $this->data['rec_per_page'] = 25; // Records Per Page
        $this->data['sourceList'] = $this->selectOption->getSelectOptionList(['type'=>1]);
        $this->data['stageList'] = $this->leadStages->getLeadStagesList();
        $this->data['companyData'] = $this->leads->getCompanyInfo();
        $this->load->view('party/crm_desk',$this->data);
    }

    public function getLeadData($lead_stage="",$fnCall = "Ajax"){
        $postData = $this->input->post();
        $leadStages = $this->leadStages->getLeadStagesList();
        $postData['party_type'] = 2;
        $postData['party_category'] = 1;

		if(empty($postData)){
            $fnCall = 'Outside';
            $postData['lead_stage'] = $lead_stage;
        }

        $next_page = 0; $leadData = Array();
		if(!empty($postData['lead_stage']))
		{
			if(isset($postData['page']) AND isset($postData['start']) AND isset($postData['length'])){
				$leadData = $this->party->getPartyList($postData);
				$next_page = intval($postData['page']) + 1;
			}
			else{ 
                $leadData = $this->party->getPartyList($postData); 
            }
		}

		$leadList['allLead'] = '';$leadList['pendingLead'] = '';$leadList['wonLead'] = '';$leadList['lostLead'] = '';$leadDetail ='';
		if(!empty($leadData))
		{
			foreach($leadData as $row)
			{
				$editButton=''; $lostBtn=''; $contactBtn=""; $viewLeadBtn='';$deleteButton =""; $stageBtn=''; $reopenBtn='';

				$userImg = base_url('assets/images/users/user_default.png');

                $editParam = "{'postData':{'id' : ".$row->id."}, 'modal_id' : 'bs-right-lg-modal', 'form_id' : 'editLead', 'call_function' : 'edit', 'title' : 'Update Lead', 'controller' : 'parties', 'js_store_fn' : 'saveLead'}"; 
                $editButton = '<a class="dropdown-item btn-success btn-edit permission-modify" href="javascript:void(0)" flow="down" onclick="modalAction('.$editParam.');"><i class="mdi mdi-square-edit-outline"></i> Edit</a>';
             
				$contactParam = "{'postData':{'party_id' : ".$row->id."},'modal_id' : 'modal-lg', 'call_function':'updatePartyContact', 'fnsave' : 'savePartyContact','controller' : 'parties', 'form_id' : 'partyContactForm', 'title' : 'Update Party Contact','button':'close','res_function':'partyContactHtml'}";
				$contactBtn = '<a class="dropdown-item btn1 btn-info permission-modify" href="javascript:void(0);" datatip="Party Contact" flow="down" style="justify-content: flex-start;" onclick="modalAction('.$contactParam.');"><i class="fa fa-address-book"></i> Party Contact</a>';

                $viewLeadParam = "{'postData':{'id':".$row->id."},'modal_id':'modal-md','form_id':'viewLead','call_function':'viewLeadDetails','button':'close','title':'Lead Details'}";
                $viewLeadBtn = '<a href="javascript:void(0)" class="stage-btn view-btn m-0" onclick="modalAction('.$viewLeadParam.');" data-msg="View Lead Details" flow="down"><i class="fas fa-eye fs-13"></i> <span class="lable">View</span></a>';	
                
				$deleteParam = "{'postData':{'id' : ".$row->id.", 'paty_type': 'Lead'}, 'message' : 'Lead', 'controller' : 'parties', 'fndelete' : 'delete'}";
				$deleteButton = '<a class="dropdown-item btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trashLead('.$deleteParam.');" flow="down"><i class="mdi mdi-trash-can-outline"></i> Remove</a>';

                 if(!empty($leadStages)){
                    foreach($leadStages as $sc){
                        if($sc->lead_stage != 10 &&  $sc->lead_stage != $row->lead_stage && $sc->stage_type != "NEW"){
                            if($sc->lead_stage == 11){
                                $lostStage = "{'postData':{'id':'".$row->id."','lead_stage':'".$sc->lead_stage."'},'fnsave':'changeLeadStages','modal_id':'modal-md','form_id':'leadLost','call_function':'leadLost', 'js_store_fn' : 'saveLead'}";
                                $stageBtn .= '<a href="javascript:void(0)" class="dropdown-item btn-edit" style="justify-content: flex-start;" onclick="modalAction('.$lostStage.');" data-msg="Lost " flow="down"><i class="mdi mdi-close-circle"> </i>'.$sc->stage_type.'</a>';
                            }else{
                                $leadStagePrm = "{'postData':{'id':'".$row->id."','lead_stage':'".$sc->lead_stage."'},'fnsave':'changeLeadStages','message':'Are you sure want to Change Status to ".$sc->stage_type."?', 'js_store_fn' : 'saveLead' ,'confirm' :'1'}";
                                $stageBtn .= '<a href="javascript:void(0)" class="dropdown-item btn-edit" style="justify-content: flex-start;" onclick="leadEdit('.$leadStagePrm.');" data-msg="Lead Stage " flow="down"><i class="mdi mdi-close-circle"> </i>'.$sc->stage_type.'</a>';
                            }
                           
                        }
                    }
                }
                if($row->lead_stage == 11){
                    $reopenPrm = "{'postData':{'id':'".$row->id."','lead_stage':'12'},'fnsave':'changeLeadStages','message':'Are you sure want to to Re-open Lead ?', 'js_store_fn' : 'saveLead' ,'confirm' :'1'}";

                    $reopenBtn = '<a href="javascript:void(0)" class="dropdown-item btn-edit" style="justify-content: flex-start;" onclick="leadEdit('.$reopenPrm.');" data-msg="Re-Open " flow="down"><i class="mdi mdi-close-circle"> </i>Re-Open</a>';
                }
                  
				$filterCls = $row->party_type.'_lead'; $cls="";

                $rmdClass = (!empty($row->reminder_date)?'pending_response':'');

                if(empty($row->sales_executive)){$cls = "text-danger";}
                $maxChars = 30;

                $wa_text = urlencode('Hello');

				$wa_number = (!empty($row->whatsapp_no) ? str_replace('-','',str_replace('+','',$row->whatsapp_no)) : '');

				$partyName = ((strlen($row->party_name) > $maxChars) ? substr($row->party_name, 0, $maxChars).'...' : $row->party_name);
				$contactPerson = "";
				if(!empty($row->contact_person)){ $contactPerson = ((strlen($row->contact_person) > $maxChars) ? substr($row->contact_person, 0, $maxChars).'...' : $row->contact_person);}
				
				$leadDetail .= '<div class="grid_item '.$filterCls.' '.$rmdClass.'" style="width:24%;">
									<div class="card stage-item transition" data-category="transition">
										<div class="stage-title">
											<span>'.$row->source.'</span>
                                            <div class="dropdown d-inline-block float-end">
												<div class="time float-start">'.formatDate($row->created_at,"d M Y H:i:s").'</div>
												<a class="dropdown-toggle item-stage-icon" data-bs-toggle="dropdown" href="#" role="button" aria-expanded="false">
													<i class="las la-ellipsis-v"></i>
												</a>
												<div class="dropdown-menu dropdown-menu-end" aria-labelledby="drop2" style="" >
													'.$contactBtn.$reopenBtn.$stageBtn .$editButton.$deleteButton.'
												</div>
											</div>
										</div>
										<div class="stage-body">
											<a href="javascript:void(0)" class="mt-0 font-13 partyData fw-bold '.$cls.'" data-party_id="'.$row->id.'" ">'.$partyName.'</a>
                                            <p class="mb-0 font-13">'.$contactPerson.'</p>
											<p class="text-muted mb-0 font-13"><i class="fas fa-user font-12"></i> '.$row->executive.'</p>
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

    /* Lead Lost */
    public function leadLost(){
        $data = $this->input->post();
        $this->data['data'] = $data;
        $this->data['reasonList'] = $this->selectOption->getSelectOptionList(['type'=>2]);
        $this->load->view($this->lead_lost,$this->data);
    }

    public function changeLeadStages(){
        $postData = $this->input->post();
        $errorMessage = [];

        if(empty($postData['id']))
			$errorMessage['id'] = "Party is required.";
        if(empty($postData['lead_stage']))
			$errorMessage['lead_stage'] = "Lead Stage is required.";

        if (!empty($postData['lead_stage']) && $postData['lead_stage'] == 11 && empty($postData['notes'])){ 
            $errorMessage['notes'] = "Reason is required.";
        }
       
        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $this->printJson($this->party->changeLeadStages($postData));
        endif;
    }

    public function viewLeadDetails(){
        $data = $this->input->post();
        $this->data['partyData'] = $this->party->getParty(['id'=>$data['id']]);
        $this->load->view('party/view_lead_detail',$this->data);        
    }

    public function getLeadDetails(){
        $data = $this->input->post();     
        $partyData = $this->party->getParty(['id'=>$data['party_id']]);
        $salesLog = $this->getSalesLog($data);
		
        $this->printJson(['partyData'=>$partyData, 'salesLog'=>$salesLog]);
    }

    public function getSalesLog($param = [],$fnCall = "Ajax"){
        $postData = $this->input->post(); 
		if(!empty($param)){$fnCall = 'Outside';$postData = $param;} 
        $slData = $this->party->getPartyActivity(['party_id'=>$postData['party_id']]);
		$salesLog = '';
		if(!empty($slData))
		{
			foreach($slData as $row)
			{ 
                $link = '';$btn="";	$dropDown = "";
                if($row->lead_stage == 2){
                    $link = '<p class="text-muted fs-11"><strong>'.$row->mode.' : </strong> </p>';
                }
                if($row->lead_stage == 2 && empty($row->response)){
                    $responseParam = "{'postData':{'id' : ".$row->id.",'party_id' : ".$row->party_id."},'modal_id' : 'modal-md', 'form_id' : 'response', 'title' : 'Reminder Response', 'call_function' : 'reminderResponse', 'fnsave' : 'saveReminder'}";
                    $btn .= '<a class="dropdown-item btn text-dark permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="modalAction('.$responseParam.');"><i class="mdi mdi-square-edit-outline fs-12"></i> Response</a>';
                }

				if(!empty($btn)){
				    $dropDown = '<a class="text-dark lead-action" data-toggle="dropdown" href="#" role="button"><i class="fas fa-ellipsis-v"></i></a>
					<div class="dropdown-menu">'.$btn.'</div>';
				}

                $icon = isset($this->iconClass[$row->lead_stage]) ? $this->iconClass[$row->lead_stage] : 'fa fa-dot-circle-o bg-soft-blue';

                $salesLog.= '<div class="activity-info">
                                <div class="icon-info-activity">
                                    <i class="'.$icon.'"></i>
                                </div>
                                <div class="activity-info-text">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h6 class="text-muted m-1 font-12">'.$row->notes.$link.'</h6>
                                    </div>
                                    <div class="d-flex justify-content-between">
                                        <span class="text-muted w-30 d-block font-12">
                                        '.date("d F,y",strtotime($row->ref_date)).$dropDown .'
                                        </span>
                                    </div>
                                    <p class=" m-1 font-12"><i class="fa fa-user"></i> '.$row->created_by_name.'</p>
                                    '.(!empty($row->response) ? '<p class="text-muted font-11"> Res : '.$row->response.'</p>' : '').'
                                </div>
                            </div>';
             
			}
		}
		if($fnCall == 'Ajax'){$this->printJson(['salesLog'=>$salesLog]);}
		else{return $salesLog;}
    }
	
    public function saveReminder(){
        $data = $this->input->post();
        $errorMessage = [];
        if (!empty($data['lead_stage']) && $data['lead_stage'] == 2) {
            if(empty($data['ref_date']))
                $errorMessage['ref_date'] = "Date is required.";
            if(empty($data['reminder_time']))
                $errorMessage['reminder_time'] = "Time is required.";
            if(empty($data['mode']))
                $errorMessage['mode'] = "Mode is required.";
            if (empty($data['remark'])) 
			    $errorMessage['remark'] = "Remark is required.";
		}
        
        if (empty($data['response']) && !empty($data['id'])) {
			$errorMessage['response'] = "Response is required.";
		}

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:

            if(empty($data['id']) && $data['lead_stage'] == 2){
                $data['ref_date'] = date("Y-m-d H:i:s",strtotime($data['ref_date']." ".$data['reminder_time']));
                unset($data['reminder_time']);
            }
         
            $result = $this->party->savePartyActivity($data);
            $result['salesLog'] = $this->getSalesLog(['party_id'=>$data['party_id']]);
            $this->printJson($result);
        endif;
    }

    public function reminderResponse(){
        $data = $this->input->post();
        $this->data['party_id'] = $data['party_id'];
        $this->data['id'] = $data['id'];
        $this->load->view('party/reminder_response',$this->data);
    }

    /* Bulk Executive */
    public function addBulkExecutive(){
		$this->data['empList'] = $this->employee->getEmployeeList();
        $this->load->view($this->bulk_executive,$this->data);        
    }

    public function saveBulkExecutive(){
        $data = $this->input->post();
        $errorMessage = array();

        if(empty($data['executive_id'])){
            $errorMessage['executive_id'] = "Executive is required.";
        }
        if(empty($data['ref_id'][0])){
            $errorMessage['general_error'] = "Please select at least one lead.";
        }

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            foreach($data['ref_id'] as $key=>$value){
                $this->party->saveExecutive(['id'=>$value, 'executive_id'=>$data['executive_id']]);
            }
			$this->printJson(['status'=>1,'message'=>'Assigned successfuly']);
        endif;
    }

	public function getPartyData() {
        $data = $this->input->post(); 
        $leadData = $this->party->getPartyList(['executive_id'=>$data['executive'],'addData'=>1]); //'party_type'=>2
        $tbodyData='';$i=1;
        if (!empty($leadData)) {
            foreach ($leadData as $row) {
                $add = Array();
                if(!empty($row->city_name)){$add[] = $row->city_name;}
                if(!empty($row->state_name)){$add[] = $row->state_name;}
                if(!empty($row->country_name)){$add[] = $row->country_name;}
                $tbodyData .= '<tr>
                        <td class="text-center">
                            <input type="checkbox" name="ref_id[]" id="ref_id_'.$i.'" class="filled-in chk-col-success BulkExecutive" value="'.$row->id.'"><label for="ref_id_'.$i.'"></label>
                        </td>
                        <td style="width:100px;font-size:11px;" class="text-wrap text-left">'.$row->party_name.'<br><small>'.$row->executive.'</small></td>
                        <td style="width:100px;font-size:11px;" class="text-wrap text-left">'.implode(', ',$add).'</td>
                        
                        <td style="width:100px;font-size:11px;" class="text-wrap text-left">'.$row->party_address.'</td>
                    </tr>';
                $i++;
            }
        } else {
            $tbodyData .= "<td colspan='4' class='text-center'>No Data</td>";
        }
        $this->printJson(['status' => 1, 'tbodyData' => $tbodyData]);
    }
}
?>