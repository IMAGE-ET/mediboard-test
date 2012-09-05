{{mb_script module=admin script=view_access_token}}

<script type="text/javascript">
Main.add(function(){
  ViewAccessToken.list();
  ViewAccessToken.edit({{$token_id}});
});
</script>

<table class="main layout">
  <tr>
    <td id="token-list" style="width: 50%"> </td>
    <td id="token-edit"> </td>
  </tr>
</table>
