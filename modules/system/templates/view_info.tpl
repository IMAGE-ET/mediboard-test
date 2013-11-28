{{* $Id: ajax_errors.tpl 7494 2009-12-02 16:34:38Z phenxdesign $ *}}

{{*
 * @package Mediboard
 * @subpackage system
 * @version $Revision: 7494 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<table class="tbl">
  <tr>
    <th>{{tr}}Parameter{{/tr}}</th>
    <th>{{tr}}Property{{/tr}}</th>
  </tr>

  {{foreach from=$props key=_name item=_prop}}
  <tr>
    <td>{{$_name}}</td>
    <td>{{$_prop}}</td>
  </tr>
  {{foreachelse}}
  <tr>
    <td class="empty">{{tr}}CView-parameters-none{{/tr}}</td>
  </tr>
  {{/foreach}}
</table>