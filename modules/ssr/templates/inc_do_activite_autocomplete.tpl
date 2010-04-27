{{* $Id: $ *}}

{{*
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<ul>
  {{foreach from=$activites item=_activite}}
    <li>
      <span style="display: none" class="value">{{$_activite->code}}</span>
      {{$_activite->_view|emphasize:$needle}}
      <br />
      <small style="opacity: 0.7">
			 	{{$_activite->_ref_type_activite->_view|emphasize:$needle}}
			</small>
    </li>
   {{foreachelse}}
    <li style="text-align: left;"><span class="informal">Aucun résultat</span></li>
  {{/foreach}}
</ul>