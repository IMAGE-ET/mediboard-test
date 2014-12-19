{{*
 * View Exchanges Data Format EAI
 *  
 * @category EAI
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
*}}

<table class="tbl">
  <tr>
    <th class="title" colspan="6">
      <a style="float: right" class="button lookup notext" href="#" onclick="ExchangeDataFormat.viewAll();">
        {{tr}}CExchangeDataFormat-view_all{{/tr}}
      </a>
      {{tr}}CExchangeDataFormat{{/tr}}
    </th>
  </tr>
  <tr>
    <th></th>
    <th>{{tr}}CExchangeDataFormat-_count_exchanges{{/tr}}</th>
    <th>{{tr}}CExchangeDataFormat-_count_msg_invalide{{/tr}}</th>
    <th>{{tr}}CExchangeDataFormat-_count_ack_invalide{{/tr}}</th>
  </tr>
  {{foreach from=$exchanges_classes key=sub_classes item=_child_classes}}
    <tr>
      <th class="section" colspan="6">
        {{tr}}{{$sub_classes}}{{/tr}}
      </th>
    </tr>
    {{foreach from=$_child_classes item=_class}}
    <tr>
      <td>
        <a href="#" onclick="ExchangeDataFormat.refreshExchanges('{{$_class->_class}}', null);" title="Afficher les échanges">
          {{tr}}{{$_class->_class}}{{/tr}}
        </a>
      </td>
      <td>{{$_class->_count_exchanges|integer}}</td>
      <td>{{$_class->_count_msg_invalide|integer}}</td>
      <td>{{$_class->_count_ack_invalide|integer}}</td>
    </tr>
    {{/foreach}}
  {{/foreach}}
</table>