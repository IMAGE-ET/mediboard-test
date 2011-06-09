<script type="text/javascript">
Main.add(function(){
  getForm("form-filter-changes").ratio.addSpinner({min: 1});
});

fixOrderItem = function(form){
  return onSubmitFormAjax(form, {onComplete: function(){
	  form.up('tr').remove();
	}});
}
</script>

<form name="form-filter-changes" method="get" action="?">
  <input type="hidden" name="m" value="dPstock" />
  <input type="hidden" name="{{$actionType}}" value="{{$action}}" />
	
	<table class="main form">
		<tr>
			<th>Rapport minimal entre les valeurs</th>
			<td class="narrow">
				<input type="text" name="ratio" value="{{$ratio}}" size="3" />
			</td>
			<td>
				<button type="submit" class="search">{{tr}}Filter{{/tr}}</button>
			</td>
		</tr>
	</table>
</form>

<h3>
	{{$changes|@count}} prix unitaires suspects sur {{$changes_struct|@count}} références
	
	<small>({{$total_order_items}} lignes de commande au total)</small>
</h3>

<table class="main tbl">
  <tr>
    <th colspan="2">
      Reference
    </th>
    <td><strong>Prix unitaire HT actuel</strong></td>
    <td><strong>Réf.</strong></td>
    {{if "cahpp"|module_active}}<td><strong>CAHPP</strong></td>{{/if}}
  </tr>
  <tr>
    <td style="text-align: right;">
       <small style="float: left; color: #666;">ID</small>
			 Date de commande
		</td>
		<td>Numéro de commande</td>
    <td>Prix à l'époque de la commande</td>
    <td></td>
    {{if "cahpp"|module_active}}<td></td>{{/if}}
  </tr>

	{{foreach from=$changes_struct item=_changes key=reference_id}}
		<tr>
			<th colspan="2">
	      <a class="button search notext" style="float: left;" href="?m=dPstock&tab=vw_idx_reference&reference_id={{$reference_id}}"></a>
				{{$references.$reference_id}}
				&mdash;
				{{$references.$reference_id->code}}
			</th>
			<td><strong>{{$references.$reference_id->price}}</strong></td>
	    <td>Réf.</td>
	    {{if "cahpp"|module_active}}<td>CAHPP</td>{{/if}}
		</tr>
		{{foreach from=$_changes item=_change}}
		  <tr>
	      <td style="text-align: right;">
	        <small style="float: left; color: #666;">{{$_change.order_item_id}}</small>
					{{$_change.date_ordered|date_format:$conf.longdate}}
				</td>
	      <td>{{$_change.order_number}}</td>
	      <td {{* style="color: {{if $_change.OP > $_change.RP}} red {{else}} green {{/if}}" *}}>{{$_change.OP}}</td>
		    <td>
		    	<form name="fix-price-ref-{{$_change.order_item_id}}" method="post" onsubmit="return fixOrderItem(this)">
		    		<input type="hidden" name="m" value="dPstock" />
            <input type="hidden" name="dosql" value="do_order_item_aed" />
            <input type="hidden" name="order_item_id" value="{{$_change.order_item_id}}" />
            <input type="hidden" name="unit_price" value="{{$references.$reference_id->price}}" />
            <button class="tick notext"></button>
		    	</form>
		    </td>
				{{if "cahpp"|module_active}}
		    <td class="text">
          <form name="fix-price-cahpp-{{$_change.order_item_id}}" method="post" onsubmit="return fixOrderItem(this)">
            <input type="hidden" name="m" value="dPstock" />
            <input type="hidden" name="dosql" value="do_order_item_aed" />
            <input type="hidden" name="order_item_id" value="{{$_change.order_item_id}}" />
            <input type="hidden" name="unit_price" value="{{$references_cahpp.$reference_id->prix_unitaire}} " />
            <button class="tick notext"></button>
          </form>
          {{$references_cahpp.$reference_id->prix_unitaire}} 
					&mdash;
          {{$references_cahpp.$reference_id|spancate:40}}</small>
				</td>
				{{/if}}
			</tr>
		{{/foreach}}
		<tr>
			<td colspan="{{if "cahpp"|module_active}}5{{else}}4{{/if}}"><br /></td>
		</tr>
	{{/foreach}}
</table>