<!-- Initialisation des variables -->
{{mb_ternary var=perm_edit test=$_line_element->signee value="0" other="1"}}
{{assign var=line value=$_line_element}}
{{assign var=dosql value="do_prescription_line_element_aed"}}
{{assign var=div_refresh value=$element}}
{{assign var=typeDate value="Soin"}}

{{assign var=category value=$_line_element->_ref_element_prescription->_ref_category_prescription}}
<tbody class="hoverable">
  <!-- Header de la ligne d'element -->
  <tr>    
    <th id="th_line_CPrescriptionLineElement_{{$_line_element->_id}}" colspan="8" style="{{if $_line_element->date_arret}}background-color:#aaa{{/if}}">
      <div style="float: left">
        <!-- Formulaire ALD -->
        {{if !$_line_element->_protocole}}
          {{include file="../../dPprescription/templates/line/inc_vw_form_ald.tpl"}} 
	      {{/if}}
      </div>     
      <div style="float: right">
        <!-- Affichage de la signature du praticien -->
		    {{if $_line_element->_ref_praticien->_id}}
		        {{$_line_element->_ref_praticien->_view}}
		        {{if !$_line_element->_protocole}}  
			        {{include file="../../dPprescription/templates/line/inc_vw_form_signature_praticien.tpl"}}
	          {{/if}}
		      {{/if}}
	    </div>
	    <!-- View de l'element -->
	    {{$_line_element->_ref_element_prescription->_view}}
	  </th>
	</tr>
  <!-- Ligne affichée seulement dans le cas des soins -->
  {{if $category->chapitre == "soin"}}
  <tr>
    <td style="width: 25px" {{if $category->chapitre == "soin"}}rowspan="3"{{/if}} >
      {{if $perm_edit}}
      <button type="button" class="trash notext" onclick="Prescription.delLineElement('{{$_line_element->_id}}','{{$element}}')">
        {{tr}}Delete{{/tr}}
      </button>
      {{/if}}
    </td>
    <!-- Gestion des dates -->
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
     {{if $category->chapitre == "soin" && ($prescription->type == "sejour" || $prescription->type == "pre_admission")}}
        <div id="stop-CPrescriptionLineElement-{{$_line_element->_id}}" style="float: right">
          {{include file="../../dPprescription/templates/line/inc_vw_stop_line.tpl" object_class="CPrescriptionLineElement"}}
        </div>
     {{/if}}
    </td>
  </tr>
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
        {{/foreach}}
      {{/if}}
    </td>
  </tr>
  {{/if}}
  <tr>
    {{if $category->chapitre != "soin"}}
    <td style="width: 25px">
      {{if $perm_edit}}
      <button type="button" class="trash notext" onclick="Prescription.delLineElement('{{$_line_element->_id}}','{{$element}}')">
        {{tr}}Delete{{/tr}}
      </button>
      {{/if}}
    </td>
    {{/if}}
    <td {{if $category->chapitre == "soin"}}colspan="3"{{else}}colspan="6"{{/if}}>
      <div style="float: right">
        <!-- Formulaire de selection d'un executant -->
        {{include file="../../dPprescription/templates/line/inc_vw_form_executants.tpl"}}
      </div>
   
      <!-- Formulaire d'ajout de commentaire -->
      {{include file="../../dPprescription/templates/line/inc_vw_form_add_comment.tpl"}}
    </td>
    
  </tr>
</tbody>