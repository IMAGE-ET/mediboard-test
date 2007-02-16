<table class="form">
  <tr>
    <th class="title" colspan="4">
      Personne à prévenir
    </th>
  </tr>
  <tr>
    <th class="category">Champ</th>
    <th width="30%" class="category">1er patient</th>
    <th width="30%" class="category">2ème patient</th>
    <th width="30%" class="category">Résultat</th>
  </tr>
  <tr>
    <th><label for="prevenir_nom" title="Nom de la personne à prévenir">Nom</label></th>
    <td>
      <input type="radio" name="_choix_prevenir_nom" value="{{$patient1->prevenir_nom}}" checked="checked" onclick="setField(this.form.prevenir_nom, '{{$patient1->prevenir_nom|smarty:nodefaults|JSAttribute}}')" />
      {{$patient1->prevenir_nom}}
    </td>
    <td>
      <input type="radio" name="_choix_prevenir_nom" value="{{$patient2->prevenir_nom}}" onclick="setField(this.form.prevenir_nom, '{{$patient2->prevenir_nom|smarty:nodefaults|JSAttribute}}')" />
      {{$patient2->prevenir_nom}}
    </td>
    <td>
      <input tabindex="300" type="text" name="prevenir_nom" value="{{$finalPatient->prevenir_nom}}" title="{{$finalPatient->_props.prevenir_nom}}" />
     </td>
  </tr>
  <tr>
    <th><label for="prevenir_prenom" title="Prénom de la personne à prévenir">Prénom</label></th>
    <td>
      <input type="radio" name="_choix_prevenir_prenom" value="{{$patient1->prevenir_prenom}}" checked="checked" onclick="setField(this.form.prevenir_prenom, '{{$patient1->prevenir_prenom|smarty:nodefaults|JSAttribute}}')" />
      {{$patient1->prevenir_prenom}}
    </td>
    <td>
      <input type="radio" name="_choix_prevenir_prenom" value="{{$patient2->prevenir_prenom}}" onclick="setField(this.form.prevenir_prenom, '{{$patient2->prevenir_prenom|smarty:nodefaults|JSAttribute}}')" />
      {{$patient2->prevenir_prenom}}
    </td>
    <td>
      <input tabindex="301" type="text" name="prevenir_prenom" value="{{$finalPatient->prevenir_prenom}}" title="{{$finalPatient->_props.prevenir_prenom}}" />
     </td>
  </tr>
  <tr>
    <th><label for="prevenir_adresse" title="Adresse de la personne à prévenir">Adresse</label></th>
    <td>
      <input type="radio" name="_choix_prevenir_adresse" value="{{$patient1->prevenir_adresse}}" checked="checked" onclick="setField(this.form.prevenir_adresse, '{{$patient1->prevenir_adresse|smarty:nodefaults|JSAttribute}}')" />
      {{$patient1->prevenir_adresse}}
    </td>
    <td>
      <input type="radio" name="_choix_prevenir_adresse" value="{{$patient2->prevenir_adresse}}" onclick="setField(this.form.prevenir_adresse, '{{$patient2->prevenir_adresse|smarty:nodefaults|JSAttribute}}')" />
      {{$patient2->prevenir_adresse}}
    </td>
    <td>
      <textarea tabindex="302" name="prevenir_adresse" title="{{$finalPatient->_props.prevenir_adresse}}">{{$finalPatient->prevenir_adresse}}</textarea>
    </td>
  </tr>
  <tr>
    <th><label for="prevenir_cp" title="Code Postal">Code Postal</label></th>
    <td>
      <input type="radio" name="_choix_prevenir_cp" value="{{$patient1->prevenir_cp}}" checked="checked" onclick="setField(this.form.prevenir_cp, '{{$patient1->prevenir_cp|smarty:nodefaults|JSAttribute}}')" />
      {{$patient1->prevenir_cp}}
    </td>
    <td>
      <input type="radio" name="_choix_prevenir_cp" value="{{$patient2->prevenir_cp}}" onclick="setField(this.form.prevenir_cp, '{{$patient2->prevenir_cp|smarty:nodefaults|JSAttribute}}')" />
      {{$patient2->prevenir_cp}}
    </td>
    <td>
      <input tabindex="303" type="text" name="prevenir_cp" value="{{$finalPatient->prevenir_cp}}" title="{{$finalPatient->_props.prevenir_cp}}" onclick="setField(this.form.prevenir_cp, '{{$patient2->prevenir_cp|smarty:nodefaults|JSAttribute}}')" />
    </td>
  </tr>
  <tr>
    <th><label for="prevenir_ville" title="Ville de la personne à prévenir">Ville</label></th>
    <td>
      <input type="radio" name="_choix_prevenir_ville" value="{{$patient1->prevenir_ville}}" checked="checked" onclick="setField(this.form.prevenir_ville, '{{$patient1->prevenir_ville|smarty:nodefaults|JSAttribute}}')" />
      {{$patient1->prevenir_ville}}
    </td>
    <td>
      <input type="radio" name="_choix_prevenir_ville" value="{{$patient2->prevenir_ville}}" onclick="setField(this.form.prevenir_ville, '{{$patient2->prevenir_ville|smarty:nodefaults|JSAttribute}}')" />
      {{$patient2->prevenir_ville}}
    </td>
    <td>
      <input tabindex="304" type="text" name="prevenir_ville" value="{{$finalPatient->prevenir_ville}}" title="{{$finalPatient->_props.prevenir_ville}}" />
    </td>
  </tr>
  <tr>
    <th><label for="_tel31" title="Téléphone de la personne à prévenir">Téléphone</label></th>
    <td>
      <input type="radio" name="_choix_prevenir_tel" value="{{$patient1->prevenir_tel}}" checked="checked"
      onclick="setField(this.form._tel31, '{{$patient1->_tel31}}'); setField(this.form._tel32, '{{$patient1->_tel32}}');
      setField(this.form._tel33, '{{$patient1->_tel33}}'); setField(this.form._tel34, '{{$patient1->_tel34}}'); setField(this.form._tel35, '{{$patient1->_tel35}}');" />
      {{$patient1->prevenir_tel}}
    </td>
    <td>
      <input type="radio" name="_choix_prevenir_tel" value="{{$patient2->prevenir_tel}}"
      onclick="setField(this.form._tel31, '{{$patient2->_tel31}}'); setField(this.form._tel32, '{{$patient2->_tel32}}');
      setField(this.form._tel33, '{{$patient2->_tel33}}'); setField(this.form._tel34, '{{$patient2->_tel34}}'); setField(this.form._tel35, '{{$patient2->_tel35}}');" />
      {{$patient2->prevenir_tel}}
    </td>
    <td>
      <input tabindex="305" type="text" name="_tel31" size="2" maxlength="2" value="{{$finalPatient->_tel31}}" title="num length|2" onkeyup="followUp(this, '_tel32', 2)" /> - 
      <input tabindex="306" type="text" name="_tel32" size="2" maxlength="2" value="{{$finalPatient->_tel32}}" title="num length|2" onkeyup="followUp(this, '_tel33', 2)" /> -
      <input tabindex="307" type="text" name="_tel33" size="2" maxlength="2" value="{{$finalPatient->_tel33}}" title="num length|2" onkeyup="followUp(this, '_tel34', 2)" /> -
      <input tabindex="308" type="text" name="_tel34" size="2" maxlength="2" value="{{$finalPatient->_tel34}}" title="num length|2" onkeyup="followUp(this, '_tel35', 2)" /> -
      <input tabindex="309" type="text" name="_tel35" size="2" maxlength="2" value="{{$finalPatient->_tel35}}" title="num length|2" />
    </td>
  </tr>
  <tr>
    <th><label for="prevenir_parente" title="Lien de parenté avec le patient">Lien de Parenté</label></th>
    <td>
      <input type="radio" name="_choix_prevenir_parente" value="{{$patient1->prevenir_parente}}" checked="checked" onclick="setField(this.form.prevenir_parente, '{{$patient1->prevenir_parente}}')" />
      {{tr}}CPatient.prevenir_parente.{{$patient1->prevenir_parente}}{{/tr}}
    </td>
    <td>
      <input type="radio" name="_choix_prevenir_parente" value="{{$patient2->prevenir_parente}}" onclick="setField(this.form.prevenir_parente, '{{$patient2->prevenir_parente}}')" />
      {{tr}}CPatient.prevenir_parente.{{$patient2->prevenir_parente}}{{/tr}}
    </td>
    <td>
      <select tabindex="310" name="prevenir_parente" title="{{$finalPatient->_props.prevenir_parente}}">
        <option value="" {{if $finalPatient->prevenir_parente===null}}selected="selected"{{/if}}>&mdash;Veuillez Choisir &mdash;</option>
        {{html_options options=$finalPatient->_enumsTrans.prevenir_parente selected=$finalPatient->prevenir_parente}}
      </select>
    </td>
  </tr>
  
  <tr>
    <th class="title" colspan="4">
      Employeur
    </th>
  </tr>
  <tr>
    <th><label for="employeur_nom" title="Nom de l'Employeur">Nom de l'employeur</label></th>
    <td>
      <input type="radio" name="_choix_employeur_nom" value="{{$patient1->employeur_nom}}" checked="checked" onclick="setField(this.form.employeur_nom, '{{$patient1->employeur_nom|smarty:nodefaults|JSAttribute}}')" />
      {{$patient1->employeur_nom}}
    </td>
    <td>
      <input type="radio" name="_choix_employeur_nom" value="{{$patient2->employeur_nom}}" onclick="setField(this.form.employeur_nom, '{{$patient2->employeur_nom|smarty:nodefaults|JSAttribute}}')" />
      {{$patient2->employeur_nom}}
    </td>
    <td>
      <input tabindex="311" type="text" name="employeur_nom" value="{{$finalPatient->employeur_nom}}" title="{{$finalPatient->_props.employeur_nom}}" />
     </td>
  </tr>
  <tr>
    <th><label for="employeur_adresse" title="Adresse de l'employeur">Adresse de l'employeur</label></th>
    <td>
      <input type="radio" name="_choix_employeur_adresse" value="{{$patient1->employeur_adresse}}" checked="checked" onclick="setField(this.form.employeur_adresse, '{{$patient1->employeur_adresse|smarty:nodefaults|JSAttribute}}')" />
      {{$patient1->employeur_adresse}}
    </td>
    <td>
      <input type="radio" name="_choix_employeur_adresse" value="{{$patient2->employeur_adresse}}" onclick="setField(this.form.employeur_adresse, '{{$patient2->employeur_adresse|smarty:nodefaults|JSAttribute}}')" />
      {{$patient2->employeur_adresse}}
    </td>
    <td>
      <textarea tabindex="312" name="employeur_adresse" title="{{$finalPatient->_props.employeur_adresse}}">{{$finalPatient->employeur_adresse}}</textarea>
    </td>
  </tr>
  <tr>
    <th><label for="employeur_cp" title="Code Postal">Code Postal</label></th>
    <td>
      <input type="radio" name="_choix_employeur_cp" value="{{$patient1->employeur_cp}}" checked="checked" onclick="setField(this.form.employeur_cp, '{{$patient1->employeur_cp|smarty:nodefaults|JSAttribute}}')" />
      {{$patient1->employeur_cp}}
    </td>
    <td>
      <input type="radio" name="_choix_employeur_cp" value="{{$patient2->employeur_cp}}" onclick="setField(this.form.employeur_cp, '{{$patient2->employeur_cp|smarty:nodefaults|JSAttribute}}')" />
      {{$patient2->employeur_cp}}
    </td>
    <td>
      <input tabindex="313" type="text" name="employeur_cp" value="{{$finalPatient->employeur_cp}}" title="{{$finalPatient->_props.employeur_cp}}" onclick="setField(this.form.employeur_cp, '{{$patient2->employeur_cp|smarty:nodefaults|JSAttribute}}')" />
    </td>
  </tr>
  <tr>
    <th><label for="employeur_ville" title="Ville de l'employeur">Ville</label></th>
    <td>
      <input type="radio" name="_choix_employeur_ville" value="{{$patient1->employeur_ville}}" checked="checked" onclick="setField(this.form.employeur_ville, '{{$patient1->employeur_ville|smarty:nodefaults|JSAttribute}}')" />
      {{$patient1->employeur_ville}}
    </td>
    <td>
      <input type="radio" name="_choix_employeur_ville" value="{{$patient2->employeur_ville}}" onclick="setField(this.form.employeur_ville, '{{$patient2->employeur_ville|smarty:nodefaults|JSAttribute}}')" />
      {{$patient2->employeur_ville}}
    </td>
    <td>
      <input tabindex="314" type="text" name="employeur_ville" value="{{$finalPatient->employeur_ville}}" title="{{$finalPatient->_props.employeur_ville}}" />
    </td>
  </tr>
  <tr>
    <th><label for="_tel41" title="Téléphone de l'employeur">Téléphone</label></th>
    <td>
      <input type="radio" name="_choix_employeur_tel" value="{{$patient1->employeur_tel}}" checked="checked"
      onclick="setField(this.form._tel41, '{{$patient1->_tel41}}'); setField(this.form._tel42, '{{$patient1->_tel42}}');
      setField(this.form._tel43, '{{$patient1->_tel43}}'); setField(this.form._tel44, '{{$patient1->_tel44}}'); setField(this.form._tel45, '{{$patient1->_tel45}}');" />
      {{$patient1->employeur_tel}}
    </td>
    <td>
      <input type="radio" name="_choix_employeur_tel" value="{{$patient2->employeur_tel}}"
      onclick="setField(this.form._tel41, '{{$patient2->_tel41}}'); setField(this.form._tel42, '{{$patient2->_tel42}}');
      setField(this.form._tel43, '{{$patient2->_tel43}}'); setField(this.form._tel44, '{{$patient2->_tel44}}'); setField(this.form._tel45, '{{$patient2->_tel45}}');" />
      {{$patient2->employeur_tel}}
    </td>
    <td>
      <input tabindex="315" type="text" name="_tel41" size="2" maxlength="2" value="{{$finalPatient->_tel41}}" title="num length|2" onkeyup="followUp(this, '_tel42', 2)" /> - 
      <input tabindex="316" type="text" name="_tel42" size="2" maxlength="2" value="{{$finalPatient->_tel42}}" title="num length|2" onkeyup="followUp(this, '_tel43', 2)" /> -
      <input tabindex="317" type="text" name="_tel43" size="2" maxlength="2" value="{{$finalPatient->_tel43}}" title="num length|2" onkeyup="followUp(this, '_tel44', 2)" /> -
      <input tabindex="318" type="text" name="_tel44" size="2" maxlength="2" value="{{$finalPatient->_tel44}}" title="num length|2" onkeyup="followUp(this, '_tel45', 2)" /> -
      <input tabindex="319" type="text" name="_tel45" size="2" maxlength="2" value="{{$finalPatient->_tel45}}" title="num length|2" />
    </td>
  </tr>
  <tr>
    <th><label for="employeur_urssaf" title="Veuillez saisir le numéro Urssaf">Numéro Urssaf</label></th>
    <td>
      <input type="radio" name="_choix_employeur_urssaf" value="{{$patient1->employeur_urssaf}}" checked="checked" onclick="setField(this.form.employeur_urssaf, '{{$patient1->employeur_urssaf|smarty:nodefaults|JSAttribute}}')" />
      {{$patient1->employeur_urssaf}}
    </td>
    <td>
      <input type="radio" name="_choix_employeur_urssaf" value="{{$patient2->employeur_urssaf}}" onclick="setField(this.form.employeur_urssaf, '{{$patient2->employeur_urssaf|smarty:nodefaults|JSAttribute}}')" />
      {{$patient2->employeur_urssaf}}
    </td>
    <td>
      <input tabindex="320" type="text" name="employeur_urssaf" value="{{$finalPatient->employeur_urssaf}}" title="{{$finalPatient->_props.employeur_urssaf}}" />
     </td>
  </tr>
</table>