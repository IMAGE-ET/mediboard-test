{{* $Id: inc_vw_admission_line.tpl 15057 2012-03-29 08:07:50Z mytto $ *}}

{{*
 * @package Mediboard
 * @subpackage dPadmissions
 * @version $Revision: 15057 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{assign var="patient" value=$_sejour->_ref_patient}}

<td colspan="2" class="text">
  {{if $canPlanningOp->read}}
    <div style="float: right;">
      {{if "web100T"|module_active}}
        {{mb_include module=web100T template=inc_button_iframe}}
      {{/if}}

      <button type="button" class="print notext" onclick="Admissions.showDocs('{{$_sejour->_id}}')"></button>

      {{if $conf.dPadmissions.show_deficience}}
        {{mb_include module=patients template=inc_vw_antecedents type=deficience callback=reloadPresent force_show=true}}
      {{/if}}
      
      {{foreach from=$_sejour->_ref_operations item=_op}}
      <a class="action" title="Imprimer la DHE de l'intervention" href="#printDHE" onclick="Admissions.printDHE('operation_id', {{$_op->_id}}); return false;">
        <img src="images/icons/print.png" />
      </a>
      {{foreachelse}}
      <a class="action" title="Imprimer la DHE du séjour" href="#printDHE" onclick="Admissions.printDHE('sejour_id', {{$_sejour->_id}}); return false;">
        <img src="images/icons/print.png" />
      </a>
      {{/foreach}}

      <a class="action" title="Modifier le séjour" href="#editDHE"
         onclick="Sejour.editModal({{$_sejour->_id}}, reloadPresent); return false;">
        <img src="images/icons/planning.png" />
      </a>
      
      {{mb_include module=system template=inc_object_notes object=$_sejour}}
    </div>
  {{/if}}



  {{if $patient->_ref_IPP}}
    <form name="editIPP{{$patient->_id}}" method="post">
      <input type="hidden" class="notNull" name="id400" value="{{$patient->_ref_IPP->id400}}" />
      <input type="hidden" class="notNull" name="object_id" value="{{$patient->_id}}" />
      <input type="hidden" class="notNull" name="object_class" value="CPatient" />
    </form>
  {{/if}}

  {{if $_sejour->_ref_NDA}}
    <form name="editNumdos{{$_sejour->_id}}" method="post">
      <input type="hidden" class="notNull" name="id400" value="{{$_sejour->_ref_NDA->id400}}"/>
      <input type="hidden" class="notNull" name="object_id" value="{{$_sejour->_id}}" />
      <input type="hidden" class="notNull" name="object_class" value="CSejour" />
    </form>
  {{/if}}

  {{if "dPsante400"|module_active}}
    {{mb_include module=dPsante400 template=inc_manually_ipp_nda sejour=$_sejour patient=$patient callback=reloadPresent}}
  {{/if}}

  <input type="checkbox" name="print_doc" value="{{$_sejour->_id}}"/>
  {{mb_include module=planningOp template=inc_vw_numdos nda_obj=$_sejour _show_numdoss_modal=1}}
  <span class="CPatient-view" onmouseover="ObjectTooltip.createEx(this, '{{$patient->_guid}}');">
    {{$patient}}
  </span>
</td>

<td class="text">
  {{mb_include module=mediusers template=inc_vw_mediuser mediuser=$_sejour->_ref_praticien}}
</td>

<td>
  <span {{if $_sejour->entree|date_format:"%Y-%m-%d" == $date}}style="color: #070;"{{/if}}
    onmouseover="ObjectTooltip.createEx(this, '{{$_sejour->_guid}}');">
    {{$_sejour->entree|date_format:$conf.datetime}}
  </span>
  <div style="position: relative;">
  <div class="sejour-bar" title="arrivée il y a {{$_sejour->_entree_relative}}j et départ prévu dans {{$_sejour->_sortie_relative}}j ">
    <div style="width: {{if $_sejour->_duree}}{{math equation='100*(-entree / (duree))' entree=$_sejour->_entree_relative duree=$_sejour->_duree format='%.2f'}}{{else}}100{{/if}}%;"></div>
  </div>
  </div>
</td>

<td>
  <span {{if $_sejour->sortie|date_format:"%Y-%m-%d" == $date}}style="color: #070;"{{/if}}>
    {{$_sejour->sortie|date_format:$conf.datetime}}
  </span>
</td>

<td class="text">
  {{if !($_sejour->type == 'exte') && !($_sejour->type == 'consult') && $_sejour->annule != 1}}
    {{mb_include template=inc_form_prestations sejour=$_sejour edit=$canAdmissions->edit}}
    {{mb_include module=hospi template=inc_placement_sejour sejour=$_sejour}}
  {{/if}}  
</td>