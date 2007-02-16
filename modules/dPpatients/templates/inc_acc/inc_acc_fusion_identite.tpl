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
    <th><label for="nom" title="Nom du patient. Obligatoire">Nom</label></th>
    <td>
      <input type="radio" name="_choix_nom" value="{{$patient1->nom}}" checked="checked" onclick="setField(this.form.nom, '{{$patient1->nom|smarty:nodefaults|JSAttribute}}')" />
      {{$patient1->nom}}
    </td>
    <td>
      <input type="radio" name="_choix_nom" value="{{$patient2->nom}}" onclick="setField(this.form.nom, '{{$patient2->nom|smarty:nodefaults|JSAttribute}}')" />
      {{$patient2->nom}}
    </td>
    <td>
      <input tabindex="101" type="text" name="nom" value="{{$finalPatient->nom}}" title="{{$finalPatient->_props.nom}}" />
     </td>
  </tr>
  <tr>
    <th><label for="prenom" title="Prénom du patient. Obligatoire">Prénom</label></th>
    <td>
      <input type="radio" name="_choix_prenom" value="{{$patient1->prenom}}" checked="checked" onclick="setField(this.form.prenom, '{{$patient1->prenom|smarty:nodefaults|JSAttribute}}')" />
      {{$patient1->prenom}}
    </td>
    <td>
      <input type="radio" name="_choix_prenom" value="{{$patient2->prenom}}" onclick="setField(this.form.prenom, '{{$patient2->prenom|smarty:nodefaults|JSAttribute}}')" />
      {{$patient2->prenom}}
    </td>
    <td>
      <input tabindex="102" type="text" name="prenom" value="{{$finalPatient->prenom}}" title="{{$finalPatient->_props.prenom}}" /></td>
  </tr>
  <tr>
    <th><label for="nom_jeune_fille" title="Nom de jeune fille d'une femme mariée">Nom de jeune fille</label></th>
    <td>
      <input type="radio" name="_choix_nom_jeune_fille" value="{{$patient1->nom_jeune_fille}}" checked="checked" onclick="setField(this.form.nom_jeune_fille, '{{$patient1->nom_jeune_fille|smarty:nodefaults|JSAttribute}}')" />
      {{$patient1->nom_jeune_fille}}
    </td>
    <td>
      <input type="radio" name="_choix_nom_jeune_fille" value="{{$patient2->nom_jeune_fille}}" onclick="setField(this.form.nom_jeune_fille, '{{$patient2->nom_jeune_fille|smarty:nodefaults|JSAttribute}}')" />
      {{$patient2->nom_jeune_fille}}
    </td>
    <td>
      <input tabindex="103" type="text" name="nom_jeune_fille" title="{{$finalPatient->_props.nom_jeune_fille}}" value="{{$finalPatient->nom_jeune_fille}}" />
    </td>
  </tr>
  <tr>
    <th><label for="sexe" title="Sexe du patient">Sexe</label></th>
    <td>
      <input type="radio" name="_choix_sexe" value="{{$patient1->sexe}}" checked="checked" onclick="setField(this.form.sexe, '{{$patient1->sexe}}')" />
      {{tr}}CPatient.sexe.{{$patient1->sexe}}{{/tr}}
    </td>
    <td>
      <input type="radio" name="_choix_sexe" value="{{$patient2->sexe}}" onclick="setField(this.form.sexe, '{{$patient2->sexe}}')" />
      {{tr}}CPatient.sexe.{{$patient2->sexe}}{{/tr}}
    </td>
    <td>
      <select tabindex="104" name="sexe" title="{{$finalPatient->_props.sexe}}">
        {{html_options options=$finalPatient->_enumsTrans.sexe selected=$finalPatient->sexe}}
      </select>
    </td>
  </tr>
  <tr>
    <th><label for="_jour" title="Date de naissance du patient, au format JJ-MM-AAAA">Date de naissance</label></th>
    <td>
      <input type="radio" name="_choix_naissance" value="{{$patient1->naissance}}" checked="checked"
      onclick="setField(this.form._jour, '{{$patient1->_jour}}'); setField(this.form._mois, '{{$patient1->_mois}}'); setField(this.form._annee, '{{$patient1->_annee}}');" />
      {{$patient1->_naissance}}
    </td>
    <td>
      <input type="radio" name="_choix_naissance" value="{{$patient2->naissance}}"
      onclick="setField(this.form._jour, '{{$patient2->_jour}}'); setField(this.form._mois, '{{$patient2->_mois}}'); setField(this.form._annee, '{{$patient2->_annee}}');" />
      {{$patient2->_naissance}}
    </td>
    <td>
      <input tabindex="105" type="text" name="_jour"  size="2" maxlength="2" value="{{if $finalPatient->_jour != "00"}}{{$finalPatient->_jour}}{{/if}}" onkeyup="followUp(this, '_mois', 2)" /> -
      <input tabindex="106" type="text" name="_mois"  size="2" maxlength="2" value="{{if $finalPatient->_mois != "00"}}{{$finalPatient->_mois}}{{/if}}" onkeyup="followUp(this, '_annee', 2)" /> -
      <input tabindex="107" type="text" name="_annee" size="4" maxlength="4" value="{{if $finalPatient->_annee != "0000"}}{{$finalPatient->_annee}}{{/if}}" />
    </td>
  </tr>
  <tr>
    <th><label for="lieu_naissance" title="Lieu de naissance du patient">Lieu de naissance</label></th>
    <td>
      <input type="radio" name="_choix_lieu_naissance" value="{{$patient1->lieu_naissance}}" checked="checked" onclick="setField(this.form.lieu_naissance, '{{$patient1->lieu_naissance|smarty:nodefaults|JSAttribute}}')" />
      {{$patient1->lieu_naissance}}
    </td>
    <td>
      <input type="radio" name="_choix_lieu_naissance" value="{{$patient2->lieu_naissance}}" onclick="setField(this.form.lieu_naissance, '{{$patient2->lieu_naissance|smarty:nodefaults|JSAttribute}}')" />
      {{$patient2->lieu_naissance}}
    </td>
    <td>
      <input tabindex="108" type="text" name="lieu_naissance" title="{{$finalPatient->_props.lieu_naissance}}" value="{{$finalPatient->lieu_naissance}}" />
    </td>
  </tr>
  <tr>
    <th><label for="nationalite" title="Nationnalité du patient">Nationnalité</label></th>
    <td>
      <input type="radio" name="_choix_nationalite" value="{{$patient1->nationalite}}" checked="checked" onclick="setField(this.form.nationalite, '{{$patient1->nationalite}}')" />
      {{tr}}CPatient.nationalite.{{$patient1->nationalite}}{{/tr}}
    </td>
    <td>
      <input type="radio" name="_choix_nationalite" value="{{$patient2->nationalite}}" onclick="setField(this.form.nationalite, '{{$patient2->nationalite}}')" />
      {{tr}}CPatient.nationalite.{{$patient2->nationalite}}{{/tr}}
    </td>
    <td>
      <select tabindex="109" name="nationalite" title="{{$finalPatient->_props.nationalite}}">
        {{html_options options=$finalPatient->_enumsTrans.nationalite selected=$finalPatient->nationalite}}
      </select>
    </td>
  </tr>
  <tr>
    <th><label for="rques" title="Remarques générales concernant le patient">Remarques</label></th>
    <td class="text">
      <input type="radio" name="_choix_rques" value="{{$patient1->rques}}" checked="checked" onclick="setField(this.form.rques, '{{$patient1->rques|smarty:nodefaults|JSAttribute}}')" />
      {{$patient1->rques|nl2br}}
    </td>
    <td class="text">
      <input type="radio" name="_choix_rques" value="{{$patient2->rques}}" onclick="setField(this.form.rques, '{{$patient2->rques|smarty:nodefaults|JSAttribute}}')" />
      {{$patient2->rques|nl2br}}
    </td>
    <td class="text">
      <textarea tabindex="110" rows="3" title="{{$finalPatient->_props.rques}}" name="rques">{{$finalPatient->rques}}</textarea>
    </td>
  </tr>
  <tr>
    <th class="title" colspan="4">
      Coordonnées
    </th>
  </tr>
  <tr>
    <th><label for="adresse" title="Adresse du patient">Adresse</label></th>
    <td>
      <input type="radio" name="_choix_adresse" value="{{$patient1->adresse}}" checked="checked" onclick="setField(this.form.adresse, '{{$patient1->adresse|smarty:nodefaults|JSAttribute}}')" />
      {{$patient1->adresse}}
    </td>
    <td>
      <input type="radio" name="_choix_adresse" value="{{$patient2->adresse}}" onclick="setField(this.form.adresse, '{{$patient2->adresse|smarty:nodefaults|JSAttribute}}')" />
      {{$patient2->adresse}}
    </td>
    <td>
      <textarea tabindex="111" name="adresse" title="{{$finalPatient->_props.adresse}}">{{$finalPatient->adresse}}</textarea>
    </td>
  </tr>
  <tr>
    <th><label for="cp" title="Code postal">Code Postal</label></th>
    <td>
      <input type="radio" name="_choix_cp" value="{{$patient1->cp}}" checked="checked" onclick="setField(this.form.cp, '{{$patient1->cp|smarty:nodefaults|JSAttribute}}')" />
      {{$patient1->cp}}
    </td>
    <td>
      <input type="radio" name="_choix_cp" value="{{$patient2->cp}}" onclick="setField(this.form.cp, '{{$patient2->cp|smarty:nodefaults|JSAttribute}}')" />
      {{$patient2->cp}}
    </td>
    <td>
      <input tabindex="112" type="text" name="cp" value="{{$finalPatient->cp}}" title="{{$finalPatient->_props.cp}}" onclick="setField(this.form.cp, '{{$patient2->cp|smarty:nodefaults|JSAttribute}}')" />
    </td>
  </tr>
  <tr>
    <th><label for="ville" title="Ville du patient">Ville</label></th>
    <td>
      <input type="radio" name="_choix_ville" value="{{$patient1->ville}}" checked="checked" onclick="setField(this.form.ville, '{{$patient1->ville|smarty:nodefaults|JSAttribute}}')" />
      {{$patient1->ville}}
    </td>
    <td>
      <input type="radio" name="_choix_ville" value="{{$patient2->ville}}" onclick="setField(this.form.ville, '{{$patient2->ville|smarty:nodefaults|JSAttribute}}')" />
      {{$patient2->ville}}
    </td>
    <td>
      <input tabindex="113" type="text" name="ville" value="{{$finalPatient->ville}}" title="{{$finalPatient->_props.ville}}" />
    </td>
  </tr>
  <tr>
    <th><label for="pays" title="Pays de domicile du patient">Pays</label></th>
    <td>
      <input type="radio" name="_choix_pays" value="{{$patient1->pays}}" checked="checked" onclick="setField(this.form.pays, '{{$patient1->pays|smarty:nodefaults|JSAttribute}}')" />
      {{$patient1->pays}}
    </td>
    <td>
      <input type="radio" name="_choix_pays" value="{{$patient2->pays}}" onclick="setField(this.form.pays, '{{$patient2->pays|smarty:nodefaults|JSAttribute}}')" />
      {{$patient2->pays}}
    </td>
    <td>
      <input tabindex="114" type="text" name="pays" value="{{$finalPatient->pays}}" title="{{$finalPatient->_props.pays}}" />
    </td>
  </tr>
  <tr>
    <th><label for="_tel1" title="Numéro de téléphone filaire">Téléphone</label></th>
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
      <input tabindex="115" type="text" name="_tel1" size="2" maxlength="2" value="{{$finalPatient->_tel1}}" title="num length|2" onkeyup="followUp(this, '_tel2', 2)" /> - 
      <input tabindex="116" type="text" name="_tel2" size="2" maxlength="2" value="{{$finalPatient->_tel2}}" title="num length|2" onkeyup="followUp(this, '_tel3', 2)" /> -
      <input tabindex="117" type="text" name="_tel3" size="2" maxlength="2" value="{{$finalPatient->_tel3}}" title="num length|2" onkeyup="followUp(this, '_tel4', 2)" /> -
      <input tabindex="118" type="text" name="_tel4" size="2" maxlength="2" value="{{$finalPatient->_tel4}}" title="num length|2" onkeyup="followUp(this, '_tel5', 2)" /> -
      <input tabindex="119" type="text" name="_tel5" size="2" maxlength="2" value="{{$finalPatient->_tel5}}" title="num length|2" onkeyup="followUp(this, '_tel21', 2)" />
    </td>
  </tr>
  <tr>
    <th><label for="_tel21" title="Numéro de téléphone portable">Portable</label></th>
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
      <input tabindex="120" type="text" name="_tel21" size="2" maxlength="2" value="{{$finalPatient->_tel21}}" title="num length|2" onkeyup="followUp(this, '_tel22', 2)" /> - 
      <input tabindex="121" type="text" name="_tel22" size="2" maxlength="2" value="{{$finalPatient->_tel22}}" title="num length|2" onkeyup="followUp(this, '_tel23', 2)" /> -
      <input tabindex="122" type="text" name="_tel23" size="2" maxlength="2" value="{{$finalPatient->_tel23}}" title="num length|2" onkeyup="followUp(this, '_tel24', 2)" /> -
      <input tabindex="123" type="text" name="_tel24" size="2" maxlength="2" value="{{$finalPatient->_tel24}}" title="num length|2" onkeyup="followUp(this, '_tel25', 2)" /> -
      <input tabindex="124" type="text" name="_tel25" size="2" maxlength="2" value="{{$finalPatient->_tel25}}" title="num length|2" />
    </td>
  </tr>
  <tr>
    <th><label for="profession" title="Profession du patient">Profession</label></th>
    <td>
      <input type="radio" name="_choix_profession" value="{{$patient1->profession}}" checked="checked" onclick="setField(this.form.profession, '{{$patient1->profession|smarty:nodefaults|JSAttribute}}')" />
      {{$patient1->profession}}
    </td>
    <td>
      <input type="radio" name="_choix_profession" value="{{$patient2->profession}}" onclick="setField(this.form.profession, '{{$patient2->profession|smarty:nodefaults|JSAttribute}}')" />
      {{$patient2->profession}}
    </td>
    <td>
      <input tabindex="125" type="text" name="profession" title="{{$finalPatient->_props.profession}}" value="{{$finalPatient->profession}}" />
    </td>
  </tr>
</table>