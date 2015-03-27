{{*
 * $Id$
 * 
 * @package    Mediboard
 * @subpackage dPcabinet
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 *}}

{{mb_script module=cabinet script=plage_consultation}}
{{mb_script module=ssr script=planning}}
{{mb_default var=multiple value=0}}

{{if $listChirs|@count && $function_id}}
  {{assign var=multiple value=1}}
{{/if}}

<script type="text/javascript">
  window.save_dates = {
    prev: '{{$prev}}',
    next: '{{$next}}',
    today: '{{$today}}'
  };
  
  Main.add(function() {
    {{if $multiple}}
      var tabs = Control.Tabs.create('tabs_prats', true, {
        afterChange: function(container) {
          var chir_id = container.get('chir_id');
          var form = getForm("changeDate");
          $V(form.chirSel, chir_id, false);
          form.chirSel.onchange();
        }
      });
    {{else}}
      refreshPlanning(null, '{{$debut}}');
    {{/if}}
  });

  function printPlanning(function_mode) {
    var form = getForm("changeDate");
    if (function_mode) {
      var url = new Url("cabinet", "print_planning_function");
      url.addParam("date", $V(form.debut));
      url.addParam("function_id", $V(form.function_id));
      url.popup(900, 600, "Planning");
    }
    else {
      var url = new Url("cabinet", "print_planning");
      url.addParam("date", $V(form.debut));
      url.addParam("chir_id", $V(form.chirSel));
      url.popup(900, 600, "Planning");
    }
  }
  
  function showConsultSiDesistement(){
    var form = getForm("changeDate");
    var url = new Url("cabinet", "vw_list_consult_si_desistement");
    {{if $multiple}}
      url.addParam("function_id", $V(form.function_id));
    {{else}}
      url.addParam("chir_id", $V(form.chirSel));
    {{/if}}
    url.pop(500, 500, "test");
  }
  
  function updateStatusCut() {
    var div = $("status_cut");
    if (window.copy_consult_id) {
      div.update("Copier en cours");
      div.setStyle({borderColor: "#080"});
    }
    else if (window.cut_consult_id) {
      div.update("Couper en cours");
      div.setStyle({borderColor: "#080"});
    }
    else {
      div.update();
      div.setStyle({borderColor: "#ddd"});
      if (window.save_elt) {
        save_elt.removeClassName("opacity-50");
      }
    }
  }
  
  function cutCopyConsultation(consultation_id, plageconsult_id, heure, action) {
    var form = getForm("cutCopyConsultFrm");
    $V(form.consultation_id, consultation_id);
    $V(form.plageconsult_id, plageconsult_id);
    $V(form.heure, heure);
    $V(form.dosql, action);
    onSubmitFormAjax(form, {onComplete: refreshPlanning});
  }


  ChirOrFunction = function(multiple) {
    var form = getForm("changeDate");
    var schir = $V(form.chirSel);
    refreshPlanning();
  };
  
  function refreshPlanning(type_date, date) {
    var form = getForm("changeDate");
    var url = new Url("dPcabinet", "ajax_vw_planning");
    url.addParam("chirSel", $V(form.chirSel));
    url.addParam("function_id", $V(form.function_id));
    if (type_date == "prev") {
      url.addParam("debut", window.save_dates.prev);
      $V(form.debut, window.save_dates.prev);
    }
    else if (type_date == "next") {
      url.addParam("debut", window.save_dates.next);
      $V(form.debut, window.save_dates.next);
    }
    else if (type_date == "today") {
      url.addParam("debut", window.save_dates.today);
      $V(form.debut, window.save_dates.today);
    }
    else if (date) {
      url.addParam("debut", date);
      $V(form.debut, date);
    }

    // filters
    url.addParam("show_free", $V(form.show_free));
    url.addParam("show_cancelled", $V(form.cancelled));
    url.addParam("hide_in_conge", $V(form.hide_in_conge));
    url.addParam("facturated", $V(form.facturated));
    url.addParam("status", $V(form.finished));
    url.addParam("actes", $V(form.actes));

    url.requestUpdate('planning-plages');
  }
  
  function setClose(heure, plage_id, date, chir_id, consult_id) {
    if (window.action_in_progress) {
      window.action_in_progress = false;
      return;
    }
    
    // Action de coller d'un couper
    if (window.cut_consult_id) {
      cutCopyConsultation(window.cut_consult_id, plage_id, heure, 'do_consultation_aed');
      // On garde la consultation d'origine, et on permet de la coller ultérieurement
      window.copy_consult_id = window.cut_consult_id;
      window.cut_consult_id = null;
      updateStatusCut();
      return;
    }
    
    // Action de coller d'un copier
    if (window.copy_consult_id) {
      cutCopyConsultation(window.copy_consult_id, plage_id, heure, 'do_copy_consultation_aed');
      return;
    }
    
    // Clic sur une consultation
    if (consult_id) {
      modalPriseRDV(consult_id);
    }
    else {
      modalPriseRDV(0, Date.fromLocaleDate(date.split(" ")[1]).toDATE(), heure, plage_id);
    }
  }
  
  function modalPriseRDV(consult_id, date, heure, plage_id) {
    var url = new Url("dPcabinet", "edit_planning");
    
    url.addParam("dialog", 1);
    url.addParam("consultation_id", consult_id);
    
    url.addParam("date_planning", date);
    url.addParam("heure", heure);
    url.addParam("plageconsult_id", plage_id);
    
    url.modal({
      width: "95%",
      height: "95%"
    });
    
    url.modalObject.observe("afterClose", refreshPlanning);
  }

  function openLegend() {
    var url = new Url("cabinet", "ajax_legend_planning_new");
    url.requestModal(300);
  }
</script>

<style>
  .event.rdvfull {
    z-index: 400;
  }

  .event.rdvfree {
    z-index: 300;
  }
</style>

<form name="cutCopyConsultFrm" method="post">
  <input type="hidden" name="m" value="dPcabinet" />
  <input type="hidden" name="dosql" />
  <input type="hidden" name="consultation_id" />
  <input type="hidden" name="plageconsult_id" />
  <input type="hidden" name="heure" />
</form>

<form name="editConsult" method="post">
  <input type="hidden" name="m" value="dPcabinet" />
  <input type="hidden" name="dosql" value="do_consultation_aed" />
  <input type="hidden" name="consultation_id" />
  <input type="hidden" name="plageconsult_id" />
  <input type="hidden" name="heure" />
</form>

<form name="chronoPatient" method="post">
  <input type="hidden" name="m" value="dPcabinet"/>
  <input type="hidden" name="dosql" value="do_consultation_aed" />
  <input type="hidden" name="consultation_id" />
  <input type="hidden" name="chrono" />
  <input type="hidden" name="arrivee" />
</form>


  <form action="?" name="changeDate" method="get">
    <input type="hidden" name="m" value="{{$m}}" />
    <input type="hidden" name="tab" value="{{$tab}}" />
    <input type="hidden" name="plageconsult_id" value="0" />
    <table>
      <tr>
        <td class="narrow" style="vertical-align: top;">
          <select name="function_id" style="width: 11em; float:left;" onchange="this.form.submit();">
            <option value="">&mdash; {{tr}}CFunctions{{/tr}}</option>
            {{foreach from=$listFnc item=_fnc}}
              <option value="{{$_fnc->_id}}" {{if $_fnc->_id == $function_id}}selected="selected" {{/if}}>{{$_fnc}}</option>
            {{/foreach}}
          </select>
        </td>
        <td colspan="3">
          {{if $multiple}}
            <ul class="control_tabs small" id="tabs_prats">
              {{foreach from=$listChirs item=_chir}}
                <li>
                  <a href="#planning-plages_{{$_chir->_id}}">
                    {{mb_include module=mediusers template=inc_vw_mediuser mediuser=$_chir}}
                    <div id="planning-plages_{{$_chir->_id}}" data-chir_id="{{$_chir->_id}}"></div>
                  </a>
                </li>
              {{/foreach}}
            </ul>
          {{/if}}
        </td>
      </tr>
    </table>
    <table class="main">
      <tr>
        <th style="width: 25%; text-align: left;">
          <select name="chirSel" style="width: 11em;float:left; {{if $multiple}}display:none;{{/if}}" onchange="ChirOrFunction({{$multiple}})">
            <option value="-1" {{if $chirSel == -1}} selected="selected" {{/if}}>&mdash; Choisir un professionnel</option>
            {{mb_include module=mediusers template=inc_options_mediuser selected=$chirSel list=$listChirs}}
          </select>
          {{if $canEditPlage}}
            <p><button type="button" class="new" onclick="PlageConsultation.edit('0');">{{tr}}CPlageconsult-title-create{{/tr}}</button></p>
          {{/if}}
        </th>
        <th style="width: 50%">
          <a href="#1" onclick="refreshPlanning('prev')">&lt;&lt;&lt;</a>

          Semaine du <span id="debut_periode">{{$debut|date_format:"%A %d %b %Y"}}</span> au
          <span id="fin_periode">{{$fin|date_format:"%A %d %b %Y"}}</span>
          <input type="hidden" name="debut" class="date" value="{{$debut}}" onchange="refreshPlanning(null, this.value)" />

          <a href="#1" onclick="refreshPlanning('next')">&gt;&gt;&gt;</a>
          <br />
          <a href="#1" onclick="refreshPlanning('today')">Aujourd'hui</a>
        </th>
        <th style="width: 15%; text-align: right;">
          <button class="lookup" type="button" onclick="Modal.open('filter_more', {showClose: true, onClose:refreshPlanning, title:'Filtres'})">{{tr}}Filter{{/tr}}</button>
          <div id="filter_more" style="display: none;">
            {{mb_include module=cabinet template=inc_filter_new_planning}}
          </div>
          <button class="help" onclick="openLegend();return false;">{{tr}}Legend{{/tr}}</button>
          <button type="button" class="print" onclick="printPlanning();">{{tr}}Print{{/tr}}</button>
          {{if $function_id}}
          <button type="button" class="print" onclick="printPlanning(1);">{{tr}}Print{{/tr}} (cabinet)</button>
          {{/if}}
          <br />
          <button type="button" class="lookup" id="desistement_count"
                  {{if !$count_si_desistement}}disabled="disabled"{{/if}}
                  onclick="showConsultSiDesistement()">
            {{tr}}CConsultation-si_desistement{{/tr}} <span>({{$count_si_desistement}})</span>
          </button>
        </th>
        <th>
          <div id="status_cut" onclick="window.cut_consult_id = null; window.copy_consult_id = null; updateStatusCut();"
            style="width: 100px; height: 14px; border: 2px dashed #ddd; font-weight: bold; text-align: center; cursor: pointer;">
          </div>
        </th>
      </tr>
    </table>
  </form>


<div id="planning-plages"></div>

