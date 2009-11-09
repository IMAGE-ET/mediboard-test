{{assign var="affectation" value=$object}}

<table class="tbl tooltip">
  <tr>
    <th class="title text">
      {{$object->_view}}
    </th>
  </tr>
 
  <tr>
    <td>
	    <strong>{{mb_label object=$affectation field=lit_id}}</strong> :
	    <em>{{$affectation->_ref_lit->_view}}</em>
	    <br />
	    
	    <strong>{{mb_label object=$affectation field=entree}}</strong> :
	    <em>{{mb_value object=$affectation field=entree}}</em>
	    <br />

	    <strong>{{mb_label object=$affectation field=sortie}}</strong> :
	    <em>{{mb_value object=$affectation field=sortie}}</em>
	    <br />

      <strong>{{mb_label object=$affectation field=confirme}}</strong> :
      <em>{{mb_value object=$affectation field=confirme}}</em>
      <br />

	    <strong>{{mb_label object=$affectation field=effectue}}</strong> :
	    <em>{{mb_value object=$affectation field=effectue}}</em>
	    <br />

			{{if $affectation->rques}}
	    <strong>{{mb_label object=$affectation field=rques}}</strong> :
	    <em>{{mb_value object=$affectation field=rques}}</em>
	    <br />
	    {{/if}}

    </td>
  </tr>
</table>