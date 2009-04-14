{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage system
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<table class="tbl">

<tr>
{{foreach from=$objects item=_object}}
  <th class="title" style="width: 50%">
  	{{$_object->_view}}
  </th>
{{/foreach}}
</tr>

{{foreach from=$object->_back key=backName item=backObjects}}
{{assign var=backSpec value=$object->_backSpecs.$backName}}
<tr>
	{{foreach from=$objects item=_object}}
  <th class="category">
    {{tr}}{{$backSpec->_initiator}}-back-{{$backName}}{{/tr}}
		{{if $_object->_count.$backName}}
    ( x {{$_object->_count.$backName}})
		{{/if}}
  </th>
	{{/foreach}}
</tr>

<tr>
{{foreach from=$objects item=_object}}
  <td>
	{{foreach from=$_object->_back.$backName item=backRef}}
    <span class="tooltip-trigger" onmouseover="ObjectTooltip.createEx(this, '{{$backRef->_guid}}')">
      {{$backRef->_view}}
    </span>
    <br />
	{{foreachelse}}
	<em>Aucun objet</em>
	{{/foreach}}
	{{if $_object->_count.$backName != count($_object->_back.$backName)}}
	...
	{{/if}}
  </td>
{{/foreach}}
</tr>

{{/foreach}}

</table>