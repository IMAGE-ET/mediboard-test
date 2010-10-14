{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{* <div id="prescription_sejour"> *}}

{{mb_include_script module="dPmedicament" script="medicament_selector"}}
{{mb_include_script module="dPmedicament" script="equivalent_selector"}}
{{mb_include_script module="dPprescription" script="element_selector"}}
{{mb_include_script module="dPprescription" script="prescription"}}
{{mb_include_script module="dPprescription" script="protocole"}}

<script type="text/javascript">

refreshListProtocolesPrescription = function(praticien_id, list, selected_id) {
  if (list) {
    var url = new Url;
    url.setModuleAction("dPplanningOp", "httpreq_vw_list_protocoles_prescription");
    url.addParam("praticien_id", praticien_id);
    url.addParam("selected_id", selected_id);
    url.requestUpdate(list);
  }
}
			          
var oFormProtocole = getForm("applyProtocoleFirst");

if (oFormProtocole) {
var url = new Url("dPprescription", "httpreq_vw_select_protocole");
var autocompleter = url.autoComplete(oFormProtocole.libelle_protocole, "protocole_auto_complete", {
    dropdown: true,
    minChars: 1,
    valueElement: oFormProtocole.elements.pack_protocole_id,
    updateElement: function(selectedElement) {
      var node = $(selectedElement).down('.view');
      $V($("applyProtocoleFirst_libelle_protocole"), (node.innerHTML).replace("&lt;", "<").replace("&gt;",">"));
      if (autocompleter.options.afterUpdateElement)
        autocompleter.options.afterUpdateElement(autocompleter.element, selectedElement);
    },
    callback: 
        function(input, queryString){
          {{if !$is_praticien}}
            return (queryString + "&prescription_id={{$prescription->_id}}&praticien_id="+$('choix_prat').value+"&type="+$("type_prescription").value);
          {{else}}
            return (queryString + "&prescription_id={{$prescription->_id}}&praticien_id={{$current_user->_id}}&type="+$("type_prescription").value);
        	{{/if}}
        }
  } );
}
addProtocole = function(prescription_id) {
  $V(oFormProtocole.prescription_id, prescription_id);
  if ($V(oFormProtocole.pack_protocole_id) == "") {
	  reloadPrescription(prescription_id);
  }
  else {
	  $V(oFormProtocole.praticien_id, $('choix_prat').value);
	  submitFormAjax(oFormProtocole, 'msgajax');
  }
}

emptyProtocole = function() {
	$V(oFormProtocole.libelle_protocole, String.fromCharCode(8212)+ " Aucun protocole");
	$V(oFormProtocole.pack_protocole_id, '');
	$V(getForm("addPrescription").type, $("type_prescription").value);
}

</script>

<div style="display: none;" id="msgajax"></div>

<table class="main">
  {{if $prescription->_id}}
  
  <!-- Cas d'une prescription de sejour -->
  {{if $prescription->object_class == "CSejour"}}
  {{assign var=sejour value=$prescription->_ref_object}}
  {{assign var=prescriptions value=$sejour->_ref_prescriptions}}
  {{assign var=prescription_pre_admission value=$prescriptions.pre_admission}}
  {{assign var=prescription_sejour value=$prescriptions.sejour}}
  {{assign var=prescription_sortie value=$prescriptions.sortie}}
  
  <tr>
    <td>
      <form name="mode_affichage">
        <!-- Permet de rester en mode lecture (et de ne pas recuperer l'operation_id en session) -->
        <input type="hidden" name="mode_sejour" value="{{$mode_sejour}}" />
      </form>
      <table style="width: 100%;">
        <tr>
          
          <!-- Pre-admission -->
          <td id="pre_admission" class="step {{if $prescription->type == 'pre_admission'}}selected{{/if}}">
          {{if $prescription_pre_admission->_id}}
	            <a href="#1" onclick="Prescription.reloadPrescSejour('{{$prescription_pre_admission->_id}}','{{$prescription->object_id}}');">
	              Pré-adm.
	            </a>
          	 {{if $prescription->type == "pre_admission"}}
	            <form name="selPratForPresc" method="get" action="">
			          <select name="selPraticien" 
			                  onchange="Prescription.reloadPrescSejour('{{$prescription_pre_admission->_id}}','{{$prescription->object_id}}',this.value);">
			              <option value="">Tous</option>
				            {{foreach from=$prescription->_praticiens item=_praticien}}
				              <option value="{{$_praticien}}" {{if $_praticien == $praticien_sortie_id}}selected="selected"{{/if}}>{{$_praticien}}</option>
				            {{/foreach}}
			          </select>
		         </form>
              {{/if}}
            {{else}}
	            <form name="addPrescriptionSejour" method="post" action="?">
	              <input type="hidden" name="m" value="dPprescription" />
	              <input type="hidden" name="dosql" value="do_prescription_aed" />
	              <input type="hidden" name="praticien_id" value="{{$app->user_id}}" />
	              <input type="hidden" name="object_id" value="{{$sejour->_id}}" />
	              <input type="hidden" name="object_class" value="CSejour" />
	              <input type="hidden" name="type" value="pre_admission" />
	              <input type="hidden" name="callback" value="reloadPrescription" />
	              <button type="button" class="add" onclick="submitFormAjax(this.form, 'systemMsg');">
	                Pré-admission
	              </button> 
	            </form>
            {{/if}}
          </td>
          
          
          <!-- Séjour -->
          <td id="sejour" class="step {{if $prescription->type == 'sejour'}}selected{{/if}}">
	          {{if $prescription_sejour->_id}}
		          <a href="#1" onclick="Prescription.reloadPrescSejour('{{$prescription_sejour->_id}}','{{$prescription->object_id}}');">
		            {{tr}}CPrescription.type.sejour{{/tr}}
		          </a>
				      {{if $prescription->type == "sejour"}}
				            <form name="selPratForPresc" method="get" action="">
						          <select name="selPraticien" 
						                  onchange="Prescription.reloadPrescSejour('{{$prescription_sejour->_id}}','{{$prescription->object_id}}',this.value);">
						              <option value="">Tous</option>
							            {{foreach from=$prescription->_praticiens item=_praticien}}
							              <option value="{{$_praticien}}" {{if $_praticien == $praticien_sortie_id}}selected="selected"{{/if}}>{{$_praticien}}</option>
							            {{/foreach}}
						          </select>
					         </form>
			        {{/if}}
	          {{else}}
              <form name="addPrescriptionSejour" method="post" action="?">
                <input type="hidden" name="m" value="dPprescription" />
                {{if $prescription_pre_admission->_id}}
                  <input type="hidden" name="dosql" value="do_duplicate_prescription_aed" />
                  <input type="hidden" name="prescription_id" value="{{$prescription_pre_admission->_id}}" />
                {{else}}
                  <input type="hidden" name="dosql" value="do_prescription_aed" />
                  <input type="hidden" name="object_id" value="{{$sejour->_id}}" />
                  <input type="hidden" name="object_class" value="CSejour" />
                {{/if}}
                <input type="hidden" name="type" value="sejour" />
                <input type="hidden" name="praticien_id" value="{{$app->user_id}}" />
                <input type="hidden" name="callback" value="reloadPrescription" />
                <button type="button" class="add" onclick="submitFormAjax(this.form, 'systemMsg');">
                  Séjour
                </button> 
              </form>
            {{/if}}
          </td>
         
          <td id="sortie" class="step {{if $prescription->type == 'sortie'}}selected{{/if}}">
          {{if $prescriptions.sortie->_id}}
              <a href="#1" onclick="Prescription.reloadPrescSejour('{{$prescription_sortie->_id}}','{{$prescription->object_id}}');">
		            {{tr}}CPrescription.type.sortie{{/tr}}
		          </a>
		          {{if $prescription->type == "sortie"}}
		            <form name="selPratForPresc" method="get" action="">
				          <select name="selPraticien" 
				                  onchange="Prescription.reloadPrescSejour('{{$prescription_sortie->_id}}','{{$prescription->object_id}}',this.value);">
				              <option value="">Tous</option>
					            {{foreach from=$prescription->_praticiens item=_praticien}}
					              <option value="{{$_praticien}}" {{if $_praticien == $praticien_sortie_id}}selected="selected"{{/if}}>{{$_praticien}}</option>
					            {{/foreach}}
				          </select>
			         </form>
              {{/if}}
	        {{else}}
	          <form name="addPrescriptionSejour" method="post" action="">
	            <input type="hidden" name="m" value="dPprescription" />
	            {{if $prescription_sejour->_id}}
	              <input type="hidden" name="prescription_id" value="{{$prescription_sejour->_id}}" />
	              <input type="hidden" name="dosql" value="do_duplicate_prescription_aed" />
	            {{else}}
	              <input type="hidden" name="dosql" value="do_prescription_aed" />
	              <input type="hidden" name="object_id" value="{{$sejour->_id}}" />
	              <input type="hidden" name="object_class" value="CSejour" />
	            {{/if}}
	            <input type="hidden" name="type" value="sortie" />
	            <input type="hidden" name="callback" value="reloadPrescription" />
	            <input type="hidden" name="praticien_id" value="{{$app->user_id}}" />
	            <button type="button" class="add" onclick="submitFormAjax(this.form, 'systemMsg');">             
	              Sortie
	            </button>
	          </form>
	        {{/if}}
          </td>
        </tr>
      </table>
    </td>
  </tr>
  {{else}}
  <!-- Cas d'une prescription de consultation -->
  <tr>
    <td class="step selected">
       Prescription {{tr}}CPrescription.type.externe{{/tr}} 
    </td>
  </tr>
  {{/if}}
  <tr>
    <td class="greedyPane">
      {{assign var=httpreq value=0}}
      <div id="prescription">
        {{include file="inc_vw_prescription.tpl" mode_protocole=0}}
      </div>
    </td>
  </tr>
  {{else}}
   {{if $full_mode && $sejour_id}}
    <tr>
      <td>
				  <table class="form">
				    <tr>
				      {{if !$is_praticien}}
                <th class="title">Praticien</th>
              {{/if}}
              {{if $operations|@count > 1}}
                <th class="title">Intervention</th>
              {{/if}}
				      <th class="title" >{{mb_title object=$prescription field="type"}}</th>
				      <th class="title" >Protocole</th>
				    </tr>
				    <tr>
				    {{if !$is_praticien}}
				      <td>
				      <select name="praticien_id" id="choix_prat" onchange="$V(getForm('applyProtocoleFirst').praticien_id, this.value); emptyProtocole();">
			         <optgroup label="Responsables">
		              {{if $chir->_id}}
                    <option class="mediuser"
                            style="border-color: #{{$chir->_ref_function->color}};" 
                            value="{{$chir->_id}}">{{$chir->_view}}</option>
                  {{/if}}
                  {{if $anesth->_id}}
                    <option class="mediuser"
                            style="border-color: #{{$anesth->_ref_function->color}};"
                            value="{{$anesth->_id}}">{{$anesth->_view}}</option>
                  {{/if}}
		            </optgroup>
		            <optgroup label="Tous les praticiens">
		              {{foreach from=$listPrats item=_praticien}}
		                <option class="mediuser"
		                        style="border-color: #{{$_praticien->_ref_function->color}};" 
		                        value="{{$_praticien->_id}}"
		                        {{if $_praticien->_id == $prescription->_current_praticien_id}}selected="selected"{{/if}}>{{$_praticien->_view}}
		                </option>
		              {{/foreach}}
		            </optgroup>
			        </select>
				      </td>
				    {{else}}
				      <input type="hidden" id="choix_prat" value="{{$app->user_id}}" />
            {{/if}}
              {{if $operations|@count > 1}}
	              <td>
		              <select name="operation_id" onchange="$V(getForm('applyProtocoleFirst').operation_id, this.value);">
		                {{foreach from=$operations item=_operation name=operations}}
		                  <option value="{{$_operation->_id}}" {{if $smarty.foreach.operations.index == $smarty.foreach.operations.last}}selected="selected"{{/if}}>
                        {{$_operation->_view}}
                      </option>
		                {{/foreach}}
		              </select>
	              </td>
              {{/if}}
				      <td>
							  <select name="type" id="type_prescription" onchange="emptyProtocole();">
					        <option value="pre_admission">Pré-admission</option>
					        <option value="sejour" selected="selected">Séjour</option>
					        <option value="sortie">Sortie</option>
					      </select>
				      </td>
				      <td>
				        <form name="applyProtocoleFirst" method="post" action="?" onsubmit="return checkForm(this);">
                  <input type="hidden" name="m" value="dPprescription" />
                  <input type="hidden" name="dosql" value="do_apply_protocole_aed" />
                  <input type="hidden" name="del" value="0" />
                  <input type="hidden" name="prescription_id" value="" />
                  <input type="hidden" name="pratSel_id" value="{{$praticien_sejour}}" />
                  <input type="hidden" name="praticien_id" value="" />
                  <input type="hidden" name="pack_protocole_id" value="" />
                  {{assign var=last_operation value=$operations|@end}}
                  {{if $operations|@count >= 1}}
                    <input type="hidden" name="operation_id" value="{{$last_operation->_id}}" />
                  {{/if}}
					        <input type="text"   name="libelle_protocole" value="&mdash; Aucun protocole" class="autocomplete" style="font-weight: bold; font-size: 1.3em; width: 250px;"/>
	                <div style="display:none; width: 200px;" class="autocomplete" id="protocole_auto_complete"></div>
                </form>
                
				        <form action="?m=dPprescription" method="post" name="addPrescription" onsubmit="return checkForm(this);">
				          <input type="hidden" name="m" value="dPprescription" />
				          <input type="hidden" name="dosql" value="do_prescription_aed" />
				          <input type="hidden" name="prescription_id" value="" />
				          <input type="hidden" name="del" value="0" />
				          <input type="hidden" name="object_id" value="{{$sejour_id}}"/>
				          <input type="hidden" name="object_class" value="CSejour" />
				          <input type="hidden" name="type" value="sejour"/>
				          <input type="hidden" name="callback" value="addProtocole" />
	                <button type="button" class="submit" onclick="submitFormAjax(this.form, 'systemMsg');">Créer</button>         
                </form>
				      </td>
				    </tr>
				    <tr>
				    </tr>
					</table>
		  </td>
		</tr>
   {{else}}
		  <tr>
		    <td>
		      <div class="small-info">
		        {{if $full_mode}}
		          Veuillez sélectionner un séjour ou une intervention pour pouvoir créer une prescription.
		        {{else}}
		          Veuillez choisir un contexte (séjour ou consultation) pour la prescription.
		        {{/if}}
		      </div>
		    </td>
		  </tr>
    {{/if}}
  {{/if}}
</table>

{{* </div> *}}