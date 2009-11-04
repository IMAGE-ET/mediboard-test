<table style="width: 100%;">
  <tr>
    <td style="width: 50%;">
    
<table class="form">
  <tr>
    <th class="category" colspan="2">Personne à prévenir</th>
  </tr>
  <tr>
    <th>{{mb_label object=$patient field="prevenir_nom"}}</th>
    <td>{{mb_field object=$patient field="prevenir_nom"}}</td>
  </tr>
  <tr>
    <th>{{mb_label object=$patient field="prevenir_prenom"}}</th>
    <td>{{mb_field object=$patient field="prevenir_prenom"}}</td>
  </tr>
  <tr>
    <th>{{mb_label object=$patient field="prevenir_adresse"}}</th>
    <td>{{mb_field object=$patient field="prevenir_adresse"}}</td>
  </tr>
  <tr>
    <th>{{mb_label object=$patient field="prevenir_cp"}}</th>
    <td>{{mb_field object=$patient field="prevenir_cp"}}</td>
  </tr>
  <tr>
    <th>{{mb_label object=$patient field="prevenir_ville"}}</th>
    <td>{{mb_field object=$patient field="prevenir_ville"}}</td>
  </tr>
  <tr>
    <th>{{mb_label object=$patient field="prevenir_tel"}}</th>
    <td>{{mb_field object=$patient field="prevenir_tel"}}</td>
  </tr>
  <tr>
    <th>{{mb_label object=$patient field="prevenir_parente"}}</th>
    <td>{{mb_field object=$patient field="prevenir_parente" defaultOption="&mdash;Veuillez Choisir"}}</td>
  </tr>
</table>

    </td>
    <td style="50%">

<table class="form">
  <tr>
    <th class="category" colspan="2">Employeur</th>
  </tr>
  <tr>
    <th>{{mb_label object=$patient field="employeur_nom"}}</th>
    <td>{{mb_field object=$patient field="employeur_nom"}}</td>
  </tr>
  <tr>
    <th>{{mb_label object=$patient field="employeur_adresse"}}</th>
    <td>{{mb_field object=$patient field="employeur_adresse"}}</td>
  </tr>
  <tr>
    <th>{{mb_label object=$patient field="employeur_cp"}}</th>
    <td>{{mb_field object=$patient field="employeur_cp"}}</td>
  </tr>
  <tr>
    <th>{{mb_label object=$patient field="employeur_ville"}}</th>
    <td>{{mb_field object=$patient field="employeur_ville"}}</td>
  </tr>
  <tr>
    <th>{{mb_label object=$patient field="employeur_tel"}}</th>
    <td>{{mb_field object=$patient field="employeur_tel"}}</td>
  </tr>
  <tr>
    <th>{{mb_label object=$patient field="employeur_urssaf"}}</th>
    <td>{{mb_field object=$patient field="employeur_urssaf" onblur="tabs.changeTabAndFocus('assure', this.form.assure_nom);"}}</td>
  </tr>
</table>

    </td>
  </tr>
</table>