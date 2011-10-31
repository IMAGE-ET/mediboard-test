<script type="text/javascript">
  updateList = function() {
    
  };
</script>
<table class="tbl">
  <tr>
    <th class="category">Id</th>
    <th class="category"><button type="button" class="notext change" onclick="updateList();" style="float: right;"></button>Nom</th>
  </tr>
  {{foreach from=$list item=_compte_rendu}}
    <tr>
      <td>
        {{$_compte_rendu->_id}}
      </td>
      <td>
      {{$_compte_rendu->nom}}</td>
    </tr>
  {{/foreach}}
</table>