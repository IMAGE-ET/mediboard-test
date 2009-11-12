<table style="width: 100%">
  <tr>
    <td style="width: 50%">

<table class="form">
  <tr>
    <th>{{mb_label object=$patient field="code_regime"}}</th>
    <td>{{mb_field object=$patient field="code_regime"}}</td>
  </tr>
  <tr>
    <th>{{mb_label object=$patient field="caisse_gest"}}</th>
    <td>{{mb_field object=$patient field="caisse_gest"}}</td>
  </tr>
  <tr>
    <th>{{mb_label object=$patient field="centre_gest"}}</th>
    <td>{{mb_field object=$patient field="centre_gest"}}</td>
  </tr>
  <tr>
    <th>{{mb_label object=$patient field="regime_sante"}}</th>
    <td>{{mb_field object=$patient field="regime_sante"}}</td>
  </tr>
  <tr>
    <th>{{mb_label object=$patient field="deb_amo"}}</th>
    <td>{{mb_field object=$patient field="deb_amo" form="editFrm" register=true}}</td>
  </tr>
  <tr>
    <th>{{mb_label object=$patient field="fin_amo"}}</th>
    <td>{{mb_field object=$patient field="fin_amo" form="editFrm" register=true}}</td>
  </tr>
  <tr>
    <th>{{mb_label object=$patient field="code_exo"}}</th>
    <td>{{mb_field object=$patient field="code_exo"}}</td>
  </tr>
  <tr>
    <th>{{mb_label object=$patient field="code_sit"}}</th>
    <td>{{mb_field object=$patient field="code_sit"}}</td>
  </tr>
  <tr>
    <th>{{mb_label object=$patient field="regime_am"}}</th>
    <td>{{mb_field object=$patient field="regime_am"}}</td>
  </tr>
  <tr>
  	<th>{{mb_label object=$patient field="medecin_traitant_declare"}}</th>
  	<td>{{mb_field object=$patient field="medecin_traitant_declare"}}</td>
  </tr>
</table>

    </td>
    <td style="width: 50%">

<table class="form">
  <tr>
    <th>{{mb_label object=$patient field="ald"}}</th>
    <td>{{mb_field object=$patient field="ald"}}</td>
  </tr>
  <tr>
    <th>{{mb_label object=$patient field="incapable_majeur"}}</th>
    <td>{{mb_field object=$patient field="incapable_majeur"}}</td>
  </tr>
  <tr>
    <th>{{mb_label object=$patient field="cmu"}}</th>
    <td>{{mb_field object=$patient field="cmu" onchange="calculFinAmo();"}}</td>
  </tr>
  <tr>
    <th>{{mb_label object=$patient field="ATNC"}}</th>
    <td>{{mb_field object=$patient field="ATNC"}}</td>
  </tr>
  <tr>
    <th>{{mb_label object=$patient field="fin_validite_vitale"}}</th>
    <td>{{mb_field object=$patient field="fin_validite_vitale" form="editFrm" register=true}}</td>
  </tr>
  <tr>
    <th>{{mb_label object=$patient field="notes_amo"}}</th>
    <td>{{mb_field object=$patient field="notes_amo"}}</td>
  </tr>
  <tr>
  	<th>{{mb_label object=$patient field="libelle_exo"}}</th>
  	<td>{{mb_field object=$patient field="libelle_exo" onblur="tabs.changeTabAndFocus('correspondance', this.form.prevenir_nom)"}}</td>
  </tr>
  <tr>
    <th>{{mb_label object=$patient field="notes_amc"}}</th>
    <td>{{mb_field object=$patient field="notes_amc"}}</td>
  </tr>
</table>

    </td>
  </tr>
</table>