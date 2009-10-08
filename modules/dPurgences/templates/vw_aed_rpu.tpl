{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPurgences
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{mb_include_script module=dPpatients script=pat_selector}}
{{mb_include_script module=dPurgences script=contraintes_rpu}}
{{mb_include_script module="dPprescription" script="prescription"}}
{{mb_include_script module="dPmedicament" script="medicament_selector"}}
{{mb_include_script module="dPmedicament" script="equivalent_selector"}}
{{mb_include_script module="dPprescription" script="element_selector"}}

<script type="text/javascript">

ContraintesRPU.contraintesProvenance = {{$contrainteProvenance|@json}};

function loadSuivi(sejour_id, user_id) {
  if(sejour_id) {
    var urlSuivi = new Url("dPhospi", "httpreq_vw_dossier_suivi");
    urlSuivi.addParam("sejour_id", sejour_id);
    urlSuivi.addParam("user_id", user_id);
    urlSuivi.requestUpdate("suivisoins", { waitingText: null } );
  }
}

function submitSuivi(oForm) {
  sejour_id = oForm.sejour_id.value;
  submitFormAjax(oForm, 'systemMsg', { onComplete: function() { loadSuivi(sejour_id); } });
}

function refreshConstantesMedicales(context_guid) {
  if(context_guid) {
    var url = new Url("dPhospi", "httpreq_vw_constantes_medicales");
    url.addParam("context_guid", context_guid);
    url.requestUpdate("constantes", { waitingText: null } );
  }
}

var constantesMedicalesDrawn = false;
function refreshConstantesHack(sejour_id) {
  if (constantesMedicalesDrawn == false && $('constantes').visible() && sejour_id) {
    refreshConstantesMedicales('CSejour-'+sejour_id);
    constantesMedicalesDrawn = true;
  }
}

function cancelRPU() {
  var oForm = document.editRPU;
  var oElement = oForm._annule;
  
  if (oElement.value == "0") {
    if (confirm("Voulez-vous vraiment annuler le dossier ?")) {
      oElement.value = "1";
      oForm.submit();
      return;
    }
  }
      
  if (oElement.value == "1") {
    if (confirm("Voulez-vous vraiment rétablir le dossier ?")) {
      oElement.value = "0";
      oForm.submit();
      return;
    }
  }
}

{{if $isPrescriptionInstalled}}
function reloadPrescription(prescription_id){
  Prescription.reloadPrescSejour(prescription_id, '','', '1', null, null, null, true, {{if $app->user_prefs.mode_readonly}}false{{else}}true{{/if}},'');
}
{{/if}}

function loadTransfert(mode_sortie, sejour_id){
  if(mode_sortie=="transfert"){
    var url = new Url();
    url.setModuleAction("dPurgences", "httpreq_vw_etab_externes");
    url.requestUpdate('listEtabs-'+sejour_id, { waitingText: null } );
  } else {
    // sinon, on vide le contenu de la div et l'etablissement de transfert du sejour
    $('listEtabs-'+sejour_id).innerHTML = "";
  }
}

function showEtabEntreeTransfert(mode) {
	// mode de transfert = transfert (7)
	if (mode == 7) {
		$('etablissement_entree_transfert').show();
	} else {
		$('etablissement_entree_transfert').hide();
		$V(getForm('editRPU')._etablissement_entree_transfert_id, '');
	}
}

Main.add(function () {
  {{if $rpu->_id && $can->edit}}
    DossierMedical.reloadDossierPatient();
    var tab_sejour = Control.Tabs.create('tab-dossier');
    loadSuivi({{$rpu->sejour_id}});
    refreshConstantesHack('{{$rpu->sejour_id}}');
  {{/if}}
  
  if (document.editAntFrm){
    document.editAntFrm.type.onchange();
  }
});

</script>

<form name="editRPU" action="?m={{$m}}{{if !$can->edit}}&amp;tab=vw_idx_rpu{{/if}}" method="post" onsubmit="return checkForm(this)">

<input type="hidden" name="m" value="dPurgences" />
<input type="hidden" name="dosql" value="do_rpu_aed" />
<input type="hidden" name="del" value="0" />
<input type="hidden" name="rpu_id" value="{{$rpu->_id}}" />
<input type="hidden" name="sejour_id" value="{{$sejour->_id}}" />
<input type="hidden" name="_annule" value="{{$rpu->_annule|default:"0"}}" />

<input type="hidden" name="_bind_sejour" value="1" />
<a class="button new" href="?m=dPurgences&amp;tab=vw_aed_rpu&amp;rpu_id=0">
  Ajouter un patient
</a>
<table class="form">
  <tr>
    {{if $rpu->_id}}
    <th class="title modify" colspan="4">
      <div style="float: left;" class="noteDiv {{$sejour->_guid}}">
        <img alt="Ecrire une note" src="images/icons/note_grey.png" />
      </div>
    
      {{mb_include module=system template=inc_object_idsante400 object=$rpu}}
      {{mb_include module=system template=inc_object_history object=$rpu}}

	    <a class="action" style="float: right;" title="Modifier uniquement le sejour" href="?m=dPplanningOp&amp;tab=vw_edit_sejour&amp;sejour_id={{$sejour->_id}}">
	      <img src="images/icons/edit.png" alt="modifier" />
	    </a>

	    Modification de du RPU {{$rpu}}
	    {{if $sejour->_num_dossier}}
	      [{{$sejour->_num_dossier}}]
	    {{/if}}
    </th>
    {{else}}
    <th class="title" colspan="4">
	    Création d'un RPU 
	    {{if $sejour->_num_dossier}}
	    	pour le dossier
	      [{{$sejour->_num_dossier}}]
	    {{/if}}
	  </th>
    {{/if}}
  </tr>

	{{if $rpu->_annule}}
	<tr>
	  <th class="category cancelled" colspan="4">
	  {{tr}}CRPU-_annule{{/tr}}
	  </th>
	</tr>
	{{/if}}
  
  <tr>
    <th>{{mb_label object=$rpu field="_responsable_id"}}</th>
    <td>
      <select name="_responsable_id">
        {{foreach from=$listResponsables item=curr_user}}
        <option value="{{$curr_user->_id}}" class="mediuser" style="border-color: #{{$curr_user->_ref_function->color}}" {{if $curr_user->_id == $rpu->_responsable_id}}selected="selected"{{/if}}>
          {{$curr_user->_view}}
        </option>
        {{/foreach}}
      </select>
    </td>
    
    <th>{{mb_label object=$rpu field="mode_entree"}}</th>
    <td>{{mb_field object=$rpu field="mode_entree" defaultOption="&mdash; Mode d'entrée" onchange="ContraintesRPU.updateProvenance(this.value, true); showEtabEntreeTransfert(this.value);"}}</td>
  </tr>
  
  <tr>
    <th>{{mb_label object=$rpu field="_entree"}}</th>
    <td class="date">{{mb_field object=$rpu field="_entree" form="editRPU" register=true}}</td>
    
	  <th>{{mb_label object=$rpu field="_etablissement_entree_transfert_id"}}</th>
	  <td>
	     <div id="etablissement_entree_transfert" {{if !$sejour->etablissement_entree_transfert_id}}style="display:none"{{/if}}> 
	       {{assign var=_transfert_id value=$sejour->etablissement_entree_transfert_id}}
	       {{if $listEtab|@count}}
				 <select name="_etablissement_entree_transfert_id">
					 <option value="">&mdash; Etablissement de transfert</option>
				   {{foreach from=$listEtab item="etab"}}
					 <option value="{{$etab->_id}}" {{if $etab->_id == $_transfert_id}}selected="selected"{{/if}}>{{$etab->_view}}</option>
					 {{/foreach}}
				</select>
				{{/if}}
       </div>
	  </td>
  </tr>

  <tr>
	  <th>
	    <input type="hidden" name="_patient_id" class="{{$sejour->_props.patient_id}}" ondblclick="PatSelector.init()" value="{{$rpu->_patient_id}}" />
	    {{mb_label object=$rpu field="_patient_id"}}
	  </th>
	  <td>
	  	<input type="text" name="_patient_view" size="20" value="{{$patient->_view}}" 
	  	  {{if $dPconfig.dPurgences.allow_change_patient || !$sejour->_id || $app->user_type == 1}} 
	  	    ondblclick="PatSelector.init()" 
	  	  {{/if}}
	  	readonly="readonly" />
	    
	    {{if $dPconfig.dPurgences.allow_change_patient || !$sejour->_id || $app->user_type == 1}} 
	      <button type="button" class="search" onclick="PatSelector.init()">Choisir un patient</button>
	    {{/if}}
	    <script type="text/javascript">
	      PatSelector.init = function(){
	        this.sForm = "editRPU";
	        this.sId   = "_patient_id";
	        this.sView = "_patient_view";
	        this.pop();
	      }
	    </script>
	    
    </td>
    
    {{if $dPconfig.dPurgences.old_rpu == "1"}}
    <th>{{mb_label object=$rpu field="urprov"}}</th>
    <td>{{mb_field object=$rpu field="urprov" defaultOption="&mdash; Provenance"}}</td>
    {{else}}
    <th>{{mb_label object=$rpu field="provenance"}}</th>
    <td>{{mb_field object=$rpu field="provenance" defaultOption="&mdash; Provenance"}}</td>
    {{/if}}
  </tr>
  
  <tr>
	  {{if $can->edit}}
    <th>{{mb_label object=$rpu field="ccmu"}}</th>
    <td>{{mb_field object=$rpu field="ccmu" defaultOption="&mdash; Code CCMU"}}</td>
		{{else}}
		<th></th>
    <td></td>
	  {{/if}}
	  <th>{{mb_label object=$rpu field="transport"}}</th>
    <td>{{mb_field object=$rpu field="transport" defaultOption="&mdash; Type de transport"}}</td>
  </tr>
  
   <!-- Selection du service -->
  <tr>
	  <th>
	    {{mb_label object=$rpu field="_service_id"}}
	  </th>
	  <td>
	    {{if $listServicesUrgence|@count == 1}}
	      {{assign var=first_service value=$listServicesUrgence|@reset}}
	      {{$first_service->_view}}
	    {{else}}
	    <select name="_service_id" class="{{$sejour->_props.service_id}}">
	      <option value="">&mdash; Choisir un service</option>
	      {{foreach from=$listServicesUrgence item=_service}}
	      <option value="{{$_service->_id}}" {{if "Urgences" == $_service->nom}} selected="selected" {{/if}}>
	        {{$_service->_view}}
	      </option>
	      {{/foreach}}
	    </select>
	    {{/if}}
	  </td>
	  <th>{{mb_label object=$rpu field="pec_transport"}}</th>
    <td>{{mb_field object=$rpu field="pec_transport" defaultOption="&mdash; Prise en charge"}}</td>
	</tr>

  <tr>
    <th>{{mb_label object=$rpu field="box_id"}}</th>
    <td>
      {{include file="../../dPhospi/templates/inc_select_lit.tpl 
      		field=box_id 
      		selected_id=$rpu->box_id 
      		ajaxSubmit=0 
      		listService=$listServicesUrgence}}
		</td>
		<th>{{mb_label object=$rpu field="accident_travail"}}</th>
    <td>{{mb_field object=$rpu field="accident_travail" form="editRPU" register=true}}</td>
  </tr>

  {{if $can->edit}}
  <tr>
    <th>{{mb_label object=$rpu field="diag_infirmier"}} <br />
     <select name="_helpers_diag_infirmier" size="1" onchange="pasteHelperContent(this);">
        <option value="">&mdash; Choisir une aide</option>
        {{html_options options=$rpu->_aides.diag_infirmier.no_enum}}
     </select><br />
     <button class="new notext" title="Ajouter une aide à la saisie" type="button" onclick="addHelp('CRPU', this.form.diag_infirmier)">{{tr}}New{{/tr}}</button><br />
      </th> 
    <td colspan="3">
   
      <!-- Aide a la saisie -->
     {{mb_field object=$rpu field="diag_infirmier"}}
      </td>
  </tr>
  {{/if}}
  
  <tr>
		<td class="button" colspan="4">
		  {{if $rpu->_id}}
			  <button class="modify" type="submit">Valider</button>
		    {{mb_ternary var=annule_text test=$sejour->annule value="Rétablir" other="Annuler"}}
		    {{mb_ternary var=annule_class test=$sejour->annule value="change" other="cancel"}}
		    
		    <button class="{{$annule_class}}" type="button" onclick="cancelRPU();">
		      {{$annule_text}}
		    </button>
		    
		    {{if $can->admin}}
				  <button class="trash" type="button" onclick="confirmDeletion(this.form,{typeName:'l\'urgence ',objName:'{{$rpu->_view|smarty:nodefaults|JSAttribute}}'})">
				    {{tr}}Delete{{/tr}}
				  </button>
				{{/if}}
				    
	    {{else}}
		    <button class="submit" name="btnFuseAction" type="submit">{{tr}}Create{{/tr}}</button>
      {{/if}}
  	</td>
  </tr>
  
</table>

</form>

<!-- Dossier Médical du patient -->
{{if $rpu->_id && $can->edit}}
<table width="100%" class="form">
  <tr>
    <th class="category">Autres informations</th>
    <th class="category">Prise en charge</th>
  </tr>

  <tr>
    <td style="width: 60%">
      {{include file="inc_vw_radio.tpl"}}
    </td>
    <td class="button">
  		{{include file="inc_pec_praticien.tpl"}}
    </td>
  </tr>
</table>

<ul id="tab-dossier" class="control_tabs">
  <li><a href="#antecedents">Antécédents / Traitements</a></li>
  <li><a href="#suivisoins">Suivi soins</a></li>
  <li onclick="refreshConstantesHack('{{$rpu->sejour_id}}')"><a href="#constantes">Constantes</a></li>
  <li><a href="#examens">Examens</a></li>
  {{if $isPrescriptionInstalled}}
  <li {{if $rpu->_ref_consult->sejour_id}}onclick="Prescription.reloadPrescSejour('', '{{$rpu->_ref_consult->sejour_id}}','', '', null, null, null, true, {{if $app->user_prefs.mode_readonly}}false{{else}}true{{/if}},'');"{{/if}}><a href="#prescription_sejour">Prescription</a></li>
  {{/if}}
</ul>

<hr class="control_tabs" />

<div id="antecedents">
  {{assign var="current_m" value="dPurgences"}}
  {{assign var="_is_anesth" value="0"}}
  {{assign var="consult" value=$rpu->_ref_consult}}
  {{assign var=sejour_id value=""}}
  {{include file="../../dPcabinet/templates/inc_ant_consult.tpl" chir_id=$app->user_id}}
</div>

<div id="suivisoins" style="display:none"></div>
<div id="constantes" style="display:none"></div>

<div id="examens">
  {{mb_include module=dPcabinet template=inc_main_consultform readonly=1}}
</div>

{{if $isPrescriptionInstalled}}
<div id="prescription_sejour" style="display: none;">
  <div class="small-info">
    Aucune prescription
  </div>
</div>
{{/if}}
{{/if}}


{{if $rpu->mode_entree}}
<script type="text/javascript">
// Lancement des fonctions de contraintes entre les champs
ContraintesRPU.updateProvenance("{{$rpu->mode_entree}}");
</script>
{{/if}}