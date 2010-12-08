{{unique_id var=uniq_ditto}}
{{assign var="one_date" value="1"}}

<table class="tbl" style="width: 40%;">
  <tbody>
    {{foreach from=$csteByTime item=_cstes_dates}}
      <tr>
        <th></th>
        {{foreach from=$_cstes_dates item=_cstes key=_date_time_cste}}
          {{if $one_date == 1}}
            <th style="text-align: center;">{{mb_ditto name="datetime$uniq_ditto" value=$_date_time_cste|date_format:$conf.datetime}}</th>
          {{/if}}
          {{assign var="one_date" value="0"}}
          </tr>
          {{foreach from=$_cstes item=_cste key=_cste_name}}
            <tr>
              <th style="text-align: right;">{{tr}}CConstantesMedicales-{{$_cste_name}}{{/tr}}</th>
              <td style="text-align: center;">{{$_cste}}</td>
            </tr>
          {{/foreach}}
        {{/foreach}}
    {{/foreach}}
  </tbody>
</table>
