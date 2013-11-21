<!-- $Id$ -->

<script>
var consultationRdV = Class.create ({
  plage_id        : null,
  consult_id      : null,
  date            : null,
  heure           : null,
  _chirview       : null,
  chir_id         : null,
  is_cancelled    : 0,

  initialize: function (plage_id, consult_id, date, heure, chir_id, chir_view, todelete) {
    this.plage_id         = plage_id;
    this.consult_id       = consult_id;
    this.date             = date;
    this.heure            = heure;
    this.chir_id          = chir_id;
    this._chirview        = chir_view;
    this.is_cancelled     = todelete;
  }
});


RDVmultiples = {
  is_multiple     : 0,
  slots           : {},
  current_rank    : 0,
  automatic_rank  : 1,
  max_rank        : 8,

  init: function(consultation_ids, multiple) {
    // selected
    RDVmultiples.is_multiple = multiple;

    if (consultation_ids.length > 0) {
      for (var b=0; b<consultation_ids.length; b++) {
        var plage_id    = consultation_ids[b][0];
        var consult_id  = consultation_ids[b][1];
        var date        = consultation_ids[b][2];
        var time        = consultation_ids[b][3];
        var chir_id     = consultation_ids[b][4];
        var chir_view   = consultation_ids[b][5];
        var annule      = consultation_ids[b][6];
        RDVmultiples.addSlot(this.current_rank, plage_id, consult_id, date, time, chir_id, chir_view, annule);        // insert
        RDVmultiples.loadPlageConsult(plage_id, consult_id, RDVmultiples.is_multiple);  // display
        if (multiple) {
          this.selRank(this.current_rank+1);
        }
      }
    }
    else {
      if (multiple) {
        $('tools_plage_0').addUniqueClassName('selected');
      }
      else {
        var selected = $("tr.selected");
        console.log(selected);
      }
    }
  },

  // add a slot to the list.
  addSlot : function(slot_number, plage_id, consult_id, date, time, chir_id, _chir_view, toTrash) {
    var oldslot = this.slots[slot_number];
    // if consult_id, We keep it
    if (oldslot && oldslot.consult_id && consult_id != oldslot.consult_id) {
      consult_id = oldslot.consult_id;
    }
    this.slots[slot_number] = new consultationRdV(plage_id, consult_id, date, time, chir_id, _chir_view, toTrash);

    // creation                           plage_id && !consult_id
    // modif de consultation              !plage_id && consult_id
    // modifier la plage de la consult    plage_id && consult_id
    // simple modif                       !plage_id && !consult_id

  },

  resetSlots : function() {
    var consult_list = $H(this.slots);
    consult_list.each(function(elt) {
      RDVmultiples.removeSlot(elt[0]);
    });
  },

  //enlever un slot (ne doit pas avoir de consult_id)
  removeSlot : function(rank) {
    var slot = this.slots[rank];

    // si consult_id => annulation du rendez-vous
    if (slot && slot.consult_id) {
      slot.is_cancelled = 1;
    }
    // sinon on le supprime + refresh
    else {
      delete this.slots[rank];
      $("listPlaces-"+rank).update("");
    }
  },
  selRank: function(rank) {
    console.log(rank);
    if (rank <= this.max_rank) {
      RDVmultiples.current_rank = rank;
      if (this.is_multiple) {
        $$('.tools_plage').each(function(elt) {
          $(elt).removeClassName('selected');
        });

        var target = $('tools_plage_'+rank);
        $(target).addUniqueClassName('selected');
      }
    }
  },

  cleanRank: function(rank) {
    RDVmultiples.selRank(rank);
    $('plistPlaces-'+rank).update('');
  },

  refreshSlot : function(rank, multiple) {
  this.selRank(rank);
  var consult_list = $H(this.slots);
  var consult = consult_list._object[rank];
    if (consult) {
      this.loadPlageConsult(consult.plage_id, consult.consult_id, multiple);
    }
  },

  // load the plageconsult to the right
  loadPlageConsult : function(plageconsult_id, consult_id, multiple) {
  var url = new Url("dPcabinet", "httpreq_list_places");
  url.addParam("plageconsult_id", plageconsult_id);
  url.addParam("consult_id"     , consult_id);
  url.addParam("multipleMode", multiple);
  url.requestUpdate("listPlaces-"+this.current_rank);
  },

  sendData : function() {
    var consult_list = $H(this.slots);
    if (consult_list.size()) {
      window.parent.PlageConsultSelector.consultations = consult_list;
      window.parent.PlageConsultSelector.updateFromSelector();
      window.parent.Control.Modal.close();
    }
    else {
      alert("test");
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
  RDVmultiples.init({{$consultation_ids|@json}}, {{$multipleMode}});


  $('plage_list_container').on("click", '{{if !$multipleMode}}button.validPlage{{else}}input.validPlage{{/if}}', function(event, element) {
    var consult_id  = element.get("consult_id");
    var plage_id    = element.get("plageid");
    var time        = element.get("time");
    var date        = element.get("date");
    var chir_id     = element.get("chir_id");
    var chir_view   = element.get("chir_name");
    var slot_id     = element.up().up().up().up().up().up().up().get('slot_number');
    RDVmultiples.addSlot(slot_id, plage_id, consult_id, date, time, chir_id, chir_view);

    //end of treatment
    {{if !$multipleMode}}
      RDVmultiples.sendData();
    {{/if}}
  });
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
      <th><label for="hour" title="Filtrer les plages englobant l'heure choisie">Filtrer les heures</label></th>
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
        <input type="radio" name="hide_finished" value="0" onclick="updatePlage()" {{if !$hide_finished}}checked="checked" {{/if}} />
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
          <button type="button" id="consult_multiple_button_validate" class="button tick" onclick="RDVmultiples.sendData(); Control.Modal.close()">Valider</button>
          <button type="button" class="button cleanup" onclick="RDVmultiples.resetSlots()">Réinitialiser</button>
          <button type="button" class="help" onclick="Modal.open('help_consult_multiple');">{{tr}}Help{{/tr}}</button>
        </td>
      {{/if}}
    </tr>
  </table>
</form>

<div id="help_consult_multiple" style="display: none;">
  <button onclick="Control.Modal.close();" class="cancel button" style="float:right;">Merci</button>
  <h2>Aide consultations multiple</h2>
  <ul>
    <li>La plage active selectionnée est de couleur brune, il faut selectionner la plage puis cliquer à gauche dans le selecteur pour changer de plage de consultation</li>
    <li>Toutes vos actions ne seront appliquées qu'à l'enregistrement de la page de modification de la consultation</li>
    <li>En mode édition, l'appuie sur le bouton corbeille va annuler la consultation et non la supprimer</li>
    <li></li>
  </ul>
</div>

<!-- liste des plages -->
<div style="float: left; width: {{if $multipleMode}}28{{else}}50{{/if}}%" id="listePlages"></div>


<!-- liste du contenu des plages -->
{{if $multipleMode}}
  {{math assign=width equation="(72/(b))" b=$app->user_prefs.NbConsultMultiple}}
  {{assign var=nbConsult value=$app->user_prefs.NbConsultMultiple}}
{{else}}
  {{assign var=nbConsult value=1}}
  {{assign var=width value="50"}}
{{/if}}

<div id="plage_list_container">
  {{foreach from=1|range:$nbConsult:-1 item=j}}
    <div id="listPlage_dom_{{$j-1}}" data-slot_number="{{$j-1}}" style="width:{{$width}}%; float:left;">
      {{if $multipleMode}}
        <div id="tools_plage_{{$j-1}}" class="tools_plage">
          <button type="button" class="trash notext" style="float:right" onclick="RDVmultiples.removeSlot('{{$j-1}}')"></button>
          <button class="button right" onclick="RDVmultiples.selRank('{{$j-1}}')">Plage {{$j}}</button>
          <input type="hidden" name="consult_id" value=""/>

        </div>
      {{/if}}
      <div id="listPlaces-{{$j-1}}" class="listPlace"></div>
    </div>
    <script>
      ViewPort.SetAvlHeight('listPlage_dom_{{$j-1}}',.95);
    </script>
  {{/foreach}}
</div>

  
<script>
  ViewPort.SetAvlHeight('listePlages', .95);
</script>
