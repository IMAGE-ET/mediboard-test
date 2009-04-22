<!-- $Id$ -->
{{mb_include_script module="dPcompteRendu" script="document"}}

<script type="text/javascript">
function view_history_patient(id){
  url = new Url();
  url.setModuleAction("dPpatients", "vw_history");
  url.addParam("patient_id", id);
  url.popup(600, 500, "patient history");
}

function viewPatient(form) {
  form = form || document.forms.actionPat;
  $V(form.tab, "vw_full_patients");
  form.submit();
}

function editPatient(form) {
	form = form || document.forms.actionPat;
  $V(form.tab, "vw_edit_patients");
  form.submit();
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

Document.refreshList = function(){
  reloadVwPatient();
}
</script>

<table class="form">
  <tr>
    <th class="category" colspan="3">
      
			{{if $patient->_id_vitale}}
      <div style="float:right;">
	      <img src="images/icons/carte_vitale.png" alt="lecture vitale" title="Bénéficiaire associé à une carte Vitale" />
      </div>
      {{/if}}

      <div class="idsante400" id="{{$patient->_class_name}}-{{$patient->_id}}"></div>

      <a style="float:right;" href="#" onclick="view_history_patient({{$patient->_id}})">
        <img src="images/icons/history.gif" alt="historique" />
      </a>

      <div style="float:left;" class="noteDiv {{$patient->_class_name}}-{{$patient->_id}}">
        <img alt="Ecrire une note" src="images/icons/note_grey.png" />
      </div>

      Identité {{if $patient->_IPP}}[{{$patient->_IPP}}]{{/if}}
    </th>
    <th class="category" colspan="2">Coordonnées</th>
  </tr>

  <tr>
    <td rowspan="3" style="width: 0.1%;">{{include file=../../dPpatients/templates/inc_vw_photo_identite.tpl mode="read" size="64"}}</td>
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
    <th>{{mb_label object=$patient field="sexe"}}</th>
    <td>{{mb_value object=$patient field="sexe"}}</td>
    <th>{{mb_label object=$patient field="ville"}}</th>
    <td>{{mb_value object=$patient field="ville"}}</td>
  </tr>
  
  <tr>
    <th colspan="2">{{mb_label object=$patient field="naissance"}}</th>
    <td>{{mb_value object=$patient field="naissance"}}</td>
    <th>{{mb_label object=$patient field="tel"}}</th>
    <td>{{mb_value object=$patient field="tel"}}</td>
  </tr>
  
  <tr>
    <th colspan="2">{{mb_label object=$patient field="nom_jeune_fille"}}</th>
    <td>{{mb_value object=$patient field="nom_jeune_fille"}}</td>
    <th>{{mb_label object=$patient field="tel2"}}</th>
    <td>{{mb_value object=$patient field="tel2"}}</td>
  </tr>
  
  {{if $patient->medecin_traitant || $patient->_ref_medecins_correspondants|@count}}
  <tr>
    <th class="category" colspan="5">Correspondants médicaux</th>
  </tr>
  
  <tr>
    <td colspan="5" class="text">
      {{assign var=medecin value=$patient->_ref_medecin_traitant}}
      {{if $medecin->_id}}
      <span class="tooltip-trigger" onmouseover="ObjectTooltip.createEx(this, '{{$medecin->_guid}}');">
        <strong>{{$medecin}}</strong> ;
      </span>
      {{/if}}
      {{foreach from=$patient->_ref_medecins_correspondants item=curr_corresp}}
	      {{assign var=medecin value=$curr_corresp->_ref_medecin}}
	      <span class="tooltip-trigger" onmouseover="ObjectTooltip.createEx(this, '{{$medecin->_guid}}');">
          {{$medecin}} ;
        </span>
      {{/foreach}}
    </td>
  </tr>
  {{/if}}

  {{if $patient->rques}}
  <tr>
    <th class="category" colspan="5">{{mb_label object=$patient field="rques"}}</th>
  </tr>
  
  <tr>
    <td colspan="5" class="text">{{mb_value object=$patient field="rques"}}</td>
  </tr>
  {{/if}}
  
  <tr>
    <td class="button" colspan="10">
      <form name="actionPat" action="?" method="get">
      <input type="hidden" name="m" value="dPpatients" />
      <input type="hidden" name="tab" value="vw_idx_patients" />
      <input type="hidden" name="patient_id" value="{{$patient->_id}}" />
      {{if @$useVitale}}
      <input type="hidden" name="useVitale" value="1" />
      {{/if}}
      
      <button type="button" class="search" onclick="viewPatient()">
        Dossier Complet
      </button>
      <button type="button" class="print" onclick="printPatient({{$patient->_id}})">
        {{tr}}Print{{/tr}}
      </button>
      {{if $canPatients->edit}}
      <button type="button" class="edit" onclick="editPatient()">
        {{tr}}Modify{{/tr}}
        {{if @$useVitale}}avec Vitale{{/if}}
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
    {{if !$app->user_prefs.simpleCabinet}}
    {{if $canPlanningOp->edit}}
    <td class="button">
      <a class="buttonnew" href="?m=dPplanningOp&amp;tab=vw_edit_planning&amp;pat_id={{$patient->_id}}&amp;operation_id=0&amp;sejour_id=0">
        Intervention
      </a>
    </td>
    {{/if}}
    {{if $canPlanningOp->read}}
    <td class="button">
      <a class="buttonnew" href="?m=dPplanningOp&amp;tab=vw_edit_urgence&amp;pat_id={{$patient->_id}}&amp;operation_id=0&amp;sejour_id=0">
        Urgence
      </a>
    </td>
    <td class="button">
      <a class="buttonnew" href="?m=dPplanningOp&amp;tab=vw_edit_sejour&amp;patient_id={{$patient->_id}}&amp;sejour_id=0">
        Séjour
      </a>
    </td>
    {{/if}}
    <td class="button">
    {{else}}
    <td colspan="4" class="button">
    {{/if}}
      {{if $canCabinet->edit}}
      <a class="buttonnew" href="?m=dPcabinet&amp;tab=edit_planning&amp;pat_id={{$patient->_id}}&amp;consultation_id=0">
        Consultation
      </a>
      {{/if}}
    </td>
  </tr>
  {{if $listPrat|@count && $canCabinet->edit}}
  <tr><th class="category" colspan="4">Consultation immédiate</th></tr>
  <tr>
    <td class="button" colspan="4">
      <form name="addConsFrm" action="?m=dPcabinet" method="post" onsubmit="return checkForm(this)">

      <input type="hidden" name="m" value="dPcabinet" />
      <input type="hidden" name="dosql" value="do_consult_now" />
      <input type="hidden" name="del" value="0" />
      <input type="hidden" name="patient_id" class="notNull ref" value="{{$patient->_id}}" />

      <label for="prat_id" title="Praticien pour la consultation immédiate. Obligatoire">Praticien</label>

      <select name="prat_id" class="notNull ref">
        <option value="">&mdash; Choisir un praticien</option>
        {{foreach from=$listPrat item=curr_prat}}
          <option class="mediuser" style="border-color: #{{$curr_prat->_ref_function->color}};" value="{{$curr_prat->user_id}}" 
            {{if $curr_prat->user_id == $app->user_id}} selected="selected" {{/if}}>
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
  {{if $affectation && $affectation->affectation_id}}
  <tr>
  	<th colspan="3" class="category">Chambre actuelle</th>
  </tr>
  <tr>
    <td colspan="3">
      {{$affectation->_ref_lit->_view}}
      depuis le {{mb_value object=$affectation field=entree}}
    </td>
  </tr>
  {{assign var="affectation" value=$patient->_ref_next_affectation}}
  {{elseif $affectation && $affectation->affectation_id}}
  <tr>
    <th colspan="3" class="category">Prochaine chambre</th>
  </tr>
  <tr>
    <td colspan="3">
      {{$affectation->_ref_lit->_view}}
      depuis le {{mb_value object=$affectation field=entree}}
    </td>
  </tr>
  {{/if}}

  {{if $patient->_ref_sejours}}
  <tr>
    <th colspan="2" class="category">Séjours</th>
  </tr>
  {{foreach from=$patient->_ref_sejours item=curr_sejour}}
  <tr>
    <td class="text">
      {{if $curr_sejour->group_id == $g && $curr_sejour->_canEdit}}
      <a class="actionPat" title="Modifier le séjour" href="?m=dPplanningOp&amp;tab=vw_edit_sejour&amp;sejour_id={{$curr_sejour->sejour_id}}">
        <img src="images/icons/planning.png" alt="Planifier"/>
      </a>
      <a class="tooltip-trigger"
         {{if $canAdmissions->view}}
         href="?m=dPadmissions&amp;tab=vw_idx_admission&amp;date={{$curr_sejour->entree_prevue|date_format:"%Y-%m-%d"}}#adm{{$curr_sejour->sejour_id}}"
         {{else}}
         href="#nothing"
         {{/if}}
         onmouseover="ObjectTooltip.createEx(this, '{{$curr_sejour->_guid}}')"
      >
      {{else}}
      <a href="#nothing">
      {{/if}}
        {{if $curr_sejour->_num_dossier && $curr_sejour->group_id == $g}}[{{$curr_sejour->_num_dossier}}]{{/if}}
        Du {{$curr_sejour->_entree|date_format:$dPconfig.date}} 
        au {{$curr_sejour->_sortie|date_format:$dPconfig.date}}
        {{if $curr_sejour->_nb_files_docs}}
          - ({{$curr_sejour->_nb_files_docs}} Doc.)
        {{/if}}
      </a>
    </td>
    {{if $curr_sejour->annule}}
    <td {{if $curr_sejour->group_id != $g}}style="background-color:#afa"{{else}}class="cancelled"{{/if}}>
      <strong>SEJOUR ANNULE</strong>
    </td>
    {{else}}
    {{if $curr_sejour->group_id == $g}}
    <td>
      Dr {{$curr_sejour->_ref_praticien->_view}}
    </td>
    {{else}}
    <td style="background-color:#afa">
      {{$curr_sejour->_ref_group->text|upper}}
    </td>
    {{/if}}
    {{/if}}
  </tr>
  
  {{foreach from=$curr_sejour->_ref_operations item=curr_op}}
  <tr>
    <td class="text">
      <ul>
      <li>
      <a class="actionPat" href="#" onclick="printIntervention({{$curr_op->_id}})">
        <img src="images/icons/print.png" alt="Imprimer" title="Imprimer l'intervention" />
      </a>
      {{if $curr_sejour->group_id == $g && $curr_op->_canEdit}}
      <a class="actionPat" title="Modifier l'intervention" href="{{$curr_op->_link_editor}}">
        <img src="images/icons/planning.png" alt="modifier"/>
      </a>
      <a class="tooltip-trigger"
         href="{{$curr_op->_link_editor}}"
         class="tooltip-trigger"
         onmouseover="ObjectTooltip.createEx(this, '{{$curr_op->_guid}}')"
      >
      {{else}}
      <a class="tooltip-trigger" title="Modification d'intervention non autorisée" href="#nothing">
      {{/if}}
        Intervention le {{$curr_op->_datetime|date_format:$dPconfig.date}}
        {{if $curr_op->_nb_files_docs}}
          - ({{$curr_op->_nb_files_docs}} Doc.)
        {{/if}}
      </a>
      </li>
      </ul>
    </td>
    {{if $curr_op->annulee}}
    <td {{if $curr_sejour->group_id != $g}}style="background-color:#afa"{{else}}class="cancelled"{{/if}}>
      <strong>OPERATION ANNULEE</strong>
    </td>
    {{else}}
    {{if $curr_sejour->group_id != $g}}
    <td style="background-color:#afa">
      {{$curr_sejour->_ref_group->_view|upper}}
    </td>
    {{else}}
    <td>
      Dr {{$curr_op->_ref_chir->_view}}
    </td>
    {{/if}}
    {{/if}}
  </tr>
  {{/foreach}}
  {{/foreach}}
  {{/if}}
  
  {{if $patient->_ref_consultations}}
  <tr><th class="category" colspan="2">Consultations</th></tr>
  {{foreach from=$patient->_ref_consultations item=curr_consult}}
  <tr>
    <td class="text">
      <a class="actionPat" title="Modifier la consultation" href="?m=dPcabinet&amp;tab=edit_planning&amp;consultation_id={{$curr_consult->_id}}">
        <img src="images/icons/planning.png" alt="modifier" />
      </a>
        {{if $curr_consult->_canEdit}}
          <a class="tooltip-trigger"
             href="?m=dPcabinet&amp;tab=edit_consultation&amp;selConsult={{$curr_consult->_id}}&amp;chirSel={{$curr_consult->_ref_plageconsult->chir_id}}"
             onmouseover="ObjectTooltip.createEx(this, '{{$curr_consult->_guid}}')"
           >
        {{else}}
          <a href="#nothing">
        {{/if}}
        Le {{$curr_consult->_datetime|date_format:$dPconfig.datetime}} - {{$curr_consult->_etat}}
      </a>
    </td>

    {{if $curr_consult->annule}}
    <td class="cancelled">[Consult annulée]</td>
    {{else}}
    <td>Dr {{$curr_consult->_ref_plageconsult->_ref_chir->_view}}</td>
    {{/if}}
  </tr>

  {{/foreach}}
  {{/if}}
</table>