{{* $Id: $ *}}

{{*
 * @package Mediboard
 * @subpackage system
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<a class="button new" href="?m={{$m}}&amp;tab={{$tab}}&amp;http_redirection_id=0">
  Créer une redirection
</a>

<table class="tbl">

<tr>
  <th colspan="10">
    {{$http_redirections|@count}}
  	{{tr}}CHttpRedirection{{/tr}}
  	{{tr}}found{{/tr}}
  </th>
</tr>

<tr>
  <th>{{mb_title class=CHttpRedirection field=priority}}</th>
  <th>{{mb_title class=CHttpRedirection field=from}}</th>
  <th>{{mb_title class=CHttpRedirection field=to}}</th>
</tr>

{{foreach from=$http_redirections item=_redirection}}
<tr {{if $_redirection->_id == $http_redirection->_id}}class="selected"{{/if}}>
  {{assign var="http_redirection_id" value=$_redirection->_id}}
  {{assign var="href" value="?m=$m&tab=$tab&http_redirection_id=$http_redirection_id"}}
  <td>
    {{mb_value object=$_redirection field=priority}}
  </td>
  <td>
    <a href="{{$href}}">{{mb_value object=$_redirection field=from}}</a>
  </td>
  <td>
    <a href="{{$href}}">{{mb_value object=$_redirection field=to}}</a>
  </td>
</tr>
{{/foreach}}
  
</table>
