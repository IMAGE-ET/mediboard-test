<!-- $Id$ -->

{literal}
<script type="text/javascript">

function pageMain() {
  initGroups("hospi");
  initGroups("op");
  initGroups("consult");
}

function popPat() {
  var url = new Url;
  url.setModuleAction("dPpatients", "pat_selector");
  url.popup(500, 500, 'Patient');
}

function printPack(hospi_id, pack_id) {
  if (pack_id) {
    var url = new Url;
    url.setModuleAction("dPcompteRendu", "edit_compte_rendu");
    url.addParam("object_id", hospi_id);
    url.addParam("pack_id", pack_id);
    url.popup(700, 600, "Impression de pack");
  }
}

function setPat( key, val ) {
  var f = document.patFrm;

  if (val != '') {
    f.patSel.value = key;
    f.patNom.value = val;
  }
  
  f.submit();
}

function printDocument(doc_id) {
  var url = new Url;
  url.setModuleAction("dPcompteRendu", "print_cr");
  url.addParam("compte_rendu_id", doc_id);
  url.popup(700, 600, 'Compte-rendu');
}

</script>
{/literal}

<table class="main">
  <tr>
    <td class="greedyPane" colspan="2">
      <form name="patFrm" action="index.php" method="get">
      <table class="form">
        <tr><th>Choix du patient :</th>
          <td class="readonly">
            <input type="hidden" name="m" value="{$m}" />
            <input type="hidden" name="patSel" value="{$patSel->patient_id}" />
            <input type="text" size="40" readonly="readonly" name="patNom" value="{$patSel->_view}" />
          </td>
          <td class="button">
            <button class="search" type="button" onclick="popPat()">chercher</button>
          </td>
        </tr>
      </table>
      </form>
    </td>
  </tr>
  {if $patSel->patient_id}
  <tr>
    <td>
      <table class="form">
        <tr><th class="category" colspan="2">Consultations</th></tr>
        {foreach from=$patSel->_ref_consultations item=curr_consult}
        <tr class="groupcollapse" id="consult{$curr_consult->consultation_id}" onclick="flipGroup({$curr_consult->consultation_id}, 'consult')">
          <td colspan="2">
            <strong>
            Dr. {$curr_consult->_ref_plageconsult->_ref_chir->_view} &mdash;
            {$curr_consult->_ref_plageconsult->date|date_format:"%A %d %B %Y"} &mdash;
            {$curr_consult->_etat} &mdash;
            {$curr_consult->_ref_files|@count} fichier(s)
            </strong>
          </td>
        </tr>
        <tr class="consult{$curr_consult->consultation_id}">
          <td colspan="2">
            <a href="index.php?m=dPcabinet&amp;tab=edit_consultation&amp;selConsult={$curr_consult->consultation_id}">
              Voir la consultation
            </a>
          </td>
        </tr>
        <tr class="consult{$curr_consult->consultation_id}">
          <th>Motif :</th>
          <td class="text">{$curr_consult->motif}</td>
        </tr>
        {if $curr_consult->rques}
        <tr class="consult{$curr_consult->consultation_id}">
          <th>Remarques :</th>
          <td class="text">{$curr_consult->rques}</td>
        </tr>
        {/if}
        {if $curr_consult->examen}
        <tr class="consult{$curr_consult->consultation_id}">
          <th>Examen :</th>
          <td class="text">{$curr_consult->examen}</td>
        </tr>
        {/if}
        {if $curr_consult->traitement}
        <tr class="consult{$curr_consult->consultation_id}">
          <th>Traitement :</th>
          <td class="text">{$curr_consult->traitement}</td>
        </tr>
        {/if}
        <tr class="consult{$curr_consult->consultation_id}">
          <th>Documents créés :</th>
          <td>
          <ul>
            {foreach from=$curr_consult->_ref_documents item=document}
            <li>
              {$document->nom}
              <button class="print" onclick="printDocument({$document->compte_rendu_id})">
                Imprimer
              </button>
            </li>
            {foreachelse}
            <li>Aucun document créé</li>
            {/foreach}
          </ul>
        </tr>
        <tr class="consult{$curr_consult->consultation_id}">
          <th>Fichiers attachés :</th>
          <td>
            <ul>
              {foreach from=$curr_consult->_ref_files item=curr_file}
              <li>
                <form name="uploadFrm{$curr_file->file_id}" action="?m=dPcabinet" enctype="multipart/form-data" method="post">
      
                <input type="hidden" name="dosql" value="do_file_aed" />
                <input type="hidden" name="del" value="1" />
                <input type="hidden" name="file_id" value="{$curr_file->file_id}" />
                <a href="mbfileviewer.php?file_id={$curr_file->file_id}">{$curr_file->file_name}</a>
                ({$curr_file->_file_size}) 
                <button class="trash" type="button" onclick="confirmDeletion(this.form,{ldelim}typeName:'le fichier',objName:'{$curr_file->file_name|escape:javascript}'{rdelim})">
                  Supprimer
                </button>
      
                </form>
              </li>
              {foreachelse}
              <li>Aucun fichier attaché</li>
              {/foreach}
            </ul>
            <form name="uploadFrm" action="?m=dPcabinet" enctype="multipart/form-data" method="post">
            <input type="hidden" name="dosql" value="do_file_aed" />
            <input type="hidden" name="del" value="0" />
            <input type="hidden" name="file_consultation" value="{$curr_consult->consultation_id}" />
            <input type="file" name="formfile" />
            <button class="submit" type="submit">Ajouter</button>

            </form>
          </td>
        </tr>
        {/foreach}
        {foreach from=$patSel->_ref_sejours item=curr_sejour}
        <tr>
          <th class="category" colspan="2">
          	Séjour du {$curr_sejour->entree_prevue}
          	au {$curr_sejour->sortie_prevue}
          </th>
        </tr>
        {foreach from=$curr_sejour->_ref_operations item=curr_op}
        <tr class="groupcollapse" id="op{$curr_op->operation_id}" onclick="flipGroup({$curr_op->operation_id}, 'op')">
          <td colspan="2">
            <strong>
            Dr. {$curr_op->_ref_chir->_view} &mdash;
            {$curr_op->_ref_plageop->date|date_format:"%A %d %B %Y"} &mdash;
            {$curr_op->_ref_files|@count} fichier(s)
            </strong>
          </td>
        </tr>
        <tr class="op{$curr_op->operation_id}">
          <td colspan="2">
            <a href="index.php?m=dPplanningOp&amp;tab=vw_idx_planning&amp;selChir={$curr_op->_ref_plageop->chir_id}&amp;date={$curr_op->_ref_plageop->date}">
              Voir l'intervention
            </a>
          </td>
        </tr>
        <tr class="op{$curr_op->operation_id}">
          <th>Actes Médicaux :</th>
          <td class="text">
            <ul>
              {foreach from=$curr_op->_ext_codes_ccam item=curr_code}
              <li><strong>{$curr_code->code}</strong> : {$curr_code->libelleLong}</li>
              {/foreach}
            </ul>
          </td>
        </tr>
        <tr class="op{$curr_op->operation_id}">
          <th>Documents créés :</th>
          <td>
          <ul>
            {foreach from=$curr_op->_ref_documents item=document}
            <li>
              {$document->nom}
              <button class="print" onclick="printDocument({$document->compte_rendu_id})">
              </button>
            </li>
            {foreachelse}
            <li>Aucun document créé</li>
            {/foreach}
          </ul>
        </tr>
        <tr class="op{$curr_op->operation_id}">
          <th>Fichiers attachés :</th>
          <td>
            <ul>
              {foreach from=$curr_op->_ref_files item=curr_file}
              <li>
                <form name="uploadFrm{$curr_file->file_id}" action="?m=dPcabinet" enctype="multipart/form-data" method="post">
      
                <input type="hidden" name="dosql" value="do_file_aed" />
                <input type="hidden" name="del" value="1" />
                <input type="hidden" name="file_id" value="{$curr_file->file_id}" />
                <a href="mbfileviewer.php?file_id={$curr_file->file_id}">{$curr_file->file_name}</a>
                ({$curr_file->_file_size}) 
                <button class="trash" type="button" onclick="confirmDeletion(this.form, 'le fichier', '{$curr_file->file_name|escape:javascript}')">
                  supprimer
                </button>
      
                </form>
              </li>
              {foreachelse}
              <li>Aucun fichier attaché</li>
              {/foreach}
            </ul>
            <form name="uploadFrm" action="?m=dPcabinet" enctype="multipart/form-data" method="post">
            <input type="hidden" name="dosql" value="do_file_aed" />
            <input type="hidden" name="del" value="0" />
            <input type="hidden" name="file_class" value="COperation" />
            <input type="hidden" name="file_object_id" value="{$curr_op->operation_id}" />
            <input type="file" name="formfile" />
            <input type="submit" value="ajouter" />

            </form>
          </td>
        {/foreach}
        {/foreach}
        </tr>
      </table>
    </td>
    <td class="pane">
    {include file="../../dPpatients/templates/inc_vw_patient.tpl"}
    </td>
  </tr>
  {/if}
</table>

