<input type="hidden" name="sejour_id" value="{{$consult->_ref_consult_anesth->_ref_operation->_ref_sejour->_id}}" />
  {{if $consult->_ref_consult_anesth->_ref_operation->_id}}
  <input type="hidden" name="listCim10Sejour" value="{{$consult->_ref_consult_anesth->_ref_operation->_ref_sejour->_ref_dossier_medical->listCim10}}" />
  {{else}}
  <input type="hidden" name="listCim10Sejour" value="" />
  {{/if}}
