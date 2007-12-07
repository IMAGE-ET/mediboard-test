<table class="tbl">
	<tr>
	  <th>Code Activite Phase</th>
	  <th>Executant</th>
	  <th>Modificateurs</th>
	  <th>Association</th>
	  <th>Montant de base</th>
	  <th>Dépassement</th>
	  <th>Montant total</th>
	</tr>
	{{foreach from=$subject->_ext_codes_ccam item=curr_code key=curr_key}}
	  {{foreach from=$curr_code->activites item=curr_activite}}
	    {{foreach from=$curr_activite->phases item=curr_phase}}
	    <tr>
	      {{assign var="acte" value=$curr_phase->_connected_acte}}
	      {{assign var="view" value=$acte->_viewUnique}}
	      {{assign var="key" value="$curr_key$view"}}
	      {{mb_ternary var=listExecutants test=$acte->_anesth value=$listAnesths other=$listChirs}}
	      <td style="text-align: center; {{if $acte->_id && $acte->code_association}}background-color: #9f9;{{elseif $acte->_id}}background-color: #fc9;{{else}}background-color: #f99;{{/if}}">{{$curr_code->code}}-{{$curr_activite->numero}}-{{$curr_phase->phase}}</td>
	      {{if $acte->_id}}
	        {{assign var="executant_id" value=$acte->executant_id}}
	        {{assign var="executant" value=$listExecutants.$executant_id}}
	      <td style="background-color: #{{$executant->_ref_function->color}};">
	        {{$executant->_view}}
	      </td>
	      {{else}}
	      <td></td>
	      {{/if}}
	      <td>{{$acte->modificateurs}}</td>
	      <td>{{$acte->code_association}}</td>
	      <td>{{$acte->montant_base}}</td>
	      <td>{{$acte->montant_depassement}}</td>
	      <td>{{$acte->_montant_facture}}</td>
	    </tr> 
	    {{/foreach}}
	  {{/foreach}}
	{{/foreach}}
</table>