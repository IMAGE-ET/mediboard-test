<!-- $Id$ -->

{{assign var="redirect" value="?m=dPcabinet&a=plage_selector&dialog=1&chir_id=$chir_id&period=$period&hour=$hour&hide_finished=$hide_finished"}}

<script type="text/javascript">
{{if $plage->plageconsult_id}}
function setClose(time) {
  window.opener.PlageConsultSelector.set(time,
    "{{$plage->plageconsult_id}}",
    "{{$plage->date|date_format:"%A %d/%m/%Y"}}",
    "{{$plage->freq}}",
    "{{$plage->chir_id}}",
    "{{$plage->_ref_chir->_view|smarty:nodefaults|escape:"javascript"}}");
  window.close();
}
{{/if}}

function pageMain() {
  regRedirectPopupCal("{{$date}}", "{{$redirect|smarty:nodefaults}}&date=");  
}

</script>

<table class="main">

<tr>
  <td class="category" colspan="2">
    
    <form name="Filter" action="?" method="get">
    
    <input type="hidden" name="m" value="dPcabinet" />
    <input type="hidden" name="a" value="plage_selector" />
    <input type="hidden" name="dialog" value="1" />
    <input type="hidden" name="date" value="{{$date}}" />
    <input type="hidden" name="chir_id" value="{{$chir_id}}" />
    <input type="hidden" name="plageconsult_id" value="{{$plage->_id}}" />

    <table class="form">
      <tr>
        <th><label for="period" title="Changer la période de recherche">Planning</label></th>
        <td>
          <select name="period" onchange="this.form.submit()">
            {{foreach from=$periods item="_period"}}
            <option value="{{$_period}}" {{if $_period == $period}}selected="selected"{{/if}}>
              {{tr}}Period.{{$_period}}{{/tr}}
            </option>
            {{/foreach}}
          </select>
        </td>
        
        <td class="button" style="width: 250px;">
          <a style="float:left" href="{{$redirect}}&amp;date={{$pdate}}">&lt;&lt;&lt;</a>
          <a style="float:right" href="{{$redirect}}&amp;date={{$ndate}}">&gt;&gt;&gt;</a>
          <strong>
            {{if $period == "day"  }}{{$date|date_format:" %A %d %B %Y"}}{{/if}}
            {{if $period == "week" }}{{$date|date_format:" semaine du %d %B %Y"}}{{/if}}
            {{if $period == "month"}}{{$date|date_format:" %B %Y"}}{{/if}}
          </strong>
          <img id="changeDate" src="./images/icons/calendar.gif" title="Choisir la date" alt="calendar" />
        </td>
        
        <th><label for="hour" title="Filtrer les plages englobalt l'heure choisie">Filtrer les heures</label></th>
        <td>
          <select name="hour" onchange="this.form.submit()">
    		<option value="">&mdash; Toutes</option>
            {{foreach from=$hours item="_hour"}}
            <option value="{{$_hour}}" {{if $_hour == $hour}} selected="selected" {{/if}}>
              {{$_hour|string_format:"%02d h"}}
            </option>
            {{/foreach}}
          </select>
        </td>
        
        <td>
          
		  <label for="hide_finished">Masquer terminées :</label>
		  <input type="radio" name="hide_finished" value="0" onchange="this.form.submit()" {{if $hide_finished == "0"}}checked="checked" {{/if}} />
		  <label for="hide_finished_0">Non</label>
		  <input type="radio" name="hide_finished" value="1" onchange="this.form.submit()" {{if $hide_finished == "1"}}checked="checked" {{/if}} />
		  <label for="hide_finished_1">Oui</label>
  
        </td>
      </tr>

    </table>

    </form>
  </td>
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
      {{foreach from=$listPlage item=_plage}}
      {{assign var="pct" value=$_plage->_fill_rate}}
      {{if $pct gt 100}}
      {{assign var="pct" value=100}}
      {{/if}}
      {{if $pct lt 50}}{{assign var="backgroundClass" value="empty"}}
      {{elseif $pct lt 90}}{{assign var="backgroundClass" value="normal"}}
      {{elseif $pct lt 100}}{{assign var="backgroundClass" value="booked"}}
      {{else}}{{assign var="backgroundClass" value="full"}}
      {{/if}} 
      <tr {{if $_plage->plageconsult_id == $plageconsult_id}}class="selected"{{/if}}>
        <td>
          <div class="progressBar">
            <div class="bar {{$backgroundClass}}" style="width: {{$pct}}%;"></div>
            <div class="text">
              <a href="{{$redirect}}&amp;plageconsult_id={{$_plage->_id}}">
                {{$_plage->date|date_format:"%A %d"}}
              </a>
            </div>
          </div>
        </td>
        <td class="text">
          <div class="mediuser" style="border-color: #{{$_plage->_ref_chir->_ref_function->color}};">
            {{$_plage->_ref_chir->_view}}
          </div>
        </td>
        <td class="text">
          {{$_plage->libelle}}
        </td>
        <td>
          {{$_plage->_affected}} / {{$_plage->_total}}
        </td>
      </tr>
      {{/foreach}}
    </table>
  </td>
  <td>
    <table class="tbl">
      {{if $plage->_id}}
      <tr>
        <th colspan="3">
          Dr. {{$plage->_ref_chir->_view}}
          <br />
          Plage du {{$plage->date|date_format:"%A %d %B %Y"}}
        </th>
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
      {{foreach from=$listPlace item=_place}}
      <tr>
        <td>
          <div style="float:left">
          <button type="button" class="tick" onclick="setClose('{{$_place.time|date_format:"%H:%M"}}')">{{$_place.time|date_format:"%Hh%M"}}</button>
          </div>
          <div style="float:right">
          {{foreach from=$_place.consultations item=_consultation}}
            <img src="./modules/dPcabinet/categories/{{$_consultation->_ref_categorie->nom_icone}}" alt="{{$_consultation->_ref_categorie->nom_categorie}}" title="{{$_consultation->_ref_categorie->nom_categorie}}" />
          {{/foreach}}
          </div>
        </td>
        <td class="text">
          {{foreach from=$_place.consultations item=_consultation}}
          
          {{if !$_consultation->patient_id}}
            {{assign var="style" value="style='background: #ffa;'"}}
          {{elseif $_consultation->premiere}}
            {{assign var="style" value="style='background: #faa;'"}}
          {{else}} 
            {{assign var="style" value=""}}
          {{/if}}
          <div {{$style|smarty:nodefaults}}>
            {{if !$_consultation->patient_id}}
              [PAUSE]
            {{else}}
              {{$_consultation->_ref_patient->_view}}
              {{if $_consultation->motif}}
              ({{$_consultation->motif|truncate:"20"}})
              {{/if}}
            {{/if}}
          </div>
          {{/foreach}}
        </td>
        <td>
          {{foreach from=$_place.consultations item=_consultation}}
            {{if !$_consultation->patient_id}}
              {{assign var="style" value="style='background: #ffa;'"}}
            {{elseif $_consultation->premiere}}
              {{assign var="style" value="style='background: #faa;'"}}
            {{else}} 
              {{assign var="style" value=""}}
            {{/if}}
            <div {{$style|smarty:nodefaults}}>
              {{$_consultation->duree}}
            </div>
          {{/foreach}}
        </td>
      </tr>
      {{/foreach}}
    </table>
  </td>
</tr>

</table>
