<!-- Fermeture des tableaux -->
    </td>
  </tr>
</table>


<style type="text/css">

div.body {
  page-break-after: always;
  padding: 10em 0 0 0;
  font-size: 12px; 
}

div.bodyWithoutPageBreak {
  padding: 10em 0 0 0;
  font-size: 12px; 
}

li {
  margin-left: 32px;
  padding-top: 3px;
}
 
div.header,
div.footer {
  position: fixed; 
  background: #ddd;
  opacity: 0.9;
  border: 0px solid #888;
  width: 100%;
  overflow:hidden;
}

h2 {
  text-align: center;
} 

h3 {
  text-align: center;
  border: 1px dotted #888;
  
}

div.header {
  top: 0cm;
  
  border-bottom-width: 1px;
  height: 7em;
}

div.footer {
  bottom: 0cm;
  border-top-width: 1px;
  height: 2em;
}

div.footer table {
  width: 100%;
  text-align: center;
}

@media print {
  div.header,div.footer {
    opacity: 1;
    background: #fff;
  }
  div.middle {
    margin-top: 270px;
    height: 0px;
  } 
  
}

@media screen {
  div.body {
    padding-bottom: 5px;
    border-bottom: 1px dotted #888;
  }
}

div#goUp {
  display: none;
}
  
</style>
  

<div class="header">
  <div style="text-align: left; margin: 4px;">
		<table class="main">
		  <tr>
		    <th colspan="2">
		      {{$etablissement->_view}}
		    </th>
		  </tr>
		  <tr>
		    <td>
		      <strong>Dr. {{$prescription->_ref_praticien->_view}}</strong>
		      <br />
		      {{$prescription->_ref_praticien->_ref_discipline->_view}}
		      <br />
		      {{$prescription->_ref_praticien->adeli}}
		    </td>
		  
		    <td style="text-align: right">
		      le {{$date|date_format:"%d %B %Y"}}
		      <br />
		      pour <strong>{{$prescription->_ref_patient->_longview}}</strong>
		    </td>
		  </tr>
		</table>    
  </div>
</div>


<!-- Affichage en mode ALD -->
{{if $lines.medicaments.med.ald || $lines.medicaments.med.no_ald ||
     $lines.medicaments.comment.ald || $lines.medicaments.comment.no_ald}}
  
  {{if $linesElt|@count}}
  <div class="body">
  {{else}}
  <div class="bodyWithoutPageBreak">
  {{/if}}

{{if $lines.medicaments.med.ald || $lines.medicaments.comment.ald}}
  <h2>Medicaments</h2>
    <!-- Affichage des ald -->
    <h3>
    Prescriptions relatives au traitement de l'affection de longue durée
		</h3>
		<ul>
    {{foreach from=$lines.medicaments.med.ald item=line_medicament_element_ald}}
      {{include file="inc_print_medicament.tpl" med=$line_medicament_element_ald}}
    {{/foreach}}
    {{foreach from=$lines.medicaments.comment.ald item=line_medicament_comment_ald}}
    <li>
      {{$line_medicament_comment_ald->commentaire}}
    </li>
    {{/foreach}}
    </ul>
    <div class="middle"></div>
    
    <!-- Affichage des no_ald -->
    <h3>
    Prescriptions SANS RAPPORT avec l'affection de longue durée
    </h3>
    <ul>
    {{foreach from=$lines.medicaments.med.no_ald item=line_medicament_element_no_ald}}
      {{include file="inc_print_medicament.tpl" med=$line_medicament_element_no_ald}}
    {{/foreach}}
    {{foreach from=$lines.medicaments.comment.no_ald item=line_medicament_comment_no_ald}}
    <li>
      {{$line_medicament_comment_no_ald->commentaire}}
    </li>
    {{/foreach}}
    </ul>
<!-- Affichage en mode normal -->
{{else}}
  <h2>Médicaments</h2>
    <!-- Affichage des no_ald -->
    <ul>
    {{foreach from=$lines.medicaments.med.no_ald item=line_medicament_element_no_ald}}
      {{include file="inc_print_medicament.tpl" med=$line_medicament_element_no_ald}}
    {{/foreach}}
    {{foreach from=$lines.medicaments.comment.no_ald item=line_medicament_comment_no_ald}}
    <li>
      {{$line_medicament_comment_no_ald->commentaire}}
    </li>
    {{/foreach}}
    </ul>
 
{{/if}}
 </div>
{{/if}}
<!-- Parcours des chapitres -->
{{foreach from=$linesElt key=name_chap item=elementsChap name="foreachChap"}}
<!-- Parcours des categories -->
{{foreach from=$elementsChap key=cat_id item=elementsCat name="foreachCat"}}
  {{assign var=name_category value=$categories.$cat_id}}
  {{foreach from=$elementsCat key=exec item=elements name="foreachExec"}}
    {{if $exec != "aucun"}}
      {{assign var=exec value=$executants.$exec}}
    {{/if}}
     
     {{if $smarty.foreach.foreachChap.last && $smarty.foreach.foreachCat.last && $smarty.foreach.foreachExec.last}}
       <div class="bodyWithoutPageBreak">
     {{else}} 
       <div class="body">
     {{/if}}
     
     <h2>{{tr}}CCategoryPrescription.chapitre.{{$name_chap}}{{/tr}} - {{$name_category->_view}}<br />{{if $exec != "aucun"}}{{$exec->_view}}{{/if}}</h2>
     
     {{if $elements.element.ald || $elements.comment.ald}}
       <h3>
         Prescriptions relatives au traitement de l'affection de longue durée
	   	 </h3>
	   	 <ul>
	     <!-- Affichage des ALD -->
	     {{foreach from=$elements.element.ald item=_element_ald}}
	       {{include file="inc_print_element.tpl" elt=$_element_ald}}
	     {{/foreach}}
	     {{foreach from=$elements.comment.ald item=_comment_ald}}
	     <li>
	       {{$_comment_ald->commentaire}}
	     </li>
	     {{/foreach}}
	     </ul>
	     
	     <div class="middle"></div>
	     
	     <h3>
         Prescriptions SANS RAPPORT avec l'affection de longue durée
       </h3>
	     <!-- Affichage des no_ald -->
	     <ul>
	     {{foreach from=$elements.element.no_ald item=_element_no_ald}}
	       {{include file="inc_print_element.tpl" elt=$_element_no_ald}}
	     {{/foreach}}
	     {{foreach from=$elements.comment.no_ald item=_comment_no_ald}}
	     <li>
	       {{$_comment_no_ald->commentaire}}
	     </li>
	     {{/foreach}}
	     </ul>
     {{else}}
	     <!-- Affichage normal -->
	     <ul>
	     {{foreach from=$elements.element.no_ald item=_element_no_ald}}
	       {{include file="inc_print_element.tpl" elt=$_element_no_ald}}
	     {{/foreach}}
	     {{foreach from=$elements.comment.no_ald item=_comment_no_ald}}
	     <li>
	       {{$_comment_no_ald->commentaire}}
	     </li>
	     {{/foreach}}
	     </ul>
     {{/if}}
     </div>
  {{/foreach}}
{{/foreach}}
{{/foreach}}


<!-- Affichage du pieds de page -->
<div class="footer">
  <div style="text-align: left; margin: 4px;">
    Dr. {{$prescription->_ref_praticien->_view}}
  </div>
</div>

<!-- Re-ouverture des tableaux -->
<table>
  <tr>
    <td>