<table class="tbl">

	{{foreach from=$actes_ccam item=curr_praticien key=praticien_id}}
	<tr> 
	  {{assign var=praticien value=$praticiens.$praticien_id}}
	  <th colspan="10">{{$praticien->_view}}</th>
	</tr>      
	{{foreach from=$curr_praticien name=acte item="curr_acte"}}
	{{if $smarty.foreach.acte.first}}
	<tr>
	  <th>{{mb_title object=$curr_acte field=code_acte}}</th>
	  <th>{{mb_title object=$curr_acte field=code_activite}}</th>
	  <th>{{mb_title object=$curr_acte field=code_phase}}</th>
	  <th>{{mb_title object=$curr_acte field=modificateurs}}</th>
	  <th>{{mb_title object=$curr_acte field=code_association}}</th>
	  {{if $dPconfig.dPsalleOp.CActeCCAM.tarif}}
	  <th>{{mb_title object=$curr_acte field=rembourse}}</th>
	  <th>{{mb_title object=$curr_acte field=montant_base}}</th>
	  <th>{{mb_title object=$curr_acte field=montant_depassement}}</th>
	  <th>{{mb_title object=$curr_acte field=_montant_facture}}</th>
	  {{/if}}
	  <th>{{mb_title object=$curr_acte field=signe}}</th>
	</tr>
	{{/if}}
	<tr>
	  <td>{{mb_value object=$curr_acte field=code_acte}}</td>
	  <td>{{mb_value object=$curr_acte field=code_activite}}</td>
	  <td>{{mb_value object=$curr_acte field=code_phase}}</td>
	  <td>{{mb_value object=$curr_acte field=modificateurs}}</td>
	  <td>{{mb_value object=$curr_acte field=code_association}}</td>
	  {{if $dPconfig.dPsalleOp.CActeCCAM.tarif}}
	  <td>{{mb_value object=$curr_acte field=rembourse}}</th>
	  <td>{{mb_value object=$curr_acte field=montant_base}}</th>
	  <td>{{mb_value object=$curr_acte field=montant_depassement}}</th>
	  <td>{{mb_value object=$curr_acte field=_montant_facture}}</th>
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