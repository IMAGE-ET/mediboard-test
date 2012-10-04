<script>
  Main.add(function(){
    var form = getForm("tools-{{$_tool_class}}-{{$_tool}}");
    form.count.addSpinner({min: 1});
  });
  
  function next{{$_tool}}(){
    var form = getForm("tools-{{$_tool_class}}-{{$_tool}}");
  
    if (!$V(form["continue"])) {
      return;
    }
  
    form.onsubmit();
  }
</script>

<form name="tools-{{$_tool_class}}-{{$_tool}}" method="get" action="?" 
  onsubmit="return onSubmitFormAjax(this, null, 'tools-{{$_tool_class}}-{{$_tool}}')">
  <input type="hidden" name="m" value="eai" />
  <input type="hidden" name="a" value="ajax_tools" />
  <input type="hidden" name="tool" value="{{$_tool}}" />
  <input type="hidden" name="suppressHeaders" value="1" />
  
  <table class="main form">
    <tr>
      <th>{{mb_label class=CExchangeDataFormat field="_date_min"}}</th>
      <td>
        <input class="date notNull" type="hidden" name="date_min" value="{{$exchange->_date_min|mbDate}}" />
        <script type="text/javascript">
          Main.add(function () {
            Calendar.regField(getForm('tools-{{$_tool_class}}-{{$_tool}}').date_min);
          });
        </script>
      </td>
    </tr>
    <tr>
      <th>{{mb_label class=CExchangeDataFormat field="_date_max"}}</th>
      <td>
        <input class="date notNull" type="hidden" name="date_max" value="{{$exchange->_date_max|mbDate}}" /> <br />
        <script type="text/javascript">
          Main.add(function () {
            Calendar.regField(getForm('tools-{{$_tool_class}}-{{$_tool}}').date_max);
          });
        </script>
      </td>
    </tr>
    <tr>
      <th></th>
      <td>
        <select name="receiver_id">
          {{foreach from=$receivers item=_receivers key=_class}}
            <optgroup label="{{tr}}{{$_class}}{{/tr}}">
              {{foreach from=$_receivers item=_receiver}}
                <option value="{{$_receiver->_guid}}">{{tr}}{{$_receiver}}{{/tr}}</option>
              {{/foreach}}
            </optgroup>
          {{/foreach}}
        </select>
      </td>
    </tr>
    <tr>
      <th>Séjours sans échanges</th>
      <td>
        <label><input type="radio" name="without_exchanges" value="1" checked /> Oui</label>
        <label><input type="radio" name="without_exchanges" value="0" /> Non</label>
      </td>
    </tr>
    <tr>
      <th>Séjours en pré-ad</th>
      <td>
        <label><input type="radio" name="only_pread" value="1" checked /> Oui</label>
        <label><input type="radio" name="only_pread" value="0" /> Non</label>
      </td>
    </tr>
    <tr>
      <th>Nombre</th>
      <td><input type="text" name="count" value="30" size="3" title="Nombre d'échanges à traiter" /></td>
    </tr>
    <tr>
      <th>Automatique</th>
      <td><input type="checkbox" name="continue" value="1" title="Automatique" /></td>
    </tr>
    <tr>
      <td colspan="2">
        <button type="submit" class="change">{{tr}}CEAI-tools-{{$_tool_class}}-{{$_tool}}-button{{/tr}}</button>
      </td>
    </tr>
  </table>
</form>