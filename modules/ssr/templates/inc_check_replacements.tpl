{{* $Id: configure.tpl 7993 2010-02-03 16:55:27Z MyttO $ *}}

{{*
 * @package Mediboard
 * @subpackage dPstock
 * @version $Revision: 7993 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}


<table class="tbl">
  <tr>
    <th>{{mb_title class=CReplacement field=sejour_id}}  </th>
    <th>{{mb_title class=CReplacement field=conge_id}}   </th>
    <th>{{mb_title class=CReplacement field=replacer_id}}</th>
  </tr>
	
{{foreach from=$replacements item=_replacement}}
  <tr>
    <td>{{mb_value object=$_replacement field=sejour_id}}  </td>
    <td>{{mb_value object=$_replacement field=conge_id}}   </td>
    <td>{{mb_value object=$_replacement field=replacer_id}}</td>
  </tr>
   
{{foreachelse}}
{{/foreach}}
  
</table>