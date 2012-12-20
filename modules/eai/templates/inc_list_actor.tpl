{{*
 * View list actor EAI
 *  
 * @category EAI
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
*}}

<table class="form">
  <tr>
    <th class="category">{{mb_label object=$actor field="nom"}}</th>
    <th class="category">{{mb_label object=$actor field="libelle"}}</th>
    <th class="category">{{mb_label object=$actor field="group_id"}}</th>
    <th class="category">{{mb_label object=$actor field="actif"}}</th>
    <th class="category">{{tr}}Actions{{/tr}}</th>
  </tr>

  <tr>
    <td>
      <a href="?m=eai&tab=vw_idx_interop_actors#interop_actor_guid={{$actor->_guid}}" target="_blank">
       {{$actor->nom}}
     </a>
    </td>
    <td>{{mb_value object=$actor field="libelle"}}</td>
    <td>{{mb_value object=$actor field="group_id"}}</td>
    <td>{{mb_value object=$actor field="actif"}}</td>                  
    <td>
      <form name="editActor" action="?m={{$m}}" method="post" onsubmit="return onSubmitFormAjax(this, { onComplete : function() {
        Domain.refreshListIncrementerActor('{{$domain->_id}}'); Domain.refreshListDomains(); }});">
        <input type="hidden" name="m" value="eai" />
        <input type="hidden" name="dosql" value="do_domain_actor_aed" />
        <input type="hidden" name="actor_guid" value="{{$actor->_guid}}" />
        <input type="hidden" name="domain_id" value="{{$domain->_id}}" />
        <input type="hidden" name="disassociated" value="0" />
        
        <button class="cancel notext" type="button" onclick="$V(this.form.disassociated, 1); this.form.onsubmit()">
          {{tr}}CDomain-actor-disassociated{{/tr}}
        </button>
      </form>
    </td>
  </tr>
</table>