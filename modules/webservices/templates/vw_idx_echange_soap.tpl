{{* $Id: vw_idx_echange_hprim.tpl 6287 2009-05-13 15:37:54Z lryo $ *}}

{{*
 * @package Mediboard
 * @subpackage webservices
 * @version $Revision: 6287 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script type="text/javascript">

</script>

<table class="main">
  {{if !$echange_soap->_id}}
  
  <tr>
    <td class="halfPane" rowspan="3">
      <table class="tbl">
        <tr>
          <th class="title" colspan="14">ECHANGES SOAP</th>
        </tr>
        <tr>
          <th></th>
          <th>{{mb_title object=$echange_soap field="echange_soap_id"}}</th>
          <th>{{mb_title object=$echange_soap field="date_echange"}}</th>
          <th>{{mb_title object=$echange_soap field="emetteur"}}</th>
          <th>{{mb_title object=$echange_soap field="destinataire"}}</th>
          <th>{{mb_title object=$echange_soap field="type"}}</th>
          <th>{{mb_title object=$echange_soap field="web_service_name"}}</th>
          <th>{{mb_title object=$echange_soap field="function_name"}}</th>
          <th>{{mb_title object=$echange_soap field="input"}}</th>
          <th>{{mb_title object=$echange_soap field="output"}}</th>
        </tr>
        {{foreach from=$listEchangeSoap item=curr_echange_soap}}
          <tbody id="echange_{{$curr_echange_soap->_id}}">
            {{include file="inc_echange_soap.tpl" object=$curr_echange_soap}}
          </tbody>
        {{foreachelse}}
          <tr>
            <td colspan="14">
              {{tr}}CEchangeHprim.none{{/tr}}
            </td>
          </tr>
        {{/foreach}}
      </table>
    </td>
  </tr>
  {{else}}
  <tr>
    <td class="halfPane" rowspan="3">
      <table class="form">
        <tr>
          <th class="title" colspan="2">
            {{mb_value object=$echange_soap field="function_name"}}
          </th>
        </tr>
        <tr>
          <th class="category">{{mb_title object=$echange_soap field="input"}}</th>
          <th class="category">{{mb_title object=$echange_soap field="output"}}</th>
        </tr>
        <tr>
          <td style="width: 50%">
            {{mb_value object=$echange_soap field="input" export=true}}
          </td>
          <td>
            {{mb_value object=$echange_soap field="output" export=true}}
          </td>
        </tr>
      </table>
    </td>
  </tr>
  {{/if}}
</table>