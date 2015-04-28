{{*
  * View of sectorisation rules
  *  
  * @category PlanningOp
  * @package  Mediboard
  * @author   SARL OpenXtrem <dev@openxtrem.com>
  * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
  * @version  SVN: $Id:$ 
  * @link     http://www.mediboard.org
*}}


<script>
  editSRF = function(id_rule, clone) {
    var url = new Url("planningOp", "ajax_edit_rule_sectorisation");
    url.addParam("rule_id", id_rule);
    url.addParam("clone", clone);
    url.requestModal();
  }
</script>


{{if !$conf.dPplanningOp.CRegleSectorisation.use_sectorisation}}
  <div class="small-warning">
    {{tr}}CRegleSectorisation-msg-not-active{{/tr}}
  </div>
{{/if}}

<button class="new" onclick="editSRF(0)">{{tr}}CRegleSectorisation-title-create{{/tr}}</button>

<form method="get" action="" name="showInactive">
  <input type="hidden" name="m" value="{{$m}}"/>
  <input type="hidden" name="tab" value="vw_sectorisations"/>
  <input type="checkbox" onchange="$V(this.form.inactive, this.checked ? 1 : 0); this.form.submit()" {{if $show_inactive}}checked="checked"{{/if}} name="_show_caduc">
  <label for="showInactive__show_caduc">{{tr}}CRegleSectorisation-show-inactive{{/tr}}</label>
    <input type="hidden" name="inactive" value="{{$show_inactive}}" />
</form>

<table class="tbl">
  <tr>
    <th class="narrow">{{tr}}Actions{{/tr}}</th>
    <th class="narrow">{{tr}}CRegleSectorisation-priority{{/tr}}</th>
    <th>{{tr}}CFunctions{{/tr}}</th>
    <th>{{tr}}CMediusers{{/tr}}</th>
    <th>{{tr}}CRegleSectorisation-duree_min{{/tr}}</th>
    <th>{{tr}}CRegleSectorisation-duree_max{{/tr}}</th>
    <th>{{tr}}CRegleSectorisation-date_min{{/tr}}</th>
    <th>{{tr}}CRegleSectorisation-date_max{{/tr}}</th>
    <th>{{tr}}CRegleSectorisation-type_admission{{/tr}}</th>
    <th>{{tr}}CRegleSectorisation-type_pec{{/tr}}</th>
    <th>{{mb_title class=CRegleSectorisation field=age_min}}</th>
    <th>{{mb_title class=CRegleSectorisation field=age_max}}</th>
    <th>{{mb_title class=CRegleSectorisation field=handicap}}</th>
    <th>Direction</th>
  </tr>

  {{foreach from=$regles item=_regle}}
    <tr {{if $_regle->_inactive}}class="hatching"{{/if}}>
      <td>
        <button class="edit notext" onclick="editSRF('{{$_regle->_id}}', 0)">{{tr}}Edit{{/tr}}</button>
        <button class="duplicate notext" onclick="editSRF('{{$_regle->_id}}', 1)">{{tr}}Duplicate{{/tr}}</button>
      </td>

      <td style="text-align: center"><strong>{{mb_value object=$_regle field=priority}}</strong></td>
      <td>{{if $_regle->_ref_function->_id}}{{mb_include module="mediusers" template="inc_vw_function" function=$_regle->_ref_function}}{{/if}}</td>
      <td>{{if $_regle->_ref_praticien->_id}}{{mb_include module="mediusers" template="inc_vw_mediuser" mediuser=$_regle->_ref_praticien}}{{/if}}</td>
      <td>{{if $_regle->duree_min}}{{mb_value object=$_regle field=duree_min}} {{tr}}night{{/tr}}(s){{/if}}</td>
      <td>{{if $_regle->duree_max}}{{mb_value object=$_regle field=duree_max}} {{tr}}night{{/tr}}(s){{/if}}</td>
      <td>{{mb_value object=$_regle field=date_min}}</td>
      <td>{{mb_value object=$_regle field=date_max}}</td>
      <td>{{if $_regle->type_admission}}{{tr}}CSejour._type_admission.{{$_regle->type_admission}}{{/tr}}{{/if}}</td>
      <td>{{if $_regle->type_pec}}{{tr}}CSejour.type_pec.{{$_regle->type_pec}}{{/tr}}{{/if}}</td>
      <td style="text-align: center">{{mb_value object=$_regle field=age_min}}</td>
      <td style="text-align: center">{{mb_value object=$_regle field=age_max}}</td>
      <td style="text-align: center">{{if $_regle->handicap}} {{mb_value object=$_regle field=handicap}} {{/if}}</td>

      <td><strong> >> {{$_regle->_ref_service}}</strong></td>
    </tr>
  {{foreachelse}}
    <tr>
      <td class="empty" colspan="14">{{tr}}CRegleSectorisation.none{{/tr}}</td>
    </tr>
  {{/foreach}}
</table>