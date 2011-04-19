{{foreach from=$dmi_lines_count item=_stat}}
  <tr>
  	<td></td>
  	<td>
		  <span onmouseover="ObjectTooltip.createEx(this, '{{$_stat.product->_guid}}')">
        {{$_stat.product}}
			</span>
		</td>
    <td>
      {{$_stat.product->code}}
    </td>
    <td>
    	<span onmouseover="ObjectTooltip.createEx(this, '{{$_stat.labo->_guid}}')">
        {{$_stat.labo}}
			</span>
    </td>
		<td>{{$_stat.sum}}</td>
	</tr>
{{foreachelse}}
  <tr>
  	<td colspan="2" class="empty">
  		Aucune valeur pour ces critères
  	</td>
  </tr>
{{/foreach}}