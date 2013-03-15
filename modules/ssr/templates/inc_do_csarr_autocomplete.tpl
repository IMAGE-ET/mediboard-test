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
      <div class="text" style="width: 300px;">
        <strong>{{$_activite->code|emphasize:$needle}}</strong>
        {{$_activite->libelle|emphasize:$needle}}
      </div>
      <div class="text compact" style="width: 300px;">
        {{$_activite->hierarchie|emphasize:$needle}}:
        {{$_activite->_ref_hierarchie->libelle|emphasize:$needle}}
      </div>
    </li>
   {{foreachelse}}
    <li style="text-align: left;">
      <span class="informal">Aucun résultat</span>
    </li>
  {{/foreach}}
</ul>