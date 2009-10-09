{{**
  * Affiche les actes CCAM potentiels et ceux réellement codés
  * A ne pas confondre avec inc_list_actes_ccam.
  *}}

<table class="form">
	<tr>
	  <th class="category">
	  	{{mb_title class=CActeCCAM field=code_acte    }}
	  	{{mb_title class=CActeCCAM field=code_activite}}
	    {{mb_title class=CActeCCAM field=code_phase   }}
	  </th>
	  
	  <th class="category">{{mb_title class=CActeCCAM field=executant_id       }}</th>
	  <th class="category">{{mb_title class=CActeCCAM field=modificateurs      }}</th>
	  <th class="category">{{mb_title class=CActeCCAM field=code_association   }}</th>
	  <th class="category">{{mb_title class=CActeCCAM field=execution          }}</th>
    {{if $dPconfig.dPsalleOp.CActeCCAM.tarif || $subject->_class_name == "CConsultation"}}
    <th class="category">{{mb_title class=CActeCCAM field=montant_base       }}</th>
    <th class="category">{{mb_title class=CActeCCAM field=montant_depassement}}</th>
    <th class="category">{{mb_title class=CActeCCAM field=_montant_facture   }}</th>
    {{/if}}
	</tr>

	{{foreach from=$subject->_ext_codes_ccam item=curr_code key=curr_key}}
	  {{foreach from=$curr_code->activites item=curr_activite}}
	    {{foreach from=$curr_activite->phases item=curr_phase}}
	    <tr>
	      {{assign var="acte" value=$curr_phase->_connected_acte}}
	      {{assign var="view" value=$acte->_viewUnique}}
	      {{assign var="key" value="$curr_key$view"}}
	      {{mb_ternary var=listExecutants test=$acte->_anesth value=$listAnesths other=$listChirs}}
	      <td style="{{if $acte->_id && $acte->code_association == $acte->_guess_association}}background-color: #9f9;{{elseif $acte->_id}}background-color: #fc9;{{else}}background-color: #f99;{{/if}}">{{$curr_code->code}}-{{$curr_activite->numero}}-{{$curr_phase->phase}}</td>
	      {{if $acte->_id}}
	        {{assign var="executant_id" value=$acte->executant_id}}
	        {{assign var="executant" value=$listExecutants.$executant_id}}
	      <td> 
	        <div class="mediuser" style="border-color: #{{$executant->_ref_function->color}};">
          {{$executant}}
	        </div>
	      </td>
	      {{else}}
	      <td></td>
	      {{/if}}
	      <td>{{$acte->modificateurs}}</td>
	      <td>{{$acte->code_association}}</td>
	      <td>{{mb_value object=$acte field=execution}}</td>
        {{if $dPconfig.dPsalleOp.CActeCCAM.tarif || $subject->_class_name == "CConsultation"}}
  	      <td style="text-align: right">{{mb_value object=$acte field=montant_base       }}</td>
  	      <td style="text-align: right">{{mb_value object=$acte field=montant_depassement}}</td>
  	      <td style="text-align: right">{{mb_value object=$acte field=_montant_facture   }}</td>
        {{/if}}
	    </tr> 
	    {{/foreach}}
	  {{/foreach}}
	{{/foreach}}
</table>