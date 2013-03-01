<!-- Séjour et interventions -->

{{if !$app->user_prefs.simpleCabinet}}
<div>
	<span onmouseover="ObjectTooltip.createDOM(this, 'sejours');">
	  {{$patient->_ref_sejours|@count}} {{tr}}CSejour{{/tr}}(s)   
	</span>
</div>
  
<table class="tbl" id="sejours" style="display: none;">
  {{foreach from=$patient->_ref_sejours item=_sejour}}
  {{if $_sejour->group_id == $g || $conf.dPpatients.CPatient.multi_group == "full"}}
  <tr>
    <td>
      <a href="?m=dPplanningOp&amp;tab=vw_edit_sejour&amp;sejour_id={{$_sejour->_id}}"
        onmouseover="ObjectTooltip.createEx(this, '{{$_sejour->_guid}}')">
        {{mb_include module=system template=inc_interval_date from=$_sejour->entree to=$_sejour->sortie}}
      </a>
		</td>
		<td>
    	{{mb_include module=mediusers template=inc_vw_mediuser mediuser=$_sejour->_ref_praticien}}
		</td>
	</tr>
  {{foreach from=$_sejour->_ref_operations item=_op}}
  <tr>
  	<td style="padding-left: 1em;">
      <a href="?m=dPplanningOp&amp;tab=vw_edit_planning&amp;operation_id={{$_op->_id}}"
        onmouseover="ObjectTooltip.createEx(this, '{{$_op->_guid}}')">
        le {{$_op->_datetime|date_format:$conf.date}}
      </a>
		</td>
		<td>
      {{mb_include module=mediusers template=inc_vw_mediuser mediuser=$_op->_ref_chir}}
		</td>
	</tr>
  {{foreachelse}}
  <tr>
  	<td colspan="2" class="empty" style="padding-left: 1em;">
       {{tr}}COperation.none{{/tr}}
  	</td>
	</tr>
	{{/foreach}}
  {{else}}
  <tr>
    <td>
      {{mb_include module=system template=inc_interval_date from=$_sejour->entree to=$_sejour->sortie}}
    </td>
    <td style="background-color:#afa">
      {{$_sejour->_ref_group->text|upper}}
    </td>
  </tr>
  {{/if}}

  {{foreachelse}}
  <tr>
    <td class="empty">{{tr}}CSejour.none{{/tr}}</td>
  </tr>
  {{/foreach}}
</table>
{{/if}}
  
<!-- Consultations -->
<div>
	<span  onmouseover="ObjectTooltip.createDOM(this, 'consultations');">
		{{$patient->_ref_consultations|@count}} {{tr}}CConsultation{{/tr}}(s)
	</span>
</div>
  
<table class="tbl" id="consultations" style="display: none;">
  
  {{foreach from=$patient->_ref_consultations item=_consult}}
  {{if $_consult->_ref_chir->_ref_function->group_id == $g || $conf.dPpatients.CPatient.multi_group == "full"}}
  <tr>
    <td>
      <a href="?m=dPcabinet&amp;tab=edit_consultation&amp;selConsult={{$_consult->_id}}"
        onmouseover="ObjectTooltip.createEx(this, '{{$_consult->_guid}}')">
        le {{$_consult->_datetime|date_format:$conf.date}}
      </a>
    </td>
    <td>
      {{mb_include module=mediusers template=inc_vw_mediuser mediuser=$_consult->_ref_chir}}
    </td>

    <td>
    </td>
  </tr>
  {{elseif $conf.dPpatients.CPatient.multi_group == "limited"}}
  <tr>
    <td>le {{$_consult->_datetime|date_format:$conf.date}}</td>
    <td style="background-color:#afa">
      {{$_consult->_ref_chir->_ref_function->_ref_group->text|upper}}
    </td>

    <td>
    </td>
  </tr>
  {{/if}}
  {{foreachelse}}
  
  <tr>
    <td colspan="2" class="empty">{{tr}}CConsultation.none{{/tr}}</td>
  </tr>
  {{/foreach}}

</table>

{{assign var=multiple_dossiers_anesth value=$conf.dPcabinet.CConsultAnesth.multiple_dossiers_anesth}}
{{if $multiple_dossiers_anesth}}
  <div id="dossiers_anesth_area">
    {{mb_include module=cabinet template=inc_multi_consult_anesth}}
  </div>
{{/if}}