{{* $Id:$ *}}

{{*
 * @package Mediboard
 * @subpackage ssr
 * @version $Revision: 7948 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<table id="{{$plateau->_guid}}" class="main" style="border-spacing: 4px; border-collapse: separate; width: auto;">
  <tr>
	  {{foreach from=$plateau->_ref_techniciens item=_technicien}}
      <td style="width: 180px;">
			{{mb_include template=inc_repartition_technicien technicien_id=$_technicien->_id}}
			</td>
		{{foreachelse}}
	    <td style="width: 180px;" class="text"><em>{{tr}}CPlateauTechnique-back-techniciens.empty{{/tr}}</em></td>
	  {{/foreach}}
  </tr>	
</table>
