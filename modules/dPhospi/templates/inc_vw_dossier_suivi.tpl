<script type="text/javascript">

delCibleTransmission = function() {
  var oDiv = $('cibleTrans');
  if(!oDiv) return;
  var oForm = document.forms['editTrans'];
  $V(oForm.object_class, '');
  $V(oForm.object_id, '');
  $V(oForm.libelle_ATC, '');
  oDiv.innerHTML = "";
}

showListTransmissions = function(page, total) {
  $$("div.list_trans").invoke("hide");
  $("list_"+page).show();
  if (!page){
    page = 0;
  }
  var url = new Url("system", "ajax_pagination");
  
  if (total){
    url.addParam("total",total);
  }
  url.addParam("step",'{{$page_step}}');
  url.addParam("page",page);
  url.addParam("change_page","showListTransmissions");
  url.requestUpdate("pagination");
}

function updateFieldsCible(selected) {
  var oForm = document.forms['editTrans'];
  Element.cleanWhitespace(selected);
  if(isNaN(selected.id)){
    $V(oForm.libelle_ATC, selected.id);
  } else {
    $V(oForm.object_id, selected.id);
    $V(oForm.object_class, 'CCategoryPrescription');  
  }
  $('cibleTrans').update(selected.innerHTML.stripTags()).show();
  $V(oForm.cible, '');
}

// Submit d'une ligne d'element
submitLineElement = function(){
  // Formulaire de creation de ligne
  var oFormLineElementSuivi = getForm('addLineElementSuivi');

  // Formulaire autocomplete
  var oFormLineSuivi = getForm('addLineSuivi');
  $V(oFormLineElementSuivi.commentaire, $V(oFormLineSuivi.commentaire));
	
	// Si la prescription de sejour n'existe pas
	if(!$V(oFormLineElementSuivi.prescription_id)){
	  var oFormPrescription = getForm("addPrescriptionSuiviSoins");
		return onSubmitFormAjax(oFormPrescription);
	} else {
    return onSubmitFormAjax(oFormLineElementSuivi, { onComplete: loadSuivi.curry('{{$sejour->_id}}') } );
	}
}

// Submit d'une ligne de commentaire
submitLineComment = function(){
  var oFormLineCommentSuivi = getForm('addLineCommentMedSuiviSoins');

  // Si la prescription de sejour n'existe pas
  if(!$V(oFormLineCommentSuivi.prescription_id)){
    var oFormPrescription = getForm("addPrescriptionSuiviSoins");
    return onSubmitFormAjax(oFormPrescription);
  } else {
    return onSubmitFormAjax(oFormLineCommentSuivi, { onComplete: loadSuivi.curry('{{$sejour->_id}}') } );
  }	
}

submitProtocoleSuiviSoins = function(){
  var oFormProtocoleSuiviSoins = getForm("applyProtocoleSuiviSoins");
	// Si la prescription de sejour n'existe pas
  if(!$V(oFormProtocoleSuiviSoins.prescription_id)){
    var oFormPrescription = getForm("addPrescriptionSuiviSoins");
    return onSubmitFormAjax(oFormPrescription);
  } else {
    return onSubmitFormAjax(oFormProtocoleSuiviSoins, { onComplete: loadSuivi.curry('{{$sejour->_id}}') } );
  }
}

updatePrescriptionId = function(prescription_id){
  // Ligne d'element
	var oFormLineElementSuivi = getForm('addLineElementSuivi');
  $V(oFormLineElementSuivi.prescription_id, prescription_id);
	
	// Ligne de commentaire
	var oFormLineCommentSuivi = getForm('addLineCommentMedSuiviSoins');
	$V(oFormLineCommentSuivi.prescription_id, prescription_id);
	
	var oFormProtocoleSuiviSoins = getForm("applyProtocoleSuiviSoins");
	$V(oFormProtocoleSuiviSoins.prescription_id, prescription_id);
  
	// Selection du formulaire a envoyer (suivant celui qui est rempli)
	if($V(oFormLineElementSuivi.element_prescription_id)){
	  var oForm = oFormLineElementSuivi;
	}
	if($V(oFormLineCommentSuivi.commentaire)){
    var oForm = oFormLineCommentSuivi;
  }
  if($V(oFormProtocoleSuiviSoins.pack_protocole_id)){
    var oForm = oFormProtocoleSuiviSoins;
  }
  
  return onSubmitFormAjax(oForm, { onComplete: loadSuivi.curry('{{$sejour->_id}}') } );
}

refreshLineSuivi = function(line_guid, action){
  var url = new Url("dPhospi", "httpreq_vw_line_suivi");
	url.addParam("line_guid", line_guid);
	url.addParam("action", action);
	url.requestUpdate(line_guid);
}

addTransmissionAdm = function(line_id, line_class){
  var oFormTransmission = getForm("editTrans");
	$V(oFormTransmission.object_id, line_id);
	$V(oFormTransmission.object_class, line_class);
  $V(oFormTransmission.text, "Réalisé");
	return onSubmitFormAjax(oFormTransmission, { onComplete: loadSuivi.curry('{{$sejour->_id}}') } );
}

highlightTransmissions = function(cible_guid){
  $('transmissions').select("."+cible_guid).each(function(e){
    e.down('.libelle_trans').addClassName('highlight');
  });
}

removeHighlightTransmissions = function(){
 $('transmissions').select('.highlight').invoke("removeClassName", "highlight");
}

Main.add(function () {
  var url = new Url("dPprescription", "httpreq_cible_autocomplete");
  url.autoComplete("editTrans_cible", "cible_auto_complete", {
    minChars: 3,
    updateElement: updateFieldsCible
  } );
	if({{$count_trans}} > 0) {
	  showListTransmissions(0, {{$count_trans}});
	}
	
  var options = {
    minHours: '{{$hour-1}}',
    maxHours: '{{$hour+1}}'
  };
	
	var dates = {};
  dates.limit = {
    start: '{{$date}}',
    stop: '{{$date}}'
  };
  Calendar.regField(getForm("editTrans").date, dates, options);
	
	// Initialisation du champ dates
	$("editTrans_date_da").value = "Heure actuelle";
	$V(getForm("editTrans").date, "now");
	
	var oFormTrans = getForm("editTrans");
  new AideSaisie.AutoComplete(oFormTrans.text, {
            objectClass: "CTransmissionMedicale", 
            //contextUserId: "{{$sejour->_ref_praticien->_id}}",
            //contextUserView: "{{$sejour->_ref_praticien->_view}}",
            timestamp: "{{$dPconfig.dPcompteRendu.CCompteRendu.timestamp}}",
						dependField1: oFormTrans.type,
						//dependField2: oFormTrans.degre,
      			validateOnBlur:0
          });
					
					
  var oFormObs = getForm("editObs");
	if(oFormObs){
	  new AideSaisie.AutoComplete(oFormObs.text, {
	            objectClass: "CObservationMedicale", 
	            //contextUserId: "{{$sejour->_ref_praticien->_id}}",
              //contextUserView: "{{$sejour->_ref_praticien->_view}}",
              timestamp: "{{$dPconfig.dPcompteRendu.CCompteRendu.timestamp}}",
	            dependField1: oFormObs.degre,
	            dependField2: '',
	            validateOnBlur:0
	          });
	}
	
	if($('form-prescription-suivi-soins')){
	  var url = new Url("dPprescription", "httpreq_do_element_autocomplete");
    url.autoComplete("addLineSuivi_libelle", "line_auto_complete", {
      minChars: 2,
			dropdown: true,
      updateElement: function(selected) {
			  var oFormAddLineSuivi = getForm('addLineElementSuivi');
			  Element.cleanWhitespace(selected);
			  var dn = selected.childNodes;
			  $V(oFormAddLineSuivi.element_prescription_id, dn[0].firstChild.nodeValue);
				$V(getForm('addLineSuivi').libelle,dn[2].innerHTML.stripTags());
			}
    } );
  }	
	
	// Chargement de l'autocomplete des protocoles
  var oFormProtocole = getForm("applyProtocoleSuiviSoins");
	if(oFormProtocole){
    var url = new Url("dPprescription", "httpreq_vw_select_protocole");
    var autocompleter = url.autoComplete(oFormProtocole.libelle_protocole, "protocole_auto_complete_suivi_soins", {
      dropdown: true,
      minChars: 1,
      valueElement: oFormProtocole.elements.pack_protocole_id,
      updateElement: function(selectedElement) {
        var node = $(selectedElement).down('.view');
        $V($("applyProtocoleSuiviSoins_libelle_protocole"), (node.innerHTML).replace("&lt;", "<").replace("&gt;",">"));
        if (autocompleter.options.afterUpdateElement)
          autocompleter.options.afterUpdateElement(autocompleter.element, selectedElement);
      },
			callback:
        function(input, queryString){
          return (queryString + "&praticien_id={{$app->user_id}}");
        }
    } );
  }
	
	
	Control.Tabs.create('tabs-suivi-soins', true);
});

</script>

<button class="add" onclick="$('form_trans').toggle(); this.toggleClassName('add').toggleClassName('remove');">Formulaire de suivi de soins</button>

<div id="form_trans" {{if !$app->user_prefs.show_transmissions_form}}style="display: none;"{{/if}}>

<ul class="control_tabs small" id="tabs-suivi-soins">
	<li><a href="#form-transmissions">Transmissions</a></li>
  {{if $isPraticien}}
    <li><a href="#form-observations">Observations</a></li>
 
	  {{if $sejour->type == "urg" && $dPconfig.dPprescription.CPrescription.prescription_suivi_soins}}
	    <li><a href="#form-prescription-suivi-soins">Prescriptions</a></li>
	  {{/if}}
  {{/if}}
</ul>
<hr class="control_tabs" /> 
 
<table class="form" style="height: 120px;">
  <tr>
  	{{if $isPraticien}}
		  <td id="form-observations">
		    <form name="editObs" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">
		      <input type="hidden" name="dosql" value="do_observation_aed" />
		      <input type="hidden" name="del" value="0" />
		      <input type="hidden" name="m" value="dPhospi" />
		      <input type="hidden" name="sejour_id" value="{{$sejour->_id}}" />
		      <input type="hidden" name="user_id" value="{{$user->_id}}" />
		      <input type="hidden" name="date" value="now" /> 
		      
		      {{mb_field object=$observation field="degre"}}
		      <br />
		      {{mb_field object=$observation field="text"}}
		      <button type="button" class="add" onclick="submitSuivi(this.form)">{{tr}}Add{{/tr}}</button> 
		    </form>
		  </td>     
    {{/if}}
		<td style="white-space: normal;" id="form-transmissions">
      Cible
      <form name="editTrans" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">
	      <input name="cible" type="text" value="" class="autocomplete" />
	      <div style="display:none; width: 350px; white-space: normal;" class="autocomplete" id="cible_auto_complete"></div>
	      <div id="cibleTrans" style="font-style: italic;" onclick="delCibleTransmission();"></div>
	      <input type="hidden" name="dosql" value="do_transmission_aed" />
	      <input type="hidden" name="del" value="0" />
	      <input type="hidden" name="m" value="dPhospi" />
	      <input type="hidden" name="object_class" value="" onchange="$V(this.form.libelle_ATC, '', false);"/>
	      <input type="hidden" name="object_id" value="" />
	      <input type="hidden" name="libelle_ATC" value=""  onchange="$V(this.form.object_class, '', false); $V(this.form.object_id, '', false);"/>
	      <input type="hidden" name="sejour_id" value="{{$sejour->_id}}" />
	      <input type="hidden" name="user_id" value="{{$user->_id}}" />

				{{mb_field object=$transmission field="date"}}
        {{mb_field object=$transmission field="degre"}}
        {{mb_field object=$transmission field="type" typeEnum=radio}}
        <button type="button" class="cancel notext" onclick="$V(this.form.type, null);"></button>
        <br />
				{{mb_field object=$transmission field="text"}}
	      <button type="button" class="add" onclick="submitSuivi(this.form)">{{tr}}Add{{/tr}}</button>
      </form>
    </td>
		{{if $sejour->type == "urg" && $dPconfig.dPprescription.CPrescription.prescription_suivi_soins && $isPraticien}}
		<td id="form-prescription-suivi-soins">
			
			  <!-- Formulaire d'ajout de prescription -->
				<form action="?" method="post" name="addPrescriptionSuiviSoins" onsubmit="return checkForm(this);">
					<input type="hidden" name="m" value="dPprescription" />
				  <input type="hidden" name="dosql" value="do_prescription_aed" />
				  <input type="hidden" name="del" value="0" />
				  <input type="hidden" name="prescription_id" value=""/>
				  <input type="hidden" name="object_id" value="{{$sejour->_id}}" />
				  <input type="hidden" name="object_class" value="CSejour" />
				  <input type="hidden" name="type" value="sejour" />
				  <input type="hidden" name="callback" value="updatePrescriptionId" />
				</form>

			  <!-- Formulaire d'ajout de ligne de prescription -->
				<form action="?" method="post" name="addLineElementSuivi" onsubmit="return checkForm(this);">
				  <input type="hidden" name="m" value="dPprescription" />
				  <input type="hidden" name="dosql" value="do_prescription_line_element_aed" />
				  <input type="hidden" name="prescription_line_element_id" value=""/>
				  <input type="hidden" name="del" value="0" />
				  <input type="hidden" name="prescription_id" value="{{$prescription->_id}}"/>
				  <input type="hidden" name="object_class" value="{{$prescription->object_class}}" />
				  <input type="hidden" name="praticien_id" value="{{$app->user_id}}" />
				  <input type="hidden" name="creator_id" value="{{$app->user_id}}" />  
				  <input type="hidden" name="debut" value="current" />
				  <input type="hidden" name="time_debut" value="current" />
				  <input type="hidden" name="element_prescription_id" value=""/>
					<input type="hidden" name="commentaire" value=""/>
				</form>
	
			  <!-- Autocomplete d'element, avec eventuellement un select de chapitre --> 
				
				<!-- Selecteur d'elements -->
				<table style="width: 100%" class="form layout">
					<tr>
						<td>
							<form name="addLineSuivi" action="?" method="post">
			          <input type="hidden" name="element_id" />
                <table class="form">
                	<tr>
                	  <th class="category" colspan="2">Catalogue d'elements</th>
									</tr>
			            <tr>
			              <th>Element</th>
			              <td>
			                <input type="text" name="libelle" value="" class="autocomplete" />
			                <div style="display:none;" class="autocomplete" id="line_auto_complete"></div>      
			              </td>
			            </tr>
			            <tr>
			              <th>Commentaire</th>
			              <td>
			                <input type="text" name="commentaire" style="width: 15em;" />      
			              </td>
			            </tr>
			            <tr>
			              <td colspan="2" class="button">
			                <button type="button" class="submit" onclick="submitLineElement();">{{tr}}Save{{/tr}}</button>
			              </td>
			            </tr>
			          </table>
			        </form>
						</td>
            <td>
              <table class="form">
              	<tr>
              		<th class="category">Commentaire</th>
								</tr>	
              	<tr>
								  <td style="text-align: center;">
								  	<form name="addLineCommentMedSuiviSoins" method="post" action="" onsubmit="return onSubmitFormAjax(this, { onComplete: function(){ Prescription.reload('{{$prescription->_id}}',null,'medicament')} } )">
		                  <input type="hidden" name="m" value="dPprescription" />
		                  <input type="hidden" name="dosql" value="do_prescription_line_comment_aed" />
		                  <input type="hidden" name="del" value="0" />
		                  <input type="hidden" name="prescription_line_comment_id" value="" />
		                  <input type="hidden" name="prescription_id" value="{{$prescription->_id}}" />
		                  <input type="hidden" name="praticien_id" value="{{$app->user_id}}" />
		                  <input type="hidden" name="chapitre" value="medicament" />
		                  <input type="hidden" name="creator_id" value="{{$app->user_id}}" />
											<input type="hidden" name="debut" value="current" />
                      <input type="hidden" name="time_debut" value="current" />
                      
		                  {{mb_field class=CPrescriptionLineComment field=commentaire}}
		                  <button class="submit" type="button" onclick="submitLineComment();">Ajouter ce commentaire</button>
		                </form>
								  </td>
								</tr>
              </table>
					  </td>
            <td>
            	<table class="form">
            		<tr>
            			<th class="category">
            				Protocole
            			</th>
            		</tr>
								<tr>
									<td>
										<form name="applyProtocoleSuiviSoins" method="post" action="?">
							        <input type="hidden" name="m" value="dPprescription" />
							        <input type="hidden" name="dosql" value="do_apply_protocole_aed" />
							        <input type="hidden" name="del" value="0" />
							        <input type="hidden" name="prescription_id" value="{{$prescription->_id}}" />
							        <input type="hidden" name="praticien_id" value="{{$app->user_id}}" />
							        <input type="hidden" name="pack_protocole_id" value="" onchange="submitProtocoleSuiviSoins();" />
							        <input type="text" name="libelle_protocole" value="&mdash; Choisir un protocole" class="autocomplete" />
							        <div style="display:none; width: 350px;" class="autocomplete" id="protocole_auto_complete_suivi_soins"></div>
										</form>
									</td>
								</tr>
            	</table>
            </td>
					</tr>	
				</table>
		  </td>
		{{/if}}
  </tr>
</table>

</div>

<div id="pagination"></div>
{{assign var=start value=0}}
{{assign var=end value=$page_step}}
{{foreach from=$sejour->_ref_suivi_medical name=steps item=_item}}
  {{if $smarty.foreach.steps.index % $page_step == 0}}
    {{assign var=id value=$smarty.foreach.steps.index}}
    <div class="list_trans" id="list_{{$id}}" style="display:none">
	    {{assign var=start value=$smarty.foreach.steps.index}}
	    {{if $start+$end > $count_trans}}
	       {{assign var=end value=$count_trans-$start}}
	    {{/if}}
	    {{assign var=mini_list value=$sejour->_ref_suivi_medical|@array_slice:$start:$end}}
	    {{include file="../../dPhospi/templates/inc_list_transmissions.tpl" without_del_form=false list_transmissions=$mini_list}}
    </div>
  {{/if}}
{{/foreach}}