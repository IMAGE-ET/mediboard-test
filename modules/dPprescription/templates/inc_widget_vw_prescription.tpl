<!-- 
<button type="button" class="print notext" onclick="Prescription.print('{{$prescription->_id}}')">
  Imprimer
</button>
 -->
{{assign var=med_element value=$prescription->_ref_lines_med_comments.med|@count}}
{{assign var=med_comment value=$prescription->_ref_lines_med_comments.comment|@count}}
{{assign var=med value=$med_element+$med_comment}}
<tr>

	  
  <td>
   {{if $prescription->type != "traitement"}}
   <a href="#{{$prescription->_id}}" onclick="PrescriptionEditor.popup('{{$prescription->_id}}','{{$object_id}}','{{$object_class}}','{{$praticien_id}}');">
   {{tr}}CPrescription.type.{{$prescription->type}}{{/tr}}
   </a>
   {{else}}
   {{tr}}CPrescription.type.{{$prescription->type}}{{/tr}}
   {{/if}}
  </td>
  <td>
    {{$prescription->_ref_praticien->_view}}
  </td>
  <td>
    {{$med}}
  </td>  
  {{include file="../../dPprescription/templates/inc_widget_vw_element.tpl" element="dmi"}}
  {{include file="../../dPprescription/templates/inc_widget_vw_element.tpl" element="anapath"}}
  {{include file="../../dPprescription/templates/inc_widget_vw_element.tpl" element="biologie"}}
  {{include file="../../dPprescription/templates/inc_widget_vw_element.tpl" element="imagerie"}}
  {{include file="../../dPprescription/templates/inc_widget_vw_element.tpl" element="consult"}}
  {{include file="../../dPprescription/templates/inc_widget_vw_element.tpl" element="kine"}}
  {{include file="../../dPprescription/templates/inc_widget_vw_element.tpl" element="soin"}}  
 
  
</tr>
