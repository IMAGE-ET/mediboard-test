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

<script type="text/javascript">
  window.save_dates = {
    prev: '{{$prev}}',
    next: '{{$next}}',
    today: '{{$today}}'
  };
  
  Main.add(function() {
    refreshPlanning(null, '{{$debut}}');
  });
  function printPlanning() {
    var url = new Url("cabinet", "print_planning");
    url.addParam("date", $V(getForm("changeDate").debut));
    url.addParam("chir_id", $V(getForm("changeDate").chirSel));
    url.popup(900, 600, "Planning");
  }
  
  function showConsultSiDesistement(){
    var url = new Url("cabinet", "vw_list_consult_si_desistement");
    url.addParam("chir_id", $V(getForm("changeDate").chirSel));
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
  
  function refreshPlanning(type_date, date) {
    var form = getForm("changeDate");
    var url = new Url("dPcabinet", "ajax_vw_planning");
    url.addParam("chirSel", $V(form.chirSel));
    if (type_date == "prev") {
      url.addParam("debut", window.save_dates.prev);
    }
    else if (type_date == "next") {
      url.addParam("debut", window.save_dates.next);
    }
    else if (type_date == "today") {
      url.addParam("debut", window.save_dates.today);
    }
    else if (date) {
      url.addParam("debut", date);
    }
    url.requestUpdate("planning-plages");
  }
  
  function setClose(heure, plage_id, date, chir_id, consult_id) {
    if (window.action_in_progress) {
      window.action_in_progress = false;
      return;
    }
    
    // Action de couper
    if (window.cut_consult_id) {
      cutCopyConsultation(window.cut_consult_id, plage_id, heure, 'do_consultation_aed');
      // On garde la consultation d'origine, et on permet de la coller ultérieurement
      window.copy_consult_id = window.cut_consult_id;
      window.cut_consult_id = null;
      updateStatusCut();
      return;
    }
    
    // Action de coller
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
    
    url.modal({width: 950, height: 700});
    
    url.modalObject.observe("afterClose", refreshPlanning);
  }
</script>

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

<form action="?" name="changeDate" method="get">
  <input type="hidden" name="m" value="{{$m}}" />
  <input type="hidden" name="tab" value="{{$tab}}" />
  <input type="hidden" name="plageconsult_id" value="0" />
  <table class="main">
    <tr>
      <th style="width: 25%; text-align: left;">
        <button type="button" style="float: left;" class="new" onclick="PlageConsultation.edit('0');">Créer une nouvelle plage</button>
        
        <select name="chirSel" style="width: 15em;" onchange="refreshPlanning()">
          <option value="-1" {{if $chirSel == -1}} selected="selected" {{/if}}>&mdash; Choisir un professionnel</option>
          {{mb_include module=mediusers template=inc_options_mediuser selected=$chirSel list=$listChirs}}
        </select>
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
        <button type="button" class="print" onclick="printPlanning();">{{tr}}Print{{/tr}}</button>
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