{{*
  * add edit sectorisation rule
  *  
  * @category PlanningOp
  * @package  Mediboard
  * @author   SARL OpenXtrem <dev@openxtrem.com>
  * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
  * @version  SVN: $Id:$ 
  * @link     http://www.mediboard.org
*}}




<form name="editRegleSectorisation" method="post">
  {{mb_key object=$rule}}
    <input type="hidden" name="dosql" value="do_sejour_sectorisation_aed" />
    <input type="hidden" name="m" value="{{$m}}" />
    <input type="hidden" name="tab" value="vw_sectorisations" />
  <table class="form tbl">
    {{if $clone == true && !$rule->_id}}
    <tr>
      <th colspan="2"><div class="small-warning">{{tr}}CRegleSectorisation-msg-duplicate-rule{{/tr}}</div></th>
    </tr>
    {{/if}}
    <tr>
      <th class="title" colspan="2">{{tr}}CRegleSectorisation-service_id{{/tr}}</th>
    </tr>
    <tr>
      <th>{{mb_label object=$rule field=service_id}}</th>
      <td>
        <select name="service_id">
          {{foreach from=$services item=_service}}
            <option value="{{$_service->_id}}" {{if $_service->_id == $rule->service_id}}selected="selected" {{/if}}>{{$_service}}</option>
          {{/foreach}}
        </select>

      </td>
    </tr>
    <tr>
      <th class="title" colspan="2">{{tr}}CRegleSectorisation-criteras{{/tr}}</th>
    </tr>

    <tr>
      <th>{{mb_label object=$rule field=group_id}}</th>
      <td>
        <select name="group_id">
          {{foreach from=$groups item=_group}}
            <option value="{{$_group->_id}}" {{if $_group->_id == $rule->group_id}}selected="selected" {{/if}}>{{$_group}}</option>
          {{/foreach}}
        </select>
      </td>
    </tr>

    <tr>
      <th>{{mb_label object=$rule field=function_id}}</th>
      <td>
        <select name="function_id">
        <option value="">{{tr}}CRegleSectorisation-whatever{{/tr}}</option>
        {{foreach from=$functions item=_function}}
          <option value="{{$_function->_id}}" {{if $_function->_id == $rule->function_id}}selected="selected" {{/if}}>
            {{$_function->_view}}
          </option>
        {{/foreach}}
        </select>
      </td>
    </tr>

    <tr>
      <th>
      {{mb_label object=$rule field="praticien_id"}}
      </th>
      <td>
        <select name="praticien_id">
          <option value="">{{tr}}CMediusers.all{{/tr}}</option>
        {{mb_include module=mediusers template=inc_options_mediuser list=$users selected=$rule->praticien_id}}
        </select>
      </td>
    </tr>

    <tr>
      <th>{{mb_label object=$rule field=duree_min}}</th>
      <td>{{mb_field object=$rule field=duree_min }} {{tr}}days{{/tr}}</td>
    </tr>

    <tr>
      <th>{{mb_label object=$rule field=duree_max}}</th>
      <td>{{mb_field object=$rule field=duree_max}} {{tr}}days{{/tr}}</td>
    </tr>

    <tr>
      <th>{{mb_label object=$rule field=date_min}}</th>
      <td>{{mb_field object=$rule field=date_min form="editRegleSectorisation" register="true"}}</td>
    </tr>

    <tr>
      <th>{{mb_label object=$rule field=date_max}}</th>
      <td>{{mb_field object=$rule field=date_max form="editRegleSectorisation" register="true"}}</td>
    </tr>

    <tr>
      <th>{{mb_label object=$rule field=type_admission}}</th>
      <td>{{mb_field object=$rule field=type_admission emptyLabel="CRegleSectorisation-whatever"}}</td>
    </tr>

    <tr>
      <th>{{mb_label object=$rule field=type_pec}}</th>
      <td>{{mb_field object=$rule field=type_pec emptyLabel="CRegleSectorisation-whatever"}}</td>
    </tr>

    <tr>
      <td colspan="2" class="button">
        <button class="submit" type="submit">{{tr}}Save{{/tr}}</button>
      {{if $rule->_id}}
        <button class="trash" type="button" onclick="confirmDeletion(this.form,{objName:'{{$rule->_view|smarty:nodefaults|JSAttribute}}'})">{{tr}}Delete{{/tr}}</button>
      {{/if}}
      </td>
    </tr>
  </table>
</form>