<!-- Initialisation des variables -->
{{*mb_ternary var=perm_edit test=$_line_element->signee value="0" other="1"*}}

{{if ($_line_element->praticien_id == $app->user_id) && !$_line_element->signee}}
  {{assign var=perm_edit value="1"}}
{{else}}
  {{assign var=perm_edit value="0"}}
{{/if}}


{{if $_line_element->date_arret}}
  {{assign var=_date_fin value=$_line_element->date_arret}}
{{else}}
  {{assign var=_date_fin value=$_line_element->_fin}}
{{/if}}

{{assign var=line value=$_line_element}}
{{assign var=dosql value="do_prescription_line_element_aed"}}
{{assign var=div_refresh value=$element}}
{{assign var=typeDate value=$element}}

{{assign var=category value=$_line_element->_ref_element_prescription->_ref_category_prescription}}
<tbody class="hoverable">
  <!-- Header de la ligne d'element -->
  <tr>    
    <th class="{{if $_line_element->date_arret}}arretee{{else}}element{{/if}}" id="th_line_CPrescriptionLineElement_{{$_line_element->_id}}" colspan="8" 
        {{if $_date_fin && $_date_fin < $today}}style="background-image:url(images/icons/ray.gif); background-repeat:repeat;"{{/if}}>
     
      <div style="position: absolute">
        <!-- Formulaire ALD -->
        {{if !$_line_element->_protocole}}
          {{include file="../../dPprescription/templates/line/inc_vw_form_ald.tpl"}} 
	      {{/if}}
      </div>    
     
      <div class="div_signature">
        <!-- Affichage de la signature du praticien -->
        {{if ($_line_element->praticien_id != $app->user_id) && !$_line_element->_protocole}}
          {{include file="../../dPprescription/templates/line/inc_vw_signature_praticien.tpl"}}
        {{else}}
          {{$_line_element->_ref_praticien->_view}}    
        {{/if}}
        
        <!-- Affichage du formulaire de signature du praticien -->
		    {{if !$_line_element->_protocole}}  
			    {{include file="../../dPprescription/templates/line/inc_vw_form_signature_praticien.tpl"}}
	      {{/if}}
		    
	    </div>
	    
	    <!-- View de l'element -->
	    {{$_line_element->_ref_element_prescription->_view}}
	    
	    
	  </th>
	</tr>

  {{if $category->chapitre != "dmi"}}
  <tr>
    <td style="width: 25px" {{if $category->chapitre != "dmi"}}rowspan="3"{{/if}} >
      {{if $perm_edit}}
      <button type="button" class="trash notext" onclick="Prescription.delLineElement('{{$_line_element->_id}}','{{$element}}')">
        {{tr}}Delete{{/tr}}
      </button>
      {{/if}}
    </td>
    <!-- Gestion des dates -->
    {{if !$_line_element->_protocole}}
    <td colspan="2">
      {{include file="../../dPprescription/templates/line/inc_vw_dates.tpl"}}
      {{if $perm_edit}}
	      <script type="text/javascript">
	        prepareForm(document.forms["editDates-{{$typeDate}}-{{$line->_id}}"]);    
		      regFieldCalendar("editDates-{{$typeDate}}-{{$line->_id}}", "debut");
	        regFieldCalendar("editDates-{{$typeDate}}-{{$line->_id}}", "_fin");       
	      </script>
      {{/if}}
    </td>
    <td>
     {{if $category->chapitre != "dmi" && ($prescription->type == "sejour" || $prescription->type == "pre_admission")}}
        <div id="stop-CPrescriptionLineElement-{{$_line_element->_id}}" style="float: right">
          {{include file="../../dPprescription/templates/line/inc_vw_stop_line.tpl" object_class="CPrescriptionLineElement"}}
        </div>
     {{/if}}
    </td>
    {{/if}}
  </tr>
  {{if $category->chapitre != "dm"}}
  <tr>
    <td colspan="3">
      {{if $perm_edit}}
	      <table style="width: 100%">
	       <tr>
			    <td style="border:none; border-right: 1px solid #999; width:5%; text-align: left;">
			      {{include file="../../dPprescription/templates/line/inc_vw_add_posologies.tpl" type="Soin"}}
			    </td>
			    <td style="border:none">
			      <img src="images/icons/a_right.png" title="" alt="" />
			    </td>
				  <td style="border:none; text-align: left;">
			      <div id="prises-Soin{{$_line_element->_id}}">
			        <!-- Parcours des prises -->
			        {{include file="../../dPprescription/templates/line/inc_vw_prises_posologie.tpl" type="Soin"}}
			      </div>
			    </td>
	      </table>
      {{else}}
        <!-- Affichage des prises -->
        {{foreach from=$_line_element->_ref_prises item=prise}}
          {{$prise->_view}} ,
        {{foreachelse}}
          Aucune posologie
        {{/foreach}}
      {{/if}}
    </td>
  </tr>
  {{/if}}
  {{/if}}
  <tr>
    {{if $category->chapitre == "dmi"}}
    <td style="width: 25px">
      {{if $perm_edit}}
      <button type="button" class="trash notext" onclick="Prescription.delLineElement('{{$_line_element->_id}}','{{$element}}')">
        {{tr}}Delete{{/tr}}
      </button>
      {{/if}}
    </td>
    {{/if}}
    <td {{if $category->chapitre != "dmi"}}colspan="3"{{else}}colspan="6"{{/if}}>
      <div style="float: right">
        <!-- Formulaire de selection d'un executant -->
        {{include file="../../dPprescription/templates/line/inc_vw_form_executants.tpl"}}
      </div>
   
      <!-- Formulaire d'ajout de commentaire -->
      {{include file="../../dPprescription/templates/line/inc_vw_form_add_comment.tpl"}}
    </td>
    
  </tr>
</tbody>