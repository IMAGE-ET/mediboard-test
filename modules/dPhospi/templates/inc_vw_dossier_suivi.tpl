<script type="text/javascript">

delCibleTransmission = function() {
  oDiv = $('cibleTrans');
  if(!oDiv) {
    return;
  }
  oForm = document.forms['editTrans'];
  $V(oForm.object_class, "");
  $V(oForm.object_id, "");
  oDiv.innerHTML = "";
}

</script>

<table class="form">
  <tr>
    <th class="title" style="width: 50%" colspan="4">
      Observations
    </th>
    <th class="title" style="width: 50%" colspan="4">
      Transmissions
    </th>
  </tr>
  <tr>
    <td colspan="4">
      {{if $isPraticien}}
      <form name="editObs" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">
	      <input type="hidden" name="dosql" value="do_observation_aed" />
	      <input type="hidden" name="del" value="0" />
	      <input type="hidden" name="m" value="dPhospi" />
	      <input type="hidden" name="sejour_id" value="{{$sejour->_id}}" />
	      <input type="hidden" name="user_id" value="{{$user->_id}}" />
	      <input type="hidden" name="date" value="now" /> 
	      <div style="float: right">
		      <select name="_helpers_text" size="1" onchange="pasteHelperContent(this);">
		        <option value="">&mdash; Choisir une aide</option>
		        {{html_options options=$observation->_aides.text.no_enum}}
		      </select>
		      <button class="new notext" title="Ajouter une aide à la saisie" type="button" onclick="addHelp('CObservationMedicale', this.form.text)">{{tr}}New{{/tr}}</button><br />      
	      </div>
	      {{mb_label object=$observation field="text"}}
	      {{mb_field object=$observation field="degre"}}
	      <br />
	      {{mb_field object=$observation field="text"}}
	      <br />
	      <button type="button" class="add" onclick="submitSuivi(this.form, '{{$prescription->_id}}')">{{tr}}Add{{/tr}}</button> 
      </form>
      {{/if}}
    </td>     
    <td colspan="4">
      <div id="cibleTrans" style="font-style: italic;" onclick="delCibleTransmission()">
      </div>
      <form name="editTrans" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">
      <input type="hidden" name="dosql" value="do_transmission_aed" />
      <input type="hidden" name="del" value="0" />
      <input type="hidden" name="m" value="dPhospi" />
      <input type="hidden" name="object_class" value="" />
      <input type="hidden" name="object_id" value="" />
      <input type="hidden" name="sejour_id" value="{{$sejour->_id}}" />
      <input type="hidden" name="user_id" value="{{$user->_id}}" />
      <input type="hidden" name="date" value="now" />
      <div style="float: right">
		    <select name="_helpers_text" size="1" onchange="pasteHelperContent(this);">
		      <option value="">&mdash; Choisir une aide</option>
		      {{html_options options=$transmission->_aides.text.no_enum}}
		    </select>
		    <button class="new notext" title="Ajouter une aide à la saisie" type="button" onclick="addHelp('CTransmissionMedicale', this.form.text)">{{tr}}New{{/tr}}</button><br />      
	    </div>
      {{mb_label object=$transmission field="text"}}
      {{mb_field object=$transmission field="degre"}}
      <br />
      {{mb_field object=$transmission field="text"}}
      <br />
      <button type="button" class="add" onclick="submitSuivi(this.form, '{{$prescription->_id}}')">{{tr}}Add{{/tr}}</button>
      </form>
    </td>
  </tr>
</table>

{{include file="../../dPhospi/templates/inc_list_transmissions.tpl"}}