<script type="text/javascript">
var form;
Main.add(function () {
  form = getForm("test");
  form.text_1.mask("~(99) 99 99 99 99", {placeholder: '_', completed: function(s) {alert(s.rawvalue)} });
});

</script>

{{if !$dialog}}
<a href="?m={{$m}}&amp;a={{$tab}}&amp;dialog=1">Lancer cette page sans les menus</a>
{{else}}
<a href="?m={{$m}}&amp;tab={{$a}}">Lancer cette page avec les menus</a>
{{/if}}

<form name="test" action="?" method="get" onsubmit="if (checkForm(this)) {Console.debug('form.submit()');} return false;">
  <input type="hidden" name="m" value="{{$m}}" />
  <input type="hidden" name="tab" value="{{$tab}}" />
  
  <table>
    <tr>
      <td>
        <select name="select_tree_1" class="select-tree" onchange="Console.debug(this.value+':'+this.options[this.selectedIndex].text);">
          <option value="1">Thomas</option>
          <option value="2" >Romain</option>
          <option value="11" >123</option>
          <optgroup label="Salariés">
            <option value="3" >Alexis</option>
            <option value="4" >Fabien</option>
          </optgroup>
          <optgroup label="Stagiaires">
            <option value="8"  selected="selected">Alexandre</option>
            <option value="9" ></option>
            <option value="10" >encore un</option>
          </optgroup>
        </select>
      </td>
    </tr>
    <tr>
      <td><input type="text" name="text_1" /> ~(99) 99 99 99 99</td>
    </tr>
    <tr>
      <td>
        <input type="text" name="text_2" value="123456789"/>
        <button type="button" name="button_1" onclick="Console.debug($(this.form.text_2).caret(3, 6, 'toto'))">Caret</button>
      </td>
    </tr>

    
    <tr>
      <td><button type="submit" name="submit_1">OK</button></td>
    </tr>
  </table>
</form>