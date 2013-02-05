<!-- $Id$ -->

<script type="text/javascript">

function showConsultations(oTd, plageconsult_id){
  oTd = $(oTd);

  oTd.up("table").select(".event").invoke("removeClassName", "selected");
  oTd.up(".event").addClassName("selected");

  var url = new Url("cabinet", "inc_consultation_plage");
  url.addParam("plageconsult_id", plageconsult_id);
  url.requestUpdate('consultations');
}

function putArrivee(oForm) {
  var today = new Date();
  oForm.arrivee.value = today.toDATETIME(true);
  oForm.submit();
}

function goToDate(oForm, date) {
  $V(oForm.debut, date);
}

function showConsultSiDesistement(){
  var url = new Url("cabinet", "vw_list_consult_si_desistement");
  url.addParam("chir_id", '{{$chirSel}}');
  url.pop(500, 500, "test");
}

function printPlage(plage_id) {
  var form = document.paramFrm;
  var url = new Url;
  url.setModuleAction("cabinet", "print_plages");
  url.addParam("plage_id", plage_id);
  url.addParam("_telephone", 1);
  url.popup(700, 550, "Planning");
}

function printPlanning() {
  var url = new Url("cabinet", "print_planning");
  url.addParam("date", "{{$debut}}");
  url.addParam("chir_id", "{{$chirSel}}");
  url.popup(900, 600, "Planning");
}

Main.add(function () {
  var planning = window["planning-{{$planning->guid}}"];
  Calendar.regField(getForm("changeDate").debut, null, {noView: true});
});
</script>

{{mb_script module=cabinet script=plage_consultation}}
{{mb_script module=ssr script=planning}}

<table class="main">
  <tr>
    <th style="width: 50%;">
      <form action="?" name="changeDate" method="get">
        <input type="hidden" name="m" value="{{$m}}" />
        <input type="hidden" name="tab" value="{{$tab}}" />
        <input type="hidden" name="plageconsult_id" value="0" />

        <a href="#1" onclick="$V($(this).getSurroundingForm().debut, '{{$prec}}')">&lt;&lt;&lt;</a>

        Semaine du {{$debut|date_format:"%A %d %b %Y"}} au {{$fin|date_format:"%A %d %b %Y"}}
        <input type="hidden" name="debut" class="date" value="{{$debut}}" onchange="this.form.submit()" />

        <a href="#1" onclick="$V($(this).getSurroundingForm().debut, '{{$suiv}}')">&gt;&gt;&gt;</a>
        <br />
        <a href="#1" onclick="$V($(this).getSurroundingForm().debut, '{{$today}}')">Aujourd'hui</a>
      </form>
      <br/>
      <button style="float: left;" class="new" onclick="PlageConsultation.edit('0');">Créer une nouvelle plage</button>
      <button style="float: right;" class="print" onclick="printPlanning();">{{tr}}Print{{/tr}}
    </th>
    <td style="min-width: 350px;">
      <form action="?" name="selectPrat" method="get">
        <input type="hidden" name="m" value="{{$m}}" />
        <input type="hidden" name="tab" value="{{$tab}}" />
        <select name="chirSel" style="width: 15em;" onchange="this.form.submit()">
          <option value="-1" {{if $chirSel == -1}} selected="selected" {{/if}}>&mdash; Choisir un professionnel</option>
          {{foreach from=$listChirs item=curr_chir}}
          <option class="mediuser" style="border-color: #{{$curr_chir->_ref_function->color}};" value="{{$curr_chir->user_id}}" {{if $chirSel == $curr_chir->user_id}} selected="selected" {{/if}}>
            {{$curr_chir->_view}}
          </option>
          {{/foreach}}
        </select>

        Cacher les : 
          <label>
            <input type="checkbox" onchange="$V(this.form.hide_payees, this.checked ? 1 : 0); this.form.submit()" {{if $hide_payees}}checked="checked"{{/if}} name="_hide_payees"> payées
            <input type="hidden" name="hide_payees" value="{{$hide_payees}}" />
          </label>
          <label>
            <input type="checkbox" onchange="$V(this.form.hide_annulees, this.checked ? 1 : 0); this.form.submit()" {{if $hide_annulees}}checked="checked"{{/if}} name="_hide_annulees"> annulées
            <input type="hidden" name="hide_annulees" value="{{$hide_annulees}}" />
          </label>
      </form>

      <br />

      {{if $chirSel && $chirSel != -1}}
        <button type="button" class="lookup" 
                {{if !$count_si_desistement}}disabled="disabled"{{/if}}
                onclick="showConsultSiDesistement()">
          {{tr}}CConsultation-si_desistement{{/tr}} ({{$count_si_desistement}})
        </button>
      {{/if}}

      {{if $plageSel->_id}}
        <a class="button new" href="?m={{$m}}&amp;tab=edit_planning&amp;consultation_id=0&amp;plageconsult_id={{$plageSel->_id}}">Planifier une consultation dans cette plage</a>
      {{/if}}

    </td>
  </tr>
  <tr>
    <td>
      <div id="planning-plages">
        {{mb_include module=ssr template=inc_vw_week}}
        <script type="text/javascript">

        Main.add(function() {
          ViewPort.SetAvlHeight("planning-plages", 1);

          var planning = window['planning-{{$planning->guid}}'];

          planning.onMenuClick = function(event, plage, elem){
            if (event == 'list') {
              showConsultations(elem,plage);
            }

            if (event == 'edit') {
              PlageConsultation.edit(plage);
            }

            if (event == 'clock') {
              var url = new Url('cabinet', 'edit_planning', 'tab');
              url.addParam('consultation_id', 0);
              url.addParam('plageconsult_id', plage);
              url.redirectOpener();
            }
          }

          // Lancer le calcul du view planning avec la hauteur height
          var height = $('planning-plages').getDimensions().height;
          planning.setPlanningHeight(height);
          planning.scroll();
        });

        </script>
      </div>
    </td>
    <td id="consultations">{{mb_include module=cabinet template=inc_consultations}}</td>
  </tr>
</table>