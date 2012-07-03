{{* $Id: $ *}}

{{*
 * @package Mediboard
 * @subpackage dPurgences
 * @version $Revision: 8582 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<td class="warning" colspan="{{$colspan}}">
  <strong>Mutation à {{$rpu->_ref_sejour->sortie|date_format:$conf.time}}</strong> <br/>
  <span onmouseover="ObjectTooltip.createEx(this, 'CSejour-{{$rpu->mutation_sejour_id}}')">
    {{mb_include module=planningOp template=inc_vw_numdos nda_obj=$rpu->_ref_sejour_mutation}}
  </span> 
</td>
