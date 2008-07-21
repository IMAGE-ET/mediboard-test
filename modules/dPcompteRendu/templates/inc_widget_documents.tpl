{{* $id: $
  * @param $object CMbObject Target Object for documents
  * @param $modelesByOwner array|CCompteRendu sorted by owner
  * @param $packs array|CPack  List of packs
  * @param $praticien_id ref|CMediuser Owner of modèles
  * @param $id_suffixe string Suffixe for id of widget
  *}}
  
{{if !@$suffixe}}{{assign var=suffixe value="std"}}{{/if}}
  
<script type="text/javascript">
Document.suffixes.push("{{$suffixe}}");
Document.suffixes = Document.suffixes.uniq();


Document.refreshList = function() {
  var url = new Url;
  url.setModuleAction("dPcompteRendu", "httpreq_widget_documents");
  url.addParam("object_class", "{{$object->_class_name}}"); 
  url.addParam("object_id"   , "{{$object->_id}}");
  url.addParam("praticien_id", "{{$praticien_id}}");
  Document.suffixes.each( function(suffixe) {
	  url.addParam("suffixe", suffixe);
	  url.make();
	  url.requestUpdate("documents-" + suffixe, { waitingText : null } );
  } );
}

</script>


<form name="DocumentAdd-{{$suffixe}}" action="?m={{$m}}" method="post">
<table class="form">
  <tr>
    <td>
      <!-- Création via select classique -->
      
      <select name="_choix_modele" onchange="Document.create(this.value, '{{$object->_id}}')">
        <option value="">&mdash; Choisir un modèle</option>
        {{foreach from=$modelesByOwner key=owner item=_modeles}}
        <optgroup label="{{tr}}CCompteRendu._owner.{{$owner}}{{/tr}}">
          {{foreach from=$_modeles item=_modele}}
          <option value="{{$_modele->_id}}">{{$_modele->nom}}</option>
          {{foreachelse}}
          <option value="">{{tr}}None{{/tr}}</option>
          {{/foreach}}
        </optgroup>
        {{/foreach}}
      </select>
      
      <select name="_choix_pack" onchange="Document.createPack(this.value, '{{$object->_id}}')">
        <option value="">&mdash; Choisir un pack</option>
        {{foreach from=$packs item=_pack}}
          <option value="{{$_pack->_id}}">{{$_pack->_view}}</option>
        {{foreachelse}}
          <option value="">{{tr}}None{{/tr}}</option>
        {{/foreach}}
        </optgroup>
      </select>

			<!-- Création via ModeleSelector -->

	    <script type="text/javascript">
	      modeleSelector[{{$object->_id}}] = new ModeleSelector("DocumentAdd-{{$suffixe}}", null, "_modele_id", "_object_id");
	    </script>

      <button type="button" class="search" onclick="modeleSelector[{{$object->_id}}].pop('{{$object->_id}}','{{$object->_class_name}}','{{$praticien_id}}')">
        Modèle
      </button>
	    <input type="hidden" name="_modele_id" value="" />
	    <input type="hidden" name="_object_id" value="" onchange="Document.create(this.form._modele_id.value, this.value,'{{$object->_id}}','{{$object->_class_name}}'); this.value=''; this.form._modele_id.value = ''; "/>
    </td>
  </tr>
</table>
</form>

<ul>
  {{foreach from=$object->_ref_documents item=document}}
  <li>
    <form name="DocumentEdit-{{$suffixe}}-{{$document->_id}}" action="?m={{$m}}" method="post">
    <input type="hidden" name="m" value="dPcompteRendu" />
    <input type="hidden" name="del" value="0" />
    <input type="hidden" name="dosql" value="do_modele_aed" />
    <input type="hidden" name="object_id" value="{{$object->_id}}" />
    
    <button class="trash notext" type="button" onclick="Document.del(this.form, '{{$document->nom|smarty:nodefaults|JSAttribute}}')">
    	{{tr}}Delete{{/tr}}
    </button>
    {{mb_field object=$document field="compte_rendu_id" hidden=1 prop=""}}
    </form>
    <a href="#" class="tooltip-trigger" onclick="Document.edit({{$document->_id}})" onmouseover="ObjectTooltip.create(this, { mode: 'objectViewHistory', params: { object_class: 'CCompteRendu', object_id: {{$document->_id}} } })">
      {{$document->nom}}
    </a>
  </li>
  {{foreachelse}}
  <li><em>Aucun document</em></li>
  {{/foreach}}
</ul>