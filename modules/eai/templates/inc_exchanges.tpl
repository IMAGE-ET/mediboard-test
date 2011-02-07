{{* $Id:$ *}}

{{*
 * @package Mediboard
 * @subpackage webservices
 * @version $Revision: 6341 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{if $total_exchanges != 0}}
  {{mb_include module=system template=inc_pagination total=$total_exchanges current=$page change_page='ExchangeDataFormat.changePage' jumper='10'}}
{{/if}}
<table class="tbl">
  <tr>
    <th class="title" colspan="20">{{tr}}{{$exchange->_class_name}}{{/tr}} - {{$type}}</th>
  </tr>
  <tr>
    <th colspan="3"></th>
    <th>{{mb_title object=$exchange field="object_class"}}</th>
    <th>{{mb_title object=$exchange field="object_class"}}</th>
    <th>{{mb_title object=$exchange field="object_id"}}</th>
    <th>{{mb_title object=$exchange field="id_permanent"}}</th>
    <th>{{mb_title object=$exchange field="date_production"}}</th>
    <th>{{mb_title object=$exchange field="destinataire_id"}}</th>
    <th>{{mb_title object=$exchange field="emetteur_id"}}</th>
    <th>{{mb_title object=$exchange field="sous_type"}}</th>
    <th>{{mb_title object=$exchange field="date_echange"}}</th>
    <th>{{mb_title object=$exchange field="statut_acquittement"}}</th>
    <th>{{mb_title object=$exchange field="_observations"}}</th>
    <th colspan="2">{{mb_title object=$exchange field="message_valide"}}</th>
    <th colspan="2">{{mb_title object=$exchange field="acquittement_valide"}}</th>
  </tr>
  {{foreach from=$exchanges item=_exchange}}
    <tbody id="exchange_{{$_exchange->_guid}}">
      {{mb_include template=inc_exchange object=$_exchange}}
    </tbody>
  {{foreachelse}}
    <tr>
      <td colspan="20">
        {{tr}}{{$exchange->_class_name}}.none{{/tr}}
      </td>
    </tr>
  {{/foreach}}
</table>