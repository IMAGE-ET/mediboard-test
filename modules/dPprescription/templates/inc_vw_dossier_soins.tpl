<table class="tbl">
  <tr>
    <th colspan="2">Prescriptions</th>
  </tr>
  <tr>
    <th>Médicament</th>
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


</table>