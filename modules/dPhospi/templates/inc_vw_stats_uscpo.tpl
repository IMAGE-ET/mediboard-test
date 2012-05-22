<script type="text/javascript">
  var graph = {{$graph|@json}};
  
  Main.add(function(){
    Flotr.draw($('graph_uscpo'), graph.series, graph.options);
  });
</script>

{{mb_include module=hospi template=inc_form_stats type=uscpo}}

<table class="main">
  <tr>
    <td class="narrow">
      <div style="width: 640px; height: 480px; float: left; margin: 1em;" id="graph_uscpo"></div>
    </td>
    <td>
      <select name="select_date" onchange="listOperations(this.value, '{{$service_id}}')">
        <option value="&mdash">&mdash; Choisissez une date</option>
        {{foreach from=$dates item=_date}}
          <option value="{{$_date}}">{{$_date|date_format:$conf.date}}</option>
        {{/foreach}}
      </select>
        <div id="list_operations_uscpo"></div>
    </td>
  </tr>
