<script type="text/javascript">

printDossierSoin = function(prescription_id, date){
  url = new Url;
  url.setModuleAction("dPprescription", "vw_plan_soin_pdf");
  url.addParam("prescription_id", prescription_id);
  url.addParam("suppressHeaders", "1");
  url.addParam("date", date);
  url.popup(800, 600, "Plan de soin");
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
    <th>Libelle</th>
    <th>Posologie</th>
    <th>Signature praticien</th>
    <th>Signature pharmacien</th>
  </tr>
  <!-- Affichage des medicaments -->
  {{if $lines_med|@count}}
  <tr>
    <th colspan="4">Medicaments</th>
  </tr>

  {{foreach from=$lines_med item=line}}
  <tr>
    <td>{{$line->_ref_produit->libelle}}  {{if $line->_traitement}}(Traitement perso){{/if}} </td>
    <td>
    {{assign var=line_id value=$line->_id}}
    {{if array_key_exists($line_id, $prises_med)}}
	    {{foreach from=$prises_med.$line_id item=prise name=prises}}
	      {{if $prise->nb_tous_les && $prise->unite_tous_les}}
	        {{$prise->quantite}} {{$prise->_ref_object->_unite_prise}}
	      {{else}}
	        {{$prise->_view}}
	      {{/if}}
	      {{if !$smarty.foreach.prises.last}},{{/if}}
	    {{/foreach}}
    {{/if}}
    </td>
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
  {{/if}}
  
  
  <!-- Affichage des elements -->
  {{foreach from=$lines_element key=name_chap item=elements_chap}}
  <tr>
    <th colspan="4">{{tr}}CCategoryPrescription.chapitre.{{$name_chap}}{{/tr}}</th>
  </tr>
  {{foreach from=$elements_chap key=name_cat item=elements_cat}}
  {{assign var=categorie value=$categories.$name_cat}}
  <tr>
    <th colspan="4" class="element">{{$categorie->nom}}</th>
  </tr>
  {{foreach from=$elements_cat item=element}}
  <tr>
    <td>{{$element->_view}}</td>
    <td>
	    {{assign var=element_id value=$element->_id}}
	    {{if array_key_exists($element_id, $prises_element)}}
		    {{foreach from=$prises_element.$element_id item=prise name=prises}}
		      {{if $prise->nb_tous_les && $prise->unite_tous_les}}
		        {{$prise->quantite}} {{$prise->_ref_object->_unite_prise}}
		      {{else}}
		        {{$prise->_view}}
		      {{/if}}
		      {{if !$smarty.foreach.prises.last}},{{/if}}
		    {{/foreach}}
	    {{/if}}
    </td> 
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
</table>
{{else}}
<div class="big-info">
  Ce dossier ne possède pas de prescription de séjour
</div>
{{/if}}