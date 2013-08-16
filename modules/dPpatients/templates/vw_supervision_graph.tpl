{{mb_script module=patients script=supervision_graph}}
{{mb_script module=mediusers script=color_selector}}

<script type="text/javascript">
Main.add(function(){
  SupervisionGraph.list(SupervisionGraph.editGraph.curry({{$supervision_graph_id}}));
});
</script>

<table class="main layout">
  <tr>
    <td style="width: 15%" id="supervision-list"></td>
    <td id="supervision-graph-editor">&nbsp;</td>
  </tr>
</table>