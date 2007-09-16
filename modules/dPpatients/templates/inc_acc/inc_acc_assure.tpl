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
    <th>{{mb_label object=$patient field="assure_nom"}}</th>
    <td>{{mb_field object=$patient field="assure_nom" tabindex="401"}}</td>
    <th rowspan="2">{{mb_label object=$patient field="assure_adresse"}}</th>
    <td rowspan="2">{{mb_field object=$patient field="assure_adresse" tabindex="451"}}</td>
  </tr>
  
  <tr>
    <th>{{mb_label object=$patient field="assure_prenom"}}</th>
    <td>{{mb_field object=$patient field="assure_prenom" tabindex="402"}}</td>
  </tr>
  
  <tr>
    <th>{{mb_label object=$patient field="assure_nom_jeune_fille"}}</th>
    <td>{{mb_field object=$patient field="assure_nom_jeune_fille" tabindex="403"}}</td>
    <th>{{mb_label object=$patient field="assure_cp"}}</th>
    <td>
      {{mb_field object=$patient field="assure_cp" tabindex="452" size="31" maxlength="5"}}
      <div style="display:none;" class="autocomplete" id="assure_cp_auto_complete"></div>
    </td>
  </tr>

  <tr>
    <th>{{mb_label object=$patient field="assure_sexe"}}</th>
    <td>{{mb_field object=$patient field="assure_sexe" tabindex="404"}}</td>
    <th>{{mb_label object=$patient field="assure_ville"}}</th>
    <td>
      {{mb_field object=$patient field="assure_ville" tabindex="453" size="31"}}
      <div style="display:none;" class="autocomplete" id="assure_ville_auto_complete"></div>
    </td>
  </tr>
  
  <tr>
    <th>{{mb_label object=$patient field="assure_naissance" defaultFor="_assure_jour"}}</th>
    <td>
      <input tabindex="405" type="text" class="num length|2" name="_assure_jour"  size="2" maxlength="2" value="{{if $patient->_assure_jour != "00"}}{{$patient->_assure_jour}}{{/if}}" onkeyup="followUp(this, '_assure_mois', 2)" /> -
      <input tabindex="406" type="text" class="num length|2" name="_assure_mois"  size="2" maxlength="2" value="{{if $patient->_assure_mois != "00"}}{{$patient->_assure_mois}}{{/if}}" onkeyup="followUp(this, '_assure_annee', 2)" /> -
      <input tabindex="407" type="text" class="num length|4" name="_assure_annee" size="4" maxlength="4" value="{{if $patient->_assure_annee != "0000"}}{{$patient->_assure_annee}}{{/if}}" />
    </td>
    <th>{{mb_label object=$patient field="assure_pays"}}</th>
    <td>
      {{mb_field object=$patient field="assure_pays" tabindex="454" size="31"}}
      <div style="display:none;" class="autocomplete" id="assure_pays_auto_complete"></div>
    </td>
  </tr>

  <tr>
    <th>{{mb_label object=$patient field="assure_lieu_naissance"}}</th>
    <td>{{mb_field object=$patient field="assure_lieu_naissance" tabindex="408"}}</td>
    <th>{{mb_label object=$patient field="assure_tel" defaultFor="_assure_tel1"}}</th>
    <td>
      {{mb_field object=$patient field="_assure_tel1" tabindex="455" size="2" maxlength="2" prop="num length|2" onkeyup="followUp(this, '_assure_tel2', 2)"}} -
      {{mb_field object=$patient field="_assure_tel2" tabindex="456" size="2" maxlength="2" prop="num length|2" onkeyup="followUp(this, '_assure_tel3', 2)"}} -
      {{mb_field object=$patient field="_assure_tel3" tabindex="457" size="2" maxlength="2" prop="num length|2" onkeyup="followUp(this, '_assure_tel4', 2)"}} -
      {{mb_field object=$patient field="_assure_tel4" tabindex="458" size="2" maxlength="2" prop="num length|2" onkeyup="followUp(this, '_assure_tel5', 2)"}} -
      {{mb_field object=$patient field="_assure_tel5" tabindex="459" size="2" maxlength="2" prop="num length|2"}}
    </td>
  </tr>
  
  <tr>
    <th>{{mb_label object=$patient field="assure_nationalite"}}</th>
    <td>
      {{mb_field object=$patient field="assure_nationalite" tabindex="409"}}
    </td>
    <th>{{mb_label object=$patient field="assure_tel2" defaultFor="_assure_tel21"}}</th>
    <td>
      {{mb_field object=$patient field="_assure_tel21" tabindex="460" size="2" maxlength="2" prop="num length|2" onkeyup="followUp(this, '_assure_tel22', 2)"}} -
      {{mb_field object=$patient field="_assure_tel22" tabindex="461" size="2" maxlength="2" prop="num length|2" onkeyup="followUp(this, '_assure_tel23', 2)"}} -
      {{mb_field object=$patient field="_assure_tel23" tabindex="462" size="2" maxlength="2" prop="num length|2" onkeyup="followUp(this, '_assure_tel24', 2)"}} -
      {{mb_field object=$patient field="_assure_tel24" tabindex="463" size="2" maxlength="2" prop="num length|2" onkeyup="followUp(this, '_assure_tel25', 2)"}} -
      {{mb_field object=$patient field="_assure_tel25" tabindex="464" size="2" maxlength="2" prop="num length|2"}}
    </td>
  </tr>
  
  <tr>
    <th rowspan="2">{{mb_label object=$patient field="assure_rques"}}</th>
    <td rowspan="2">{{mb_field object=$patient field="assure_rques" tabindex="410"}}</td>
    <th>{{mb_label object=$patient field="assure_profession"}}</th>
    <td>{{mb_field object=$patient field="assure_profession" tabindex="465"}}</td>
	</tr>
	<tr>
    <th>{{mb_label object=$patient field="assure_matricule"}}</th>
    <td>{{mb_field object=$patient field="assure_matricule" tabindex="466" onblur="oAccord.changeTabAndFocus(0, this.form.nom)"}}</td>
  </tr>

</table>