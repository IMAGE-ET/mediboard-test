{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPurgences
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<td>
  {{$rpu->$debut|date_format:$dPconfig.time}}
</td>
<td id="{{$fin}}-{{$rpu->_id}}">
  {{mb_include module=dPurgences template=inc_vw_fin_attente}}
</td>