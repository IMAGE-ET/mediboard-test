{{mb_include_script module=dPpatients script=pat_selector}}
{{mb_include_script module=dPurgences script=contraintes_rpu}}

<script type="text/javascript">

ContraintesRPU.contraintesProvenance = {{$contrainteProvenance|@json}};

function submitRadio(oForm){
  submitFormAjax(oForm, 'systemMsg', {onComplete: function() { reloadRadio(oForm) } }); 
}

function reloadRadio(oForm){
  var url = new Url;
  url.setModuleAction("dPurgences", "httpreq_vw_radio");
  url.addParam("rpu_id", oForm.rpu_id.value);
  url.requestUpdate('radio', { waitingText: null } );
}

function loadSuivi(sejour_id) {
  var url = new Url;
  url.setModuleAction("dPhospi", "httpreq_vw_dossier_suivi");
  url.addParam("sejour_id", sejour_id);
  url.requestUpdate("suivisoins", { waitingText: null } );
}

function submitSuivi(oForm) {
  sejour_id = oForm.sejour_id.value;
  submitFormAjax(oForm, 'systemMsg', { onComplete: function() { loadSuivi(sejour_id); } });
}

function refreshConstantesMedicales(context_guid) {
  if(context_guid) {
    var url = new Url;
    url.setModuleAction("dPhospi", "httpreq_vw_constantes_medicales");
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

<form name="editRPU" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">

<input type="hidden" name="m" value="dPurgences" />
<input type="hidden" name="dosql" value="do_rpu_aed" />
<input type="hidden" name="del" value="0" />
<input type="hidden" name="rpu_id" value="{{$rpu->_id}}" />
<input type="hidden" name="sejour_id" value="{{$sejour->_id}}" />
<input type="hidden" name="_annule" value="{{$rpu->_annule|default:"0"}}" />

<input type="hidden" name="_bind_sejour" value="1" />
<a class="buttonnew" href="?m=dPurgences&amp;tab=vw_aed_rpu&amp;rpu_id=0">
  Ajouter un patient
</a>
<table class="form">
  <tr>
    {{if $rpu->_id}}
    <th class="title modify" colspan="4">
	    <a style="float:right;" href="#" onclick="view_log('CRPU',{{$rpu->_id}})">
	      <img src="images/icons/history.gif" alt="historique" />
	    </a>

	    <a class="action" style="float: right;" title="Modifier uniquement le sejour" href="?m=dPplanningOp&amp;tab=vw_edit_sejour&amp;sejour_id={{$sejour->_id}}">
	      <img src="images/icons/edit.png" alt="modifier" />
	    </a>

	    Modification de du RPU {{$rpu->_view}}
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
    <td>{{mb_field object=$rpu field="mode_entree" defaultOption="&mdash; Mode d'entrée" onchange="ContraintesRPU.updateProvenance(this.value, true)"}}</td>
  </tr>
  
  <tr>
    <th>{{mb_label object=$rpu field="_entree"}}</th>
    <td class="date">{{mb_field object=$rpu field="_entree" form="editRPU" register=true}}</td>

	  {{if $dPconfig.dPurgences.old_rpu == "1"}}
    <th>{{mb_label object=$rpu field="urprov"}}</th>
    <td>{{mb_field object=$rpu field="urprov" defaultOption="&mdash; Provenance"}}</td>
		{{else}}
    <th>{{mb_label object=$rpu field="provenance"}}</th>
    <td>{{mb_field object=$rpu field="provenance" defaultOption="&mdash; Provenance"}}</td>
	  {{/if}}
  </tr>

  <tr>
	  <th>
	    <input type="hidden" name="_patient_id" class="{{$sejour->_props.patient_id}}" ondblclick="PatSelector.init()" value="{{$rpu->_patient_id}}" />
	    {{mb_label object=$rpu field="_patient_id"}}
	  </th>
	  <td class="readonly">
	  	<input type="text" name="_patient_view" size="20" value="{{$patient->_view}}" ondblclick="PatSelector.init()" readonly="readonly" />
	   
	    <button type="button" class="search" onclick="PatSelector.init()">Choisir un patient</button>
	    <script type="text/javascript">
	      PatSelector.init = function(){
	        this.sForm = "editRPU";
	        this.sId   = "_patient_id";
	        this.sView = "_patient_view";
	        this.pop();
	      }
	    </script>
	    
    </td>
    
    <th>{{mb_label object=$rpu field="transport"}}</th>
    <td>{{mb_field object=$rpu field="transport" defaultOption="&mdash; Type de transport"}}</td>
  </tr>
  
  <tr>
	  {{if $can->edit}}
    <th>{{mb_label object=$rpu field="ccmu"}}</th>
    <td>{{mb_field object=$rpu field="ccmu" defaultOption="&mdash; Code CCMU"}}</td>
	  {{/if}}
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
    <td id="radio">
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
</ul>

<hr class="control_tabs" />

<div id="antecedents">
  {{assign var="current_m" value="dPurgences"}}
  {{assign var="_is_anesth" value="0"}}
  {{assign var="consult" value=$rpu->_ref_consult}}
  {{assign var=sejour_id value=""}}
  {{include file="../../dPcabinet/templates/inc_ant_consult.tpl" chir_id=$rpu->_responsable_id}}
</div>

<div id="suivisoins" style="display:none"></div>
<div id="constantes" style="display:none"></div>

{{/if}}

<script type="text/javascript">

// Lancement des fonctions de contraintes entre les champs
{{if $rpu->mode_entree}}
ContraintesRPU.updateProvenance("{{$rpu->mode_entree}}");
{{/if}}

</script>