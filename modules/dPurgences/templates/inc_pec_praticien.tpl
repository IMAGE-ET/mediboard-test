{{** 
  * Permet un accès la prise en charge UPATOU, la crée si elle n'existe pas
  * 
  * @param $listPrats array|CMediusers Praticiens disponibles
  * @param $rpu CRPU Résumé de passage aux urgences
  *}}

{{assign var=sejour value=$rpu->_ref_sejour}}
{{assign var=consult value=$rpu->_ref_consult}}
 
{{if !$consult->_id}}
{{if $can->edit}}
<form name="createConsult-{{$rpu->_id}}" method="post" action="?">
  <input type="hidden" name="dosql" value="do_consult_now" />
  <input type="hidden" name="m" value="dPcabinet" />
  <input type="hidden" name="del" value="0" />
  <input type="hidden" name="sejour_id" value="{{$sejour->_id}}" />   
  <input type="hidden" name="patient_id" value="{{$sejour->patient_id}}" />    
  <select name="prat_id">
    <option value="">&mdash; Choisir un praticien</option>
    {{foreach from=$listPrats item=_prat}}
    <option class="mediuser" style="border-color: #{{$_prat->_ref_function->color}};" value="{{$_prat->_id}}"
			{{if $app->user_id == $_prat->_id}} selected="selected" {{/if}}>
      {{$_prat->_view}}
    </option>
    {{/foreach}}
  </select>
  
  <br />
  <button type="submit" class="new" onclick="return checkPraticien(this.form)">Prendre en charge</button>
</form>

{{else}}
  - 
{{/if}}

{{else}}
	par {{$consult->_ref_praticien->_view}}
	{{if $can->edit}}
	<br />
	<a class="action" title="Prise en charge" href="?m=dPurgences&amp;tab=edit_consultation&amp;selConsult={{$consult->_id}}">
	  Voir prise en charge
	</a>
	{{/if}}
  
{{/if}}
