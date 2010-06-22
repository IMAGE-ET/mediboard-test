<table class="main tbl">
  <tr><th colspan="100" class="title">Constantes</th></tr>
  <tr>
    <th>{{tr}}CConstantesMedicales-datetime-court{{/tr}}</th>
    {{assign var="i" value=1}}
    {{foreach from="CConstantesMedicales"|static:"list_constantes" key=_constante item=_params}}
    {{if $i > 10}}
      </tr><tr>
      {{assign var="i" value=1}}
    {{/if}}
    <th>{{tr}}CConstantesMedicales-{{$_constante}}-court{{/tr}}</th>
    {{assign var="i" value=$i+1}}
    {{/foreach}}
  </tr>
  {{foreach from=$csteByTime key=_time item=_cste_time}}
  <tr>
    <td style="text-align: center;">{{mb_ditto name="datetime" value=$_time|date_format:$dPconfig.datetime}}</td>
    {{foreach from=$_cste_time item=_constante_medicale}}
    <td style="text-align: right">{{$_constante_medicale}}</td>
    {{/foreach}}
  </tr>  
  {{/foreach}}
</table>