<tr><th class="category" colspan="10">Dossier médical</th></tr>
  
  <tr>
    <th>{{mb_label object=$consult field="_date"}}</th>
    <td>{{mb_value object=$consult field="_date"}}</td>
    
    <th>{{mb_label object=$sejour field="_num_dossier"}}</th>
    <td>{{mb_value object=$sejour field="_num_dossier"}}</td>
  </tr>
  
  <tr>
    <th>{{mb_label object=$consult field="heure"}}</th>
    <td>{{mb_value object=$consult field="heure"}}</td>
  </tr>
    
  <tr>
    <th>Patient</th>
    <td>{{mb_value object=$patient field="_view"}} {{mb_include module=dPpatients template=inc_vw_ipp ipp=$patient->_IPP}}</td>
    
    <th>Né(e) le </th>
    <td>{{mb_value object=$patient field=naissance}} </td>
  </tr>
  
  <tr>
    <th>{{mb_label object=$patient field="sexe"}}</th>
    <td>{{if $patient->sexe == "m"}} M {{else}} F {{/if}}</td>
    
    <th>{{mb_label object=$patient field="_age"}}</th>
    <td>{{mb_value object=$patient field="_age"}} ans</td>
  </tr>
  
  <tr>
    <th>{{mb_label object=$patient field="adresse"}}</th>
    <td>{{mb_value object=$patient field="adresse"}}</td>
  </tr>
  
  <tr>
    <th>{{mb_label object=$patient field="cp"}} - {{mb_label object=$patient field="ville"}}</th>
    <td>{{mb_value object=$patient field="cp"}} {{mb_value object=$patient field="ville"}}</td>
  </tr>
  
  <tr>
    <th>{{mb_label object=$patient field="tel"}}</th>
    <td>{{mb_value object=$patient field="tel"}}</td>
  </tr>
  
  <tr>
    <th>{{mb_label object=$patient field="tel2"}}</th>
    <td>{{mb_value object=$patient field="tel2"}}</td>
  </tr>
  
  <tr>
    <th>{{mb_label object=$patient field="medecin_traitant"}}</th>
    <td>{{mb_value object=$patient field="medecin_traitant"}}</td>
  </tr>