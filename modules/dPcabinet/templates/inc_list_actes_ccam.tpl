<script type="text/javascript">     

viewCode = function(code, object_class){
  var url = new Url;
  url.setModuleAction("dPccam", "vw_full_code");
  url.addParam("codeacte", code);
  url.addParam("object_class", object_class);
  url.addParam("hideSelect", "1");
  url.popup(700, 550, "Code CCAM");
}

</script>
   
<!-- S'il y a deja des actes codés, affichage seulement des actes codes -->
{{if $subject->_ref_actes_ccam && $vue == "complete"}}
	<tr>
	  <th class="category" colspan="10">
	  	{{tr}}{{$subject->_class_name}}-_ref_actes_ccam{{/tr}}
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
	    <a href="#" onclick="viewCode('{{$code}}','CSejour')">
	      {{mb_value object=$_acte field=code_acte}}
	    </a>
	  </td>  

	  <td>{{mb_value object=$_acte field=code_activite}}</td>
	  <td>{{mb_value object=$_acte field=code_phase}}</td>
	  <td>{{mb_value object=$_acte field=modificateurs}}</td>
	  <td>{{mb_value object=$_acte field=code_association}}</td>

	  <td> 
	    <div class="mediuser" style="border-color: #{{$_acte->_ref_executant->_ref_function->color}};">
        {{$_acte->_ref_executant}}
	    </div>
	  </td>    

	  <td>{{mb_value object=$_acte field=execution}}</td>

    {{if @$extra == "comment"}}
	  <td>{{mb_value object=$_acte field=commentaire}}</td>	  
	  {{/if}}

    {{if @$extra == "tarif"}}
    <td style="text-align: right">{{mb_value object=$_acte field=montant_base}}</td>
    <td style="text-align: right">{{mb_value object=$_acte field=montant_depassement}}</td>
    <td style="text-align: right">{{mb_value object=$_acte field=_montant_facture}}</td>
	  {{/if}}
	  
	</tr>
	
	{{/foreach}}
	
<!-- Sinon, affichage des actes prevus -->
{{elseif $subject->_ext_codes_ccam}}
	<tr>
	  <th class="category" colspan="2">{{tr}}{{$subject->_class_name}}-_ext_codes_ccam{{/tr}}</th>
	</tr>
	{{foreach from=$subject->_ext_codes_ccam item=_code}}
	<tr>
	  <td class="text" colspan="2">
	    <strong>{{$_code->code}}</strong> :
	    {{$_code->libelleLong}}

	    {{if @$view_tarif}}
	    <!-- Tarifs des activités (phase 0) -->
	    <em>(
			{{foreach from=$_code->activites item=_actvite name=tarif}}
    	Activité {{$_actvite->numero}} : {{$_actvite->phases.0->tarif}}{{$dPconfig.currency_symbol}}
    	{{if !$smarty.foreach.tarif.last}}&mdash;{{/if}}
    	{{/foreach}}
	    {{/if}}
	    )</em>
	  </td>
	</tr>
	{{/foreach}}
{{/if}}
