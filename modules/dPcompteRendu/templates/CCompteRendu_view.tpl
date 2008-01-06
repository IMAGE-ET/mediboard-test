{{assign var="document" value=$object}}

<table class="tbl tooltip">
  <tr>
    <th>
      {{$document->_view}}
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
	    {{$document->_ref_object->_view}}
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