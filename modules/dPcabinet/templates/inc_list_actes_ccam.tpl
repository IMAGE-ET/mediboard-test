{{mb_include_script module="dPccam" script="code_ccam" ajax="true"}}
   
<!-- S'il y a deja des actes codés, affichage seulement des actes codes -->
{{if $subject->_ref_actes_ccam && $vue == "complete"}}
<table class="tbl">
	<tr>
	  <th class="title" colspan="10">
	  	{{tr}}Codage des actes CCAM{{/tr}}
	  </th>
	</tr> 
	{{foreach from=$subject->_ref_actes_ccam item=_acte name=acte}}
	{{if $smarty.foreach.acte.first}}
	<tr>
	  <th>{{mb_title object=$_acte field=code_acte         }}</th>
	  <th>{{mb_title object=$_acte field=code_activite     }}</th>
	  <th>{{mb_title object=$_acte field=code_phase        }}</th>
	  <th>{{mb_title object=$_acte field=modificateurs     }}</th>
	  <th>{{mb_title object=$_acte field=code_association  }}</th>
	  <th>{{mb_title object=$_acte field=executant_id      }}</th>
	  <th>{{mb_title object=$_acte field=execution         }}</th>
    {{if @$extra == "comment"}}
	  <th>{{mb_title object=$_acte field=commentaire       }}</th>
	  {{/if}}
    {{if @$extra == "tarif"}}
    <th>{{mb_title object=$_acte field=montant_base       }}</th>
    <th>{{mb_title object=$_acte field=montant_depassement}}</th>
    <th>{{mb_title object=$_acte field=_montant_facture   }}</th>
	  {{/if}}
	</tr>
	{{/if}}
	
	{{assign var=code value=$_acte->code_acte}}
	{{if $_acte->code_activite != ""}}
	  {{assign var=codeActivite value=$_acte->code_activite}}
	  {{assign var=code value="$code-$codeActivite"}}
	  {{if $_acte->code_phase != ""}}
	    {{assign var=codePhase value=$_acte->code_phase}}
	    {{assign var=code value="$code-$codePhase"}}
	  {{/if}}
	{{/if}}
	<tr>
	  <td> 
	    <a class="button search" href="#CodeCCAM-show-{{$code}}" onclick="CodeCCAM.show('{{$code}}','{{$subject->_class_name}}')">
	      {{mb_value object=$_acte field=code_acte}}
	    </a>
	  </td>  

	  <td class="text">{{mb_value object=$_acte field=code_activite}}</td>
	  <td class="text">{{mb_value object=$_acte field=code_phase}}</td>
	  <td class="text">{{mb_value object=$_acte field=modificateurs}}</td>
	  <td class="text">{{mb_value object=$_acte field=code_association}}</td>

	  <td class="text"> 
		  {{mb_include module=mediusers template=inc_vw_mediuser mediuser=$_acte->_ref_executant}}
	  </td>    

	  <td class="text">{{mb_value object=$_acte field=execution}}</td>

    {{if @$extra == "comment"}}
	  <td class="text">{{mb_value object=$_acte field=commentaire}}</td>	  
	  {{/if}}

    {{if @$extra == "tarif"}}
    <td class="text" style="text-align: right">{{mb_value object=$_acte field=montant_base}}</td>
    <td  class="text" style="text-align: right">{{mb_value object=$_acte field=montant_depassement}}</td>
    <td  class="text" style="text-align: right">{{mb_value object=$_acte field=_montant_facture}}</td>
	  {{/if}}
	  
	</tr>
	
	{{/foreach}}
</table>	
<!-- Sinon, affichage des actes prevus -->

{{elseif $subject->_ext_codes_ccam}}
<table class="tbl">
	<tr>
	  <th class="category" colspan="2">{{tr}}CActeCCAM{{/tr}}</th>
	</tr>
	{{foreach from=$subject->_ext_codes_ccam item=_code}}
	<tr>
	  <td>
	    <a class="button search" href="#CodeCCAM-Show-{{$_code->code}}" onclick="CodeCCAM.show('{{$_code->code}}', '{{$subject->_class_name}}')">
	    	{{$_code->code}}
			</a>
	
		</td>
		<td class="text" style="max-width:22em">
	    {{$_code->libelleLong}}

	    {{if @$view_tarif}}
	    <!-- Tarifs des activités (phase 0) -->
	    <em>(
			{{foreach from=$_code->activites item=_actvite name=tarif}}
    	Activité {{$_actvite->numero}} : {{$_actvite->phases.0->tarif|currency}}
    	{{if !$smarty.foreach.tarif.last}}&mdash;{{/if}}
    	{{/foreach}}
	    )</em>
      {{/if}}
	  </td>
	</tr>
	{{/foreach}}
</table>
{{/if}}
