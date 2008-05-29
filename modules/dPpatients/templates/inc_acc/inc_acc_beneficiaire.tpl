<table class="form">
  <tr>
    <th>{{mb_label object=$patient field="code_regime"}}</th>
    <td>{{mb_field object=$patient field="code_regime" tabindex="201"}}</td>
    
    <th>{{mb_label object=$patient field="ald"}}</th>
    <td>{{mb_field object=$patient field="ald" tabindex="251"}}</td>
  </tr>

  <tr>
    <th>{{mb_label object=$patient field="caisse_gest"}}</th>
    <td>{{mb_field object=$patient field="caisse_gest" tabindex="202"}}</td>
    
    <th>{{mb_label object=$patient field="incapable_majeur"}}</th>
    <td>{{mb_field object=$patient field="incapable_majeur" tabindex="252"}}</td>
  </tr>

  <tr>
    <th>{{mb_label object=$patient field="centre_gest"}}</th>
    <td>{{mb_field object=$patient field="centre_gest" tabindex="203"}}</td>
    
    <th>{{mb_label object=$patient field="cmu"}}</th>
    <td>{{mb_field object=$patient field="cmu" onchange="calculFinAmo();" tabindex="253"}}</td>
  </tr>

  <tr>
    <th>{{mb_label object=$patient field="regime_sante"}}</th>
    <td>{{mb_field object=$patient field="regime_sante" tabindex="204"}}</td>
    
    <th>{{mb_label object=$patient field="ATNC"}}</th>
    <td>{{mb_field object=$patient field="ATNC" tabindex="254"}}</td>
  </tr>
  
  <tr>
    <th>{{mb_label object=$patient field="deb_amo"}}</th>
    <td class="date">{{mb_field object=$patient field="deb_amo" tabindex="205" form="editFrm" register=true}}</td>
    
    <th>{{mb_label object=$patient field="fin_validite_vitale"}}</th>
    <td class="date">{{mb_field object=$patient field="fin_validite_vitale" form="editFrm" register=true tabindex="255" }}</td>
  </tr>
  
  <tr>
    <th>{{mb_label object=$patient field="fin_amo"}}</th>
    <td class="date">{{mb_field object=$patient field="fin_amo" tabindex="206" form="editFrm" register=true}}</td>
    <th rowspan="4">{{mb_label object=$patient field="notes_amo"}}</th>
    <td rowspan="4">{{mb_field object=$patient field="notes_amo" tabindex="256" onblur="tabs.changeTabAndFocus('correspondance', this.form.prevenir_nom)"}}</td>
  </tr>
  
  <tr>
    <th>{{mb_label object=$patient field="code_exo"}}</th>
    <td>{{mb_field object=$patient field="code_exo" tabindex="207"}}</td>
  </tr>
  
  <tr>
    <th>{{mb_label object=$patient field="code_sit"}}</th>
    <td>{{mb_field object=$patient field="code_sit" tabindex="208"}}</td>
  </tr>
  
  <tr>
    <th>{{mb_label object=$patient field="regime_am"}}</th>
    <td>{{mb_field object=$patient field="regime_am" tabindex="209"}}</td>
  </tr>
  
</table>