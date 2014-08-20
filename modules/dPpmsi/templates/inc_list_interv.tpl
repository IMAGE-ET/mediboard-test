<tr>
  <td>
    {{if $_operation->facture}}
      <img src="images/icons/tick.png" alt="ok" />
    {{else}}
      <img src="images/icons/cross.png" alt="alerte" />
    {{/if}}
  </td>
  
	<td>
  	{{assign var=sejour value=$_operation->_ref_sejour}}
    <span onmouseover="ObjectTooltip.createEx(this, '{{$sejour->_guid}}')">
    	<strong>{{mb_include module=planningOp template=inc_vw_numdos nda_obj=$sejour}}</strong>
    </span>
  </td>
  
	<td class="text">
    {{mb_include module=mediusers template=inc_vw_mediuser mediuser=$_operation->_ref_chir}}
  </td>
	
  <td class="text">
    {{assign var=patient value=$sejour->_ref_patient}}
    <a href="?m=dPpmsi&amp;tab=vw_dossier_pmsi&amp;patient_id={{$sejour->patient_id}}&amp;sejour_id={{$sejour->_id}}">
    	<span onmouseover="ObjectTooltip.createEx(this, '{{$patient->_guid}}')">
	      {{mb_include module=patients template=inc_vw_ipp ipp=$patient->_IPP}}
	      {{$patient}}
    	</span> 
    </a>
  </td>

  <td>
    {{if $_operation->rank}}
      {{$_operation->time_operation|date_format:$conf.time}}
    {{else}}
      NP
    {{/if}}
  </td>
	
  <td class="text">
  	{{mb_include module=planningOp template=inc_vw_operation}}
  </td>

  <td>{{$_operation->_nb_files_docs+$_operation->_ref_sejour->_nb_files_docs}}</td>
  
	<td style="text-align: center">
    {{mb_value object=$_operation field=labo}}
  </td>
  <td style="text-align: center">
    {{mb_value object=$_operation field=anapath}}
  </td>
</tr>