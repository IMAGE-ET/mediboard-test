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
      <button type="button" onclick="printTableau()" class="print">Impression</button>
      <button type="button" onclick="showRapport('{{$date}}')" class="print">Rapport</button>
    </td>
    <td>
      <form name="chgAff" action="?m={{$m}}" method="get">
      <input type="hidden" name="m" value="dPhospi" />
      <input type="hidden" name="tab" value="vw_affectations" />
      <input type="hidden" name="date" class="date" value="{{$date}}" onchange="this.form.submit()" />
        <select name="mode" onchange="reloadTableau();" style="float: right;">
          <option value="0" {{if $mode == 0}}selected="selected"{{/if}}>{{tr}}Instant view{{/tr}}</option>
          <option value="1" {{if $mode == 1}}selected="selected"{{/if}}>{{tr}}Day view{{/tr}}</option>
        </select>
      
      {{foreach from=$services item=curr_service}}
        {{if $curr_service->externe}}
          <label title="Afficher le service {{$curr_service->nom}}">
        {{else}}
          <label title="Afficher le service externe {{$curr_service->nom}}">
        {{/if}}
        <input
          type="checkbox"
          name="list_services[]"
          value="{{$curr_service->_id}}"
          {{if in_array($curr_service->_id, $list_services)}}
          checked="checked" 
          {{/if}}
          />
          {{if $curr_service->externe}}
            <em>{{$curr_service->nom}}</em>
          {{else}}
            {{$curr_service->nom}}
          {{/if}}
        </label>
      {{/foreach}}
      
      <!--  Hack des sous-bois, cf $V -->
      {{if $services|@count == 1}}
        <input type="hidden" name="list_services[]" value="" />
      {{/if}}
      
        <button class="search" type="button" onclick="reloadTableau();">Afficher</button> 
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
          {{include file="inc_affectations_services.tpl"}}
          </td>
          {{/if}}
        {{/foreach}}
        </tr>
      </table>
    </td>
  </tr>
</table>