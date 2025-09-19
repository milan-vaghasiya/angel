<div style="padding:0px 10px;text-align:justify;font-family:Times New Roman;font-size:15px;">
	<table>
		<tr>
			<th style="text-align:center;font-size:18px;"><u>OFFER LETTER</u></th>
		</tr>
		<tr>
			<td style="width:100%;text-align:right;padding-top:20px;"><b>Date: <?= date("d-m-Y")?></b></td>
		</tr>
		<tr>
			<td style="width:100%;padding-top:30px;">
				<p>To,</p>
				<p><b><?= $empData->emp_name;?></b></p>
				<p><b><?= $empData->emp_address;?></b></p><br>
				<p><b>Dear <?= $empData->emp_name;?>,</b></p>
			</td>
		</tr>
	</table>
	<p style="text-indent:40px;">This has reference to your application for employment and the subsequent interview you had with us. We are pleased to make you an offer for the position of <b><?=$empData->designation_name?></b> in our organization. Based at Plant Office <?=$companyData->company_address?> <b>Your CTC will be as per mutual agreed</b>. Your joining date will on or before <b><?= date("d-m-Y",strtotime($empData->emp_joining_date))?></b>. On joining, you will need to sign an Employment Detailing all terms and conditions of your appointment.</b>.</p>
	
	<p>You will be on probation for a period of <b>6 months</b> from the date of joining and this can be extended for a further period at the Company’s discretion. Notice Period: In Probation Period is One Month and After Confirmation notice Period is Two Months or salary in lieu thereof.</p>

	<p>Please carry a copy of the below mentioned documents on the date of joining. You will also need to carry the originals for verification.</p>
	
	<ul style="list-style-type:circle">
		<li>5 coloured passport size photos</li>
		<li>Experience letters & Reliving Letter </li>
		<li>Salary proof (if applicable, last 3 salary slips, bank statement where salary was credited)</li>
		<li>Photo ID proof</li>
		<li>Address proof</li>
		<li>PAN card</li>
		<li>Cheque with your authorised Signature</li>
	</ul>

	<p>Please sign and return to us the duplicate of this letter as a token of your acceptance of the above offer letter latest by <b><?=date("d-m-Y")?></b>.</p>
	
	<p>We look forward to you joining our team and hope it is the beginning of a mutually fulfilling association.</p>

	<p><b>For,<?=$companyData->company_name?></b></p>

	<p><b>MANAGING DIRECTOR</b><br>
	I have read and accept the terms and conditions.</p>
	
	<table>
		<tr>
			<td style="width:15%;">Date of Joining :</td>
			<td style="width:30%;"><b><?= date("d-m-Y",strtotime($empData->emp_joining_date))?></b></td>
			<td style="width:55%;"></td>
		</tr>
		<tr>
			<td style="width:20%;padding-top:15px;">Signature :</td>
			<td style="width:25%;padding-top:15px;" style="border-bottom:1px solid #000;"></td>
		</tr>
		<tr>
			<td style="width:20%;padding-top:15px;">Date :</td>
			<td style="width:25%;padding-top:15px;" style="border-bottom:1px solid #000;"></td>
		</tr>
	</table>
</div>