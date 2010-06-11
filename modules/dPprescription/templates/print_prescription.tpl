{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{if $print}}
<script type="text/javascript">
Main.add(window.print);
</script> 
{{/if}}

<!-- Fermeture des tableaux -->
    </td>
  </tr>
</table>

<style type="text/css">
{{include file=../../dPcompteRendu/css/print.css}}

div.body, table.body, div.bodyWithoutPageBreak, table.bodyWithoutPageBreak {
  padding-top: {{$header}}px;
  padding-bottom: {{$footer}}px;
}
	

@media screen {
	div.body, table.body, div.bodyWithoutPageBreak, table.bodyWithoutPageBreak {
	  padding-top: {{$header}}px;
	  padding-bottom: {{$footer}}px;
  }
}


/* Partie variable */
@media print {
  div.body, table.body, div.bodyWithoutPageBreak, table.bodyWithoutPageBreak {
    padding-top: {{$header+3}}px;
    padding-bottom: {{$footer+3}}px;
  }
	
	* {
    font-size: 12px;
    font-family: Arial,Helvetica,sans-serif;
  }
}

div.header {
  height: {{$header}}px;
}

div.footer {
  height: {{$footer}}px;
}


</style>

<div class="header" onclick="window.print();" style="cursor: pointer">
  {{if $_ald}}
	 <table class="main">
        <tr>
          <td class="left">
            <strong>Dr {{$praticien->_view}}</strong>
            <br />
						{{mb_value object=$praticien field=spec_cpam_id}}
            <br />
            {{mb_value object=$etablissement field=adresse}}
	          {{$etablissement->cp}} {{$etablissement->ville}}
					  <br />
            
            <span style="float: right">
					  {{mb_value object=$praticien field=secteur}}
            </span>

						<table style="width: 1%">
							<tr>
								<td style="white-space: nowrap;">
								  {{mb_value object=$praticien field=adeli}}
								</td>
								{{if $praticien->cab}}
								<td>
			            <table style="width: 1%">
			              <tr><td style="border-bottom: 1px solid black; text-align: center;">{{mb_value object=$praticien field=cab}}</td></tr>
			              <tr><td>{{mb_label object=$praticien field=cab}}</td></tr>
			            </table>
								</td>
								{{/if}}
                {{if $praticien->conv}}
                <td>
                  <table style="width: 1%">
                    <tr><td style="border-bottom: 1px solid black; text-align: center;">{{mb_value object=$praticien field=conv}}</td></tr>
                    <tr><td>{{mb_label object=$praticien field=conv}}</td></tr>
                  </table>
                </td>
                {{/if}}
                {{if $praticien->zisd}}
								<td>
                  <table style="width: 1%">
                    <tr><td style="border-bottom: 1px solid black; text-align: center;">{{mb_value object=$praticien field=zisd}}</td></tr>
                    <tr><td>{{mb_label object=$praticien field=zisd}}</td></tr>
                  </table>
                </td>
								{{/if}}
								{{if $praticien->ik}}
                <td>
                  <table style="width: 1%">
                    <tr><td style="border-bottom: 1px solid black; text-align: center;">{{mb_value object=$praticien field=ik}}</td></tr>
                    <tr><td>{{mb_label object=$praticien field=ik}}</td></tr>
                  </table>
                </td>
								{{/if}}
							</tr>
						</table>

					</td>
          <td class="right">
            <strong>{{$prescription->_ref_patient->_view}}</strong>
          </td>
        </tr>
      </table>  
	{{else}}
		{{if $generated_header}}
	    {{$generated_header|smarty:nodefaults}}
	  {{else}}
	  	<table class="main">
			  <tr>
			    <td class="left">
			      {{if $praticien->_id}}
				      <strong>Dr {{$praticien->_view}}</strong>
				      <br />
				      {{mb_title object=$praticien field=adeli}}
				      {{mb_value object=$praticien field=adeli}}
				      <br />
				      {{$praticien->_ref_discipline->_view}}
				      <br />
				      {{mb_value object=$praticien field=titres}}
				      <br />
			      {{elseif $prescription->object_id}}
			        Prescription globale
			      {{/if}}
			    </td>
			    <td class="center">
			      <h1>{{$etablissement->_view}}</h1>
			      {{if $function}}
			        {{mb_value object=$function field=soustitre}}
			      {{/if}}
			    </td>
			    <td class="right">
			      le {{$date|date_format:"%d %B %Y"}}
			      <br />
			      {{if $prescription->object_id}}
						A l'attention de 
						<br />		      
			      <strong>{{$prescription->_ref_patient->_view}}</strong>
			      <br />
			      Age: {{$prescription->_ref_patient->_age}} ans<br />
			      Poids: {{$poids}} kg
			      {{else}}
			      Protocole: {{$prescription->libelle}}
			      {{/if}}
			    </td>
			  </tr>
			</table>  
		{{/if}} 
	{{/if}}
</div>

<!-- Affichage du pieds de page -->
{{if !$_ald}}
<div class="footer">
  {{if $generated_footer}}
    {{$generated_footer|smarty:nodefaults}}
  {{else}}
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
{{/if}}

<!-- Affichage en mode ALD -->
{{if $lines.medicaments.med.ald || $lines.medicaments.med.no_ald ||
     $lines.medicaments.comment.ald || $lines.medicaments.comment.no_ald}}
  
  {{if $linesElt|@count}}
  <div class="body">
  {{else}}
  <div class="bodyWithoutPageBreak">
  {{/if}}
  
{{if $lines.medicaments.med.ald || $lines.medicaments.comment.ald}}
    <!-- Affichage des ald -->
   <div style="border: 1px dotted #555; padding-right: 10px; text-align: center;">
     <strong>Prescriptions relatives au traitement de l'affection de longue durée reconnue (liste ou hors liste)</strong>
		 <br />
		 (AFFECTION EXONERANTE)
	 </div>
		
		<ul>
      {{foreach from=$lines.medicaments.med.ald item=line_medicament_element_ald}}
        {{include file="inc_print_medicament.tpl" med=$line_medicament_element_ald nodebug=true}}
      {{/foreach}}
      {{foreach from=$lines.medicaments.comment.ald item=line_medicament_comment_ald}}
		      {{include file="inc_print_commentaire.tpl" comment=$line_medicament_comment_ald nodebug=true}}
	    {{/foreach}}
    </ul>
    <div class="middle"></div>
    <!-- Affichage des no_ald -->
		<div style="border: 1px dotted #555; padding-right: 10px; text-align: center;">
     <strong>Prescriptions SANS RAPPORT avec l'affection de longue durée</strong>
     <br />
    (MALADIES INTERCURRENTES)
   </div>
    <ul>
    {{foreach from=$lines.medicaments.med.no_ald item=line_medicament_element_no_ald}}
      {{if $line_medicament_element_no_ald->_class_name == "CPrescriptionLineMedicament"}}
        {{include file="inc_print_medicament.tpl" med=$line_medicament_element_no_ald nodebug=true}}
      {{else}}
        {{include file="inc_print_prescription_line_mix.tpl" perf=$line_medicament_element_no_ald nodebug=true}}
      {{/if}} 
    {{/foreach}}
    {{foreach from=$lines.medicaments.comment.no_ald item=line_medicament_comment_no_ald}}
      {{include file="inc_print_commentaire.tpl" comment=$line_medicament_comment_no_ald nodebug=true}}
	  {{/foreach}}
    </ul>
<!-- Affichage en mode normal -->
{{else}}
    <!-- Affichage des no_ald -->
    <ul>
    {{foreach from=$lines.medicaments.med.no_ald item=line_medicament_element_no_ald name="foreach_med"}}
		  {{if !$smarty.foreach.foreach_med.first && $smarty.foreach.foreach_med.index%15 == 0}}
			 </div>
			 <div class="body">
			{{/if}}
		
      {{if $line_medicament_element_no_ald->_class_name == "CPrescriptionLineMedicament"}}
        {{include file="inc_print_medicament.tpl" med=$line_medicament_element_no_ald nodebug=true}}
        {{if !$prescription->object_id}}
	        {{if $line_medicament_element_no_ald->_ref_substitution_lines.CPrescriptionLineMedicament|@count
	          || $line_medicament_element_no_ald->_ref_substitution_lines.CPrescriptionLineMix|@count}}
	        <br />
	        <ul style="margin-left: 15px;">
	        <strong>Substitutions possibles:</strong> 
	        {{foreach from=$line_medicament_element_no_ald->_ref_substitution_lines item=_subst_line_by_chap}}
	        {{foreach from=$_subst_line_by_chap item=_subst_line_med}}
	          {{if $_subst_line_med->_class_name == "CPrescriptionLineMedicament"}}
	            {{include file="inc_print_medicament.tpl" med=$_subst_line_med nodebug=true}}
	          {{else}}
	            {{include file="inc_print_prescription_line_mix.tpl" perf=$_subst_line_med nodebug=true}}
	          {{/if}}
	        {{/foreach}}
	        {{/foreach}}
	        </ul>
	        {{/if}}  
        {{/if}}
      {{else}}
        {{include file="inc_print_prescription_line_mix.tpl" perf=$line_medicament_element_no_ald}}
        {{if !$prescription->object_id}}
	        {{if $line_medicament_element_no_ald->_ref_substitution_lines.CPrescriptionLineMedicament|@count
	          || $line_medicament_element_no_ald->_ref_substitution_lines.CPrescriptionLineMix|@count}}
	        <br />
	        <ul style="margin-left: 15px;">
	          <strong>Substitutions possibles:</strong> 
	        {{foreach from=$line_medicament_element_no_ald->_ref_substitution_lines item=_subst_line_by_chap}}
	        {{foreach from=$_subst_line_by_chap item=_subst_line_med}}
	          {{if $_subst_line_med->_class_name == "CPrescriptionLineMedicament"}}
	            {{include file="inc_print_medicament.tpl" med=$_subst_line_med nodebug=true}}
	          {{else}}
	            {{include file="inc_print_prescription_line_mix.tpl" perf=$_subst_line_med nodebug=true}}
	          {{/if}}
	        {{/foreach}}
	        {{/foreach}}
	        </ul>
	        {{/if}}  
        {{/if}}
      {{/if}}  
    {{/foreach}}
      {{foreach from=$lines.medicaments.comment.no_ald item=line_medicament_comment_no_ald}}
        {{include file="inc_print_commentaire.tpl" comment=$line_medicament_comment_no_ald nodebug=true}}
	    {{/foreach}}
    </ul>
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
     
     {{if $smarty.foreach.foreachChap.last && $smarty.foreach.foreachExec.last &&  !$linesDMI|@count}}
       <div class="bodyWithoutPageBreak">
     {{else}} 
       <div class="body">
     {{/if}}

     <h2>{{$dPconfig.dPprescription.CCategoryPrescription.$name_chap.phrase}}</h2>
     {{if array_key_exists("ald", $elements) && $elements.ald|@count}}
	   <div style="border: 1px dotted #555; padding-right: 10px; text-align: center;">
	     <strong>Prescriptions relatives au traitement de l'affection de longue durée reconnue (liste ou hors liste)</strong>
	     <br />
	     (AFFECTION EXONERANTE)
	   </div>
	   {{/if}}

     {{if array_key_exists("ald", $elements)}}
     <ul>
	     <!-- Affichage des ALD -->
	     {{foreach from=$elements.ald key=name_cat item=_elements_ald name="foreach_elts_ald"}}  
	        {{foreach from=$_elements_ald  item=_element_ald name=foreach_elt_ald}}
	           {{if $smarty.foreach.foreach_elt_ald.first}}
						   {{if $name_cat != "inj"}}
			           {{assign var=category value=$categories.$name_chap.$name_cat}}
				         <strong>{{$category->nom}}</strong>
				         {{if $dPconfig.dPprescription.CCategoryPrescription.show_header && $category->header}}, {{$category->header}}{{/if}}
				         {{if $dPconfig.dPprescription.CCategoryPrescription.show_description && $category->description}}, {{$category->description}}{{/if}}
		           {{else}}
							   <strong>Injections par IDE à domicile (férié et dimanche)</strong>
							 {{/if}}
						 {{/if}}

             {{if $name_cat == "inj"}}
               {{include file="inc_print_medicament.tpl" med=$_element_ald nodebug=true}}
             {{else}}
			         {{if $_element_ald->_class_name == "CPrescriptionLineElement"}} 
		             <!-- Affichage de l'element -->
		             {{include file="inc_print_element.tpl" elt=$_element_ald nodebug=true}}
		           {{else}}
	               {{include file="inc_print_commentaire.tpl" comment=$_element_ald nodebug=true}}
		           {{/if}}
						 {{/if}}
	        {{/foreach}}
	     {{/foreach}}
	     </ul>
			 {{/if}}
	     
	    {{if array_key_exists("ald", $elements) && $elements.ald|@count}}
	    <div class="middle"></div>
	    <div style="border: 1px dotted #555; padding-right: 10px; text-align: center;">
	     <strong>Prescriptions SANS RAPPORT avec l'affection de longue durée</strong>
	     <br />
	    (MALADIES INTERCURRENTES)
	   </div>

	    {{/if}}
	     <!-- Affichage des no_ald -->
	    <ul>
	     {{foreach from=$elements.no_ald key=name_cat item=_elements_no_ald name="foreach_elts_no_ald"}}
	       {{foreach from=$_elements_no_ald  item=_element_no_ald name=foreach_elt_no_ald}}
	           {{if $smarty.foreach.foreach_elt_no_ald.first}}
						   {{if $name_cat != "inj"}}
							   {{assign var=category value=$categories.$name_chap.$name_cat}}
			           <strong>{{$category->nom}}</strong>
			         	 {{if $dPconfig.dPprescription.CCategoryPrescription.show_header && $category->header}}, {{$category->header}}{{/if}}
				         {{if $dPconfig.dPprescription.CCategoryPrescription.show_description && $category->description}}, {{$category->description}}{{/if}}
			         {{else}}
							   <strong>Injections par IDE à domicile (férié et dimanche)</strong>
							 {{/if}}
						 {{/if}}
		
		         {{if $name_cat == "inj"}}
						   {{include file="inc_print_medicament.tpl" med=$_element_no_ald nodebug=true}}
						 {{else}}
			         {{if $_element_no_ald->_class_name == "CPrescriptionLineElement"}}
		             <!-- Affichage de l'element -->
		             {{include file="inc_print_element.tpl" elt=$_element_no_ald nodebug=true}}
		           {{else}}
	               {{include file="inc_print_commentaire.tpl" comment=$_element_no_ald nodebug=true}}
		           {{/if}}
						 {{/if}}
	        {{/foreach}}
	     {{/foreach}}
	     </ul>
     </div>
  {{/foreach}}
{{/foreach}}

{{if $linesDMI|@count}}
  <div class="bodyWithoutPageBreak">
    <h1>DMI</h1>
    <ul>
    {{foreach from=$linesDMI item=_line_dmi}}
      <li><strong>{{$_line_dmi->_ref_product->name}}</strong>:
      <ul>
        <li>Quantité: <strong>{{$_line_dmi->quantity}}</strong></li>
        <li>Code produit: <strong>{{$_line_dmi->_ref_product->code}}</strong></li>
        <li>Code lot: <strong>{{$_line_dmi->_ref_product_order_item_reception->code}}</strong></li>
      </ul>
      </li>
    {{/foreach}}
    </ul>
  </div>
{{/if}}

<!-- Re-ouverture des tableaux -->
<table>
  <tr>
    <td>