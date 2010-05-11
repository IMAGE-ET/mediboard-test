{{* $Id: $ *}}

{{*
 * @package Mediboard
 * @subpackage dPurgences
 * @version $Revision: 8582 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<td class="warning" colspan="{{$colspan}}">
  <strong>Mutation à {{$rpu->_ref_sejour->sortie|date_format:$dPconfig.time}}</strong> <br/>
  <span onmouseover="ObjectTooltip.createEx(this, 'CSejour-{{$rpu->mutation_sejour_id}}')">
    {{mb_include module=dPplanningOp template=inc_vw_numdos num_dossier=$rpu->_ref_sejour_mutation->_num_dossier}}
  </span> 
</td>
