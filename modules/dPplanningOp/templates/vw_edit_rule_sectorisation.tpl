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

<script>
  Main.add(function() {
    var form = getForm('editRegleSectorisation');
    form.elements.age_min.addSpinner({min: 0, step: 10});
    form.elements.age_max.addSpinner({min: 0, step: 10});
  });

  changePrio = function(more) {
    var oform = getForm('editRegleSectorisation');
    var old_value = $V(oform.priority) || 0;
    if (more) {
      $V(oform.priority, parseInt(old_value) + 1);
    }
    else {
      if (old_value >= 1) {
        $V(oform.priority, old_value - 1);
      }
    }
  }
</script>

<form name="editRegleSectorisation" method="post" action="">
  {{mb_key object=$rule}}
    <input type="hidden" name="dosql" value="do_sejour_sectorisation_aed" />
    <input type="hidden" name="m" value="{{$m}}" />
    <input type="hidden" name="tab" value="vw_sectorisations" />
  <table class="form">
    {{if $clone == true && !$rule->_id}}
      <tr>
        <th colspan="2"><div class="small-warning">{{tr}}CRegleSectorisation-msg-duplicate-rule{{/tr}}</div></th>
      </tr>
    {{/if}}

    <tr>
      <th class="title" colspan="2">{{tr}}CRegleSectorisation-priority{{/tr}}</th>
    </tr>
    <tr>
      <th>{{mb_label object=$rule field=priority}}</th>
      <td>
        <button type="button" class="remove notext" onclick="changePrio();"></button>
        {{mb_field object=$rule field=priority}}
        <button type="button" class="add notext" onclick="changePrio(1)"></button>
      </td>
    </tr>

    <tr>
      <th class="title" colspan="2">{{tr}}CRegleSectorisation-service_id{{/tr}}</th>
    </tr>
    <tr>
      <th>{{mb_label object=$rule field=service_id}}</th>
      <td>
        <select name="service_id" class="notNull" style="width:15em;">
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
        <select name="group_id" class="notNull" style="width:15em;">
          {{foreach from=$groups item=_group}}
            <option value="{{$_group->_id}}" {{if $_group->_id == $rule->group_id}}selected="selected" {{/if}}>{{$_group}}</option>
          {{/foreach}}
        </select>
      </td>
    </tr>

    <tr>
      <th>{{mb_label object=$rule field=function_id}}</th>
      <td>
        <select name="function_id"  style="width:15em;">
          <option value="">{{tr}}CRegleSectorisation-function_id.all{{/tr}}</option>
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
        <select name="praticien_id" style="width:15em;">
          <option value="">{{tr}}CMediusers.all{{/tr}}</option>
        {{mb_include module=mediusers template=inc_options_mediuser list=$users selected=$rule->praticien_id}}
        </select>
      </td>
    </tr>

    <tr>
      <th>{{mb_label object=$rule field=duree_min}}</th>
      <td>{{mb_field object=$rule field=duree_min }} {{tr}}night{{/tr}}(s)</td>
    </tr>

    <tr>
      <th>{{mb_label object=$rule field=duree_max}}</th>
      <td>{{mb_field object=$rule field=duree_max}} {{tr}}night{{/tr}}(s)</td>
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
      <td>{{mb_field object=$rule field=type_admission emptyLabel="CRegleSectorisation-type_admission.all"}}</td>
    </tr>

    <tr>
      <th>{{mb_label object=$rule field=type_pec}}</th>
      <td>{{mb_field object=$rule field=type_pec emptyLabel="CRegleSectorisation-type_pec.all"}}</td>
    </tr>

    <tr>
      <th class="category" colspan="2">{{tr}}CPatient{{/tr}}</th>
    </tr>

    <tr>
      <th>{{mb_label object=$rule field=age_min}}</th>
      <td>{{mb_field object=$rule field=age_min}}</td>
    </tr>

    <tr>
      <th>{{mb_label object=$rule field=age_max}}</th>
      <td>{{mb_field object=$rule field=age_max}}</td>
    </tr>

    <tr>
      <th>{{mb_label object=$rule field=handicap}}</th>
      <td>{{mb_field object=$rule field=handicap typeEnum='select' emptyLabel='CRegleSectorisation-handicap.any'}}</td>
    </tr>

    <tr>
      <td colspan="2" class="button">
        <button class="submit" type="submit">{{tr}}Save{{/tr}}</button>
      {{if $rule->_id}}
        <button class="trash" type="button" onclick="confirmDeletion(this.form,{objName:'{{$rule->_view|smarty:nodefaults|JSAttribute}}'})">
          {{tr}}Delete{{/tr}}
        </button>
      {{/if}}
      </td>
    </tr>
  </table>
</form>