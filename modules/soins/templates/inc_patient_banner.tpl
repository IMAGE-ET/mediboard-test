{{*
 * $Id$
 *  
 * @package    Mediboard
 * @subpackage soins
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 * @link       http://www.mediboard.org
*}}

{{mb_default var=mode_pharma value=0}}
{{mb_default var=mode_protocole value=0}}
{{mb_default var=operation_id value=0}}
{{mb_default var=rpu value=null}}

{{assign var=prescription value=$sejour->_ref_prescription_sejour}}
{{assign var=dossier_medical value=$patient->_ref_dossier_medical}}
{{assign var=antecedents value=$dossier_medical->_ref_antecedents_by_type}}
{{assign var=sejour_id value=$sejour->_id}}
{{assign var=conf_preselect_prat value=$conf.dPprescription.CPrescription.preselection_praticien_auto}}
{{assign var=is_executant_prescription value=CAppUI::$user->isExecutantPrescription()}}

<table class="form">
  <tr>
    <th class="title text" style="text-align: left; border: none;">
      {{mb_include module=system template=inc_object_notes object=$patient}}
      <a href="?m=dPpatients&tab=vw_full_patients&patient_id={{$patient->_id}}">
        {{mb_include module="patients" template=inc_vw_photo_identite mode="read" size=32}}
      </a>
    </th>
    <th class="title text" colspan="2" style="border: none;">
      <form name="actionPat" action="?" method="get">
        <input type="hidden" name="m" value="patients" />
        <input type="hidden" name="tab" value="vw_idx_patients" />
        <input type="hidden" name="patient_id" value="{{$patient->_id}}" />
        <h2 style="color: #fff; font-weight: bold; text-align: center;">
          <span style="font-size: 0.7em;" onmouseover="ObjectTooltip.createEx(this, '{{$patient->_guid}}')">
            {{$patient->_view}}
          </span>
          -
          <span style="font-size: 0.7em;" onmouseover="ObjectTooltip.createEx(this, '{{$sejour->_guid}}')">
            {{if $rpu}}
              {{$sejour->_shortview|replace:"Du":"RPU du"}}
            {{else}}
              {{$sejour->_shortview|replace:"Du":"Séjour du"}} <br /> {{$sejour->_ref_curr_affectation->_ref_lit}}
            {{/if}}
          </span>
          {{include file="../../soins/templates/inc_vw_antecedent_allergie.tpl" nodebug=true}}

          {{if $dossier_medical->_id && $dossier_medical->_count_allergies}}
            <script type="text/javascript">
              ObjectTooltip.modes.allergies = {
                module: "patients",
                action: "ajax_vw_allergies",
                sClass: "tooltip"
              };
            </script>
            <img src="images/icons/warning.png" onmouseover="ObjectTooltip.createEx(this, '{{$patient->_guid}}', 'allergies');" />
          {{/if}}

          {{if $prescription->_jour_op}}
            <br/>
            {{foreach from=$prescription->_jour_op item=_info_jour_op}}
              (<span onmouseover="ObjectTooltip.createEx(this, '{{$_info_jour_op.operation_guid}}');">J{{$_info_jour_op.jour_op}}</span>)
            {{/foreach}}
          {{/if}}
        </h2>
      </form>
    </th>
    <th class="title text" style="text-align: right; border: none;">
      {{mb_include module=system template=inc_object_idsante400 object=$patient}}
      {{mb_include module=system template=inc_object_history object=$patient}}

      <a href="#print-{{$patient->_guid}}" onclick="Patient.print('{{$patient->_id}}')">
        <img src="images/icons/print.png" alt="imprimer" title="Imprimer la fiche patient" />
      </a>

      {{if $can->edit}}
        <a href="#edit-{{$patient->_guid}}" onclick="Patient.edit('{{$patient->_id}}')">
          <img src="images/icons/edit.png" alt="modifier" title="Modifier le patient" />
        </a>
      {{/if}}

      {{if $app->user_prefs.vCardExport}}
        <a href="#export-{{$patient->_guid}}" onclick="Patient.exportVcard('{{$patient->_id}}')">
          <img src="images/icons/vcard.png" alt="export" title="Exporter le patient" />
        </a>
      {{/if}}

      {{if $patient->date_lecture_vitale}}
        <div>
          <img src="images/icons/carte_vitale.png" title="{{tr}}CPatient-date-lecture-vitale{{/tr}} : {{mb_value object=$patient field="date_lecture_vitale" format=relative}}" />
        </div>
      {{/if}}

      <br/>
      <label {{if $app->user_prefs.options_ordo_checked}}style="display: none;"{{/if}}>
        <input type="checkbox" id="options_print" name="options_print"
               {{if $app->user_prefs.options_ordo_checked}}checked="checked"{{/if}}/>
        <span style="font-size: xx-small;">Options</span>
      </label>

      <button type="button" class="print" onclick="
        if (!$('options_print').checked) {
        Prescription.printPrescription('{{$prescription->_id}}', {
        dci: {{if $prescription->type!="sejour" && $app->user_prefs.dci_checked_externe}}1{{else}}0{{/if}},
        globale: 0,
        in_progress: {{if $prescription->type!="sejour"}}1{{else}}0{{/if}},
        showOptions: 0,
        praticien_sortie_id: '{{$prescription->_current_praticien_id}}'
        });
        }
        else {
        Prescription.printOrdonnance('{{$prescription->_id}}', '', '{{$operation_id}}');
        }
        ">
        Ordonnance
      </button>

      <div id="select_praticien" style="display: none;">
        {{if !$is_praticien && !$mode_protocole && ($operation_id || $can->admin || $mode_pharma || ($is_executant_prescription && !$conf.dPprescription.CPrescription.role_propre))}}
          <form name="selPraticienLine" action="?" method="get">
            <select style="font-size: 0.8em; width: 15em;" name="praticien_id" onchange="{{if !$conf_preselect_prat}}toggleAddLine();{{/if}} Prescription.changePraticienMed(this.value); {{if !$mode_pharma}}Prescription.changePraticienElt(this.value);{{/if}} if($('protocole_prat_name')) { $('protocole_prat_name').update('Dr '+this.options[this.selectedIndex].text); }">
              {{if !$conf_preselect_prat}}
                <option value="">&mdash; Choix du prescripteur</option>
              {{/if}}
              <optgroup label="Responsables">
                <option class="mediuser" style="border-color: #{{$prescription->_ref_current_praticien->_ref_function->color}};"
                        value="{{$prescription->_ref_current_praticien->_id}}"
                        {{if $conf_preselect_prat && $prescription->_ref_current_praticien->_id == $prescription->_current_praticien_id}}selected="selected"{{/if}}>{{$prescription->_ref_current_praticien->_view}}</option>
                {{if @$operation->_ref_anesth->_id}}
                  <option  class="mediuser" style="border-color: #{{$operation->_ref_anesth->_ref_function->color}};"
                           value="{{$operation->_ref_anesth->_id}}"
                           {{if $conf_preselect_prat && $operation->_ref_anesth->_id == $prescription->_current_praticien_id}}selected="selected"{{/if}}>{{$operation->_ref_anesth->_view}}</option>
                {{/if}}
              </optgroup>
              <optgroup label="Tous les praticiens">
                {{foreach from=$listPrats item=_praticien}}
                  <option class="mediuser"
                          style="border-color: #{{$_praticien->_ref_function->color}};"
                          value="{{$_praticien->_id}}"
                          {{if $conf_preselect_prat && $_praticien->_id == $prescription->_current_praticien_id}}selected="selected"{{/if}}>{{$_praticien->_view}}
                  </option>
                {{/foreach}}
              </optgroup>
            </select>
          </form>
        {{/if}}
      </div>
    </th>
  </tr>
  {{mb_include module=soins template=inc_infos_patients_soins add_class_poids=1 add_class_taille=1}}
</table>