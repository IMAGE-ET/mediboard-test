<script type="text/javascript">      
      
/*
if(window.opener){
  window.opener.PrescriptionEditor.refresh('{{$prescription->_id}}','{{$prescription->object_class}}');
}
*/

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

</script>

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
	    <th colspan="9">{{$category->_view}}</th>
	  </tr>
	  
	  <!-- Parcours des categories d'elements et de commentaires -->
	  {{foreach from=$lines_cat.element item=line_element}}
	    {{include file="inc_vw_line_element_elt.tpl" _line_element=$line_element}}
	  {{/foreach}}
	  {{foreach from=$lines_cat.comment item=line_comment}}
	    {{include file="inc_vw_line_comment_elt.tpl" _line_comment=$line_comment}}
	  {{/foreach}}
	
	  {{assign var=lines_element value=$lines_cat.element|@count}}
	  {{assign var=lines_comment value=$lines_cat.comment|@count}}
	  {{assign var=nb_lines value=$nb_lines+$lines_element+$lines_comment}}  
  {{/foreach}}
</table>

<script type="text/javascript">

Prescription.refreshTabHeader('div_{{$element}}','{{$nb_lines}}');

// Autocomplete
prepareForm(document.search{{$element}});
  
url = new Url();
url.setModuleAction("dPprescription", "httpreq_do_element_autocomplete");
url.addParam("category", "{{$element}}");
url.autoComplete("search{{$element}}_{{$element}}", "{{$element}}_auto_complete", {
    minChars: 3,
    updateElement: function(element) { updateFieldsElement(element, 'search{{$element}}', '{{$element}}') }
} );

</script>