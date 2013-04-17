{{* $Id:$ *}}

{{*
 * @package Mediboard
 * @subpackage system
 * @version $Revision: 7494 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<tr {{if !$isadmin}}style="display:none;"{{/if}}>
  <th>{{mb_label object=$source field="name"}}</th>
  <td><input type="text" readonly="readonly" name="name" value="{{$source->name}}" /></td>
</tr>
<tr {{if !$isadmin}}style="display:none;"{{/if}}>
  <th>{{mb_label object=$source field="role"}}</th>
  <td>{{mb_field object=$source field="role"}}</td>
</tr>
<tr>
  <th>{{mb_label object=$source field="active"}}</th>
  <td>{{mb_field object=$source field="active"}}</td>
</tr>
<tr {{if !$isadmin}}style="display:none;"{{/if}}>
  <th>{{mb_label object=$source field="loggable"}}</th>
  <td>{{mb_field object=$source field="loggable"}}</td>
</tr>
<tr>
  <th>{{mb_label object=$source field="host"}}</th>
  <td>{{mb_field object=$source field="host"}}</td>
</tr>

