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
  <th class="category" colspan="3">UFs</th>
</tr>
{{foreach from=$affectations_uf item=_affectation_uf}}
  {{assign var=uf value=$_affectation_uf->_ref_uf}}
   <tr>
      <td>{{mb_value object=$_affectation_uf field=uf_id}}</td>
      <td><strong>{{mb_value object=$uf field=type}}</strong></td>
      <td class="empty">
        {{if $uf->date_debut && $uf->date_fin}}
          (Du {{mb_value object=$uf field=date_debut}}
          au {{mb_value object=$uf field=date_fin}})
        {{elseif $uf->date_debut}}
          (Jusqu'au {{mb_value object=$uf field=date_fin}})
        {{elseif $uf->date_fin}}
          (A partir du {{mb_value object=$uf field=date_fin}})
        {{/if}}
      </td>
    </tr>
  {{/foreach}}
</table>