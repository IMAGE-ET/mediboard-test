<table class="form">
  <tr>
    <th class="halfPane category" colspan="2">
      Identité
    </th>
    <th class="halfPane category" colspan="2">
      Coordonnées
    </th>
  </tr>
  
  <tr>
    <th><label for="nom" title="Nom du patient. Obligatoire">Nom </label></th>
    <td><input tabindex="101" type="text" name="nom" value="{{$patient->nom}}" title="{{$patient->_props.nom}}" /></td>
    <th rowspan="2"><label for="adresse" title="Adresse du patient">Adresse</label></th>
    <td rowspan="2"><textarea tabindex="151" name="adresse" title="{{$patient->_props.adresse}}" rows="1">{{$patient->adresse}}</textarea></td>
  </tr>
  
  <tr>
    <th><label for="prenom" title="Prénom du patient. Obligatoire">Prénom </label></th>
    <td><input tabindex="102" type="text" name="prenom" value="{{$patient->prenom}}" title="{{$patient->_props.prenom}}" /></td>
  </tr>
  
  <tr>
    <th><label for="nom_jeune_fille" title="Nom de jeune fille d'une femme mariée">Nom de jeune fille</label></th>
    <td><input tabindex="103" type="text" name="nom_jeune_fille" title="{{$patient->_props.nom_jeune_fille}}" value="{{$patient->nom_jeune_fille}}" /></td>
    <th><label for="cp" title="Code postal">Code Postal</label></th>
    <td>
      <input tabindex="152" size="31" maxlength="5" type="text" name="cp" value="{{$patient->cp}}" title="{{$patient->_props.cp}}" />
      <div style="display:none;" class="autocomplete" id="cp_auto_complete"></div>
    </td>
  </tr>

  <tr>
    <th><label for="sexe" title="Sexe du patient">Sexe</label></th>
    <td>
      <select tabindex="104" name="sexe" title="{{$patient->_props.sexe}}">
        <option value="m" {{if $patient->sexe == "m"}} selected="selected" {{/if}}>masculin</option>
        <option value="f" {{if $patient->sexe == "f"}} selected="selected" {{/if}}>féminin</option>
        <option value="j" {{if $patient->sexe == "j"}} selected="selected" {{/if}}>femme célibataire</option>
      </select>
    </td>
    <th><label for="ville" title="Ville du patient">Ville</label></th>
    <td>
      <input tabindex="153" size="31" type="text" name="ville" value="{{$patient->ville}}" title="{{$patient->_props.ville}}" />
      <div style="display:none;" class="autocomplete" id="ville_auto_complete"></div>
    </td>
  </tr>
  
  <tr>
    <th><label for="_jour" title="Date de naissance du patient, au format JJ-MM-AAAA">Date de naissance</label></th>
    <td>
      <input tabindex="105" type="text" name="_jour"  size="2" maxlength="2" value="{{if $patient->_jour != "00"}}{{$patient->_jour}}{{/if}}" onkeyup="followUp(this, '_mois', 2)" /> -
      <input tabindex="106" type="text" name="_mois"  size="2" maxlength="2" value="{{if $patient->_mois != "00"}}{{$patient->_mois}}{{/if}}" onkeyup="followUp(this, '_annee', 2)" /> -
      <input tabindex="107" type="text" name="_annee" size="4" maxlength="4" value="{{if $patient->_annee != "0000"}}{{$patient->_annee}}{{/if}}" />
    </td>
    <th><label for="pays" title="Pays de domicile du patient">Pays</label></th>
    <td>
      <input tabindex="154" size="31" type="text" name="pays" value="{{$patient->pays}}" title="{{$patient->_props.pays}}" />
      <div style="display:none;" class="autocomplete" id="pays_auto_complete"></div>
    </td>
  </tr>

  <tr>
    <th><label for="lieu_naissance" title="Lieu de naissance du patient">Lieu de naissance</label></th>
    <td><input tabindex="108" type="text" name="lieu_naissance" title="{{$patient->_props.lieu_naissance}}" value="{{$patient->lieu_naissance}}" /></td>
    <th><label for="_tel1" title="Numéro de téléphone filaire">Téléphone</label></th>
    <td>
      <input tabindex="155" type="text" name="_tel1" size="2" maxlength="2" value="{{$patient->_tel1}}" title="num|length|2" onkeyup="followUp(this, '_tel2', 2)" /> - 
      <input tabindex="156" type="text" name="_tel2" size="2" maxlength="2" value="{{$patient->_tel2}}" title="num|length|2" onkeyup="followUp(this, '_tel3', 2)" /> -
      <input tabindex="157" type="text" name="_tel3" size="2" maxlength="2" value="{{$patient->_tel3}}" title="num|length|2" onkeyup="followUp(this, '_tel4', 2)" /> -
      <input tabindex="158" type="text" name="_tel4" size="2" maxlength="2" value="{{$patient->_tel4}}" title="num|length|2" onkeyup="followUp(this, '_tel5', 2)" /> -
      <input tabindex="159" type="text" name="_tel5" size="2" maxlength="2" value="{{$patient->_tel5}}" title="num|length|2" />
    </td>
  </tr>
  
  <tr>
    <th><label for="nationalite" title="Nationnalité du patient">Nationnalité</label></th>
    <td>
      <select tabindex="109" name="nationalite" title="{{$patient->_props.nationalite}}">
        <option value="local" {{if $patient->nationalite == "local"}} selected="selected" {{/if}}>{{tr}}local{{/tr}}</option>
        <option value="etranger" {{if $patient->nationalite == "etranger"}} selected="selected" {{/if}}>{{tr}}etranger{{/tr}}</option>
      </select>
    </td>
    <th><label for="_tel21" title="Numéro de téléphone portable">Portable</label></th>
    <td>
      <input tabindex="160" type="text" name="_tel21" size="2" maxlength="2" value="{{$patient->_tel21}}" title="num|length|2" onkeyup="followUp(this, '_tel22', 2)" /> - 
      <input tabindex="161" type="text" name="_tel22" size="2" maxlength="2" value="{{$patient->_tel22}}" title="num|length|2" onkeyup="followUp(this, '_tel23', 2)" /> -
      <input tabindex="162" type="text" name="_tel23" size="2" maxlength="2" value="{{$patient->_tel23}}" title="num|length|2" onkeyup="followUp(this, '_tel24', 2)" /> -
      <input tabindex="163" type="text" name="_tel24" size="2" maxlength="2" value="{{$patient->_tel24}}" title="num|length|2" onkeyup="followUp(this, '_tel25', 2)" /> -
      <input tabindex="164" type="text" name="_tel25" size="2" maxlength="2" value="{{$patient->_tel25}}" title="num|length|2" />
    </td>
  </tr>
  
  <tr>
    <th><label for="rques" title="Remarques générales concernant le patient">Remarques</label></th>
    <td>
      <textarea tabindex="110" title="{{$patient->_props.rques}}" name="rques">{{$patient->rques}}</textarea>
    </td>
    <th><label for="profession" title="Profession du patient">Profession</label></th>
    <td><input tabindex="165" type="text" name="profession" value="{{$patient->profession}}" title="{{$patient->_props.profession}}" /></td>
  </tr>

</table>