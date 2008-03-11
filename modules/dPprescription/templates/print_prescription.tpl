<!-- Fermeture des tableaux -->
</td>
</tr>
</tbody>
</table>

<style type="text/css">

@media print {
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
   <th colspan="2" class="title">Médicaments</th>
  </tr>
  <tr>
    <th>Produit</th>
    <th>Posologie</th>
  </tr>
  <!-- AFFICHAGE SUIVANT ALD -->
  {{if @$lines.medicament.element.ald || @$lines.medicament.comment.ald}}
    <tr>
      <th colspan="2">
      Prescriptions relatives au traitement de l'affection de longue durée
      </th>
    </tr>
    <!-- Parcours des ald -->
    {{foreach from=$lines.medicament.element.ald item="elt_ald"}}
    <tr>
      <td>
        {{$elt_ald->_ref_produit->libelle}}
      </td>
      <td>
       {{$elt_ald->_ref_posologie->_view}}
     </td>
    </tr>
    {{/foreach}}  
    {{foreach from=$lines.medicament.comment.ald item="comment_ald"}}
     <tr>
      <td colspan="3">
      {{$comment_ald->commentaire}}
     </td>
    </tr> 
    {{/foreach}}
    <tr>
  </table>
  <div class="middle"></div>
  <table class="tbl">
  <tr>
    <th colspan="2">  
      Prescriptions SANS RAPPORT avec l'affection de longue durée
    </th>
      </tr>
      {{if !$lines.medicament.element.no_ald && !$lines.medicament.comment.no_ald}}
        <tr>
          <td colspan="2">Aucun élément de prescription</td>
        </tr>
      {{/if}}
      <!-- Parcours des non ald -->
      {{foreach from=$lines.medicament.element.no_ald item="elt_no_ald"}}
       <tr>
         <td>
           {{$elt_no_ald->_ref_produit->libelle}}
        </td>
        <td>
          {{$elt_no_ald->_ref_posologie->_view}}
        </td>
      </tr>
      {{/foreach}}  
      {{foreach from=$lines.medicament.comment.no_ald item="comment_no_ald"}}
      <tr>
        <td colspan="3">
         {{$comment_no_ald->commentaire}}
        </td>
      </tr>
     {{/foreach}}  
  {{else}}
  <!-- AFFICHAGE NORMAL -->
  {{foreach from=$prescription->_ref_lines_med_comments.med item=curr_line}}
  <tr>
    <td>
      {{$curr_line->_ref_produit->libelle}}
    </td>
    <td>
      {{$curr_line->_ref_posologie->_view}}
    </td>
  </tr>
  {{/foreach}}
  {{foreach from=$prescription->_ref_lines_med_comments.comment item=_line_comment}}
    <tr>
      <td colspan="3">
        {{$_line_comment->commentaire}}
      </td>
    </tr>
  {{/foreach}}

{{/if}}
</table>
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
		  <th colspan="2" class="title">
		    {{tr}}CCategoryPrescription.chapitre.{{$chap}}{{/tr}}
		  </th>
		</tr>
		<tr>
		  <th>Libelle</th>
		  <th>Commentaire</th>
		</tr>
		
  <!-- Affichage sous forme d'ALD -->
  {{if @$lines.$chap.element.ald || @$lines.$chap.comment.ald}}
    <tr>
      <th colspan="2">
      Prescriptions relatives au traitement de l'affection de longue durée
      </th>
    </tr>
    <!-- Parcours des ald -->
    {{foreach from=$lines.$chap.element.ald item="_elt_ald"}}
    <tr>
      <td>
        {{$_elt_ald->_ref_element_prescription->_view}}
      </td>
      <td>
       {{$_elt_ald->commentaire}}
     </td>
    </tr>
    {{/foreach}}  
    {{foreach from=$lines.$chap.comment.ald item="_comment_ald"}}
     <tr>
      <td colspan="3">
      {{$_comment_ald->commentaire}}
     </td>
    </tr> 
    {{/foreach}}
  </table>
  <div class="middle"></div>
  <table class="tbl">
    <tr>
    <th colspan="2">  
      Prescriptions SANS RAPPORT avec l'affection de longue durée
    </th>
    </tr>
    {{if !$lines.$chap.element.no_ald && !$lines.$chap.comment.no_ald}}
    <tr>
      <td colspan="2">Aucun élément de prescription</td>
    </tr>
    {{/if}}
      
      <!-- Parcours des non ald -->
      {{foreach from=$lines.$chap.element.no_ald item="_elt_no_ald"}}
       <tr>
         <td>
           {{$_elt_no_ald->_ref_element_prescription->_view}}
        </td>
        <td>
          {{$_elt_no_ald->commentaire}}
        </td>
      </tr>
      {{/foreach}}  
      {{foreach from=$lines.$chap.comment.no_ald item="_comment_no_ald"}}
      <tr>
        <td colspan="3">
         {{$_comment_no_ald->commentaire}}
        </td>
      </tr>
     {{/foreach}}
      
  {{else}}
  <!-- Affichage normal -->
	
		{{foreach from=$curr_chap.element item=curr_line_element}}
		<tr>
		  <td>
		   {{$curr_line_element->_ref_element_prescription->_view}}
		  </td>
		  <td>
		    {{$curr_line_element->commentaire}}
		  </td>
		</tr>
		{{/foreach}}
	  {{foreach from=$curr_chap.comment item=curr_line_comment}}
		<tr>
		  <td colspan="2">
		    {{$curr_line_comment->commentaire}}
		  </td>
		</tr>
		{{/foreach}}
	
  {{/if}}
</table>

</div>
{{/if}}
{{/foreach}}


<table>
<tbody>
<tr>
<td>