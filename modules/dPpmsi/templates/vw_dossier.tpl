<!-- $Id$ -->

{{include file="../../dPfiles/templates/inc_files_functions.tpl"}}
{{mb_include_script module="dPpatients" script="pat_selector"}}

<script type="text/javascript">

function choosePreselection(oSelect) {
  if (!oSelect.value) { 
    return;
  }
  
  var aParts = oSelect.value.split("|");
  var sLibelle = aParts.pop();
  var sCode = aParts.pop();

  var oForm = oSelect.form;
  oForm.code_uf.value = sCode;
  oForm.libelle_uf.value = sLibelle;
  
  oSelect.value = "";
}

function imprimerDocument(doc_id) {
  var url = new Url();
  url.setModuleAction("dPcompteRendu", "print_cr");
  url.addParam("compte_rendu_id", doc_id);
  url.popup(700, 600, "Compte-rendu");
}

function submitSHSLink() {
  var oPatForm = document.editPatFrm;
  //debugObject(oPatForm);
  var oOpForm = document.editOpFrm;
  //submitFormAjax(oPatForm, 'systemMsg');
  submitFormAjax(oOpForm, 'systemMsg');
}

function exporterDossier(operation_id, oOptions) {
  oDefaultOptions = {
  	onlySentFiles : false
  };
  
  Object.extend(oDefaultOptions, oOptions);
  
  var url = new Url();
  url.setModuleAction("dPinterop", "export_hprim");
  url.addParam("operation_id", operation_id);
  url.addParam("sent_files", oDefaultOptions.onlySentFiles ? 1 : 0);
  
  oRequestOptions = {
    waitingText: oDefaultOptions.onlySentFiles ? 
  	  "Chargement des fichers envoyés" : 
  	  "Export H'XML vers Sa@nté.com"
  }
  
  url.requestUpdate("hprim_export" + operation_id, oRequestOptions); 
}

function submitPatForm(operation_id) {
  var oForm = document.forms["editPatFrm" + operation_id];
  var iTarget = "updatePat" + operation_id;
  submitFormAjax(oForm, iTarget);
}

function submitSejourForm(operation_id) {
  var oForm = document.forms["editSejourFrm" + operation_id];
  var iTarget = "updateSejour" + operation_id;
  submitFormAjax(oForm, iTarget);
}

function submitOpForm(operation_id) {
  var oForm = document.forms["editOpFrm" + operation_id];
  var iTarget = "updateOp" + operation_id;
  submitFormAjax(oForm, iTarget);
}

function submitAllForms(operation_id) {
  submitPatForm(operation_id);
  submitSejourForm(operation_id);
  submitOpForm(operation_id);
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

function pageMain() {
  PairEffect.initGroup("effectSejour");
}
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
      <form name="patFrm" action="index.php" method="get">
      <table class="form">
        <tr>
          <th><label for="patNom" title="Merci de choisir un patient pour voir son dossier">Choix du patient</label></th>
          <td class="readonly">
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
        <tr>
          <th colspan="4" class="title">Liste des séjours</th>
        </tr>
        {{foreach from=$patient->_ref_sejours item=curr_sejour}}
        {{assign var="GHM" value=$curr_sejour->_ref_GHM}}
        <tr id="sejour{{$curr_sejour->sejour_id}}-trigger">
          <td colspan="4" style="background-color:#aaf;">
          	Dr. {{$curr_sejour->_ref_praticien->_view}} -
          	Séjour du {{$curr_sejour->entree_prevue|date_format:"%d %b %Y (%Hh%M)"}}
          	au {{$curr_sejour->sortie_prevue|date_format:"%d %b %Y (%Hh%M)"}}
          </td>
        </tr>
        <tbody class="effectSejour" id="sejour{{$curr_sejour->sejour_id}}">
        <tr>
          <th>Diagnostics du patient</th>
          <td class="text" colspan="3">
            <ul>
              {{foreach from=$patient->_codes_cim10 item=curr_code}}
              <li>
                {{$curr_code->code}} : {{$curr_code->libelle}}
              </li>
              {{foreachelse}}
              <li>Pas de diagnostic</li>
              {{/foreach}}
            </ul>
          </td>
        </tr>
        <tr>
          <th>Antécedents du patient</th>
          <td class="text" colspan="3">
            <ul>
              {{foreach from=$patient->_ref_antecedents key=curr_type item=list_antecedent}}
              {{if $list_antecedent|@count}}
		        <li>
		          {{tr}}CAntecedent.type.{{$curr_type}}{{/tr}}
		          {{foreach from=$list_antecedent item=curr_antecedent}}
		          <ul>
		            <li>
                      {{$curr_antecedent->date|date_format:"%d %b %Y"}} -
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
          </td>
        </tr>
        <tr>
          <th>Traitements du patient</th>
          <td class="text" colspan="3">
            <ul>
              {{foreach from=$patient->_ref_traitements item=curr_trmt}}
              <li>
                {{if $curr_trmt->fin}}
                  Du {{$curr_trmt->debut|date_format:"%d %b %Y"}} au {{$curr_trmt->fin|date_format:"%d %b %Y"}}
                {{else}}
                  Depuis le {{$curr_trmt->debut|date_format:"%d %b %Y"}}
                {{/if}}
                : <em>{{$curr_trmt->traitement}}</em>
              </li>
              {{foreachelse}}
              <li>Pas de traitements</li>
              {{/foreach}}
            </ul>
          </td>
        </tr>
        {{foreach from=$curr_sejour->_ref_operations item=curr_op}}
        <tr>
          <th class="category" colspan="4">
            Dr. {{$curr_op->_ref_chir->_view}}
            &mdash; {{$curr_op->_datetime|date_format:"%A %d %B %Y"}}
            &mdash; {{$curr_op->_ref_salle->nom}}
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
        {{if $curr_op->_ref_consult_anesth->consultation_anesth_id}}
        <tr>
          <th class="category" colspan="4">
            Consultation pré-anesthésique
          </th>
        </tr>
        <tr>
          <th>Consultation</th>
          <td class="text" colspan="3">
            Le {{$curr_op->_ref_consult_anesth->_ref_plageconsult->date|date_format:"%A %d %b %Y"}}
            avec le Dr. {{$curr_op->_ref_consult_anesth->_ref_plageconsult->_ref_chir->_view}}
          </td>
        </tr>
        <tr>
          <td class="button">Poids</td>
          <td class="button">Taille</td>
          <td class="button">Groupe</td>
          <td class="button">Tension</td>
        </tr>
        <tr>
          <td class="button">{{$curr_op->_ref_consult_anesth->poid}} kg</td>
          <td class="button">{{$curr_op->_ref_consult_anesth->taille}} cm</td>
          <td class="button">{{tr}}CConsultAnesth.groupe.{{$curr_op->_ref_consult_anesth->groupe}}{{/tr}} {{tr}}CConsultAnesth.rhesus.{{$curr_op->_ref_consult_anesth->rhesus}}{{/tr}}</td>
          <td class="button">{{$curr_op->_ref_consult_anesth->tasys}}/{{$curr_op->_ref_consult_anesth->tadias}}</td>
        </tr>
        {{/if}}
        <tr>
          <th class="category" colspan="4">
            Intervention
          </th>
        </tr>
        <tr>
          <td class="button" colspan="4">
            <button class="print" onclick="printFeuilleBloc({{$curr_op->operation_id}})">
              Imprimer la feuille de bloc
            </button>
          </td>
        </tr>
        <tr>
          <td colspan="4">
            <form name="editPatFrm{{$curr_op->operation_id}}" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">

            <input type="hidden" name="dosql" value="do_patients_aed" />
            <input type="hidden" name="m" value="dPpatients" />
            <input type="hidden" name="del" value="0" />
            <input type="hidden" name="patient_id" value="{{$patient->patient_id}}" />
 
            <table class="form">
              <tr>
                <th class="category" colspan="2">
                  <em>Lien S@nté.com</em> : Patient 
                  <button class="submit" type="button" onclick="submitPatForm({{$curr_op->operation_id}})">Valider</button>
                </th>
              </tr>

              <tr>
                <th>
                  <label for="SHS" title="Choisir un identifiant de patient correspondant à l'opération">Identifiant de patient</label>
                </th>
                <td>
                  <input type="text" class="notNull {{$patient->_props.SHS}}" name="SHS" value="{{$patient->SHS}}" size="8" maxlength="8" />
                </td>
              </tr>

              <tr>
                <td colspan="2" id="updatePat{{$curr_op->operation_id}}" />
              </tr>
            </table>
 
            </form>
 
            <form name="editSejourFrm{{$curr_op->operation_id}}" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">
            
            <input type="hidden" name="dosql" value="do_sejour_aed" />
            <input type="hidden" name="m" value="dPplanningOp" />
            <input type="hidden" name="del" value="0" />
            <input type="hidden" name="sejour_id" value="{{$curr_sejour->sejour_id}}" />
 
            <table class="form">
              <tr>
                <th class="category" colspan="2">
                  <em>Lien S@nté.com</em> : Séjour
                  <button class="submit" type="button" onclick="submitSejourForm({{$curr_op->operation_id}})">Valider</button>
                </th>
              </tr>

              <tr>
                <th>
                  <label for="venue_SHS" title="Choisir un identifiant pour la venue correspondant à l'opération">Identifiant de venue</label>
                  <br />Suggestion :
                </th>
                <td>
                  <input type="text" class="notNull {{$curr_sejour->_props.venue_SHS}}" name="venue_SHS" value="{{$curr_sejour->venue_SHS}}" size="8" maxlength="8" />
                  <br />{{$curr_sejour->_venue_SHS_guess}}
                </td>
              </tr>

              <tr>
                <td colspan="2" id="updateSejour{{$curr_op->operation_id}}" />
              </tr>
            </table>

            </form>
            
            <form name="editOpFrm{{$curr_op->operation_id}}" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">
            <input type="hidden" name="dosql" value="do_planning_aed" />
            <input type="hidden" name="m" value="dPplanningOp" />
            <input type="hidden" name="del" value="0" />
            <input type="hidden" name="operation_id" value="{{$curr_op->operation_id}}" />
 
            <table class="form">
              <tr>
                <th class="category" colspan="2">
                  <em>Lien S@nté.com</em> : Intervention
                  <button class="submit" type="button" onclick="submitOpForm({{$curr_op->operation_id}})">Valider</button>
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
                <th><label for="code_uf" title="Choisir un code pour l'unité fonctionnelle">Code d'unité fonct.</label></th>
                <td><input type="text" class="notNull {{$curr_op->_props.code_uf}}" name="code_uf" value="{{$curr_op->code_uf}}" size="10" maxlength="10" /></td>
              </tr>

              <tr>
                <th><label for="libelle_uf" title="Choisir un libellé pour l'unité fonctionnelle">Libellé d'unité fonct.</label></th>
                <td><input type="text" class="notNull {{$curr_op->_props.libelle_uf}}" name="libelle_uf" value="{{$curr_op->libelle_uf}}" size="20" maxlength="35" /></td>
              </tr>

              <tr>
                <td colspan="2" id="updateOp{{$curr_op->operation_id}}" />
              </tr>

            </table>

            </form>
            
            <table class="form">
              <tr>
                <td class="button">
                  <button class="submit" type="button" onclick="submitAllForms({{$curr_op->operation_id}})" >Tout valider</button>
                </td>
              </tr>
            </table>
          </td>
        </tr>
        <tr>
          <td colspan="4">
            <table class="tbl">
              <tr>
                <th class="category">supprimer</th>
                <th class="category">Code</th>
                <th class="category">Activité</th>
                <th class="category">Phase &mdash; Modifs.</th>
                <th class="category">Association</th>
              </tr>
              {{foreach from=$curr_op->_ref_actes_ccam item=curr_acte}}
              <tr>
                <td class="button">
                  <form name="formActe-{{$curr_acte->_view}}" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">
                  <input type="hidden" name="m" value="dPsalleOp" />
                  <input type="hidden" name="dosql" value="do_acteccam_aed" />
                  <input type="hidden" name="del" value="0" />
                  <input type="hidden" name="acte_id" value="{{$curr_acte->acte_id}}" />
                  <button class="trash notext" type="button" onclick="confirmDeletion(this.form, {typeName:'l\'acte',objName:'{{$curr_acte->code_acte|smarty:nodefaults|JSAttribute}}'})">
                    {{tr}}Delete{{/tr}}
                  </button>
                  </form>
                </td>
                <td class="text">{{$curr_acte->_ref_executant->_view}} : {{$curr_acte->code_acte}}</td>
                <td class="button">{{$curr_acte->code_activite}}</td>
                <td class="button">
                  {{$curr_acte->code_phase}}
                  {{if $curr_acte->modificateurs}}
                    &mdash; {{$curr_acte->modificateurs}}
                  {{/if}}
                </td>
                <td>
                  {{$curr_acte->_guess_association}}
                </td>
              </tr>
              {{/foreach}}
            </table>
          </td>
        </tr>

        <tr>
          <td class="button" colspan="4">
            <a class="buttonedit" href="?m=dPpmsi&amp;tab=edit_actes&amp;operation_id={{$curr_op->operation_id}}">
              Modifier les actes
            </a>
            <button class="tick" onclick="exporterDossier({{$curr_op->operation_id}})">Exporter vers S@nté.com</button>
          </td>
        </tr>
        <tr>
          <td class="text" id="hprim_export{{$curr_op->operation_id}}" colspan="4">
          </td>
        </tr>
        
        <tr>
          <th>Documents attachés :</th>
          <td colspan="3" id="File{{$curr_op->_class_name}}{{$curr_op->_id}}">
            <a href="#" onclick="setObject( {
              objClass: '{{$curr_op->_class_name}}', 
              keywords: '', 
              id: {{$curr_op->operation_id|smarty:nodefaults|JSAttribute}}, 
              view:'{{$curr_op->_view|smarty:nodefaults|JSAttribute}}'} )">
              Voir les Documents ({{$curr_op->_nb_files_docs}})
            </a>
          </td>
        </tr>
        {{/foreach}}
        <tr>
          <th class="category" colspan="4">
            Groupage
            <a href="?m=dPpmsi&amp;tab=labo_groupage&amp;sejour_id={{$curr_sejour->sejour_id}}">
              (envoyer vers le labo)
            </a>
          </th>
        </tr>
        <tr>
          <td colspan="4">
            <form name="editFrm" action="?m={{$m}}" method="post">
            <input type="hidden" name="m" value="dPplanningOp" />
            <input type="hidden" name="dosql" value="do_sejour_aed" />
            <input type="hidden" name="del" value="0" />
            <input type="hidden" name="sejour_id" value="{{$curr_sejour->sejour_id}}" />
            Diagnostic principal :
            <input type="text" name="DP" value="{{$curr_sejour->DP}}"/>
            <button class="modify" type="submit">Valider</button>
            </form>
            {{if $curr_sejour->_ref_GHM->_CM}}
            <br />
            <strong>Catégorie majeure CM{{$GHM->_CM}}</strong> : {{$GHM->_CM_nom}}
            <br />
            <strong>GHM</strong> : {{$GHM->_GHM}} ({{$GHM->_tarif_2006}} €)
            <br />
            {{$GHM->_GHM_nom}}
            <br />
            <em>Appartenance aux groupes {{$GHM->_GHM_groupe}}</em>
            <br />
            <strong>Bornes d'hospitalisation</strong> :
            de {{$GHM->_borne_basse}}
            à {{$GHM->_borne_haute}} jours
            {{else}}
            <strong>{{$GHM->_GHM}}</strong>
            {{/if}}
          </td>
        </tr>
        </tbody>
        {{/foreach}}

       </table>
     </td>
     {{/if}}
    
  </tr>
</table>

