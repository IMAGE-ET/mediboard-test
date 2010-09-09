{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPpmsi
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{mb_include_script module="dPfiles" script="files"}}
{{mb_include_script module="dPpatients" script="pat_selector"}}
{{mb_include_script module="hprim21" script="pat_hprim_selector"}}
{{mb_include_script module="hprim21" script="sejour_hprim_selector"}}
{{mb_include_script module="dPplanningOp" script="cim10_selector"}}

<script type="text/javascript">
  
CIM10Selector.initDP = function(sejour_id){
  this.sForm = "editDP-"+sejour_id;
  this.sView = "DP";
  this.sChir = "_praticien_id";
  this.pop();
}

CIM10Selector.initDR = function(sejour_id){
  this.sForm = "editDR-"+sejour_id;
  this.sView = "DR";
  this.sChir = "_praticien_id";
  this.pop();
}

CIM10Selector.initDAS = function(sejour_id){
  this.sForm = "editDossierMedical-"+sejour_id;
  this.sView = "_added_code_cim";
  this.sChir = "_praticien_id";
  this.pop();
}

function choosePreselection(oSelect) {
  if (!oSelect.value) { 
    return;
  }
  
  var aParts = oSelect.value.split("|");
  var sLibelle = aParts.pop();
  var sCode = aParts.pop();

  var oForm = oSelect.form;
  $V(oForm.code_uf, sCode);
  $V(oForm.libelle_uf, sLibelle);
  
  oSelect.value = "";
}

function imprimerDocument(doc_id) {
  var url = new Url("dPcompteRendu", "print_cr");
  url.addParam("compte_rendu_id", doc_id);
  url.popup(800, 800, "Compte-rendu");
}

function exporterHPRIM(object_id, typeObject, oOptions) {
  var oDefaultOptions = {
  	onlySentFiles : false
  };
  
  Object.extend(oDefaultOptions, oOptions);
  
  var url = new Url("dPpmsi", "export_evtServeurActivitePmsi");
  url.addParam("object_id", object_id);
  url.addParam("typeObject", typeObject);
  url.addParam("sent_files", oDefaultOptions.onlySentFiles ? 1 : 0);
  
  var oRequestOptions = {
    waitingText: oDefaultOptions.onlySentFiles ? 
  	  "Chargement des fichers envoyés" : 
  	  "Export H'XML"
  };
  
  url.requestUpdate("hprim_export_" + typeObject + object_id, oRequestOptions); 
}

var ExtRefManager = {
  sejour_id: null,
  
  submitIPPForm: function() {
    var oForm = document.forms.editIPP;
    return onSubmitFormAjax(oForm, {onComplete: ExtRefManager.reloadIPPForm});
  },
  
  reloadIPPForm: function() {
    var url = new Url("dPpmsi", "httpreq_ipp_form");
    url.addParam("pat_id", '{{$patient->_id}}');
    url.requestUpdate("IPP");
  },
  
  submitNumdosForm: function(sejour_id) {
    ExtRefManager.sejour_id = sejour_id;
    var oForm = document.forms["editNumdos" + this.sejour_id];
    return onSubmitFormAjax(oForm, {onComplete: ExtRefManager.reloadNumdosForm});
  },

  reloadNumdosForm: function() {
    var url = new Url("dPpmsi", "httpreq_numdos_form");
    url.addParam("sejour_id", ExtRefManager.sejour_id);
    url.requestUpdate("Numdos" + ExtRefManager.sejour_id);
  }
}

function submitOpForm(operation_id) {
  var oForm = document.forms["editOpFrm" + operation_id];
  var iTarget = "updateOp" + operation_id;
  submitFormAjax(oForm, iTarget);
}

function ZoomAjax(objectClass, objectId, elementClass, elementId, sfn){
  popFile(objectClass, objectId, elementClass, elementId, sfn);
}

printFicheBloc = function(oper_id) {
  var url = new Url("dPsalleOp", "print_feuille_bloc");
  url.addParam("operation_id", oper_id);
  url.popup(700, 600, 'FeuilleBloc');
}

printFicheAnesth = function(consultation_id, operation_id) {
    var url = new Url;
    url.setModuleAction("dPcabinet", "print_fiche"); 
    url.addParam("consultation_id", consultation_id);
    url.addParam("operation_id", operation_id);
    url.popup(700, 500, "printFicheAnesth");
}

function reloadDiagnostic(sejour_id, modeDAS) {
  var urlDiag = new Url("dPpmsi", "httpreq_diagnostic");
  urlDiag.addParam("sejour_id", sejour_id);
  urlDiag.addParam("modeDAS", modeDAS);
  urlDiag.requestUpdate("cim-"+sejour_id);
	
  var urlListDiag = new Url("dPpmsi", "httpreq_list_diags");
  urlListDiag.addParam("sejour_id", sejour_id);
  urlListDiag.requestUpdate("cim-list-"+sejour_id);
	
  var urlGHM = new Url("dPpmsi", "httpreq_vw_GHM");
  urlGHM.addParam("sejour_id", sejour_id);
  urlGHM.requestUpdate("GHM-"+sejour_id);
}

function reloadListActes(operation_id) {
  var urlActes = new Url("dPpmsi", "httpreq_list_actes");
  urlActes.addParam("operation_id", operation_id);
  urlActes.requestUpdate("modifActes-"+operation_id);
}

function loadSejour(sejour_id) {
	var url = new Url("dPpmsi", "ajax_vw_sejour");
  url.addParam("sejour_id", sejour_id);
  url.requestUpdate("sejour");
}

SejourHprimSelector.doSet = function(){
  var oFormSejour = document[SejourHprimSelector.sForm];
  $V(oFormSejour[SejourHprimSelector.sId]  , SejourHprimSelector.prepared.id);
  ExtRefManager.submitNumdosForm(oFormSejour.object_id.value);
  if(SejourHprimSelector.prepared.IPPid) {
    var oFormIPP = document[SejourHprimSelector.sIPPForm];
    $V(oFormIPP[SejourHprimSelector.sIPPId]  , SejourHprimSelector.prepared.IPPid);
    ExtRefManager.submitIPPForm();
  }
}

printDossierComplet = function(sejour_id){
  var url = new Url("soins", "print_dossier_soins");
  url.addParam("sejour_id", sejour_id);
  url.popup("850", "500", "Dossier complet");
}

{{if $isSejourPatient}}
Main.add(function () {
  loadSejour({{$isSejourPatient}});
});
{{/if}}
</script>

<form name="FrmClass" action="?m={{$m}}" method="get" onsubmit="reloadListFileDossier('load'); return false;">
  <input type="hidden" name="selKey"   value="" />
  <input type="hidden" name="selClass" value="" />
  <input type="hidden" name="selView"  value="" />
  <input type="hidden" name="keywords" value="" />
  <input type="hidden" name="file_id"  value="" />
  <input type="hidden" name="typeVue"  value="0" />
</form>

<table class="main">
  <tr>
    <td style="width:1%">
      <form name="patFrm" action="?" method="get">
        <table class="form">
          <tr>
            <th><label for="patNom" title="Merci de choisir un patient pour voir son dossier">Choix du patient</label></th>
            <td>
              <input type="hidden" name="m" value="dPpmsi" />
              <input type="hidden" name="pat_id" value="{{$patient->patient_id}}" onchange="this.form.submit()" />
              <input type="text" readonly="readonly" name="patNom" value="{{$patient->_view}}" ondblclick="PatSelector.init()" />
              <button class="search" type="button" onclick="PatSelector.init()">Chercher</button>
              <script type="text/javascript">
              PatSelector.init = function(){
                this.sForm = "patFrm";
                this.sId   = "pat_id";
                this.sView = "patNom";
                this.pop();
              }
              </script>
            </td>
          </tr>
        </table>
      </form>
      {{if $patient->_id}}
      <div id="vwPatient">
        {{mb_include module=dPpatients template=inc_vw_identite_patient}}
      </div>
      <table class="form">
        <tr>
          <th class="category" colspan="2">Liste des séjours</th>
        </tr>
        {{foreach from=$patient->_ref_sejours item=_sejour}}
          {{if $_sejour->group_id == $g || $dPconfig.dPpatients.CPatient.multi_group == "full"}}
          <tr {{if $_sejour->_id == $isSejourPatient}}class="selected{{/if}}">
            <td class="text">
              {{mb_include module=dPplanningOp template=inc_vw_numdos num_dossier=$_sejour->_num_dossier}}
              <a href="#{{$_sejour->_guid}}" onclick="loadSejour('{{$_sejour->_id}}'); $(this).up('tr').addUniqueClassName('selected')">
                <span onmouseover="ObjectTooltip.createEx(this, '{{$_sejour->_guid}}')">
                  {{$_sejour->_shortview}}
                  {{if $_sejour->_nb_files_docs}}
                    - ({{$_sejour->_nb_files_docs}} Doc.)
                  {{/if}}
                </span>
              </a>
            </td>
            <td style="text-align: left;" {{if $_sejour->annule}}class="cancelled"{{/if}}>
              {{mb_include module=mediusers template=inc_vw_mediuser mediuser=$_sejour->_ref_praticien}}
            </td>
          </tr>
          {{foreach from=$_sejour->_ref_operations item=curr_op}}
          <tr>
            <td class="text" style="text-indent: 1em;">
              <span onmouseover="ObjectTooltip.createEx(this, '{{$curr_op->_guid}}')">
                Intervention le {{$curr_op->_datetime|date_format:$dPconfig.date}}
                {{if $curr_op->_nb_files_docs}}
                  - ({{$curr_op->_nb_files_docs}} Doc.)
                {{/if}}
              </span>
            </td>
            <td style="text-align: left;" {{if $curr_op->annulee}}class="cancelled"{{/if}}>
              {{mb_include module=mediusers template=inc_vw_mediuser mediuser=$curr_op->_ref_chir}}
            </td>
          </tr>
          {{/foreach}}
          {{elseif $dPconfig.dPpatients.CPatient.multi_group == "limited" && !$_sejour->annule}}
          <tr>
            <td>
              Le {{$_sejour->_datetime|date_format:$dPconfig.datetime}}
            </td>
            <td style="background-color:#afa">
              {{$_sejour->_ref_chir->_ref_function->_ref_group->text|upper}}
            </td>
          </tr>
          {{/if}}
        {{/foreach}}
      </table>
      {{/if}}
    </td>
    {{if $patient->_id}}
    <td id="sejour">
      {{mb_include module=dPpmsi template=inc_vw_sejour}}
    </td>
    {{/if}}
  </tr>
</table>