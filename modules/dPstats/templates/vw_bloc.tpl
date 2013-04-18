{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPstats
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{mb_script module="dPplanningOp" script="ccam_selector"}}

<script type="text/javascript">

var printTabAllPrats = function() {
  var oForm = getForm("filter-bloc");
  var url = new Url("dPstats", "print_tab_occupation_salle");
  url.addParam("date_debut"   , $V(oForm._date_min));
  url.addParam("date_fin"     , $V(oForm._date_max));
  url.addParam("CCAM"         , $V(oForm.codes_ccam));
  url.addParam("type"         , $V(oForm.type_hospi));
  url.addParam("discipline_id", $V(oForm.discipline_id));
  url.addParam("bloc_id"      , $V(oForm.bloc_id));
  url.addParam("salle_id"     , $V(oForm.salle_id));
  url.addParam("hors_plage"   , $V(oForm.hors_plage));
  url.popup(500, 500, "tableau");
}
  
var Details = {
  activite: function(date){
    var form = getForm("filter-bloc");
    
    var url = new Url("dPstats", "vw_graph_activite_zoom");
    url.addParam("date"      , date);
    url.addParam("hors_plage", $V(form.hors_plage));
    url.mergeParams(detailOptions);
    url.popup(760, 500, "ZoomMonth");
  },
  occupation_total: function(date){
    var form = getForm("filter-bloc");
    
    var url = new Url("dPstats", "vw_graph_occupation_zoom");
    url.addParam("date"      , date);
    url.addParam("hors_plage", $V(form.hors_plage));
    url.addParam("type_hospi", $V(form.type_hospi));
    url.mergeParams(detailOptions);
    url.popup(760, 500, "ZoomMonth");
  },
  temps_salle: function(date){
    var form = getForm("filter-bloc");
    
    var url = new Url("dPstats", "vw_graph_temps_salle_zoom");
    url.mergeParams(detailOptions);
    url.addParam("date"      , date);
    url.addParam("hors_plage", $V(form.hors_plage));
    url.addParam("type_hospi", $V(form.type_hospi));
    url.popup(760, 500, "ZoomMonth");
  }
};

var detailOptions = {
  salle_id     : "{{$filter->salle_id}}",
  prat_id      : "{{$filter->_prat_id}}",
  codes_ccam   : "{{$filter->codes_ccam|smarty:nodefaults|escape:'javascript'}}",
  discipline_id: "{{$filter->_specialite}}",
  size         : 2
};

var graphs = {{$graphs|@json}};

Main.add(function(){
  drawGraphs(true);
});

function drawGraphs(showLegend){
  $H(graphs).each(function(pair){
    var g = pair.value;
    $("graph-"+pair.key).update();
    $("legend-"+pair.key).update();
    
    g.options.legend = {
      show: true,
      container: (showLegend ? null : $("legend-"+pair.key))
    };
    Flotr.draw($('graph-'+pair.key), g.series, g.options);
  });
  
  Object.keys(Details).each(function(d){
    if (!$('graph-'+d)) return;
    
    var select = DOM.select({}, 
      DOM.option({value: ""}, "&ndash; Vue sur un mois &ndash;")
    );
    
    graphs[d].options.xaxis.ticks.each(function(tick){
      select.insert(DOM.option({value: tick[1]}, tick[1]));
    });
    
    select.observe("change", function(event){
      Details[d]($V(Event.element(event)));
    });
    
    $('graph-'+d).down('.flotr-tabs-group').insert(select);
  });
}
</script>

<form name="filter-bloc" action="?" method="get" onsubmit="return checkForm(this)">
<input type="hidden" name="m" value="dPstats" />
<input type="hidden" name="_chir" value="{{$app->user_id}}" />
<input type="hidden" name="_class" value="" />
<table class="main form">
  <tr>
    <th colspan="6" class="category">
      Activité du bloc opératoire
      <select name="type_view_bloc" onchange="this.form.submit()">
        <option value="nbInterv"    {{if $type_view_bloc == "nbInterv"}}    selected="selected" {{/if}}>Nombre d'interventions</option>
        <option value="dureeInterv" {{if $type_view_bloc == "dureeInterv"}} selected="selected" {{/if}}>Occupation du bloc</option>
      </select>
    </th>
  </tr>
  <tr>
    <th>{{mb_label object=$filter field="_date_min"}}</th>
    <td>{{mb_field object=$filter field="_date_min" form="filter-bloc" canNull="false" register=true}}</td>
    <th>{{mb_label object=$filterSejour field="type"}}</th>
    <td>
      <select name="type_hospi" style="width: 15em;">
        <option value="">&mdash; Tous les types d'hospi</option>
        {{foreach from=$filterSejour->_specs.type->_locales key=key_hospi item=curr_hospi}}
        <option value="{{$key_hospi}}" {{if $key_hospi == $filterSejour->type}}selected="selected"{{/if}}>
          {{$curr_hospi}}
        </option>
        {{/foreach}}
      </select>
    </td>
    <th>{{mb_label class=CSalle field="bloc_id"}}</th>
    <td>
      <select name="bloc_id" style="width: 15em;">
        <option value="">&mdash; {{tr}}CBlocOperatoire.all{{/tr}}</option>
        {{foreach from=$listBlocs item=curr_bloc}}
        <option value="{{$curr_bloc->_id}}" {{if $curr_bloc->_id == $bloc->_id }}selected="selected"{{/if}}>
          {{$curr_bloc->nom}}
        </option>
        {{/foreach}}
      </select>
    </td>
  </tr>
  <tr>
    <th>{{mb_label object=$filter field="_date_max"}}</th>
    <td>{{mb_field object=$filter field="_date_max" form="filter-bloc" canNull="false" register=true}} </td>
    <th>{{mb_label object=$filter field="_prat_id"}}</th>
    <td>
      <select name="prat_id" style="width: 15em;">
        <option value="0">&mdash; Tous les praticiens</option>
        {{foreach from=$listPrats item=curr_prat}}
        <option value="{{$curr_prat->user_id}}" {{if $curr_prat->user_id == $filter->_prat_id}}selected="selected"{{/if}}>
          {{$curr_prat->_view}}
        </option>
        {{/foreach}}
      </select>
      <button type="button" class="print notext" onclick="printTabAllPrats()">Tous les praticiens</button>
    </td>
    <th>{{mb_label object=$filter field="salle_id"}}</th>
    <td>
      <select name="salle_id" style="width: 15em;">
        <option value="">&mdash; {{tr}}CSalle.all{{/tr}}</option>
        {{foreach from=$listBlocsForSalles item=curr_bloc}}
        <optgroup label="{{$curr_bloc->nom}}">
          {{foreach from=$curr_bloc->_ref_salles item=curr_salle}}
          <option value="{{$curr_salle->_id}}" {{if $curr_salle->_id == $filter->salle_id}}selected="selected"{{/if}}>
            {{$curr_salle->nom}}
          </option>
          {{foreachelse}}
          <option value="" disabled="disabled">{{tr}}CSalle.none{{/tr}}</option>
          {{/foreach}}
        </optgroup>
        {{/foreach}}
      </select>
    </td>
  </tr>
  <tr>
    <th>{{mb_label object=$filter field="codes_ccam"}}</th>
    <td>
      {{mb_field object=$filter field="codes_ccam" canNull="true" size="20"}}
      <button class="search" type="button" onclick="CCAMSelector.init()">Rechercher</button>   
      <script type="text/javascript">
        CCAMSelector.init = function(){
          this.sForm = "filter-bloc";
          this.sView = "codes_ccam";
          this.sChir = "_chir";
          this.sClass = "_class";
          this.pop();
        }
      </script>
    </td>
    <th>{{mb_label object=$filter field="_specialite"}}</th>
    <td colspan="3">
      <select name="discipline_id" style="width: 15em;">
        <option value="0">&mdash; Toutes les spécialités</option>
        {{foreach from=$listDisciplines item=curr_disc}}
        <option value="{{$curr_disc->discipline_id}}" {{if $curr_disc->discipline_id == $filter->_specialite }}selected="selected"{{/if}}>
          {{$curr_disc->_view}}
        </option>
        {{/foreach}}
      </select>
    </td>
  </tr>
  <tr>
    <td colspan="6" class="button">
      <button class="search" type="submit">Afficher</button>
      <label><input type="checkbox" onclick="drawGraphs(this.checked)" checked="checked" /> Légende intégrée</label>
      <label><input type="checkbox" name="hors_plage_view" {{if $hors_plage}}checked="true"{{/if}}
        onchange="$V(this.form.hors_plage, this.checked ? 1 : 0)"/>Hors plage</label>
      <input type="hidden" name="hors_plage" value="{{$hors_plage}}" />
    </td>
  </tr>
</table>
</form>

{{foreach from=$graphs item=graph key=key}}
<table class="layout">
  <tr>
    <td><div style="width: 600px; height: 400px; float: left; margin: 1em;" id="graph-{{$key}}"></div></td>
    <td style="vertical-align: top;" id="legend-{{$key}}"></td>
  </tr>
</table>
{{/foreach}}