<table class="main tbl">
  <tr><th colspan="100" class="title">Constantes</th></tr>
  <tr>
    <th>{{tr}}CConstantesMedicales-datetime-court{{/tr}}</th>
    {{foreach from="CConstantesMedicales"|static:"list_constantes" key=_constante item=_params}}
    <th>{{tr}}CConstantesMedicales-{{$_constante}}-court{{/tr}}</th>
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