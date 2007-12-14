<table class="tbl">
  <tr>
    <th>{{tr}}CRPU-_patient_id{{/tr}}</th>
    <th>{{tr}}CRPU-_entree{{/tr}}</th>
    <th>{{tr}}CRPU-ccmu{{/tr}}</th>
    <th>{{tr}}CRPU-diag_infirmier{{/tr}}</th>
  </tr>
  {{foreach from=$listSejours item=curr_sejour}}
  <tr>
    <td>
      <a href="?m=dPurgences&tab=vw_aed_rpu&rpu_id={{$curr_sejour->_ref_rpu->_id}}">
        {{$curr_sejour->_ref_patient->_view}}
      </a>
    </td>
    <td>
      <a href="?m=dPurgences&tab=vw_aed_rpu&rpu_id={{$curr_sejour->_ref_rpu->_id}}">
        {{$curr_sejour->_entree}}
      </a>
    </td>
    <td>
      <a href="?m=dPurgences&tab=vw_aed_rpu&rpu_id={{$curr_sejour->_ref_rpu->_id}}">
        {{$curr_sejour->_ref_rpu->ccmu}}
      </a>
    </td>
    <td>
      <a href="?m=dPurgences&tab=vw_aed_rpu&rpu_id={{$curr_sejour->_ref_rpu->_id}}">
        {{$curr_sejour->_ref_rpu->diag_infirmier|nl2br}}
      </a>
    </td>
  </tr>
  {{/foreach}}
</table>