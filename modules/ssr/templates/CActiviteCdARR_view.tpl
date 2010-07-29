{{* $Id: $ *}}

{{*
 * @package Mediboard
 * @subpackage ssr
 * @version $Revision:  $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{include file=CMbObject_view.tpl}}

{{assign var=activite value=$object}}

<table class="tooltip tbl">
  <tr>
  	<td>
			<strong>{{mb_label object=$activite field=type}}</strong>:
      {{$activite->_ref_type_activite}}
		</td>
	</tr>
</table>
