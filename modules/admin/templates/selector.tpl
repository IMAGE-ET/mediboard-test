<!-- $Id$ -->

<script type="text/javascript">
  function setClose(){
    var list = document.frmSelector.list;
    var value = list.options[list.selectedIndex].value;
    var text  = list.options[list.selectedIndex].text ;
    window.opener.{{$callback}}(value, text);
    window.close();
  }
</script>

<form name="frmSelector">

<table class="form">
  <tr>
    <th class="category">
      {{tr}}Select{{/tr}} {{tr}}{{$title}}{{/tr}}
    </th>
  </tr>
  
  <tr>
    <td>
      <select name="list" size="8">
        {{html_options options=$list}}
      </select>
    </td>
  </tr>

  <tr>
    <td class="button">
      <input type="button" class="button" value="{{tr}}cancel{{/tr}}" onclick="window.close()" />
      <input type="button" class="button" value="{{tr}}select{{/tr}}" onclick="setClose()" />
    </td>
  </tr>
</table>

</form>