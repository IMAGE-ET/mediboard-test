<div style="max-height: 300px; overflow-y: auto; overflow-x: hidden;">

<form name="TreatEvents" method="post" action="?">
  <input type="hidden" name="m" value="ssr" />
  <input type="hidden" name="dosql" value="do_treat_evenements" />
  <input type="hidden" name="realise_ids" value="" />
  <input type="hidden" name="annule_ids" value="" />
</form>

<table class="tbl" style="margin-right: 10px; text-align: left;" id="list-evenements-modal">
  <tr>
    <th colspan="8" class="title">Evenements</th>
  </tr>
  {{foreach from=$evenements item=_evenements_by_sejour key=sejour_id}}
    {{assign var=sejour value=$sejours.$sejour_id}}
    <tr>
      <th colspan="6">
        {{assign var=patient value=$sejour->_ref_patient}}
        <big onmouseover="ObjectTooltip.createEx(this, '{{$patient->_guid}}')">
          {{$patient}}
        </big>
      </th>
      <th class="narrow">
        <button style="float: right" class="tick notext" type="button" onclick="ModalValidation.toggleSejour('{{$sejour_id}}', 'realise');">
          {{tr}}Validate{{/tr}}
        </button>
      </th>
      <th class="narrow">
        <button style="float: right" class="cancel notext" type="button" onclick="ModalValidation.toggleSejour('{{$sejour_id}}', 'annule');">
          {{tr}}Cancel{{/tr}}
        </button>
      </th>
    </tr> 

    {{assign var=count_traite value=0}}
    {{foreach from=$_evenements_by_sejour item=_evenements_by_element}}
    {{foreach from=$_evenements_by_element item=_evenement}}
      {{if $_evenement->_traite}} {{assign var=count_traite value=$count_traite+1}} {{/if}}
    {{/foreach}}
    {{/foreach}}
    {{if $count_traite}} 
    <tr>
      <td colspan="8">
	      <div class="small-info">
	        {{$count_traite}} événéments ont déjà été traités pour ce patient.
	        <strong>Aucun ne sera donc pas pré-sélectionné</strong>.
	      </div>
      </td>
    </tr>      
    {{/if}}

    {{foreach from=$_evenements_by_sejour item=_evenements_by_element}}
    {{foreach from=$_evenements_by_element item=_evenement}}
      <tr>
        <td class="text">
          {{assign var=line    value=$_evenement->_ref_prescription_line_element}}
          {{assign var=element value=$line->_ref_element_prescription}}
          {{if $line}}
            {{mb_ditto name="element-$sejour_id" value=$line|ternary:$element->_view:'-'}}
          {{/if}}
        </td>
        <td style="text-align: right;">
          {{assign var=config_date value=$conf.date}}
          {{mb_ditto name="date-$sejour_id" value=$_evenement->debut|@date_format:"%A $config_date"}}
        </td>
        <td>
           {{$_evenement->debut|@date_format:$conf.time}}
        </td>
        <td>
          {{mb_value object=$_evenement field="duree"}} min
        </td>
        
        <td>
          {{if $_evenement->_count_actes}}
          <div>
            {{foreach from=$_evenement->_ref_actes_cdarr item=_acte}}
              <span onmouseover="ObjectTooltip.createEx(this, '{{$_acte->_guid}}')">
                {{$_acte}}
              </span> 
            {{/foreach}}
          </div>
          
          {{foreach from=$_evenement->_ref_actes_csarr item=_acte}}
          <div>
            <strong onmouseover="ObjectTooltip.createEx(this, '{{$_acte->_guid}}')">
              {{$_acte}}
            </strong> 
            {{foreach from=$_acte->_ref_activite_csarr->_ref_modulateurs item=_modulateur}}
            <label title="{{$_modulateur->_libelle}}">
              <!--input type="checkbox" /-->
              {{$_modulateur->modulateur}}
            </label>
            {{/foreach}}
          </div>
          {{/foreach}}
          
          {{else}}
          <div class="small-warning">
            {{tr}}CEvenementSSR-warning-no_code_ssr{{/tr}}
          </div>
          
          {{/if}}
        </td> 

        <td>
        	{{assign var=equipement value=$_evenement->_ref_equipement}}
					{{if $equipement->_id}} 
					  {{$equipement}}
					{{/if}}
        </td>
        <td>
          <input class="{{$sejour->_guid}} {{$_evenement->_guid}} realise" type="checkbox" value="{{$_evenement->_id}}" 
					  onchange="if (this.checked) $$('input.{{$_evenement->_guid}}.annule')[0].checked = false;"
					  {{if !$_evenement->_count_actes}} disabled="disabled" 
						{{elseif $_evenement->realise || !$count_traite}}  checked="checked" 
						{{/if}} 
					/>
        </td>
        <td>
          <input class="{{$sejour->_guid}} {{$_evenement->_guid}} annule" type="checkbox" value="{{$_evenement->_id}}" 
            onchange="if (this.checked) $$('input.{{$_evenement->_guid}}.realise')[0].checked = false;"
            {{if !$_evenement->_count_actes}} disabled="disabled" 
            {{elseif $_evenement->annule}}  checked="checked" 
            {{/if}} 
          />
        </td>
      </tr>
    {{/foreach}}
    {{/foreach}}
  {{foreachelse}}
	<tr>
    <td class="empty">{{tr}}CEvenementSSR.none{{/tr}}</td>
  </tr>
  {{/foreach}}
</table>
</div>

<hr />

{{if $count_zero_actes}} 
<div class="small-warning">
	{{tr}}CEvenementCdARR-msg-count_zero_actes{{/tr}} :
	(<strong>{{$count_zero_actes}} {{tr}}CEvenementSSR{{/tr}} </strong>)
</div>
{{/if}}

<table class="form">
  <tr>
    <td colspan="7" class="button">
      <button type="button" class="submit singleclick" onclick="ModalValidation.submitModal();">{{tr}}Validate{{/tr}}</button>
      <button type="button" class="cancel" onclick="ModalValidation.close();">{{tr}}Cancel{{/tr}}</button>
    </td>
  </tr>
</table>