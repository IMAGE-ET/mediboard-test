{assign var="patient" value=$consult->_ref_patient}
{assign var="consult_anesth" value=$consult->_ref_consult_anesth}
<table class="form" id="admission">
  <tr>
    <td colspan="2">
      <table width="100%" style="font-size: 120%; padding-bottom: 50px;">
        <tr>
          <th class="title" colspan="2">
            <a href="javascript:window.print()">
              Consultation pré-anesthésique
            </a>
          </th>
        </tr>
        <tr>
          <th>Date: </th>
          <td>{$consult->_ref_plageconsult->date|date_format:"%A %d %B %Y"}</td>
        </tr>
        <tr>
          <th>Anesthésiste: </th>
          <td>Dr. {$consult->_ref_chir->_view}</td>
        </tr>
      </table>
    </td>
  </tr>
  <tr>
    <td class="halfPane">
      <table width="100%" style="font-size: 120%; padding-bottom: 50px;">
        <tr>
          <th class="category" colspan="2">
            Informations sur le patient
          </th>
        </tr>
        <tr>
          <th>Nom / Prenom: </th>
          <td>{$patient->_view} ({$patient->_age} ans)</td>
        </tr>
        <tr>
          <th>Date de naissance / Sexe: </th>
          <td>
            né(e) le {$patient->_jour}/{$patient->_mois}/{$patient->_annee}
            de sexe {if $patient->sexe == "m"} masculin {else} féminin {/if}
          </td>
        </tr>
        <tr>
          <th>Poids: </th>
          <td>{$consult->_ref_consult_anesth->poid} kg</td>
        </tr>
        <tr>
          <th>Taille: </th>
          <td>{$consult->_ref_consult_anesth->taille} m</td>
        </tr>
        <tr>
          <th>Groupe sanguin: </th>
          <td>{$consult->_ref_consult_anesth->groupe} {$consult->_ref_consult_anesth->rhesus}</td>
        </tr>
        <tr>
          <th>Tension: </th>
          <td>{$consult->_ref_consult_anesth->tasys} / {$consult->_ref_consult_anesth->tadias}</td>
        </tr>
        <tr>
          <th>Tabac: </th>
          <td>{$consult->_ref_consult_anesth->tabac}</td>
        </tr>
        <tr>
          <th>Oenolisme: </th>
          <td>{$consult->_ref_consult_anesth->oenolisme}</td>
        </tr>
        <tr>
          <th>Transfusions: </th>
          <td>{$consult->_ref_consult_anesth->transfusions}</td>
        </tr>
      </table>
    </td>
    <td class="halfPane">
      <table width="100%" style="font-size: 120%; padding-bottom: 50px;">
        <tr>
          <th class="category" colspan="2">
            Antécédents / Traitements
          </th>
        </tr>
        <tr>
          <td colspan="2">
            <strong>Diagnostics du patient</strong>
            <ul>
              {foreach from=$patient->_codes_cim10 item=curr_code}
              <li>
                {$curr_code->code}: {$curr_code->libelle}
              </li>
              {foreachelse}
              <li>Pas de diagnostic</li>
              {/foreach}
            </ul>
            <strong>Antécédents du patient</strong>
            <ul>
              {foreach from=$patient->_ref_antecedents item=curr_ant}
              <li>
                {$curr_ant->type} le {$curr_ant->date|date_format:"%d/%m/%Y"} :
                <i>{$curr_ant->rques}</i>
                </form>
              </li>
              {foreachelse}
              <li>Pas d'antécédents</li>
              {/foreach}
            </ul>
            <strong>Traitements du patient</strong>
            <ul>
              {foreach from=$patient->_ref_traitements item=curr_trmt}
              <li>
                {if $curr_trmt->fin}
                  Du {$curr_trmt->debut|date_format:"%d/%m/%Y"} au {$curr_trmt->fin|date_format:"%d/%m/%Y"}
                {else}
                  Depuis le {$curr_trmt->debut|date_format:"%d/%m/%Y"}
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
      </table>
    </td>
  </tr>
  <tr>
    <td colspan="2">
      <table width="100%" style="font-size: 120%; padding-bottom: 50px;">
        <tr>
          <th class="category" colspan="2">
            Intervention
          </th>
        </tr>
        <tr>
          <td colspan="2">
            Intervention le <strong>{$consult_anesth->_ref_operation->_ref_plageop->date|date_format:"%a %d %b %Y"}</strong>
            par le <strong>Dr. {$consult_anesth->_ref_operation->_ref_chir->_view}</strong><br />
            <ul>
              {foreach from=$consult_anesth->_ref_operation->_ext_codes_ccam item=curr_code}
              <li><em>{$curr_code->libelleLong}</em> ({$curr_code->code})</li>
              {/foreach}
            </ul>
          </td>
        </tr>
        <tr>
          <th>Anesthésie prévue: </th>
          <td>{$consult_anesth->_ref_operation->_lu_type_anesth}</td>
        </tr>
      </table>
    </td>
  </tr>
</table>