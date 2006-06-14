<!-- $Id$ -->

{literal}
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

function popPat() {
  var url = new Url();
  url.setModuleAction("dPpatients", "pat_selector");
  url.popup(500, 500, "Patient");
}

function setPat( key, val ) {
  var f = document.patFrm;
  if (val != '') {
    f.pat_id.value = key;
    f.patNom.value = val;
  }
  f.submit();
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

function pageMain() {
  initGroups("sejour");
  {/literal}
  {foreach from=$patient->_ref_operations item=curr_op}
  exporterDossier({$curr_op->operation_id}, {ldelim}onlySentFiles : true{rdelim});
  {/foreach}
  {literal}
}

</script>
{/literal}

<table class="main">
  <tr>
    <td>
      <form name="patFrm" action="index.php" method="get">
      <table class="form">
        <tr><th>Choix du patient :</th>
          <td class="readonly">
            <input type="hidden" name="m" value="dPpmsi" />
            <input type="hidden" name="pat_id" value="{$patient->patient_id}" />
            <input type="text" readonly="readonly" name="patNom" value="{$patient->_view}" />
          </td>
          <td class="button">
            <input type="button" value="chercher" onclick="popPat()" />
          </td>
        </tr>
      </table>
      </form>
      {if $patient->patient_id}
      {include file="../../dPpatients/templates/inc_vw_patient.tpl"}
      {/if}
    </td>
    {if $patient->patient_id}
    <td>
      <table class="form">
        <tr>
          <th colspan="4" class="title">Liste des séjours</th>
        </tr>
        {foreach from=$patient->_ref_sejours item=curr_sejour}
        {assign var="GHM" value=$curr_sejour->_ref_GHM}
        <tr class="groupcollapse" id="sejour{$curr_sejour->sejour_id}" onclick="flipGroup({$curr_sejour->sejour_id}, 'sejour')">
          <td colspan="4" style="background-color:#aaf;">
          	Dr. {$curr_sejour->_ref_praticien->_view} -
          	Séjour du {$curr_sejour->entree_prevue|date_format:"%d %b %Y (%Hh%M)"}
          	au {$curr_sejour->sortie_prevue|date_format:"%d %b %Y (%Hh%M)"}
          </td>
        </tr>
        <tr class="sejour{$curr_sejour->sejour_id}">
          <th>Diagnostics du patient</th>
          <td class="text" colspan="3">
            <ul>
              {foreach from=$patient->_codes_cim10 item=curr_code}
              <li>
                {$curr_code->code}: {$curr_code->libelle}
              </li>
              {foreachelse}
              <li>Pas de diagnostic</li>
              {/foreach}
            </ul>
          </td>
        </tr>
        <tr class="sejour{$curr_sejour->sejour_id}">
          <th>Antécedents du patient</th>
          <td class="text" colspan="3">
            <ul>
              {foreach from=$patient->_ref_antecedents item=curr_ant}
              <li>
                {$curr_ant->type} le {$curr_ant->date|date_format:"%d %b %Y"} :
                <i>{$curr_ant->rques}</i>
                </form>
              </li>
              {foreachelse}
              <li>Pas d'antécédents</li>
              {/foreach}
            </ul>
          </td>
        </tr>
        <tr class="sejour{$curr_sejour->sejour_id}">
          <th>Traitements du patient</th>
          <td class="text" colspan="3">
            <ul>
              {foreach from=$patient->_ref_traitements item=curr_trmt}
              <li>
                {if $curr_trmt->fin}
                  Du {$curr_trmt->debut|date_format:"%d %b %Y"} au {$curr_trmt->fin|date_format:"%d %b %Y"}
                {else}
                  Depuis le {$curr_trmt->debut|date_format:"%d %b %Y"}
                {/if}
                : <i>{$curr_trmt->traitement}</i>
                </form>
              </li>
              {foreachelse}
              <li>Pas de traitements</li>
              {/foreach}
            </ul>
          </td>
        </tr>
        {foreach from=$curr_sejour->_ref_operations item=curr_op}
        <tr class="sejour{$curr_sejour->sejour_id}">
          <th class="category" colspan="4">
            Dr. {$curr_op->_ref_chir->_view}
            &mdash; {$curr_op->_ref_plageop->date|date_format:"%A %d %B %Y"}
            &mdash; {$curr_op->_ref_plageop->_ref_salle->nom}
          </th>
        </tr>
        {foreach from=$curr_op->_ext_codes_ccam item=curr_code}
        <tr class="sejour{$curr_sejour->sejour_id}">
          <th>{$curr_code->code}</th>
          <td class="text" colspan="3">{$curr_code->libelleLong}</td>
        </tr>
        {/foreach}
        {if $curr_op->_ref_consult_anesth->consultation_anesth_id}
        <tr class="sejour{$curr_sejour->sejour_id}">
          <td colspan="4">
            <strong>Consultation pré-anesthésique</strong>
          </td>
        </tr>
        <tr class="sejour{$curr_sejour->sejour_id}">
          <th>Consultation</th>
          <td class="text" colspan="3">
            Le {$curr_op->_ref_consult_anesth->_ref_plageconsult->date|date_format:"%A %d %b %Y"}
            avec le Dr. {$curr_op->_ref_consult_anesth->_ref_plageconsult->_ref_chir->_view}
          </td>
        </tr>
        <tr class="sejour{$curr_sejour->sejour_id}">
          <td class="button">Poids</td>
          <td class="button">Taille</td>
          <td class="button">Groupe</td>
          <td class="button">Tension</td>
        </tr>
        <tr class="sejour{$curr_sejour->sejour_id}">
          <td class="button">{$curr_op->_ref_consult_anesth->poid} kg</td>
          <td class="button">{$curr_op->_ref_consult_anesth->taille} cm</td>
          <td class="button">{$curr_op->_ref_consult_anesth->groupe} {$curr_op->_ref_consult_anesth->rhesus}</td>
          <td class="button">{$curr_op->_ref_consult_anesth->tasys}/{$curr_op->_ref_consult_anesth->tadias}</td>
        </tr>
        {/if}
        <tr class="sejour{$curr_sejour->sejour_id}">
          <td colspan="4">
            <strong>Intervention</strong>
          </td>
        </tr>
        <tr class="sejour{$curr_sejour->sejour_id}">
          <th>Date</th>
          <td class="text" colspan="3">
            Le {$curr_op->_ref_plageop->date|date_format:"%A %d %b %Y"}
            par le Dr. {$curr_op->_ref_chir->_view},
          </td>
        </tr>
        <tr class="sejour{$curr_sejour->sejour_id}">
          <th rowspan="6">Heures</th>
          <th>Entrée en salle</th>
          <td>
            {$curr_op->entree_bloc|date_format:"%Hh%M"}
          </td>
          <td rowspan="6">
            <form name="editPatFrm{$curr_op->operation_id}" action="?m={$m}" method="post" onsubmit="return true;">
            <input type="hidden" name="dosql" value="do_patients_aed" />
            <input type="hidden" name="m" value="dPpatients" />
            <input type="hidden" name="del" value="0" />
            <input type="hidden" name="patient_id" value="{$curr_op->_ref_pat->patient_id}" />
 
            <table class="form">
              <tr>
                <th class="category" colspan="2">
                  <em>Lien S@nté.com</em> : Patient 
                  <input type="submit" value="Valider" />
                </th>
              </tr>
              <tr>
                <th><label for="SHS" title="Choisir un identifiant de patient correspondant à l'opération">Identifiant de patient</label></th>
                <td><input type="text" title="notNull|num|length|8" name="SHS" value="{$curr_op->_ref_pat->SHS}" size="8" maxlength="8" /></td>
              </tr>
            </table>
 
            </form>
 
            <form name="editSejourFrm{$curr_op->operation_id}" action="?m={$m}" method="post" onsubmit="return checkForm(this)">
            <input type="hidden" name="dosql" value="do_sejour_aed" />
            <input type="hidden" name="m" value="dPplanningOp" />
            <input type="hidden" name="del" value="0" />
            <input type="hidden" name="operation_id" value="{$curr_op->operation_id}" />
 
            <table class="form">
              <tr>
                <th class="category" colspan="2">
                  <em>Lien S@nté.com</em> : Séjour
                  <input type="submit" value="Valider" />
                </th>
              </tr>
              <tr>
                <th>
                  <label for="venue_SHS" title="Choisir un identifiant pour la venue correspondant à l'opération">Identifiant de venue :</label><br />
                  Suggestion :
                </th>
                <td>
                  <input type="text" title="{$curr_sejour->_props.venue_SHS}" name="venue_SHS" value="{$curr_sejour->venue_SHS}" size="8" maxlength="8" /><br />
                  {$curr_sejour->_venue_SHS_guess}
                </td>
              </tr>
            </table>
            
            </form>
            
            <form name="editOpFrm{$curr_op->operation_id}" action="?m={$m}" method="post" onsubmit="return checkForm(this)">
            <input type="hidden" name="dosql" value="do_planning_aed" />
            <input type="hidden" name="m" value="dPplanningOp" />
            <input type="hidden" name="del" value="0" />
            <input type="hidden" name="operation_id" value="{$curr_op->operation_id}" />
 
            <table class="form">
              <tr>
                <th class="category" colspan="2">
                  <em>Lien S@nté.com</em> : Intervention
                  <input type="submit" value="Valider" />
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
                <th><label for="code_uf" title="Choisir un code pour l'unité fonctionnelle">Code d'unité fonct. :</label></th>
                <td><input type="text" title="notNull|str|maxLength|10" name="code_uf" value="{$curr_op->code_uf}" size="10" maxlength="10" /></td>
              </tr>
              <tr>
                <th><label for="libelle_uf" title="Choisir un libellé pour l'unité fonctionnelle">Libellé d'unité fonct. :</label></th>
                <td><input type="text" title="notNull|str|maxLength|35" name="libelle_uf" value="{$curr_op->libelle_uf}" size="20" maxlength="35" /></td>
              </tr>
            </table>
            
            </form>
            
            <table class="form">
              <tr>
                <td class="button">
                  <button type="button" onclick="submitSHSLink()" >Valider</button>
                </td>
              </tr>
            </table>
          </td>
        </tr>
        <tr class="sejour{$curr_sejour->sejour_id}">
          <th>Pose du garrot</th>
          <td>
            {$curr_op->pose_garrot|date_format:"%Hh%M"}
          </td>
        </tr>
        <tr class="sejour{$curr_sejour->sejour_id}">
          <th>Début d'intervention</th>
          <td>
            {$curr_op->debut_op|date_format:"%Hh%M"}
          </td>
        </tr>
        <tr class="sejour{$curr_sejour->sejour_id}">
          <th>Fin d'intervention</th>
          <td>
            {$curr_op->fin_op|date_format:"%Hh%M"}
          </td>
        </tr>
        <tr class="sejour{$curr_sejour->sejour_id}">
          <th>Retrait du garrot</th>
          <td>
            {$curr_op->retrait_garrot|date_format:"%Hh%M"}
          </td>
        </tr>
        <tr class="sejour{$curr_sejour->sejour_id}">
          <th>Sortie de salle</th>
          <td>
            {$curr_op->sortie_bloc|date_format:"%Hh%M"}
          </td>
        </tr>
        <tr class="sejour{$curr_sejour->sejour_id}">
          <td class="button">supprimer</td>
          <td class="button"><strong>Code</strong></td>
          <td class="button"><strong>Activité</strong></td>
          <td class="button"><strong>Phase &mdash; Modificateurs</strong></td>
        </tr>
        {foreach from=$curr_op->_ref_actes_ccam item=curr_acte}
        <tr class="sejour{$curr_sejour->sejour_id}">
          <td class="button">
            <form name="formActe-{$curr_acte->_view}" action="?m={$m}" method="post" onsubmit="return checkForm(this)">
            <input type="hidden" name="m" value="dPsalleOp" />
            <input type="hidden" name="dosql" value="do_acteccam_aed" />
            <input type="hidden" name="del" value="1" />
            <input type="hidden" name="acte_id" value="{$curr_acte->acte_id}" />
            <button type="submit">
              <img src="modules/dPpmsi/images/cross.png" />
            </button>
            </form>
          </td>
          <td class="text">{$curr_acte->_ref_executant->_view} : {$curr_acte->code_acte}</td>
          <td class="button">{$curr_acte->code_activite}</td>
          <td class="button">
            {$curr_acte->code_phase}
            {if $curr_acte->modificateurs}
              &mdash; {$curr_acte->modificateurs}
            {/if}
          </td>
        </tr>
        {/foreach}
        {foreach from=$curr_op->_ref_documents item=document}
        <tr class="sejour{$curr_sejour->sejour_id}">
          <th>{$document->nom}</th>
          {if $document->source}
          <td colspan="3">
            <button onclick="imprimerDocument({$document->compte_rendu_id})">
              <img src="modules/dPpmsi/images/print.png" />
            </button>
          </td>
          {else}
          <td colspan="3">
            -
          </td>
          {/if}
        </tr>
        {/foreach}
        <tr class="sejour{$curr_sejour->sejour_id}">
          <td class="button" colspan="4">
            <a class="button" href="?m=dPpmsi&amp;tab=edit_actes&amp;operation_id={$curr_op->operation_id}">
              Modifier les actes
            </a>
            <button onclick="exporterDossier({$curr_op->operation_id})">Exporter vers S@nté.com</button>
          </td>
        </tr>
        <tr class="sejour{$curr_sejour->sejour_id}">
          <td class="text" id="hprim_export{$curr_op->operation_id}" colspan="4">
          </td>
        </tr>
        {/foreach}
        <tr class="sejour{$curr_sejour->sejour_id}">
          <th class="category" colspan="4">
            Groupage
            <a href="index.php?m=dPpmsi&amp;tab=labo_groupage&amp;sejour_id={$curr_sejour->sejour_id}">
              (envoyer vers le labo)
            </a>
          </th>
        </tr>
        <tr class="sejour{$curr_sejour->sejour_id}">
          <td colspan="4">
            <form name="editFrm" action="?m={$m}" method="post">
            <input type="hidden" name="m" value="dPplanningOp" />
            <input type="hidden" name="dosql" value="do_sejour_aed" />
            <input type="hidden" name="del" value="0" />
            <input type="hidden" name="sejour_id" value="{$curr_sejour->sejour_id}" />
            Diagnostic principal :
            <input type="text" name="DP" value="{$curr_sejour->DP}"/>
            <button type="submit">Modifier</button>
            </form>
            {if $curr_sejour->_ref_GHM->_CM}
            <strong>Catégorie majeure CM{$GHM->_CM}</strong> : {$GHM->_CM_nom}
            <br />
            <strong>GHM</strong> : {$GHM->_GHM} ({$GHM->_tarif_2006} €)
            <br />
            {$GHM->_GHM_nom}
            <br />
            <i>Appartenance aux groupes {$GHM->_GHM_groupe}</i>
            <br />
            <strong>Bornes d'hospitalisation</strong> :
            de {$GHM->_borne_basse} jour(s)
            à {$GHM->_borne_haute} jours
            {else}
            <strong>{$GHM->_GHM}</strong>
            {/if}
          </td>
        </tr>
        {/foreach}
       </table>
      {/if}
    </td>
  </tr>
</table>

