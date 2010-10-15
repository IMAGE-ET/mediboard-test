{{unique_id var=uniq_ditto}}
{{assign var="one_date" value="1"}}

<table class="tbl" style="width: 50%; font-size: small">
  <tr><th colspan="2" class="title">Constantes</th></tr>
  {{foreach from=$csteByTime item=_cstes_dates}}
    <tr>
      <th>{{tr}}CConstantesMedicales-datetime-court{{/tr}}</th>
      {{foreach from=$_cstes_dates item=_cstes key=_date_time_cste}}
        {{if $one_date == 1}}
          <td style="text-align: right;">{{mb_ditto name="datetime$uniq_ditto" value=$_date_time_cste|date_format:$dPconfig.datetime}}</td>
        {{/if}}
        {{assign var="one_date" value="0"}}
        </tr>
        {{foreach from=$_cstes item=_cste key=_cste_name}}
          <tr>
            <th>{{$_cste_name}}</td>
            <td style="text-align: right">{{$_cste}}</td>
          </tr>
        {{/foreach}}
      {{/foreach}}
  {{/foreach}}
</table>
