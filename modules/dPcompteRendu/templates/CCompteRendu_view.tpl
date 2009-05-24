{{assign var="document" value=$object}}

<table class="tbl tooltip">
  <tr>
    <th>
      {{if !$document->object_id}}
			Modèle : 
			{{/if}}
      {{$document}}
    </th>
  </tr>
  <tr>
    <td>
      <strong>Produit par :</strong>
	    {{$document->_ref_chir->_view}}
    </td>
	</tr>
	<tr>
    <td class="text">
      <strong>Associé à :</strong>
	    {{$document->_ref_object}}
	  </td>
	</tr>
	<tr>
	  <td>
      <strong>Taille :</strong>
	    {{$document->source|count_words}}
	    mots
	  </td>
	</tr>
<!--
	<tr>
    <td>
      <strong>Validé :</strong>
	    {{mb_value object=$document field="valide"}}
	  </td>
  </tr>
-->
</table>