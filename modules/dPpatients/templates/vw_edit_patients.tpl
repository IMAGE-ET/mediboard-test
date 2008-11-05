<!-- $Id$ -->

{{mb_include_script module="dPpatients" script="autocomplete"}}
{{mb_include_script module="dPpatients" script="siblings_checker"}}

{{include file="../../dPpatients/templates/inc_intermax.tpl"}}

<script type="text/javascript">

Intermax.ResultHandler["Consulter Vitale"] =
Intermax.ResultHandler["Lire Vitale"] = function() {
  var url = new Url;
//  url.setModuleTab("dPpatients", "vw_edit_patients");
  url.addParam("m", "dPpatients");
  url.addParam("{{$actionType}}",  "vw_edit_patients");
  url.addParam("dialog",  "{{$dialog}}");
  url.addParam("useVitale", 1);
  url.redirect();
}

function copyAssureValues(element) {
	// Hack pour gérer les form fields
	var sPrefix = element.name[0] == "_" ? "_assure" : "assure_";
  eOther = element.form[sPrefix + element.name];
  
  $V(eOther, $V(element));
  eOther.fire("mask:check");
}

function copyIdentiteAssureValues(element) {
	if (element.form.rang_beneficiaire.value == "01") {
		copyAssureValues(element);
	}
}

function confirmCreation(oForm){
  if (!checkForm(oForm)) {
    return false;
  }
  
  SiblingsChecker.request();
  return false;
}

function printPatient(id) {
  var url = new Url();
  url.setModuleAction("dPpatients", "print_patient");
  url.addParam("patient_id", id);
  url.popup(700, 550, "Patient");
}

var tabs;
Main.add(function () {
  initInseeFields("editFrm", "cp", "ville","pays");
  initInseeFields("editFrm", "prevenir_cp", "prevenir_ville");
  initInseeFields("editFrm", "employeur_cp", "employeur_ville");
  initPaysField("editFrm", "pays", "tel");
  
  initInseeFields("editFrm", "assure_cp", "assure_ville","assure_pays");
  initPaysField("editFrm", "assure_pays","assure_tel");
  
  tabs = new Control.Tabs('tab-patient');
});

</script>

{{if $patient->_id}}
<a class="buttonnew" href="?m={{$m}}&amp;{{$actionType}}={{$action}}&amp;dialog={{$dialog}}&amp;patient_id=0">Créer un nouveau patient</a></td>
{{/if}}
<table class="main">
  <tr>
  {{if $patient->_id}}
    <th class="title modify" colspan="5">
      {{if $app->user_prefs.GestionFSE}}
	      <button class="search" type="button" onclick="Intermax.trigger('Lire Vitale');" style="float: left;">
	        Lire Vitale
	      </button>
	      {{if $patient->_id_vitale}}
	      <button class="search" type="button" onclick="Intermax.Triggers['Consulter Vitale']({{$patient->_id_vitale}});" style="float: left;">
	        Consulter Vitale
	      </button>
	      {{/if}}
	      <button class="change intermax-result" type="button" onclick="Intermax.result();" style="float: left;">
	        Résultat Vitale
	      </button>
			{{/if}}
    
			{{if $patient->_id_vitale}}
      <div style="float:right;">
	      <img src="images/icons/carte_vitale.png" alt="lecture vitale" title="Bénéficiaire associé à une carte Vitale" />
      </div>
      {{/if}}
      
      <div class="idsante400" id="CPatient-{{$patient->_id}}"></div>
          
      <a style="float:right;" href="#" onclick="view_log('CPatient',{{$patient->_id}})">
        <img src="images/icons/history.gif" alt="historique" />
      </a>
      Modification du dossier de {{$patient->_view}} 
      {{if $patient->_IPP}}[{{$patient->_IPP}}]{{/if}}
      {{if $patient->_bind_vitale}}{{tr}}UseVitale{{/tr}}{{/if}}
    </th>
  {{else}}
    <th class="title" colspan="5">
      {{if $app->user_prefs.GestionFSE}}
	      <button class="search" type="button" onclick="Intermax.trigger('Lire Vitale');" style="float: left;">
	        Lire Vitale
	      </button>
	      <button class="change intermax-result" type="button" onclick="Intermax.result();" style="float: left;">
	        Résultat Vitale
	      </button>
			{{/if}}
			{{tr}}Create{{/tr}}
      {{if $patient->_bind_vitale}}{{tr}}UseVitale{{/tr}}{{/if}}
    </th>
  {{/if}}
  </tr>
  
  <tr>
    <td colspan="5">
      <ul id="tab-patient" class="control_tabs">
        <li><a href="#identite">Identité</a></li>
        <li><a href="#beneficiaire">Bénéficiaire de soins</a></li>
        <li><a href="#medecins">Médecins correspondants</a></li>
        <li><a href="#correspondance">Correspondance</a></li>
        <li><a href="#assure">Assuré social</a></li>
      </ul>
      <hr class="control_tabs" />
      
      <form name="editFrm" action="?m={{$m}}" method="post" onsubmit="return confirmCreation(this)">
        <input type="hidden" name="dosql" value="do_patients_aed" />
        <input type="hidden" name="del" value="0" />
        {{if $patient->_bind_vitale}}
        <input type="hidden" name="_bind_vitale" value="1" />
        {{/if}}
        
        {{mb_field object=$patient field="patient_id" hidden=1 prop=""}}
        {{if $dialog}}
        <input type="hidden" name="dialog" value="{{$dialog}}" />
        {{/if}}
        
        <div id="identite" style="display: none;">{{include file="inc_acc/inc_acc_identite.tpl"}}</div>
        <div id="beneficiaire" style="display: none;">{{include file="inc_acc/inc_acc_beneficiaire.tpl"}}</div>
        <div id="correspondance" style="display: none;">{{include file="inc_acc/inc_acc_corresp.tpl"}}</div>
        <div id="assure" style="display: none;">{{include file="inc_acc/inc_acc_assure.tpl"}}</div>
      </form>
      
      <div id="medecins" style="display: none;">{{include file="inc_acc/inc_acc_medecins.tpl"}}</div>
    </td>
  </tr>
  
  <tr>
    <td class="button" colspan="5" style="text-align:center;" id="button">
      <div id="divSiblings" style="display:none;"></div>
      {{if $patient->_id}}
        <button tabindex="400" type="submit" class="submit" onclick="return document.forms.editFrm.onsubmit();">
          {{tr}}Modify{{/tr}}
          {{if $patient->_bind_vitale}}
          &amp; {{tr}}BindVitale{{/tr}}
          {{/if}}
        </button>
        <button type="button" class="print" onclick="printPatient({{$patient->patient_id}})">
          {{tr}}Print{{/tr}}
        </button>
        <button type="button" class="trash" onclick="confirmDeletion(this.form,{typeName:'le patient',objName:'{{$patient->_view|smarty:nodefaults|JSAttribute}}'})">
          {{tr}}Delete{{/tr}}
        </button>
      {{else}}
        <button tabindex="400" type="submit" class="submit" onclick="return document.forms.editFrm.onsubmit();">
          {{tr}}Create{{/tr}}
          {{if $patient->_bind_vitale}}
          &amp; {{tr}}BindVitale{{/tr}}
          {{/if}}
        </button>
      {{/if}}
    </td>
  </tr>
</table>
