<script type="text/javascript">      
     
// On vide toutes les valeurs du formulaire d'ajout d'element
var oForm = document.addLineElement;
oForm.prescription_line_element_id.value = "";
oForm.del.value = "0";
oForm.element_prescription_id.value = "";

// Preselection des executants
preselectExecutant = function(executant_id, category_id){
 $$('select.executant-'+category_id).each( function(select) {
   select.value = executant_id;
   select.onchange();
 })
}


changePraticienElt = function(praticien_id){
  var oFormAddLineElement = document.addLineElement;
  var oFormAddLineCommentElement = document.forms["addLineComment{{$element}}"];
  
  oFormAddLineElement.praticien_id.value = praticien_id;
  if(oFormAddLineCommentElement){
    oFormAddLineCommentElement.praticien_id.value = praticien_id;
  }
}


// On met à jour les valeurs de praticien_id
Main.add( function(){
  if(document.selPraticienLine){
	  changePraticienElt(document.selPraticienLine.praticien_id.value);
  }
} );

</script>

<!-- Ne pas donner la possibilite de signer les lignes d'un protocole -->
{{if $prescription->object_id && $is_praticien}}
<div style="float: right">
  <form name="valideAllLines-{{$element}}" method="post" action="">
    <input type="hidden" name="m" value="dPprescription" />
    <input type="hidden" name="dosql" value="do_valide_all_lines_aed" />
    <input type="hidden" name="prescription_id" value="{{$prescription->_id}}" />
    <input type="hidden" name="chapitre" value="{{$element}}" />
    <button class="tick" type="button" onclick="submitFormAjax(this.form, 'systemMsg')">
    	Signer les lignes "{{tr}}CCategoryPrescription.chapitre.{{$element}}{{/tr}}"
    </button>
  </form>
</div>
{{/if}}



<!-- Formulaire d'ajout de ligne d'elements et de commentaires -->
{{include file="inc_vw_form_addLine.tpl"}}

<table class="tbl">
  {{assign var=lines value=$prescription->_ref_lines_elements_comments.$element}}
  {{assign var=nb_lines value=0}}
  
  <!-- Parcours des elements de type $element -->
  {{foreach from=$lines item=lines_cat key=category_id}}
	  {{assign var=category value=$categories.$category_id}}
	  <tr>
	    <!-- Affichage de la categorie -->
	    <th class="title" colspan="9">{{$category->_view}}</th>
	  </tr>
	  
	  <!-- Parcours des categories d'elements et de commentaires -->
	  {{foreach from=$lines_cat.element item=line_element}}
	    {{include file="inc_vw_line_element_elt.tpl" _line_element=$line_element}}
	  {{/foreach}}
	  {{if $lines_cat.comment|@count}}
	  <tr>
	    <th colspan="8" class="element">Commentaires</th>
	  </tr>
	  
	  {{/if}}
	  {{foreach from=$lines_cat.comment item=line_comment}}
	    {{include file="inc_vw_line_comment_elt.tpl" _line_comment=$line_comment}}
	  {{/foreach}}
  {{/foreach}}
</table>

<script type="text/javascript">

Prescription.refreshTabHeader('div_{{$element}}','{{$prescription->_counts_by_chapitre.$element}}');

// Autocomplete
prepareForm(document.search{{$element}});
  
url = new Url();
url.setModuleAction("dPprescription", "httpreq_do_element_autocomplete");
url.addParam("category", "{{$element}}");
url.autoComplete("search{{$element}}_{{$element}}", "{{$element}}_auto_complete", {
  minChars: 2,
  updateElement: function(element) { updateFieldsElement(element, 'search{{$element}}', '{{$element}}') }
} );

</script>