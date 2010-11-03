<tr>
  <th class="category" colspan="4">
    Dossier médical
    {{if $sejour->_num_dossier}}
     - [{{mb_value object=$sejour field="_num_dossier"}}]
    {{/if}}
  </th>
</tr>
  
<tr>
  <th>{{mb_label object=$consult field="_date"}}</th>
  <td>
    {{mb_value object=$consult field="_date"}} 
    {{mb_value object=$consult field="heure"}}
  </td>
  
  <th style="width: 25%">{{mb_label object=$consult field="motif"}}</th>
  <td>{{mb_value object=$consult field="motif"}}</td>
</tr>
  
<tr>
  <th style="width: 20%;">Patient</th>
  <td style="width: 30%;">
    {{mb_value object=$patient field="_view"}} 
    {{mb_include module=dPpatients template=inc_vw_ipp ipp=$patient->_IPP}}
  </td>
  
  <th>{{mb_label object=$patient field="adresse"}}</th>
  <td>{{mb_value object=$patient field="adresse"}}</td>
</tr>

<tr>
  <th style="width: 20%;">Né(e) le </th>
  <td>{{mb_value object=$patient field=naissance}} ({{mb_value object=$patient field="_age"}} ans)</td>
  
  <th>{{mb_label object=$patient field="cp"}} - {{mb_label object=$patient field="ville"}}</th>
  <td>{{mb_value object=$patient field="cp"}} {{mb_value object=$patient field="ville"}}</td>
</tr>

<tr>
  <th>{{mb_label object=$patient field="sexe"}}</th>
  <td>{{if $patient->sexe == "m"}} M {{else}} F {{/if}}</td>
  
  <th>{{mb_label object=$patient field="tel"}}</th>
  <td>{{mb_value object=$patient field="tel"}}</td>
</tr>

<tr>
  <th>{{mb_label object=$patient field="medecin_traitant"}}</th>
  <td>{{mb_value object=$patient field="medecin_traitant"}}</td>
  
  <th>{{mb_label object=$patient field="tel2"}}</th>
  <td>{{mb_value object=$patient field="tel2"}}</td>
</tr>