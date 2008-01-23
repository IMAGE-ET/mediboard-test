<table class="tbl">

	{{foreach from=$actes_ccam item=curr_praticien key=praticien_id}}
	<tr> 
	  {{assign var="praticien" value=$praticiens.$praticien_id}}
	  <th {{if $dPconfig.dPsalleOp.CActeCCAM.tarif}}colspan="9"{{else}}colspan="6"{{/if}}>{{$praticien->_view}}</th>
	</tr>      
	{{foreach from=$curr_praticien name=acte item="curr_acte"}}
	{{if $smarty.foreach.acte.first}}
	<tr>
	  <th>Code</th>
	  <th>Activite</th>
	  <th>Phase</th>
	  <th>Modificateurs</th>
	  <th>Association</th>
	  {{if $dPconfig.dPsalleOp.CActeCCAM.tarif}}
	  <th>Montant de base</th>
	  <th>Dépassement</th>
	  <th>Montant total</th>
	  {{/if}}
	  <th>Signature</th>
	</tr>
	{{/if}}
	<tr>
	  <td>{{$curr_acte->code_acte}}</td> 
	  <td>{{$curr_acte->code_activite}}</td>
	  <td>{{$curr_acte->code_phase}}</td>
	  <td>{{$curr_acte->modificateurs}}</td>
	  <td>{{$curr_acte->code_association}}</td>
	  {{if $dPconfig.dPsalleOp.CActeCCAM.tarif}}
	  <td>{{$curr_acte->montant_base|string_format:"%.2f"}} &euro;</td>
	  <td>{{$curr_acte->montant_depassement|string_format:"%.2f"}} &euro;</td>
	  <td>{{$curr_acte->_tarif|string_format:"%.2f"}} &euro;</td>
	  {{/if}}
    <td style="text-align: center">
    {{if $curr_acte->signe}}
      <img src="images/icons/tick.png" alt="" />
    {{else}}
      <img src="images/icons/cross.png" alt="" />
    {{/if}}
    </td>
	</tr>
	{{/foreach}}
	<!-- Formulaire de signature des actes ccam -->
	{{if $nonSigne.$praticien_id}}
	<tr>
	  <td {{if $dPconfig.dPsalleOp.CActeCCAM.tarif}}colspan="7"{{else}}colspan="4"{{/if}}></td>
	  <td colspan="2" style="text-align: right">
	    <form name="signature_acte-{{$praticien->_id}}" action="?" method="post">
	      <input type="hidden" name="dosql" value="do_signature_actes_aed" />
	      <input type="hidden" name="m" value="dPsalleOp" />
	      <input type="hidden" name="del" value="0" />
	      <input type="hidden" name="object_id" value="{{$object->_id}}" />
	      <input type="hidden" name="object_class" value="{{$object->_class_name}}" />
	      <input type="hidden" name="praticien_id" value="{{$praticien->_id}}" />
	      <input type="password"  class="notNull str" size="10" maxlength="32" name="password" />
        <button type="submit" class="submit">Signer</button>
	    </form>
	  </td>
	</tr>
	{{/if}}
	{{/foreach}}
</table>