{{* $Id:$ *}}

{{*
 * @package Mediboard
 * @subpackage ssr
 * @version $Revision: 7948 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<table class="main" style="border-spacing: 4px; border-collapse: separate; width: auto;">
  <tr>
    <th class="title" colspan="0">{{$plateau}}</th>
  </tr>
  <tr>
	  {{foreach from=$plateau->_ref_techniciens item=_technicien}}
      <td style="width: 180px;">
			{{mb_include template=inc_repartition_kine kine_id=$_technicien->kine_id}}
			</td>
		{{foreachelse}}
	    <td><em>{{tr}}CPlateauTechnique-back-techniciens.empty{{/tr}}</em></td>
	  {{/foreach}}
  </tr>	
</table>
