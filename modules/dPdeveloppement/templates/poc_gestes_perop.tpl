<script type="text/javascript" src="lib/flot/jquery.min.js"></script>
<script type="text/javascript">$.noConflict();</script>
<script type="text/javascript" src="lib/flot/jquery.flot.js"></script>
<script type="text/javascript" src="lib/flot/jquery.flot.symbol.min.js"></script>
<script type="text/javascript" src="lib/flot/jquery.flot.crosshair.min.js"></script>
<script type="text/javascript" src="lib/flot/jquery.flot.resize.min.js"></script>

<script type="text/javascript">
Main.add(function(){
  (function ($) {
    function generate(start, end, fn) {
        var res = [];
        for (var i = 0; i <= 50; ++i) {
            var x = start + i / 50 * (end - start);
            res.push([x/**478000+(new Date("2012-01-10 17:00:00")).getTime()*/, fn(x)]);
        }
        return res;
    }

    var data = [
        { data: generate(0, 10, function (x) { return Math.sqrt(x)}), yaxis:1, shadowSize: 0, color: "blue", label:"foo" },
        { data: generate(0, 10, function (x) { return Math.sin(x)}),  yaxis:2, shadowSize: 0, color: "green", label:"bar", points: { symbol: "diamond" } },
        { data: generate(0, 10, function (x) { return Math.cos(x)}),  yaxis:3, shadowSize: 0, color: "red", label:"baz", points: { symbol: "triangle" } }
    ]; 
    
    var ph = $("#placeholder");
    
    ph.bind("plothover", function (event, pos, item) {
      $$(".yAxis").each(function(axis){
        axis.style.fontWeight = "normal";
        axis.style.background = "#ccc";
      });
      
      if (item) {
        $$(".y"+item.series.yaxis.n+"Axis")[0].style.fontWeight="bold";
      }
    });

    var plot = $.plot(ph,
                      data,
                      {   //crosshair: { mode: "xy", lineWidth: 1 },
                          grid: {
                            hoverable: true,
                            //markings: [{ xaxis: { from: 4.2, to: 5.3 }, color: "#ccffcc"}],
                          },
                          series: { points: { show: true, radius: 3 } },
                          xaxes: [
                              { position: 'bottom'/*, mode: "time" */}
                          ],
                          
                          yaxes: [
                              { position: 'left', color: "blue", labelWidth: 20 },
                              { position: 'left', color: "green", labelWidth: 20 },
                              { position: 'left', color: "red", labelWidth: 20 }
                          ]
                      });
                      
                      
  })(jQuery);
});
</script>

<style type="text/css">
  .geste {
    border-bottom: 1px dotted #ccc;
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
     min-width: 2px;
  }
  
  .geste > div:hover .marking {
    content: "";
    display: inline-block;
  }
  
  .geste .label {
    padding: 1px 3px;
  }
  
  table.gestes {
    border: 1px solid #ccc;
    border-spacing: 0;
    border-collapse: collapse;
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
</style>

<div style="position: relative;">
<div id="placeholder" style="width:900px;height:300px;"></div>

<table class="main gestes" style="table-layout: fixed; width: 900px;">
  <col style="width: 100px;" />
  
  <tr>
    <th>Perfusions</th>
    <td>
      <div style="padding-left: 90px;" class="geste">
        <div style="width: 20px;">
          <div class="marking" style="width: 20px;"></div>
          <div class="label">geste 1</div>
        </div>
      </div>
      
      <div style="padding-left: 170px;" class="geste">
        <div>
          <div class="marking"></div>
          <div class="label">geste 2</div>
        </div>
      </div>
      
      <div style="padding-left: 290px;" class="geste">
        <div style="width: 160px; background: #fc6;">
          <div class="marking" style="width: 160px;"></div>
          <div class="label">geste 3</div>
        </div>
      </div>
    </td>
  </tr>
</table>
</div>
