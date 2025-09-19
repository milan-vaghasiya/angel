<form>
	<div class="col-md-12">
        <div class="row">
			<input type="hidden" name="id" value="<?=(!empty($dataRow->id))?$dataRow->id:""?>" />
			
			<div class="col-md-12 form-group">
				<label for='kpi_name'>KPI Type</label>
				<input type="text" id="kpi_name" name="kpi_name" class="form-control req" value="<?=(!empty($dataRow->kpi_name))?$dataRow->kpi_name:""?>">
			</div>
		</div>
	</div>	
</form>
            
