{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage system
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<tbody>
	{{foreach from=$objects item=_object}}
	<tr>
		<td>
			<a href="#1"> 
        <span onmouseover="ObjectTooltip.createEx(this, '{{$_object->_guid}}');">
				  {{$_object}}
				</span>
			</a>
		</td>
		{{foreach from=$columns item=_column}}
		  <td>
		  	{{mb_value object=$_object field=$_column}}
		  </td>
		{{/foreach}}
	</tr>
	{{foreachelse}}
	<tr>
		{{math assign=colspan equation="x+1" x=$columns|@count}}
		
		<td class="empty" colspan="{{$colspan}}">
			{{tr}}{{$tag->object_class}}.none{{/tr}}
		</td>
	</tr>
	{{/foreach}}
</tbody>