<table class="tbl">
  <tr>
    <th>Patient</th>
    <th>Arrivée</th>
    <th>Degré d'urgence</th>
    <th>Diagnostic infirmier</th>
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