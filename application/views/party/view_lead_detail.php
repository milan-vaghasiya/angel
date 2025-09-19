<form>
    <div class="col-md-12">
        <div class="row">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <tr>
                        <th>Company Name</th>
                        <td colspan="3"><?=(!empty($partyData->party_name) ? $partyData->party_name : '')?></td>
                    </tr>
                    <tr>
                        <th style="width:25%">Contact Person</th>
                        <td style="width:25%"><?=(!empty($partyData->contact_person) ? $partyData->contact_person : '')?></td>
                        <th style="width:25%">Contact No.</th>
                        <td style="width:25%"><?=(!empty($partyData->party_mobile) ? $partyData->party_mobile : '')?></td>
                    </tr>
                    <tr>
                        <th>Country</th>
                        <td><?=(!empty($partyData->country_name) ? $partyData->country_name : '')?></td>
                        <th>State</th>
                        <td><?=(!empty($partyData->state_name) ? $partyData->state_name : '')?></td>
                    </tr>
                    <tr>
                        <th>City</th>
                        <td><?=(!empty($partyData->city_name) ? $partyData->city_name : '')?></td>
                        <th>Pincode</th>
                        <td><?=(!empty($partyData->party_pincode) ? $partyData->party_pincode : '')?></td>
                    </tr>
                    <tr>
                        <th>Address</th>
                        <td colspan="3"><?=(!empty($partyData->party_address) ? $partyData->party_address : '')?></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</form>