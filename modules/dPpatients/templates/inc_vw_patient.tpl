<!-- $Id$ -->

{literal}
<script type="text/javascript">
//<![CDATA[
function printPatient(id) {
  var url = new Url;
  url.setModuleAction("dPpatients", "print_patient");
  url.addParam("patient_id", id);
  url.popup(700, 550, "Patient");
}

function printIntervention(id) {
  var url = new Url;
  url.setModuleAction("dPplanningOp", "view_planning");
  url.addParam("operation_id", id);
  url.popup(700, 550, "Admission");
}
//]]>
</script>
{/literal}

<table class="form">
  <tr>
    <th class="category" colspan="2">Identité</th>
    <th class="category" colspan="2">Informations médicales</th>
  </tr>

  <tr>
    <th>Nom:</th>
    <td>{$patient->nom}</td>
    <th>Incapable majeur:</th>
    <td>
      {if $patient->incapable_majeur == "o"} oui {/if}
      {if $patient->incapable_majeur == "n"} non {/if}
    </td>
  </tr>
  
  <tr>
    <th>Prénom:</th>
    <td>{$patient->prenom}</td>
    <th>ATNC:</th>
    <td>
      {if $patient->ATNC == "o"} oui {/if}
      {if $patient->ATNC == "n"} non {/if}
    </td>
  </tr>
  
  <tr>
    <th>Nom de jeune fille:</th>
    <td>{$patient->nom_jeune_fille}</td>
    <th>Code administratif:</th>
    <td>{$patient->SHS}</td>
  </tr>
  
  <tr>
    <th>Date de naissance:</th>
    <td>{$patient->_jour} / {$patient->_mois} / {$patient->_annee}</td>
    <th>Numéro d'assuré social:</th>
    <td>{$patient->matricule}</td>
  </tr>
  
  <tr>
    <th>Sexe:</th>
    <td>
      {if $patient->sexe == "m"} masculin {/if}
      {if $patient->sexe == "f"} féminin {/if}
      {if $patient->sexe == "j"} femme célibataire {/if} 
    </td>
    <td colspan="2"></td>
  </tr>
  
  <tr>
    <th class="category" colspan="2">Coordonnées</th>
    <th class="category" colspan="2">Remarques</th>
  </tr>
  
  <tr>
    <th>Adresse:</th>
    <td class="text">{$patient->adresse|nl2br:php}</td>
    <td rowspan="5" colspan="2" class="text">{$patient->rques|nl2br:php}</td>
  </tr>
  
  <tr>
    <th>Code Postal:</th>
    <td>{$patient->cp}</td>
  </tr>
  
  <tr>
    <th>Ville:</th>
    <td>{$patient->ville}</td>
  </tr>
  
  <tr>
    <th>Téléphone:</th>
    <td>{$patient->_tel1} {$patient->_tel2} {$patient->_tel3} {$patient->_tel4} {$patient->_tel5}</td>
  </tr>
  
  <tr>
    <th>Portable:</th>
    <td>{$patient->tel2}</td>
  </tr>
  
  {if $canEdit}
  <tr>
    <td class="button" colspan="4">
      <form name="modif" action="./index.php" method="get">
      <input type="hidden" name="m" value="dPpatients" />
      <input type="hidden" name="tab" value="vw_edit_patients" />
      <input type="hidden" name="patient_id" value="{$patient->patient_id}" />
      <button type="submit" class="modify">
        Modifier
      </button>
      <button type="button" class="print" onclick="printPatient({$patient->patient_id})">
        Imprimer
      </button>
      </form>

    </td>
  </tr>
  {/if}
</table>

<table class="form">
  <tr><th class="category" colspan="3">Planifier</th></tr>
  <tr>
    <td class="button">
      <a href="index.php?m=dPplanningOp&amp;tab=vw_edit_planning&amp;pat_id={$patient->patient_id}&amp;operation_id=0&amp;sejour_id=0">
        Une intervention
      </a>
    </td>
    <td class="button">
      <a href="index.php?m=dPplanningOp&amp;tab=vw_edit_sejour&amp;patient_id={$patient->patient_id}&amp;sejour_id=0">
        Un séjour
      </a>
    </td>
    <td class="button">
      <a href="index.php?m=dPcabinet&amp;tab=edit_planning&amp;pat_id={$patient->patient_id}&amp;consultation_id=0">
        Une consultation
      </a>
    </td>
  </tr>
  {if $listPrat|@count && $canEditCabinet}
  <tr><th class="category" colspan="3">Consultation immédiate</th></tr>
  <tr>
    <td class="button" colspan="3">
      <form name="addConsFrm" action="index.php?m=dPcabinet" method="post" onsubmit="return checkForm(this)">
      <input type="hidden" name="m" value="dPcabinet" />
      <input type="hidden" name="dosql" value="do_consult_now" />
      <input type="hidden" name="del" value="0" />
      <input type="hidden" name="patient_id" title="notNull|ref" value="{$patient->patient_id}" />
      <label for="prat_id" title="Praticien pour la consultation immédiate. Obligatoire">Praticien</label>
      <select name="prat_id" title="notNull|ref">
        <option value="">&mdash; Choisir un praticien</option>
        {foreach from=$listPrat item=curr_prat}
          <option value="{$curr_prat->user_id}" {if $curr_prat->user_id == $app->user_id} selected="selected" {/if}>
            {$curr_prat->_view}
          </option>
        {/foreach}
      </select>
      <input type="submit" value="Consulter maintenant" />
      </form>
    </td>
  </tr>
  {/if}
</table>

<table class="form">
  {assign var="affectation" value=$patient->_ref_curr_affectation}
  {if $affectation->affectation_id}
  <tr>
  	<th colspan="3" class="category">Chambre actuelle</th>
  </tr>
  <tr>
    <td colspan="3">
      {$affectation->_ref_lit->_view}
      depuis {$affectation->entree|date_format:"%d %b %Y à %H:%M"}
    </td>
  </tr>
  {assign var="affectation" value=$patient->_ref_next_affectation}
  {elseif $affectation->affectation_id}
  <tr>
    <th colspan="3" class="category">Prochaine chambre</th>
  </tr>
  <tr>
    <td colspan="3">
      {$affectation->_ref_lit->_view}
      depuis {$affectation->entree|date_format:"%d %b %Y à %H:%M"}
    </td>
  </tr>
  {/if}

  {if $patient->_ref_sejours}
  <tr>
    <th colspan="2" class="category">Séjours</th>
  </tr>
  {foreach from=$patient->_ref_sejours item=curr_sejour}
  <tr>
    <td>
      <a class="actionPat" title="Modifier le séjour" href="index.php?m=dPplanningOp&amp;tab=vw_edit_sejour&amp;sejour_id={$curr_sejour->sejour_id}">
        <img src="modules/dPpatients/images/planning.png" alt="Planifier"/>
      </a>
      <a class="actionPat" href="index.php?m=dPadmissions&amp;tab=vw_idx_admission&amp;date={$curr_sejour->entree_prevue|date_format:"%Y-%m-%d"}#adm{$curr_sejour->sejour_id}">
        Séjour du {$curr_sejour->entree_prevue|date_format:"%d %b %Y"} 
        au {$curr_sejour->sortie_prevue|date_format:"%d %b %Y"}
      </a>
	</td>
    {if $curr_sejour->annule}
 	<td style="background: #f00">
      <strong>[SEJOUR ANNULE]</strong>
	</td>
    {else}
 	<td>
      <a href="index.php?m=dPadmissions&amp;tab=vw_idx_admission&amp;date={$curr_sejour->entree_prevue|date_format:"%Y-%m-%d"}#adm{$curr_sejour->sejour_id}">
        Dr. {$curr_sejour->_ref_praticien->_view}
      </a>
	</td>
    {/if}
  </tr>
  {foreach from=$curr_sejour->_ref_operations item=curr_op}
  <tr>
    <td>
      <a class="actionPat" href="javascript:printIntervention({$curr_op->operation_id})">
        <img src="modules/dPpatients/images/print.png" alt="Imprimer" title="Imprimer l'opération"/>
      </a>
      <a class="actionPat" href="index.php?m=dPplanningOp&amp;tab=vw_edit_planning&amp;operation_id={$curr_op->operation_id}">
        <img src="modules/dPpatients/images/planning.png" alt="modifier" title="modifier" />
      </a>
      <a class="actionPat" href="index.php?m=dPplanningOp&amp;tab=vw_edit_planning&amp;operation_id={$curr_op->operation_id}">
        Intervention le {$curr_op->_datetime|date_format:"%d %b %Y"}
      </a>
    </td>
    {if $curr_op->annulee}
 	<td style="background: #f00">
      <strong>[OPERATION ANNULEE]</strong>
	</td>
    {else}
    <td>
      <a href="index.php?m=dPplanningOp&amp;tab=vw_edit_planning&amp;operation_id={$curr_op->operation_id}">
        Dr. {$curr_op->_ref_chir->_view}
      </a>
    </td>
    {/if}
  </tr>
  {/foreach}
  {/foreach}
  {/if}
  
  {if $patient->_ref_consultations}
  <tr><th class="category" colspan="2">Consultations</th></tr>
  {foreach from=$patient->_ref_consultations item=curr_consult}
  <tr>
    <td>
      {if $curr_consult->annule}
      [ANNULE]
      {else}
      <a class="actionPat" href="index.php?m=dPcabinet&amp;tab=edit_planning&amp;consultation_id={$curr_consult->consultation_id}">
        <img src="modules/dPpatients/images/planning.png" alt="modifier" title="modifier" />
      </a>
      {/if}
      <a class="actionPat" href="index.php?m=dPcabinet&amp;tab=edit_consultation&amp;selConsult={$curr_consult->consultation_id}">
        Le {$curr_consult->_ref_plageconsult->date|date_format:"%d %b %Y"}
      </a>
    </td>
    <td>
      <a href="index.php?m=dPcabinet&amp;tab=edit_consultation&amp;selConsult={$curr_consult->consultation_id}">
        Dr. {$curr_consult->_ref_plageconsult->_ref_chir->_view}
      </a>
    </td>
  </tr>
  {/foreach}
  {/if}
</table>