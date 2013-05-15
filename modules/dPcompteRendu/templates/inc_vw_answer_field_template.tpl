{{*
  * Answer for the search of field template
  *  
  * @category CompteRendu
  * @package  Mediboard
  * @author   SARL OpenXtrem <dev@openxtrem.com>
  * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
  * @version  SVN: $Id:$ 
  * @link     http://www.mediboard.org
*}}

{{foreach from=$results key=key item=_field}}
  <tr>
    <td>
      <a href="#" ondblclick="insertField(this);" data-fieldHtml="{{$_field.fieldHTML}}">
        {{$key}}
      </a>
    </td>
  </tr>
{{foreachelse}}
  <tr><td class="empty">{{tr}}No result{{/tr}}</td></tr>
{{/foreach}}