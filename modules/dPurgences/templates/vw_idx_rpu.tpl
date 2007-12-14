<table class="tbl">
  <tr>
    <th>{{tr}}CRPU-_patient_id{{/tr}}</th>
    <th>{{tr}}CRPU-_entree{{/tr}}</th>
    <th>{{tr}}CRPU-ccmu{{/tr}}</th>
    <th>{{tr}}CRPU-_responsable_id{{/tr}}</th>
    <th>{{tr}}CRPU-diag_infirmier{{/tr}}</th>
    <th />
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
        {{$curr_sejour->_entree|date_format:"%d/%m/%Y à %Hh%M"}}
      </a>
    </td>
    <td>
      <a href="?m=dPurgences&tab=vw_aed_rpu&rpu_id={{$curr_sejour->_ref_rpu->_id}}">
        {{tr}}CRPU.ccmu.{{$curr_sejour->_ref_rpu->ccmu}}{{/tr}}
      </a>
    </td>
    <td>
      <a href="?m=dPurgences&tab=vw_aed_rpu&rpu_id={{$curr_sejour->_ref_rpu->_id}}">
        {{$curr_sejour->_ref_praticien->_view}}
      </a>
    </td>
    <td>
      <a href="?m=dPurgences&tab=vw_aed_rpu&rpu_id={{$curr_sejour->_ref_rpu->_id}}">
        {{$curr_sejour->_ref_rpu->diag_infirmier|nl2br}}
      </a>
    </td>
    <td class="button">
      <!-- ici c'est comme pour une consult immédiate -->
      <form>
        <select name="praticien_id">
          <option>&mdash Choisir un praticien</option>
          {{foreach from=$listPrats item="curr_prat"}}
          <option value="$curr_prat->_id" {{if $curr_prat->_id == $app->user_id}}selected="selected"{{/if}}>
            Dr. {{$curr_prat->_view}}
          </option>
          {{/foreach}}
        </select>
        <br />
        <button type="button" class="new">Prendre en charge</button>
      </select>
    </td>
  </tr>
  {{/foreach}}
</table>