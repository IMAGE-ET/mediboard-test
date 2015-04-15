{{* $Id: inc_consultations.tpl$  *}}

{{*
 * @package Mediboard
 * @subpackage dPcabinet
 * @version $Revision: 11962 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<tr>
  <th class="title" colspan="6">
    <strong>
    {{if $plageSel->_id}}
      <button class="print notext" onclick="printPlage({{$plageSel->_id}})" style="float:right">{{tr}}Print{{/tr}}</button>
      <button class="new" type="button" onclick="Consultation.editRDVModal(0, '{{$plageSel->chir_id}}', '{{$plageSel->_id}}');" style="float:right;">
        Planifier dans cette plage
      </button>
      {{mb_include module=system template=inc_object_notes object=$plageSel}}
        Consultations du {{$plageSel->date|date_format:$conf.longdate}}<br/>
        {{if $plageSel->chir_id != $chirSel}}
         remplacement de {{$plageSel->_ref_chir->_view}}
        {{elseif $plageSel->remplacant_id}}
         remplacé par {{$plageSel->_ref_remplacant->_view}}
        {{elseif $plageSel->pour_compte_id}}
         pour le compte de {{$plageSel->_ref_pour_compte->_view}}
        {{/if}}
    {{else}}
      {{tr}}CPlageconsult.none{{/tr}}
    {{/if}}
    </strong>
  </th>
</tr>

<tr>
  <th class="narrow">{{mb_title class=CConsultation field=heure}}</th>
  <th>{{mb_title class=CConsultation field=patient_id}}</th>
  <th>{{mb_title class=CConsultation field=motif}}</th>
  <th>{{mb_title class=CConsultation field=rques}}</th>
  <th id="inc_consult_notify_arrivate" >RDV</th>
  <th id="th_inc_consult_etat">{{mb_title class=CConsultation field=_etat}}</th>
</tr>
{{foreach from=$plageSel->_ref_consultations item=_consult}}
  <tr {{if $_consult->chrono == $_consult|const:'TERMINE'}} class="hatching" {{/if}}>
    {{assign var=consult_id    value=$_consult->_id}}
    {{assign var=patient       value=$_consult->_ref_patient}}
    {{assign var=href_consult  value="?m=$m&tab=edit_consultation&selConsult=$consult_id"}}
    {{assign var=href_planning value="?m=$m&tab=edit_planning&consultation_id=$consult_id"}}
    {{assign var=href_patient  value="?m=patients&tab=vw_edit_patients&patient_id=$patient->_id"}}

    {{if !$patient->_id}}
      {{assign var="style" value="style='background: #ffa;'"}}
    {{elseif $_consult->premiere}}
      {{assign var="style" value="style='background: #faa;'"}}
    {{elseif $_consult->derniere}}
      {{assign var="style" value="style='background: #faf;'"}}
    {{elseif $_consult->_ref_sejour->_id}}
      {{assign var="style" value="style='background: #CFFFAD;'"}}
    {{else}}
      {{assign var="style" value=""}}
    {{/if}}

    <td {{$style|smarty:nodefaults}}>
      <div style="float: left">
      {{if $patient->_id}}
        <a href="#" onclick="Consultation.edit('{{$_consult->_id}}');">
          <span onmouseover="ObjectTooltip.createEx(this, '{{$_consult->_guid}}')">
            {{$_consult->heure|date_format:$conf.time}}
          </span>
         </a>
      {{else}}
        <span onmouseover="ObjectTooltip.createEx(this, '{{$_consult->_guid}}')">
          {{mb_value object=$_consult field=heure}}
        </span>
      {{/if}}
      </div>
    </td>

    <td {{$style|smarty:nodefaults}} class="text">
      {{if !$patient->_id}}
        [PAUSE]
      {{else}}
        <button class="edit notext button" style="float: right;"  title="Modifier le dossier administratif" onclick="Patient.editModal('{{$patient->_id}}')">Modifier le patient
        </button>
        <a href="#" onclick="Consultation.edit('{{$_consult->_id}}');">
        {{mb_value object=$patient}}
        </a>
      {{/if}}
    </td>

    <td class="text" {{$style|smarty:nodefaults}}>
      {{assign var=categorie value=$_consult->_ref_categorie}}

      {{if $categorie->_id}}
      <div>
        {{mb_include module=cabinet template=inc_icone_categorie_consult
          categorie=$categorie
          onclick="IconeSelector.changeCategory('$consult_id', this)"
          display_name=true
        }}
      </div>
      {{/if}}

      {{if $patient->_id}}
        <a href="{{$href_consult}}" title="Voir la consultation">
          {{$_consult->motif|truncate:35:"...":false|nl2br}}
        </a>
      {{else}}
        {{$_consult->motif|truncate:35:"...":false|nl2br}}
      {{/if}}
    </td>
    <td class="text" {{$style|smarty:nodefaults}}>
      {{if $patient->_id}}
        <a href="{{$href_consult}}" title="Voir la consultation">{{$_consult->rques|truncate:35:"...":false|nl2br}}</a>
      {{else}}
        {{$_consult->rques|truncate:35:"...":false|nl2br}}
      {{/if}}
      {{if @$modules.3333tel->mod_active}}
        {{mb_include module=3333tel template=inc_check_3333tel object=$_consult tiny=1}}
      {{/if}}
    </td>
    <td {{$style|smarty:nodefaults}}>
      {{if $_consult->duree > 1}}
        <div style="float:right;">({{math equation="a*b" a=$_consult->duree b=$_consult->_ref_plageconsult->_freq}} min)</div>
      {{/if}}
      <form name="etatFrm{{$_consult->_id}}" action="?m=dPcabinet" method="post">
        <input type="hidden" name="m" value="dPcabinet" />
        <input type="hidden" name="dosql" value="do_consultation_aed" />
        {{mb_key object=$_consult}}
        <input type="hidden" name="chrono" value="{{$_consult|const:'PATIENT_ARRIVE'}}" />
        <input type="hidden" name="arrivee" value="" />
      </form>

      <div id="form-motif_annulation-{{$_consult->_id}}" style="display: none">
        <form name="cancelFrm{{$_consult->_id}}" action="?m=dPcabinet" method="post" onsubmit="">
          <input type="hidden" name="m" value="dPcabinet" />
          <input type="hidden" name="dosql" value="do_consultation_aed" />
          {{mb_key object=$_consult}}
          <input type="hidden" name="chrono" value="{{$_consult|const:'TERMINE'}}" />
          <input type="hidden" name="annule" value="1" />
          <table class="tbl main">
            <tr>
              <th colspan="2" class="title">
                {{$_consult->_view}}
                <button type="button" class="cancel notext" onclick="Control.Modal.close();" style="float:right;">{{tr}}Close{{/tr}}</button>
              </th>
            </tr>
            <tr>
              <td colspan="2" class="text">
                <div class="small-warning">{{tr}}CConsultation-confirm-cancel-1{{/tr}}</div>
              </td>
            </tr>
            <tr>
              <td style="text-align: right"><strong>{{mb_title object=$_consult field=motif_annulation}}</strong></td>
              <td>{{mb_field object=$_consult field=motif_annulation typeEnum="radio" separator="<br/>"}}</td>
            </tr>
            <tr>
              <td colspan="4" class="button">
                <button type="button" class="tick" onclick="this.form.submit();" id="submit_cancelFrm{{$_consult->_id}}">{{tr}}Validate{{/tr}}</button>
              </td>
            </tr>
          </table>
        </form>
      </div>

      <a class="action" href="#" onclick="Consultation.editRDVModal('{{$_consult->_id}}')">
        <img src="images/icons/planning.png" title="Modifier le rendez-vous" />
      </a>
      {{if !$_consult->annule}}
        {{if $_consult->chrono == $_consult|const:'PLANIFIE' && $patient->_id}}
          <button class="tick button notext" type="button" onclick="putArrivee(document.etatFrm{{$_consult->_id}})">Notifier l'arrivée du patient</button>
          <button type="button" class="cancel button notext" onclick="cancelRdv(document.cancelFrm{{$_consult->_id}});">
            Annuler ce rendez-vous
          </button>
        {{elseif $patient->_id}}
          <form name="cancel_arrive_{{$_consult->_id}}" action="?m=dPcabinet" method="post">
            <input type="hidden" name="m" value="dPcabinet" />
            <input type="hidden" name="dosql" value="do_consultation_aed" />
            {{mb_key object=$_consult}}
            <input type="hidden" name="annule" value="0" />
            <input type="hidden" name="chrono" value="{{$_consult|const:'PLANIFIE'}}" />
          </form>
          <button type="button" class="tick_cancel notext" onclick="cancelArrivee(document.cancel_arrive_{{$_consult->_id}})">Annuler l'arrivée</button>
        {{/if}}
      {{else}}
        <form name="cancel_annulation_{{$_consult->_id}}" action="?m=dPcabinet" method="post">
          <input type="hidden" name="m" value="dPcabinet" />
          <input type="hidden" name="dosql" value="do_consultation_aed" />
          {{mb_key object=$_consult}}
          <input type="hidden" name="annule" value="0" />
        </form>
        <button class="undo button notext" type="button" onclick="undoCancellation(document.cancel_annulation_{{$_consult->_id}});">Rétablir</button>
      {{/if}}
    </td>
    <td {{$style|smarty:nodefaults}} {{if $_consult->annule}}class="error"{{/if}}>
      {{if $patient->_id}}
        {{$_consult->_etat}}
      {{/if}}
    </td>
  </tr>
{{foreachelse}}
  <tr>
    <td colspan="6" class="empty">{{tr}}CConsultation.none{{/tr}}</td>
  </tr>
{{/foreach}}