<script type="text/javascript">      

// On vide toutes les valeurs du formulaire d'ajout d'element
var oForm = document.addLineElement;
oForm.prescription_line_element_id.value = "";
oForm.del.value = "0";
oForm.element_prescription_id.value = "";

// On met à jour les valeurs de praticien_id
Main.add( function(){
  if(document.selPraticienLine){
	  changePraticienElt(document.selPraticienLine.praticien_id.value, '{{$element}}');
  }
  
  Prescription.refreshTabHeader('div_{{$element}}','{{$prescription->_counts_by_chapitre.$element}}');
  
  if(document.search{{$element}}){
    // Autocomplete
    prepareForm(document.search{{$element}});
      
    url = new Url();
    url.setModuleAction("dPprescription", "httpreq_do_element_autocomplete");
    url.addParam("category", "{{$element}}");
    url.autoComplete("search{{$element}}_{{$element}}", "{{$element}}_auto_complete", {
      minChars: 2,
      updateElement: function(element) { updateFieldsElement(element, 'search{{$element}}', '{{$element}}') }
    } );
  }
} );


{{if $prescription->_praticiens|@count}}
  if(document.selPratForPresc){
		 var praticiens = {{$prescription->_praticiens|smarty:nodefaults|escape:"htmlall"|@json}};
		 var chps = document.selPratForPresc.selPraticien;
		 chps.innerHTML = "";
		 chps.insert('<option value="">Tous</option>');
		 for(var prat in praticiens){
		   chps.insert('<option value='+prat+'>'+praticiens[prat]+'</option>');
		 }
		 var praticien_sortie_id = {{$praticien_sortie_id|json}};
		 $A(chps).each( function(option) {
		  option.selected = option.value==praticien_sortie_id;
		 });
	 }
{{/if}}



</script>


<table class="form">
  <tr>
    {{*if $prescription->_can_add_line*}}
      <th class="category">Nouvelle ligne</th>
    {{*/if*}}
    <th class="category">Actions</th>
  </tr>
  <tr>
    {{*if $prescription->_can_add_line*}}
    <td>
      {{include file="inc_vw_form_addLine.tpl"}}
    </td>
    {{*/if*}}
    <td>
		  {{if $prescription->object_id && is_array($prescription->_ref_lines_elements_comments) && array_key_exists($element, $prescription->_ref_lines_elements_comments)}}
		  <button class="{{if $readonly}}edit{{else}}lock{{/if}}" type="button" onclick="Prescription.reload('{{$prescription->_id}}', '', '{{$element}}', '', '{{$mode_pharma}}', null, {{if $readonly}}false{{else}}true{{/if}}, {{if $readonly}}false{{else}}true{{/if}});">
		    {{if $readonly}}Modification
		    {{else}}Lecture seule
		    {{/if}}
		  </button>
		  {{/if}}
		  
			{{if $readonly}}
			  <button class="lock" type="button" onclick="Prescription.reload('{{$prescription->_id}}', '', '{{$element}}', '', '{{$mode_pharma}}', null, true, {{if $lite}}false{{else}}true{{/if}});">
			    {{if $lite}}Vue complète
			    {{else}}Vue simplifiée
			    {{/if}}
			  </button>
			{{/if}}
			   
		  <!-- Ne pas donner la possibilite de signer les lignes d'un protocole -->
		  {{if $prescription->object_id && $is_praticien}}
		  <button class="tick" type="button" onclick="submitValideAllLines('{{$prescription->_id}}', '{{$element}}');">
		  	Signer les lignes "{{tr}}CCategoryPrescription.chapitre.{{$element}}{{/tr}}"
		  </button>
		  {{/if}}
    </td>
  </tr>
</table>


<!-- Formulaire d'ajout de ligne d'elements et de commentaires -->
{{if $lite && is_array($prescription->_ref_lines_elements_comments) && array_key_exists($element, $prescription->_ref_lines_elements_comments) && $readonly}}
 <table class="tbl">
   <th style="width:15%;">Libellé</th>
   <th style="width:10%;">Catégorie</th>
   <th style="width:10%;">Praticien</th>
   <th style="width:15%;">Début</th>
   <th style="width:10%;">Durée</th>
   <th style="width:20%;">Prises</th>
   <th style="width:10%;">Exécutant</th>
   <th style="width:10%">Emplacement</th>
 </table>
{{/if}}
	  
{{if is_array($prescription->_ref_lines_elements_comments) && array_key_exists($element, $prescription->_ref_lines_elements_comments)}}

  {{assign var=lines value=$prescription->_ref_lines_elements_comments.$element}}
  {{assign var=nb_lines value=0}}
  
  <!-- Parcours des elements de type $element -->
  {{foreach from=$lines item=lines_cat key=category_id}}
	  {{assign var=category value=$categories.$element.$category_id}}
	
	  <!-- Elements d'une categorie-->
	  <table class="tbl" id="elt_{{$category->_id}}">
	    {{if !$lite}}
      <tr><th class="title" colspan="9">{{$category->_view}}</th></tr>	  
      {{/if}}
	  </table>
    
	  <table class="tbl" id="elt_art_{{$category->_id}}"></table>
    
    <table class="tbl">
	  {{foreach from=$lines_cat.element item=line_element}}
	    {{if !$praticien_sortie_id || ($praticien_sortie_id == $line_element->praticien_id)}}
	      {{if $readonly}}
	        {{if $lite}}
	          {{include file="inc_vw_line_element_lite.tpl" _line_element=$line_element prescription_reelle=$prescription nodebug=true}}
	        {{else}}
            {{include file="inc_vw_line_element_readonly.tpl" _line_element=$line_element prescription_reelle=$prescription nodebug=true}}
          {{/if}}
        {{else}}
          {{include file="inc_vw_line_element_elt.tpl" _line_element=$line_element prescription_reelle=$prescription nodebug=true}}
	      {{/if}}
      {{/if}}
	  {{/foreach}}
	  </table>
	  
	  <!-- Commentaires d'une categorie -->
	  <table class="tbl">
  	  {{if $lines_cat.comment|@count}}
  	  {{if !$lite}}
  	  <tr>
  	    <th colspan="9" class="element">Commentaires</th>
  	  </tr>
  	  {{/if}}
  	  {{/if}}
  	  {{foreach from=$lines_cat.comment item=line_comment}}
  	    {{if !$praticien_sortie_id || ($praticien_sortie_id != $line_comment->praticien_id)}}
          {{if $readonly}}
            {{if $lite}}
              {{assign var=line value=$line_comment}}  
							 <tr>
							   <td style="width:15%;" class="text">
							     {{$line->commentaire}}
							   </td>
							   <td style="width:10%;" class="text">{{$category->_view}}</td>
							   <td style="width:10%;" class="text">
							   							     <!-- Affichage de la signature du praticien -->
							     {{if $line->_can_view_signature_praticien}}
							       {{include file="../../dPprescription/templates/line/inc_vw_signature_praticien.tpl"}}
							     {{elseif !$line->_protocole}}
							       {{$line->_ref_praticien->_view}}    
							     {{/if}}
							     </td>
							   <td style="width:15%;"></td>
							   <td style="width:10%;"></td>
							   <td style="width:20%;"></td>
							   
							   <td style="width: 10%;">
								   {{if $line->executant_prescription_line_id || $line->user_executant_id}}
								     {{$line->_ref_executant->_view}}
								   {{/if}}
							   </td>
							   <td style="width: 10%;">  

							   </td>
							</tr>
            {{else}}
              {{include file="inc_vw_line_comment_readonly.tpl" _line_comment=$line_comment prescription_reelle=$prescription nodebug=true}}
            {{/if}}
          {{else}}
            {{include file="inc_vw_line_comment_elt.tpl" _line_comment=$line_comment prescription_reelle=$prescription nodebug=true}}
  	      {{/if}}
        {{/if}}
  	  {{/foreach}}
	  </table>
	  {{/foreach}}
  {{else}}
  <div class="big-info"> 
     Il n'y a aucun élément de type "{{tr}}CCategoryPrescription.chapitre.{{$element}}{{/tr}}" dans cette prescription.
  </div>
  {{/if}}
  <br />
  {{if $prescription->object_id}}
  <!-- Affichage de l'historique -->
  <table class="tbl">
		{{foreach from=$historique key=type_prescription item=hist_prescription}}
		  {{if is_array($hist_prescription->_ref_lines_elements_comments) && array_key_exists($element, $hist_prescription->_ref_lines_elements_comments)}}
	    {{foreach from=$hist_prescription->_ref_lines_elements_comments.$element item=_hist_lines name="foreach_hist_elt"}}
		    {{if $smarty.foreach.foreach_hist_elt.first}}
			    <tr>
			      <th colspan="7" class="title">Historique {{tr}}CPrescription.type.{{$type_prescription}}{{/tr}}</th>
			    </tr>
	      {{/if}}
			  {{foreach from=$_hist_lines item=_hist_line}}
			    {{foreach from=$_hist_line item=_line}}
			      <tr>
			        <!-- Affichage d'une ligne de commentaire -->
			        {{if $_line->_class_name == "CPrescriptionLineComment"}}
			          <td colspan="4">{{$_line->commentaire}}</td>
			        {{else}}
			          {{assign var=chapitre value=$_line->_ref_element_prescription->_ref_category_prescription->chapitre}}
			          <!-- Affichage d'une ligne d'element -->
					      <td
					      {{if $chapitre == "dmi"}}
					       colspan="4"
					      {{/if}}><a href="#" onmouseover="ObjectTooltip.create(this, { params: { object_class: '{{$_line->_class_name}}', object_id: {{$_line->_id}} } })">{{$_line->_view}}</a></td>
					      
					      {{if $chapitre != "dmi"}}
						      {{if !$_line->fin}}
							    <td>{{mb_label object=$_line field="debut"}}: {{mb_value object=$_line field="debut"}}</td>
							    {{if $chapitre != "anapath" && $chapitre != "imagerie" && $chapitre != "consult"}}
							    <td>
							      {{mb_label object=$_line field="duree"}}: 
							        {{if $_line->duree && $_line->unite_duree}}
							          {{mb_value object=$_line field="duree"}}  
							          {{mb_value object=$_line field="unite_duree"}}
							        {{else}}
							        -
							        {{/if}}
							    </td>
							    <td>{{mb_label object=$_line field="_fin"}}: {{mb_value object=$_line field="_fin"}}</td>
							    {{/if}}
							    {{else}}
							    <td colspan="3">
							      {{mb_label object=$_line field="fin"}}: {{mb_value object=$_line field="fin"}}
							    </td>
						      {{/if}}
					      {{/if}}
				      {{/if}}
				        <td>Praticien: {{$_line->_ref_praticien->_view}}</td>
                <td>
				      		{{mb_label object=$_line field="ald"}}:
						      {{if $_line->ald}}Oui{{else}}Non{{/if}}
				        </td>
				        <td> Exécutant: {{if $_line->_ref_executant}}{{$_line->_ref_executant->_view}}{{else}}Aucun{{/if}}</td>
				    </tr>
			    {{/foreach}}
			  {{/foreach}}
			{{/foreach}}
			{{/if}}
		{{/foreach}}
	</table>
{{/if}}