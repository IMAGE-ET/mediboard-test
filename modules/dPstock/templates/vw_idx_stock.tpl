{{mb_include_script module=dPstock script=product_selector}}
{{mb_include_script module=dPstock script=filter}}

<script type="text/javascript">
function pageMain() {
  filterFields = ["category_id", "keywords", "only_ordered_stocks"];
  stocksFilter = new Filter("filter-stocks", "{{$m}}", "httpreq_vw_stocks_list", "list-stocks", filterFields);
  stocksFilter.submit();
  
  var map = $("dents-schema-map");
  var image = $("dents-schema-image");
  var oMenu = $("dents-schema-menu");
  var alpha = 0.3;
  
  retFalse = function (e) {
    var sel;
    if (window.getSelection) {
      sel = window.getSelection();
      if(sel && sel.removeAllRanges) sel.removeAllRanges(); 
    }
    else if(document.selection && document.selection.empty){
      document.selection.empty();
    }
  }
  
  var disc = null;
  
	var x, y, r = null;
	var states = [null, 'bridge', 'pivot', 'mobile', 'appareil'];
	var selectedDent = null;
	
	var oHover = new Element('div');
  oHover.addClassName('dent');
  oHover.addClassName('hover');
  oHover.hide();
  image.insert({before: oHover});
  
  oMenu.addClassName('dent-menu');
  oMenu.size = states.length;
  states.each (function (o) {
    var oOption = new Element('option');
    oOption.value = o;
    oOption.text = o;
    oOption.addClassName(o);
    oMenu.insert(oOption);
  });
  oMenu.hide();
  

  map.childElements().each(
    function (o) {
      disc = o.coords.split(',');
      x = parseInt(disc[0]);
      y = parseInt(disc[1]);
      r = parseInt(disc[2]);
      
      var oDent = new Element('div');
      oDent.addClassName('dent');
		  image.insert({before: oDent});
			oDent.setStyle({
			  marginTop: y-r+'px',
			  marginLeft: x-r+'px',
			  width: r*2+'px',
			  height: r*2+'px'
			});
			oDent.setOpacity(0);
			
			oDent.id = o.id;
			o.id = null;
      
      oDent.onmouseover = function (e) {
	      if (!selectedDent) {
				  oHover.clonePosition(this);
				  oHover.setStyle({
				   top: this.cumulativeOffset().top - 2 + 'px',
				   left: this.cumulativeOffset().left - 2 + 'px'
				  });
				  oHover.show();
				}
      }
      
      oDent.onmouseout = function (e) {
        if (!selectedDent) {
          oHover.hide();
        }
      }
      
      oDent.onclick = function (e) {
        if (!selectedDent) {
	        /*for (var i = 0; i < states.length; i++) {
	          if (this.hasClassName(states[i])) {
	            this.className = 'dent';
	            this.addClassName(states[(i+1) % states.length]);
	          }
	        }*/
	        selectedDent = this;
	        oMenu.setStyle({
	         top: this.cumulativeOffset().top + 'px',
	         left: this.cumulativeOffset().left + this.getWidth() + 4 + 'px'
	        });
	        oMenu.setValue($w(selectedDent.className)[$w(selectedDent.className).length-1]);
	        oMenu.show();
        }
      }
      
      oMenu.onchange = function (e) {
        selectedDent.className = 'dent';
        if (this.value) {
          selectedDent.addClassName(this.value);
        }
        selectedDent.setOpacity(alpha);
        selectedDent = null;
        this.selectedIndex = -1;
        this.hide();
        oHover.hide();
      }
      
      oDent.ondblclick = retFalse;
    }
  );
}
</script>
<select id="dents-schema-menu"></select>

<div id="dents-schema-container">
<img name="chicos" src="images/pictures/dents.png" width="503" height="420" border="0" id="dents-schema-image" usemap="#dents-schema-map" alt="" />
</div>
<map name="dents-schema-map" id="dents-schema-map">
<area shape="circle" coords="164,52, 11" href="#1" alt="" id="dent_11" />
<area shape="circle" coords="145,63, 11" href="#1" alt="" id="dent_12" />
<area shape="circle" coords="127,74, 12" href="#1" alt="" id="dent_13" />
<area shape="circle" coords="118,93, 12" href="#1" alt="" id="dent_14" />
<area shape="circle" coords="109,112, 13" href="#1" alt="" id="dent_15" />
<area shape="circle" coords="103,137, 17" href="#1" alt="" id="dent_16" />
<area shape="circle" coords="99,165, 16" href="#1" alt="" id="dent_17" />
<area shape="circle" coords="98,193, 15" href="#1" alt="" id="dent_18" />
<area shape="circle" coords="185,52, 11" href="#1" alt="" id="dent_21" />
<area shape="circle" coords="204,63, 11" href="#1" alt="" id="dent_22" />
<area shape="circle" coords="222,74, 12" href="#1" alt="" id="dent_23" />
<area shape="circle" coords="231,93, 12" href="#1" alt="" id="dent_24" />
<area shape="circle" coords="240,113, 13" href="#1" alt="" id="dent_25" />
<area shape="circle" coords="246,137, 17" href="#1" alt="" id="dent_26" />
<area shape="circle" coords="249,165, 16" href="#1" alt="" id="dent_27" />
<area shape="circle" coords="251,193, 15" href="#1" alt="" id="dent_28" />
<area shape="circle" coords="183,375, 9" href="#1" alt="" id="dent_31" />
<area shape="circle" coords="198,368, 9" href="#1" alt="" id="dent_32" />
<area shape="circle" coords="212,357, 11" href="#1" alt="" id="dent_33" />
<area shape="circle" coords="225,341, 11" href="#1" alt="" id="dent_34" />
<area shape="circle" coords="234,322, 12" href="#1" alt="" id="dent_35" />
<area shape="circle" coords="243,298, 18" href="#1" alt="" id="dent_36" />
<area shape="circle" coords="247,269, 16" href="#1" alt="" id="dent_37" />
<area shape="circle" coords="251,241, 15" href="#1" alt="" id="dent_38" />
<area shape="circle" coords="166,375, 9" href="#1" alt="" id="dent_41" />
<area shape="circle" coords="151,367, 9" href="#1" alt="" id="dent_42" />
<area shape="circle" coords="137,357, 11" href="#1" alt="" id="dent_43" />
<area shape="circle" coords="124,342, 11" href="#1" alt="" id="dent_44" />
<area shape="circle" coords="114,323, 12" href="#1" alt="" id="dent_45" />
<area shape="circle" coords="106,298, 18" href="#1" alt="" id="dent_46" />
<area shape="circle" coords="102,269, 16" href="#1" alt="" id="dent_47" />
<area shape="circle" coords="97,242, 15" href="#1" alt="" id="dent_48" />
<area shape="circle" coords="366,133, 7" href="#1" alt="" id="dent_51" />
<area shape="circle" coords="355,139, 8" href="#1" alt="" id="dent_52" />
<area shape="circle" coords="346,150, 9" href="#1" alt="" id="dent_53" />
<area shape="circle" coords="338,166, 11" href="#1" alt="" id="dent_54" />
<area shape="circle" coords="333,185, 12" href="#1" alt="" id="dent_55" />
<area shape="circle" coords="379,133, 7" href="#1" alt="" id="dent_61" />
<area shape="circle" coords="390,139, 8" href="#1" alt="" id="dent_62" />
<area shape="circle" coords="399,150, 9" href="#1" alt="" id="dent_63" />
<area shape="circle" coords="405,166, 11" href="#1" alt="" id="dent_64" />
<area shape="circle" coords="411,185, 12" href="#1" alt="" id="dent_65" />
<area shape="circle" coords="378,290, 6" href="#1" alt="" id="dent_71" />
<area shape="circle" coords="387,284, 7" href="#1" alt="" id="dent_72" />
<area shape="circle" coords="398,274, 8" href="#1" alt="" id="dent_73" />
<area shape="circle" coords="405,262, 8" href="#1" alt="" id="dent_74" />
<area shape="circle" coords="413,246, 10" href="#1" alt="" id="dent_75" />
<area shape="circle" coords="367,290, 6" href="#1" alt="" id="dent_81" />
<area shape="circle" coords="357,284, 7" href="#1" alt="" id="dent_82" />
<area shape="circle" coords="346,274, 8" href="#1" alt="" id="dent_83" />
<area shape="circle" coords="339,261, 8" href="#1" alt="" id="dent_84" />
<area shape="circle" coords="330,247, 10" href="#1" alt="" id="dent_85" />
</map>
<table class="main">
  <tr>
    <td class="halfPane" rowspan="3">

      <form name="filter-stocks" action="?" method="post" onsubmit="return stocksFilter.submit('keywords');">
        <input type="hidden" name="m" value="{{$m}}" />
        
        <select name="category_id" onchange="stocksFilter.submit();">
          <option value="0" >&mdash; {{tr}}CProductCategory.all{{/tr}} &mdash;</option>
        {{foreach from=$list_categories item=curr_category}}
          <option value="{{$curr_category->category_id}}" {{if $category_id==$curr_category->_id}}selected="selected"{{/if}}>{{$curr_category->name}}</option>
        {{/foreach}}
        </select>
        
        <input type="text" name="keywords" value="" />
        <button type="button" class="search" onclick="stocksFilter.submit('keywords');">{{tr}}Filter{{/tr}}</button>
        <button type="button" class="cancel notext" onclick="stocksFilter.empty();"></button><br />
        
        <input type="checkbox" name="only_ordered_stocks" onchange="stocksFilter.submit();" />
        <label for="only_ordered_stocks">Seulement les stocks en cours de réapprovisionnement</label>
      </form>
      
      <div id="list-stocks"></div>
    </td>
    
    <!-- Edit/New Stock form -->
    <td class="halfPane">
      <a class="buttonnew" href="?m={{$m}}&amp;tab=vw_idx_stock&amp;stock_id=0">
        {{tr}}CProductStock.create{{/tr}}
      </a>
      <form name="edit_stock" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">
      <input type="hidden" name="dosql" value="do_stock_aed" />
      <input type="hidden" name="stock_id" value="{{$stock->_id}}" />
      <input type="hidden" name="group_id" value="{{$g}}" />
      <input type="hidden" name="del" value="0" />
      <table class="form">
        {{if $stock->_id}}
        <th class="title modify" colspan="2">{{tr}}CProductStock.modify{{/tr}} {{$stock->_view}}</th>
        {{else}}
        <th class="title" colspan="2">{{tr}}CProductStock.create{{/tr}}</th>
        {{/if}}
        <tr>
          <th>{{mb_label object=$stock field="quantity"}}</th>
          <td>{{mb_field object=$stock field="quantity" form="edit_stock" increment="1" min=0}}</td>
        </tr>
        <tr>
          <th>{{mb_label object=$stock field="product_id"}}</th>
          <td class="readonly">
            <input type="hidden" name="product_id" value="{{$stock->product_id}}" class="{{$stock->_props.product_id}}" />
            <input type="text" name="product_name" value="{{$stock->_ref_product->name}}" size="30" readonly="readonly" ondblclick="ProductSelector.init()" />
            <button class="search" type="button" onclick="ProductSelector.init()">{{tr}}Search{{/tr}}</button>
            <script type="text/javascript">
            ProductSelector.init = function(){
              this.sForm = "edit_stock";
              this.sId   = "product_id";
              this.sView = "product_name";
              this.pop({{$stock->product_id}});
            }
            </script>
          </td>
        </tr>
        <tr>
          <th>{{mb_label object=$stock field="order_threshold_critical"}}</th>
          <td>{{mb_field object=$stock field="order_threshold_critical" form="edit_stock" increment="1"}}</td>
        </tr>
        <tr>
          <th>{{mb_label object=$stock field="order_threshold_min"}}</th>
          <td>{{mb_field object=$stock field="order_threshold_min" form="edit_stock" increment="1"}}</td>
        </tr>
        <tr>
          <th>{{mb_label object=$stock field="order_threshold_optimum"}}</th>
          <td>{{mb_field object=$stock field="order_threshold_optimum" form="edit_stock" increment="1"}}</td>
        </tr>
        <tr>
          <th>{{mb_label object=$stock field="order_threshold_max"}}</th>
          <td>{{mb_field object=$stock field="order_threshold_max" form="edit_stock" increment="1"}}</td>
        </tr>
        <tr>
          <td class="button" colspan="4">
            {{if $stock->_id}}
            <button class="modify" type="submit">{{tr}}Modify{{/tr}}</button>
            <button type="button" class="trash" onclick="confirmDeletion(this.form,{typeName:'',objName:'{{$stock->_view|smarty:nodefaults|JSAttribute}}'})">
              {{tr}}Delete{{/tr}}
            </button>
            {{else}}
            <button class="submit" type="submit">{{tr}}Create{{/tr}}</button>
            {{/if}}
          </td>
        </tr>        
      </table>
      </form>
    </td>
  </tr>
</table>