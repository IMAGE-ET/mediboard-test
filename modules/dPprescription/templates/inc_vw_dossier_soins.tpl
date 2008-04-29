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


<table>
  <tr>
    <td>
      <button type="button" class="print" onclick="printDossierSoin('{{$prescription_id}}','{{$date}}');" title="{{tr}}Print{{/tr}}">
	      Imprimer la feuille de soin immédiate
      </button>
    </td>
  </tr>
</table>

<hr />

<table class="tbl">
  <tr>
    <th colspan="2">
      Dossier de soin du {{$date|@date_format:"%d/%m/%Y"}}
    </th>
  </tr>
  {{if $lines_med|@count}}
  <tr>
    <th colspan="2">Medicaments</th>
  </tr>
  <tr>
    <th>Libelle</th>
    <th>Posologie</th>
  </tr>
  {{foreach from=$lines_med item=line}}
  <tr>
    <td>{{$line->_ref_produit->libelle}}</td>
    <td>
    {{assign var=line_id value=$line->_id}}
    {{if array_key_exists($line_id, $prises)}}
	    {{foreach from=$prises.$line_id item=prise name=prises}}
	      {{if $prise->nb_tous_les && $prise->unite_tous_les}}
	        {{$prise->quantite}} {{$prise->_ref_object->_unite_prise}}
	      {{else}}
	        {{$prise->_view}}
	      {{/if}}
	      {{if !$smarty.foreach.prises.last}},{{/if}}
	    {{/foreach}}
    {{/if}}
    </td>
  </tr>
  {{/foreach}}
  {{/if}}
  
  {{if $lines_soin|@count}}
  <tr>
    <th colspan="2">Soins</th>
  </tr>
  <tr>
    <th>Libelle</th>
    <th>Posologie</th>
  </tr>
  {{foreach from=$lines_soin item=_soin}}
  <tr>
    <td>
      {{if !$_soin->signee}}
         <img src="images/icons/cross.png" title="Soin non signé par le praticien" alt="Soin non signé par le praticien">
      {{/if}}
      {{$_soin->_ref_element_prescription->_view}}
    
    </td>
    <td>
    {{assign var=soin_id value=$_soin->_id}}
    {{if array_key_exists($soin_id, $prises_soin)}}
	    {{foreach from=$prises_soin.$soin_id item=prise name=prises_soin}}
	      {{if $prise->nb_tous_les && $prise->unite_tous_les}}
	        {{$prise->quantite}} {{$prise->_ref_object->_unite_prise}}
	      {{else}}
	        {{$prise->_view}}
	      {{/if}}
	      {{if !$smarty.foreach.prises_soin.last}},{{/if}}
	    {{/foreach}}
    {{/if}}
    </td>
  </tr>
  {{/foreach}}
  {{/if}}
</table>