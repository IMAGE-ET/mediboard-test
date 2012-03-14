</div>
    </td>
  </tr>
</table>

{{if $debugMode && !$offline}}
  {{mb_include style=mediboard template=performance}}
{{/if}}

<script type="text/javascript">
  Main.add(function(){
    __pageLoad = ((new Date).getTime() - __loadStart)+"ms";
  });
</script>
  
</body>
</html>