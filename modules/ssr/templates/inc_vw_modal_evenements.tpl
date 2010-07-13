<div style="height: 300px; overflow-y:auto; overflow-x:hidden;">
<table class="tbl" style="margin-right:10px" id="list-evenements-modal">
	<tr>
		<th colspan="7">Evenements</th>
	</tr>
	{{foreach from=$evenements item=_evenements_by_sejour key=sejour_id}}
	  {{assign var=sejour value=$sejours.$sejour_id}}
		<tr>
		  <th colspan="7">{{$sejour->_ref_patient->_view}}</th>
		</tr>	
		{{foreach from=$_evenements_by_sejour item=_evenements_by_element}}
	  {{foreach from=$_evenements_by_element item=_evenement}}
			<tr>
        <td>
          {{mb_ditto name="element-$sejour_id" value=$_evenement->_ref_prescription_line_element->_ref_element_prescription->_view}}
        </td>
        <td style="text-align: center;">
          {{mb_ditto name="date-$sejour_id" value=$_evenement->debut|@date_format:$dPconfig.date}}
        </td>
				<td>
				   {{$_evenement->debut|@date_format:$dPconfig.time}}
				</td>
				<td>
					{{mb_value object=$_evenement field="duree"}} min
				</td>		
				<td>
					{{foreach from=$_evenement->_ref_actes_cdarr item=_acte}}
					  {{$_acte}} 
					{{/foreach}}
				</td>	
				<td>
          {{mb_value object=$_evenement field="equipement_id"}}
        </td>
        <td>
          <input type="checkbox" checked="checked" onclick="tab_selected.toggle('{{$_evenement->_id}}');" value="{{$_evenement->_id}}" />
        </td>
      </tr>
			{{/foreach}}
		{{/foreach}}
	{{/foreach}}
	<tr>
		<td colspan="7" class="button">
			<button type="button" class="submit" onclick="submitValidation(oFormSelectedEvents); modalWindow.close();">{{tr}}Validate{{/tr}}</button>
			<button type="button" class="cancel" onclick="modalWindow.close();">{{tr}}Cancel{{/tr}}</button>
		</td>
	</tr>
</table>
</div>