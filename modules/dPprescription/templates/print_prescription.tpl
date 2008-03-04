<!-- Header -->
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
</table>
  
<!-- Affichage des médicaments -->
{{if $prescription->_ref_prescription_lines|@count}}
<table class="tbl">
  <tr>
   <th colspan="2" class="title">Médicaments</th>
  </tr>
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
{{/if}}

<!-- Affichage des autres produits -->
<table class="tbl">
	<!-- Affichage des lignes de prescriptions hors medicaments -->
	{{foreach from=$prescription->_ref_prescription_lines_element_by_cat key=chap item=curr_chap}}
	<tr>
	  <th colspan="2" class="title">
	    {{tr}}CCategoryPrescription.chapitre.{{$chap}}{{/tr}}
	  </th>
	</tr>
	<tr>
	  <th>Libelle</th>
	  <th>Commentaire</th>
	</tr>
	{{foreach from=$curr_chap item=curr_line_element}}
	<tr>
	  <td>
	   {{$curr_line_element->_ref_element_prescription->_view}}
	  </td>
	  <td>
	    {{$curr_line_element->commentaire}}
	  </td>
	</tr>
	{{/foreach}}
	{{/foreach}}
</table>