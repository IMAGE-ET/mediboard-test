<table class="main">
  <tr>
    <td colspan="2">
      <form name="employeSelector" action="index.php" method="get">
      <input type="hidden" name="m" value="{{$m}}" />
      <label for="employecab_id" title="Veuillez sélectionner l'utilisateur concerné">Employé Concerné</label>
      <select name="employecab_id" onchange="this.form.submit()">
        <option value="">&mdash; Nouvel employé</option>
      {{foreach from=$listEmployes item=curr_emp}}
        <option value="{{$curr_emp->employecab_id}}" {{if $curr_emp->employecab_id == $employe->employecab_id}}selected="selected"{{/if}}>
          {{$curr_emp->_view}}
        </option>
      {{/foreach}}
      </select>
      </form>
    </td>
  </tr>
  <tr>
    <td class="halfPane">
      <form name="editEmploye" action="./index.php?m={{$m}}" method="post" onSubmit="return checkForm(this)">
      <input type="hidden" name="dosql" value="do_employe_aed" />
      <input type="hidden" name="m" value="dPgestionCab" />
      <input type="hidden" name="employecab_id" value="{{$employe->employecab_id}}" />
      <input type="hidden" name="function_id" value="{{$employe->function_id}}" />
      <input type="hidden" name="del" value="0" />
      <table class="form">
        <tr>
          {{if $employe->employecab_id}}
          <th class="title" colspan="2">Modification de {{$employe->_view}}</th>
          {{else}}
          <th class="title" colspan="2">Création d'un employé</th>
          {{/if}}
        </tr>
        <tr>
          <th><label for="nom" title="Nom de l'employé">Nom</label></th>
          <td>
            <input type="text" name="nom" class="{{$employe->_props.nom}}" value="{{$employe->nom}}" />
          </td>
        </tr>
        <tr>
          <th><label for="prenom" title="Prénom de l'employé">Prénom</label></th>
          <td>
            <input type="text" name="prenom" class="{{$employe->_props.prenom}}" value="{{$employe->prenom}}" />
          </td>
        </tr>
        <tr>
          <th><label for="function" title="Fonction de l'employé">Fonction</label></th>
          <td>
            <input type="text" name="function" class="{{$employe->_props.function}}" value="{{$employe->function}}" />
          </td>
        </tr>
        <tr>
          <th>
            <label for="adresse" title="Adresse de l'employé">Adresse</label>
          </th>
          <td>
            <input type="text" size="30" name="adresse" class="{{$employe->_props.adresse}}" value="{{$employe->adresse}}" />
          </td>
        </tr>
        <tr>
          <th>
            <label for="cp" title="Code postal de l'employé">Code Postal</label>
          </th>
          <td>
            <input type="text" size="6" name="cp" class="{{$employe->_props.cp}}" value="{{$employe->cp}}" />
          </td>
        </tr>
        <tr>
          <th>
            <label for="ville" title="Ville de l'employé">Ville</label>
          </th>
          <td>
            <input type="text" name="ville" class="{{$employe->_props.ville}}" value="{{$employe->ville}}" />
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
      {{if $employe->employecab_id}}
      <form name="params" action="./index.php?m={{$m}}" method="post" onSubmit="return checkForm(this)">
      <input type="hidden" name="dosql" value="do_paramsPaie_aed" />
      <input type="hidden" name="m" value="dPgestionCab" />
      <input type="hidden" name="params_paie_id" value="{{$paramsPaie->params_paie_id}}" />
      <input type="hidden" name="employecab_id" value="{{$employe->employecab_id}}" />
      <input type="hidden" name="del" value="0" />
      <table class="form">
        <tr>
          <th class="title" colspan="3">Employé</th>
        </tr>
        <tr>
          <th>
            <label for="matricule" title="Code de sécurité sociale">Sécurité sociale</label>
          </th>
          <td colspan="2">
            <input type="text" name="matricule" class="{{$paramsPaie->_props.matricule}}" value="{{$paramsPaie->matricule}}" />
          </td>
        </tr>
        <tr>
          <th class="title" colspan="3">Employeur</th>
        </tr>
        <tr>
          <th>
            <label for="nom" title="Raison sociale de l'employeur">Nom</label>
          </th>
          <td colspan="2">
            <input type="text" name="nom" class="{{$paramsPaie->_props.nom}}" value="{{$paramsPaie->nom}}" />
          </td>
        </tr>
        <tr>
          <th>
            <label for="adresse" title="Adresse de l'employeur">Adresse</label>
          </th>
          <td colspan="2">
            <input type="text" size="30" name="adresse" class="{{$paramsPaie->_props.adresse}}" value="{{$paramsPaie->adresse}}" />
          </td>
        </tr>
        <tr>
          <th>
            <label for="cp" title="Code postal de l'employeur">Code Postal</label>
          </th>
          <td colspan="2">
            <input type="text" size="6" name="cp" class="{{$paramsPaie->_props.cp}}" value="{{$paramsPaie->cp}}" />
          </td>
        </tr>
        <tr>
          <th>
            <label for="ville" title="Ville de l'employeur">Ville</label>
          </th>
          <td colspan="2">
            <input type="text" name="ville" class="{{$paramsPaie->_props.ville}}" value="{{$paramsPaie->ville}}" />
          </td>
        </tr>
        <tr>
          <th>
            <label for="siret" title="Numero de SIRET de l'employeur">Siret</label>
          </th>
          <td colspan="2">
            <input type="text" size="15" name="siret" class="{{$paramsPaie->_props.siret}}" value="{{$paramsPaie->siret}}" />
          </td>
        </tr>
        <tr>
          <th>
            <label for="ape" title="Code APE de l'employeur">Code APE</label>
          </th>
          <td colspan="2">
            <input type="text" size="5" name="ape" class="{{$paramsPaie->_props.ape}}" value="{{$paramsPaie->ape}}" />
          </td>
        </tr>
        <tr>
          <th class="title" colspan="3">Paramètres fiscaux</th>
        </tr>
        <tr>
          <th>
            <label for="smic" title="Valeur du smic horaire">Smic horaire</label>
          </th>
          <td colspan="2">
            <input type="text" size="5" name="smic" class="{{$paramsPaie->_props.smic}}" value="{{$paramsPaie->smic}}" />
            €
          </td>
        </tr>
        <tr>
          <th class="category">Cotisations</th>
          <th class="category">salariales</th>
          <th class="category">patronnales</th>
        </tr>
        <tr>
          <th><label for="csgds" title="CSG déductible">CSG déductible :</label></th>
          <td>
            <input type="text" size="5" name="csgds" class="{{$paramsPaie->_props.csgds}}" value="{{$paramsPaie->csgds}}" />
            %
          </td>
          <td>-</td>
        </tr>
        <tr>
          <th>
            <label for="csgnds" title="CSG non déductible">CSG non déductible :</label>
          </th>
          <td>
            <input type="text" size="5" name="csgnds" class="{{$paramsPaie->_props.csgnds}}" value="{{$paramsPaie->csgnds}}" />
            %
          </td>
          <td>-</td>
        </tr>
        <tr>
          <th>
            <label for="ssms" title="S.S. maladie">S.S. maladie :</label>`
          </th>
          <td>
            <input type="text" size="5" name="ssms" class="{{$paramsPaie->_props.ssms}}" value="{{$paramsPaie->ssms}}" />
            %
          </td>
          <td>
            <input type="text" size="5" name="ssmp" class="{{$paramsPaie->_props.ssmp}}" value="{{$paramsPaie->ssmp}}" />
            %
          </td>
        </tr>
        <tr>
          <th>
            <label for="ssvs" title="S.S. vieillesse">S.S. vieillesse :</label>
          </th>
          <td>
           <input type="text" size="5" name="ssvs" class="{{$paramsPaie->_props.ssvs}}" value="{{$paramsPaie->ssvs}}" />
            %
          </td>
          <td>
            <input type="text" size="5" name="ssvp" class="{{$paramsPaie->_props.ssvp}}" value="{{$paramsPaie->ssvp}}" />
            %
          </td>
        </tr>
        <tr>
          <th>
            <label for="rcs" title="Retraite complémentaire">Retraite complémentaire :</label>
          </th>
          <td>
            <input type="text" size="5" name="rcs" class="{{$paramsPaie->_props.rcs}}" value="{{$paramsPaie->rcs}}" />
            %
          </td>
          <td>
            <input type="text" size="5" name="rcp" class="{{$paramsPaie->_props.rcp}}" value="{{$paramsPaie->rcp}}" />
            %
          </td>
        </tr>
        <tr>
          <th>
            <label for="agffs" title="AGFF">AGFF :</label>
          </th>
          <td>
            <input type="text" size="5" name="agffs" class="{{$paramsPaie->_props.agffs}}" value="{{$paramsPaie->agffs}}" />
            %
          </td>
          <td>
            <input type="text" size="5" name="agffp" class="{{$paramsPaie->_props.agffp}}" value="{{$paramsPaie->agffp}}" />
            %
          </td>
        </tr>
        <tr>
          <th>
            <label for="aps" title="Assurance prévoyance">Assurance prévoyance :</label>
          </th>
          <td>
            <input type="text" size="5" name="aps" class="{{$paramsPaie->_props.aps}}" value="{{$paramsPaie->aps}}" />
            %
          </td>
          <td>
            <input type="text" size="5" name="app" class="{{$paramsPaie->_props.app}}" value="{{$paramsPaie->app}}" />
            %
          </td>
        </tr>
        <tr>
          <th>
            <label for="acs" title="AGFF">Assurance chômage :</label>
          </th>
          <td>
            <input type="text" size="5" name="acs" class="{{$paramsPaie->_props.acs}}" value="{{$paramsPaie->acs}}" />
            %
          </td>
          <td>
            <input type="text" size="5" name="acp" class="{{$paramsPaie->_props.acp}}" value="{{$paramsPaie->acp}}" />
            %
          </td>
        </tr>
        <tr>
          <th>
            <label for="aatp" title="Accident du travail">Accident du travail :</label>
          </th>
          <td>-</td>
          <td>
            <input type="text" size="5" name="aatp" class="{{$paramsPaie->_props.aatp}}" value="{{$paramsPaie->aatp}}" />
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
      {{else}}
      <table class="form">
        <tr>
          <th class="title">
            Veuillez sélectionner ou créer un employé
          </th>
        </tr>
      </table>
      {{/if}}
    </td>
  </tr>
</table>