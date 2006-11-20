<!-- $Id$ -->

<script type="text/javascript">

function printDocument(doc_id) {
  var url = new Url;
  url.setModuleAction("dPcompteRendu", "print_cr");
  url.addParam("compte_rendu_id", doc_id);
  url.popup(700, 600, 'Compte-rendu');
}

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


<table class="tbl">
  <tr>
    <th colspan="3" class="title">{{$patient->_view}}</th>
  </tr>
  <tr>
    <th>Antécédents</th>
    <th>Documents</th>
    <th>Fichiers</th>
  </tr>
  
  <tr>
    <td class="text" valign="top">
      <ul>
      {{if $patient->_ref_antecedents}}
        {{foreach from=$listAnt key=keyAnt item=currTypeAnt}}
        {{if $currTypeAnt}}
        <li>
          <strong>{{tr}}CAntecedent.type.{{$keyAnt}}{{/tr}}</strong>
          {{foreach from=$currTypeAnt item=currAnt}}
          <ul><li>         
            {{if $currAnt->date|date_format:"%d/%m/%Y"}}
              {{$currAnt->date|date_format:"%d/%m/%Y"}} :
            {{/if}}
            {{$currAnt->rques}}
          </li></ul>
          {{/foreach}}
        </li>
        {{/if}}
        {{/foreach}}
      {{else}}
        <li>Pas d'antécédents</li>
      {{/if}}
      </ul>
    </td>
    <td rowspan="3" class="text" valign="top">
      <ul>
      {{foreach from=$patient->_ref_documents item=curr_doc}}
        <li>
          {{$curr_doc->nom}}
          <button class="print notext" onclick="printDocument({{$curr_doc->compte_rendu_id}})">
          </button>
        </li>
      {{/foreach}}
      </ul>
    </td>
    <td rowspan="3" class="text" valign="top">
      <ul>
      {{foreach from=$patient->_ref_files item=curr_file}}
        <li>
          <a href="#" OnClick="popFile('{{$patient->_class_name}}','{{$patient->_id}}','{{$curr_file->_class_name}}','{{$curr_file->_id}}')">{{$curr_file->file_name}}</a>
          ({{$curr_file->_file_size}})
        </li>
      {{/foreach}}
      </ul>
    </td>
  </tr>
  <tr>
    <th>Traitements</th>
  </tr>
  <tr>
    <td class="text" valign="top">
      <ul>
        {{foreach from=$patient->_ref_traitements item=curr_trmt}}
        <li>
          {{if $curr_trmt->fin}}
            Du {{$curr_trmt->debut|date_format:"%d/%m/%Y"}} au {{$curr_trmt->fin|date_format:"%d/%m/%Y"}}
          {{else}}
            Depuis le {{$curr_trmt->debut|date_format:"%d/%m/%Y"}}
          {{/if}}
          : <i>{{$curr_trmt->traitement}}</i>
        </li>
        {{foreachelse}}
        <li>Pas de traitements</li>
        {{/foreach}}
      </ul>
    </td>
  </tr>
  
  <tr>
    <th colspan="3" class="title">Consultations</th>
  </tr>
  <tr>
    <th>Résumé</th>
    <th>Documents</th>
    <th>Fichiers</th>
  </tr>
  <tr>
    <td class="text" valign="top">
      <ul>
        {{foreach from=$consultations item=curr_consult}}
        <li>
          Dr. {{$curr_consult->_ref_plageconsult->_ref_chir->_view}}
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
        </li>
    {{/foreach}}
    </td>
    <td class="text" valign="top">
      <ul>
      {{foreach from=$docsCons item=curr_doc}}
        <li>
          {{$curr_doc->nom}}
          <button class="print notext" onclick="printDocument({{$curr_doc->compte_rendu_id}})">
          </button>
        </li>
      {{/foreach}}
      </ul>
    </td>
    <td class="text" valign="top">
      <ul>
      {{foreach from=$filesCons item=curr_file}}
        <li>
          <a href="#" OnClick="popFile('{{$curr_file->file_class}}','{{$curr_file->file_object_id}}','{{$curr_file->_class_name}}','{{$curr_file->file_id}}')">{{$curr_file->file_name}}</a>
          ({{$curr_file->_file_size}})
        </li>
      {{/foreach}}
      </ul>
    </td>
  </tr>
  <tr>
    <th colspan="3" class="title">Interventions</th>
  </tr>
  <tr>
    <th>Résumé</th>
    <th>Documents</th>
    <th>Fichiers</th>
  </tr>
  <tr>
    <td class="text" valign="top">
      <ul>
        {{foreach from=$sejours item=curr_sejour}}
        {{foreach from=$curr_sejour->_ref_operations item=curr_op}}
        <li>
          Dr. {{$curr_op->_ref_chir->_view}}
          &mdash; {{$curr_op->_ref_plageop->date|date_format:"%d/%m/%Y"}}
          {{foreach from=$curr_op->_codes_ccam|smarty:nodefaults item=curr_code}}
            <br />
            {{$curr_code}}
          {{/foreach}}
        </li>
        {{foreachelse}}
        <li>
          Hospitalisation simple du {{$curr_sejour->entree_prevue|date_format:"%d/%m/%Y"}}
          au {{$curr_sejour->sortie_prevue|date_format:"%d/%m/%Y"}}
        </li>
        {{/foreach}}
        {{/foreach}}
      </ul>
    </td>
    <td class="text" valign="top">
      <ul>
      {{foreach from=$docsOp item=curr_doc}}
        <li>
          {{$curr_doc->nom}}
          <button class="print notext" onclick="printDocument({{$curr_doc->compte_rendu_id}})">
          </button>
        </li>
      {{/foreach}}
      </ul>
    </td>
    <td class="text" valign="top">
      <ul>
      {{foreach from=$filesOp item=curr_file}}
        <li>
          <a href="#" OnClick="popFile('{{$curr_file->file_class}}','{{$curr_file->file_object_id}}','{{$curr_file->_class_name}}','{{$curr_file->_id}}')">{{$curr_file->file_name}}</a>
          ({{$curr_file->_file_size}})
        </li>
      {{/foreach}}
      </ul>
    </td>
  </tr>
</table>