{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage bloodSalvage
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{mb_include_script module="dPplanningOp" script="ccam_selector"}}

<script type="text/javascript">
var oCcamField = null,
		filterForm;

function yTickFormatter(y) {
	return parseInt(y).toString();
}

function drawGraphs(data) {
  var container = $("graphs").update("");
  
  $H(data).each(function(pair) {
    var graph = new Element("div", {id:"stats-"+pair.key, style:"width: 600px; height: 300px; margin: auto;"});
    container.insert(graph);
    
    var data = pair.value;
    Flotr.draw(
      $('stats-'+pair.key),
      data.series, Object.extend({
        yaxis: {tickFormatter: yTickFormatter}
      }, data.options)
    );
  });
}

function updateGraphs(form){
  WaitingMessage.cover($("graphs"));
  
  var url = new Url("bloodSalvage", "httpreq_json_stats");
  url.addFormData(form);
  url.requestJSON(drawGraphs);
  return false;
}

Main.add(function () {
  filterForm = getForm('stats-filter');

  updateGraphs(filterForm);
  
  oCcamField = new TokenField(filterForm["filters[codes_ccam]"], { 
    onChange : updateTokenCcam
  });
  
  updateTokenCcam($V(filterForm["filters[codes_ccam]"]));
  
  switchMode("{{$mode}}");
});

function switchMode(mode) {
  var type = (mode === "comparison") ? "radio" : "checkbox";
  
  $$(".comparison input").each(function(input){
    input.hide();
    input.disabled = true;
  });
  
  /*$$(".comparison input[type="+type+"]").each(function(input){
    input.show();
    input.disabled = null;
  });
  
  if ((type == "radio") && ($$(".comparison input:checked").length < 2)) {
    $$(".comparison input[name=comparison_left]")[0].checked = true;
    $$(".comparison input[name=comparison_right]")[1].checked = true;
  }*/
}

CCAMSelector.init = function() {
  this.sForm  = "stats-filter";
  this.sView  = "_code_ccam";
  this.sChir  = "filters[chir_id]";
  this.sClass = "_class_name";
  this.pop();
}

function updateTokenCcam(v) {
  var i, codes = v.split("|").without("");
  for (i = 0; i < codes.length; i++) {
    codes[i] += '<button class="remove notext" type="button" onclick="oCcamField.remove(\''+codes[i]+'\')"></button>';
  }
  $("list_codes_ccam").update(codes.join(", "));
  $V(filterForm._code_ccam, '');
}
</script>

<form name="stats-filter" action="?" method="get" onsubmit="return updateGraphs(this)">
  <table class="main form">
    <tr>
      <th><label for="months_count">Depuis</label></th>
      <td>
        <select name="months_count">
          <option value="24" {{if $months_count == 24}}selected="selected"{{/if}}>24 mois</option>
          <option value="12" {{if $months_count == 12}}selected="selected"{{/if}}>12 mois</option>
          <option value="6" {{if $months_count == 6}}selected="selected"{{/if}}>6 mois</option>
        </select>
      </td>
      
      <th>
        <label for="filters[code_asa]">Code ASA</label>
        <span class="comparison">
          <input type="checkbox" value="code_asa" name="comparison[]" />
          <input type="radio" value="code_asa" name="comparison_left" />
          <input type="radio" value="code_asa" name="comparison_right" />
        </span>
      </th>
      <td>
        <select name="filters[code_asa]">
          <option value="">&mdash; Tous</option>
          {{foreach from=$fields.codes_asa item=code}}
          <option value="{{$code}}" {{if $code == $filters.code_asa}}selected="selected"{{/if}}>{{$code}}</option>
          {{/foreach}}
        </select>
      </td>
    </tr>
    <tr>
      <th>
        <label for="filters[chir_id]">Chirurgien</label>
        <span class="comparison">
          <input type="checkbox" value="chir_id" name="comparison[]" />
          <input type="radio" value="chir_id" name="comparison_left" />
          <input type="radio" value="chir_id" name="comparison_right" />
        </span>
      </th>
      <td>
        <select name="filters[chir_id]">
          <option value="">&mdash; Tous</option>
          {{foreach from=$fields.chir_id item=user}}
          <option value="{{$user->_id}}" {{if $filters.chir_id == $user->_id}}selected="selected"{{/if}} style="border-left: #{{$user->_ref_function->color}} 5px solid;">{{$user->_view}}</option>
          {{/foreach}}
        </select>
      </td>
      
      <th>
        <label for="_code_ccam">Codes CCAM</label>
        <span class="comparison">
          <input type="checkbox" value="_code_ccam" name="comparison[]" />
          <input type="radio" value="_code_ccam" name="comparison_left" />
          <input type="radio" value="_code_ccam" name="comparison_right" />
        </span>
      </th>
      <td>
        <input type="hidden" name="filters[codes_ccam]" value="{{$filters.codes_ccam}}" />
        <input type="hidden" name="_class_name" value="COperation" />
        <input type="text" name="_code_ccam" ondblclick="CCAMSelector.init()" size="10" value="" />
				<button class="search notext" type="button" onclick="CCAMSelector.init()">{{tr}}Search{{/tr}}</button>
        <button class="tick notext" type="button" onclick="oCcamField.add($V(this.form['_code_ccam']))">{{tr}}Add{{/tr}}</button>
      </td>
    </tr>
    
    <tr>
      <th>
        <label for="filters[anesth_id]">Anésthesiste</label>
        <span class="comparison">
          <input type="checkbox" value="anesth_id" name="comparison[]" />
          <input type="radio" value="anesth_id" name="comparison_left" />
          <input type="radio" value="anesth_id" name="comparison_right" />
        </span>
      </th>
      <td>
        <select name="filters[anesth_id]">
          <option value="">&mdash; Tous</option>
          {{foreach from=$fields.anesth_id item=user}}
          <option value="{{$user->_id}}" {{if $filters.anesth_id == $user->_id}}selected="selected"{{/if}} style="border-left: #{{$user->_ref_function->color}} 5px solid;">{{$user->_view}}</option>
          {{/foreach}}
        </select>
      </td>
      <th>Codes choisis :</th>
      <td id="list_codes_ccam"></td>
    </tr>
    <tr>
      <td colspan="4" class="button">
        <!--
        <label>
          Mode comparaison
          <input type="checkbox" onclick="switchMode(this.checked ? 'comparison' : '')" {{if $mode == "comparison"}} checked="checked" {{/if}} />
        </label>
        -->
        <button type="submit" class="search">Filtrer</button>
      </td>
    </tr>
  </table>
</form>

<div id="graphs"></div>
