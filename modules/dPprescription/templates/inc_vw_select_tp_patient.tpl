{{* $Id:  $ *}}

{{*
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<option value="">&mdash; Traitements perso</option>
{{foreach from=$traitements key=line_id item=_traitement}}
<option value="{{$line_id}}">
  {{$_traitement->libelle_abrege}} {{$_traitement->dosage}} ({{$_traitement->forme}})
</option>
{{/foreach}}
