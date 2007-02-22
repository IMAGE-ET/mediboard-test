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
    <th>{{mb_label object=$finalPatient field="prevenir_nom"}}</th>
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
    <th>{{mb_label object=$finalPatient field="prevenir_prenom"}}</th>
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
    <th>{{mb_label object=$finalPatient field="prevenir_adresse"}}</th>
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
    <th>{{mb_label object=$finalPatient field="prevenir_cp"}}</th>
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
    <th>{{mb_label object=$finalPatient field="prevenir_ville"}}</th>
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
    <th>{{mb_label object=$finalPatient field="prevenir_tel" defaultFor="_tel31"}}</th>
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
      {{mb_field object=$finalPatient field="_tel31" tabindex="305" size="2" maxlength="2" spec="num length|2" onkeyup="followUp(this, '_tel32', 2)"}} -
      {{mb_field object=$finalPatient field="_tel32" tabindex="306" size="2" maxlength="2" spec="num length|2" onkeyup="followUp(this, '_tel33', 2)"}} -
      {{mb_field object=$finalPatient field="_tel33" tabindex="307" size="2" maxlength="2" spec="num length|2" onkeyup="followUp(this, '_tel34', 2)"}} -
      {{mb_field object=$finalPatient field="_tel34" tabindex="308" size="2" maxlength="2" spec="num length|2" onkeyup="followUp(this, '_tel35', 2)"}} -
      {{mb_field object=$finalPatient field="_tel35" tabindex="309" size="2" maxlength="2" spec="num length|2"}}
    </td>
  </tr>
  <tr>
    <th>{{mb_label object=$finalPatient field="prevenir_parente"}}</th>
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
    <th>{{mb_label object=$finalPatient field="employeur_nom"}}</th>
    <td>
      <input type="radio" name="_choix_employeur_nom" value="{{$patient1->employeur_nom}}" checked="checked" onclick="setField(this.form.employeur_nom, '{{$patient1->employeur_nom|smarty:nodefaults|JSAttribute}}')" />
      {{$patient1->employeur_nom}}
    </td>
    <td>
      <input type="radio" name="_choix_employeur_nom" value="{{$patient2->employeur_nom}}" onclick="setField(this.form.employeur_nom, '{{$patient2->employeur_nom|smarty:nodefaults|JSAttribute}}')" />
      {{$patient2->employeur_nom}}
    </td>
    <td>
      {{mb_field object=$finalPatient field="employeur_nom" tabindex="311"}}
    </td>
  </tr>
  <tr>
    <th>{{mb_label object=$finalPatient field="employeur_adresse"}}</th>
    <td>
      <input type="radio" name="_choix_employeur_adresse" value="{{$patient1->employeur_adresse}}" checked="checked" onclick="setField(this.form.employeur_adresse, '{{$patient1->employeur_adresse|smarty:nodefaults|JSAttribute}}')" />
      {{$patient1->employeur_adresse}}
    </td>
    <td>
      <input type="radio" name="_choix_employeur_adresse" value="{{$patient2->employeur_adresse}}" onclick="setField(this.form.employeur_adresse, '{{$patient2->employeur_adresse|smarty:nodefaults|JSAttribute}}')" />
      {{$patient2->employeur_adresse}}
    </td>
    <td>
      {{mb_field object=$finalPatient field="employeur_adresse" tabindex="312"}}
    </td>
  </tr>
  <tr>
    <th>{{mb_label object=$finalPatient field="employeur_cp"}}</th>
    <td>
      <input type="radio" name="_choix_employeur_cp" value="{{$patient1->employeur_cp}}" checked="checked" onclick="setField(this.form.employeur_cp, '{{$patient1->employeur_cp|smarty:nodefaults|JSAttribute}}')" />
      {{$patient1->employeur_cp}}
    </td>
    <td>
      <input type="radio" name="_choix_employeur_cp" value="{{$patient2->employeur_cp}}" onclick="setField(this.form.employeur_cp, '{{$patient2->employeur_cp|smarty:nodefaults|JSAttribute}}')" />
      {{$patient2->employeur_cp}}
    </td>
    <td>
      {{mb_field object=$finalPatient field="employeur_cp" tabindex="313"}}
    </td>
  </tr>
  <tr>
    <th>{{mb_label object=$finalPatient field="employeur_ville"}}</th>
    <td>
      <input type="radio" name="_choix_employeur_ville" value="{{$patient1->employeur_ville}}" checked="checked" onclick="setField(this.form.employeur_ville, '{{$patient1->employeur_ville|smarty:nodefaults|JSAttribute}}')" />
      {{$patient1->employeur_ville}}
    </td>
    <td>
      <input type="radio" name="_choix_employeur_ville" value="{{$patient2->employeur_ville}}" onclick="setField(this.form.employeur_ville, '{{$patient2->employeur_ville|smarty:nodefaults|JSAttribute}}')" />
      {{$patient2->employeur_ville}}
    </td>
    <td>
      {{mb_field object=$finalPatient field="employeur_ville" tabindex="314"}}
    </td>
  </tr>
  <tr>
    <th>{{mb_label object=$finalPatient field="employeur_tel"}}</th>
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
      {{mb_field object=$finalPatient field="_tel41" tabindex="315" size="2" maxlength="2" spec="num length|2" onkeyup="followUp(this, '_tel42', 2)"}} -
      {{mb_field object=$finalPatient field="_tel42" tabindex="316" size="2" maxlength="2" spec="num length|2" onkeyup="followUp(this, '_tel43', 2)"}} -
      {{mb_field object=$finalPatient field="_tel43" tabindex="317" size="2" maxlength="2" spec="num length|2" onkeyup="followUp(this, '_tel44', 2)"}} -
      {{mb_field object=$finalPatient field="_tel44" tabindex="318" size="2" maxlength="2" spec="num length|2" onkeyup="followUp(this, '_tel45', 2)"}} -
      {{mb_field object=$finalPatient field="_tel45" tabindex="319" size="2" maxlength="2" spec="num length|2"}}
    </td>
  </tr>
  <tr>
    <th>{{mb_label object=$finalPatient field="employeur_urssaf"}}</th>
    <td>
      <input type="radio" name="_choix_employeur_urssaf" value="{{$patient1->employeur_urssaf}}" checked="checked" onclick="setField(this.form.employeur_urssaf, '{{$patient1->employeur_urssaf|smarty:nodefaults|JSAttribute}}')" />
      {{$patient1->employeur_urssaf}}
    </td>
    <td>
      <input type="radio" name="_choix_employeur_urssaf" value="{{$patient2->employeur_urssaf}}" onclick="setField(this.form.employeur_urssaf, '{{$patient2->employeur_urssaf|smarty:nodefaults|JSAttribute}}')" />
      {{$patient2->employeur_urssaf}}
    </td>
    <td>
      {{mb_field object=$finalPatient field="employeur_urssaf" tabindex="320"}}
     </td>
  </tr>
</table>