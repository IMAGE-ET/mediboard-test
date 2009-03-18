{{mb_include_script module="dPplanningOp" script="ccam_selector"}}

<script type="text/javascript">
var series = {{$series|@json}};
var options = {{$options|@json}};

var oCcamField = null;
var graph;
var filterForm;

function drawGraph() {
  // Let's draw the graph
  graph = Flotr.draw(
    $('stats'),
    series, Object.extend({
      bars: {show:true, barWidth:0.5, fillOpacity: 0.6},
      mouse: {track: false},
      legend: {show: false, position: 'nw'},
      yaxis: {min: 0},
      HtmlText: false,
      spreadsheet: {
		  	show: true,
		  	tabGraphLabel: 'Graphique',
		  	tabDataLabel: 'Données',
        toolbarDownload: 'Télécharger le fichier CSV',
        toolbarSelectAll: 'Séléctionner le tableau'
		  }
    }, options)
  );
}

Main.add(function () {
  filterForm = getForm('stats-filter');

  drawGraph(series, options);
  
  oCcamField = new TokenField(filterForm["filters[codes_ccam]"], { 
    onChange : updateTokenCcam
  });
  
  updateTokenCcam($V(filterForm["filters[codes_ccam]"]));
});

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

<form name="stats-filter" action="?" method="get">
  <input type="hidden" name="m" value="{{$m}}" />
  <input type="hidden" name="tab" value="{{$tab}}" />
  
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
      
      <th><label for="filters[code_asa]">Code ASA</label>
      <td>
        <select name="filters[code_asa]">
          <option value="">&mdash; Tous</option>
          {{foreach from=$list_codes_asa item=code}}
          <option value="{{$code}}" {{if $code == $filters.code_asa}}selected="selected"{{/if}}>{{$code}}</option>
          {{/foreach}}
        </select>
      </td>
    </tr>
    <tr>
      <th><label for="filters[chir_id]">Chirurgien</label></th>
      <td>
        <select name="filters[chir_id]">
          <option value="">&mdash; Tous</option>
          {{foreach from=$list_chir item=user}}
          <option value="{{$user->_id}}" {{if $filters.chir_id == $user->_id}}selected="selected"{{/if}} style="border-left: #{{$user->_ref_function->color}} 5px solid;">{{$user->_view}}</option>
          {{/foreach}}
        </select>
      </td>
      
      <th><label for="_code_ccam">Codes CCAM</label></th>
      <td>
        <input type="hidden" name="filters[codes_ccam]" value="{{$filters.codes_ccam}}" />
        <input type="hidden" name="_class_name" value="COperation" />
        <input type="text" name="_code_ccam" ondblclick="CCAMSelector.init()" size="10" value="" />
        <button class="tick notext" type="button" onclick="oCcamField.add($V(this.form['_code_ccam']))">{{tr}}Add{{/tr}}</button>
      </td>
    </tr>
    
    <tr>
      <th><label for="filters[anesth_id]">Anésthesiste</label></th>
      <td>
        <select name="filters[anesth_id]">
          <option value="">&mdash; Tous</option>
          {{foreach from=$list_anesth item=user}}
          <option value="{{$user->_id}}" {{if $filters.anesth_id == $user->_id}}selected="selected"{{/if}} style="border-left: #{{$user->_ref_function->color}} 5px solid;">{{$user->_view}}</option>
          {{/foreach}}
        </select>
      </td>
      <th>Codes choisis :</th>
      <td id="list_codes_ccam"></td>
    </tr>
    <tr>
      <td colspan="4"><button type="submit" class="search">Filtrer</button></td>
    </tr>
  </table>

  <div style="text-align: center;">
    <div id="stats" style="width: 600px; height: 300px; margin: auto;"></div>
  </div>
</form>

