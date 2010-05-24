<script type="text/javascript">

modalWindow = null;

updateFieldsElementSSR = function(selected, oFormElement, category_id) {
  Element.cleanWhitespace(selected);
  var dn = selected.childNodes;
  
	if(dn[0].className != 'informal'){
		// On vide l'autocomplete
		$V(oFormElement.libelle, '');
	  
		// On remplit la categorie et l'element_id dans le formulaire de creation de ligne
		var oForm = getForm("addLineSSR");
		$V(oForm._category_id, category_id);
		
		// Si la prescription existe, creation de la ligne
		if($V(oForm.prescription_id)){
		  $V(oForm.element_prescription_id, dn[0].firstChild.nodeValue);
			updateModal();
		}
	  // Sinon, creation de la prescription
	  else {
		  $V(oForm.element_prescription_id, dn[0].firstChild.nodeValue, false);
	    var oFormPrescriptionSSR = getForm("addPrescriptionSSR");
	    return onSubmitFormAjax(oFormPrescriptionSSR); 
	  }
	}
}

updateFormLine = function(prescription_id){
  var oFormLineSSR = getForm("addLineSSR");
	$V(oFormLineSSR.prescription_id, prescription_id);
}

updateListLines = function(category_id, prescription_id, full_line_id){
  var oFormLine = getForm("addLineSSR");
	
	_category_id = category_id ? category_id : $V(oFormLine._category_id);
	_prescription_id = prescription_id ? prescription_id : $V(oFormLine.prescription_id);
  var url = new Url;
	url.setModuleAction("ssr", "ajax_vw_list_lines");
	url.addParam("category_id", _category_id);
	url.addParam("prescription_id", _prescription_id);
	url.addParam("full_line_id", full_line_id);
	url.requestUpdate("lines-"+_category_id);
}

viewModal = function(){
  Element.cleanWhitespace($('modal_SSR'));
  // Si la modale contient du texte, on l'affiche
	if($('modal_SSR').innerHTML != ''){
	  modalWindow = modal($('modal_SSR'), {
	    className: 'modal'
	  });
	} 
	// Sinon, on submit le formulaire de creation de ligne
	else {
	  return onSubmitFormAjax(getForm('addLineSSR'), { onComplete: updateListLines  });
	}
}

updateModal = function(){
  var oForm = getForm("addLineSSR");
  var url = new Url;
	url.setModuleAction("ssr", "ajax_vw_modal");
	url.addParam("category_id", $V(oForm._category_id));
	url.addParam("element_prescription_id", $V(oForm.element_prescription_id));
	url.addParam("prescription_id", $V(oForm.prescription_id));
	url.requestUpdate("modal_SSR", { onComplete: viewModal } );
}

updateBilanId = function(bilan_id){
  $V(getForm("Edit-CBilanSSR").bilan_id, bilan_id);
}

Main.add( function(){
  {{foreach from=$categories item=_category}}
	  var url = new Url("dPprescription", "httpreq_do_element_autocomplete");
	  url.addParam("category", "{{$_category->chapitre}}");
		url.addParam("category_id", "{{$_category->_id}}");
	  url.autoComplete("search_{{$_category->_guid}}_libelle", "{{$_category->_guid}}_auto_complete", {
		  dropdown: true,
	    minChars: 2,
			updateElement: function(element) { updateFieldsElementSSR(element, getForm('search_{{$_category->_guid}}'), '{{$_category->_id}}') }
	  } );
  {{/foreach}}
	
  var oFormBilanSSR = getForm("Edit-CBilanSSR");
	
  new AideSaisie.AutoComplete(oFormBilanSSR.entree, {
    objectClass: "CBilanSSR", 
    userId: "{{$app->user_id}}"
  });
  
  new AideSaisie.AutoComplete(oFormBilanSSR.sortie, {
    objectClass: "CBilanSSR", 
    userId: "{{$app->user_id}}"
  });
} );

</script>

<div id="modal_SSR" style="display: none;"></div>

{{mb_include_script module="dPprescription" script="prescription"}}

<!-- Formulaire de creation de lignes de prescription -->
<form action="?" method="post" name="addLineSSR" onsubmit="return checkForm(this);">
  <input type="hidden" name="m" value="dPprescription" />
  <input type="hidden" name="dosql" value="do_prescription_line_element_aed" />
  <input type="hidden" name="prescription_line_element_id" value=""/>
  <input type="hidden" name="del" value="0" />
  <input type="hidden" name="prescription_id" value="{{$prescription_SSR->_id}}" onchange="return onSubmitFormAjax(this.form, { onComplete: updateListLines } );"/>											
  <input type="hidden" name="praticien_id" value="{{$app->user_id}}" />
  <input type="hidden" name="creator_id" value="{{$app->user_id}}" />
  <input type="hidden" name="element_prescription_id" value="" />
	<input type="hidden" name="debut" value="" />
	<input type="hidden" name="_category_id" value=""/>
</form>

<!-- Formulaire de modification de ligne -->
<form action="?" method="post" name="editLine">
  <input type="hidden" name="m" value="dPprescription" />
  <input type="hidden" name="dosql" value="do_prescription_line_element_aed" />
  <input type="hidden" name="del" value="0" />
  <input type="hidden" name="prescription_line_element_id" value=""/>
	<input type="hidden" name="date_arret" value=""/>
</form>	

<!-- Formulaire d'ajout de prescription -->
<form action="?" method="post" name="addPrescriptionSSR" onsubmit="return checkForm(this);">
	<input type="hidden" name="m" value="dPprescription" />
	<input type="hidden" name="dosql" value="do_prescription_aed" />
	<input type="hidden" name="del" value="0" />
	<input type="hidden" name="prescription_id" value=""/>
  <input type="hidden" name="object_id" value="{{$sejour->_id}}" />
  <input type="hidden" name="object_class" value="CSejour" />
  <input type="hidden" name="type" value="sejour" />
	<input type="hidden" name="callback" value="updateFormLine" />
</form>

<table class="main">
	<tr>
		<td style="width: 60%">
			<table class="form">
				<tr>
				  <th class="title" colspan="2">Prescription</th>
				</tr>
		    {{foreach from=$categories item=_category}}
		      {{assign var=category_id value=$_category->_id}}
		      <tr>
		        <th style="width: 1%;">
		        	<span onmouseover="ObjectTooltip.createEx(this, '{{$_category->_guid}}')">{{$_category->_view}}</span>
					  </th>
		        <td>
		          <form name="search_{{$_category->_guid}}" action="?">
		            <input type="text" name="libelle" value="" class="autocomplete" />
		            <div style="display:none;" class="autocomplete" id="{{$_category->_guid}}_auto_complete"></div>
		          </form>
		        </td>
		      </tr>
					<tbody  id="lines-{{$category_id}}">
						{{assign var=full_line_id value=""}}
              {{include file="inc_list_lines.tpl" nodebug=true}}
					</tbody>
			  {{foreachelse}}
        <tr>
          <td colspan="2">
          	<div class="small-info">Ce chapitre ne contient aucune catégorie</div>
          </td>
        </tr>
				{{/foreach}}
			</table>
		</td>
		
    <td>
    	<form name="Edit-CBilanSSR" action="?m={{$m}}" method="post" onsubmit="return onSubmitFormAjax(this);">
			  <input type="hidden" name="m" value="ssr" />
			  <input type="hidden" name="dosql" value="do_bilan_ssr_aed" />
			  <input type="hidden" name="del" value="0" />
				<input type="hidden" name="callback" value="updateBilanId" />
				{{mb_key object=$bilan}}
        {{mb_field object=$bilan field=sejour_id hidden=1}}
	    	<table class="form">
          <tr>
            <th class="title" style="width: 50%">{{tr}}CBilanSSR{{/tr}}</th>
          </tr>
	    	  <tr>
				    <th class="category">{{mb_label object=$bilan field=entree}}</th>
				  </tr>
					<tr>
					<td colspan="2">
	          {{mb_field object=$bilan field=entree onblur="this.form.onsubmit()"}}
	        </td>
					</tr>
	        <tr>
	          <th colspan="2" class="category">{{mb_label object=$bilan field=sortie}}</th>
	        </tr>			
				  <tr>
				    <td colspan="2">
				      {{mb_field object=$bilan field=sortie onblur="this.form.onsubmit()"}}
				    </td> 
				  </tr>
					<tr>
			      <td class="button" colspan="2">
			        <button class="submit" type="button">
			          {{tr}}Save{{/tr}}
			        </button>
			      </td>
			    </tr>
			  </table>
			</form>
    </td>
  </tr>
</table>