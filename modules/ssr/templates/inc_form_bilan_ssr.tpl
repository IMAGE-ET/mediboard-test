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
			return onSubmitFormAjax(oForm, { onComplete: updateListLines });
			//updateModal();
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
	$V(oFormLineSSR.prescription_id, prescription_id, $V(oFormLineSSR.element_prescription_id) ? true : false);

	if(document.forms.applyProtocole){
	  var oFormProt = getForm("applyProtocole");
	  $V(oFormProt.prescription_id, prescription_id, $V(oFormProt.pack_protocole_id) ? true : false);
	}
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
	var form = getForm("Planification-CBilanSSR");
	if (form) {
	  $V(form.bilan_id, bilan_id);
	}
}
refreshFile = function(prot_id) {
	var url = new Url("ssr", "ajax_vw_list_files");
	url.addParam("object_id", prot_id.substr(5));
	url.addParam("object_class", "CPrescription");
	url.requestUpdate("files");
}

Main.add( function(){
  {{if $can_edit_prescription}}
	  {{foreach from=$categories item=_category}}
		  var url = new Url("dPprescription", "httpreq_do_element_autocomplete");
		  url.addParam("category", "{{$_category->chapitre}}");
			url.addParam("category_id", "{{$_category->_id}}");
		  url.autoComplete(getForm('search_{{$_category->_guid}}').libelle, "{{$_category->_guid}}_auto_complete", {
			  dropdown: true,
		    minChars: 2,
				updateElement: function(element) { updateFieldsElementSSR(element, getForm('search_{{$_category->_guid}}'), '{{$_category->_id}}') }
		  } );
	  {{/foreach}}
	{{/if}}
	
  var oFormProtocole = getForm("applyProtocole");
  if(oFormProtocole){
    var url = new Url("dPprescription", "httpreq_vw_select_protocole");
    var autocompleter = url.autoComplete(oFormProtocole.libelle_protocole, "protocole_auto_complete", {
      dropdown: true,
      minChars: 2,
      valueElement: oFormProtocole.elements.pack_protocole_id,
      updateElement: function(selectedElement) {
        var node = $(selectedElement).down('.view');
        $V(oFormProtocole.libelle_protocole, node.innerHTML.replace("&lt;", "<").replace("&gt;",">"));
        if (autocompleter.options.afterUpdateElement)
          autocompleter.options.afterUpdateElement(autocompleter.element, selectedElement);
      },
			callback: 
        function(input, queryString){
          return (queryString + "&prescription_id={{$prescription_SSR->_id}}&praticien_id={{$app->user_id}}"); 
        }
    } );  
  }
} );

submitProtocole = function(){
  var oForm = getForm("applyProtocole");
  return onSubmitFormAjax(oForm, { onComplete:  refreshFormBilanSSR });
}

refreshFormBilanSSR = function(){
  var url = new Url("ssr", "ajax_form_bilan_ssr");
	url.addParam("sejour_id", "{{$sejour->_id}}");
	url.requestUpdate("bilan");
}

refreshAfterDuplicate = function(line_id){
  updateListLines(null, "{{$prescription_SSR->_id}}", line_id);
	$V(getForm("addLineSSR").callback, "");
}

duplicateSSRLine = function(element_prescription_id, category_id){
  var oForm = getForm("addLineSSR");
	$V(oForm._category_id, category_id);
	$V(oForm.element_prescription_id, element_prescription_id);
	$V(oForm.callback, "refreshAfterDuplicate");
	return onSubmitFormAjax(oForm);
}


</script>

<div id="modal_SSR" style="display: none;"></div>

{{if "dPprescription"|module_active}}
{{mb_script module="dPprescription" script="prescription"}}
{{/if}}

<!-- Formulaire de creation de lignes de prescription -->
<form action="?" method="post" name="addLineSSR" onsubmit="return checkForm(this);">
  <input type="hidden" name="m" value="dPprescription" />
  <input type="hidden" name="dosql" value="do_prescription_line_element_aed" />
  <input type="hidden" name="prescription_line_element_id" value=""/>
  <input type="hidden" name="del" value="0" />
  <input type="hidden" name="prescription_id" value="{{$prescription_SSR->_id}}" 
	       onchange="return onSubmitFormAjax(this.form, { 
				   onComplete: function(){ 
					   if(getForm('searchElement')){ 
						   refreshFormBilanSSR();
						} else { 
						  updateListLines(); 
						} 
					}
				 } );"/>											
				 
  <input type="hidden" name="praticien_id" value="{{$app->user_id}}" />
  <input type="hidden" name="creator_id" value="{{$app->user_id}}" />
  <input type="hidden" name="element_prescription_id" value="" />
	<input type="hidden" name="debut" value="current" />
	<input type="hidden" name="callback" value="" />
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
				{{if $app->_ref_user->isPraticien() || $can->admin}}
				  <tr>
					  <td colspan="2">
					  	<form name="applyProtocole" method="post" action="?" onsubmit="if(!this.prescription_id.value){ return onSubmitFormAjax(getForm('addPrescriptionSSR'))} else { return submitProtocole() };">

				        <table>
				          <tr>
				            <td>
				              <input type="hidden" name="m" value="dPprescription" />
                      <input type="hidden" name="dosql" value="do_apply_protocole_aed" />
                      <input type="hidden" name="del" value="0" />
                      <input type="hidden" name="prescription_id" value="{{$prescription_SSR->_id}}" onchange="this.form.onsubmit();"/>
                      <input type="hidden" name="praticien_id" value="{{$app->user_id}}" />
                      <input type="hidden" name="pratSel_id" value="" />
                      <input type="hidden" name="_active" value="1" />
                      
                     <input type="hidden" name="pack_protocole_id" value="" onchange="refreshFile(this.value)"/>
				            </td>
				          </tr>
				          <tr>
				            <td>
				              <input type="text" name="libelle_protocole" value="&mdash; Choisir un protocole" class="autocomplete" style="font-weight: bold; font-size: 1.3em; width: 200px;"/>
                      <div style="display:none; width: 350px;" class="autocomplete" id="protocole_auto_complete"></div>
				            </td>
				            <td>
                      <div id="files" style="float:left"> {{mb_include module="ssr" template="inc_vw_list_files" count_object=0 }}</div>
                    </td>
                    <td>
                      <button type="submit" class="submit">Appliquer</button>
                    </td>
                  </tr>
                </table>
              </form>
					  </td>
			    </tr>
				{{else}}
				<tr>
					<td colspan="2">
						<script type="text/javascript">
							Main.add( function(){
							    var url = new Url("ssr", "ajax_autocomplete_prescription_executant");
							    url.autoComplete(getForm("searchElement").libelle, "searchElement_auto_complete", {
							      minChars: 2,
										dropdown: true,
							      updateElement: function(selected) { 
										  Element.cleanWhitespace(selected);
											var dn = selected.childNodes;
										  var element_id = dn[0].firstChild.nodeValue;
										
                      var oForm = getForm("addLineSSR");
											$V(oForm.element_prescription_id, element_id);
                      
											// si la prescription n'est pas encore cr��, on l'a cr�e
											if(!$V(oForm.prescription_id)){
											  var oFormAddPrescription = getForm('addPrescriptionSSR');
												onSubmitFormAjax(oFormAddPrescription);
											} else {
											  onSubmitFormAjax(oForm, { onComplete:  refreshFormBilanSSR });
                      }
										}
							    } );
						  } );
						</script>	
						
						<form name="searchElement">	
							<input type="text" name="libelle" value="" class="autocomplete"  style="font-weight: bold; font-size: 1.3em; width: 300px;"/>
              <input type="hidden" name="element_id" onchange="" />
              <div style="display:none;" class="autocomplete" id="searchElement_auto_complete"></div>
						</form>
					</td>
				</tr>	
				{{/if}}
				<tr>
				  <th class="title" colspan="2">Prescription</th>
				</tr>
		    {{foreach from=$categories item=_category}}
		      {{assign var=category_id value=$_category->_id}}
		      <tr>
		        {{if $can_edit_prescription}}
	            <th class="narrow">
			        	<strong onmouseover="ObjectTooltip.createEx(this, '{{$_category->_guid}}')">{{$_category}}</strong>
						  </th>
			        <td>
			        	<form name="search_{{$_category->_guid}}" action="?" method="post">
			            <input type="text" name="libelle" value="" class="autocomplete" />
			            <div style="display:none;" class="autocomplete" id="{{$_category->_guid}}_auto_complete"></div>
			          </form>
							</td>
						{{else}}
						  <td></td>
							<th style="text-align: left;">
							  <strong>
						    <strong onmouseover="ObjectTooltip.createEx(this, '{{$_category->_guid}}')">{{$_category}}</strong>
								</strong>
              </th>
						{{/if}}
		      </tr>
					<tbody  id="lines-{{$category_id}}">
						{{assign var=full_line_id value=""}}
            {{include file="inc_list_lines.tpl" nodebug=true}}
					</tbody>
			  {{foreachelse}}
        <tr>
          <td colspan="2">
          	<div class="small-info">Aucune prescription</div>
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
						<td>
              <fieldset>
                <legend>{{mb_label object=$bilan field=entree}}</legend>
                {{mb_field object=$bilan field=entree rows=6 onblur="this.form.onsubmit()" form="Edit-CBilanSSR"}}
              </fieldset>
  	        </td>
					</tr>

          <tr>
            <td>
              <fieldset>
                <legend>{{mb_label object=$bilan field=sortie}}</legend>
                {{mb_field object=$bilan field=sortie rows=6 onblur="this.form.onsubmit()" form="Edit-CBilanSSR"}}
              </fieldset>
            </td>
          </tr>

			  </table>
			</form>

      {{if $can->admin && $bilan->_id}}
      <hr />
      <form name="Planification-CBilanSSR" action="?m={{$m}}" method="post" onsubmit="return checkForm(this);">
        <input type="hidden" name="m" value="ssr" />
        <input type="hidden" name="dosql" value="do_bilan_ssr_aed" />
        <input type="hidden" name="del" value="0" />
        {{mb_key object=$bilan}}
        {{mb_field object=$bilan field=sejour_id hidden=1}}
        {{mb_field object=$bilan field=planification hidden=1}}

        <table class="form">

          <tr>
            <td class="button">
            	{{if $bilan->planification}} 
              <button type="button" class="cancel" onclick="$V(this.form.planification, '0'); this.form.submit();">
                {{tr}}CBilanSSR-planification-turn-off{{/tr}}
              </button>
            	{{else}}
              <button type="button" class="change" onclick="$V(this.form.planification, '1'); this.form.submit();">
                {{tr}}CBilanSSR-planification-turn-on{{/tr}}
              </button>
            	{{/if}}
						</td>
          </tr>

        </table>
      </form>
      {{/if}}
			
			<!-- Affichage des sejours SSR du patient -->
      <div id="sejours_ssr"></div>

    </td>
  </tr>
</table>