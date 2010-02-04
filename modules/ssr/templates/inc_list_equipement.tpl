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
    <th>{{mb_title class=CEquipement field=nom}}</th>
  </tr>
  {{foreach from=$plateau->_ref_equipements item=_equipement}}
  <tr>
    <td>{{mb_value object=$_equipement field=nom}}</td>
  </tr>   
  {{foreachelse}}
  <tr>
    <td><em>{{tr}}None{{/tr}}</em></td>
  </tr>   
  {{/foreach}}
</table>
