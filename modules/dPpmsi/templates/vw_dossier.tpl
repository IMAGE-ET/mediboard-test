<!-- $Id$ -->

{{include file="../../dPfiles/templates/inc_files_functions.tpl"}}
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
  var url = new Url();
  url.setModuleAction("dPcompteRendu", "print_cr");
  url.addParam("compte_rendu_id", doc_id);
  url.popup(800, 800, "Compte-rendu");
}

function exporterHPRIM(object_id, typeObject, oOptions) {
  oDefaultOptions = {
  	onlySentFiles : false
  };
  
  Object.extend(oDefaultOptions, oOptions);
  
  var url = new Url();
  url.setModuleAction("dPpmsi", "export_evtServeurActivitePmsi");
  url.addParam("object_id", object_id);
  url.addParam("typeObject", typeObject);
  url.addParam("sent_files", oDefaultOptions.onlySentFiles ? 1 : 0);
  
  oRequestOptions = {
    waitingText: oDefaultOptions.onlySentFiles ? 
  	  "Chargement des fichers envoyés" : 
  	  "Export H'XML vers Sa@nté.com"
  }
  
  url.requestUpdate("hprim_export_" + typeObject + object_id, oRequestOptions); 
}

var ExtRefManager = {
  sejour_id: null,
  
  submitIPPForm: function() {
    var oForm = document.forms.editIPP;
    return onSubmitFormAjax(oForm, {onComplete: ExtRefManager.reloadIPPForm});
  },
  
  reloadIPPForm: function() {
    var url = new Url();
    url.setModuleAction("dPpmsi", "httpreq_ipp_form");
    url.addParam("pat_id", '{{$patient->_id}}');
    url.requestUpdate("IPP", {waitingText: null });
  },
  
  submitNumdosForm: function(sejour_id) {
    ExtRefManager.sejour_id = sejour_id;
    var oForm = document.forms["editNumdos" + this.sejour_id];
    return onSubmitFormAjax(oForm, {onComplete: ExtRefManager.reloadNumdosForm});
  },

  reloadNumdosForm: function() {
    var url = new Url();
    url.setModuleAction("dPpmsi", "httpreq_numdos_form");
    url.addParam("sejour_id", ExtRefManager.sejour_id);
    url.requestUpdate("Numdos" + ExtRefManager.sejour_id, {waitingText: null });
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

function printFeuilleBloc(oper_id) {
  var url = new Url;
  url.setModuleAction("dPsalleOp", "print_feuille_bloc");
  url.addParam("operation_id", oper_id);
  url.popup(700, 600, 'FeuilleBloc');
}

function reloadDiagnostic(sejour_id, modeDAS) {
  var urlDiag = new Url();
  urlDiag.setModuleAction("dPpmsi", "httpreq_diagnostic");
  urlDiag.addParam("sejour_id", sejour_id);
  urlDiag.addParam("modeDAS", modeDAS);
  urlDiag.requestUpdate("cim-"+sejour_id, { waitingText : null } );
  var urlListDiag = new Url();
  urlListDiag.setModuleAction("dPpmsi", "httpreq_list_diags");
  urlListDiag.addParam("sejour_id", sejour_id);
  urlListDiag.requestUpdate("cim-list-"+sejour_id, { 
		waitingText : null
  } );
  var urlGHM = new Url();
  urlGHM.setModuleAction("dPpmsi", "httpreq_vw_GHM");
  urlGHM.addParam("sejour_id", sejour_id);
  urlGHM.requestUpdate("GHM-"+sejour_id, { 
		waitingText : null
  } );
}

function reloadListActes(operation_id) {
  var urlActes = new Url();
  urlActes.setModuleAction("dPpmsi", "httpreq_list_actes");
  urlActes.addParam("operation_id", operation_id);
  urlActes.requestUpdate("modifActes-"+operation_id, { 
		waitingText : null
  } );
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

Main.add(function () {
  PairEffect.initGroup("effectSejour");
});
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
    <td>
      <form name="patFrm" action="?" method="get">
      <table class="form">
        <tr>
          <th><label for="patNom" title="Merci de choisir un patient pour voir son dossier">Choix du patient</label></th>
          <td>
            <input type="hidden" name="m" value="dPpmsi" />
            <input type="hidden" name="pat_id" value="{{$patient->patient_id}}" onchange="this.form.submit()" />
            <input type="text" readonly="readonly" name="patNom" value="{{$patient->_view}}" />
          </td>
          <td class="button">
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
      {{if $patient->patient_id}}
      <div id="vwPatient">
      {{include file="../../dPpatients/templates/inc_vw_patient.tpl"}}
      </div>
      {{/if}}
    </td>
    {{if $patient->patient_id}}
    <td>
      <table class="form">
      {{if $patient->_ref_IPP}}
        <tr>
          <td colspan="4" id="IPP">
            {{include file="inc_ipp_form.tpl"}}
          </td>
        </tr>
        {{/if}}
        <tr>
          <th colspan="4" class="title">Liste des séjours</th>
        </tr>
        {{foreach from=$patient->_ref_sejours item=_sejour}}
        <tr id="sejour{{$_sejour->sejour_id}}-trigger">
          <td colspan="4" style="background-color:#aaf;">
          	Dr {{$_sejour->_ref_praticien->_view}}
						&mdash;
						{{mb_include module=system template=inc_interval_datetime from=$_sejour->entree_prevue to=$_sejour->sortie_prevue}}
          </td>
        </tr>
        <tbody class="effectSejour" id="sejour{{$_sejour->sejour_id}}">
        <tr>
          <td colspan="4" id="Numdos{{$_sejour->sejour_id}}" class="text">
            {{include file="inc_numdos_form.tpl"}}
          </td>
        </tr>
        <tr>
          <th class="category" colspan="4">
            <a style="float: right" title="Modifier le séjour" href="?m=dPplanningOp&amp;tab=vw_edit_sejour&amp;sejour_id={{$_sejour->_id}}">
              <img src="images/icons/planning.png" alt="Planifier" />
            </a>
            <a style="float: right" title="Modifier les diagnostics" href="?m=dPpmsi&amp;tab=labo_groupage&amp;sejour_id={{$_sejour->_id}}">
              <img src="images/icons/edit.png" alt="Planifier" />
            </a>
            {{$_sejour->_view}}
          </th>
        </tr>
        <tr>
          <td class="text halfPane" colspan="2">
            <div id="cim-{{$_sejour->_id}}">
            {{assign var="sejour" value=$_sejour}}
            {{include file="inc_diagnostic.tpl"}}
            </div>
          </td>
          <td class="text halfPane" colspan="2">
            <div id="GHM-{{$_sejour->_id}}">
            {{include file="inc_vw_GHM.tpl"}}
            </div>
          </td>
        </tr>
        <tr>
          <td colspan="4" id="hprim_export_sej{{$sejour->_id}}">
          </td>
        </tr>
        <tr>
          <th class="category" colspan="2">Diagnostics CIM</th>
          <th class="category" colspan="2">Antécedents</th>
        </tr>
        <tr>
          <td class="text" colspan="2">
            <div id="cim-list-{{$_sejour->_id}}">
              {{include file="inc_list_diags.tpl"}}
		        </div>
          </td>
          <td class="text" colspan="2" {{if is_array($patient->_ref_dossier_medical->_ref_traitements)}}rowspan="3"{{/if}}>
            <ul>
              <li>Du patient
                <ul>
                  {{foreach from=$patient->_ref_dossier_medical->_ref_antecedents key=curr_type item=list_antecedent}}
                  {{if $list_antecedent|@count}}
                  <li>
                    {{tr}}CAntecedent.type.{{$curr_type}}{{/tr}}
                    {{foreach from=$list_antecedent item=curr_antecedent}}
                    <ul>
                      <li>
                        {{if $curr_antecedent->date}}
                          {{mb_value object=$curr_antecedent field=date}} -
                        {{/if}}
                        <em>{{$curr_antecedent->rques}}</em>
                      </li>
                    </ul>
                    {{/foreach}}
                  </li>
                  {{/if}}
                  {{foreachelse}}
                  <li>Pas d'antécédents</li>
                  {{/foreach}}
                </ul>
              </li>
              <li>Significatifs du séjour
                <ul>
                  {{foreach from=$_sejour->_ref_dossier_medical->_ref_antecedents key=curr_type item=list_antecedent}}
                  {{if $list_antecedent|@count}}
                  <li>
                    {{tr}}CAntecedent.type.{{$curr_type}}{{/tr}}
                    {{foreach from=$list_antecedent item=curr_antecedent}}
                    <ul>
                      <li>
                        {{if $curr_antecedent->date}}
                          {{mb_value object=$curr_antecedent field=date}} -
                        {{/if}}
                        <em>{{$curr_antecedent->rques}}</em>
                      </li>
                    </ul>
                    {{/foreach}}
                  </li>
                  {{/if}}
                  {{foreachelse}}
                  <li>Pas d'antécédents</li>
                  {{/foreach}}
                </ul>
              </li>
            </ul>
          </td>
        </tr>
        
        {{if is_array($patient->_ref_dossier_medical->_ref_traitements)}}
        <tr>
          <th class="category" colspan="2">Traitements</th>
        </tr>
        <tr>
          <td class="text" colspan="2">
            <ul>
              <li>Du patient
                <ul>
		              {{foreach from=$patient->_ref_dossier_medical->_ref_traitements item=curr_trmt}}
		              <li>
                    {{if $curr_trmt->fin}}
                      Depuis {{mb_value object=$curr_trmt field=debut}} 
                      jusqu'à {{mb_value object=$curr_trmt field=fin}} :
                    {{elseif $curr_trmt->debut}}
                      Depuis {{mb_value object=$curr_trmt field=debut}} :
                    {{/if}}
		                <em>{{$curr_trmt->traitement}}</em>
		              </li>
		              {{foreachelse}}
		              <li>Pas de traitements</li>
		              {{/foreach}}
		            </ul>
		          </li>
              <li>Significatifs du séjour
                <ul>
		              {{foreach from=$_sejour->_ref_dossier_medical->_ref_traitements item=curr_trmt}}
		              <li>
                    {{if $curr_trmt->fin}}
                      Depuis {{mb_value object=$curr_trmt field=debut}} 
                      jusqu'à {{mb_value object=$curr_trmt field=fin}} :
                    {{elseif $curr_trmt->debut}}
                      Depuis {{mb_value object=$curr_trmt field=debut}} :
                    {{/if}}
		                <em>{{$curr_trmt->traitement}}</em>
		              </li>
		              {{foreachelse}}
		              <li>Pas de traitements</li>
		              {{/foreach}}
		            </ul>
		          </li>
            </ul>
          </td>
        </tr>
        {{/if}}
       
        {{foreach from=$_sejour->_ref_operations item=curr_op}}
        <tr>
          <th class="category" colspan="4">
            Intervention par le Dr {{$curr_op->_ref_chir->_view}}
            &mdash; {{$curr_op->_datetime|date_format:$dPconfig.longdate}}
            &mdash; 
            {{if $curr_op->salle_id}}
              {{$curr_op->_ref_salle->nom}}
            {{else}}
              Salle inconnue
            {{/if}}
          </th>
        </tr>
        {{if $curr_op->libelle}}
        <tr>
          <th>Libellé</th>
          <td colspan="3" class="text"><em>{{$curr_op->libelle}}</em></td>
        {{/if}}
        {{foreach from=$curr_op->_ext_codes_ccam item=curr_code}}
        <tr>
          <th>{{$curr_code->code}}</th>
          <td class="text" colspan="3">{{$curr_code->libelleLong}}</td>
        </tr>
        {{/foreach}}
        <tr>
          <th>{{mb_label object=$curr_op field=anapath}}</th>
          <td colspan="3">{{mb_value object=$curr_op field=anapath}}</td>
        </tr>
        <tr>
          <th>{{mb_label object=$curr_op field=labo}}</th>
          <td colspan="3">{{mb_value object=$curr_op field=labo}}</td>
        </tr>
        <tr>
          <th>{{mb_label object=$curr_op field=prothese}}</th>
          <td colspan="3">{{mb_value object=$curr_op field=prothese}}</td>
        </tr>
        {{if $curr_op->_ref_consult_anesth->consultation_anesth_id}}
        <tr>
          <td class="button" colspan="4">Consultation de pré-anesthésie le {{$curr_op->_ref_consult_anesth->_ref_plageconsult->date|date_format:"%A %d %b %Y"}}
            avec le Dr {{$curr_op->_ref_consult_anesth->_ref_plageconsult->_ref_chir->_view}}
          </td>
        </tr>
        {{assign var=const_med value=$curr_op->_ref_consult_anesth->_ref_consultation->_ref_patient->_ref_constantes_medicales}}
        <tr>
          <td class="button">Poids</td>
          <td class="button">Taille</td>
          <td class="button">Groupe</td>
          <td class="button">Tension</td>
        </tr>
        <tr>
          <td class="button">{{$const_med->poids}} kg</td>
          <td class="button">{{$const_med->taille}} cm</td>
          <td class="button">{{tr}}CConsultAnesth.groupe.{{$curr_op->_ref_consult_anesth->groupe}}{{/tr}} {{tr}}CConsultAnesth.rhesus.{{$curr_op->_ref_consult_anesth->rhesus}}{{/tr}}</td>
          <td class="button">{{$const_med->_ta_systole}}/{{$const_med->_ta_diastole}}</td>
        </tr>
        {{/if}}
        <tr>
          <td class="button" colspan="4">
            <button class="print" onclick="printFeuilleBloc({{$curr_op->operation_id}})">
              Imprimer la feuille de bloc
            </button>
            <br />
            <a class="button edit" href="?m=dPpmsi&amp;tab=edit_actes&amp;operation_id={{$curr_op->operation_id}}">
              Modifier les actes
            </a>
          </td>
        </tr>
        <tr>
          <td colspan="4" id="modifActes-{{$curr_op->_id}}">
            {{include file="inc_confirm_actes_ccam.tpl"}}
          </td>
        </tr>
        <tr>
          <td colspan="4">
            <form name="editOpFrm{{$curr_op->_id}}" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">
            <input type="hidden" name="dosql" value="do_planning_aed" />
            <input type="hidden" name="m" value="dPplanningOp" />
            <input type="hidden" name="del" value="0" />
            <input type="hidden" name="operation_id" value="{{$curr_op->operation_id}}" />
 
            <table class="form">
              <tr>
                <th class="category" colspan="2">
                  <em>Lien S@nté.com</em> : Intervention
                </th>
              </tr>

              <tr>
                <th><label for="_cmca_uf_preselection" title="Choisir une pré-selection pour remplir les unités fonctionnelles">Pré-sélection</label></th>
                <td>
                  <select name="_cmca_uf_preselection" onchange="choosePreselection(this)">
                    <option value="">&mdash; Choisir une pré-selection</option>
                    <option value="ABS|ABSENT">(ABS) Absent</option>
                    <option value="AEC|ARRONDI EURO">(AEC) Arrondi Euro</option>
                    <option value="AEH|ARRONDI EURO">(AEH) Arrondi Euro</option>
                    <option value="AMB|CHIRURGIE AMBULATOIRE">(AMB) Chirurgie Ambulatoire</option>
                    <option value="CHI|CHIRURGIE">(CHI) Chirurgie</option>
                    <option value="CHO|CHIRURGIE COUTEUSE">(CHO) Chirurgie Coûteuse</option>
                    <option value="EST|ESTHETIQUE">(EST) Esthétique</option>
                    <option value="EXL|EXL POUR RECUP V4 V5">(EXL) EXL pour récup. v4 v5</option>
                    <option value="EXT|EXTERNES">(EXT) Externes</option>
                    <option value="MED|MEDECINE">(MED) Médecine</option>
                    <option value="PNE|PNEUMOLOGUE">(PNE) Pneumologie</option>
                    <option value="TRF|TRANSFERT >48H">(TRF) Transfert > 48h</option>
                    <option value="TRI|TRANSFERT >48H">(TRI) Transfert > 48h</option>
                  </select>
                </td>
              </tr>

              <tr>
                <th>
                  <label for="code_uf" title="Choisir un code pour l'unité fonctionnelle">Code d'unité fonct.</label>
                </th>
                <td>
                  <input type="text" class="notNull {{$curr_op->_props.code_uf}}" name="code_uf" value="{{$curr_op->code_uf}}" size="10" maxlength="10" />
                </td>
              </tr>

              <tr>
                <th>
                  <label for="libelle_uf" title="Choisir un libellé pour l'unité fonctionnelle">Libellé d'unité fonct.</label>
                </th>
                <td>
                  <input type="text" class="notNull {{$curr_op->_props.libelle_uf}}" name="libelle_uf" value="{{$curr_op->libelle_uf}}" size="20" maxlength="35" onchange="submitOpForm({{$curr_op->_id}})" />
                </td>
              </tr>

              <tr>
                <td colspan="2" id="updateOp{{$curr_op->operation_id}}" />
              </tr>

            </table>

            </form>
          </td>
        </tr>

        <tr>
          <td colspan="2">
            <button class="tick" onclick="exporterHPRIM({{$curr_op->_id}}, 'op')">Export S@nté.com</button>
          </td>
          <td colspan="2" class="text">
            {{if $curr_op->_ref_hprim_files|@count}}
            <div class="small-success">
              Export déjà effectué {{$curr_op->_ref_hprim_files|@count}} fois
            </div>
            {{else}}
            <div class="small-info">
              Pas d'export effectué
            </div>
            {{/if}}
          </td>
        </tr>
        <tr>
          <td class="text" id="hprim_export_op{{$curr_op->_id}}" colspan="4">
          </td>
        </tr>
        
        <tr>
          <td colspan="4">Documents attachés :</td>
        </tr>
        <tr>
          <td colspan="4" id="File{{$curr_op->_class_name}}{{$curr_op->_id}}">
            <a href="#1" onclick="setObject( {
              objClass: '{{$curr_op->_class_name}}', 
              keywords: '', 
              id: {{$curr_op->operation_id|smarty:nodefaults|JSAttribute}}, 
              view:'{{$curr_op->_view|smarty:nodefaults|JSAttribute}}'} )">
              Voir les Documents ({{$curr_op->_nb_files_docs}})
            </a>
          </td>
        </tr>
        {{/foreach}}
        </tbody>
        {{/foreach}}

       </table>
     </td>
     {{/if}}
    
  </tr>
</table>

