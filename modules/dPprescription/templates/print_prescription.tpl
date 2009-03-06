{{if $print}}
<script type="text/javascript">
Main.add(window.print);
</script> 
{{/if}}

<!-- Fermeture des tableaux -->
    </td>
  </tr>
</table>

{{assign var=header value=10}}
{{assign var=footer value=5}}
<style type="text/css">
{{include file=../../dPcompteRendu/css/print.css header=10 footer=8}}
</style>
  
<div class="header">
	<table class="main">
	  <tr>
	    <td class="left">
	      {{assign var=function value=$praticien->_ref_function}}
	      
	      <strong>Dr {{$praticien->_view}}</strong>
	      <br />
	      {{mb_title object=$praticien field=adeli}}
	      {{mb_value object=$praticien field=adeli}}
	      <br />
	      {{$praticien->_ref_discipline->_view}}
	      <br />
	      {{mb_value object=$praticien field=titres}}
	      <br />
	    </td>
	    
	    <td class="center">
	      <h1>{{$etablissement->_view}}</h1>
	      {{mb_value object=$function field=soustitre}}
	    </td>
	  
	    <td class="right">
	      le {{$date|date_format:"%d %B %Y"}}
	      <br />
	      {{if $prescription->object_id}}
				A l'attention de 
				<br />		      
	      <strong>{{$prescription->_ref_patient->_view}}</strong>
	      {{else}}
	      Protocole: {{$prescription->libelle}}
	      {{/if}}
	    </td>
	  </tr>
	</table>    
</div>

<!-- Affichage du pieds de page -->
<div class="footer">
  {{if $prescription->type == "externe"}}
  <table>
  	<tr>
  	  <td class="left">
		   	{{mb_value object=$function field=soustitre}}
			</td>
  	  <td class="center">
		   	{{mb_value object=$function field=adresse}}
				<br />
				{{$function->cp}} &mdash; {{$function->ville}}
			</td>
  	  <td class="right">
			  Tel: {{mb_value object=$function field=tel}}
	  		<br />
	  		Fax: {{mb_value object=$function field=fax}}
	  	</td>
		</tr>
  </table>
	{{/if}}
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
  <h1>Medicaments</h1>
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
      {{if $line_medicament_element_no_ald->_class_name == "CPrescriptionLineMedicament"}}
        {{include file="inc_print_medicament.tpl" med=$line_medicament_element_no_ald}}
      {{else}}
        {{include file="inc_print_perfusion.tpl" perf=$line_medicament_element_no_ald}}
      {{/if}} 
    {{/foreach}}
    {{foreach from=$lines.medicaments.comment.no_ald item=line_medicament_comment_no_ald}}
		  <li>
		    {{$line_medicament_comment_no_ald->commentaire}}
		  </li>
	  {{/foreach}}
    </ul>
    
    {{if $traitements_arretes|@count && !$ordonnance}}
    <br />
    <h1>Traitements arrêtés</h1>
    <ul>
    {{foreach from=$traitements_arretes item=line_traitement}}
      <li>{{$line_traitement->_view}} (le {{$line_traitement->date_arret|date_format:"%d/%m/%Y"}})</li>
    {{/foreach}}
    </ul>
    {{/if}}
    
<!-- Affichage en mode normal -->
{{else}}
  <h1>Médicaments</h1>
    <!-- Affichage des no_ald -->
    <ul>
    {{foreach from=$lines.medicaments.med.no_ald item=line_medicament_element_no_ald}}
      {{if $line_medicament_element_no_ald->_class_name == "CPrescriptionLineMedicament"}}
        {{include file="inc_print_medicament.tpl" med=$line_medicament_element_no_ald}}
        {{if !$prescription->object_id}}
	        {{if $line_medicament_element_no_ald->_ref_substitution_lines|@count}}
	        <br />
	        <ul style="margin-left: 15px;">
	        <strong>Substitutions possibles:</strong> 
	        {{foreach from=$line_medicament_element_no_ald->_ref_substitution_lines item=_subst_line_med}}
	          {{include file="inc_print_medicament.tpl" med=$_subst_line_med}}
	        {{/foreach}}
	        </ul>
	        {{/if}}  
        {{/if}}
      {{else}}
        {{include file="inc_print_perfusion.tpl" perf=$line_medicament_element_no_ald}}
      {{/if}}  
    {{/foreach}}
      {{foreach from=$lines.medicaments.comment.no_ald item=line_medicament_comment_no_ald}}
		    <li>
		      {{$line_medicament_comment_no_ald->commentaire}}
		    </li>
	    {{/foreach}}
    </ul>
    
    
    {{if $traitements_arretes|@count && !$ordonnance}}
    <br />
    <h1>Traitements arrêtés</h1>
    <ul>
    {{foreach from=$traitements_arretes item=line_traitement}}
      <li>{{$line_traitement->_view}} (le {{$line_traitement->date_arret|date_format:"%d/%m/%Y"}})</li>
    {{/foreach}}
    </ul>
    {{/if}}
    
{{/if}}
 </div>
{{/if}}


<!-- Parcours des chapitres -->
{{foreach from=$linesElt key=name_chap item=elementsChap name="foreachChap"}}
 
<!-- Parcours des categories -->
  {{foreach from=$elementsChap key=exec item=elements name="foreachExec"}}
    {{if $exec != "aucun"}}
      {{assign var=exec value=$executants.$exec}}
    {{/if}}
     
     {{if $smarty.foreach.foreachChap.last && $smarty.foreach.foreachExec.last}}
       <div class="bodyWithoutPageBreak">
     {{else}} 
       <div class="body">
     {{/if}}
     
     <h1>{{tr}}CCategoryPrescription.chapitre.{{$name_chap}}{{/tr}}<br />{{if $exec != "aucun"}}{{$exec->_view}}{{/if}}</h1>
     
     <h2>{{$dPconfig.dPprescription.CCategoryPrescription.$name_chap.phrase}}</h2>
     
     {{if $elements.ald|@count}}
     <h3>
	     Prescriptions relatives au traitement de l'affection de longue durée 
		 </h3>
	   {{/if}}

     <ul>
	     <!-- Affichage des ALD -->
	     {{foreach from=$elements.ald key=name_cat item=_elements_ald name="foreach_elts_ald"}}  
	        {{foreach from=$_elements_ald  item=_element_ald name=foreach_elt_ald}}
	           {{if $smarty.foreach.foreach_elt_ald.first}}
	           {{assign var=category value=$categories.$name_chap.$name_cat}}
		         <strong>{{$category->nom}}</strong>
		         {{/if}}

		         {{if $_element_ald->_class_name == "CPrescriptionLineElement"}} 
	             <!-- Affichage de l'element -->
	             {{include file="inc_print_element.tpl" elt=$_element_ald}}
	           {{else}}
	             <!-- Affichage du commentaire -->
	              <li>{{$_element_ald->commentaire}}</li>
	           {{/if}}
	           
	        {{/foreach}}
	     {{/foreach}}
	     </ul>
	     
	    {{if $elements.ald|@count}}
	    <div class="middle"></div>
		  	 <h3>
	         Prescriptions SANS RAPPORT avec l'affection de longue durée 
	       </h3>
	    {{/if}}
	     
	     <!-- Affichage des no_ald -->
	     <ul>
	     {{foreach from=$elements.no_ald key=name_cat item=_elements_no_ald name="foreach_elts_no_ald"}}
	       {{foreach from=$_elements_no_ald  item=_element_no_ald name=foreach_elt_no_ald}}
	           {{if $smarty.foreach.foreach_elt_no_ald.first}}
	           {{assign var=category value=$categories.$name_chap.$name_cat}}
		         <strong>{{$category->nom}}</strong>
		         {{/if}}
		
		         {{if $_element_no_ald->_class_name == "CPrescriptionLineElement"}}
	             <!-- Affichage de l'element -->
	             {{include file="inc_print_element.tpl" elt=$_element_no_ald}}
	           {{else}}
	             <!-- Affichage du commentaire -->
	             <li>{{$_element_no_ald->commentaire}}</li>
	           {{/if}}
	        {{/foreach}}
	     {{/foreach}}
	     </ul>
        </div>
  {{/foreach}}
{{/foreach}}

{{if $linesDMI|@count}}
  <div class="body">
    <h1>DMI</h1>
    <ul>
    {{foreach from=$linesDMI item=_line_dmi}}
      <li>{{$_line_dmi->_ref_product->_view}}</li>
    {{/foreach}}
    </ul>
  </div>
{{/if}}
<!-- Re-ouverture des tableaux -->
<table>
  <tr>
    <td>