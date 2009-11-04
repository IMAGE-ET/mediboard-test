{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<ul>
  {{foreach from=$elements item=element}}
    <li>
      <small style="display: none;">{{$element->_id}}</small>
      <small style="display: none;">{{$element->_ref_category_prescription->chapitre}}</small>
      <strong>{{$element->_ref_category_prescription}}</strong> :
      {{$element->libelle|emphasize:$libelle}}
    </li>
  {{/foreach}}
</ul>