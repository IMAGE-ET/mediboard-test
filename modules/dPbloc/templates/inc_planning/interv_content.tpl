<!-- Intervention -->
		  {{if $curr_op->annulee}}
		    <td class="cancelled">ANNULEE</td>
		  {{elseif $curr_op->rank}}
		    <td>{{$curr_op->time_operation|date_format:$dPconfig.time}}</td>
		  {{else}}
		    <td>NP</td>
		  {{/if}}
		  <td class="text">
        {{if $curr_op->libelle}}
          <em>[{{$curr_op->libelle}}]</em>
          <br />
        {{/if}}
        {{foreach from=$curr_op->_ext_codes_ccam item=curr_code}}
          {{if !$curr_code->_code7}}<strong>{{/if}}
          <em>{{$curr_code->code}}</em>
          {{if $filter->_ccam_libelle}}
            : {{$curr_code->libelleLong|truncate:60:"...":false}}
            <br/>
          {{else}}
            ;
          {{/if}}
          {{if !$curr_code->_code7}}</strong>{{/if}}
        {{/foreach}}
      </td>
		  <td>{{$curr_op->cote|truncate:1:""|capitalize}}</td>
      <td>
        {{if $curr_op->type_anesth != null}}
        {{$curr_op->_lu_type_anesth}}
        {{else}}
        Non Disponible
        {{/if}}
      </td>
		  <td class="text">{{$curr_op->rques|nl2br}}</td>
		  <td class="text">
		    {{if $curr_op->commande_mat == '0' && $curr_op->materiel != ''}}
		    <em>Materiel manquant:</em>
		    {{/if}}
		    {{$curr_op->materiel|nl2br}}
		  </td>