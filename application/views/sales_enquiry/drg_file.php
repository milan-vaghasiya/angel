<form>
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" name="id" value="<?=$dataRow->id?>">

            <div class="col-md-12 form-group">
				<label for="drg_file">Drawing File</label>
				<div class="input-group">
					<input type="file" name="drg_file" class="form-control" value="" />
					<?php if(!empty($dataRow->drg_file)): ?>
						<div class="input-group-append">
							<a href="<?=base_url('assets/uploads/sales_enquiry/'.$dataRow->drg_file)?>" class="btn btn-outline-primary" target="_blank" ><i class="fas fa-download"></i></a>
						</div>
					<?php endif; ?>
				</div>
			</div>
        </div>
    </div>
</form>
