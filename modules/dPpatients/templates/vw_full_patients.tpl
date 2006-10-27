<!-- $Id: $ -->

{{include file="../../dPfiles/templates/inc_files_functions.tpl"}}

<script type="text/javascript">
function saveObjectInfos(oObject){
  var url = new Url;
  url.setModuleAction("dPpatients", "httpreq_do_savesession");
  url.addParam("selClass", oObject.objClass);
  url.addParam("selKey", oObject.id);
  url.requestUpdate('systemMsg', { waitingText : null });
}

function view_labo() {
  var url = new Url;
  url.setModuleAction("dPImeds", "httpreq_vw_patient_results");
  url.addParam("patient_id", "{{$patient->_id}}");
  url.requestUpdate('listView', { waitingText : null });
}

function view_history_patient(id){
  url = new Url();
  url.setModuleAction("dPpatients", "vw_history");
  url.addParam("patient_id", id);
  url.popup(600, 500, "history");
}

function editPatient() {
  var oForm = document.actionPat;
  var oTabField = oForm.tab;
  oTabField.value = "vw_edit_patients";
  oForm.submit();
}

function printPatient(id) {
  var url = new Url;
  url.setModuleAction("dPpatients", "print_patient");
  url.addParam("patient_id", id);
  url.popup(700, 550, "Patient");
}

function pageMain() {
  initAccord(true);
  PairEffect.initGroup("patientEffect", { 
    bStoreInCookie: true
  });
}

</script>

<table class="main">
  <tr>
    <td>

      <form name="FrmClass" action="?m={{$m}}" method="get" onsubmit="reloadListFile(); return false;">
      <input type="hidden" name="selKey"   value="{{$selKey}}" />
      <input type="hidden" name="selClass" value="{{$selClass}}" />
      <input type="hidden" name="selView"  value="{{$selView}}" />
      <input type="hidden" name="keywords" value="" />
      <input type="hidden" name="file_id"  value="" />
      <input type="hidden" name="typeVue"  value="1" />
      </form>
      
      {{assign var="href" value="index.php?m=dPpatients&tab=vw_full_patients"}}

      <table class="form">
        <tr id="mainInfo-trigger">
          <th class="title" colspan="4">
            {{$patient->_view}} ({{$patient->_age}} ans)
          </th>
        </tr>

        <tbody class="patientEffect" id="mainInfo">
        <tr>
          <th class="category" colspan="2">
            <a style="float:right;" href="javascript:view_history_patient({{$patient->patient_id}})">
              <img src="images/history.gif" alt="historique" />
            </a>
            Identité
          </th>
          <th class="category" colspan="2">
            {{if $patient->_canRead}}
            <div style="float:right;">
              <a href="#" onclick="setObject( {
                objClass: 'CPatient', 
                keywords: '', 
                id: {{$patient->patient_id|smarty:nodefaults|JSAttribute}}, 
                view: '{{$patient->_view|smarty:nodefaults|JSAttribute}}' })"
                title="{{$patient->_nb_files_docs}} doc(s)">
                {{$patient->_nb_files_docs}}
                <img align="top" src="modules/{{$m}}/images/next{{if !$patient->_nb_files_docs}}_red{{/if}}.png" title="{{$patient->_nb_files_docs}} doc(s)" alt="Afficher les documents"  />                
              </a>
            </div>
            {{/if}} 
            Coordonnées
          </th>
        </tr>
        <tr>
          <th>Nom</th>
          <td>{{$patient->nom}}</td>
          <th>Adresse</th>
          <td class="text">{{$patient->adresse|nl2br}}</td>
        </tr>
        <tr>
          <th>Prénom</th>
          <td>{{$patient->prenom}}</td>
          <th>Code Postal</th>
          <td>{{$patient->cp}}</td>
        </tr>
        <tr>
          <th>Nom de naissance</th>
          <td>{{$patient->nom_jeune_fille}}</td>
          <th>Ville</th>
          <td>{{$patient->ville}}</td>
        </tr>
        <tr>
          <th>Date de naissance</th>
          <td>{{$patient->_naissance}}</td>
          <th>Téléphone</th>
          <td>{{$patient->_tel1}} {{$patient->_tel2}} {{$patient->_tel3}} {{$patient->_tel4}} {{$patient->_tel5}}</td>
        </tr>
        <tr>
          <th>Sexe</th>
          <td>
            {{tr}}CPatient.sexe.{{$patient->sexe}}{{/tr}}
          </td>
          <th>Portable</th>
          <td>{{$patient->_tel21}} {{$patient->_tel22}} {{$patient->_tel23}} {{$patient->_tel24}} {{$patient->_tel25}}</td>
        </tr>
        {{if $patient->rques}}
        <tr>
          <th class="category" colspan="4">Remarques</th>
        </tr>
        <tr>
          <td colspan="4" class="text">{{$patient->rques|nl2br}}</td>
        </tr>
        {{/if}}
        <tr>
          <th colspan="2" class="category">Infos médicales</th>
          <th colspan="2" class="category">Médecins</th>
        </tr>
        <tr>
          <th>N° SS</th>
          <td>{{$patient->matricule}}</td>
          <th>Traitant</th>
          <td>
            {{if $patient->medecin_traitant}}
            Dr. {{$patient->_ref_medecin_traitant->_view}}
            {{/if}}
          </td>
        </tr>
        <tr>
          <th>Régime de santé</th>
          <td>{{$patient->regime_sante}}</td>
          <th rowspan="3">Correspondants</th>
          <td>
            {{if $patient->medecin1}}
            Dr. {{$patient->_ref_medecin1->_view}}
            {{/if}}
          </td>
        </tr>
        <tr>
          <th>CMU</th>
          <td>
            {{if $patient->cmu}}
            jusqu'au {{$patient->cmu|date_format:"%d/%m/%Y"}}
            {{else}}
            -
            {{/if}}
          </td>
          <td>
            {{if $patient->medecin2}}
            Dr. {{$patient->_ref_medecin2->_view}}
            {{/if}}
          </td>
        </tr>
        <tr>
          <th>ALD</th>
          <td>
            {{if $patient->ald}}
            {{$patient->ald|nl2br}}
            {{else}}
            -
            {{/if}}
          </td>
          <td>
            {{if $patient->medecin3}}
            Dr. {{$patient->_ref_medecin3->_view}}
            {{/if}}
          </td>
        </tr>
        <tr>
          <td class="button" colspan="4">
            <form name="actionPat" action="./index.php" method="get">
            <input type="hidden" name="m" value="dPpatients" />
            <input type="hidden" name="tab" value="vw_idx_patients" />
            <input type="hidden" name="patient_id" value="{{$patient->patient_id}}" />
            <button type="button" class="print" onclick="printPatient({{$patient->patient_id}})">
              Imprimer
            </button>
            {{if $canEdit}}
            <button type="button" class="modify" onclick="editPatient()">
              Modifier
            </button>
            {{/if}}
            </form>
          </td>
        </tr>
        </tbody>
      </table>
      <table class="form">
        <tr id="sejours-trigger">
          <th colspan="2" class="title">{{$patient->_ref_sejours|@count}} séjour(s)</th>
        </tr>
        
        <tbody class="patientEffect" id="sejours">
        {{foreach from=$patient->_ref_sejours item=curr_sejour}}
        <tr>
          <td>
            <a title="Modifier le séjour" href="index.php?m=dPplanningOp&amp;tab=vw_edit_sejour&amp;sejour_id={{$curr_sejour->sejour_id}}">
              <img src="modules/dPpatients/images/planning.png" alt="Planifier"/>
            </a>
            Du {{$curr_sejour->entree_prevue|date_format:"%d/%m/%Y"}}
            au {{$curr_sejour->sortie_prevue|date_format:"%d/%m/%Y"}}
            - Dr. {{$curr_sejour->_ref_praticien->_view}}
          </td>
          <td style="text-align:right;">
          {{if $curr_sejour->_canRead}}
            <a href="#" onclick="setObject( {
              objClass: 'CSejour', 
              keywords: '', 
              id: {{$curr_sejour->sejour_id|smarty:nodefaults|JSAttribute}}, 
              view:'{{$curr_sejour->_view|smarty:nodefaults|JSAttribute}}'} )"
              title="{{$curr_sejour->_nb_files_docs}} doc(s)">
              {{$curr_sejour->_nb_files_docs}}
              <img align="top" src="modules/{{$m}}/images/next{{if !$curr_sejour->_nb_files_docs}}_red{{/if}}.png" title="{{$curr_sejour->_nb_files_docs}} doc(s)" alt="Afficher les documents"  />
            </a>
            {{/if}}         
          </td>
        </tr>
        {{foreach from=$curr_sejour->_ref_operations item=curr_op}}
        <tr>
          <td>
            <ul><li>
            <a href="index.php?m=dPplanningOp&amp;tab=vw_edit_planning&amp;operation_id={{$curr_op->operation_id}}">
              <img src="modules/dPpatients/images/planning.png" alt="modifier" title="modifier" />
            </a>
            {{$curr_op->_datetime|date_format:"%d/%m/%Y"}} - Intervention du Dr. {{$curr_op->_ref_chir->_view}}
            </li></ul>
          </td>
          <td style="text-align:right;">
          {{if $curr_op->_canRead}}
            <a href="#" onclick="setObject( {
              objClass: 'COperation', 
              keywords: '', 
              id: {{$curr_op->operation_id|smarty:nodefaults|JSAttribute}}, 
              view:'{{$curr_op->_view|smarty:nodefaults|JSAttribute}}'} )"
              title="{{$curr_op->_nb_files_docs}} doc(s)">
              {{$curr_op->_nb_files_docs}}
              <img align="top" src="modules/{{$m}}/images/next{{if !$curr_op->_nb_files_docs}}_red{{/if}}.png" title="{{$curr_op->_nb_files_docs}} doc(s)" alt="Afficher les documents"  />
            </a>
            {{/if}} 
          </td>
        </tr>
        {{/foreach}}
        {{/foreach}}
        </tbody>
        <tr id="consultations-trigger">
          <th colspan="2" class="title">{{$patient->_ref_consultations|@count}} consultation(s)</th>
        </tr>

        <tbody class="patientEffect" id="consultations">
        {{foreach from=$patient->_ref_consultations item=curr_consult}}
        <tr>
          <td>
            {{if $curr_consult->annule}}
            [ANNULE]<br />
            {{else}}
            <a href="index.php?m=dPcabinet&amp;tab=edit_planning&amp;consultation_id={{$curr_consult->consultation_id}}">
              <img src="modules/dPpatients/images/planning.png" alt="modifier" title="modifier" />
            </a>
            {{/if}}
            {{$curr_consult->_ref_plageconsult->date|date_format:"%d/%m/%Y"}} - Dr. {{$curr_consult->_ref_chir->_view}}
          </td>
          <td style="text-align:right;">
          {{if $curr_consult->_canRead}}
            <a href="#" onclick="setObject( {
              objClass: 'CConsultation', 
              keywords: '', 
              id: {{$curr_consult->consultation_id|smarty:nodefaults|JSAttribute}}, 
              view: '{{$curr_consult->_view|smarty:nodefaults|JSAttribute}}'} )"
              title="{{$curr_consult->_nb_files_docs}} doc(s)">
              {{$curr_consult->_nb_files_docs}}
              <img align="top" src="modules/{{$m}}/images/next{{if !$curr_consult->_nb_files_docs}}_red{{/if}}.png" title="{{$curr_consult->_nb_files_docs}} doc(s)" alt="Afficher les documents"  />
            </a>
            {{/if}}
          </td>
        </tr>
        {{/foreach}}
        </tbody>

        <tr>
        
        {{if $diagnosticsInstall}}
          <th colspan="4" class="title" onclick="view_labo()">
            Laboratoires
          </th>
        </tr>
        {{/if}}

      </table>
    </td>
    <td class="greedyPane" id="listView">
      {{include file="../../dPfiles/templates/inc_list_view_colonne.tpl"}}
    </td>
  </tr>
</table>