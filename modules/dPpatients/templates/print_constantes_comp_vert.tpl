<table style="width: 50%; font-size: small">
  <tr><th colspan="2" class="title">Constantes</th></tr>
  <tr>
    <th>{{tr}}CConstantesMedicales-datetime-court{{/tr}}</th>
    <td style="text-align: right">{{$datetime|date_format:$dPconfig.datetime}}</td>
  </tr>
  {{foreach from="CConstantesMedicales"|static:"list_constantes" key=_constante item=_params}}
	  <tr>
	    <th>{{tr}}CConstantesMedicales-{{$_constante}}-court{{/tr}}</th>
	    {{foreach from=$csteByTime key=_time item=_cste_time}}
	       {{foreach name="constante_time" from=$_cste_time item=_constante_medicale key=_const_key}}
	         {{if $_const_key == $_constante}}
	           <td style="text-align: right">{{$_constante_medicale}}</td>
	         {{/if}}
	       {{/foreach}}
	    {{/foreach}}
	  </tr>
	{{/foreach}}
</table>