<!-- $Id$ -->

<script type="text/javascript">

function popFile(objectClass, objectId, elementClass, elementId){
  var url = new Url;
  url.ViewFilePopup(objectClass, objectId, elementClass, elementId, 0);
}

function newExam(sAction, consultation_id) {
  if (sAction) {
    var url = new Url;
    url.setModuleAction("dPcabinet", sAction);
    url.addParam("consultation_id", consultation_id);
    url.popup(900, 600, "Examen");  
  }
}

</script>

<!-- Dossier Médical -->
{{include file=../../dPpatients/templates/CDossierMedical_complete.tpl object=$patient->_ref_dossier_medical}}

<table class="tbl">
  <tr>
    <th colspan="4" class="title">{{$patient->_view}}</th>
  </tr>

	</tr>
    <th colspan="2">
    	Fichiers du patient
    </th>
    <th colspan="2">
    	Documents du patient
    </th>
  </tr>

  <tr>
    <td colspan="2">
      {{foreach from=$patient->_ref_documents item=curr_doc}}
      <a href="#document-{{$curr_doc->_id}}" onclick="popFile('{{$curr_doc->object_class}}','{{$curr_doc->object_id}}','{{$curr_doc->_class_name}}','{{$curr_doc->_id}}')">
        {{$curr_doc->nom}}
      </a>
      {{foreachelse}}
      <em>Aucun</em>
      {{/foreach}}
    </td>
    <td colspan="2">
      {{foreach from=$patient->_ref_files item=curr_file}}
      <a href="#file-{{$curr_file->_id}}" onclick="popFile('{{$patient->_class_name}}','{{$patient->_id}}','{{$curr_file->_class_name}}','{{$curr_file->_id}}')">
        {{$curr_file->file_name}}
      </a>
      {{foreachelse}}
      <em>Aucun</em>
      {{/foreach}}
    </td>
  </tr>
  
  <tr>
    <th colspan="4" class="title">Consultations</th>
  </tr>
  <tr>
    <th>Résumé</th>
    <th>Documents</th>
    <th>
      <label title="Réglements du patient et l'assurance maladie, en rouge si non réglé en totalité">
        Règlement<br />Patient - AM
      </label>
    </th>
  </tr>
  
  <!-- Consultations -->
  {{foreach from=$patient->_ref_consultations item=curr_consult}}
  <tr>
    <td class="text" valign="top">
      Dr {{$curr_consult->_ref_plageconsult->_ref_chir->_view}}
      &mdash; {{$curr_consult->_ref_plageconsult->date|date_format:"%d/%m/%Y"}}
        {{if $curr_consult->motif}}
	      <br />
	      <strong>Motif:</strong>
	      <i>{{$curr_consult->motif}}</i>
	    {{/if}}
	    {{if $curr_consult->rques}}
	      <br />
	      <strong>Remarques:</strong>
	      <i>{{$curr_consult->rques}}</i>
	    {{/if}}
	    {{if $curr_consult->examen}}
	      <br />
	      <strong>Examens:</strong>
	      <i>{{$curr_consult->examen}}</i>
	    {{/if}}
	    {{if $curr_consult->traitement}}
	      <br />
	      <strong>Traitement:</strong>
	      <i>{{$curr_consult->traitement}}</i>
	    {{/if}}
	    {{if $curr_consult->_ref_examaudio->examaudio_id}}
	      <br />
	      <a href="#" onclick="newExam('exam_audio', {{$curr_consult->consultation_id}})">
	        <strong>Audiogramme</strong>
	      </a>
	    {{/if}}
    </td>
    <td valign="top">
      {{foreach from=$curr_consult->_ref_documents item=curr_doc}}
      <a href="#" onclick="popFile('{{$curr_doc->object_class}}','{{$curr_doc->object_id}}','{{$curr_doc->_class_name}}','{{$curr_doc->_id}}')">
        {{$curr_doc->nom}}
      </a>
      {{/foreach}}
      {{foreach from=$curr_consult->_ref_files item=curr_file}}
      <a href="#" onclick="popFile('{{$curr_file->object_class}}','{{$curr_file->object_id}}','{{$curr_file->_class_name}}','{{$curr_file->_id}}')">
        {{$curr_file->file_name}}
      </a>
      {{/foreach}}
    </td>
    
    <td style="text-align: center">
    {{if $curr_consult->tarif}}
      {{if $curr_consult->du_patient}}
      <div style="display: inline; {{if !$curr_consult->patient_date_reglement}} color: #f00;{{/if}}">
        {{$curr_consult->_reglements_total_patient}}&euro;
      </div>
      /
      {{/if}}
      {{$curr_consult->du_patient}}&euro;
      -
      {{if $curr_consult->du_tiers}}
      <div style="display: inline; {{if !$curr_consult->tiers_date_reglement}} color: #f00;{{/if}}">
        {{$curr_consult->_reglements_total_tiers}}&euro;
      </div>
      /
      {{/if}}
      {{$curr_consult->du_tiers}}&euro;
    {{/if}}
    </td>
  </tr>
  {{/foreach}}
  
  <!-- Interventions -->
  <tr>
    <th colspan="4" class="title">Interventions</th>
  </tr>
  <tr>
    <th>Résumé</th>
    <th colspan="3">Documents</th>
  </tr>
  {{foreach from=$patient->_ref_sejours item=curr_sejour}}
  <tr>
    <td class="text" valign="top">
      <ul>
        {{foreach from=$curr_sejour->_ref_operations item=curr_op}}
        <li>
          Dr {{$curr_op->_ref_chir->_view}}
          &mdash; {{$curr_op->_ref_plageop->date|date_format:"%d/%m/%Y"}}
          {{if $curr_op->libelle}}
          <br/>
          <strong>{{mb_label object=$curr_op field="libelle"}}</strong> :
          {{mb_value object=$curr_op field="libelle"}}
          {{/if}}
          {{foreach from=$curr_op->_ext_codes_ccam item=curr_code}}
          <br />
          <strong>{{$curr_code->code}}</strong>
          : {{$curr_code->libelleLong}}
          {{/foreach}}
        </li>
        {{foreachelse}}
        <li>
          Hospitalisation simple du {{$curr_sejour->entree_prevue|date_format:"%d/%m/%Y"}}
          au {{$curr_sejour->sortie_prevue|date_format:"%d/%m/%Y"}}
        </li>
        {{/foreach}}
      </ul>
    </td>
    <td colspan="2" valign="top">
      {{foreach from=$curr_sejour->_ref_operations item=curr_op}}
      {{foreach from=$curr_op->_ref_documents item=curr_doc}}
      <a href="#" onclick="popFile('{{$curr_doc->object_class}}','{{$curr_doc->object_id}}','{{$curr_doc->_class_name}}','{{$curr_doc->_id}}')">
        {{$curr_doc->nom}}
      </a>
      {{/foreach}}
      {{/foreach}}
      {{foreach from=$curr_sejour->_ref_operations item=curr_op}}
      {{foreach from=$curr_op->_ref_files item=curr_file}}
      <a href="#" onclick="popFile('{{$curr_file->object_class}}','{{$curr_file->object_id}}','{{$curr_file->_class_name}}','{{$curr_file->_id}}')">
        {{$curr_file->file_name}}
      </a>
      {{/foreach}}
      {{/foreach}}
    </td>
  </tr>
  {{/foreach}}
</table>