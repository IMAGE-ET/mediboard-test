<!-- $Id$ -->
<script type="text/javascript">

var PlageConsult = {
  max_number : {{$app->user_prefs.NbConsultMultiple}},
  page_displayed : 1,
  currPlage: {{if $plage->_id}}{{$plage->_id}}{{else}}0{{/if}},
  setClose: function(time) {
    alert("Veuillez choisir une plage");
  },
  changePlage: function(plage_id, multipleMode) {
    if (!plage_id) return;

    //single mode
    if(!multipleMode) {
      if (this.currPlage) {
        this.page_displayed = 1;
        $("plage-"+this.currPlage).removeClassName("selected");
      }
      this.currPlage = plage_id;
      $("plage-"+this.currPlage).addClassName("selected");
    }


    this.currPlage = plage_id;
    if (multipleMode) {
      this.togglePage(this.currPlage);
      if (this.page_displayed > this.max_number) {
        return false;
      }
    }
    else {
      this.refreshPlage('1', multipleMode);
    }
  },
  togglePage: function(plage_id){
    $("plage-"+this.currPlage).toggleClassName("selected");
    var found = false;
    for(var a=0; a<=window.parent.PlageConsultSelector.pages.length; a++) {
      if (window.parent.PlageConsultSelector.pages[a] == plage_id) {
        found = a;
        break;
      }
    }

    if (found === false) {
      window.parent.PlageConsultSelector.pages[this.page_displayed] = plage_id;
      this.refreshPlage(this.page_displayed, true);
      this.page_displayed ++ ;
    }
    else {
      window.parent.PlageConsultSelector.pages[found] = plage_id;
      this.currPlage = plage_id;
      this.page_displayed = found ;
      this.refreshPlage(found, true);
    }
  },
  refreshPlage: function(page_number, multiple) {
    var url = new Url("dPcabinet", "httpreq_list_places");
    url.addParam("plageconsult_id", this.currPlage);
    url.addParam("multipleMode", multiple);
    url.requestUpdate("listPlaces-"+page_number);
  },
  cleanuPlage : function(page_number) {
    $("listPlaces-"+page_number).update();
  },
  addPlaceBefore: function(plage_id) {
    alert("Veuillez choisir une plage");
  },
  reset : function() {
    var plage = window.parent.PlageConsultSelector;
    if (plage.pages.length > 1) {
      for (var a = 1; a<(plage.pages.length); a++) {
        if ($("listPlaces-"+a)) {
          $("listPlaces-"+a).update("");
        }
        $("plage-"+plage.pages[a]).removeClassName("selected");
      }
      plage.resetConsult();
      plage.resetPage();
      this.page_displayed = 1;
    }
  }
};

Main.add(function () {
  Calendar.regField(getForm("Filter").date, null, {noView: true});
  {{if !$multipleMode}}PlageConsult.changePlage({{$plageconsult_id}});{{/if}}
});

</script>

<form name="Filter" action="?" method="get">
  <input type="hidden" name="m" value="dPcabinet" />
  <input type="hidden" name="a" value="plage_selector" />
  <input type="hidden" name="dialog" value="1" />
  <input type="hidden" name="chir_id" value="{{$chir_id}}" />
  <input type="hidden" name="function_id" value="{{$function_id}}" />
  <input type="hidden" name="plageconsult_id" value="{{$plage->_id}}" />
  <input type="hidden" name="_line_element_id" value="{{$_line_element_id}}" />
  <table class="form">
    <tr>
      <!-- planning type -->
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

      <!-- date -->
      <td class="button" style="width: 250px;">
        <a style="float:left" href="#1" onclick="$V(getForm('Filter').plageconsult_id, ''); $V(getForm('Filter').date, '{{$pdate}}')">&lt;&lt;&lt;</a>
        <a style="float:right" href="#1" onclick="$V(getForm('Filter').plageconsult_id, ''); $V(getForm('Filter').date, '{{$ndate}}')">&gt;&gt;&gt;</a>
        <strong>
          {{if $period == "day"  }}{{$refDate|date_format:" %A %d %B %Y"}}{{/if}}
          {{if $period == "week" || $period == "4weeks"}}{{$refDate|date_format:" semaine du %d %B %Y (%U)"}}{{/if}}
          {{if $period == "month"}}{{$refDate|date_format:" %B %Y"}}{{/if}}
        </strong>
        <input type="hidden" name="date" class="date" value="{{$date}}" onchange="$V(getForm('Filter').plageconsult_id, ''); this.form.submit()" />
      </td>

      <!-- filter -->
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

      <!-- hide -->
      <td>
        <label for="hide_finished">Masquer terminées :</label>
        <input type="radio" name="hide_finished" value="0" onclick="this.form.submit()" {{if $hide_finished == "0"}}checked="checked" {{/if}} />
        <label for="hide_finished_0">Non</label>
        <input type="radio" name="hide_finished" value="1" onclick="this.form.submit()" {{if $hide_finished == "1"}}checked="checked" {{/if}} />
        <label for="hide_finished_1">Oui</label>
      </td>

      {{if $multipleMode}}
        <td>
          <button type="button" class="button tick" onclick="window.parent.PlageConsultSelector.checkMultiple(); Control.Modal.close()">Valider</button>
          <button type="button" class="button cancel" onclick="PlageConsult.reset()">Réinitialiser</button>
        </td>
      {{/if}}
    </tr>

  </table>
</form>

<div style="float: left; width: {{if $multipleMode}}28{{else}}50{{/if}}%" id="listePlages">
  {{include file="inc_list_plages.tpl" multiple=$multipleMode}}
</div>
{{if $multipleMode}}
  {{math assign=width equation="(72/(b))" b=$app->user_prefs.NbConsultMultiple}}
  {{foreach from=$app->user_prefs.NbConsultMultiple|range:2:-1 item=j}}
    <div id="listPlaces-{{$j}}" class="listPlace" style="float:right; width:{{$width}}%"></div>
    <script>
      ViewPort.SetAvlHeight('listPlaces-{{$j}}', 1);
    </script>
  {{/foreach}}
{{/if}}
<!-- classic one -->
<div id="listPlaces-1" class="listPlace" style="float: right; width: {{if $multipleMode}}{{$width}}{{else}}50{{/if}}%;">
</div>

  
<script>
  ViewPort.SetAvlHeight('listPlaces-1', 1);
  ViewPort.SetAvlHeight('listePlages', 1);
</script>
