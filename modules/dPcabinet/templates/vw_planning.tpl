<!-- $Id$ -->

<script>

  // default value
  var target_plage_consult = '{{$plageSel->_id}}';

showConsultations = function (oTd, plageconsult_id){
  oTd = $(oTd);
  oTd.up("table").select(".event").invoke("removeClassName", "selected");
  oTd.up(".event").addClassName("selected");
  refreshPlageConsult(plageconsult_id);
};

refreshPlageConsult = function (plageconsult_id) {
  if (!plageconsult_id) {
    plageconsult_id = target_plage_consult;
  }
  target_plage_consult = plageconsult_id;
  var oform = getForm("selectPrat");

  //refresh the target
  var url = new Url("cabinet", "inc_consultation_plage");
  url.addParam("plageconsult_id", target_plage_consult);
  url.addParam("show_payees", $V(oform.show_payees));
  url.addParam("show_annulees", $V(oform.show_annulees));
  url.requestUpdate('consultations');
};

putArrivee =function (oForm) {
  var today = new Date();
  oForm.arrivee.value = today.toDATETIME(true);
  oForm.submit();
};

goToDate = function (oForm, date) {
  $V(oForm.debut, date);
};

showConsultSiDesistement = function (){
  var url = new Url("cabinet", "vw_list_consult_si_desistement");
  url.addParam("chir_id", '{{$chirSel}}');
  url.pop(500, 500, "test");
};

printPlage = function (plage_id) {
  var url = new Url;
  url.setModuleAction("cabinet", "print_plages");
  url.addParam("plage_id", plage_id);
  url.addParam("_telephone", 1);
  url.popup(700, 550, "Planning");
};

printPlanning =function () {
  var url = new Url("cabinet", "print_planning");
  url.addParam("date", "{{$debut}}");
  url.addParam("chir_id", "{{$chirSel}}");
  url.popup(900, 600, "Planning");
};

Main.add(function () {
  var planning = window["planning-{{$planning->guid}}"];
  Calendar.regField(getForm("changeDate").debut, null, {noView: true});

  {{if $plageSel->_id}}
    var plageList =  $$(".{{$plageSel->_guid}}");
    if (plageList.length > 0) {
      showConsultations(plageList[0].down("a"), "{{$plageSel->_id}}");
    }
  {{/if}}
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

        <select name="chirSel" style="width: 15em; float: left;" onchange="this.form.submit()">
          <option value="-1" {{if $chirSel == -1}} selected="selected" {{/if}}>&mdash; Choisir un professionnel</option>
          {{mb_include module=mediusers template=inc_options_mediuser selected=$chirSel list=$listChirs}}
        </select>

        <a href="#1" id="vw_planning_a_semaine" onclick="$V($(this).getSurroundingForm().debut, '{{$prec}}')">&lt;&lt;&lt;</a>

        Semaine du {{$debut|date_format:"%A %d %b %Y"}} au {{$fin|date_format:"%A %d %b %Y"}}
        <input type="hidden" name="debut" class="date" value="{{$debut}}" onchange="this.form.submit()" />

        <a href="#1" onclick="$V($(this).getSurroundingForm().debut, '{{$suiv}}')">&gt;&gt;&gt;</a>
        <br />
        <a href="#1" onclick="$V($(this).getSurroundingForm().debut, '{{$today}}')">Aujourd'hui</a>
      </form>
      <br/>
      {{if $canEditPlage}}
        <button style="float: left;" class="new" id="create_plage_consult_button" onclick="PlageConsultation.edit('0');">{{tr}}CPlageconsult-title-create{{/tr}}</button>
      {{/if}}
    </th>
    <td style="min-width: 350px;">
      <button style="float: right;" class="print" onclick="printPlanning();">{{tr}}Print{{/tr}}</button>
      {{if $chirSel && $chirSel != -1}}
        <button type="button" class="lookup"
                {{if !$count_si_desistement}}disabled="disabled"{{/if}}
                onclick="showConsultSiDesistement()">
          {{tr}}CConsultation-si_desistement{{/tr}} ({{$count_si_desistement}})
        </button>
      {{/if}}
      <form action="?" name="selectPrat" method="get">
        <p>Afficher les :
          <label>
            <input type="checkbox" name="_show_payees" onchange="$V(this.form.show_payees, this.checked ? 1 : 0); refreshPlageConsult();" {{if $show_payees}}checked="checked"{{/if}}> payées
            <input type="hidden" name="show_payees" value="{{$show_payees}}" />
          </label>
          <label>
            <input type="checkbox" name="_show_annulees" onchange="$V(this.form.show_annulees, this.checked ? 1 : 0); refreshPlageConsult();" {{if $show_annulees}}checked="checked"{{/if}}> annulées
            <input type="hidden" name="show_annulees" value="{{$show_annulees}}" />
          </label>
        </p>
      </form>
    </td>
  </tr>
  <tr>
    <td>
      <div id="planning-plages">
        {{mb_include module=system template=calendars/vw_week}}
        <script type="text/javascript">

          redirectRDV = function(plageconsult_id) {
            var url = new Url('cabinet', 'edit_planning', 'tab');
            url.addParam('consultation_id', 0);
            url.addParam('plageconsult_id', plageconsult_id);
            url.redirectOpener();
          };

        Main.add(function() {
          ViewPort.SetAvlHeight("planning-plages", 1);

          var planning = window['planning-{{$planning->guid}}'];

          // click on main div, refresh the consult_id
          $(planning).events.each(function(elt) {
            var target_element = $(elt.internal_id);
            target_element.setStyle({'cursor': 'pointer'});
            target_element.observe('click', function(event) {
              if (event.element().hasClassName('event')) {
                var button = target_element.down('.button.list');
                showConsultations(button, elt.guid.split("-")[1]);
              }
            })
          });

          planning.onMenuClick = function(event, plage, elem){
            if (event == 'list') {
              showConsultations(elem, plage);
            }

            if (event == 'edit') {
              PlageConsultation.edit(plage);
            }

            if (event == 'clock') {
              redirectRDV(plage);
            }
          };

          // Lancer le calcul du view planning avec la hauteur height
          var height = $('planning-plages').getDimensions().height;
          planning.setPlanningHeight(height);
          planning.scroll();
        });

        </script>
      </div>
    </td>
    <td id="consultations">
      {{if !$plageSel->_id}}
        {{mb_include module=cabinet template=inc_consultations}}
      {{/if}}
    </td>
  </tr>
</table>