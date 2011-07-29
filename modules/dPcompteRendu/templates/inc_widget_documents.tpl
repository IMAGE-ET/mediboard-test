{{* $id: $
  * @param $object CMbObject Target Object for documents
  * @param $modelesByOwner array|CCompteRendu sorted by owner
  * @param $packs array|CPack  List of packs
  * @param $praticien CMediuser Owner of modeles
  *}}
  
{{assign var=object_class value=$object->_class}}
{{assign var=object_id value=$object->_id}}
{{assign var=pdf_thumbnails value=$conf.dPcompteRendu.CCompteRendu.pdf_thumbnails}}
{{unique_id var=unique_id}}


<form name="DocumentAdd-{{$unique_id}}-{{$object->_guid}}" action="?m={{$m}}" method="post">
<input type="text" value="&mdash; Mod�le" name="keywords_modele" class="autocomplete str" autocomplete="off" onclick="this.value = ''; this.onclick=null;" style="width: 5em;" />
<input type="text" value="&mdash; Pack" name="keywords_pack" class="autocomplete str" autocomplete="off" onclick="this.value = ''; this.onclick=null;" style="width: 4em;"/>

<script type="text/javascript">

Main.add(function() {
  var form = getForm('DocumentAdd-{{$unique_id}}-{{$object->_guid}}');
  var url;
  
  url = new Url("dPcompteRendu", "ajax_modele_autocomplete");
  url.addParam("user_id", "{{$praticien->_id}}");
  url.addParam("function_id", "{{$praticien->function_id}}");
  url.addParam("object_class", '{{$object->_class}}');
  url.addParam("object_id", '{{$object->_id}}');
  url.autoComplete(form.keywords_modele, '', {
    minChars: 2,
    afterUpdateElement: createDoc,
    dropdown: true,
    width: "250px"
  });

  url = new Url("dPcompteRendu", "ajax_pack_autocomplete");
  url.addParam("user_id", "{{$praticien->_id}}");
  url.addParam("function_id", "{{$praticien->function_id}}");
  url.addParam("object_class", '{{$object->_class}}');
  url.addParam("object_id", '{{$object->_id}}');
  url.autoComplete(form.keywords_pack, '', {
    minChars: 2,
    afterUpdateElement: createPack,
    dropdown: true,
    width: "250px"
  });
  
  function createDoc(input, selected) {
    var id = selected.down(".id").innerHTML;
    var object_class = null;

    if (id == 0) {
      object_class = '{{$object->_class}}';
    }
    
    if (selected.select(".fast_edit").length) {
      Document.fastMode('{{$object->_class}}', id, '{{$object_id}}', null, null, '{{$unique_id}}');
    } else {
      Document.create(id, '{{$object_id}}', null, object_class, null);
    }
    
    $V(input, '');
  }

  function createPack(input, selected) {
    if (selected.select(".fast_edit").length) {
      Document.fastModePack(selected.down(".id").innerHTML, '{{$object_id}}');
    }
    else {
      Document.createPack(selected.down(".id").innerHTML, '{{$object_id}}');
    }
    $V(input, '');
  } 
});
</script>

<!-- Cr�ation via ModeleSelector -->

<script type="text/javascript">
  modeleSelector[{{$object_id}}] = new ModeleSelector("DocumentAdd-{{$unique_id}}-{{$object->_guid}}", null, "_modele_id", "_object_id");
</script>

<button type="button" class="search notext" onclick="modeleSelector[{{$object_id}}].pop('{{$object_id}}','{{$object_class}}','{{$praticien->_id}}')">
	{{if $praticien->_can->edit}}
  Tous
	{{else}}
  Mod�les disponibles
  {{/if}}
</button>

<!-- Impression de tous les mod�les disponibles pour l'objet -->
<button type="button" class="print notext" onclick="Document.printSelDocs('{{$object->_id}}', '{{$object_class}}');">
  {{tr}}Print{{/tr}}
</button>

<input type="hidden" name="_modele_id" value="" />
<input type="hidden" name="_object_id" value="" onchange="Document.create(this.form._modele_id.value, this.value,'{{$object_id}}','{{$object_class}}'); $V(this, ''); $V(this.form._modele_id, ''); "/>

</form>

<table class="form">
{{assign var=doc_count value=$object->_ref_documents|@count}}
{{if $mode != "hide"}}
  
  {{if $doc_count && $mode == "collapse"}}
  <tr id="DocsEffect-{{$object->_guid}}-trigger">
    <th class="category" colspan="3">
    	{{tr}}{{$object->_class}}{{/tr}} :
    	{{$doc_count}} document(s)

		  <script type="text/javascript">
		    Main.add(function () {
		      new PairEffect("DocsEffect-{{$object->_guid}}", { 
		        bStoreInCookie: true
		      });
		    });
		  </script>
    </th>
  </tr>
  {{/if}}

  <tbody id="DocsEffect-{{$object->_guid}}" {{if $mode == "collapse" && $doc_count}}style="display: none;"{{/if}}>
  
  {{foreach from=$object->_ref_documents item=document}}
  <tr>
    <td class="text">
      {{if $document->_is_editable}}
	      <a href="#{{$document->_guid}}" onclick="Document.edit({{$document->_id}}); return false;" style="display: inline;">
      {{/if}}
	      <span onmouseover="ObjectTooltip.createEx(this, '{{$document->_guid}}', 'objectView')">
	        {{$document}}
	      </span>
      {{if $document->_is_editable}}
	      </a>
      {{/if}}
      {{if $document->private}}
        &mdash; <em>{{tr}}CCompteRendu-private{{/tr}}</em>
      {{/if}}
	  </td>
	  
	  <td class="button" style="width: 1px; white-space: nowrap;">
	    <form name="Edit-{{$document->_guid}}" action="?m={{$m}}" method="post">
  	    <input type="hidden" name="m" value="dPcompteRendu" />
  	    <input type="hidden" name="dosql" value="do_modele_aed" />
        <input type="hidden" name="del" value="0" />
  	    {{mb_key object=$document}}
        
  	    <input type="hidden" name="object_id" value="{{$object_id}}" />
  	    <input type="hidden" name="object_class" value="{{$object_class}}" />
        
        <button type="button" class="print notext"
          onclick="{{if $pdf_thumbnails && $app->user_prefs.pdf_and_thumbs}}
            Document.printPDF({{$document->_id}});
          {{else}}
            Document.print({{$document->_id}})
          {{/if}}">
          {{tr}}Print{{/tr}}
        </button>
  	    
  	    <button type="button" class="trash notext" onclick="Document.del(this.form, '{{$document->nom|smarty:nodefaults|JSAttribute}}')">
  	    	{{tr}}Delete{{/tr}}
  	    </button>
	    </form>
 	  </td> 

    {{if $conf.dPfiles.system_sender}}
 	  <td class="button" style="width: 1px">
 	    <form name="Send-{{$document->_guid}}" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">
  	    <input type="hidden" name="m" value="dPcompteRendu" />
  	    <input type="hidden" name="dosql" value="do_modele_aed" />
  	    <input type="hidden" name="del" value="0" />
        {{mb_key object=$document}}
      
        <!-- Send File -->
  		  {{mb_include module=dPfiles template=inc_file_send_button 
  	 	 		_doc_item=$document
  		 		notext=notext
  		 		onComplete="Document.refreshList('$object_class','$object_id')"
  	 	  }}
      </form>
 	  </td>
		{{/if}}
	</tr>
  {{foreachelse}}
  <tr>
    <td colspan="3" class="empty">
      {{tr}}{{$object->_class}}{{/tr}} : Aucun document
    </td>
  </tr>
  {{/foreach}}
{{/if}}

</table>