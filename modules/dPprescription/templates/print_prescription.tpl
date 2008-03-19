<!-- Fermeture des tableaux -->
    </td>
  </tr>
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


<!-- Test pour savoir si la prescription contient des medicaments -->
{{if $prescription->_ref_lines_med_comments.med || $prescription->_ref_lines_med_comments.comment}}
  <div class="body">
		<table class="tbl">
		  <tr>
		   <th class="title">Médicaments</th>
		  </tr>  
		</table> 
		<!-- Affichage fragmenté pour la gestion des ALD -->
		{{if @$lines.medicament.element.ald || @$lines.medicament.comment.ald}}
		<table class="tbl">
		  <tr>
		    <th>
		      Prescriptions relatives au traitement de l'affection de longue durée
		    </th>
		  </tr>
		</table>
		<!-- ALD -->  
		{{include file = inc_print_medicament.tpl 
		          medicaments = $lines.medicament.element.ald
		          commentaires = $lines.medicament.comment.ald}}    
		
		<div class="middle"></div>
		
		<!-- NON ALD -->
		<table class="tbl">
		  <tr>
		    <th>  
		      Prescriptions SANS RAPPORT avec l'affection de longue durée
		    </th>
		  </tr>
		</table>
		{{if !$lines.medicament.element.no_ald && !$lines.medicament.comment.no_ald}}
		   Aucun élément de prescription
		 {{else}}
		   {{include file = inc_print_medicament.tpl 
		             medicaments = $lines.medicament.element.no_ald
		             commentaires = $lines.medicament.comment.no_ald}}    
		 {{/if}}  
	  <!-- AFFICHAGE NORMAL -->
    {{else}}
	      {{include file = "inc_print_medicament.tpl" 
	                medicaments = $prescription->_ref_lines_med_comments.med
	                commentaires = $prescription->_ref_lines_med_comments.comment}}
	  {{/if}}
  </div>	
{{/if}}


<!-- Parcours par chapitre des elements de la prescription -->
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
    </table>
  
    <!-- Affichage sous forme d'ALD -->
   {{if @$lines.$chap.element.ald || @$lines.$chap.comment.ald}}
   <table class="tbl">
     <tr>
       <th>
         Prescriptions relatives au traitement de l'affection de longue durée
       </th>
     </tr>
   </table>
   
   <!-- Parcours des ald -->
   {{include file=inc_print_element.tpl
             elements = $lines.$chap.element.ald
             commentaires = $lines.$chap.comment.ald}}

    <div class="middle"></div>

    <table class="tbl">
    <tr>
      <th>  
        Prescriptions SANS RAPPORT avec l'affection de longue durée
      </th>
    </tr>
  </table>
  
  {{if !$lines.$chap.element.no_ald && !$lines.$chap.comment.no_ald}}
    Aucun élément de prescription
  {{/if}}

  <!-- Non ALD -->
  {{include file=inc_print_element.tpl
            elements = $lines.$chap.element.no_ald
            commentaires = $lines.$chap.comment.no_ald}}

  {{else}}
  
  <!-- Affichage normal -->
  {{include file=inc_print_element.tpl
          elements = $curr_chap.element
          commentaires = $curr_chap.comment}}


  {{/if}}
 </div>
{{/if}}
{{/foreach}}

<!-- Re-ouverture des tableaux -->
<table>
  <tr>
    <td>