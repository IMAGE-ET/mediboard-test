<!-- $Id$ -->

<script type="text/javascript">
function view_history_patient(id){
  url = new Url();
  url.setModuleAction("dPpatients", "vw_history");
  url.addParam("patient_id", id);
  url.popup(600, 500, "history");
}

function viewPatient() {
  var oForm = document.actionPat;
  var oTabField = oForm.tab;
  oTabField.value = "vw_full_patients";
  oForm.submit();
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

function popFile(objectClass, objectId, elementClass, elementId){
  var url = new Url;
  url.ViewFilePopup(objectClass, objectId, elementClass, elementId, 0);
}

function editDocument(compte_rendu_id) {
  var url = new Url;
  url.setModuleAction("dPcompteRendu", "edit_compte_rendu");
  url.addParam("compte_rendu_id", compte_rendu_id);
  url.popup(700, 700, "Document");
}

function createDocument(oSelect, patient_id) {
  if (modele_id = oSelect.value) {
    var url = new Url;
    url.setModuleAction("dPcompteRendu", "edit_compte_rendu");
    url.addParam("modele_id", modele_id);
    url.addParam("object_id", patient_id);
    url.popup(700, 700, "Document");
  }
  oSelect.value = "";
}

function printIntervention(id) {
  var url = new Url;
  url.setModuleAction("dPplanningOp", "view_planning");
  url.addParam("operation_id", id);
  url.popup(700, 550, "Admission");
}

function reloadVwPatient(){
  var mainUrl = new Url;
  mainUrl.setModuleAction("dPpatients", "httpreq_vw_patient");
  mainUrl.addParam("patient_id", document.actionPat.patient_id.value);
  mainUrl.requestUpdate('vwPatient', { waitingText : null });
}

function reloadAfterSaveDoc(){
  reloadVwPatient();
}
</script>

<table class="form">
  <tr>
    <th class="category" colspan="2">
      <a style="float:right;" href="#" onclick="view_history_patient({{$patient->patient_id}})">
        <img src="images/icons/history.gif" alt="historique" />
      </a>
      Identité
    </th>
    <th class="category" colspan="2">Coordonnées</th>
  </tr>

  <tr>
    <th>{{mb_label object=$patient field="nom"}}</th>
    <td>{{mb_value object=$patient field="nom"}}</td>
    <th>{{mb_label object=$patient field="adresse"}}</th>
    <td class="text">{{mb_value object=$patient field="adresse"}}</td>
  </tr>
  
  <tr>
    <th>{{mb_label object=$patient field="prenom"}}</th>
    <td>{{mb_value object=$patient field="prenom"}}</td>
    <th>{{mb_label object=$patient field="cp"}}</th>
    <td>{{mb_value object=$patient field="cp"}}</td>
  </tr>
  
  <tr>
    <th>{{mb_label object=$patient field="nom_jeune_fille"}}</th>
    <td>{{mb_value object=$patient field="nom_jeune_fille"}}</td>
    <th>{{mb_label object=$patient field="ville"}}</th>
    <td>{{mb_value object=$patient field="ville"}}</td>
  </tr>
  
  <tr>
    <th>{{mb_label object=$patient field="naissance"}}</th>
    <td>{{mb_value object=$patient field="naissance"}}</td>
    <th>{{mb_label object=$patient field="tel"}}</th>
    <td>{{mb_value object=$patient field="tel"}}</td>
  </tr>
  
  <tr>
    <th>{{mb_label object=$patient field="sexe"}}</th>
    <td>{{mb_value object=$patient field="sexe"}}</td>
    <th>{{mb_label object=$patient field="tel2"}}</th>
    <td>{{mb_value object=$patient field="tel2"}}</td>
  </tr>
  
  {{if $patient->medecin_traitant || $patient->medecin1 || $patient->medecin2 || $patient->medecin3}}
  <tr>
    <th class="category" colspan="4">Médecins correspondants</th>
  </tr>
  
  <tr>
    <td colspan="4" class="text">
      {{if $patient->medecin_traitant}}
        {{$patient->_ref_medecin_traitant->_view}} ;
      {{/if}}
      {{if $patient->medecin1}}
        {{$patient->_ref_medecin1->_view}} ;
      {{/if}}
      {{if $patient->medecin2}}
        {{$patient->_ref_medecin2->_view}} ;
      {{/if}}
      {{if $patient->medecin3}}
        {{$patient->_ref_medecin3->_view}} ;
      {{/if}}
    </td>
  </tr>
  {{/if}}

  {{if $patient->rques}}
  <tr>
    <th class="category" colspan="4">{{mb_label object=$patient field="rques"}}</th>
  </tr>
  
  <tr>
    <td colspan="4" class="text">{{mb_value object=$patient field="rques"}}</td>
  </tr>
  {{/if}}
  
  <tr>
    <td class="button" colspan="4">
      <form name="actionPat" action="./index.php" method="get">
      <input type="hidden" name="m" value="dPpatients" />
      <input type="hidden" name="tab" value="vw_idx_patients" />
      <input type="hidden" name="patient_id" value="{{$patient->patient_id}}" />
      <button type="button" class="search" onclick="viewPatient()">
        Afficher
      </button>
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
</table>

<table class="form">
  <tr>
    <th class="category" colspan="4">Planifier</th>
  </tr>
  <tr>
    <td class="button">
      <a class="buttonnew" href="index.php?m=dPplanningOp&amp;tab=vw_edit_planning&amp;pat_id={{$patient->patient_id}}&amp;operation_id=0&amp;sejour_id=0">
        Intervention
      </a>
    </td>
    <td class="button">
      <a class="buttonnew" href="index.php?m=dPplanningOp&amp;tab=vw_edit_urgence&amp;pat_id={{$patient->patient_id}}&amp;operation_id=0&amp;sejour_id=0">
        Urgence
      </a>
    </td>
    <td class="button">
      <a class="buttonnew" href="index.php?m=dPplanningOp&amp;tab=vw_edit_sejour&amp;patient_id={{$patient->patient_id}}&amp;sejour_id=0">
        Séjour
      </a>
    </td>
    <td class="button">
      <a class="buttonnew" href="index.php?m=dPcabinet&amp;tab=edit_planning&amp;pat_id={{$patient->patient_id}}&amp;consultation_id=0">
        Consultation
      </a>
    </td>
  </tr>
  {{if $listPrat|@count && $canEditCabinet}}
  <tr><th class="category" colspan="4">Consultation immédiate</th></tr>
  <tr>
    <td class="button" colspan="4">
      <form name="addConsFrm" action="index.php?m=dPcabinet" method="post" onsubmit="return checkForm(this)">
      <input type="hidden" name="m" value="dPcabinet" />
      <input type="hidden" name="dosql" value="do_consult_now" />
      <input type="hidden" name="del" value="0" />
      <input type="hidden" name="patient_id" title="notNull refMandatory" value="{{$patient->patient_id}}" />
      <label for="prat_id" title="Praticien pour la consultation immédiate. Obligatoire">Praticien</label>
      <select name="prat_id" title="notNull refMandatory">
        <option value="">&mdash; Choisir un praticien</option>
        {{foreach from=$listPrat item=curr_prat}}
          <option value="{{$curr_prat->user_id}}" {{if $curr_prat->user_id == $app->user_id}} selected="selected" {{/if}}>
            {{$curr_prat->_view}}
          </option>
        {{/foreach}}
      </select>
      <button class="new" type="submit">Consulter</button>
      </form>
    </td>
  </tr>
  {{/if}}
</table>

<table class="form">
  {{assign var="affectation" value=$patient->_ref_curr_affectation}}
  {{if $affectation->affectation_id}}
  <tr>
  	<th colspan="3" class="category">Chambre actuelle</th>
  </tr>
  <tr>
    <td colspan="3">
      {{$affectation->_ref_lit->_view}}
      depuis {{$affectation->entree|date_format:"%d %b %Y à %H:%M"}}
    </td>
  </tr>
  {{assign var="affectation" value=$patient->_ref_next_affectation}}
  {{elseif $affectation->affectation_id}}
  <tr>
    <th colspan="3" class="category">Prochaine chambre</th>
  </tr>
  <tr>
    <td colspan="3">
      {{$affectation->_ref_lit->_view}}
      depuis {{$affectation->entree|date_format:"%d %b %Y à %H:%M"}}
    </td>
  </tr>
  {{/if}}

  {{if $patient->_ref_sejours}}
  <tr>
    <th colspan="2" class="category">Séjours</th>
  </tr>
  {{foreach from=$patient->_ref_sejours item=curr_sejour}}
  <tr>
    <td>
      <a class="actionPat" title="Modifier le séjour" href="index.php?m=dPplanningOp&amp;tab=vw_edit_sejour&amp;sejour_id={{$curr_sejour->sejour_id}}">
        <img src="images/icons/planning.png" alt="Planifier"/>
      </a>
      <a class="actionPat" href="index.php?m=dPadmissions&amp;tab=vw_idx_admission&amp;date={{$curr_sejour->entree_prevue|date_format:"%Y-%m-%d"}}#adm{{$curr_sejour->sejour_id}}">
        Séjour du {{$curr_sejour->entree_prevue|date_format:"%d %b %Y"}} 
        au {{$curr_sejour->sortie_prevue|date_format:"%d %b %Y"}}
        {{if $curr_sejour->_nb_files_docs}}
          - ({{$curr_sejour->_nb_files_docs}} Doc.)
        {{/if}}
      </a>
	</td>
    {{if $curr_sejour->annule}}
 	<td style="background: #f00">
      <strong>[SEJOUR ANNULE]</strong>
	</td>
    {{else}}
 	<td>
      <a href="index.php?m=dPadmissions&amp;tab=vw_idx_admission&amp;date={{$curr_sejour->entree_prevue|date_format:"%Y-%m-%d"}}#adm{{$curr_sejour->sejour_id}}">
        Dr. {{$curr_sejour->_ref_praticien->_view}}
      </a>
	</td>
    {{/if}}
  </tr>
  {{foreach from=$curr_sejour->_ref_operations item=curr_op}}
  <tr>
    <td>
      <a class="actionPat" href="#" onclick="printIntervention({{$curr_op->operation_id}})">
        <img src="images/icons/print.png" alt="Imprimer" title="Imprimer l'opération"/>
      </a>
      <a class="actionPat" href="{{$curr_op->_link_editor}}">
        <img src="images/icons/planning.png" alt="modifier" title="modifier" />
      </a>
      <a class="actionPat" href="{{$curr_op->_link_editor}}">
        Intervention le {{$curr_op->_datetime|date_format:"%d %b %Y"}}
        {{if $curr_op->_nb_files_docs}}
          - ({{$curr_op->_nb_files_docs}} Doc.)
        {{/if}}
      </a>
    </td>
    {{if $curr_op->annulee}}
 	<td style="background: #f00">
      <strong>[OPERATION ANNULEE]</strong>
	</td>
    {{else}}
    <td>
      <a href="{{$curr_op->_link_editor}}">
        Dr. {{$curr_op->_ref_chir->_view}}
      </a>
    </td>
    {{/if}}
  </tr>
  {{/foreach}}
  {{/foreach}}
  {{/if}}
  
  {{if $patient->_ref_consultations}}
  <tr><th class="category" colspan="2">Consultations</th></tr>
  {{foreach from=$patient->_ref_consultations item=curr_consult}}
  <tr>
    <td>
      {{if $curr_consult->annule}}
      [ANNULE]
      {{else}}
      <a class="actionPat" href="index.php?m=dPcabinet&amp;tab=edit_planning&amp;consultation_id={{$curr_consult->consultation_id}}">
        <img src="images/icons/planning.png" alt="modifier" title="modifier" />
      </a>
      {{/if}}
      <a class="actionPat" href="index.php?m=dPcabinet&amp;tab=edit_consultation&amp;selConsult={{$curr_consult->consultation_id}}">
        Le {{$curr_consult->_ref_plageconsult->date|date_format:"%d %b %Y"}} - {{$curr_consult->_etat}}
      </a>
    </td>
    <td>
      <a href="index.php?m=dPcabinet&amp;tab=edit_consultation&amp;selConsult={{$curr_consult->consultation_id}}">
        Dr. {{$curr_consult->_ref_plageconsult->_ref_chir->_view}}
      </a>
    </td>
  </tr>
  {{/foreach}}
  {{/if}}
</table>