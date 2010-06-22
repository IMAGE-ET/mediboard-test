<table class="main tbl">
  <tr><th colspan="100" class="title">Constantes</th></tr>
  <tr>
    <th>{{tr}}CConstantesMedicales-datetime-court{{/tr}}</th>
    {{assign var="i" value=1}}
    {{foreach name="constante" from="CConstantesMedicales"|static:"list_constantes" key=_constante item=_params}}
      {{if $i == 11}}
        {{assign var=save_constante value=$smarty.foreach.constante.index}}
        {{php}}break{{/php}}
      {{/if}}
      <th>{{tr}}CConstantesMedicales-{{$_constante}}-court{{/tr}}</th>
      {{assign var="i" value=$i+1}}
    {{/foreach}}
  </tr>

  {{foreach name="constante_b" from=$csteByTime key=_time item=_cste_time}}
    <tr>
      <td style="text-align: center;">{{mb_ditto name="datetime" value=$_time|date_format:$dPconfig.datetime}}</td>
      {{assign var="i" value=1}}
      {{foreach name="constante_time" from=$_cste_time item=_constante_medicale}}
        {{if $i == 11}}
          {{assign var="save_cste_time" value=$smarty.foreach.constante_time.index}}
          {{php}}break{{/php}}
        {{/if}}
        <td style="text-align: right">{{$_constante_medicale}}</td>
        {{assign var="i" value=$i+1}}
      {{/foreach}}
    </tr>
  {{/foreach}}
  
  <tr>
    <th>{{tr}}CConstantesMedicales-datetime-court{{/tr}}</th>
    {{foreach name="constante2" from="CConstantesMedicales"|static:"list_constantes" key=_constante item=_params}}
      {{if $smarty.foreach.constante2.index > $save_constante }}
        <th>{{tr}}CConstantesMedicales-{{$_constante}}-court{{/tr}}</th>
      {{/if}}
    {{/foreach}}
  </tr>
  
  {{assign var=reset_mb_ditto value=true}}
  {{foreach name="constante_b2" from=$csteByTime key=_time item=_cste_time}}
      <td style="text-align: center;">{{mb_ditto name="datetime" value=$_time|date_format:$dPconfig.datetime reset=$reset_mb_ditto}}</td>
      {{foreach name="constante_time" from=$_cste_time item=_constante_medicale}}
        {{if $smarty.foreach.constante_time.index > $save_cste_time}}
          <td style="text-align: right">{{$_constante_medicale}}</td>
        {{/if}}
      {{/foreach}}
    </tr>
  {{assign var=reset_mb_ditto value=false}}
  {{/foreach}}
  
</table>