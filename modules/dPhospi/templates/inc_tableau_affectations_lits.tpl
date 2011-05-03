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
          Il y a {{$alerte}} patient(s) non placés dans la semaine qui vient
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
      <button type="button" onclick="showLegend()" class="search">Légende</button>
      <button type="button" onclick="showRapport('{{$date}}')" class="print">Rapport</button>
    </td>
    <td>
      {{include file="inc_mode_hospi.tpl"}}
      <form name="chgAff" action="?m={{$m}}" method="get">
      <input type="hidden" name="m" value="dPhospi" />
      <input type="hidden" name="tab" value="vw_affectations" />
      <input type="hidden" name="date" class="date" value="{{$date}}" onchange="this.form.submit()" />
      
      {{foreach from=$services item=curr_service}}
        <label title="Afficher le service {{$curr_service->nom}}">
        <input
          type="checkbox"
          name="list_services[]"
          value="{{$curr_service->_id}}"
          {{if in_array($curr_service->_id, $list_services)}}
          checked="checked" 
          {{/if}}
          />
          {{$curr_service->nom}}
        </label>
      {{/foreach}}
        <button class="search" type="button" onclick="reloadTableau();">Afficher</button> 
      </form>
    </td>
  </tr>

  <tr>
    <td class="greedyPane" colspan="2">
      <table class="affectations">
        <tr>
        {{foreach from=$services item=curr_service}}
          {{if $curr_service->_ref_chambres|@count}}
          <td class="fullService narrow" id="service{{$curr_service->service_id}}">
          {{include file="inc_affectations_services.tpl"}}
          </td>
          {{/if}}
        {{/foreach}}
        </tr>
      </table>
    </td>
  </tr>
</table>