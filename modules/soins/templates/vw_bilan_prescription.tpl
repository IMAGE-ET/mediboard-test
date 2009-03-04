{{mb_include_script module="dPmedicament" script="medicament_selector"}}
{{mb_include_script module="dPmedicament" script="equivalent_selector"}}
{{mb_include_script module="dPprescription" script="element_selector"}}
{{mb_include_script module="dPprescription" script="prescription"}}

<table class="main">
  <tr>
    <td colspan="2">
      <form name="bilanPrescriptions" action="?" method="get">
        <input type="hidden" name="m" value="{{$m}}" />
        <input type="hidden" name="tab" value="vw_bilan_prescription" />
         
	      <table class="form">
	        <tr>
	          <th class="category" colspan="5">Critères de recherche</th>
	        </tr>
	        <tr>
	          <td>
	            Type de prescription
	            <select name="type">
	              <option value="sejour" {{if $type == "sejour"}}selected="selected"{{/if}}>Séjour</option>
	              <option value="sortie_manquante" {{if $type == "sortie_manquante"}}selected="selected"{{/if}}>Sortie manquante</option>
	              <option value="externe" {{if $type == "externe"}}selected="selected"{{/if}}>Externe</option>
	            </select>
	          </td>
	          <td>
	            <select name="signee">
	              <option value="0" {{if $signee == "0"}}selected="selected"{{/if}}>Non signées</option>
	              <option value="all" {{if $signee == "all"}}selected="selected"{{/if}}>Toutes</option>
	            </select>
	          </td>
	          <td>
			       <select name="praticien_id">
			          <option value="">&mdash; Sélection d'un praticien</option>
				        {{foreach from=$praticiens item=praticien}}
				        <option class="mediuser" 
				                style="border-color: #{{$praticien->_ref_function->color}};" 
				                value="{{$praticien->_id}}"
				                {{if $praticien->_id == $praticien_id}}selected="selected"{{/if}}>{{$praticien->_view}}
				        </option>
				        {{/foreach}}
				      </select>
	          </td>
	          <td class="date">
	            {{mb_field object=$plageconsult field="date" form="bilanPrescriptions" register="true"}}
	          </td>
	          <td>
	            <button class="button tick" type="submit">Filtrer</button>
	          </td>
	        </tr>
	      </table>
      </form>
    </td>
  </tr>
  <tr>
    <td style="width: 150px">
      <table class="tbl">
        <tr>
          <th>Prescriptions</th>
        </tr>
      {{foreach from=$prescriptions item=_prescription}}
        <tr>
          <td class="text">
            <a href="#{{$_prescription->_id}}" onclick="Prescription.reloadPrescSejour('{{$_prescription->_id}}','','','','','','',true,{{if $app->user_prefs.mode_readonly}}false{{else}}true{{/if}})">
            {{$_prescription->_ref_patient->_view}}
            </a>
          </td>
        </tr>
      {{/foreach}}
      </table>
    </td>
    <td>
      <div id="prescription_sejour">
        <div class="big-info">
          Selectionnez une prescription sur la gauche pour la visualiser 
        </div>
      </div>
    </td>
  </tr>
</table>