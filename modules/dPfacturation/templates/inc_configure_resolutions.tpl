Il y a {{$liaisons|@count}} problèmes de doubles facture
<br/>

<table class="main tbl">
  <tr>
    <th>Facture</th>
    <th>Patient</th>
    <th>Praticien</th>
    <th>Date Consultation1</th>
    <th>Date Consultation2</th>
  </tr>
  {{foreach from=$liaisons item=liaison}}
    <tr>
      {{assign var=facture value=$liaison->_ref_facture}}
      <td>{{$facture->_view}}</td>
      <td>
        <a href="#" onmouseover="ObjectTooltip.createEx(this, '{{$facture->_ref_patient->_guid}}')">
          {{$facture->_ref_patient->_view|truncate:30:"...":true}}</a>
      </td>
      <td>{{$facture->_ref_praticien->_view}}</td>
      <td>
        <a href="#" onmouseover="ObjectTooltip.createEx(this, '{{$facture->_ref_last_consult->_guid}}')">
          {{$facture->_ref_first_consult->_date}}</a>
      </td>
      <td>
        <a href="#" onmouseover="ObjectTooltip.createEx(this, '{{$facture->_ref_last_consult->_guid}}')">
          {{$facture->_ref_last_consult->_date}}</a>
      </td>
    </tr>
  {{/foreach}}
</table>
