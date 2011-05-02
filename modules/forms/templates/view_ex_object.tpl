
{{mb_script module="system" script="object_selector"}}

<form name="filter-ex_object" method="get" onsubmit="return Url.update(this, 'list-ex_object')">
  <input type="hidden" name="m" value="forms" />
  <input type="hidden" name="a" value="ajax_list_ex_object" />
	
	<table class="main form" style="width: auto;">
		<tr>
			<th>Type d'objet de référence</th>
			<td>
				<select name="reference_class" class="str notNull">
					{{foreach from=$reference_classes item=_class}}
					  <option value="{{$_class}}" {{if $reference_class == $_class}} selected="selected" {{/if}}>{{tr}}{{$_class}}{{/tr}}</option>
					{{/foreach}}
				</select>
			</td>
	    <th>Objet</th>
	    <td>
	      <input type="hidden" name="reference_id" value="{{$reference_id}}" class="ref notNull" />
	      <input type="text" name="_reference_view" value="{{$reference->_view}}" readonly="readonly" size="60" ondblclick="ObjectSelector.init()" />
	      <button type="button" class="search" onclick="ObjectSelector.init()">{{tr}}Search{{/tr}}</button>
	      <script type="text/javascript">
	        ObjectSelector.init = function(){
	          this.sForm     = "filter-ex_object";
	          this.sId       = "reference_id";
	          this.sView     = "_reference_view";
	          this.sClass    = "reference_class";
	          this.onlyclass = "true";
	          this.pop();
	        }
	      </script>
	    </td>
			<td>
				<button type="submit" class="search">{{tr}}Filter{{/tr}}</button>
			</td>
		</tr>
	</table>
</form>

<style type="text/css">
.vertical th span {
  writing-mode:tb-rl;
  /*-webkit-transform:rotate(90deg);
  -moz-transform:rotate(90deg);
  -o-transform: rotate(90deg);
  white-space:nowrap;
  display:block;
	position: absolute;
  bottom:0;*/
}
</style>

<div id="list-ex_object"></div>
