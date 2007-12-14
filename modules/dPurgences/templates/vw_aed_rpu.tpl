{{mb_include_script module="dPpatients" script="pat_selector"}}

<script type="text/javascript">

function pageMain() {
  regFieldCalendar("editRpu", "_entree", true);

}

</script>

<form name="editRpu" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">

<input type="hidden" name="m" value="dPurgences" />
<input type="hidden" name="dosql" value="do_rpu_aed" />
<input type="hidden" name="del" value="0" />
<input type="hidden" name="rpu_id" value="{{$rpu->_id}}" />
<input type="hidden" name="sejour_id" value="{{$sejour->_id}}" />

<table class="form">
  <tr>
    {{if $rpu->_id}}
    <th class="title modify" colspan="3">Modification de l'urgence de {{$rpu->_view}}</th>
    {{else}}
    <th class="title" colspan="3">Création d'une urgence</th>
    {{/if}}
  </tr>
  <tr>
    <th>{{mb_label object=$rpu field="_responsable_id"}}</th>
    <td colspan="2">
      <select name="_responsable_id">
        {{foreach from=$listResponsables item=curr_user}}
        <option value="{{$curr_user->_id}}" {{if $curr_user->_id == $rpu->_responsable_id}}selected="selected"{{/if}}>
          {{$curr_user->_view}}
        </option>
        {{/foreach}}
      </select>
    </td>
  </tr>
  <tr>
    <th>{{mb_label object=$rpu field="_entree"}}</th>
    <td class="date" colspan="2">{{mb_field object=$rpu field="_entree" form="editRpu"}}</td>
  </tr>
  <tr>
  <th>
    <input type="hidden" name="_patient_id" class="{{$sejour->_props.patient_id}}" ondblclick="PatSelector.init()" value="{{$rpu->_patient_id}}" />
    {{mb_label object=$rpu field="_patient_id"}}
  </th>
  <td class="readonly">
  	<input type="text" name="_patient_view" size="20" value="{{$patient->_view}}" ondblclick="PatSelector.init()" readonly="readonly" />
  </td>
    <td colspan="2" class="button">
  	  <button type="button" class="search" onclick="PatSelector.init()">Choisir un patient</button>
      <script type="text/javascript">
        PatSelector.init = function(){
          this.sForm = "editRpu";
          this.sId   = "_patient_id";
          this.sView = "_patient_view";
          this.pop();
        }
      </script>
    </td>
  </tr>
  <tr>
    <th>{{mb_label object=$rpu field="ccmu"}}</th>
    <td colspan="2">{{mb_field object=$rpu field="ccmu"}}</td>
  </tr>
  <tr>
    <th>{{mb_label object=$rpu field="diag_infirmier"}}</th>
    <td colspan="2">{{mb_field object=$rpu field="diag_infirmier"}}</td>
  </tr>
  <tr>
		<td class="button" colspan="3">
		  {{if $rpu->_id}}
		  <button class="modify" type="submit">Valider</button>
		  <button class="trash" type="button" onclick="confirmDeletion(this.form,{typeName:'l\'urgence ',objName:'{{$rpu->_view|smarty:nodefaults|JSAttribute}}'})">
		    Supprimer
		  </button>
	   {{else}}
	   <button class="submit" name="btnFuseAction" type="submit">{{tr}}Create{{/tr}}</button>
    {{/if}}
  </td>
  </tr>
</table>

</form>