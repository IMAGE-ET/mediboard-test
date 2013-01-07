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
    <th rowspan="2" style="width: 15%;"></th>
    <th colspan="2">
      Consultation
    </th>
    <th colspan="2">
      Séjour
    </th>
    <th colspan="2">
      Intervention
    </th>
    <th colspan="2">
      Totaux
    </th>
  </tr>
  <tr>
    {{foreach from=1|range:4 item=i}}
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
      {{foreach from=$_cotation item=_total_by_class}}
        {{foreach from=$_total_by_class item=_total}}
          <td style="text-align: right;">
            <div>{{$_total.sect1|currency}}</div>
            <div>{{$_total.sect2|currency}}</div>
          </td>
        {{/foreach}}
      {{/foreach}}
      <td style="text-align: right;">
        <div>{{$total_by_prat.$_chir_id.ccam.sec1|currency}}</div>
        <div>{{$total_by_prat.$_chir_id.ccam.sec2|currency}}</div>
      </td>
      <td style="text-align: right;">
        <div>{{$total_by_prat.$_chir_id.ngap.sec1|currency}}</div>
        <div>{{$total_by_prat.$_chir_id.ngap.sec2|currency}}</div>
      </td>
    </tr>
  {{/foreach}}
  <tr>
    <td style="text-align: right">
      <strong>Total</strong>
    </td>
    {{foreach from=$total_by_class item=_total_by_code}}
      {{foreach from=$_total_by_code item=_total}}
        <td style="text-align: right">
          <div>{{$_total.sec1|currency}}</div>
          <div>{{$_total.sec2|currency}}</div>
        </td>
      {{/foreach}}
    {{/foreach}}
    <td style="text-align: right">
      <div>{{$total.ccam.sec1|currency}}</div>
      <div>{{$total.ccam.sec2|currency}}</div>
    </td>
    <td style="text-align: right">
      <div>{{$total.ngap.sec1|currency}}</div>
      <div>{{$total.ngap.sec2|currency}}</div>
    </td>
  </tr>
</table>