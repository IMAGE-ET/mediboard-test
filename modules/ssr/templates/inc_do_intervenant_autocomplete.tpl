{{* $Id: $ *}}

{{*
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<ul>
  {{foreach from=$intervenants item=_intervenant}}
    <li>
      <span style="display: none" class="values">
        <span class="executant_id">{{$_intervenant->user_id}}</span>
        <span class="code_intervenant_cdarr">{{$_intervenant->code_intervenant_cdarr}}</span>
        <span class="_executant">{{$_intervenant}}</span>
      </span>
      {{$_intervenant->_view|emphasize:$needle}}
      <br />
      <small class="opacity-70">
        {{$_intervenant->_ref_code_intervenant_cdarr}}
      </small>
    </li>
   {{foreachelse}}
    <li style="text-align: left;"><span class="informal">Aucun résultat</span></li>
  {{/foreach}}
</ul>