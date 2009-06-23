<!-- $Id$ -->

<script type="text/javascript">

var PlageConsult = {
  currPlage: {{if $plage->_id}}{{$plage->_id}}{{else}}0{{/if}},
  setClose: function(time) {
    alert("Veuillez choisir une plage");
  },
  changePlage: function(plage_id) {
    if(this.currPlage) {
      $("plage-"+this.currPlage).removeClassName("selected");
    }
    this.currPlage = plage_id;
    $("plage-"+this.currPlage).addClassName("selected");
    this.refreshPlage();
  },
  refreshPlage: function() {
    var url = new Url("dPcabinet", "httpreq_list_places");
    url.addParam("plageconsult_id", this.currPlage);
    url.requestUpdate("listPlaces", { waitingText: null });
  },
  addPlaceBefore: function(plage_id) {
    alert("Veuillez choisir une plage");
  }
}

Main.add(function () {
  Calendar.regField(getForm("Filter").date, null, {noView: true});
});

</script>

<table class="main">

<tr>
  <td class="category" colspan="2">
    
    <form name="Filter" action="?" method="get">
    
    <input type="hidden" name="m" value="dPcabinet" />
    <input type="hidden" name="a" value="plage_selector" />
    <input type="hidden" name="dialog" value="1" />
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
          <a style="float:left" href="javascript:;" onclick="$V(getForm('Filter').plageconsult_id, ''); $V(getForm('Filter').date, '{{$pdate}}')">&lt;&lt;&lt;</a>
          <a style="float:right" href="javascript:;" onclick="$V(getForm('Filter').plageconsult_id, ''); $V(getForm('Filter').date, '{{$ndate}}')">&gt;&gt;&gt;</a>
          <strong>
            {{if $period == "day"  }}{{$date|date_format:" %A %d %B %Y"}}{{/if}}
            {{if $period == "week" }}{{$date|date_format:" semaine du %d %B %Y"}}{{/if}}
            {{if $period == "month"}}{{$date|date_format:" %B %Y"}}{{/if}}
          </strong>
          <input type="hidden" name="date" class="date" value="{{$date}}" onchange="$V(getForm('Filter').plageconsult_id, ''); this.form.submit()" />
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
    {{include file="inc_list_plages.tpl"}}
  </td>
  <td id="listPlaces">
    {{include file="inc_list_places.tpl"}}
  </td>
</tr>

</table>
