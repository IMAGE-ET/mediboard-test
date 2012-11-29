{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPccam
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<ul>
  {{foreach from=$codes item=_code}}
    <li>
      <div class="compact" style="float: right;">
        {{foreach from=$_code->activites item=_activite}}
          {{foreach from=$_activite->phases item=_phase}}
             {{if $_phase->tarif}}
             <span title="activité {{$_activite->numero}}, phase {{$_phase->phase}}">
               {{$_phase->tarif|currency}}
             </span>
             {{/if}}
          {{/foreach}}
        {{/foreach}}
      </div>
      <strong class="code">{{$_code->code}}</strong>
      <br />
      <small>{{$_code->libelleLong|smarty:nodefaults|emphasize:$keywords}}</small>
    </li>
  {{/foreach}}
</ul>