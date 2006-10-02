<table class="form">
  <tr>
    <th class="halfPane category" colspan="2">
      Personne à prévenir
    </th>
    <th class="halfPane category" colspan="2">
      Employeur
    </th>
  </tr>

  <tr>
    <th><label for="prevenir_nom" title="Nom de la personne à prévenir">Nom</label></th>
    <td><input tabindex="301" type="text" name="prevenir_nom" value="{{$patient->prevenir_nom}}" title="{{$patient->_props.prevenir_nom}}" /></td>
    <th><label for="employeur_nom" title="Nom de l'Employeur">Nom de l'employeur</label></th>
    <td><input tabindex="351" type="text" name="employeur_nom" value="{{$patient->employeur_nom}}" title="{{$patient->_props.employeur_nom}}" /></td>
  </tr>
  
  <tr>
    <th><label for="prevenir_prenom" title="Prénom de la personne à prévenir">Prénom</label></th>
    <td><input tabindex="302" type="text" name="prevenir_prenom" value="{{$patient->prevenir_prenom}}" title="{{$patient->_props.prevenir_prenom}}" /></td>
    <th rowspan="2"><label for="employeur_adresse" title="Adresse de l'employeur">Adresse de l'employeur</label></th>
    <td rowspan="2"><textarea tabindex="352" name="employeur_adresse" title="{{$patient->_props.employeur_adresse}}" rows="1">{{$patient->employeur_adresse}}</textarea></td>
  </tr>
  
  <tr>
    <th rowspan="2"><label for="prevenir_adresse" title="Adresse de la personne à prévenir">Adresse</label></th>
    <td rowspan="2"><textarea tabindex="303" name="prevenir_adresse" title="{{$patient->_props.prevenir_adresse}}" rows="1">{{$patient->prevenir_adresse}}</textarea></td>
  </tr>
  
  <tr>
    <th><label for="employeur_cp" title="Code Postal">Code Postal</label></th>
    <td>
      <input tabindex="353" size="31" maxlength="5" type="text" name="employeur_cp" value="{{$patient->employeur_cp}}" title="{{$patient->_props.employeur_cp}}" />
      <div style="display:none;" class="autocomplete" id="employeur_cp_auto_complete"></div>
    </td>
  </tr>
  
  <tr>
    <th><label for="prevenir_cp" title="Code Postal">Code Postal</label></th>
    <td>
      <input tabindex="304" size="31" maxlength="5" type="text" name="prevenir_cp" value="{{$patient->prevenir_cp}}" title="{{$patient->_props.prevenir_cp}}" />
      <div style="display:none;" class="autocomplete" id="prevenir_cp_auto_complete"></div>
    </td>
    <th><label for="prevenir_ville" title="Ville de la personne à prévenir">Ville</label></th>
    <td>
      <input tabindex="354" size="31" type="text" name="employeur_ville" value="{{$patient->employeur_ville}}" title="{{$patient->_props.employeur_ville}}" />
      <div style="display:none;" class="autocomplete" id="employeur_ville_auto_complete"></div>
    </td>
  </tr>
  
  <tr>
    <th><label for="prevenir_ville" title="Ville de la personne à prévenir">Ville</label></th>
    <td>
      <input tabindex="305" size="31" type="text" name="prevenir_ville" value="{{$patient->prevenir_ville}}" title="{{$patient->_props.prevenir_ville}}" />
      <div style="display:none;" class="autocomplete" id="prevenir_ville_auto_complete"></div>
    </td>
    <th><label for="employeur_tel" title="Téléphone de l'employeur">Téléphone</label></th>
    <td>
      <input tabindex="355" type="text" name="_tel41" size="2" maxlength="2" value="{{$patient->_tel41}}" title="num|length|2" onkeyup="followUp(this, '_tel42', 2)" /> - 
      <input tabindex="356" type="text" name="_tel42" size="2" maxlength="2" value="{{$patient->_tel42}}" title="num|length|2" onkeyup="followUp(this, '_tel43', 2)" /> -
      <input tabindex="357" type="text" name="_tel43" size="2" maxlength="2" value="{{$patient->_tel43}}" title="num|length|2" onkeyup="followUp(this, '_tel44', 2)" /> -
      <input tabindex="358" type="text" name="_tel44" size="2" maxlength="2" value="{{$patient->_tel44}}" title="num|length|2" onkeyup="followUp(this, '_tel45', 2)" /> -
      <input tabindex="359" type="text" name="_tel45" size="2" maxlength="2" value="{{$patient->_tel45}}" title="num|length|2" />
    </td>
  </tr>
  
  <tr>
    <th><label for="prevenir_tel" title="Téléphone de la personne à prévenir">Téléphone</label></th>
    <td>
      <input tabindex="306" type="text" name="_tel31" size="2" maxlength="2" value="{{$patient->_tel31}}" title="num|length|2" onkeyup="followUp(this, '_tel32', 2)" /> - 
      <input tabindex="307" type="text" name="_tel32" size="2" maxlength="2" value="{{$patient->_tel32}}" title="num|length|2" onkeyup="followUp(this, '_tel33', 2)" /> -
      <input tabindex="308" type="text" name="_tel33" size="2" maxlength="2" value="{{$patient->_tel33}}" title="num|length|2" onkeyup="followUp(this, '_tel34', 2)" /> -
      <input tabindex="309" type="text" name="_tel34" size="2" maxlength="2" value="{{$patient->_tel34}}" title="num|length|2" onkeyup="followUp(this, '_tel35', 2)" /> -
      <input tabindex="310" type="text" name="_tel35" size="2" maxlength="2" value="{{$patient->_tel35}}" title="num|length|2" />
    </td>
    <th><label for="employeur_urssaf" title="Veuillez saisir le numéro Urssaf">Numéro Urssaf</label></th>
    <td><input tabindex="360" type="text" name="employeur_urssaf" value="{{$patient->employeur_urssaf}}" title="{{$patient->_props.employeur_urssaf}}" /></td>
  </tr>
  
  <tr>
    <th><label for="prevenir_parente" title="Lien de parenté avec le patient">Lien de Parenté</label></th>
    <td>
      <select tabindex="311" name="prevenir_parente" title="{{$patient->_props.prevenir_parente}}">
        <option value="" {{if $patient->prevenir_parente===null}}selected="selected"{{/if}}>&mdash;Veuillez Choisir &mdash;</option>
        {{html_options options=$patient->_enumsTrans.prevenir_parente selected=$patient->prevenir_parente}}
      </select>
    </td>
    <td colspan="2"></td>
  </tr>

</table>