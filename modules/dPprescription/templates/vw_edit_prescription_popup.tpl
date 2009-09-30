{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<div id="prescription_sejour">

{{mb_include_script module="dPmedicament" script="medicament_selector"}}
{{mb_include_script module="dPmedicament" script="equivalent_selector"}}
{{mb_include_script module="dPprescription" script="element_selector"}}
{{mb_include_script module="dPprescription" script="prescription"}}
{{mb_include_script module="dPprescription" script="protocole"}}

<script type="text/javascript">

function refreshListProtocolesPrescription(praticien_id, list, selected_id) {
  if (list) {
    var url = new Url;
    url.setModuleAction("dPplanningOp", "httpreq_vw_list_protocoles_prescription");
    url.addParam("praticien_id", praticien_id);
    url.addParam("selected_id", selected_id);
    url.requestUpdate(list, { waitingText: null} );
  }
}
			          
Main.add(function () {
  if (formAnesth = document.forms.editProtoPrescriptionAnesth) {
    refreshListProtocolesPrescription('{{$anesth_id}}', formAnesth.pack_protocole_id);
  }
  
  if (formChir = document.forms.editProtoPrescriptionChir) {
    refreshListProtocolesPrescription('{{$chir_id}}', formChir.pack_protocole_id);
  }
});

</script>

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
	            <a href="#1" onclick="Prescription.reloadPrescSejour('{{$prescription_pre_admission->_id}}','{{$prescription->object_id}}', null, null, null, null, null, true, {{if $app->user_prefs.mode_readonly}}false{{else}}true{{/if}},'');">
	              Pré-adm. <!-- {{tr}}CPrescription.type.pre_admission{{/tr}}  -->
	            </a>
          	 {{if $prescription->type == "pre_admission"}}
	            <form name="selPratForPresc" method="get" action="">
			          <select name="selPraticien" 
			                  onchange="Prescription.reloadPrescSejour('{{$prescription_pre_admission->_id}}','{{$prescription->object_id}}',this.value, null, null, null, null, true, {{if $app->user_prefs.mode_readonly}}false{{else}}true{{/if}},'');">
			              <option value="">Tous</option>
				            {{foreach from=$prescription->_praticiens item=_praticien}}
				              <option value="{{$_praticien}}" {{if $_praticien == $praticien_sortie_id}}selected="selected"{{/if}}>{{$_praticien}}</option>
				            {{/foreach}}
			          </select>
		         </form>
            {{/if}}
            
            {{if $prescription->type == "pre_admission" && !$prescription_sejour->_id}}
              <form name="addPrescriptionSejour" method="post" action="?">
	              <input type="hidden" name="m" value="dPprescription" />
			          <input type="hidden" name="dosql" value="do_duplicate_prescription_aed" />
			          <input type="hidden" name="prescription_id" value="{{$prescription->_id}}" />
			          <input type="hidden" name="type" value="sejour" />
			          <button type="button" class="add" onclick="submitFormAjax(this.form, 'systemMsg');">
			            Séjour
			          </button> 
		          </form>   
            {{/if}}
          {{/if}}
          </td>
          
          
          <!-- Séjour -->
          <td id="sejour" class="step {{if $prescription->type == 'sejour'}}selected{{/if}}">
	          {{if $prescription_sejour->_id}}
		          <a href="#1" onclick="Prescription.reloadPrescSejour('{{$prescription_sejour->_id}}','{{$prescription->object_id}}', null, null, null, null, null, true, {{if $app->user_prefs.mode_readonly}}false{{else}}true{{/if}}, '');">
		            {{tr}}CPrescription.type.sejour{{/tr}}
		          </a>
				      {{if $prescription->type == "sejour"}}
				            <form name="selPratForPresc" method="get" action="">
						          <select name="selPraticien" 
						                  onchange="Prescription.reloadPrescSejour('{{$prescription_sejour->_id}}','{{$prescription->object_id}}',this.value, null, null, null, null, true, {{if $app->user_prefs.mode_readonly}}false{{else}}true{{/if}}, '');">
						              <option value="">Tous</option>
							            {{foreach from=$prescription->_praticiens item=_praticien}}
							              <option value="{{$_praticien}}" {{if $_praticien == $praticien_sortie_id}}selected="selected"{{/if}}>{{$_praticien}}</option>
							            {{/foreach}}
						          </select>
					         </form>
			            {{/if}}
		          {{if $prescription->type == "sejour" && !$prescription_sortie->_id}}
			          <form name="addPrescriptionSejour" method="post" action="">
				          <input type="hidden" name="m" value="dPprescription" />
				          <input type="hidden" name="dosql" value="do_duplicate_prescription_aed" />
				          <input type="hidden" name="prescription_id" value="{{$prescription->_id}}" />
				          <input type="hidden" name="type" value="sortie" />
						      <button type="button" class="add" onclick="submitFormAjax(this.form, 'systemMsg');">			       
						       Sortie
						      </button>
			          </form>
		          {{/if}}
	          {{/if}}
          </td>
         
          <td id="sortie" class="step {{if $prescription->type == 'sortie'}}selected{{/if}}">
          {{if $prescriptions.sortie->_id}}
              <a href="#1" onclick="Prescription.reloadPrescSejour('{{$prescription_sortie->_id}}','{{$prescription->object_id}}', null, null, null, null, null, true, {{if $app->user_prefs.mode_readonly}}false{{else}}true{{/if}},'');">
		            {{tr}}CPrescription.type.sortie{{/tr}}
		          </a>
		          {{if $prescription->type == "sortie"}}
		            <form name="selPratForPresc" method="get" action="">
				          <select name="selPraticien" 
				                  onchange="Prescription.reloadPrescSejour('{{$prescription_sortie->_id}}','{{$prescription->object_id}}',this.value, null, null, null, null, true, {{if $app->user_prefs.mode_readonly}}false{{else}}true{{/if}},'');">
				              <option value="">Tous</option>
					            {{foreach from=$prescription->_praticiens item=_praticien}}
					              <option value="{{$_praticien}}" {{if $_praticien == $praticien_sortie_id}}selected="selected"{{/if}}>{{$_praticien}}</option>
					            {{/foreach}}
				          </select>
			         </form>
	            {{/if}}
          {{/if}}
          </td>
        </tr>
      </table>
    </td>
  </tr>
  {{else}}
  <!-- Cas d'une prescription de consultation -->
  <tr>
    <th style="background-color: #EEF; border: 2px ridge #55A">
      Prescription Externe
    </th>
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
			  <form action="?m=dPprescription" method="post" name="addPrescription" onsubmit="return checkForm(this);">
				  <input type="hidden" name="m" value="dPprescription" />
				  <input type="hidden" name="dosql" value="do_prescription_aed" />
				  <input type="hidden" name="prescription_id" value="" />
				  <input type="hidden" name="del" value="0" />
				  <input type="hidden" name="object_id" value="{{$sejour_id}}"/>
				  <input type="hidden" name="object_class" value="CSejour" />
				  <input type="hidden" name="callback" value="" />
				  <input type="hidden" name="type" value="pre_admission" />
				  {{if $mode_anesth}}
				    <button type="button" class="submit" onclick="this.form.callback.value = 'reloadPrescription'; submitFormAjax(this.form, 'systemMsg');">Créer une prescription de sejour</button>				  
				  {{else}}
				  <button type="button" class="submit" onclick="submitFormAjax(this.form, 'systemMsg', { onComplete: function() { 
				    document.addPrescription.type.value = 'sejour';
				    document.addPrescription.callback.value = 'reloadPrescription';
				    submitFormAjax(document.addPrescription, 'systemMsg');
				  } } );">Créer une prescription de séjour</button>
				  {{/if}}
				</form>
        <br />
        
        {{if $operation_id}}
        {{if $anesth_id}}
        <form name="editProtoPrescriptionAnesth" action="?m=dPprescription" method="post" onsubmit="return onSubmitFormAjax(this)">
          <input type="hidden" name="m" value="dPprescription" />
          <input type="hidden" name="dosql" value="do_apply_protocole_aed" />
          <input type="hidden" name="operation_id" value="{{$operation_id}}" />
          <input type="hidden" name="prescription_id" value="" />
          <input type="hidden" name="praticien_id" value="{{$anesth_id}}" />
          <input type="hidden" name="callback" value="" />
          
          <label for="protocole_id">{{tr}}CProtocole-protocole_prescription_anesth_id{{/tr}}</label>
          <select name="pack_protocole_id" class="notNull"></select>
          
          <button class="submit" type="submit">Appliquer</button>
        </form>
        <br />
        {{/if}}
        {{if $chir_id}}
        <form name="editProtoPrescriptionChir" action="?m=dPprescription" method="post" onsubmit="return onSubmitFormAjax(this)">
          <input type="hidden" name="m" value="dPprescription" />
          <input type="hidden" name="dosql" value="do_apply_protocole_aed" />
          <input type="hidden" name="operation_id" value="{{$operation_id}}" />
          <input type="hidden" name="prescription_id" value="" />
          <input type="hidden" name="praticien_id" value="{{$chir_id}}" />
          <input type="hidden" name="callback" value="" />
          
          <label for="protocole_id">{{tr}}CProtocole-protocole_prescription_chir_id{{/tr}}</label>
          <select name="pack_protocole_id" class="notNull"></select>
          
          <button class="submit" type="submit">Appliquer</button>
        </form>
        {{/if}}
        {{/if}}
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

</div>