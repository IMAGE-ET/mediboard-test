</div>
    </td>
  </tr>
</table>

{{if $debugMode && !$offline}}
  {{mb_include template=../../../style/mediboard/templates/performance}}
{{/if}}

<script type="text/javascript">
  Main.add(function(){
    __pageLoad = ((new Date).getTime() - __loadStart)+"ms";
  });
</script>
  
</body>
</html>