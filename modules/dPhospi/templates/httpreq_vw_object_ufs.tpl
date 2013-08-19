{{* $Id: httpreq_vw_object_ufs.tpl $ *}}

{{*
 * @package Mediboard
 * @subpackage dPhospi
 * @version $Revision: 6341 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}
<table class="form"> 
<tr>
  <th class="category">UFs</th>
</tr>
{{foreach from=$affectations_uf item=_affectation_uf}}
   <tr>
      <td>
          {{mb_value object=$_affectation_uf field=uf_id}}
      </td>
    </tr>
  {{/foreach}}
</table>