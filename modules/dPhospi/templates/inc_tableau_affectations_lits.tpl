<script type="text/javascript">
Main.add(function () {
  Calendar.regField(getForm("chgAff").date, null, {noView: true, inline: true, container: $('calendar-container').update("")});
});
</script>

<table class="main layout">
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

  <tr>
    <td class="greedyPane" colspan="2">
      <table class="layout affectations">
        <tr>
        {{foreach from=$services item=curr_service}}
          {{if $curr_service->_ref_chambres|@count}}
          <td class="fullService narrow" id="service{{$curr_service->service_id}}">
          {{mb_include module=hospi template=inc_affectations_services}}
          </td>
          {{/if}}
        {{/foreach}}
        </tr>
      </table>
    </td>
  </tr>
</table>