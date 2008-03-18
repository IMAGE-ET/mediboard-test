<!-- Fermeture des tableaux -->
</td>
</tr>
</tbody>
</table>

<style type="text/css">

span.signature {
  display: block;
  position: fixed;
  left: 20px;
  bottom: 30px;
  }

@media print {
  div#goUp {
    display: none;
  }
  span, ul, table {
    font-size: 12pt;
  }
  ul {
    padding-top: 10px;
    padding-bottom: 10px;
  }
  div.body {
    page-break-after: always;
    padding-top: 5cm;
  }
  div.header {
    position: fixed;
    top: 0.5cm;
    border-bottom: 1px solid black;
  }
  div.print_decalage{
    padding-top: 5cm;
  }
  div.middle {
    margin-top: 250px;
    height: 0px;
  }
}

</style>

<span class="signature">Dr. {{$prescription->_ref_praticien->_view}}</span>

<div class="header">
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
</div>

{{if $prescription->_ref_lines_med_comments.med || $prescription->_ref_lines_med_comments.comment}}
<div class="body">
<table class="tbl">
  <tr>
   <th class="title">Médicaments</th>
  </tr>
<!-- AFFICHAGE SUIVANT ALD -->
  {{if @$lines.medicament.element.ald || @$lines.medicament.comment.ald}}
  <tr>
    <th>
      Prescriptions relatives au traitement de l'affection de longue durée
    </th>
  </tr>
</table>

<!-- Parcours des ald -->
<ul>
  {{foreach from=$lines.medicament.element.ald item="elt_ald"}}
  <li>
    <strong>{{$elt_ald->_ref_produit->libelle}}</strong>:
    {{foreach from=$elt_ald->_ref_prises item=prise}}
      {{if $prise->quantite}}
      {{$prise->_view}}, 
      {{/if}}
    {{foreachelse}}
      {{$elt_ald->_ref_posologie->_view}}
    {{/foreach}}
     <em>{{$elt_ald->commentaire}}</em>
    {{$elt_ald->_duree_prise}}
    {{if $elt_ald->_specif_prise}}
		({{$elt_ald->_specif_prise}})
		{{/if}}
  </li>
  {{/foreach}}  
  {{foreach from=$lines.medicament.comment.ald item="comment_ald"}}
  <li>
    {{$comment_ald->commentaire}}
  </li>
  {{/foreach}}
</ul>
<div class="middle"></div>
<table class="tbl">
  <tr>
    <th>  
      Prescriptions SANS RAPPORT avec l'affection de longue durée
    </th>
  </tr>
</table>
<!-- Parcours des non ald -->
<ul>
  {{if !$lines.medicament.element.no_ald && !$lines.medicament.comment.no_ald}}
  <li>Aucun élément de prescription</li>
  {{/if}}
  {{foreach from=$lines.medicament.element.no_ald item="elt_no_ald"}}
  <li>
    <strong>{{$elt_no_ald->_ref_produit->libelle}}</strong>:
    {{foreach from=$elt_no_ald->_ref_prises item=prise}}
      {{if $prise->quantite}}
      {{$prise->_view}}, 
      {{/if}}
    {{foreachelse}}
      {{$elt_no_ald->_ref_posologie->_view}}
   {{/foreach}}  
   <em>{{$elt_no_ald->commentaire}}</em>
   {{$elt_no_ald->_duree_prise}}
   {{if $elt_no_ald->_specif_prise}}
	   ({{$elt_no_ald->_specif_prise}})
	 {{/if}}
  </li>
  {{/foreach}}  
  {{foreach from=$lines.medicament.comment.no_ald item="comment_no_ald"}}
  <li>
    {{$comment_no_ald->commentaire}}
  </li>
  {{/foreach}}  
  </ul>
  
  {{else}}
  </tr>
</table>
<!-- AFFICHAGE NORMAL -->
<ul>
  {{foreach from=$prescription->_ref_lines_med_comments.med item=curr_line}}
  <li>
    <strong>{{$curr_line->_ref_produit->libelle}}</strong>: 
    {{foreach from=$curr_line->_ref_prises item=prise}}
      {{if $prise->quantite}}
      {{$prise->_view}}, 
      {{/if}}
    {{foreachelse}}
      {{$curr_line->_ref_posologie->_view}}
    {{/foreach}}   
     <em>{{$curr_line->commentaire}}</em>
    {{$curr_line->_duree_prise}}
     {{if $curr_line->_specif_prise}}
	   ({{$curr_line->_specif_prise}})
	 {{/if}}
  </li>
  {{/foreach}}
  {{foreach from=$prescription->_ref_lines_med_comments.comment item=_line_comment}}
  <li>
    {{$_line_comment->commentaire}}
  </li>
  {{/foreach}}
</ul>
{{/if}}
</div>
{{/if}}

<!-- Parcours des chapitres -->
{{foreach from=$prescription->_ref_lines_elements_comments key=chap item=curr_chap name=Presc}}
{{if $curr_chap.element || $curr_chap.comment}}
{{if !$smarty.foreach.Presc.last}}
<div class="body">
{{else}}
<div class="print_decalage">
{{/if}}
<table class="tbl">
  <tr>
	  <th class="title">
		  {{tr}}CCategoryPrescription.chapitre.{{$chap}}{{/tr}}
		</th>
  </tr>
  <!-- Affichage sous forme d'ALD -->
  {{if @$lines.$chap.element.ald || @$lines.$chap.comment.ald}}
  <tr>
    <th>
      Prescriptions relatives au traitement de l'affection de longue durée
    </th>
  </tr>
</table>
<!-- Parcours des ald -->
<ul>
  {{foreach from=$lines.$chap.element.ald item="_elt_ald"}}
  <li>
    <strong>{{$_elt_ald->_ref_element_prescription->_view}}</strong>
    ({{$_elt_ald->_ref_element_prescription->_ref_category_prescription->_view}})
    <em>{{$_elt_ald->commentaire}}</em>
  </li>
  {{/foreach}}  
  {{foreach from=$lines.$chap.comment.ald item="_comment_ald"}}
  <li>
    {{$_comment_ald->commentaire}}
  </li> 
  {{/foreach}}
</ul>
<div class="middle"></div>
<table class="tbl">
  <tr>
    <th>  
      Prescriptions SANS RAPPORT avec l'affection de longue durée
    </th>
  </tr>
</table>
<ul>
  {{if !$lines.$chap.element.no_ald && !$lines.$chap.comment.no_ald}}
  <li>
    Aucun élément de prescription
  </li>
  {{/if}}
  <!-- Parcours des non ald -->
  {{foreach from=$lines.$chap.element.no_ald item="_elt_no_ald"}}
  <li>
    <strong>{{$_elt_no_ald->_ref_element_prescription->_view}}</strong>
    ({{$_elt_no_ald->_ref_element_prescription->_ref_category_prescription->_view}})
    <em>{{$_elt_no_ald->commentaire}}</em>
  </li>
  {{/foreach}}  
  {{foreach from=$lines.$chap.comment.no_ald item="_comment_no_ald"}}
  <li>
    {{$_comment_no_ald->commentaire}}
  </li>
  {{/foreach}}
</ul>  
  {{else}}
  </tr>
</table>
<!-- Affichage normal -->
<ul>
  {{foreach from=$curr_chap.element item=curr_line_element}}
  <li>
    <strong>{{$curr_line_element->_ref_element_prescription->_view}}</strong>
		({{$curr_line_element->_ref_element_prescription->_ref_category_prescription->_view}})
		<em>{{$curr_line_element->commentaire}}</em>
  </li>
  {{/foreach}}
	{{foreach from=$curr_chap.comment item=curr_line_comment}}
  <li>
    {{$curr_line_comment->commentaire}}
  </li>
  {{/foreach}}
</ul>
{{/if}}
</div>
{{/if}}
{{/foreach}}


<table>
<tbody>
<tr>
<td>