<!-- $Id$ -->

{literal}
<script type="text/javascript">

function printDocument(doc_id) {
  var url = new Url;
  url.setModuleAction("dPcompteRendu", "print_cr");
  url.addParam("compte_rendu_id", doc_id);
  url.popup(700, 600, 'Compte-rendu');
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
{/literal}

<table class="tbl">
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
        {foreach from=$consultations item=curr_consult}
        <li>
          Dr. {$curr_consult->_ref_plageconsult->_ref_chir->_view}
          &mdash; {$curr_consult->_ref_plageconsult->date|date_format:"%d/%m/%Y"}
          {if $curr_consult->motif}
            <br />
            <strong>Motif:</strong>
            <i>{$curr_consult->motif}</i>
          {/if}
          {if $curr_consult->rques}
            <br />
            <strong>Remarques:</strong>
            <i>{$curr_consult->rques}</i>
          {/if}
          {if $curr_consult->examen}
            <br />
            <strong>Examens:</strong>
            <i>{$curr_consult->examen}</i>
          {/if}
          {if $curr_consult->traitement}
            <br />
            <strong>Traitement:</strong>
            <i>{$curr_consult->traitement}</i>
          {/if}
          {if $curr_consult->_ref_examaudio->examaudio_id}
            <br />
            <a href="javascript:newExam('exam_audio', {$curr_consult->consultation_id})">
              <strong>Audiogramme</strong>
            </a>
          {/if}
        </li>
    {/foreach}
    </td>
    <td class="text" valign="top">
      <ul>
      {foreach from=$docsCons item=curr_doc}
        <li>
          {$curr_doc->nom}
          <button onclick="printDocument({$curr_doc->compte_rendu_id})">
            <img src="modules/dPcabinet/images/print.png" />
          </button>
        </li>
      {/foreach}
      </ul>
    </td>
    <td class="text" valign="top">
      <ul>
      {foreach from=$filesCons item=curr_file}
        <li>
          <a href="mbfileviewer.php?file_id={$curr_file->file_id}" target="_blank">{$curr_file->file_name}</a>
          ({$curr_file->_file_size})
        </li>
      {/foreach}
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
        {foreach from=$operations item=curr_op}
        <li>
          Dr. {$curr_op->_ref_chir->_view}
          &mdash; {$curr_op->_ref_plageop->date|date_format:"%d/%m/%Y"}
          {foreach from=$curr_op->_codes_ccam item=curr_code}
            <br />
            {$curr_code}
          {/foreach}
        </li>
        {/foreach}
      </ul>
    </td>
    <td class="text" valign="top">
      <ul>
      {foreach from=$docsOp item=curr_doc}
        <li>
          {$curr_doc->nom}
          <button onclick="printDocument({$curr_doc->compte_rendu_id})">
            <img src="modules/dPcabinet/images/print.png" />
          </button>
        </li>
      {/foreach}
      </ul>
    </td>
    <td class="text" valign="top">
      <ul>
      {foreach from=$filesOp item=curr_file}
        <li>
          <a href="mbfileviewer.php?file_id={$curr_file->file_id}" target="_blank">{$curr_file->file_name}</a>
          ({$curr_file->_file_size})
        </li>
      {/foreach}
      </ul>
    </td>
  </tr>
</table>