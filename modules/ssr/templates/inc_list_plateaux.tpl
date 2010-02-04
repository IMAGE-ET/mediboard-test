{{* $Id: vw_aed_rpu.tpl 7951 2010-02-01 10:44:08Z lryo $ *}}

{{*
 * @package Mediboard
 * @subpackage ssr
 * @version $Revision: 7951 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 *}}

<table class="tbl">
	<tr>
    <th>{{mb_title class=CPlateauTechnique field=nom}}</th>
    <th>{{tr}}CPlateauTechnique-back-equipements{{/tr}}</th>
    <th>{{tr}}CPlateauTechnique-back-techniciens{{/tr}}</th>
	</tr>
	
	{{foreach from=$plateaux item=_plateau}}
  <tr {{if $_plateau->_id == $plateau->_id}}class="selected"{{/if}}>
    <td>
    	<a href="?m={{$m}}&amp;plateau_id={{$_plateau->_id}}">
        {{mb_value object=$_plateau field=nom}}
    	</a>
		</td>
  </tr>
	{{foreachelse}}
  <tr>
  	<td colspan="10"><em>{{tr}}CPlateauTechnique.none{{/tr}}</em></td>
  </tr>
	{{/foreach}}
	
</table>