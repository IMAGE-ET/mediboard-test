<table class="form">
  <tr>
    <th class="halfPane category" colspan="2">
      Personne à prévenir
    </th>
    <th class="halfPane category" colspan="2">
      Employeur
    </th>
  </tr>

  <tr>
    <th><label for="prevenir_nom" title="Nom de la personne à prévenir">Nom</label></th>
    <td>{{mb_field object=$patient field="prevenir_nom" tabindex="301"}}</td>
    <th><label for="employeur_nom" title="Nom de l'Employeur">Nom de l'employeur</label></th>
    <td>{{mb_field object=$patient field="employeur_nom" tabindex="351"}}</td>
  </tr>
  
  <tr>
    <th><label for="prevenir_prenom" title="Prénom de la personne à prévenir">Prénom</label></th>
    <td>{{mb_field object=$patient field="prevenir_prenom" tabindex="302"}}</td>
    <th rowspan="2"><label for="employeur_adresse" title="Adresse de l'employeur">Adresse de l'employeur</label></th>
    <td rowspan="2">{{mb_field object=$patient field="employeur_adresse" tabindex="352"}}</td>
  </tr>
  
  <tr>
    <th rowspan="2"><label for="prevenir_adresse" title="Adresse de la personne à prévenir">Adresse</label></th>
    <td rowspan="2">{{mb_field object=$patient field="prevenir_adresse" tabindex="303"}}</td>
  </tr>
  
  <tr>
    <th><label for="employeur_cp" title="Code Postal">Code Postal</label></th>
    <td>
      {{mb_field object=$patient field="employeur_cp" tabindex="353" size="31" maxlength="5"}}
      <div style="display:none;" class="autocomplete" id="employeur_cp_auto_complete"></div>
    </td>
  </tr>
  
  <tr>
    <th><label for="prevenir_cp" title="Code Postal">Code Postal</label></th>
    <td>
      {{mb_field object=$patient field="prevenir_cp" tabindex="304" size="31" maxlength="5"}}
      <div style="display:none;" class="autocomplete" id="prevenir_cp_auto_complete"></div>
    </td>
    <th><label for="employeur_ville" title="Ville de l'employeur">Ville</label></th>
    <td>
      {{mb_field object=$patient field="employeur_ville" tabindex="354" size="31"}}
      <div style="display:none;" class="autocomplete" id="employeur_ville_auto_complete"></div>
    </td>
  </tr>
  
  <tr>
    <th><label for="prevenir_ville" title="Ville de la personne à prévenir">Ville</label></th>
    <td>
      {{mb_field object=$patient field="prevenir_ville" tabindex="305" size="31"}}
      <div style="display:none;" class="autocomplete" id="prevenir_ville_auto_complete"></div>
    </td>
    <th><label for="_tel41" title="Téléphone de l'employeur">Téléphone</label></th>
    <td>
      {{mb_field object=$patient field="_tel41" tabindex="355" size="2" maxlength="2" spec="num|length|2" onkeyup="followUp(this, '_tel42', 2)"}} -
      {{mb_field object=$patient field="_tel42" tabindex="356" size="2" maxlength="2" spec="num|length|2" onkeyup="followUp(this, '_tel43', 2)"}} -
      {{mb_field object=$patient field="_tel43" tabindex="357" size="2" maxlength="2" spec="num|length|2" onkeyup="followUp(this, '_tel44', 2)"}} -
      {{mb_field object=$patient field="_tel44" tabindex="358" size="2" maxlength="2" spec="num|length|2" onkeyup="followUp(this, '_tel45', 2)"}} -
      {{mb_field object=$patient field="_tel45" tabindex="359" size="2" maxlength="2" spec="num|length|2"}}
    </td>
  </tr>
  
  <tr>
    <th><label for="_tel31" title="Téléphone de la personne à prévenir">Téléphone</label></th>
    <td>
      {{mb_field object=$patient field="_tel31" tabindex="306" size="2" maxlength="2" spec="num|length|2" onkeyup="followUp(this, '_tel32', 2)"}} -
      {{mb_field object=$patient field="_tel32" tabindex="307" size="2" maxlength="2" spec="num|length|2" onkeyup="followUp(this, '_tel33', 2)"}} -
      {{mb_field object=$patient field="_tel33" tabindex="308" size="2" maxlength="2" spec="num|length|2" onkeyup="followUp(this, '_tel34', 2)"}} -
      {{mb_field object=$patient field="_tel34" tabindex="309" size="2" maxlength="2" spec="num|length|2" onkeyup="followUp(this, '_tel35', 2)"}} -
      {{mb_field object=$patient field="_tel35" tabindex="310" size="2" maxlength="2" spec="num|length|2"}}
    </td>
    <th><label for="employeur_urssaf" title="Veuillez saisir le numéro Urssaf">Numéro Urssaf</label></th>
    <td>{{mb_field object=$patient field="employeur_urssaf" tabindex="360"}}</td>
  </tr>
  
  <tr>
    <th><label for="prevenir_parente" title="Lien de parenté avec le patient">Lien de Parenté</label></th>
    <td>
      {{mb_field object=$patient field="prevenir_parente" tabindex="311" defaultOption="&mdash;Veuillez Choisir &mdash;"}}
    </td>
    <td colspan="2"></td>
  </tr>

</table>