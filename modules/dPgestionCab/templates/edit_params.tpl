<table class="main">
  <tr>
    <td colspan="2">
      <form name="userSelector" action="index.php" method="get">
      <input type="hidden" name="m" value="{$m}" />
      <select name="user_id" onchange="this.form.submit()">
      {foreach from=$listUsers item=curr_user}
        <option value="{$curr_user->user_id}" {if $curr_user->user_id == $user->user_id}selected="selected"{/if}>
          {$curr_user->_view}
        </option>
      {/foreach}
      </select>
      </form>
    </td>
  </tr>
  <tr>
    <td class="halfPane">
      <form name="mediuser" action="./index.php?m={$m}" method="post" onSubmit="return checkForm(this)">
      <input type="hidden" name="dosql" value="do_mediusers_aed" />
      <input type="hidden" name="m" value="mediusers" />
      <input type="hidden" name="user_id" value="{$user->user_id}" />
      <input type="hidden" name="function_id" value="{$user->function_id}" />
      <input type="hidden" name="del" value="0" />
      <table class="form">
        <tr>
          <th class="title" colspan="2">Informations sur l'utilisateur</th>
        </tr>
        <tr>
          <th>Nom :</th>
          <td>{$user->_view}</td>
        </tr>
        <tr>
          <th>Fonction :</th>
          <td>{$user->_user_type}</td>
        </tr>
        <tr>
          <th>
            <label for="_user_adresse" title="Adresse de l'employé">Adresse</label>
          </th>
          <td>
            <input type="text" size="30" name="_user_adresse" title="{$user->_user_props._user_adresse}" value="{$user->_user_adresse}" />
          </td>
        </tr>
        <tr>
          <th>
            <label for="_user_cp" title="Code postal de l'employé">Code Postal</label>
          </th>
          <td>
            <input type="text" size="6" name="_user_cp" title="{$user->_user_props._user_cp}" value="{$user->_user_cp}" />
          </td>
        </tr>
        <tr>
          <th>
            <label for="_user_ville" title="Ville de l'employé">Ville</label>
          </th>
          <td>
            <input type="text" name="_user_ville" title="{$user->_user_props._user_ville}" value="{$user->_user_ville}" />
          </td>
        </tr>
        <tr>
          <td class="button" colspan="2">
            <button class="submit" type="submit">Sauver</button>
          </td>
        </tr>
      </table>
      </form>
    </td>
    <td class="halfPane">
      <form name="params" action="./index.php?m={$m}" method="post" onSubmit="return checkForm(this)">
      <input type="hidden" name="dosql" value="do_paramsPaie_aed" />
      <input type="hidden" name="m" value="dPgestionCab" />
      <input type="hidden" name="params_paie_id" value="{$paramsPaie->params_paie_id}" />
      <input type="hidden" name="user_id" value="{$user->user_id}" />
      <input type="hidden" name="del" value="0" />
      <table class="form">
        <tr>
          <th class="title" colspan="3">Employé</th>
        </tr>
        <tr>
          <th>
            <label for="matricule" title="Code de sécurité sociale">Sécurité sociale</label>
          </th>
          <td colspan="2"><input type="text" name="matricule" title="{$paramsPaie->_props.matricule}" value="{$paramsPaie->matricule}" /></td>
        </tr>
        <tr>
          <th class="title" colspan="3">Employeur</th>
        </tr>
        <tr>
          <th>
            <label for="nom" title="Raison sociale de l'employeur">Nom</label>
          </th>
          <td colspan="2"><input type="text" name="nom" title="{$paramsPaie->_props.nom}" value="{$paramsPaie->nom}" /></td>
        </tr>
        <tr>
          <th>
            <label for="adresse" title="Adresse de l'employeur">Adresse</label>
          </th>
          <td colspan="2"><input type="text" size="30" name="adresse" title="{$paramsPaie->_props.adresse}" value="{$paramsPaie->adresse}" /></td>
        </tr>
        <tr>
          <th>
            <label for="cp" title="Code postal de l'employeur">Code Postal</label>
          </th>
          <td colspan="2"><input type="text" size="6" name="cp" title="{$paramsPaie->_props.cp}" value="{$paramsPaie->cp}" /></td>
        </tr>
        <tr>
          <th>
            <label for="ville" title="Ville de l'employeur">Ville</label>
          </th>
          <td colspan="2"><input type="text" name="ville" title="{$paramsPaie->_props.ville}" value="{$paramsPaie->ville}" /></td>
        </tr>
        <tr>
          <th>
            <label for="siret" title="Numero de SIRET de l'employeur">Siret</label>
          </th>
          <td colspan="2"><input type="text" size="15" name="siret" title="{$paramsPaie->_props.siret}" value="{$paramsPaie->siret}" /></td>
        </tr>
        <tr>
          <th>
            <label for="ape" title="Code APE de l'employeur">Code APE</label>
          </th>
          <td colspan="2"><input type="text" size="5" name="ape" title="{$paramsPaie->_props.ape}" value="{$paramsPaie->ape}" /></td>
        </tr>
        <tr>
          <th class="title" colspan="3">Paramètres fiscaux</th>
        </tr>
        <tr>
          <th>
            <label for="smic" title="Valeur du smic horaire">Smic horaire</label>
          </th>
          <td colspan="2">
            <input type="text" size="5" name="smic" title="{$paramsPaie->_props.smic}" value="{$paramsPaie->smic}" />
            €
          </td>
        </tr>
        <tr>
          <th class="category">Cotisations</th>
          <th class="category">salariales</th>
          <th class="category">patronnales</th>
        </tr>
        <tr>
          <th>CSG déductible :</th>
          <td>
            <input type="text" size="5" name="csgds" title="{$paramsPaie->_props.csgds}" value="{$paramsPaie->csgds}" />
            %
          </td>
          <td>-</td>
        </tr>
        <tr>
          <th>CSG non déductible :</th>
          <td>
            <input type="text" size="5" name="csgnds" title="{$paramsPaie->_props.csgnds}" value="{$paramsPaie->csgnds}" />
            %
          </td>
          <td>-</td>
        </tr>
        <tr>
          <th>S.S. maladie :</th>
          <td>
            <input type="text" size="5" name="ssms" title="{$paramsPaie->_props.ssms}" value="{$paramsPaie->ssms}" />
            %
          </td>
          <td>
            <input type="text" size="5" name="ssmp" title="{$paramsPaie->_props.ssmp}" value="{$paramsPaie->ssmp}" />
            %
          </td>
        </tr>
        <tr>
          <th>S.S. vieillesse :</th>
          <td>
            <input type="text" size="5" name="ssvs" title="{$paramsPaie->_props.ssvs}" value="{$paramsPaie->ssvs}" />
            %
          </td>
          <td>
            <input type="text" size="5" name="ssvp" title="{$paramsPaie->_props.ssvp}" value="{$paramsPaie->ssvp}" />
            %
          </td>
        </tr>
        <tr>
          <th>Retraite complémentaire :</th>
          <td>
            <input type="text" size="5" name="rcs" title="{$paramsPaie->_props.rcs}" value="{$paramsPaie->rcs}" />
            %
          </td>
          <td>
            <input type="text" size="5" name="rcp" title="{$paramsPaie->_props.rcp}" value="{$paramsPaie->rcp}" />
            %
          </td>
        </tr>
        <tr>
          <th>AGFF :</th>
          <td>
            <input type="text" size="5" name="agffs" title="{$paramsPaie->_props.agffs}" value="{$paramsPaie->agffs}" />
            %
          </td>
          <td>
            <input type="text" size="5" name="agffp" title="{$paramsPaie->_props.agffp}" value="{$paramsPaie->agffp}" />
            %
          </td>
        </tr>
        <tr>
          <th>Assurance prévoyance :</th>
          <td>
            <input type="text" size="5" name="aps" title="{$paramsPaie->_props.aps}" value="{$paramsPaie->aps}" />
            %
          </td>
          <td>
            <input type="text" size="5" name="app" title="{$paramsPaie->_props.app}" value="{$paramsPaie->app}" />
            %
          </td>
        </tr>
        <tr>
          <th>Assurance chômage :</th>
          <td>
            <input type="text" size="5" name="acs" title="{$paramsPaie->_props.acs}" value="{$paramsPaie->acs}" />
            %
          </td>
          <td>
            <input type="text" size="5" name="acp" title="{$paramsPaie->_props.acp}" value="{$paramsPaie->acp}" />
            %
          </td>
        </tr>
        <tr>
          <th>Accident du travail :</th>
          <td>-</td>
          <td>
            <input type="text" size="5" name="aatp" title="{$paramsPaie->_props.aatp}" value="{$paramsPaie->aatp}" />
            %
          </td>
        </tr>
        <tr>
          <td class="button" colspan="3">
            <button class="submit" type="submit">Sauver</button>
          </td>
        </tr>
      </table>
      </form>
    </td>
  </tr>
</table>