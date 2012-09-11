<button class="new" onclick="editStockLocation(0)">
  {{tr}}CProductStockLocation-title-create{{/tr}}
</button>


<form name="edit_stock_location" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">
  <input type="hidden" name="dosql" value="do_stock_location_aed" />
  <input type="hidden" name="stock_location_id" value="{{$stock_location->_id}}" />
  <input type="hidden" name="group_id" value="{{$host_group_id}}" />
  <input type="hidden" name="del" value="0" />
  <table class="form">
    {{mb_include module=system template=inc_form_table_header object=$stock_location}}
    
    <tr>
      <th>{{mb_label object=$stock_location field="name"}}</th>
      <td>{{mb_field object=$stock_location field="name"}}</td>
    </tr>
    
    <tr>
      <th>{{mb_label object=$stock_location field="object_id"}}</th>
      <td>
        {{if $stock_location->_id && ($stock_location->_back.group_stocks|@count || $stock_location->_back.service_stocks|@count)}}
          <div class="small-info">
            Impossible de changer le type d'emplacement car il possède déjà des stocks
          </div>
        {{/if}}
        <select name="_type" {{if $stock_location->_id && ($stock_location->_back.group_stocks|@count || $stock_location->_back.service_stocks|@count)}}disabled="disabled"{{/if}}>
          <option value="" disabled="disabled"> &ndash; Choisir un type</option>
          {{foreach from=$types item=_type key=_label}}
            <optgroup label="{{$_label}}">
              {{foreach from=$_type item=_object}}
                <option value="{{$_object->_guid}}" {{if $_object->_guid == $stock_location->_type}}selected="selected"{{/if}}>{{$_object}}</option>
              {{/foreach}}
            </optgroup>
          {{/foreach}}
        </select>
      </td>
    </tr>
    <tr>
      <th>{{mb_label object=$stock_location field="desc"}}</th>
      <td>{{mb_field object=$stock_location field="desc"}}</td>
    </tr>
    <tr>
      <th>{{mb_label object=$stock_location field="position"}}</th>
      <td>{{mb_value object=$stock_location field="position"}}</td>
    </tr>
    <tr>
      <th>{{mb_label object=$stock_location field="_before"}}</th>
      <td>
        {{mb_field object=$stock_location field="_before" hidden=true}}
        <input type="text" name="_before_autocomplete_view" value="" />
        
        <script type="text/javascript">
          Main.add(function(){
            var form = getForm("edit_stock_location");
            var input = form._before_autocomplete_view;
            
            var url = new Url("dPstock", "httpreq_vw_related_locations");
            url.addParam("exclude_location_id", '{{$stock_location->_id}}');
            url.autoComplete(input, null, {
              minChars: 1,
              method: "get",
              select: "view",
              dropdown: true,
              callback: function(input, queryString){
                return queryString + "&owner_guid="+$V(input.form._type);
              },
              afterUpdateElement: function(field,selected){
                $V(field.form._before, selected.className.match(/[a-z]-(\d+)/i)[1]);
              }});
          });
        </script>
      </td>
    </tr>
    <tr>
      <td class="button" colspan="4">
        {{if $stock_location->_id}}
        <button class="modify" type="submit">{{tr}}Save{{/tr}}</button>
        <button type="button" class="trash" onclick="confirmDeletion(this.form,{typeName:'',objName:'{{$stock_location->_view|smarty:nodefaults|JSAttribute}}'})">
          {{tr}}Delete{{/tr}}
        </button>
        {{else}}
        <button class="submit" type="submit">{{tr}}Create{{/tr}}</button>
        {{/if}}
      </td>
    </tr>  
  </table>
</form>
  
{{if $stock_location->_id}}
<table class="main tbl">
  <tr>
    <th class="category" colspan="10">
      <button style="float: right;" class="print notext" onclick="new Url('dPstock','print_stock_location').addParam('stock_location_id','{{$stock_location->_id}}').popup()">
        {{tr}}Print{{/tr}}
      </button>
      Stocks à cet emplacement
    </th>
  </tr>
  {{foreach from=$stock_location->_back.group_stocks item=_stock}}
    <tr>
      <td>
        <strong onmouseover="ObjectTooltip.createEx(this, '{{$_stock->_guid}}')">
          {{$_stock}}
        </strong>
      </td>
      <td>{{$_stock->quantity}}</td>
      <td>{{mb_include module=stock template=inc_bargraph stock=$_stock}}</td>
    </tr>
  {{foreachelse}}
    <tr>
      <td colspan="10" class="empty">{{tr}}CProductStockGroup.none{{/tr}}</td>
    </tr>
  {{/foreach}}
  
  {{foreach from=$stock_location->_back.service_stocks item=_stock}}
    <tr>
      <td>
        <strong onmouseover="ObjectTooltip.createEx(this, '{{$_stock->_guid}}')">
          {{$_stock}}
        </strong>
      </td>
      <td>{{$_stock->quantity}}</td>
      <td>{{mb_include module=stock template=inc_bargraph stock=$_stock}}</td>
    </tr>
  {{foreachelse}}
    <tr>
      <td colspan="10" class="empty">{{tr}}CProductStockGroup.none{{/tr}}</td>
    </tr>
  {{/foreach}}
</table>
{{/if}}