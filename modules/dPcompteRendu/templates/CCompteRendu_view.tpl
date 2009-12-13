{{assign var="document" value=$object}}

{{if !$document->object_id}}
<table class="tbl">
  <tr>
    <th class="title">Modèle</th>
  </tr>
</table>
{{/if}}
  
<table class="tbl">
  <tr>
    <td>
      <strong>{{mb_label object=$document field=compte_rendu_id}}:</strong>
	    {{mb_value object=$document field=compte_rendu_id}}
    </td>
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

<hr />

{{include file=CMbObject_view.tpl}}