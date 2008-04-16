<tr>
  <td>{{$stock->_view}}</td>
  <td id="stock-{{$stock->_id}}-bargraph">{{include file="inc_bargraph.tpl"}}</td>
  <td>
    {{if $ajax}}
    <script type="text/javascript">
      prepareForm(document.forms['form-stock-out-stock-{{$stock->_id}}']);
    </script>
    {{/if}}
    <form name="form-stock-out-stock-{{$stock->_id}}" action="?" method="post">
      <input type="hidden" name="m" value="{{$m}}" />
      <input type="hidden" name="tab" value="{{$tab}}" />
      <input type="hidden" name="dosql" value="do_stock_out_aed" />
      <input type="hidden" name="del" value="0" />
      <input type="hidden" name="stock_id" value="{{$stock->_id}}" />
      <input type="hidden" name="function_id" value="" />
      <input type="hidden" name="date" value="now" />
      <input type="hidden" name="_do_stock_out" value="1" />
      
      {{assign var=id value=$stock->_id}} 
      {{mb_field object=$stock field=quantity form="form-stock-out-stock-$id" increment=1 size=3}}
      
      <button type="button" class="remove" onclick="stockOut(this.form, 1);">Sortie</button>
      <button type="button" class="add" onclick="stockOut(this.form, -1);">Retour</button>

      <input type="text" name="code" value="" />
    </form>
  </td>
</tr>
