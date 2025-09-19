<html>
    <head>
        <title>QUOTATION</title>
        <!-- Favicon icon -->
        <link rel="icon" type="image/png" sizes="16x16" href="<?=base_url();?>assets/images/favicon.png">
    </head>
    <body>
        <div class="row">
            <div class="col-12">
                <table>
                    <tr>
                        <td>
                            <img src="<?=$letter_head?>" class="img">
                        </td>
                    </tr>
                </table>

                <table class="table bg-light-grey">
                    <tr class="" style="letter-spacing: 2px;font-weight:bold;padding:2px !important; border-bottom:1px solid #000000;">
                        <td style="width:33%;" class="fs-16 text-left">GSTIN: <?=$companyData->company_gst_no?></td>
                        <td style="width:34%;" class="fs-18 text-center">QUOTATION</td>
                        <td style="width:33%;" class="fs-16 text-right"></td>
                    </tr>
                </table>
                
                <table class="table item-list-bb fs-22" style="margin-top:5px;">
                    <tr>
                        <td style="width:60%; vertical-align:top;" rowspan="4">
                            <b>M/S. <?=$dataRow->party_name?></b><br>
                            <?=(!empty($partyData->party_address) ? $partyData->party_address ." ".$partyData->party_pincode : '')?><br>
                            <b>City : </b><?= $partyData->city_name?> <b>State : </b><?=$partyData->state_name ?> <b>Country : </b><?=$partyData->country_name ?><br><br><br>
							
							<b>Kind. Attn.: <?=$partyData->contact_person?></b><br>
							Contact No.: <?=$partyData->party_mobile?><br>
							Email: <?=$partyData->party_email?>
                        </td>
                        <td>
                            <b>Qtn. No. : <?=$dataRow->trans_number?></b>
                        </td>
                        <td>
                            Rev No. : <?=sprintf("%02d",$dataRow->quote_rev_no)?>  / <?=formatDate($dataRow->doc_date)?>
                        </td>
                    </tr>
                    <tr>
                        <td style="width:40%;" colspan="2">
                            <b>Qtn. Date</b> : <?=formatDate($dataRow->trans_date)?><br>
                        </td>
                    </tr>
					<tr>
                        <td style="width:40%;" colspan="2">
                            <b>Your Reference</b> : <?=$dataRow->ref_by.(!empty($dataRow->ref_number)?' ['.$dataRow->ref_number.']': '')?><br>
                        </td>
                    </tr>
                    <tr>
                        <td style="width:40%;" colspan="2">
                            <b>Reference Date</b> : <?=(!empty($dataRow->delivery_date) ? formatDate($dataRow->delivery_date) : '')?><br>
                        </td>
                    </tr>
                </table>
                
                <table class="table item-list-bb" style="margin-top:10px;">
                    <thead>
                        <tr>
                            <th style="width:40px;">No.</th>
                            <th class="text-left" style="width:100px;">Item Name</th>
                            <th style="width:50px;" >HSN/SAC</th>
                            <th style="width:40px;">Part/Drg No.</th>
                            <th style="width:80px;">Annual Vol</th>
                            <th style="width:50px;">Qty<small>(NOS)</small></th>
                            <th style="width:80px;">Rate<small>(<?=$partyData->currency?>)</small></th>
                            <th style="width:50px;">Taxable Amount<small>(<?=$partyData->currency?>)</small></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            $i=1;$totalQty = 0;
                            if(!empty($dataRow->itemList)):
                                foreach($dataRow->itemList as $row):	
									$rowspan = (!empty($row->item_remark) ? '2': '1');

                                    echo '<tr>';
                                        echo '<td class="text-center"  rowspan="'.$rowspan.'">'.$i++.'</td>';
                                        echo '<td>'.(!empty($row->item_code) ? '['.$row->item_code.'] ' : '').$row->item_name.'</td>';
                                        echo '<td class="text-center">'.$row->hsn_code.'</td>';
                                        echo '<td class="text-center">'.$row->drw_no.'</td>';
                                        echo '<td class="text-center">'.sprintf('%.2f',$row->annual_vol).'</td>';
                                        echo '<td class="text-center">'.sprintf('%.2f',$row->qty).' ('.$row->uom.')</td>';
                                        echo '<td class="text-center">'.moneyFormatIndia($row->price).'</td>';
                                        echo '<td class="text-right" rowspan="'.$rowspan.'">'.moneyFormatIndia($row->taxable_amount).'</td>';
                                    echo '</tr>';
                                    echo (!empty($row->item_remark)) ? '<tr><td colspan="6"><b>Notes : </b>'.$row->item_remark.'</td></tr>' : '';
                                    $totalQty += $row->qty;
                                endforeach;
                            endif;
                            $blankLines = (5 - $i);
                            if($blankLines > 0):
                                for($j=1;$j<=$blankLines;$j++):
                                    echo '<tr>
                                        <td style="border-top:none;border-bottom:none;">&nbsp;</td>
                                        <td style="border-top:none;border-bottom:none;"></td>
                                        <td style="border-top:none;border-bottom:none;"></td>
                                        <td style="border-top:none;border-bottom:none;"></td>
                                        <td style="border-top:none;border-bottom:none;"></td>
                                        <td style="border-top:none;border-bottom:none;"></td>
                                        <td style="border-top:none;border-bottom:none;"></td>
                                        <td style="border-top:none;border-bottom:none;"></td>
                                    </tr>';
                                endfor;
                            endif;
                        ?>
                        <tr>
                            <th colspan="5" class="text-right">Total Qty.</th>
                            <th class="text-right"><?=sprintf('%.3f',$totalQty)?></th>
                            <!-- <th></th> -->
                            <th class="text-right">Sub Total</th>
                            <th class="text-right"><?=moneyFormatIndia(sprintf('%.2f',$dataRow->taxable_amount))?></th>
                            
                        </tr>
						<?php
							$rwspan= 0; $srwspan = '';
							$beforExp = "";
							$afterExp = "";
							$invExpenseData = (!empty($dataRow->expenseData)) ? $dataRow->expenseData : array();
							foreach ($expenseList as $row) :
								$expAmt = 0;
								$amtFiledName = $row->map_code . "_amount";
								if (!empty($invExpenseData) && $row->map_code != "roff") :
									$expAmt = floatVal($invExpenseData->{$amtFiledName});
								endif;

								if(!empty($expAmt)):
									if ($row->position == 1) :
										if($rwspan == 0):
											$beforExp .= '<th class="text-right">'.$row->exp_name.'</th>
											<td class="text-right">'.sprintf('%.2f',$expAmt).'</td>';
										else:
											$beforExp .= '<tr>
												<th class="text-right">'.$row->exp_name.'</th>
												<td class="text-right">'.sprintf('%.2f',$expAmt).'</td>
											</tr>';
										endif;                                
									else:
										$afterExp .= '<tr>
											<th class="text-right">'.$row->exp_name.'</th>
											<td class="text-right">'.sprintf('%.2f',$expAmt).'</td>
										</tr>';
									endif;
									$rwspan++;
								endif;
							endforeach;

							$taxHtml = '';
							foreach ($taxList as $taxRow) :
								$taxAmt = 0;
								$taxAmt = floatVal($dataRow->{$taxRow->map_code.'_amount'});
								if(!empty($taxAmt)):
									if($rwspan == 0):
										$taxHtml .= '<th class="text-right">'.$taxRow->name.'</th>
										<td class="text-right">'.sprintf('%.2f',$taxAmt).'</td>';
									else:
										$taxHtml .= '<tr>
											<th class="text-right">'.$taxRow->name.'</th>
											<td class="text-right">'.sprintf('%.2f',$taxAmt).'</td>
										</tr>';
									endif;
								
									$rwspan++;
								endif;
							endforeach;

							$fixRwSpan = (!empty($rwspan))?3:0;
						?>
						<tr>
							<th class="text-left" colspan="6" rowspan="<?=$rwspan?>">
								<b>Note: </b> <?= $dataRow->remark?>
							</th>

							<?php if(empty($rwspan)): ?>
                                <th class="text-right">Round Off</th>
								<td class="text-right"><?=sprintf('%.2f',$dataRow->round_off_amount)?></td>
                            <?php endif; ?>
                            
						</tr>
						<?=$beforExp.$taxHtml.$afterExp?>
						<tr>
							<th class="text-left" colspan="6" rowspan="3">
								Amount In Words (<?=$partyData->currency?>): <br><?=numToWordEnglish(sprintf('%.2f',$dataRow->net_amount))?>
							</th>

							<?php if(empty($rwspan)): ?>
                                <th class="text-right">Grand Total (<?=$partyData->currency?>)</th>
                                <th class="text-right"><?=moneyFormatIndia(sprintf('%.2f',$dataRow->net_amount))?></th>
                            <?php endif; ?>
						</tr>

						<?php if(!empty($rwspan)): ?>
						<tr>
							<th class="text-right">Round Off</th>
							<td class="text-right"><?=sprintf('%.2f',$dataRow->round_off_amount)?></td>
						</tr>
						<tr>
							<th class="text-right">Grand Total (<?=$partyData->currency?>)</th>
							<th class="text-right"><?=moneyFormatIndia(sprintf('%.2f',$dataRow->net_amount))?></th>
						</tr>	
						<?php endif; ?>
                    </tbody>
                </table>
                
                <div style="font-size:12px;padding-left:10px;">
                    <strong class="text-left">Terms & Conditions :-</strong><br>
                    <?php
                        if(!empty($termsData->condition)):
                                echo $termsData->condition;
                        endif;
                    ?>
                </div>
				<htmlpagefooter name="lastpage">
                    <table class="table top-table" style="margin-top:0px;border-top:1px solid #545454;">
                        <tr>
                            <td style="width:30%;"></td>
                            <td style="width:20%;"></td>
                            <td style="width:20%;"></td>
                            <th class="text-center">For, <?=$companyData->company_name?></th>
                        </tr>
                        <tr>
                            <td colspan="3" height="40"></td>
                        </tr>
                        <tr>
                            <td><br>This is a computer-generated quotation.</td>
                            <td class="text-center"><?=$dataRow->created_name?><br>Prepared By</td>
                            <td class="text-center"><?=$dataRow->internal_aprv_name?><br>Approved By</td>
                            <td class="text-center"><br>Authorised By</td>
                        </tr>
                    </table>
                    <table class="table top-table" style="margin-top:0px;border-top:1px solid #545454;">
						<tr>
							<td style="width:25%;">Qtn. No. & Date : <?=$dataRow->trans_number.' ['.formatDate($dataRow->trans_date).']'?></td>
							<td style="width:25%;"></td>
							<td style="width:25%;text-align:right;">Page No. {PAGENO}/{nbpg}</td>
						</tr>
					</table>
                </htmlpagefooter>
				<sethtmlpagefooter name="lastpage" value="on" /> 
            </div>
        </div>        
    </body>
</html>