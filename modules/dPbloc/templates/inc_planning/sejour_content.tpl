<!-- Sejour -->
      <td>
        {{$sejour->type|truncate:1:""|capitalize}}
        ({{$sejour->_duree_prevue}}j)
      </td>
		  <td>
		    {{mb_value object=$sejour field=_entree}}
		  </td>
		  <td class="text">
        {{assign var="affectation" value=$sejour->_ref_first_affectation}}
		    {{if $affectation->_id}}
		    {{$affectation->_ref_lit->_view}}
		    {{else}}
		    Non placé
		    {{/if}}
		  </td>