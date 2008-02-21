<table class="main">
  <tr>
    <th>
      {{$etablissement->_view}}
    </th>
  </tr>
  <tr>
    <td>
      Dr. {{$prescription->_ref_praticien->_view}}
      <br />
      {{$prescription->_ref_praticien->_ref_discipline->_view}}
      <br />
      {{$prescription->_ref_praticien->adeli}}
    </td>
  </tr>
  <tr>
    <td style="text-align: right">
      le {{$date|date_format:"%d %B %Y"}}
      <br />
      pour {{$prescription->_ref_patient->_longview}}
    </td>
  </tr>
  <tr>
    <td>
      <table class="tbl">
        <tr>
          <th>Produit</th>
          <th>Posologie</th>
        </tr>
        {{foreach from=$prescription->_ref_prescription_lines item=curr_line}}
        <tr>
          <td>
            {{$curr_line->_ref_produit->libelle}}
          </td>
          <td>
            {{$curr_line->_ref_posologie->_view}}
          </td>
        </tr>
        {{/foreach}}
      </table>
    </td>
  </tr>
</table>