{{unique_id var=uniq_ditto}}
<table class="tbl" style="width: 100%;">
  <tbody>
    {{foreach from=$csteByTime item=_cstes_dates}}
      <tr>
        <th style="width: 10%;"></th>
          {{foreach from=$_cstes_dates item=_cstes}}
            {{foreach from=$_cstes item=_cste key=_cste_name}}
              <th style="width: 10%;">{{tr}}CConstantesMedicales-{{$_cste_name}}-court{{/tr}}</th>
            {{/foreach}}
          {{/foreach}}
      </tr>
      <tr>
        {{foreach from=$_cstes_dates item=_cstes key=_date_time_cste}}
          <th>{{mb_ditto name="datetime$uniq_ditto" value=$_date_time_cste|date_format:$conf.datetime}}</th>
          {{foreach from=$_cstes item=_cste}}
            <td style="text-align: center;">{{$_cste}}</td>
          {{/foreach}}
        {{/foreach}}
      </tr>
    {{foreachelse}}
      <tr>
        <td>{{tr}}CConstantesMedicales.none{{/tr}}</td>
      </tr>
    {{/foreach}}
  </tbody>
</table>
