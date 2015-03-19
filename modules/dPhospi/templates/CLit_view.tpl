{{*
 * $Id$
 *  
 * @category Hospi
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
*}}

{{if $object->_id && !$object->_can->read}}
  <div class="small-info">
    {{tr}}{{$object->_class}}{{/tr}} : {{tr}}access-forbidden{{/tr}}
  </div>
  {{mb_return}}
{{/if}}

<table class="tbl">
  <tr>
    <th class="title text" colspan="2">
      {{mb_include module=system template=inc_object_idsante400}}
      {{mb_include module=system template=inc_object_history}}
      {{mb_include module=system template=inc_object_notes}}

      {{tr}}CLit{{/tr}} {{$object}}
    </th>
  </tr>
  <tr>
    <th>{{tr}}CService{{/tr}}</th>
    <td><span onmouseover="ObjectTooltip.createEx(this, '{{$object->_ref_service->_guid}}');">{{$object->_ref_service}}</span></td>
  </tr>

  <tr>
    <th>{{tr}}CChambre{{/tr}}</th>
    <td><span onmouseover="ObjectTooltip.createEx(this, '{{$object->_ref_chambre->_guid}}');">{{$object->_ref_chambre}}</span></td>
  </tr>

  {{if $object->_ref_affectations|@count}}
    <tr>
      <th>Affectations du jour dans ce lit</th>
      <td>
        {{foreach from=$object->_ref_affectations item=_affectation}}
          <p><span onmouseover="ObjectTooltip.createEx(this, '{{$_affectation->_guid}}');">{{$_affectation}}</span></p>
        {{/foreach}}
      </td>
    </tr>
  {{/if}}

  {{if @$modules.hotellerie->mod_active}}
    <tr>
      <th>Dernier nettoyage</th>
      <td {{if !$object->_ref_last_cleanup->_id}}class="empty"{{/if}}>
        {{if !$object->_ref_last_cleanup->_id}}
          {{tr}}CBedCleanup.none{{/tr}}
        {{else}}
          <span onmouseover="ObjectTooltip.createEx(this, '{{$object->_ref_last_cleanup->_guid}}');">
            {{$object->_ref_last_cleanup}}
          </span>
        {{/if}}
      </td>
    </tr>

    <tr>
      <th>Nettoyage en cours</th>
      <td {{if !$object->_ref_current_cleanup->_id}}class="empty"{{/if}}>
        {{if !$object->_ref_current_cleanup->_id && @$modules.hotellerie->_can->read}}
          <form method="post" name="add_cleanup" onsubmit="return onSubmitFormAjax(this, (function() {this.up('.tooltip').remove(); }).bind(this));">
            <input type="hidden" name="@class" value="CBedCleanup" >
            <input type="hidden" name="@id" value="" >
            <input type="hidden" name="lit_id" value="{{$object->_id}}">
            <button class="new">Demande de nettoyage</button>
          </form>
        {{else}}
        <span onmouseover="ObjectTooltip.createEx(this, '{{$object->_ref_current_cleanup->_guid}}');">
          {{$object->_ref_current_cleanup}}
        </span>
        {{/if}}
      </td>
    </tr>
  {{/if}}
</table>