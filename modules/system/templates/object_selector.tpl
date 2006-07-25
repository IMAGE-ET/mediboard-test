<script type="text/javascript">

function setClose(key, val){
  window.opener.setData(key,val);
  window.close();
}
</script>


<table class="tbl">
  <tr>
    <th align="center" colspan="2">Résultat de la recherche</th>
  </tr>
  
  {{foreach from=$list item=curr_list}}
    <tr>
      <td>{{$curr_list->_view}}</td>     
      <td class="button"><button type="button" onclick="setClose({{$curr_list->$key}}, '{{$curr_list->_view|escape:javascript}}')">selectionner</button></td>
    </tr>
  {{/foreach}}
</table>