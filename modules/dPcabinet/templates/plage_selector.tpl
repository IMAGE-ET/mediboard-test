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
        if ($("plage-"+this.currPlage)) {
          $("plage-"+this.currPlage).removeClassName("selected");
        }
      }
      this.currPlage = plage_id;
      if ($("plage-"+this.currPlage)) {
        $("plage-"+this.currPlage).addClassName("selected");
      }
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

  emptyPage: function(plage_id) {
    $("Places_"+plage_id).update("");
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
      if (this.page_displayed <= this.max_number) {
        window.parent.PlageConsultSelector.pages[this.page_displayed] = plage_id;
        this.refreshPlage(this.page_displayed, true);
        this.page_displayed ++;
      }
    }
    else {
      window.parent.PlageConsultSelector.pages[found] = plage_id;
      this.currPlage = plage_id;
      this.page_displayed = found ;
      this.cleanuPlage(found);
    }
  },
  refreshPlage: function(page_number, multiple) {
    var url = new Url("dPcabinet", "httpreq_list_places");
    url.addParam("plageconsult_id", this.currPlage);
    url.addParam("multipleMode", multiple);
    url.requestUpdate("listPlaces-"+page_number);
  },
  cleanuPlage : function(page_number) {
    var plage = window.parent.PlageConsultSelector;
    $("listPlaces-"+page_number).update();
    plage.pages.splice(page_number, 1);
    this.currPlage = page_number;
  },
  addPlaceBefore: function(plage_id) {
    alert("Veuillez choisir une plage");
  },
  reset : function(id) {
    var plage = window.parent.PlageConsultSelector;
    if (plage.pages.length > 1) {
      plage.resetConsult();
      plage.resetPage();
      $$('.listPlace').each(function(elt) {
        elt.update('');
        elt.removeClassName("selected");
      });
      this.page_displayed = 1;
    }
  }
};

updatePlage = function(sdate) {
  var form = getForm("Filter");
  $V(form.date, sdate);

  if ($V(form.period) == "weekly") {
    form.submit();
  }
  var url = new Url("cabinet", "ajax_list_plages");
  url.addParam("dialog"             , 1);
  url.addParam("chir_id"            , $V(form.chir_id));
  url.addParam("function_id"        , $V(form.function_id));
  url.addParam("plageconsult_id"    , $V(form.plageconsult_id));
  url.addParam("consultation_id"    , $V(form.consultation_id));
  url.addParam("_line_element_id"   , $V(form._line_element_id));
  url.addParam("period"             , $V(form.period));
  url.addParam("multipleMode"       , "{{$multipleMode}}");
  url.addParam("hour"               , $V(form.hour));
  url.addParam("date"               , sdate ? sdate : $V(form.date));
  url.addParam("hide_finished"      , $V(form.hide_finished));
  url.addParam("function_id"       , $V(form._function_id));
  url.requestUpdate('listePlages');
};

Main.add(function () {
  {{* Calendar.regField(getForm("Filter").date, null, {noView: true}); *}}
  updatePlage();
  {{if !$multipleMode}}
      PlageConsult.changePlage('{{$plageconsult_id}}');
  {{/if}}
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
  <input type="hidden" name="consultation_id" value="{{$consultation_id}}"/>
  <table class="form">
    <tr>
      <!-- planning type -->
      <th><label for="period" title="Changer la période de recherche">Planning</label></th>
      <td>
        <select name="period" onchange="updatePlage()">
          {{foreach from=$periods item="_period"}}
          <option value="{{$_period}}" {{if $_period == $period}}selected="selected"{{/if}}>
            {{tr}}Period.{{$_period}}{{/tr}}
          </option>
          {{/foreach}}
        </select>
      </td>

      <!-- date -->
      <td class="button" style="width: 250px;">
        <input type="hidden" name="date" class="date" value="{{$date}}" onchange="$V(getForm('Filter').plageconsult_id, '');" />
      </td>

      <!-- filter -->
      <th><label for="hour" title="Filtrer les plages englobalt l'heure choisie">Filtrer les heures</label></th>
      <td>
        <select name="hour" onchange="updatePlage()">
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
        <input type="radio" name="hide_finished" value="0" onclick="updatePlage()" {{if $hide_finished == "0"}}checked="checked" {{/if}} />
        <label for="hide_finished_0">Non</label>
        <input type="radio" name="hide_finished" value="1" onclick="updatePlage()" {{if $hide_finished == "1"}}checked="checked" {{/if}} />
        <label for="hide_finished_1">Oui</label>
      </td>

      <th>Filtre par fonction</th>
      <td>
        <select name="_function_id" style="width: 15em;" onchange="updatePlage()">
          <option value="">&mdash; {{tr}}Choose{{/tr}}</option>
          {{foreach from=$listFunctions item=_function}}
            <option value="{{$_function->_id}}" class="mediuser" style="border-color: #{{$_function->color}};" {{if $function_id == $_function->_id}}selected="selected" {{/if}}>
              {{$_function->_view}}
            </option>
          {{/foreach}}
        </select>
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
 
</div>
{{if $multipleMode}}
  {{math assign=width equation="(72/(b))" b=$app->user_prefs.NbConsultMultiple}}
  {{foreach from=$app->user_prefs.NbConsultMultiple|range:2:-1 item=j}}
    <div id="listPlaces-{{$j}}" class="listPlace" style="float:right; width:{{$width}}%"></div>
    <script>
      ViewPort.SetAvlHeight('listPlaces-{{$j}}',.95);
    </script>
  {{/foreach}}
{{/if}}
<!-- classic one -->
<div id="listPlaces-1" class="listPlace" style="float: right; width: {{if $multipleMode}}{{$width}}{{else}}50{{/if}}%;">
</div>

  
<script>
  ViewPort.SetAvlHeight('listPlaces-1',.95);
  ViewPort.SetAvlHeight('listePlages', .95);
</script>
