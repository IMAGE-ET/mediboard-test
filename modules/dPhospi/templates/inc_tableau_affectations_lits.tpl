<script>
  Main.add(function () {
    Calendar.regField(getForm("chgAff").date, null, {noView: true, inline: true, container: $('calendar-container').update("")});

    updateWidths();

    if (!window.events_tableau_attached) {
      Event.observe(window, "resize", updateWidths);
      window.events_tableau_attached = true;
    }
  });

  updateWidths = function() {
    var vp_height = document.viewport.getHeight();
    $("view_affectatons_tableau").setStyle( {
      height: vp_height * 0.82 + "px"
    });
    $("header_vue_tableau").style.width = $("main_div_tableau").getWidth() + "px";
    var container = $("container_header");
    container.style.height = container.down("div").getHeight() + "px";
  }
</script>

{{assign var=width_service value=100}}
{{if $services|@count}}
  {{assign var=nb_services value=$services|@count}}
  {{math equation=100/x x=$nb_services assign=width_service}}
{{/if}}

<div id="container_header" style="width: 100%">
  <div id="header_vue_tableau" style="position: absolute; z-index: 200; background: #fff;">
    <table class="main layout" style="position: relative;">
      <tr>
        <td colspan="2">
          <div style="float:right;">
            <strong>Planning du {{$date|date_format:$conf.longdate}} - {{$totalLits}} place(s) de libre</strong>
          </div>
          {{if $alerte}}
            <div class="warning" style="float: left;">
              <a href="#1" onclick="showAlerte('{{$emptySejour->_type_admission}}')">
                Il y a {{$alerte}} patient(s) non placés dans la semaine à venir
                {{if $emptySejour->_type_admission}}
                  ({{tr}}CSejour._type_admission.{{$emptySejour->_type_admission}}{{/tr}})
                {{/if}}
              </a>
            </div>
          {{else}}
            <div class="info">
              Tous les patients sont placés pour la semaine à venir
            </div>
          {{/if}}
        </td>
      </tr>
      <tr>
        <td>
          <button type="button" onclick="printTableau()" class="print">Impression</button>
          <button type="button" onclick="showRapport('{{$date}}')" class="print">Rapport</button>
          {{if "astreintes"|module_active}}{{mb_include module=astreintes template=inc_button_astreinte_day date=$date}}{{/if}}
        </td>
        <td>
          <form name="chgAff" method="get" onsubmit="return onSubmitFormAjax(this, null, 'tableau')">
            <input type="hidden" name="m" value="dPhospi" />
            <input type="hidden" name="a" value="vw_affectations" />
            <input type="hidden" name="date" class="date" value="{{$date}}" onchange="this.form.onsubmit()" />
            <select name="mode" onchange="this.form.onsubmit();" style="float: right;">
              <option value="0" {{if $mode == 0}}selected="selected"{{/if}}>{{tr}}Instant view{{/tr}}</option>
              <option value="1" {{if $mode == 1}}selected="selected"{{/if}}>{{tr}}Day view{{/tr}}</option>
            </select>
          </form>
        </td>
      </tr>
    </table>
    <table class="tbl">
      <tr>
        {{foreach from=$services item=curr_service}}
          <th class="text {{if $curr_service->externe}}service_externe{{/if}}" style="width: {{$width_service}}%;">
            {{$curr_service->nom}}
            <br />
              <span style="font-size: 80%;">
              {{if $curr_service->externe}}
                externe
              {{else}}
                {{$curr_service->_nb_lits_dispo}} lit(s) dispo
              {{/if}}
              </span>
          </th>
        {{/foreach}}
      </tr>
    </table>
  </div>
</div>

<div id="view_affectatons_tableau" style="overflow-x: auto; overflow-y: scroll;">
  <table class="main layout" style="padding-top; 300px;" id="main_div_tableau">
    <tr>
      {{foreach from=$services item=curr_service}}
        <td class="fullService narrow" id="service{{$curr_service->service_id}}" style="width: {{$width_service}}%;">
          {{mb_include module=hospi template=inc_affectations_services}}
        </td>
      {{/foreach}}
    </tr>
  </table>
</div>