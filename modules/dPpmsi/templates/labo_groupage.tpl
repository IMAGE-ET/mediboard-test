<table class="main">
  <tr>
    <td class="halfpane">
    <form name="editFrm" action="index.php?m={$m}" method="post" onsubmit="return checkForm(this)">
    <input type="hidden" name="m" value="{$m}" />
    <input type="hidden" name="dosql" value="do_ghm_aed" />
      <input type="hidden" name="del" value="0" />
      <input type="hidden" name="ghm_id" value="{$GHM->ghm_id}" />
      <input type="hidden" name="operation_id" value="{$operation->operation_id}" />
      <table class="form">
        <tr><th class="title" colspan="2">Patient</th></tr>
        <tr>
          <th class="category">Age (ex : 53a ou 120j)</th>
          <th class="category">Sexe</th>
        </tr>
        <tr>
          <td><input type="text" name="_age" value="{$GHM->_age}" size="4" /></td>
          <td>
            <select name="_sexe">
              <option value="masculin" {if $GHM->_sexe == "masculin"}selected="selected"{/if}>Masculin</option>
              <option value="féminin" {if $GHM->_sexe == "féminin"}selected="selected"{/if}>Féminin</option>
            </select>
      </table>
      <table class="form">
        <tr><th class="title" colspan="4">Diagnostics</th></tr>
        <tr>
          <th class="category">DP</th>
          <th class="category">DR</th>
          <th class="category">DAS (sign.)</th>
          <th class="category">DAD (doc.)</th>
        </tr>
        <tr>
          <td><input type="text" name="_DP" value="{$GHM->_DP}" size="7" /></td>
          <td><input type="text" name="DR" value="{$GHM->DR}" size="7" /></td>
          <td>
            {counter start=0 print=false assign=curr}
            {foreach from=$GHM->_DASs item=DAS key=key}
            <input type="text" name="_DASs[{$curr}]" value="{$DAS}" size="7" /><br />
            {counter}
            {/foreach}
            <input type="text" name="_DASs[{$GHM->_DASs|@count}]" value="" size="7" />
          </td>
          <td>
            {counter start=0 print=false assign=curr}
            {foreach from=$GHM->_DADs item=DAD key=key}
            <input type="text" name="_DADs[{$curr}]" value="{$DAD}" size="7" /><br />
            {counter}
            {/foreach}
            <input type="text" name="_DADs[{$GHM->_DADs|@count}]" value="" size="7" />
          </td>
        </tr>
      </table>
      <table class="form">
        <tr><th class="title" colspan="3">Actes</th></tr>
        <tr>
          <th class="category">Code</th>
          <th class="category">Phase</th>
          <th class="category">Activite</th>
        </tr>
        {counter start=0 print=false assign=curr}
        {foreach from=$GHM->_actes item=acte key=key}
        <tr>
          <td><input type="text" name="_actes[{$curr}][code]" value="{$acte.code}" size="8" /></td>
          <td><input type="text" name="_actes[{$curr}][phase]" value="{$acte.phase}" size="2" /></td>
          <td><input type="text" name="_actes[{$curr}][activite]" value="{$acte.activite}" size="2" /></td>
        </tr>
        {counter}
        {/foreach}
        <tr>
          <td><input type="text" name="_actes[{$GHM->actes|@count}][code]" value="" size="8" /></td>
          <td><input type="text" name="_actes[{$GHM->actes|@count}][phase]" value="" size="2" /></td>
          <td><input type="text" name="_actes[{$GHM->actes|@count}][activite]" value="" size="2" /></td>
        </tr>
      </table>
      <table class="form">
        <tr><th class="title" colspan="5">Hospi</th></tr>
        <tr>
          <th class="category">Type</th>
          <th class="category">Durée (jours)</th>
          <th class="category">Nb. de séances</th>
          <th class="category">Motif de séjour</th>
          <th class="category">Destination</th>
        </tr>
        <tr>
          <td>
            <select name="_type_hospi">
              <option value="séance" {if $GHM->_type_hospi == "séance"}selected="selected"{/if}>Séance</option>
              <option value="ambu" {if $GHM->_type_hospi == "ambu"}selected="selected"{/if}>Ambulatoire ( < 2 jours)</option>
              <option value="comp" {if $GHM->_type_hospi == "comp"}selected="selected"{/if}>Hospi. complète( > 2 jours)</option>
              <option value="exte" {if $GHM->_type_hospi == "exte"}selected="selected"{/if}>Hospi. externe</option>
            </select>
          </td>
          <td>
            <input type="text" name="_duree" value="{$GHM->_duree}" size="3" />
          </td>
          <td>
            <input type="text" name="_seances" value="{$GHM->_seances}" size="3" />
          </td>
          <td>
            <select name="_motif">
              <option value="hospi" {if $GHM->_motif == "hospi"}selected="selected"{/if}>Hospitalisation</option>
              <option value="décès" {if $GHM->_motif == "décès"}selected="selected"{/if}>Décès du patient</option>
              <option value="transfert" {if $GHM->_motif == "transfert"}selected="selected"{/if}>Transfert</option>
            </select>
          </td>
          <td>
            <select name="_destination">
              <option value="MCO" {if $GHM->_destination == "MCO"}selected="selected"{/if}>MCO</option>
              <option value="court séjour" {if $GHM->_destination == "court séjour"}selected="selected"{/if}>Court séjour</option>
            </select>
          </td>
        </tr>
        <tr>
          <td class="button" colspan="5">
            <button type="submit">Calculer</button>
          </td>
        </tr>
      </table>
      </form>
    </td>
    <td class="halfpane">
      <table class="tbl">
        <tr>
          <th class="title">
            Résultat
          </th>
        </tr>
        <tr>
          <td class="text">
            {if $GHM->_CM}
            <strong>Catégorie majeure CM{$GHM->_CM}</strong> : {$GHM->_CM_nom}
            <br />
            <strong>GHM</strong> : {$GHM->_GHM} ({$GHM->_tarif_2006} €)
            <br />
            {$GHM->_GHM_nom}
            <br />
            <i>Appartenance aux groupes {$GHM->_GHM_groupe}</i>
            <br />
            <strong>Bornes d'hospitalisation</strong> : de {$GHM->_borne_basse} jour(s) à {$GHM->_borne_haute} jours
            <br />
            <strong>Chemin :</strong> <br />
            {$GHM->_chemin}
            {else}
            <strong>{$GHM->_GHM}</strong>
            {/if}
          </td>
        </tr>
      </table>
    </td>
  </tr>
</table>