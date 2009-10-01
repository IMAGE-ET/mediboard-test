{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPstats
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{mb_include_script module="dPplanningOp" script="ccam_selector"}}

<script type="text/javascript">
function zoomGraphIntervention(date){
  var url = new Url("dPstats", "vw_graph_activite_zoom");
  url.addParam("date"         , date);
  url.addParam("salle_id"     , "{{$filter->salle_id}}");
  url.addParam("prat_id"      , "{{$filter->_prat_id}}");
  url.addParam("codes_ccam"   , "{{$filter->codes_ccam|smarty:nodefaults|escape:"javascript"}}");
  url.addParam("discipline_id", "{{$filter->_specialite}}");
  url.addParam("size"         , 2);
  url.popup(760, 400, "ZoomMonth");
}

var graphs = {{$graphs|@json}};
Main.add(function(){
  drawGraphs(true);
});

function drawGraphs(showLegend){
  var zoomSelect = $("graph-activite-zoom-date");
  $("graph-0").insert({before: zoomSelect});
  
  graphs.each(function(g, i){
    Flotr.draw($('graph-'+i), g.series, Object.extend(g.options, {legend: {show: showLegend}}));
  });
  
  if (zoomSelect.options.length == 1) {
    graphs[0].options.xaxis.ticks.each(function(tick){
      zoomSelect.insert(new Element('option', {value: tick[1]}).update(tick[1]));
    });
  }
  
  $('graph-0').select('.flotr-tabs-group').first().insert(zoomSelect.show());
}
</script>

<form name="bloc" action="?" method="get" onsubmit="return checkForm(this)">
<input type="hidden" name="m" value="dPstats" />
<input type="hidden" name="_chir" value="{{$app->user_id}}" />
<input type="hidden" name="_class_name" value="" />
<table class="main form">
  <tr>
    <th colspan="6" class="category">Activité du bloc opératoire</th>
  </tr>
  <tr>
     <td>{{mb_label object=$filter field="_date_min"}}</td>
    <td class="date">{{mb_field object=$filter field="_date_min" form="bloc" canNull="false" register=true}} </td>
    <td>{{mb_label class=CSalle field="bloc_id"}}</td>
    <td>
      <select name="bloc_id">
        <option value="">&mdash; {{tr}}CBlocOperatoire.select{{/tr}}</option>
        {{foreach from=$listBlocs item=curr_bloc}}
        <option value="{{$curr_bloc->_id}}" {{if $curr_bloc->_id == $bloc->_id }}selected="selected"{{/if}}>
          {{$curr_bloc->nom}}
        </option>
        {{/foreach}}
      </select>
    </td>
    <td>{{mb_label object=$filter field="salle_id"}}</td>
    <td>
      <select name="salle_id">
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
    <td>{{mb_label object=$filter field="_date_max"}}</td>
    <td class="date">{{mb_field object=$filter field="_date_max" form="bloc" canNull="false" register=true}} </td>
    <td>{{mb_label object=$filter field="_prat_id"}}</td>
    <td colspan="3">
      <select name="prat_id">
        <option value="0">&mdash; Tous les praticiens</option>
        {{foreach from=$listPrats item=curr_prat}}
        <option value="{{$curr_prat->user_id}}" {{if $curr_prat->user_id == $filter->_prat_id}}selected="selected"{{/if}}>
          {{$curr_prat->_view}}
        </option>
        {{/foreach}}
      </select>
    </td>
  </tr>
  <tr>
    <td>{{mb_label object=$filter field="codes_ccam"}}</td>
    <td>
      {{mb_field object=$filter field="codes_ccam" canNull="true" size="20"}}
      <button class="search" type="button" onclick="CCAMSelector.init()">Rechercher</button>   
      <script type="text/javascript">
        CCAMSelector.init = function(){
          this.sForm = "bloc";
          this.sView = "codes_ccam";
          this.sChir = "_chir";
          this.sClass = "_class_name";
          this.pop();
        }
      </script>
      
    </td>
    <td>{{mb_label object=$filter field="_specialite"}}</td>
    <td colspan="3">
      <select name="discipline_id">
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
      <label><input type="checkbox" onclick="drawGraphs(this.checked)" checked="checked" /> Légende</label>
    </td>
  </tr>
</table>
</form>

<select id="graph-activite-zoom-date" onchange="zoomGraphIntervention($V(this))" style="display: none;">
  <option selected="selected" disabled="disabled">&ndash; Vue sur un mois &ndash;</option>
</select>

{{foreach from=$graphs item=graph key=key}}
	<div style="width: 480px; height: 350px; float: left; margin: 1em;" id="graph-{{$key}}"></div>
{{/foreach}}