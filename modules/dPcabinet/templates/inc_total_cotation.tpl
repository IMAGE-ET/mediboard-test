{{*
  * 
  *  
  * @category dPcabinet
  * @package  Mediboard
  * @author   SARL OpenXtrem <dev@openxtrem.com>
  * @license  OXOL, see http://www.mediboard.org/public/OXOL
  * @version  SVN: $Id:$ 
  * @link     http://www.mediboard.org
*}}

<h1 style="cursor: pointer; text-align: center;">
  <a onclick="window.print()">Total du {{$debut|date_format:$conf.date}} au {{$fin|date_format:$conf.date}}</a>
</h1>

<table class="tbl">
  <tr>
    <th rowspan="2" colspan="2" style="width: 18%;">Praticien</th>
    <th colspan="2">
      Consultation
    </th>
    <th colspan="2">
      Séjour
    </th>
    <th colspan="2">
      Intervention
    </th>
    <th colspan="2" rowspan="2">
      Totaux
    </th>
  </tr>
  <tr>
    {{foreach from=1|range:3 item=i}}
      <th>
        CCAM
      </th>
      <th>
        NGAP
      </th>
    {{/foreach}}
  </tr>
  {{foreach from=$cotation item=_cotation key=_chir_id}}
    <tr>
      <td>
        {{mb_include module=mediusers template=inc_vw_mediuser mediuser=$prats.$_chir_id}}
      </td>
      <td class="narrow">
        Sec 1<br />
        Sec 2
      </td>
      {{foreach from=$_cotation item=_total_by_class}}
        {{foreach from=$_total_by_class item=_total}}
          <td style="text-align: right;">
            <div>{{$_total.sect1|currency}}</div>
            <div>{{$_total.sect2|currency}}</div>
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
  <tr>
    <td style="text-align: right;" colspan="2">
      <strong>Total</strong>
    </td>
    {{foreach from=$total_by_class item=_total_by_code}}
      {{foreach from=$_total_by_code item=_total}}
        <td style="text-align: right;">
          <strong>{{$_total|currency}}</strong>
        </td>
      {{/foreach}}
    {{/foreach}}
    <td style="text-align: right;">
      <strong>{{$total|currency}}</strong>
    </td>
    <td style="text-align: right;">
      <strong>100%</strong>
    </td>
  </tr>
  <tr>
    <td colspan="2"></td>
    {{foreach from=$total_by_class item=_total_by_code}}
      <td colspan="2" style="text-align: right;">
        {{if $total}}
          {{math equation=x+y x=$_total_by_code.ccam y=$_total_by_code.ngap assign=sub_total}}
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
    <td colspan="2"></td>
  </tr>
</table>