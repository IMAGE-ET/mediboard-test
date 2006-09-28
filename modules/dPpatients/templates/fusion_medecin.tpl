<script type="text/javascript">

function setField(oField, sValue) {
  oField.value = sValue;
}

function setChecked(oField, sValue) {
  for (i=0; i < oField.length; i++){
    if (oField[i].value == sValue)
      oField[i].checked = true;
  }
}

</script>

<h2 class="module {{$m}}">Fusion de médecins</h2>

<form name="editFrm" action="index.php?m={{$m}}" method="post" onsubmit="return checkForm(this)">

<input type="hidden" name="dosql" value="do_medecins_fusion" />
<input type="hidden" name="del" value="0" />
<input type="hidden" name="medecin1_id" value="{{$medecin1->medecin_id}}" />
<input type="hidden" name="medecin2_id" value="{{$medecin2->medecin_id}}" />

<table class="form">
  <tr>
    <th class="category">Champ</th>
    <th width="30%" class="category">1er medecin</th>
    <th width="30%" class="category">2ème medecin</th>
    <th width="30%" class="category">Résultat</th>
  <tr>
    <th><label for="nom" title="Nom du medecin. Obligatoire">Nom</label></th>
    <td>
      <input type="radio" name="_choix_nom" value="{{$medecin1->nom}}" checked="checked" onclick="setField(this.form.nom, '{{$medecin1->nom|escape:"javascript"}}')" />
      {{$medecin1->nom}}
    </td>
    <td>
      <input type="radio" name="_choix_nom" value="{{$medecin2->nom}}" onclick="setField(this.form.nom, '{{$medecin2->nom|escape:"javascript"}}')" />
      {{$medecin2->nom}}
    </td>
    <td>
      <input tabindex="1" type="text" name="nom" value="{{$finalMedecin->nom}}" title="{{$finalMedecin->_props.nom}}" />
     </td>
  </tr>
  <tr>
    <th><label for="prenom" title="Prénom du medecin. Obligatoire">Prénom</label></th>
    <td>
      <input type="radio" name="_choix_prenom" value="{{$medecin1->prenom}}" checked="checked" onclick="setField(this.form.prenom, '{{$medecin1->prenom|escape:"javascript"}}')" />
      {{$medecin1->prenom}}
    </td>
    <td>
      <input type="radio" name="_choix_prenom" value="{{$medecin2->prenom}}" onclick="setField(this.form.prenom, '{{$medecin2->prenom|escape:"javascript"}}')" />
      {{$medecin2->prenom}}
    </td>
    <td>
      <input tabindex="2" type="text" name="prenom" value="{{$finalMedecin->prenom}}" title="{{$finalMedecin->_props.prenom}}" /></td>
  </tr>
  <tr>
    <th><label for="adresse" title="Adresse du medecin">Adresse</label></th>
    <td>
      <input type="radio" name="_choix_adresse" value="{{$medecin1->adresse}}" checked="checked" onclick="setField(this.form.adresse, '{{$medecin1->adresse|escape:"javascript"}}')" />
      {{$medecin1->adresse}}
    </td>
    <td>
      <input type="radio" name="_choix_adresse" value="{{$medecin2->adresse}}" onclick="setField(this.form.adresse, '{{$medecin2->adresse|escape:"javascript"}}')" />
      {{$medecin2->adresse}}
    </td>
    <td>
      <textarea tabindex="8" name="adresse" title="{{$finalMedecin->_props.adresse}}" rows="1">{{$finalMedecin->adresse}}</textarea>
    </td>
  </tr>
  <tr>
    <th><label for="cp" title="Code postal">Code Postal</label></th>
    <td>
      <input type="radio" name="_choix_cp" value="{{$medecin1->cp}}" checked="checked" onclick="setField(this.form.cp, '{{$medecin1->cp|escape:"javascript"}}')" />
      {{$medecin1->cp}}
    </td>
    <td>
      <input type="radio" name="_choix_cp" value="{{$medecin2->cp}}" onclick="setField(this.form.cp, '{{$medecin2->cp|escape:"javascript"}}')" />
      {{$medecin2->cp}}
    </td>
    <td>
      <input tabindex="9" type="text" name="cp" value="{{$finalMedecin->cp}}" title="{{$finalMedecin->_props.cp}}" onclick="setField(this.form.cp, '{{$medecin2->cp|escape:"javascript"}}')" />
    </td>
  </tr>
  <tr>
    <th><label for="ville" title="Ville du medecin">Ville</label></th>
    <td>
      <input type="radio" name="_choix_ville" value="{{$medecin1->ville}}" checked="checked" onclick="setField(this.form.ville, '{{$medecin1->ville|escape:"javascript"}}')" />
      {{$medecin1->ville}}
    </td>
    <td>
      <input type="radio" name="_choix_ville" value="{{$medecin2->ville}}" onclick="setField(this.form.ville, '{{$medecin2->ville|escape:"javascript"}}')" />
      {{$medecin2->ville}}
    </td>
    <td>
      <input tabindex="10" type="text" name="ville" value="{{$finalMedecin->ville}}" title="{{$finalMedecin->_props.ville}}" />
    </td>
  </tr>
  <tr>
    <th><label for="_tel1" title="Numéro de téléphone">Téléphone</label></th>
    <td>
      <input type="radio" name="_choix_tel" value="{{$medecin1->tel}}" checked="checked"
      onclick="setField(this.form._tel1, '{{$medecin1->_tel1}}'); setField(this.form._tel2, '{{$medecin1->_tel2}}');
      setField(this.form._tel3, '{{$medecin1->_tel3}}'); setField(this.form._tel4, '{{$medecin1->_tel4}}'); setField(this.form._tel5, '{{$medecin1->_tel5}}');" />
      {{$medecin1->tel}}
    </td>
    <td>
      <input type="radio" name="_choix_tel" value="{{$medecin2->tel}}"
      onclick="setField(this.form._tel1, '{{$medecin2->_tel1}}'); setField(this.form._tel2, '{{$medecin2->_tel2}}');
      setField(this.form._tel3, '{{$medecin2->_tel3}}'); setField(this.form._tel4, '{{$medecin2->_tel4}}'); setField(this.form._tel5, '{{$medecin2->_tel5}}');" />
      {{$medecin2->tel}}
    </td>
    <td>
      <input type="text" name="_tel1" size="2" maxlength="2" value="{{$finalMedecin->_tel1}}" title="num|length|2" onkeyup="followUp(this, '_tel2', 2)" /> - 
      <input type="text" name="_tel2" size="2" maxlength="2" value="{{$finalMedecin->_tel2}}" title="num|length|2" onkeyup="followUp(this, '_tel3', 2)" /> -
      <input type="text" name="_tel3" size="2" maxlength="2" value="{{$finalMedecin->_tel3}}" title="num|length|2" onkeyup="followUp(this, '_tel4', 2)" /> -
      <input type="text" name="_tel4" size="2" maxlength="2" value="{{$finalMedecin->_tel4}}" title="num|length|2" onkeyup="followUp(this, '_tel5', 2)" /> -
      <input type="text" name="_tel5" size="2" maxlength="2" value="{{$finalMedecin->_tel5}}" title="num|length|2" onkeyup="followUp(this, '_tel21', 2)" />
    </td>
  </tr>
  <tr>
    <th><label for="_fax1" title="Numéro de fax">Fax</label></th>
    <td>
      <input type="radio" name="_choix_fax" value="{{$medecin1->fax}}" checked="checked"
      onclick="setField(this.form._fax1, '{{$medecin1->_fax1}}'); setField(this.form._fax2, '{{$medecin1->_fax2}}');
      setField(this.form._fax3, '{{$medecin1->_fax3}}'); setField(this.form._fax4, '{{$medecin1->_fax4}}'); setField(this.form._fax5, '{{$medecin1->_fax5}}');" />
      {{$medecin1->fax}}
    </td>
    <td>
      <input type="radio" name="_choix_fax" value="{{$medecin2->fax}}"
      onclick="setField(this.form._fax1, '{{$medecin2->_fax1}}'); setField(this.form._fax2, '{{$medecin2->_fax2}}');
      setField(this.form._fax3, '{{$medecin2->_fax3}}'); setField(this.form._fax4, '{{$medecin2->_fax4}}'); setField(this.form._fax5, '{{$medecin2->_fax5}}');" />
      {{$medecin2->fax}}
    </td>
    <td>
      <input type="text" name="_fax1" size="2" maxlength="2" value="{{$finalMedecin->_fax1}}" title="num|length|2" onkeyup="followUp(this, '_fax2', 2)" /> - 
      <input type="text" name="_fax2" size="2" maxlength="2" value="{{$finalMedecin->_fax2}}" title="num|length|2" onkeyup="followUp(this, '_fax3', 2)" /> -
      <input type="text" name="_fax3" size="2" maxlength="2" value="{{$finalMedecin->_fax3}}" title="num|length|2" onkeyup="followUp(this, '_fax4', 2)" /> -
      <input type="text" name="_fax4" size="2" maxlength="2" value="{{$finalMedecin->_fax4}}" title="num|length|2" onkeyup="followUp(this, '_fax5', 2)" /> -
      <input type="text" name="_fax5" size="2" maxlength="2" value="{{$finalMedecin->_fax5}}" title="num|length|2" />
    </td>
  </tr>
  <tr>
    <th><label for="disciplines" title="Disciplines du medecin">Discipline</label></th>
    <td class="text">
      <input type="radio" name="_choix_disciplines" value="{{$medecin1->disciplines}}" checked="checked" onclick="setField(this.form.disciplines, '{{$medecin1->disciplines|escape:"javascript"}}')" />
      {{$medecin1->disciplines|nl2br}}
    </td>
    <td class="text">
      <input type="radio" name="_choix_disciplines" value="{{$medecin2->disciplines}}" onclick="setField(this.form.disciplines, '{{$medecin2->disciplines|escape:"javascript"}}')" />
      {{$medecin2->disciplines|nl2br}}
    </td>
    <td class="text">
      <textarea rows="3" title="{{$finalMedecin->_props.disciplines}}" name="disciplines">{{$finalMedecin->disciplines|nl2br}}</textarea>
    </td>
  </tr>
  <tr>
    <th><label for="orientations" title="Orientations du medecin">Orientations</label></th>
    <td class="text">
      <input type="radio" name="_choix_orientations" value="{{$medecin1->orientations}}" checked="checked" onclick="setField(this.form.orientations, '{{$medecin1->orientations|escape:"javascript"}}')" />
      {{$medecin1->orientations|nl2br}}
    </td>
    <td class="text">
      <input type="radio" name="_choix_orientations" value="{{$medecin2->orientations}}" onclick="setField(this.form.orientations, '{{$medecin2->orientations|escape:"javascript"}}')" />
      {{$medecin2->orientations|nl2br}}
    </td>
    <td class="text">
      <textarea rows="3" title="{{$finalMedecin->_props.orientations}}" name="orientations">{{$finalMedecin->orientations|nl2br}}</textarea>
    </td>
  </tr>
  <tr>
    <th><label for="complementaires" title="Complementaires du medecin">Complementaires</label></th>
    <td class="text">
      <input type="radio" name="_choix_complementaires" value="{{$medecin1->complementaires}}" checked="checked" onclick="setField(this.form.complementaires, '{{$medecin1->complementaires|escape:"javascript"}}')" />
      {{$medecin1->complementaires|nl2br}}
    </td>
    <td class="text">
      <input type="radio" name="_choix_complementaires" value="{{$medecin2->complementaires}}" onclick="setField(this.form.complementaires, '{{$medecin2->complementaires|escape:"javascript"}}')" />
      {{$medecin2->complementaires|nl2br}}
    </td>
    <td class="text">
      <textarea rows="3" title="{{$finalMedecin->_props.complementaires}}" name="complementaires">{{$finalMedecin->complementaires|nl2br}}</textarea>
    </td>
  </tr>
  <tr>
    <td class="button" colspan="4"><button type="submit" class="submit">Fusionner</button></td>
  </tr>
</table>

</form>