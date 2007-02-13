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
    <td>{{mb_field object=$patient field="nom" tabindex="101"}}</td>
    <th rowspan="2"><label for="adresse" title="Adresse du patient">Adresse</label></th>
    <td rowspan="2">{{mb_field object=$patient field="adresse" tabindex="151"}}</td>
  </tr>
  
  <tr>
    <th><label for="prenom" title="Prénom du patient. Obligatoire">Prénom </label></th>
    <td>{{mb_field object=$patient field="prenom" tabindex="102"}}</td>
  </tr>
  
  <tr>
    <th><label for="nom_jeune_fille" title="Nom de jeune fille d'une femme mariée">Nom de jeune fille</label></th>
    <td>{{mb_field object=$patient field="nom_jeune_fille" tabindex="103"}}</td>
    <th><label for="cp" title="Code postal">Code Postal</label></th>
    <td>
      {{mb_field object=$patient field="cp" tabindex="152" size="31" maxlength="5"}}
      <div style="display:none;" class="autocomplete" id="cp_auto_complete"></div>
    </td>
  </tr>

  <tr>
    <th><label for="sexe" title="Sexe du patient">Sexe</label></th>
    <td>
      {{mb_field object=$patient field="sexe" defaultSelected="m" tabindex="104"}}
    </td>
    <th><label for="ville" title="Ville du patient">Ville</label></th>
    <td>
      {{mb_field object=$patient field="ville" tabindex="153" size="31"}}
      <div style="display:none;" class="autocomplete" id="ville_auto_complete"></div>
    </td>
  </tr>
  
  <tr>
    <th><label for="_jour" title="Date de naissance du patient, au format JJ-MM-AAAA">Date de naissance</label></th>
    <td>
      <input tabindex="105" type="text" title="num|length|2" name="_jour"  size="2" maxlength="2" value="{{if $patient->_jour != "00"}}{{$patient->_jour}}{{/if}}" onkeyup="followUp(this, '_mois', 2)" /> -
      <input tabindex="106" type="text" title="num|length|2" name="_mois"  size="2" maxlength="2" value="{{if $patient->_mois != "00"}}{{$patient->_mois}}{{/if}}" onkeyup="followUp(this, '_annee', 2)" /> -
      <input tabindex="107" type="text" title="num|length|4" name="_annee" size="4" maxlength="4" value="{{if $patient->_annee != "0000"}}{{$patient->_annee}}{{/if}}" />
    </td>
    <th><label for="pays" title="Pays de domicile du patient">Pays</label></th>
    <td>
      {{mb_field object=$patient field="pays" tabindex="154" size="31"}}
      <div style="display:none;" class="autocomplete" id="pays_auto_complete"></div>
    </td>
  </tr>

  <tr>
    <th><label for="lieu_naissance" title="Lieu de naissance du patient">Lieu de naissance</label></th>
    <td>{{mb_field object=$patient field="lieu_naissance" tabindex="108"}}</td>
    <th><label for="_tel1" title="Numéro de téléphone filaire">Téléphone</label></th>
    <td>
      {{mb_field object=$patient field="_tel1" tabindex="155" size="2" maxlength="2" spec="num|length|2" onkeyup="followUp(this, '_tel2', 2)"}} -
      {{mb_field object=$patient field="_tel2" tabindex="156" size="2" maxlength="2" spec="num|length|2" onkeyup="followUp(this, '_tel3', 2)"}} -
      {{mb_field object=$patient field="_tel3" tabindex="157" size="2" maxlength="2" spec="num|length|2" onkeyup="followUp(this, '_tel4', 2)"}} -
      {{mb_field object=$patient field="_tel4" tabindex="158" size="2" maxlength="2" spec="num|length|2" onkeyup="followUp(this, '_tel5', 2)"}} -
      {{mb_field object=$patient field="_tel5" tabindex="159" size="2" maxlength="2" spec="num|length|2"}}
    </td>
  </tr>
  
  <tr>
    <th><label for="nationalite" title="Nationnalité du patient">Nationnalité</label></th>
    <td>
      {{mb_field object=$patient field="nationalite" defaultSelected="local" tabindex="109"}}
    </td>
    <th><label for="_tel21" title="Numéro de téléphone portable">Portable</label></th>
    <td>
      {{mb_field object=$patient field="_tel21" tabindex="160" size="2" maxlength="2" spec="num|length|2" onkeyup="followUp(this, '_tel22', 2)"}} -
      {{mb_field object=$patient field="_tel22" tabindex="161" size="2" maxlength="2" spec="num|length|2" onkeyup="followUp(this, '_tel23', 2)"}} -
      {{mb_field object=$patient field="_tel23" tabindex="162" size="2" maxlength="2" spec="num|length|2" onkeyup="followUp(this, '_tel24', 2)"}} -
      {{mb_field object=$patient field="_tel24" tabindex="163" size="2" maxlength="2" spec="num|length|2" onkeyup="followUp(this, '_tel25', 2)"}} -
      {{mb_field object=$patient field="_tel25" tabindex="164" size="2" maxlength="2" spec="num|length|2"}}
    </td>
  </tr>
  
  <tr>
    <th><label for="rques" title="Remarques générales concernant le patient">Remarques</label></th>
    <td>
      {{mb_field object=$patient field="rques" tabindex="110"}}
    </td>
    <th><label for="profession" title="Profession du patient">Profession</label></th>
    <td>{{mb_field object=$patient field="profession" tabindex="165" onblur="oAccord.changeTabAndFocus(1, this.form.regime_sante);"}}</td>
  </tr>

</table>