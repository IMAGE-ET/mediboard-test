{{if $line instanceof CPrescriptionLineMedicament && $conf.pharmacie.ask_stock_location_administration}}
  {{if $line->_ref_stocks_service|@count == 0}}
    <div class="small-warning">
      Il n'existe pas de stock pour ce produit
    </div>
  {{else}}
    {{mb_label object=$adm field=stock_id}}
    <select name="stock_id" class="{{$adm->_props.stock_id}}">
      {{if $line->_ref_stocks_service|@count > 1}}
        <option value=""> &ndash; {{tr}}Chose{{/tr}} </option>
      {{/if}}
      
      {{* <optgroup label="{{tr}}CService{{/tr}}"> *}}
      
        {{foreach from=$line->_ref_stocks_service item=_stock}}
          <option value="{{$_stock->_id}}">{{$_stock->_ref_location}}</option>
        {{/foreach}}
        
      {{* </optgroup> *}}
      
      {{* <optgroup label="{{tr}}CGroups{{/tr}}">
        {{foreach from=$line->_ref_stocks_group item=_stock}}
          <option value="{{$_stock->_id}}">{{$_stock->_ref_location}}</option>
        {{/foreach}}
      </optgroup> *}}
    </select>
  {{/if}}
{{/if}}