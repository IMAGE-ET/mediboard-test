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
    <th>{{mb_label object=$finalPatient field="nom"}}</th>
    <td>
      <input type="radio" name="_choix_nom" value="{{$patient1->nom}}" checked="checked" onclick="setField(this.form.nom, '{{$patient1->nom|smarty:nodefaults|JSAttribute}}')" />
      {{$patient1->nom}}
    </td>
    <td>
      <input type="radio" name="_choix_nom" value="{{$patient2->nom}}" onclick="setField(this.form.nom, '{{$patient2->nom|smarty:nodefaults|JSAttribute}}')" />
      {{$patient2->nom}}
    </td>
    <td>
      {{mb_field object=$finalPatient field="nom" tabindex="101"}}
     </td>
  </tr>
  <tr>
    <th>{{mb_label object=$finalPatient field="prenom"}}</th>
    <td>
      <input type="radio" name="_choix_prenom" value="{{$patient1->prenom}}" checked="checked" onclick="setField(this.form.prenom, '{{$patient1->prenom|smarty:nodefaults|JSAttribute}}')" />
      {{$patient1->prenom}}
    </td>
    <td>
      <input type="radio" name="_choix_prenom" value="{{$patient2->prenom}}" onclick="setField(this.form.prenom, '{{$patient2->prenom|smarty:nodefaults|JSAttribute}}')" />
      {{$patient2->prenom}}
    </td>
    <td>
      {{mb_field object=$finalPatient field="prenom" tabindex="102"}}
    </td>
  </tr>
  <tr>
    <th>{{mb_label object=$finalPatient field="nom_jeune_fille"}}</th>
    <td>
      <input type="radio" name="_choix_nom_jeune_fille" value="{{$patient1->nom_jeune_fille}}" checked="checked" onclick="setField(this.form.nom_jeune_fille, '{{$patient1->nom_jeune_fille|smarty:nodefaults|JSAttribute}}')" />
      {{$patient1->nom_jeune_fille}}
    </td>
    <td>
      <input type="radio" name="_choix_nom_jeune_fille" value="{{$patient2->nom_jeune_fille}}" onclick="setField(this.form.nom_jeune_fille, '{{$patient2->nom_jeune_fille|smarty:nodefaults|JSAttribute}}')" />
      {{$patient2->nom_jeune_fille}}
    </td>
    <td>
      {{mb_field object=$finalPatient field="nom_jeune_fille" tabindex="103"}}
    </td>
  </tr>
  <tr>
    <th>{{mb_label object=$finalPatient field="sexe"}}</th>
    <td>
      <input type="radio" name="_choix_sexe" value="{{$patient1->sexe}}" checked="checked" onclick="setField(this.form.sexe, '{{$patient1->sexe}}')" />
      {{tr}}CPatient.sexe.{{$patient1->sexe}}{{/tr}}
    </td>
    <td>
      <input type="radio" name="_choix_sexe" value="{{$patient2->sexe}}" onclick="setField(this.form.sexe, '{{$patient2->sexe}}')" />
      {{tr}}CPatient.sexe.{{$patient2->sexe}}{{/tr}}
    </td>
    <td>
      {{mb_field object=$finalPatient field="sexe" tabindex="104"}}
    </td>
  </tr>
  <tr>
    <th>{{mb_label object=$finalPatient field="naissance"}}</th>
    <td>
      <input type="radio" name="_choix_naissance" value="{{$patient1->naissance}}" checked="checked"
      onclick="setField(this.form._jour, '{{$patient1->_jour}}'); setField(this.form._mois, '{{$patient1->_mois}}'); setField(this.form._annee, '{{$patient1->_annee}}');" />
      {{mb_value object=$patient1 field="naissance"}}
    </td>
    <td>
      <input type="radio" name="_choix_naissance" value="{{$patient2->naissance}}"
      onclick="setField(this.form._jour, '{{$patient2->_jour}}'); setField(this.form._mois, '{{$patient2->_mois}}'); setField(this.form._annee, '{{$patient2->_annee}}');" />
      {{mb_value object=$patient2 field="naissance"}}
    </td>
    <td>
      <input tabindex="105" type="text" name="_jour"  size="2" maxlength="2" value="{{if $finalPatient->_jour != "00"}}{{$finalPatient->_jour}}{{/if}}" onkeyup="followUp(event)" /> -
      <input tabindex="106" type="text" name="_mois"  size="2" maxlength="2" value="{{if $finalPatient->_mois != "00"}}{{$finalPatient->_mois}}{{/if}}" onkeyup="followUp(event)" /> -
      <input tabindex="107" type="text" name="_annee" size="4" maxlength="4" value="{{if $finalPatient->_annee != "0000"}}{{$finalPatient->_annee}}{{/if}}" />
    </td>
  </tr>
  <tr>
    <th>{{mb_label object=$finalPatient field="lieu_naissance"}}</th>
    <td>
      <input type="radio" name="_choix_lieu_naissance" value="{{$patient1->lieu_naissance}}" checked="checked" onclick="setField(this.form.lieu_naissance, '{{$patient1->lieu_naissance|smarty:nodefaults|JSAttribute}}')" />
      {{$patient1->lieu_naissance}}
    </td>
    <td>
      <input type="radio" name="_choix_lieu_naissance" value="{{$patient2->lieu_naissance}}" onclick="setField(this.form.lieu_naissance, '{{$patient2->lieu_naissance|smarty:nodefaults|JSAttribute}}')" />
      {{$patient2->lieu_naissance}}
    </td>
    <td>
      {{mb_field object=$finalPatient field="lieu_naissance" tabindex="108"}}
    </td>
  </tr>
  <tr>
    <th>{{mb_label object=$finalPatient field="nationalite"}}</th>
    <td>
      <input type="radio" name="_choix_nationalite" value="{{$patient1->nationalite}}" checked="checked" onclick="setField(this.form.nationalite, '{{$patient1->nationalite}}')" />
      {{tr}}CPatient.nationalite.{{$patient1->nationalite}}{{/tr}}
    </td>
    <td>
      <input type="radio" name="_choix_nationalite" value="{{$patient2->nationalite}}" onclick="setField(this.form.nationalite, '{{$patient2->nationalite}}')" />
      {{tr}}CPatient.nationalite.{{$patient2->nationalite}}{{/tr}}
    </td>
    <td>
      {{mb_field object=$finalPatient field="nationalite" tabindex="109"}}
    </td>
  </tr>
  <tr>
    <th>{{mb_label object=$finalPatient field="rques"}}</th>
    <td class="text">
      <input type="radio" name="_choix_rques" value="{{$patient1->rques}}" checked="checked" onclick="setField(this.form.rques, '{{$patient1->rques|smarty:nodefaults|JSAttribute}}')" />
      {{$patient1->rques|nl2br}}
    </td>
    <td class="text">
      <input type="radio" name="_choix_rques" value="{{$patient2->rques}}" onclick="setField(this.form.rques, '{{$patient2->rques|smarty:nodefaults|JSAttribute}}')" />
      {{$patient2->rques|nl2br}}
    </td>
    <td class="text">
      {{mb_field object=$finalPatient field="rques" tabindex="110" rows="3"}}
    </td>
  </tr>
  <tr>
    <th class="title" colspan="4">
      Coordonnées
    </th>
  </tr>
  <tr>
    <th>{{mb_label object=$finalPatient field="adresse"}}</th>
    <td class="text">
      <input type="radio" name="_choix_adresse" value="{{$patient1->adresse}}" checked="checked" onclick="setField(this.form.adresse, '{{$patient1->adresse|smarty:nodefaults|JSAttribute}}')" />
      {{$patient1->adresse}}
    </td>
    <td class="text">
      <input type="radio" name="_choix_adresse" value="{{$patient2->adresse}}" onclick="setField(this.form.adresse, '{{$patient2->adresse|smarty:nodefaults|JSAttribute}}')" />
      {{$patient2->adresse}}
    </td>
    <td>
      {{mb_field object=$finalPatient field="adresse" tabindex="111"}}
    </td>
  </tr>
  <tr>
    <th>{{mb_label object=$finalPatient field="cp"}}</th>
    <td>
      <input type="radio" name="_choix_cp" value="{{$patient1->cp}}" checked="checked" onclick="setField(this.form.cp, '{{$patient1->cp|smarty:nodefaults|JSAttribute}}')" />
      {{$patient1->cp}}
    </td>
    <td>
      <input type="radio" name="_choix_cp" value="{{$patient2->cp}}" onclick="setField(this.form.cp, '{{$patient2->cp|smarty:nodefaults|JSAttribute}}')" />
      {{$patient2->cp}}
    </td>
    <td>
      {{mb_field object=$finalPatient field="cp" tabindex="112"}}
    </td>
  </tr>
  <tr>
    <th>{{mb_label object=$finalPatient field="ville"}}</th>
    <td>
      <input type="radio" name="_choix_ville" value="{{$patient1->ville}}" checked="checked" onclick="setField(this.form.ville, '{{$patient1->ville|smarty:nodefaults|JSAttribute}}')" />
      {{$patient1->ville}}
    </td>
    <td>
      <input type="radio" name="_choix_ville" value="{{$patient2->ville}}" onclick="setField(this.form.ville, '{{$patient2->ville|smarty:nodefaults|JSAttribute}}')" />
      {{$patient2->ville}}
    </td>
    <td>
      {{mb_field object=$finalPatient field="ville" tabindex="113"}}
    </td>
  </tr>
  <tr>
    <th>{{mb_label object=$finalPatient field="pays"}}</th>
    <td>
      <input type="radio" name="_choix_pays" value="{{$patient1->pays}}" checked="checked" onclick="setField(this.form.pays, '{{$patient1->pays|smarty:nodefaults|JSAttribute}}')" />
      {{$patient1->pays}}
    </td>
    <td>
      <input type="radio" name="_choix_pays" value="{{$patient2->pays}}" onclick="setField(this.form.pays, '{{$patient2->pays|smarty:nodefaults|JSAttribute}}')" />
      {{$patient2->pays}}
    </td>
    <td>
      {{mb_field object=$finalPatient field="pays" tabindex="114"}}
    </td>
  </tr>
  <tr>
    <th>{{mb_label object=$finalPatient field="tel"}}</th>
    <td>
      <input type="radio" name="_choix_tel" value="{{$patient1->tel}}" checked="checked"
      onclick="setField(this.form._tel1, '{{$patient1->_tel1}}'); setField(this.form._tel2, '{{$patient1->_tel2}}');
      setField(this.form._tel3, '{{$patient1->_tel3}}'); setField(this.form._tel4, '{{$patient1->_tel4}}'); setField(this.form._tel5, '{{$patient1->_tel5}}');" />
      {{$patient1->tel}}
    </td>
    <td>
      <input type="radio" name="_choix_tel" value="{{$patient2->tel}}"
      onclick="setField(this.form._tel1, '{{$patient2->_tel1}}'); setField(this.form._tel2, '{{$patient2->_tel2}}');
      setField(this.form._tel3, '{{$patient2->_tel3}}'); setField(this.form._tel4, '{{$patient2->_tel4}}'); setField(this.form._tel5, '{{$patient2->_tel5}}');" />
      {{$patient2->tel}}
    </td>
    <td>
      {{mb_field object=$finalPatient field="_tel1" tabindex="115" size="2" maxlength="2" prop="num length|2" onkeyup="followUp(event)"}} -
      {{mb_field object=$finalPatient field="_tel2" tabindex="116" size="2" maxlength="2" prop="num length|2" onkeyup="followUp(event)"}} -
      {{mb_field object=$finalPatient field="_tel3" tabindex="117" size="2" maxlength="2" prop="num length|2" onkeyup="followUp(event)"}} -
      {{mb_field object=$finalPatient field="_tel4" tabindex="118" size="2" maxlength="2" prop="num length|2" onkeyup="followUp(event)"}} -
      {{mb_field object=$finalPatient field="_tel5" tabindex="119" size="2" maxlength="2" prop="num length|2"}}
    </td>
  </tr>
  <tr>
    <th>{{mb_label object=$finalPatient field="tel2"}}</th>
    <td>
      <input type="radio" name="_choix_tel2" value="{{$patient1->tel2}}" checked="checked"
      onclick="setField(this.form._tel21, '{{$patient1->_tel21}}'); setField(this.form._tel22, '{{$patient1->_tel22}}');
      setField(this.form._tel23, '{{$patient1->_tel23}}'); setField(this.form._tel24, '{{$patient1->_tel24}}'); setField(this.form._tel25, '{{$patient1->_tel25}}');" />
      {{$patient1->tel2}}
    </td>
    <td>
      <input type="radio" name="_choix_tel2" value="{{$patient2->tel2}}"
      onclick="setField(this.form._tel21, '{{$patient2->_tel21}}'); setField(this.form._tel22, '{{$patient2->_tel22}}');
      setField(this.form._tel23, '{{$patient2->_tel23}}'); setField(this.form._tel24, '{{$patient2->_tel24}}'); setField(this.form._tel25, '{{$patient2->_tel25}}');" />
      {{$patient2->tel2}}
    </td>
    <td>
      {{mb_field object=$finalPatient field="_tel21" tabindex="120" size="2" maxlength="2" prop="num length|2" onkeyup="followUp(event)"}} -
      {{mb_field object=$finalPatient field="_tel22" tabindex="121" size="2" maxlength="2" prop="num length|2" onkeyup="followUp(event)"}} -
      {{mb_field object=$finalPatient field="_tel23" tabindex="122" size="2" maxlength="2" prop="num length|2" onkeyup="followUp(event)"}} -
      {{mb_field object=$finalPatient field="_tel24" tabindex="123" size="2" maxlength="2" prop="num length|2" onkeyup="followUp(event)"}} -
      {{mb_field object=$finalPatient field="_tel25" tabindex="124" size="2" maxlength="2" prop="num length|2"}}
    </td>
  </tr>
  <tr>
    <th>{{mb_label object=$finalPatient field="profession"}}</th>
    <td>
      <input type="radio" name="_choix_profession" value="{{$patient1->profession}}" checked="checked" onclick="setField(this.form.profession, '{{$patient1->profession|smarty:nodefaults|JSAttribute}}')" />
      {{$patient1->profession}}
    </td>
    <td>
      <input type="radio" name="_choix_profession" value="{{$patient2->profession}}" onclick="setField(this.form.profession, '{{$patient2->profession|smarty:nodefaults|JSAttribute}}')" />
      {{$patient2->profession}}
    </td>
    <td>
      {{mb_field object=$finalPatient field="profession" tabindex="125"}}
    </td>

  {{assign var=field value=fin_validite_vitale}}
  <tr>
    <th>{{mb_label object=$finalPatient field=$field}}</th>
    <td>
      <input type="radio" name="_choix_{{$field}}" value="{{$patient1->$field}}" checked="checked" onclick="setField(this.form.{{$field}}, '{{$patient1->$field|smarty:nodefaults|JSAttribute}}')" />
      {{$patient1->$field}}
    </td>
    <td>
      <input type="radio" name="_choix_{{$field}}" value="{{$patient2->$field}}" onclick="setField(this.form.{{$field}}, '{{$patient2->$field|smarty:nodefaults|JSAttribute}}')" />
      {{$patient2->$field}}
    </td>
    <td class="date">{{mb_field object=$finalPatient field=$field tabindex="207"}}</td>
  </tr>

  {{assign var=field value=matricule}}
  <tr>
    <th>{{mb_label object=$finalPatient field=$field}}</th>
    <td>
      <input type="radio" name="_choix_{{$field}}" value="{{$patient1->$field}}" checked="checked" onclick="setField(this.form.{{$field}}, '{{$patient1->$field|smarty:nodefaults|JSAttribute}}')" />
      {{$patient1->$field}}
    </td>
    <td>
      <input type="radio" name="_choix_{{$field}}" value="{{$patient2->$field}}" onclick="setField(this.form.{{$field}}, '{{$patient2->$field|smarty:nodefaults|JSAttribute}}')" />
      {{$patient2->$field}}
    </td>
    <td>
      {{mb_field object=$finalPatient field=$field tabindex="206" size="15" maxlength="15"}}
    </td>
  </tr>

  <tr>
    <th>{{mb_label object=$finalPatient field="rang_beneficiaire"}}</th>
    <td>
      <input type="radio" name="_choix_rang_beneficiaire" value="{{$patient1->rang_beneficiaire}}" checked="checked" onclick="setField(this.form.rang_beneficiaire, '{{$patient1->rang_beneficiaire|smarty:nodefaults|JSAttribute}}')" />
      {{$patient1->rang_beneficiaire}}
    </td>
    <td>
      <input type="radio" name="_choix_rang_beneficiaire" value="{{$patient2->rang_beneficiaire}}" onclick="setField(this.form.rang_beneficiaire, '{{$patient2->rang_beneficiaire|smarty:nodefaults|JSAttribute}}')" />
      {{$patient2->rang_beneficiaire}}
    </td>
    <td>{{mb_field object=$finalPatient field="rang_beneficiaire" tabindex="207"}}</td>
  </tr>

  </tr>
</table>