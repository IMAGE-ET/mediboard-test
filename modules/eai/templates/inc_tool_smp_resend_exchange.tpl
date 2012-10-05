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

{{mb_script module="eai" script="action"}}

<form name="tools-{{$_tool_class}}-{{$_tool}}" method="get" action="?" 
  onsubmit="return onSubmitFormAjax(this, null, 'tools-{{$_tool_class}}-{{$_tool}}')">
  <input type="hidden" name="m" value="eai" />
  <input type="hidden" name="a" value="ajax_resend_exchange" />
  <input type="hidden" name="tool" value="{{$_tool}}" />
  <input type="hidden" name="suppressHeaders" value="1" />
  <input type="hidden" name="action" value="" />
  
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
      <th>ID départ</th>
      <td><input type="text" name="id_start" value="" size="6" title="Démarrer à l'ID ..." /></td>
    </tr>
    <tr>
      <th>NDA (si plusieurs séparer par des |)</th>
      <td><input type="text" name="list_nda" value="" size="25" placeholder="NDA des séjours à rejouer" /></td>
    </tr>
    <tr>
      <th>{{tr}}CInteropReceiver{{/tr}}</th>
      <td>
        <select name="receiver_guid">
          {{foreach from=$receivers item=_receivers key=_class}}
            <optgroup label="{{tr}}{{$_class}}{{/tr}}">
              {{foreach from=$_receivers item=_receiver}}
                <option value="{{$_receiver->_guid}}">{{$_receiver}}</option>
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
      <th>{{tr}}CMovement{{/tr}}</th>
      <td>
        <select name="movement_type">
          {{foreach from=$movement->_specs.movement_type->_locales key=_type item=_locale}}
            <option value="{{$_type}}">{{$_locale}}</option>
          {{/foreach}}
        </select>
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
        <button type="button" class="new" onclick="$V(this.form.action, 'start'); this.form.onsubmit()">
          {{tr}}CEAI-tools-{{$_tool_class}}-{{$_tool}}-button{{/tr}}
        </button>
        <button type="button" class="change" onclick="$V(this.form.action, 'retry'); this.form.onsubmit()">
          {{tr}}Retry{{/tr}}      
        </button>
        <button type="button" class="tick" onclick="$V(this.form.action, 'continue'); this.form.onsubmit()">
          {{tr}}Continue{{/tr}}      
        </button>
      </td>
    </tr>
  </table>
</form>