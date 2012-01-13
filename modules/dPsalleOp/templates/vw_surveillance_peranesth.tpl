
<script type="text/javascript">
Main.add(function(){
  (function ($) {
    var data = {{$data|@json}}; 
    
    var ph = $("#placeholder");
    
    ph.bind("plothover", function (event, pos, item) {
      /*$$(".yAxis").each(function(axis){
        axis.style.fontWeight = "normal";
        axis.style.background = "#ccc";
      });
      
      if (item) {
        $$(".y"+item.series.yaxis.n+"Axis")[0].style.fontWeight="bold";
      }*/
     
      if (item) {
        var key = item.dataIndex+"-"+item.seriesIndex;
        if (previousPoint != key) {
          previousPoint = key;
          $("#flot-tooltip").remove();
          
          var x = item.datapoint[0],
              y = item.datapoint[1];
          
          var date = new Date();
          date.setTime(x);
          
          var contents = 
          "<big style='font-weight:bold'>"+y+" "+item.series.unit+"</big>"+
          "<hr />"+item.series.label+"<br />" + printf("%02d:%02d:%02d", date.getUTCHours(), date.getUTCMinutes(), date.getUTCSeconds());
          
          $$("body")[0].insert(DOM.div({className: "tooltip", id: "flot-tooltip"}, contents).setStyle({
            position: 'absolute',
            top:  item.pageY + 5 + "px",
            left: item.pageX + 5 + "px"
          }));
        }
      }
      else {
        $("#flot-tooltip").remove();
        previousPoint = null;            
      }
    });

    var plot = $.plot(ph, data, {
      grid: { hoverable: true, markings: [
        // Debut op
        {xaxis: {from: 0, to: {{$time_debut_op}}}, color: "rgba(0,0,0,0.1)"},
        {xaxis: {from: {{$time_debut_op}}, to: {{$time_debut_op+1000}}}, color: "black"},
        
        // Fin op
        {xaxis: {from: {{$time_fin_op}}, to: Number.MAX_VALUE}, color: "rgba(0,0,0,0.1)"},
        {xaxis: {from: {{$time_fin_op}}, to: {{$time_fin_op+1000}}}, color: "black"}
      ] },
      series: { points: { show: true, radius: 3 } },
      xaxes: {{$xaxes|@json}},
      yaxes: {{$yaxes|@json}}
    });
                      
                      
  })(jQuery);
});
</script>

<style type="text/css">
  .geste {
    margin-bottom: -5px;
  }
  
  .geste > div {
    cursor: pointer;
    white-space: nowrap;
    display: block;
    width: 2px;
    background: #bbb;
  }
  
  .geste > div .marking {
    position: absolute; 
    top: 0; 
    bottom: 0; 
    display: none;
    background: rgba(0,0,0,0.1);
    -ms-filter:"progid:DXImageTransform.Microsoft.gradient(startColorstr=#10000000,endColorstr=#10000000)";
    min-width: 2px;
    border: 1px solid #ccc;
    border-top: none;
    border-bottom: none;
    margin: 0 -1px;
  }
  
  .geste > div .marking > span {
    position: absolute; 
    top: 300px;
    background: #eee;
    padding: 4px;
    border: 1px solid #ccc;
    border-left: none;
  }
  
  .geste > div:hover .marking {
    display: block;
  }
  
  .geste .label {
    padding: 0 3px;
  }
  
  table.gestes {
    border: 1px solid #ccc;
    border-spacing: 0;
    border-collapse: collapse;
    empty-cells: hide;
  }
  
  table.gestes td,
  table.gestes th {
    border: 1px solid #ccc;
    padding: 0;
  }
  
  table.gestes th {
    vertical-align: middle;
    background: #eee;
  }
  
  table.gestes td {
    padding-bottom: 10px;
  }
  
  .yaxis-labels > div {
    display: inline-block;
    text-align: center;
    font-size: 10px;
    width: 33px;
  }
  
  .yaxis-labels .symbol {
    font-size: 16px;
  }
</style>

{{assign var=width value=0}}

<div style="position: relative;">
<div class="yaxis-labels">
  {{foreach from=$yaxes|@array_reverse item=_yaxis}}
    {{if $_yaxis.used}}
      {{assign var=width value=$width+33.3}}
      
      <div style="color: {{$_yaxis.color}};">
        {{$_yaxis.label}}
        <div class="symbol">{{$_yaxis.symbolChar|smarty:nodefaults}}</div>
      </div>
    {{/if}}
  {{/foreach}}
</div>
<div id="placeholder" style="width:900px;height:300px;"></div>

<table class="main gestes" style="table-layout: fixed; width: 900px;">
  <col style="width: {{$width}}px;" />
  
  {{foreach from=$gestes key=_label item=_gestes}}
    <tr>
      <th>{{tr}}{{$_label}}{{/tr}}</th>
      <td>
      {{foreach from=$_gestes item=_geste}}
        <div style="padding-left: {{$_geste.position}}%; {{if $_geste.alert}} color: red; {{/if}}" class="geste">
          <div>
            <div class="marking">
              <span>{{$_geste.datetime|date_format:$conf.datetime}}</span>
            </div>
            <div class="label">{{$_geste.label}}</div>
          </div>
        </div>
      {{/foreach}}
      </td>
    </tr>
  {{/foreach}}
</table>
</div>
