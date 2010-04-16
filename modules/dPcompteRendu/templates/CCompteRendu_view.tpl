{{assign var=pdf_thumbnails value=$dPconfig.dPcompteRendu.CCompteRendu.pdf_thumbnails}}
{{assign var="document" value=$object}}
{{if $document->object_id}}
<script type="text/javascript">
/*  displaythumb = function() {
  var url = new Url("dPcompteRendu", "ajax_display_first_thumb");
  url.addParam("compte_rendu_id", '{{$document->_id}}');
  url.addParam("nomdoc",'{{$document->nom}}');
  url.addParam("user_id",'{{$document->_ref_chir->_id}}');
  url.requestUpdate("thumbnail-{{$document->_id}}");
}
Main.add(function(){ displaythumb();})*/
</script>
{{/if}}

{{if !$document->object_id}}
<table class="tbl">
  <tr>
    <th class="title">{{tr}}CCompteRendu-modele-one{{/tr}}</th>
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
      <strong>{{tr}}CCompteRendu-created-by{{/tr}} :</strong>
      {{$document->_ref_chir->_view}}
    </td>
  </tr>

  <tr>
    <td class="text">
      <strong>{{tr}}CCompteRendu-linked-object{{/tr}} :</strong>
	    {{$document->_ref_object}}
	  </td>
  </tr>

  <tr>
    <td>
      <strong>{{tr}}CCompteRendu-count-words{{/tr}} :</strong>
        {{$document->source|count_words}} {{tr}}CCompteRendu-words{{/tr}}
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

{{if $document->object_id && $pdf_thumbnails == 1}}
<table class="tbl">
  <tr>
    <th class="title">{{tr}}CCompteRendu-thumbnail{{/tr}}</th>
  </tr>
  <tr>
    <td id="thumbnail-{{$document->_id}}" style="text-align: center; background: #fff url(style/mediboard/images/icons/loading.gif) center center no-repeat;">
      <img src="?m=dPcompteRendu&amp;a=ajax_display_first_thumb&amp;suppressHeaders=1&amp;compte_rendu_id={{$document->_id}}&amp;nomdoc={{$document->nom}}&amp;user_id={{$document->_ref_chir->_id}}"
           style="border: 1px solid #666;"/>
    </td>
  </tr>
</table>
{{/if}}