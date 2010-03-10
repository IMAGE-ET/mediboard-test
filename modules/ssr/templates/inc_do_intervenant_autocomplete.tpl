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
      <span style="display: none">{{$_intervenant->user_id}}</span>
      <span style="display: none">{{$_intervenant->code_intervenant_cdarr}}</span>
			<span style="display: none">{{$_intervenant->_view}}</span>
      {{$_intervenant->_view|emphasize:$needle}}
      <br />
      <small style="opacity: 0.7">
			 	{{$_intervenant->_ref_code_intervenant_cdarr->_view}}
			</small>
    </li>
   {{foreachelse}}
    <li style="text-align: left;"><span class="informal">Aucun résultat</span></li>
  {{/foreach}}
</ul>