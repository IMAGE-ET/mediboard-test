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
    <th>{{mb_label object=$patient field="nom"}}</th>
    <td>{{mb_field object=$patient field="nom" tabindex="101"}}</td>
    <th rowspan="2">{{mb_label object=$patient field="adresse"}}</th>
    <td rowspan="2">{{mb_field object=$patient field="adresse" tabindex="151"}}</td>
  </tr>
  
  <tr>
    <th>{{mb_label object=$patient field="prenom"}}</th>
    <td>{{mb_field object=$patient field="prenom" tabindex="102"}}</td>
  </tr>
  
  <tr>
    <th>{{mb_label object=$patient field="nom_jeune_fille"}}</th>
    <td>{{mb_field object=$patient field="nom_jeune_fille" tabindex="103"}}</td>
    <th>{{mb_label object=$patient field="cp"}}</th>
    <td>
      {{mb_field object=$patient field="cp" tabindex="152" size="31" maxlength="5"}}
      <div style="display:none;" class="autocomplete" id="cp_auto_complete"></div>
    </td>
  </tr>

  <tr>
    <th>{{mb_label object=$patient field="sexe"}}</th>
    <td>{{mb_field object=$patient field="sexe" tabindex="104"}}</td>
    <th>{{mb_label object=$patient field="ville"}}</th>
    <td>
      {{mb_field object=$patient field="ville" tabindex="153" size="31"}}
      <div style="display:none;" class="autocomplete" id="ville_auto_complete"></div>
    </td>
  </tr>
  
  <tr>
    <th>{{mb_label object=$patient field="naissance" defaultFor="_jour"}}</th>
    <td>
      <input tabindex="105" type="text" class="num length|2" name="_jour"  size="2" maxlength="2" value="{{if $patient->_jour != "00"}}{{$patient->_jour}}{{/if}}" onkeyup="followUp(this, '_mois', 2)" /> -
      <input tabindex="106" type="text" class="num length|2" name="_mois"  size="2" maxlength="2" value="{{if $patient->_mois != "00"}}{{$patient->_mois}}{{/if}}" onkeyup="followUp(this, '_annee', 2)" /> -
      <input tabindex="107" type="text" class="num length|4" name="_annee" size="4" maxlength="4" value="{{if $patient->_annee != "0000"}}{{$patient->_annee}}{{/if}}" />
    </td>
    <th>{{mb_label object=$patient field="pays"}}</th>
    <td>
      {{mb_field object=$patient field="pays" tabindex="154" size="31"}}
      <div style="display:none;" class="autocomplete" id="pays_auto_complete"></div>
    </td>
  </tr>
  
  <tr>
    <th>{{mb_label object=$patient field="rang_naissance"}}</th>
    <td>{{mb_field object=$patient field="rang_naissance" tabindex="108"}}</td>
    <th>{{mb_label object=$patient field="tel" defaultFor="_tel1"}}</th>
    <td>
      {{mb_field object=$patient field="_tel1" tabindex="155" size="2" maxlength="2" prop="num length|2" onkeyup="followUp(this, '_tel2', 2)"}} -
      {{mb_field object=$patient field="_tel2" tabindex="156" size="2" maxlength="2" prop="num length|2" onkeyup="followUp(this, '_tel3', 2)"}} -
      {{mb_field object=$patient field="_tel3" tabindex="157" size="2" maxlength="2" prop="num length|2" onkeyup="followUp(this, '_tel4', 2)"}} -
      {{mb_field object=$patient field="_tel4" tabindex="158" size="2" maxlength="2" prop="num length|2" onkeyup="followUp(this, '_tel5', 2)"}} -
      {{mb_field object=$patient field="_tel5" tabindex="159" size="2" maxlength="2" prop="num length|2"}}
    </td>  
  </tr>

  <tr>
    <th>{{mb_label object=$patient field="lieu_naissance"}}</th>
    <td>{{mb_field object=$patient field="lieu_naissance" tabindex="109"}}</td>
    <th>{{mb_label object=$patient field="tel2" defaultFor="_tel21"}}</th>
    <td>
      {{mb_field object=$patient field="_tel21" tabindex="160" size="2" maxlength="2" prop="num length|2" onkeyup="followUp(this, '_tel22', 2)"}} -
      {{mb_field object=$patient field="_tel22" tabindex="161" size="2" maxlength="2" prop="num length|2" onkeyup="followUp(this, '_tel23', 2)"}} -
      {{mb_field object=$patient field="_tel23" tabindex="162" size="2" maxlength="2" prop="num length|2" onkeyup="followUp(this, '_tel24', 2)"}} -
      {{mb_field object=$patient field="_tel24" tabindex="163" size="2" maxlength="2" prop="num length|2" onkeyup="followUp(this, '_tel25', 2)"}} -
      {{mb_field object=$patient field="_tel25" tabindex="164" size="2" maxlength="2" prop="num length|2"}}
    </td>
  </tr>
  
  <tr>
    <th>{{mb_label object=$patient field="nationalite"}}</th>
    <td>
      {{mb_field object=$patient field="nationalite" tabindex="110"}}
    </td>
    <th>{{mb_label object=$patient field="profession"}}</th>
    <td>{{mb_field object=$patient field="profession" tabindex="165" onblur="oAccord.changeTabAndFocus(1, this.form.regime_sante);"}}</td>
  </tr>
  
  <tr>
    <th>{{mb_label object=$patient field="rques"}}</th>
    <td>{{mb_field object=$patient field="rques" tabindex="111"}}</td>
    <td colspan="2"></td>
  </tr>

</table>