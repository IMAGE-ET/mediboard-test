{{* $Id:$ *}}

{{*
 * @package Mediboard
 * @subpackage ssr
 * @version $Revision: 7948 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<table class="tbl">
  <tr>
    <th class="title" colspan="0">{{$plateau}}</th>
  </tr>
  <tr>
	  {{foreach from=$plateau->_ref_techniciens item=_technicien}}
	    <th style="width: 200px;">
	      {{$_technicien}}
			</th>
		{{foreachelse}}
	    <td><em>{{tr}}CPlateauTechnique-back-techniciens.empty{{/tr}}</em></th>
	  {{/foreach}}
  </tr>

  <tr>
    {{foreach from=$plateau->_ref_techniciens item=_technicien}}
		  <td id="repartition-{{$_technicien->_guid}}">
		    <em>{{tr}}Aucun patient affecté{{/tr}}</em>
		  </td>
	  {{/foreach}}
  </td>
	
</table>
