<!-- $Id$ -->

<script type="text/javascript">
{{if $plage->plageconsult_id}}
function setClose(time) {
  window.opener.setRDV(time,
    "{{$plage->plageconsult_id}}",
    "{{$plage->date|date_format:"%A %d/%m/%Y"}}",
    "{{$plage->freq}}",
    "{{$plage->chir_id}}",
    "{{$plage->_ref_chir->_view|escape:"javascript"}}");
  window.close();
}
{{/if}}

function pageMain() {
  regRedirectPopupCal("{{$date}}", "index.php?m=dPcabinet&a=plage_selector&dialog=1&chir_id={{$chir_id}}&date=");  
}

</script>

<table class="main">

<tr>
  <th class="category" colspan="2">
    <a href="index.php?m=dPcabinet&amp;a=plage_selector&amp;dialog=1&amp;chir_id={{$chir_id}}&amp;date={{$pdate}}">&lt;&lt;&lt;</a>
    {{$date|date_format:"%B %Y"}}
    <img id="changeDate" src="./images/calendar.gif" title="Choisir la date" alt="calendar" />
    <a href="index.php?m=dPcabinet&amp;a=plage_selector&amp;dialog=1&amp;chir_id={{$chir_id}}&amp;date={{$ndate}}">&gt;&gt;&gt;</a>
  </th>
</tr>

<tr>
  <td>
    <table class="tbl">
      <tr>
        <th>Date</th>
        <th>Praticien</th>
        <th>Libelle</th>
        <th>Etat</th>
      </tr>
      {{foreach from=$listPlage item=curr_plage}}
      {{assign var="pct" value=$curr_plage->_fill_rate}}
      {{if $pct gt 100}}
      {{assign var="pct" value=100}}
      {{/if}}
      {{if $pct lt 50}}{{assign var="backgroundClass" value="empty"}}
      {{elseif $pct lt 90}}{{assign var="backgroundClass" value="normal"}}
      {{elseif $pct lt 100}}{{assign var="backgroundClass" value="booked"}}
      {{else}}{{assign var="backgroundClass" value="full"}}
      {{/if}} 
      <tr style="{{if $curr_plage->plageconsult_id == $plageconsult_id}}font-weight: bold;{{/if}}">
        <td>
          <a href="index.php?m=dPcabinet&amp;a=plage_selector&amp;dialog=1&amp;plageconsult_id={{$curr_plage->plageconsult_id}}&amp;chir_id={{$chir_id}}&amp;date={{$date}}">
            <div class="progressBar">
              <div class="bar {{$backgroundClass}}" style="width: {{$pct}}%;"></div>
              <div class="text">{{$curr_plage->date|date_format:"%A %d"}}</div>
            </div>
          </a>
        </td>
        <td class="text">
          {{$curr_plage->_ref_chir->_view}}
        </td>
        <td class="text">
          {{$curr_plage->libelle}}
        </td>
        <td>
          {{$curr_plage->_affected}} / {{$curr_plage->_total}}
        </td>
      </tr>
      {{/foreach}}
    </table>
  </td>
  <td>
    <table class="tbl">
      {{if $plage->plageconsult_id}}
      <tr>
        <th colspan="3">Plage du {{$plage->date|date_format:"%A %d %B %Y"}}</th>
      </tr>
      <tr>
        <th>Heure</th>
        <th>Patient</th>
        <th>Durée</th>
      </tr>
      {{else}}
      <tr>
        <th colspan="3">Pas de plage le {{$date|date_format:"%A %d %B %Y"}}</th>
      </tr>
      {{/if}}
      {{foreach from=$listPlace item=curr_place}}
      <tr>
        <td><button type="button" class="tick" onclick="setClose('{{$curr_place.time|date_format:"%H:%M"}}')">{{$curr_place.time|date_format:"%Hh%M"}}</button></td>
        <td class="text">
          {{foreach from=$curr_place.consultations item=curr_consultation}}
          <div {{if $curr_consultation->premiere}}style="background: #faa;" {{/if}}>
            {{$curr_consultation->_ref_patient->_view}}
            {{if $curr_consultation->motif}}
            ({{$curr_consultation->motif|truncate:"20"}})
            {{/if}}
          </div>
          {{/foreach}}
        </td>
        <td>
          {{foreach from=$curr_place.consultations item=curr_consultation}}
          <div {{if $curr_consultation->premiere}}style="background: #faa;" {{/if}}>
            {{$curr_consultation->duree}}
          </div>
          {{/foreach}}
        </td>
      </tr>
      {{/foreach}}
    </table>
  </td>
</tr>

</table>
