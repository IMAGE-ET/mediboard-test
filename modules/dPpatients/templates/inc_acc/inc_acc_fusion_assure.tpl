<table class="form">
  <tr>
    <th class="title" colspan="4">
      Identité
    </th>
  </tr>
  <tr>
    <th class="category">Champ</th>
    <th width="30%" class="category">1er patient</th>
    <th width="30%" class="category">2ème patient</th>
    <th width="30%" class="category">Résultat</th>
  </tr>
  <tr>
    <th>{{mb_label object=$finalPatient field="assure_nom"}}</th>
    <td>
      <input type="radio" name="_choix_assure_nom" value="{{$patient1->assure_nom}}" checked="checked" onclick="setField(this.form.assure_nom, '{{$patient1->assure_nom|smarty:nodefaults|JSAttribute}}')" />
      {{$patient1->assure_nom}}
    </td>
    <td>
      <input type="radio" name="_choix_assure_nom" value="{{$patient2->assure_nom}}" onclick="setField(this.form.assure_nom, '{{$patient2->assure_nom|smarty:nodefaults|JSAttribute}}')" />
      {{$patient2->assure_nom}}
    </td>
    <td>
      {{mb_field object=$finalPatient field="assure_nom" tabindex="401"}}
     </td>
  </tr>
  <tr>
    <th>{{mb_label object=$finalPatient field="assure_prenom"}}</th>
    <td>
      <input type="radio" name="_choix_assure_prenom" value="{{$patient1->assure_prenom}}" checked="checked" onclick="setField(this.form.assure_prenom, '{{$patient1->assure_prenom|smarty:nodefaults|JSAttribute}}')" />
      {{$patient1->assure_prenom}}
    </td>
    <td>
      <input type="radio" name="_choix_assure_prenom" value="{{$patient2->assure_prenom}}" onclick="setField(this.form.assure_prenom, '{{$patient2->assure_prenom|smarty:nodefaults|JSAttribute}}')" />
      {{$patient2->assure_prenom}}
    </td>
    <td>
      {{mb_field object=$finalPatient field="assure_prenom" tabindex="402"}}
    </td>
  </tr>
  <tr>
    <th>{{mb_label object=$finalPatient field="assure_nom_jeune_fille"}}</th>
    <td>
      <input type="radio" name="_choix_assure_nom_jeune_fille" value="{{$patient1->assure_nom_jeune_fille}}" checked="checked" onclick="setField(this.form.assure_nom_jeune_fille, '{{$patient1->assure_nom_jeune_fille|smarty:nodefaults|JSAttribute}}')" />
      {{$patient1->assure_nom_jeune_fille}}
    </td>
    <td>
      <input type="radio" name="_choix_assure_nom_jeune_fille" value="{{$patient2->assure_nom_jeune_fille}}" onclick="setField(this.form.assure_nom_jeune_fille, '{{$patient2->assure_nom_jeune_fille|smarty:nodefaults|JSAttribute}}')" />
      {{$patient2->assure_nom_jeune_fille}}
    </td>
    <td>
      {{mb_field object=$finalPatient field="assure_nom_jeune_fille" tabindex="403"}}
    </td>
  </tr>
  <tr>
    <th>{{mb_label object=$finalPatient field="assure_sexe"}}</th>
    <td>
      <input type="radio" name="_choix_assure_sexe" value="{{$patient1->assure_sexe}}" checked="checked" onclick="setField(this.form.assure_sexe, '{{$patient1->assure_sexe}}')" />
      {{tr}}CPatient.assure_sexe.{{$patient1->assure_sexe}}{{/tr}}
    </td>
    <td>
      <input type="radio" name="_choix_assure_sexe" value="{{$patient2->assure_sexe}}" onclick="setField(this.form.assure_sexe, '{{$patient2->assure_sexe}}')" />
      {{tr}}CPatient.assure_sexe.{{$patient2->assure_sexe}}{{/tr}}
    </td>
    <td>
      {{mb_field object=$finalPatient field="assure_sexe" tabindex="404"}}
    </td>
  </tr>
  <tr>
    <th>{{mb_label object=$finalPatient field="assure_naissance"}}</th>
    <td>
      <input type="radio" name="_choix_assure_naissance" value="{{$patient1->assure_naissance}}" checked="checked"
      onclick="setField(this.form._assure_jour, '{{$patient1->_assure_jour}}'); setField(this.form._assure_mois, '{{$patient1->_assure_mois}}'); setField(this.form._assure_annee, '{{$patient1->_assure_annee}}');" />
      {{$patient1->_assure_naissance}}
    </td>
    <td>
      <input type="radio" name="_choix_assure_naissance" value="{{$patient2->assure_naissance}}"
      onclick="setField(this.form._assure_jour, '{{$patient2->_assure_jour}}'); setField(this.form._assure_mois, '{{$patient2->_assure_mois}}'); setField(this.form._assure_annee, '{{$patient2->_assure_annee}}');" />
      {{$patient2->_assure_naissance}}
    </td>
    <td>
      <input tabindex="405" type="text" name="_assure_jour"  size="2" maxlength="2" value="{{if $finalPatient->_assure_jour != "00"}}{{$finalPatient->_assure_jour}}{{/if}}" onkeyup="followUp(event)" /> -
      <input tabindex="406" type="text" name="_assure_mois"  size="2" maxlength="2" value="{{if $finalPatient->_assure_mois != "00"}}{{$finalPatient->_assure_mois}}{{/if}}" onkeyup="followUp(event)" /> -
      <input tabindex="407" type="text" name="_assure_annee" size="4" maxlength="4" value="{{if $finalPatient->_assure_annee != "0000"}}{{$finalPatient->_assure_annee}}{{/if}}" />
    </td>
  </tr>
  <tr>
    <th>{{mb_label object=$finalPatient field="assure_lieu_naissance"}}</th>
    <td>
      <input type="radio" name="_choix_assure_lieu_naissance" value="{{$patient1->assure_lieu_naissance}}" checked="checked" onclick="setField(this.form.assure_lieu_naissance, '{{$patient1->assure_lieu_naissance|smarty:nodefaults|JSAttribute}}')" />
      {{$patient1->assure_lieu_naissance}}
    </td>
    <td>
      <input type="radio" name="_choix_assure_lieu_naissance" value="{{$patient2->assure_lieu_naissance}}" onclick="setField(this.form.assure_lieu_naissance, '{{$patient2->assure_lieu_naissance|smarty:nodefaults|JSAttribute}}')" />
      {{$patient2->assure_lieu_naissance}}
    </td>
    <td>
      {{mb_field object=$finalPatient field="assure_lieu_naissance" tabindex="408"}}
    </td>
  </tr>
  <tr>
    <th>{{mb_label object=$finalPatient field="assure_nationalite"}}</th>
    <td>
      <input type="radio" name="_choix_assure_nationalite" value="{{$patient1->assure_nationalite}}" checked="checked" onclick="setField(this.form.assure_nationalite, '{{$patient1->assure_nationalite}}')" />
      {{tr}}CPatient.assure_nationalite.{{$patient1->assure_nationalite}}{{/tr}}
    </td>
    <td>
      <input type="radio" name="_choix_assure_nationalite" value="{{$patient2->assure_nationalite}}" onclick="setField(this.form.assure_nationalite, '{{$patient2->assure_nationalite}}')" />
      {{tr}}CPatient.assure_nationalite.{{$patient2->assure_nationalite}}{{/tr}}
    </td>
    <td>
      {{mb_field object=$finalPatient field="assure_nationalite" tabindex="409"}}
    </td>
  </tr>
  <tr>
    <th>{{mb_label object=$finalPatient field="assure_rques"}}</th>
    <td class="text">
      <input type="radio" name="_choix_assure_rques" value="{{$patient1->assure_rques}}" checked="checked" onclick="setField(this.form.assure_rques, '{{$patient1->assure_rques|smarty:nodefaults|JSAttribute}}')" />
      {{$patient1->assure_rques|nl2br}}
    </td>
    <td class="text">
      <input type="radio" name="_choix_assure_rques" value="{{$patient2->assure_rques}}" onclick="setField(this.form.assure_rques, '{{$patient2->assure_rques|smarty:nodefaults|JSAttribute}}')" />
      {{$patient2->assure_rques|nl2br}}
    </td>
    <td class="text">
      {{mb_field object=$finalPatient field="assure_rques" tabindex="410" rows="3"}}
    </td>
  </tr>
  <tr>
    <th class="title" colspan="4">
      Coordonnées
    </th>
  </tr>
  <tr>
    <th>{{mb_label object=$finalPatient field="assure_adresse"}}</th>
    <td>
      <input type="radio" name="_choix_assure_adresse" value="{{$patient1->assure_adresse}}" checked="checked" onclick="setField(this.form.assure_adresse, '{{$patient1->assure_adresse|smarty:nodefaults|JSAttribute}}')" />
      {{$patient1->assure_adresse}}
    </td>
    <td>
      <input type="radio" name="_choix_assure_adresse" value="{{$patient2->assure_adresse}}" onclick="setField(this.form.assure_adresse, '{{$patient2->assure_adresse|smarty:nodefaults|JSAttribute}}')" />
      {{$patient2->assure_adresse}}
    </td>
    <td>
      {{mb_field object=$finalPatient field="assure_adresse" tabindex="411"}}
    </td>
  </tr>
  <tr>
    <th>{{mb_label object=$finalPatient field="assure_cp"}}</th>
    <td>
      <input type="radio" name="_choix_assure_cp" value="{{$patient1->assure_cp}}" checked="checked" onclick="setField(this.form.assure_cp, '{{$patient1->assure_cp|smarty:nodefaults|JSAttribute}}')" />
      {{$patient1->assure_cp}}
    </td>
    <td>
      <input type="radio" name="_choix_assure_cp" value="{{$patient2->assure_cp}}" onclick="setField(this.form.assure_cp, '{{$patient2->assure_cp|smarty:nodefaults|JSAttribute}}')" />
      {{$patient2->assure_cp}}
    </td>
    <td>
      {{mb_field object=$finalPatient field="assure_cp" tabindex="412"}}
    </td>
  </tr>
  <tr>
    <th>{{mb_label object=$finalPatient field="assure_ville"}}</th>
    <td>
      <input type="radio" name="_choix_assure_ville" value="{{$patient1->assure_ville}}" checked="checked" onclick="setField(this.form.assure_ville, '{{$patient1->assure_ville|smarty:nodefaults|JSAttribute}}')" />
      {{$patient1->assure_ville}}
    </td>
    <td>
      <input type="radio" name="_choix_assure_ville" value="{{$patient2->assure_ville}}" onclick="setField(this.form.assure_ville, '{{$patient2->assure_ville|smarty:nodefaults|JSAttribute}}')" />
      {{$patient2->assure_ville}}
    </td>
    <td>
      {{mb_field object=$finalPatient field="assure_ville" tabindex="413"}}
    </td>
  </tr>
  <tr>
    <th>{{mb_label object=$finalPatient field="assure_pays"}}</th>
    <td>
      <input type="radio" name="_choix_assure_pays" value="{{$patient1->assure_pays}}" checked="checked" onclick="setField(this.form.assure_pays, '{{$patient1->assure_pays|smarty:nodefaults|JSAttribute}}')" />
      {{$patient1->assure_pays}}
    </td>
    <td>
      <input type="radio" name="_choix_assure_pays" value="{{$patient2->assure_pays}}" onclick="setField(this.form.assure_pays, '{{$patient2->assure_pays|smarty:nodefaults|JSAttribute}}')" />
      {{$patient2->assure_pays}}
    </td>
    <td>
      {{mb_field object=$finalPatient field="assure_pays" tabindex="414"}}
    </td>
  </tr>
  <tr>
    <th>{{mb_label object=$finalPatient field="assure_tel"}}</th>
    <td>
      <input type="radio" name="_choix_assure_tel" value="{{$patient1->assure_tel}}" checked="checked"
      onclick="setField(this.form._assure_tel1, '{{$patient1->_assure_tel1}}'); setField(this.form._assure_tel2, '{{$patient1->_assure_tel2}}');
      setField(this.form._assure_tel3, '{{$patient1->_assure_tel3}}'); setField(this.form._assure_tel4, '{{$patient1->_assure_tel4}}'); setField(this.form._assure_tel5, '{{$patient1->_assure_tel5}}');" />
      {{$patient1->assure_tel}}
    </td>
    <td>
      <input type="radio" name="_choix_assure_tel" value="{{$patient2->assure_tel}}"
      onclick="setField(this.form._assure_tel1, '{{$patient2->_assure_tel1}}'); setField(this.form._assure_tel2, '{{$patient2->_assure_tel2}}');
      setField(this.form._assure_tel3, '{{$patient2->_assure_tel3}}'); setField(this.form._assure_tel4, '{{$patient2->_assure_tel4}}'); setField(this.form._assure_tel5, '{{$patient2->_assure_tel5}}');" />
      {{$patient2->assure_tel}}
    </td>
    <td>
      {{mb_field object=$finalPatient field="_assure_tel1" tabindex="115" size="2" maxlength="2" prop="num length|2" onkeyup="followUp(event)"}} -
      {{mb_field object=$finalPatient field="_assure_tel2" tabindex="116" size="2" maxlength="2" prop="num length|2" onkeyup="followUp(event)"}} -
      {{mb_field object=$finalPatient field="_assure_tel3" tabindex="117" size="2" maxlength="2" prop="num length|2" onkeyup="followUp(event)"}} -
      {{mb_field object=$finalPatient field="_assure_tel4" tabindex="118" size="2" maxlength="2" prop="num length|2" onkeyup="followUp(event)"}} -
      {{mb_field object=$finalPatient field="_assure_tel5" tabindex="119" size="2" maxlength="2" prop="num length|2"}}
    </td>
  </tr>
  <tr>
    <th>{{mb_label object=$finalPatient field="assure_tel2"}}</th>
    <td>
      <input type="radio" name="_choix_assure_tel2" value="{{$patient1->assure_tel2}}" checked="checked"
      onclick="setField(this.form._assure_tel21, '{{$patient1->_assure_tel21}}'); setField(this.form._assure_tel22, '{{$patient1->_assure_tel22}}');
      setField(this.form._assure_tel23, '{{$patient1->_assure_tel23}}'); setField(this.form._tassure_el24, '{{$patient1->_assure_tel24}}'); setField(this.form._assure_tel25, '{{$patient1->_assure_tel25}}');" />
      {{$patient1->assure_tel2}}
    </td>
    <td>
      <input type="radio" name="_choix_assure_tel2" value="{{$patient2->assure_tel2}}"
      onclick="setField(this.form._assure_tel21, '{{$patient2->_assure_tel21}}'); setField(this.form._assure_tel22, '{{$patient2->_assure_tel22}}');
      setField(this.form._assure_tel23, '{{$patient2->_assure_tel23}}'); setField(this.form._assure_tel24, '{{$patient2->_assure_tel24}}'); setField(this.form._assure_tel25, '{{$patient2->_assure_tel25}}');" />
      {{$patient2->assure_tel2}}
    </td>
    <td>
      {{mb_field object=$finalPatient field="_assure_tel21" tabindex="420" size="2" maxlength="2" prop="num length|2" onkeyup="followUp(event)"}} -
      {{mb_field object=$finalPatient field="_assure_tel22" tabindex="421" size="2" maxlength="2" prop="num length|2" onkeyup="followUp(event)"}} -
      {{mb_field object=$finalPatient field="_assure_tel23" tabindex="422" size="2" maxlength="2" prop="num length|2" onkeyup="followUp(event)"}} -
      {{mb_field object=$finalPatient field="_assure_tel24" tabindex="423" size="2" maxlength="2" prop="num length|2" onkeyup="followUp(event)"}} -
      {{mb_field object=$finalPatient field="_assure_tel25" tabindex="424" size="2" maxlength="2" prop="num length|2"}}
    </td>
  </tr>
  <tr>
    <th>{{mb_label object=$finalPatient field="assure_profession"}}</th>
    <td>
      <input type="radio" name="_choix_assure_profession" value="{{$patient1->assure_profession}}" checked="checked" onclick="setField(this.form.assure_profession, '{{$patient1->assure_profession|smarty:nodefaults|JSAttribute}}')" />
      {{$patient1->assure_profession}}
    </td>
    <td>
      <input type="radio" name="_choix_assure_profession" value="{{$patient2->assure_profession}}" onclick="setField(this.form.assure_profession, '{{$patient2->assure_profession|smarty:nodefaults|JSAttribute}}')" />
      {{$patient2->assure_profession}}
    </td>
    <td>
      {{mb_field object=$finalPatient field="assure_profession" tabindex="425"}}
    </td>
  </tr>
  <tr>
    <th>{{mb_label object=$finalPatient field="assure_matricule"}}</th>
    <td>
      <input type="radio" name="_choix_assure_matricule" value="{{$patient1->assure_matricule}}" checked="checked" onclick="setField(this.form.assure_matricule, '{{$patient1->assure_matricule|smarty:nodefaults|JSAttribute}}')" />
      {{$patient1->assure_matricule}}
    </td>
    <td>
      <input type="radio" name="_choix_assure_matricule" value="{{$patient2->assure_matricule}}" onclick="setField(this.form.assure_matricule, '{{$patient2->assure_matricule|smarty:nodefaults|JSAttribute}}')" />
      {{$patient2->assure_matricule}}
    </td>
    <td>
      {{mb_field object=$finalPatient field="assure_matricule" tabindex="426" size="15" maxlength="15"}}
    </td>
	</tr>  
</table>