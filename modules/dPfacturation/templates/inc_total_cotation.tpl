<h1 style="cursor: pointer; text-align: center;">
  <a onclick="window.print()">Total du {{$debut|date_format:$conf.date}} au {{$fin|date_format:$conf.date}}</a>
</h1>

<table class="tbl">
  <tr>
    <th rowspan="2" colspan="2" style="width: 20%;">
      Praticien
    </th>
    {{foreach from=$object_classes item=classe}}
      <th colspan="2" style="width: 20%;">
        {{tr}}{{$classe}}{{/tr}}
      </th>
    {{/foreach}}
    <th colspan="2" rowspan="2" style="width: 20%;">
      Totaux
    </th>
  </tr>
  <tr>
    {{foreach from=$object_classes item=classe}}
      {{foreach from=$tab_actes item=acte key=nom}}
      <th>
        {{$nom|upper}}
      </th>
      {{/foreach}}
    {{/foreach}}
  </tr>
  {{foreach from=$cotation item=_cotation key=_chir_id}}
    <tr>
      <td>
        {{mb_include module=mediusers template=inc_vw_mediuser mediuser=$prats.$_chir_id}}
      </td>
      <td class="narrow">
        {{if $conf.ref_pays == 1}}
          Sec 1<br />
          Sec 2
        {{/if}}
      </td>
      {{foreach from=$_cotation item=_total_by_class}}
        {{foreach from=$_total_by_class item=_total}}
          <td style="text-align: right;">
            <div>{{$_total.sect1|currency}}</div>
            {{if $conf.ref_pays == 1}}
              <div>{{$_total.sect2|currency}}</div>
            {{/if}}
          </td>
        {{/foreach}}
      {{/foreach}}
      <td style="text-align: right;">
        <strong>{{$total_by_prat.$_chir_id|currency}}</strong>
      </td>
      <td style="text-align: right;">
        {{math equation=(x/y)*100 x=$total_by_prat.$_chir_id y=$total assign=percent_prat}}
        <strong>
          {{$percent_prat|round:2}}%
        </strong>
      </td>
    </tr>
  {{/foreach}}
  <tbody class="hoverable">
    <tr>
      <td  rowspan="2"  colspan="2" style="text-align: right;">
        <strong>Total</strong>
      </td>
      {{foreach from=$total_by_class item=_total_by_code}}
        {{foreach from=$_total_by_code item=_total}}
          <td style="text-align: right;">
            <strong>{{$_total|currency}}</strong>
          </td>
        {{/foreach}}
      {{/foreach}}
      <td rowspan="2" style="text-align: right;">
        <strong>{{$total|currency}}</strong>
      </td>
      <td rowspan="2" style="text-align: right;">
        <strong>100%</strong>
      </td>
    </tr>
    <tr>
      {{foreach from=$total_by_class item=_total_by_code}}
        <td colspan="2" style="text-align: center;">
          {{if $total}}
            {{if $conf.ref_pays == 1}}
              {{math equation=x+y x=$_total_by_code.ccam y=$_total_by_code.ngap assign=sub_total}}
            {{else}}
              {{math equation=x+y x=$_total_by_code.tarmed y=$_total_by_code.caisse assign=sub_total}}
            {{/if}}
            {{math equation=(x/y)*100 x=$sub_total y=$total assign=percent_total}}
            <strong>
              {{$sub_total|currency}}
              {{if $percent_total}}
                ({{$percent_total|round:2}}%)
              {{/if}}
            </strong>
          {{/if}}
        </td>
      {{/foreach}}
    </tr>
  </tbody>
</table>