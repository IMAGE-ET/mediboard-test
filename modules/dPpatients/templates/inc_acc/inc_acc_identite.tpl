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
      {{mb_field object=$patient field="_jour" tabindex="105" onkeyup="followUp(event)"}}
      {{mb_field object=$patient field="_mois" tabindex="106" onkeyup="followUp(event)"}}
      {{mb_field object=$patient field="_annee" tabindex="107"}}
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
      {{mb_field object=$patient field="_tel1" tabindex="155" size="2" maxlength="2" prop="num length|2" onkeyup="followUp(event)"}} -
      {{mb_field object=$patient field="_tel2" tabindex="156" size="2" maxlength="2" prop="num length|2" onkeyup="followUp(event)"}} -
      {{mb_field object=$patient field="_tel3" tabindex="157" size="2" maxlength="2" prop="num length|2" onkeyup="followUp(event)"}} -
      {{mb_field object=$patient field="_tel4" tabindex="158" size="2" maxlength="2" prop="num length|2" onkeyup="followUp(event)"}} -
      {{mb_field object=$patient field="_tel5" tabindex="159" size="2" maxlength="2" prop="num length|2"}}
    </td>  
  </tr>

  <tr>
    <th>{{mb_label object=$patient field="lieu_naissance"}}</th>
    <td>{{mb_field object=$patient field="lieu_naissance" tabindex="109"}}</td>
    <th>{{mb_label object=$patient field="tel2" defaultFor="_tel21"}}</th>
    <td>
      {{mb_field object=$patient field="_tel21" tabindex="160" size="2" maxlength="2" prop="num length|2" onkeyup="followUp(event)"}} -
      {{mb_field object=$patient field="_tel22" tabindex="161" size="2" maxlength="2" prop="num length|2" onkeyup="followUp(event)"}} -
      {{mb_field object=$patient field="_tel23" tabindex="162" size="2" maxlength="2" prop="num length|2" onkeyup="followUp(event)"}} -
      {{mb_field object=$patient field="_tel24" tabindex="163" size="2" maxlength="2" prop="num length|2" onkeyup="followUp(event)"}} -
      {{mb_field object=$patient field="_tel25" tabindex="164" size="2" maxlength="2" prop="num length|2"}}
    </td>
  </tr>
  
  <tr>
    <th>{{mb_label object=$patient field="nationalite"}}</th>
    <td>
      {{mb_field object=$patient field="nationalite" tabindex="110"}}
    </td>
    <th>{{mb_label object=$patient field="profession"}}</th>
    <td>{{mb_field object=$patient field="profession" tabindex="165"}}</td>
  </tr>
  
  <tr>
    <th rowspan="3">{{mb_label object=$patient field="rques"}}</th>
    <td rowspan="3">{{mb_field object=$patient field="rques" tabindex="111"}}</td>
    <th>{{mb_label object=$patient field="fin_validite_vitale"}}</th>
    <td class="date">{{mb_field object=$patient field="fin_validite_vitale" form="editFrm" tabindex="166" }}</td>
  </tr>

  <tr>
    <th>{{mb_label object=$patient field="matricule"}}</th>
    <td>{{mb_field object=$patient field="matricule" tabindex="167"}}</td>
  </tr>
  
  <tr>
    <th>{{mb_label object=$patient field="rang_beneficiaire"}}</th>
    <td>{{mb_field object=$patient field="rang_beneficiaire" tabindex="168" onblur="this.value == '01' ?
           oAccord.changeTabAndFocus(1, this.form.regime_sante) :
           oAccord.changeTabAndFocus(3, this.form.assure_nom);"}}</td>
  </tr>
</table>