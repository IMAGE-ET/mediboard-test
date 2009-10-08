{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage system
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<table class="tbl">
  {{foreach from=$logs item="log"}}
  <tr style="text-align: center">
    <td>{{mb_ditto name=user value=$log->_ref_user->_view}}</td>
    <td>{{mb_value object=$log field=date format=relative}}</td>
  </tr>
  {{foreachelse}}
  <tr>
    <td>{{tr}}CUserLog.none{{/tr}}</td>
  </tr>
  {{/foreach}}
</table>