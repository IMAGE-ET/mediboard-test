<script type="text/javascript">

printDossierSoin = function(prescription_id, date){
  url = new Url;
  url.setModuleAction("dPprescription", "vw_plan_soin_pdf");
  url.addParam("prescription_id", prescription_id);
  url.popup(900, 600, "Plan de soin");
}


</script>


<table class="tbl">
  <tr>
    <th class="title">{{$sejour->_view}} (Dr {{$sejour->_ref_praticien->_view}})</th>
  </tr>
</table>

</table>
{{if $prescription_id}}

<h2 style="text-align: center">Dossier de soin du {{$date|@date_format:"%d/%m/%Y"}}</h2>


<table>
  <tr>
    <td>
      <button type="button" class="print" onclick="printDossierSoin('{{$prescription_id}}','{{$date}}');" title="{{tr}}Print{{/tr}}">
	      Imprimer la feuille de soins immédiate
      </button>
    </td>
  </tr>
</table>

<table class="tbl">
  <tr>
    <th rowspan="2">Type</th>
    <th rowspan="2">Libelle</th>
    <th rowspan="2">Posologie</th>
    <th colspan="8">Heures</th>
    <th rowspan="2">Signature<br /> praticien</th>
    <th rowspan="2">Signature<br /> pharmacien</th>
  </tr>
  <tr>
   {{foreach from=$tabHours item=_hour}}
	   <th>{{$_hour}}h</th>           
	 {{/foreach}}
  </tr>
  <!-- Affichage des medicaments -->
  {{if $lines_med|@count}}
    {{foreach from=$lines_med item=_line name="foreach_med"}}
		  {{foreach from=$_line key=unite_prise item=line name="foreach_line"}}
				  <tr>
				    {{if $smarty.foreach.foreach_med.first && $smarty.foreach.foreach_line.first}}
				    <th rowspan="{{$nb_produit_by_cat.med}}">Medicaments</th>
				    {{/if}}
				    <td class="text">{{$line->_ref_produit->libelle}}  {{if $line->_traitement}}(Traitement perso){{/if}} </td>
				    <td class="text">   
					    {{assign var=line_id value=$line->_id}}
					    {{if array_key_exists($line_id, $prises_med)}}
						    <ul>
						    {{foreach from=$prises_med.$line_id.$unite_prise item=prise name=prises}}
						      <li>
						      {{if $prise->nb_tous_les && $prise->unite_tous_les}}
						        {{$prise->quantite}} {{$prise->_ref_object->_unite_prise}}
						      {{else}}
						        {{$prise->_view}}
						      {{/if}}
						      </li>
						    {{/foreach}}
						    </ul>
					    {{/if}}
				    </td>
				    <!-- Affichage des heures de prises des medicaments -->
				    {{if count($list_prises_med) && @array_key_exists($unite_prise, $list_prises_med.$line_id)}}
					    {{assign var=prise_line value=$list_prises_med.$line_id.$unite_prise}}
					    {{foreach from=$tabHours item=_hour}}
				        <td style="text-align: center">
					        {{if array_key_exists($_hour, $prise_line)}}
					          {{assign var=quantite value=$prise_line.$_hour}}
					        {{else}}
					         {{assign var=quantite value=""}}
					        {{/if}}
				          {{$quantite}}
				        </td>
					     {{/foreach}}
					   {{else}}
					     {{foreach from=$tabHours item=_hour}}
					     <td></td>
					     {{/foreach}}
					   {{/if}}
				   <td style="text-align: center">
					    {{if $line->signee}}
					    <img src="images/icons/tick.png" alt="Signée par le praticien" title="Signée par le praticien" />
					    {{else}}
					    <img src="images/icons/cross.png" alt="Non signée par le praticien" title="Non signée par le praticien" />
					    {{/if}}
				    </td>
				    <td style="text-align: center">
					    {{if $line->valide_pharma}}
					    <img src="images/icons/tick.png" alt="Signée par le pharmacien" title="Signée par le pharmacien" />
					    {{else}}
					    <img src="images/icons/cross.png" alt="Non signée par le pharmacien" title="Non signée par le pharmacien" />
					    {{/if}}
				    </td>
				  </tr>
		  {{/foreach}}
	  {{/foreach}}
  {{/if}}
  
  
  <!-- Affichage des elements -->
  {{foreach from=$lines_element key=name_chap item=elements_chap}}
    {{foreach from=$elements_chap key=name_cat item=elements_cat}}
      {{assign var=categorie value=$categories.$name_chap.$name_cat}}
      {{foreach from=$elements_cat item=_element name="foreach_cat"}}
        {{foreach from=$_element key=unite_prise item=element name="foreach_elt"}}
      
        <tr>
		      {{if $smarty.foreach.foreach_elt.first && $smarty.foreach.foreach_cat.first}}
		        <th rowspan="{{$nb_produit_by_cat.$name_cat}}">{{tr}}CCategoryPrescription.chapitre.{{$name_chap}}{{/tr}} - {{$categorie->nom}}</th>
		      {{/if}}
    
			    <td class="text">{{$element->_view}}</td>
   	      
   	      <td class="text">
				    {{assign var=element_id value=$element->_id}}
				    {{if @array_key_exists($element_id, $prises_element)}}
				      <ul>
					    {{foreach from=$prises_element.$element_id.$unite_prise item=prise name=prises}}
					    <li>
					      {{if $prise->nb_tous_les && $prise->unite_tous_les}}
					        {{$prise->quantite}} {{$prise->_ref_object->_unite_prise}}
					      {{else}}
					        {{$prise->_view}}
					      {{/if}}
					    </li>
					    {{/foreach}}
					    </ul>
				    {{/if}}
			   </td>
				    <!-- Affichage des heures de prises des medicaments -->
				    {{if count($list_prises_element) && @array_key_exists($unite_prise, $list_prises_element.$element_id)}}
					    {{assign var=prise_line value=$list_prises_element.$element_id.$unite_prise}}
					    {{foreach from=$tabHours item=_hour}}
				        <td style="text-align: center">
					        {{if array_key_exists($_hour, $prise_line)}}
					          {{assign var=quantite value=$prise_line.$_hour}}
					        {{else}}
					         {{assign var=quantite value=""}}
					        {{/if}}
				          {{$quantite}}
				        </td>
					     {{/foreach}}
					   {{else}}
					     {{foreach from=$tabHours item=_hour}}
					     <td></td>
					     {{/foreach}}
					   {{/if}}
				   <td style="text-align: center">
				      {{if $element->signee}}
				        <img src="images/icons/tick.png" alt="Signée par le praticien" title="Signée par le praticien" />
				      {{else}}
				        <img src="images/icons/cross.png" alt="Non signée par le praticien" title="Non signée par le praticien" />
				      {{/if}}
				    </td>
				    <td style="text-align: center">
				    -
				    </td>
          </tr>
        {{/foreach}}
      {{/foreach}}
    {{/foreach}}
  {{/foreach}}
</table>
{{else}}
<div class="big-info">
  Ce dossier ne possède pas de prescription de séjour
</div>
{{/if}}