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
    <th>{{mb_label object=$patient field="prevenir_nom"}}</th>
    <td>{{mb_field object=$patient field="prevenir_nom" tabindex="301"}}</td>
    <th>{{mb_label object=$patient field="employeur_nom"}}</th>
    <td>{{mb_field object=$patient field="employeur_nom" tabindex="351"}}</td>
  </tr>
  
  <tr>
    <th>{{mb_label object=$patient field="prevenir_prenom"}}</th>
    <td>{{mb_field object=$patient field="prevenir_prenom" tabindex="302"}}</td>
    <th rowspan="2">{{mb_label object=$patient field="employeur_adresse"}}</th>
    <td rowspan="2">{{mb_field object=$patient field="employeur_adresse" tabindex="352"}}</td>
  </tr>
  
  <tr>
    <th rowspan="2">{{mb_label object=$patient field="prevenir_adresse"}}</th>
    <td rowspan="2">{{mb_field object=$patient field="prevenir_adresse" tabindex="303"}}</td>
  </tr>
  
  <tr>
    <th>{{mb_label object=$patient field="employeur_cp"}}</th>
    <td>
      {{mb_field object=$patient field="employeur_cp" tabindex="353" size="31" maxlength="5"}}
      <div style="display:none;" class="autocomplete" id="employeur_cp_auto_complete"></div>
    </td>
  </tr>
  
  <tr>
    <th>{{mb_label object=$patient field="prevenir_cp"}}</th>
    <td>
      {{mb_field object=$patient field="prevenir_cp" tabindex="304" size="31" maxlength="5"}}
      <div style="display:none;" class="autocomplete" id="prevenir_cp_auto_complete"></div>
    </td>
    <th>{{mb_label object=$patient field="employeur_ville"}}</th>
    <td>
      {{mb_field object=$patient field="employeur_ville" tabindex="354" size="31"}}
      <div style="display:none;" class="autocomplete" id="employeur_ville_auto_complete"></div>
    </td>
  </tr>
  
  <tr>
    <th>{{mb_label object=$patient field="prevenir_ville"}}</th>
    <td>
      {{mb_field object=$patient field="prevenir_ville" tabindex="305" size="31"}}
      <div style="display:none;" class="autocomplete" id="prevenir_ville_auto_complete"></div>
    </td>
    <th>{{mb_label object=$patient field="employeur_tel" defaultFor="_tel41"}}</th>
    <td>{{mb_field object=$patient field="_tel4"}}</td>
  </tr>
  
  <tr>
    <th>{{mb_label object=$patient field="prevenir_tel" defaultFor="_tel31"}}</th>
    <td>{{mb_field object=$patient field="_tel3"}}</td>
    <th>{{mb_label object=$patient field="employeur_urssaf"}}</th>
    <td>{{mb_field object=$patient field="employeur_urssaf" tabindex="360"}}</td>
  </tr>
  
  <tr>
    <th>{{mb_label object=$patient field="prevenir_parente"}}</th>
    <td>{{mb_field object=$patient field="prevenir_parente" tabindex="311" defaultOption="&mdash;Veuillez Choisir &mdash;"}}</td>
    <td colspan="2"></td>
  </tr>

</table>