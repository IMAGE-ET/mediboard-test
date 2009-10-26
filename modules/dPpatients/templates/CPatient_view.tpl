{{assign var="patient" value=$object}}

<table class="tbl">
  <tr>
    <th colspan="2">
      {{$patient->_view}}
    </th>
  </tr>
  <tr>
    <th>Identité</th>
    <th>Coordonnées</th>
  </tr>
  <tr>
    <td>
      <strong>{{mb_label object=$patient field="nom"}} :</strong>
      {{mb_value object=$patient field="nom"}}
      <br />
      <strong>{{mb_label object=$patient field="prenom"}} :</strong>
      {{mb_value object=$patient field="prenom"}}{{if $patient->prenom_2}}, 
      {{mb_value object=$patient field="prenom_2"}}{{/if}}{{if $patient->prenom_3}}, 
      {{mb_value object=$patient field="prenom_3"}}{{/if}}{{if $patient->prenom_4}}, 
      {{mb_value object=$patient field="prenom_4"}} {{/if}}
      <br />
      <strong>{{mb_label object=$patient field="sexe"}} :</strong>
      {{mb_value object=$patient field="sexe"}}
      <br />
      <strong>{{mb_label object=$patient field="naissance"}} :</strong>
      {{mb_value object=$patient field="naissance"}}
      <br />
      <strong>{{mb_label object=$patient field="profession"}} :</strong>
      {{mb_value object=$patient field="profession"}}
      <br />
      <strong>{{mb_label object=$patient field="matricule"}} :</strong>
      {{mb_value object=$patient field="matricule"}}
    </td>
    <td>
      <strong>{{mb_label object=$patient field="adresse"}} :</strong>
      {{mb_value object=$patient field="adresse"}}
      <strong>{{mb_label object=$patient field="cp"}} :</strong>
      {{mb_value object=$patient field="cp"}}
      <br />
      <strong>{{mb_label object=$patient field="ville"}} :</strong>
      {{mb_value object=$patient field="ville"}}
      <br />
      <strong>{{mb_label object=$patient field="tel"}} :</strong>
      {{mb_value object=$patient field="tel"}}
      <br />
      <strong>{{mb_label object=$patient field="tel2"}} :</strong>
      {{mb_value object=$patient field="tel2"}}
      <br />
      <strong>{{mb_label object=$patient field="rques"}} :</strong>
      {{mb_value object=$patient field="rques"}}
    </td>
  </tr>
</table>