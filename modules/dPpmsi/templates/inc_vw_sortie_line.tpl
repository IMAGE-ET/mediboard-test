{{*
 * $Id$
 *  
 * @category ${Module}
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @link     http://www.mediboard.org*}}

{{mb_script module=soins  script=plan_soins ajax=true}}

<td class="text CPatient-view" colspan="2">
  {{if $canPlanningOp->read}}
    <div style="float: right;">
      {{if "web100T"|module_active}}
        {{mb_include module=web100T template=inc_button_iframe}}
      {{/if}}

      <button type="button" class="print notext" onclick="Admissions.showDocs('{{$_sejour->_id}}')"></button>

      {{foreach from=$_sejour->_ref_operations item=curr_op}}
        <a class="action" title="Imprimer la DHE de l'intervention" href="#1" onclick="Admissions.printDHE('operation_id', {{$curr_op->_id}}); return false;">
          <img src="images/icons/print.png" />
        </a>
        {{foreachelse}}
        <a class="action" title="Imprimer la DHE du séjour" href="#1" onclick="Admissions.printDHE('sejour_id', {{$_sejour->_id}}); return false;">
          <img src="images/icons/print.png" />
        </a>
      {{/foreach}}

      <a class="action" title="Modifier le séjour" href="#editDHE"
         onclick="Sejour.editModal({{$_sejour->_id}}, reloadSorties); return false;">
        <img src="images/icons/planning.png" />
      </a>

      {{mb_include module=system template=inc_object_notes object=$_sejour}}
    </div>
  {{/if}}

  <input type="checkbox" name="print_doc" value="{{$_sejour->_id}}"/>

  {{mb_include module=planningOp template=inc_vw_numdos nda_obj=$_sejour _show_numdoss_modal=1}}

  <span onmouseover="ObjectTooltip.createEx(this, '{{$_sejour->_ref_patient->_guid}}');">
    {{$_sejour->_ref_patient->_view}}
  </span>
</td>
<td class="text">
  {{mb_value object=$_sejour field=entree date=$date}}
</td>
<td>
  <span onmouseover="ObjectTooltip.createEx(this, '{{$_sejour->_guid}}');">
    {{if ($_sejour->sortie_prevue < $date_min) || ($_sejour->sortie_prevue > $date_max)}}
      {{$_sejour->sortie_prevue|date_format:$conf.datetime}}
    {{else}}
      {{$_sejour->sortie_prevue|date_format:$conf.time}}
    {{/if}}
  </span>
  {{if $_sejour->confirme}}
    <img src="images/icons/tick.png" title="Sortie confirmée par le praticien" />
  {{/if}}
</td>
<td class="text button">
  <form name="facturer-{{$_sejour->_guid}}" action="?" method="post"
        onsubmit="return onSubmitFormAjax(this, reloadSortieLine.curry('{{$_sejour->_id}}'));">
    {{mb_key   object=$_sejour}}
    {{mb_class object=$_sejour}}

    <input type="hidden" name="facture" value="1"/>

    <button {{if $_sejour->facture}}disabled{{/if}} type="submit" class="tick">Facturer</button>
  </form>
  <button {{if !$_sejour->_ref_prescription_sejour->_id}}disabled{{/if}} type="button" class="print"
          onclick="PlanSoins.printAdministrations('{{$_sejour->_ref_prescription_sejour->_id}}')">Facturation</button>
  <!-- <button type="button" class="print" disabled>CHOP</button>
  <button type="button" class="print" disabled>Tarmed</button> -->
</td>