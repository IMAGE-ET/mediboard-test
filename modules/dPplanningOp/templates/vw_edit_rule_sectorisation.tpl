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
  <table class="form">
    <tr>
      <th class="title" colspan="2">{{tr}}Service{{/tr}}</th>
    </tr>
    <tr>
      <th>{{mb_label object=$rule field=service_id}}</th>
      <td>
        <select name="service_id">
          {{foreach from=$services item=_service}}
            <option value="{{$_service->_id}}">{{$_service}}</option>
          {{/foreach}}
        </select>

      </td>
    </tr>
    <tr>
      <th class="title" colspan="2">{{tr}}Criteras{{/tr}}</th>
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
      <td>{{mb_field object=$rule field=function_id}}</td>
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
      <td>{{mb_field object=$rule field=duree_min }}</td>
    </tr>

    <tr>
      <th>{{mb_label object=$rule field=duree_max}}</th>
      <td>{{mb_field object=$rule field=duree_max}}</td>
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
        <button class="trash" type="button" onclick="confirmDeletion(this.form,{typeName:'la règle',objName:'{{$rule->_view|smarty:nodefaults|JSAttribute}}', ajax :true})">{{tr}}Delete{{/tr}}</button>
      {{/if}}
      </td>
    </tr>
  </table>
</form>