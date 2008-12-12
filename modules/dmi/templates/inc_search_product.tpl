{{* $Id: $ *}}

		<table class="tbl">
		  <tr>
		    <th>{{mb_title class=CProduct field=name}}</th>
		    <th>{{mb_title class=CProduct field=description}}</th>
		    <th>{{tr}}CProductDeliveryTrace-delivered_quantity{{/tr}}</th>
		    <th>{{tr}}CProductOrderItemReception-received_quantity{{/tr}}</th>
		  </tr>
		  <tr>
		    <td>{{mb_value object=$product field=name}}</td>
		    <td>{{mb_value object=$product field=description}}</td>
		    <td>{{$quantite_delivree}}</td>
		    <td>{{$quantite_delivrable}}</td>
		  </tr>
		  <tr>
		    <td colspan="10">
			    <button type="submit" class="submit">{{tr}}Save{{/tr}}</button>
			  </td>
		  </tr>
		</table>