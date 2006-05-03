<table class="main">
  <tr>
    <td class="halfpane">
    <form name="GHMFrm" action="?m={$m}" method="get">
    <input type="hidden" name="m" value="{$m}" />
      <table class="form">
        <tr><th class="title" colspan="2">Patient</th></tr>
        <tr>
          <th class="category">Age (ex : 53a ou 120j)</th>
          <th class="category">Sexe</th>
        </tr>
        <tr>
          <td><input type="text" name="age" value="{$GHM->age}" size="4" /></td>
          <td>
            <select name="sexe">
              <option value="masculin" {if $GHM->sexe == "masculin"}selected="selected"{/if}>Masculin</option>
              <option value="féminin" {if $GHM->sexe == "féminin"}selected="selected"{/if}>Féminin</option>
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
          <td><input type="text" name="DP" value="{$GHM->DP}" size="7" /></td>
          <td><input type="text" name="DR" value="{$GHM->DR}" size="7" /></td>
          <td>
            {counter start=0 print=false assign=curr}
            {foreach from=$GHM->DASs item=DAS key=key}
            <input type="text" name="DASs[{$curr}]" value="{$DAS}" size="7" /><br />
            {counter}
            {/foreach}
            <input type="text" name="DASs[{$GHM->DASs|@count}]" value="" size="7" />
          </td>
          <td>
            {counter start=0 print=false assign=curr}
            {foreach from=$GHM->DADs item=DAD key=key}
            <input type="text" name="DADs[{$curr}]" value="{$DAD}" size="7" /><br />
            {counter}
            {/foreach}
            <input type="text" name="DADs[{$GHM->DADs|@count}]" value="" size="7" />
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
        {foreach from=$GHM->actes item=acte key=key}
        <tr>
          <td><input type="text" name="actes[{$curr}][code]" value="{$acte.code}" size="8" /></td>
          <td><input type="text" name="actes[{$curr}][phase]" value="{$acte.phase}" size="2" /></td>
          <td><input type="text" name="actes[{$curr}][activite]" value="{$acte.activite}" size="2" /></td>
        </tr>
        {counter}
        {/foreach}
        <tr>
          <td><input type="text" name="actes[{$GHM->actes|@count}][code]" value="" size="8" /></td>
          <td><input type="text" name="actes[{$GHM->actes|@count}][phase]" value="" size="2" /></td>
          <td><input type="text" name="actes[{$GHM->actes|@count}][activite]" value="" size="2" /></td>
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
            <select name="type_hospi">
              <option value="séance" {if $GHM->type_hospi == "séance"}selected="selected"{/if}>Séance</option>
              <option value="ambu" {if $GHM->type_hospi == "ambu"}selected="selected"{/if}>Ambulatoire ( < 2 jours)</option>
              <option value="comp" {if $GHM->type_hospi == "comp"}selected="selected"{/if}>Hospi. complète( > 2 jours)</option>
              <option value="exte" {if $GHM->type_hospi == "exte"}selected="selected"{/if}>Hospi. externe</option>
            </select>
          </td>
          <td>
            <input type="text" name="duree" value="{$GHM->duree}" size="3" />
          </td>
          <td>
            <input type="text" name="seances" value="{$GHM->seances}" size="3" />
          </td>
          <td>
            <select name="motif">
              <option value="hospi" {if $GHM->motif == "hospi"}selected="selected"{/if}>Hospitalisation</option>
              <option value="décès" {if $GHM->type_hospi == "décès"}selected="selected"{/if}>Décès du patient</option>
              <option value="transfert" {if $GHM->type_hospi == "transfert"}selected="selected"{/if}>Transfert</option>
            </select>
          </td>
          <td>
            <select name="destination">
              <option value="MCO" {if $GHM->destination == "MCO"}selected="selected"{/if}>MCO</option>
              <option value="court séjour" {if $GHM->destination == "court séjour"}selected="selected"{/if}>Court séjour</option>
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
            {if $GHM->CM}
            <strong>Catégorie majeure CM{$GHM->CM}</strong> : {$GHM->CM_nom}
            <br />
            <strong>GHM</strong> : {$GHM->GHM} ({$GHM->tarif_2006} €)
            <br />
            {$GHM->GHM_nom}
            <br />
            <i>Appartenance aux groupes {$GHM->GHM_groupe}</i>
            <br />
            <strong>Bornes d'hospitalisation</strong> : de {$GHM->borne_basse} jour(s) à {$GHM->borne_haute} jours
            <br />
            <strong>Chemin :</strong> <br />
            {$GHM->chemin}
            {else}
            <strong>{$GHM->GHM}</strong>
            {/if}
          </td>
        </tr>
      </table>
    </td>
  </tr>
</table>