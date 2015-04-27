{{* $Id: inc_vw_admissions.tpl 6387 2009-06-03 07:44:06Z phenxdesign $ *}}

{{*
 * @package Mediboard
 * @subpackage dPadmissions
 * @version $Revision: 6387 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{mb_script module=planningOp script=prestations ajax=1}}
{{mb_default var=is_modal value=0}}

<script>

  callbackModal = function() {
    window.parent.Control.Modal.close();
    window.parent.see_consult_without_dhe();
  };

  var callbackDHE = {{if $is_modal}}callbackModal{{else}}Admissions.updateListPreAdmissions{{/if}};

  openDHEModal = function(pat_id) {
    var url = new Url('dPplanningOp','vw_edit_planning');
    url.addParam('pat_id', pat_id);
    url.addParam('operation_id', 0);
    url.addParam('sejour_id',0);
    url.addParam('dialog',1);
    url.modal({width: '95%',height: '95%', onclose: callbackDHE});
    url.modalObject.observe('afterClose', callbackDHE);
  };

  {{if !$is_modal}}
    Prestations.callback = reloadPreAdmission;
    Calendar.regField(getForm("changeDatePreAdmissions").date, null, {noView: true});
  {{/if}}
</script>

<table class="tbl">
  {{if !$is_modal}}
    <tr>
      <th class="title" colspan="9">
        <a href="#" onclick="Admissions.updateListPreAdmissions('{{$hier}}');" style="display: inline"><<<</a>
        {{$date|date_format:$conf.longdate}}
        <form name="changeDatePreAdmissions" action="?" method="get">
          <input type="hidden" name="m" value="{{$m}}" />
          <input type="hidden" name="tab" value="vw_idx_preadmission" />
          <input type="hidden" name="date" class="date" value="{{$date}}" onchange="this.form.submit()" />
        </form>
        <a href="#" onclick="Admissions.updateListPreAdmissions('{{$demain}}');" style="display: inline">>>></a>
        <br />

        <select name="filter" style="float:right;" onchange="Admissions.pre_admission_filter = $V(this); Admissions.updateListPreAdmissions();">
          <option value="">&mdash; Toutes les pré-admissions</option>
          <option value="dhe" {{if $filter == "dhe"}}selected="selected" {{/if}}>Pré-admissions sans intervention prévue</option>
        </select>

        <em style="float: left; font-weight: normal;">
          {{$listConsultations|@count}} pré-admissions ce jour {{if $filter}}sans interventions{{/if}}
        </em>
      </th>
    </tr>
  {{/if}}
  <tr>
    <th colspan="2">Consultation d'anesthésie</th>
    <th colspan="6">Hospitalisation</th>
  </tr>
  <tr>
    <th>
      {{mb_colonne class="CConsultation" field="patient_id" order_col=$order_col_pre order_way=$order_way_pre order_suffixe="_pre" url="?m=$m&tab=vw_idx_preadmission"}}
    </th>
    <th>
      {{mb_colonne class="CConsultation" field="heure" order_col=$order_col_pre order_way=$order_way_pre order_suffixe="_pre" url="?m=$m&tab=vw_idx_preadmission"}}
    </th>
    <th>Praticien</th>
    <th>Admission</th>
    <th>Chambre</th>
    <th>Préparé</th>
    <th>CMU</th>
    <th>DH</th>
  </tr>
  {{foreach from=$listConsultations item=curr_consult}}
    {{mb_include module=admissions template="inc_vw_preadmission_line" nodebug=true}}
  {{foreachelse}}
    <tr>
      <td colspan="9" class="empty">Aucune pré-admission</td>
    </tr>
  {{/foreach}}
</table>

{{mb_include module=forms template=inc_widget_ex_class_register_multiple_end event_name=preparation_entree object_class="CSejour"}}