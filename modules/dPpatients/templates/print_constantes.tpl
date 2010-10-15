{{unique_id var=uniq_ditto}}
<table class="tbl" style="width: 100%; font-size: small">
  <tr>
    <th colspan="11" class="title">Constantes</th>
  </tr>
  {{foreach from=$csteByTime item=_cstes_dates}}
    <tr>
      <th style="width: 9%;">{{tr}}CConstantesMedicales-datetime-court{{/tr}}</th>
        {{foreach from=$_cstes_dates item=_cstes}}
          {{foreach from=$_cstes item=_cste key=_cste_name}}
            <th style="width: 9%;">{{tr}}CConstantesMedicales-{{$_cste_name}}-court{{/tr}}</th>
          {{/foreach}}
        {{/foreach}}
    </tr>
    <tr>
      {{foreach from=$_cstes_dates item=_cstes key=_date_time_cste}}
        <td>{{mb_ditto name="datetime$uniq_ditto" value=$_date_time_cste|date_format:$dPconfig.datetime}}</td>
        {{foreach from=$_cstes item=_cste}}
          <td style="text-align: right">{{$_cste}}</td>
        {{/foreach}}
      {{/foreach}}
    </tr>
  {{foreachelse}}
  <th>{{tr}}CConstantesMedicales.none{{/tr}}</th>
  {{/foreach}}
</table>
