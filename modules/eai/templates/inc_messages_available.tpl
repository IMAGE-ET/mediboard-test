{{*
 * Messages available
 *  
 * @category EAI
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
*}}

<fieldset>
  <legend>{{tr}}{{$message}}{{/tr}} <span class="compact">({{tr}}{{$message}}-desc{{/tr}})</span></legend>
  
  <table class="tbl">
  {{foreach from=$messages_supported item=_message_supported}}
    {{assign var=event      value=$_message_supported->_event}}

    <tr>
      <th class="section narrow">
        {{tr}}{{$_message_supported->message}}{{/tr}}
      </th>
      <td class="text compact">
        {{tr}}{{$_message_supported->message}}-desc{{/tr}}
      </td>
    </tr>
  {{/foreach}}
  </table>

</fieldset>
