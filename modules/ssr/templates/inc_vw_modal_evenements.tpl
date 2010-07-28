<div style="max-height: 300px; overflow-y: auto; overflow-x: hidden;">
	
<table class="tbl" style="margin-right: 10px" id="list-evenements-modal">
  <tr>
    <th colspan="7" class="title">Evenements</th>
  </tr>
  {{foreach from=$evenements item=_evenements_by_sejour key=sejour_id}}
    {{assign var=sejour value=$sejours.$sejour_id}}
    <tr>
      <th colspan="7">
        {{assign var=patient value=$sejour->_ref_patient}}
        <button style="float: right" class="change notext" type="button" onclick="Console.debug('{{$sejour_id}}', 'toggle for séjour')">
        	{{tr}}Change{{/tr}}
				</button>
        <big onmouseover="ObjectTooltip.createEx(this, '{{$patient->_guid}}')">
          {{$patient}}
        </big>
      </th>
    </tr> 
    {{foreach from=$_evenements_by_sejour item=_evenements_by_element}}
    {{foreach from=$_evenements_by_element item=_evenement}}
      <tr>
        <td class="text">
          {{mb_ditto name="element-$sejour_id" value=$_evenement->_ref_prescription_line_element->_ref_element_prescription->_view}}
        </td>
        <td style="text-align: right;">
          {{assign var=config_date value=$dPconfig.date}}
          {{mb_ditto name="date-$sejour_id" value=$_evenement->debut|@date_format:"%A $config_date"}}
        </td>
        <td>
           {{$_evenement->debut|@date_format:$dPconfig.time}}
        </td>
        <td>
          {{mb_value object=$_evenement field="duree"}} min
        </td>   
        <td>
          {{foreach from=$_evenement->_ref_actes_cdarr item=_acte}}
            <span onmouseover="ObjectTooltip.createEx(this, '{{$_acte->_guid}}')">
              {{$_acte}}
            </span> 
          {{/foreach}}
        </td> 
        <td>
          {{mb_value object=$_evenement field="equipement_id"}}
        </td>
        <td>
          <input class="{{$sejour->_guid}}" type="checkbox" checked="checked" onclick="tab_selected.toggle('{{$_evenement->_id}}');" value="{{$_evenement->_id}}" />
        </td>
      </tr>
      {{/foreach}}
    {{/foreach}}
  {{/foreach}}
</table>
</div>

<hr />
<table class="form">
  <tr>
    <td colspan="7" class="button">
      <button type="button" class="submit" onclick="submitValidation(oFormSelectedEvents); modalWindow.close();">{{tr}}Validate{{/tr}}</button>
      <button type="button" class="cancel" onclick="modalWindow.close();">{{tr}}Cancel{{/tr}}</button>
    </td>
  </tr>
</table>