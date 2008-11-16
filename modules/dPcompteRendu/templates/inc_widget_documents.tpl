{{* $id: $
  * @param $object CMbObject Target Object for documents
  * @param $modelesByOwner array|CCompteRendu sorted by owner
  * @param $packs array|CPack  List of packs
  * @param $praticien CMediuser Owner of modeles
  *}}
  
{{assign var=object_class value=$object->_class_name}}
{{assign var=object_id value=$object->_id}}

<script type="text/javascript">
  Main.add( function() { prepareForm("DocumentAdd-{{$object->_guid}}"); } )
</script>

<form name="DocumentAdd-{{$object->_guid}}" action="?m={{$m}}" method="post">

<table class="form">
  <tr>
    <td class="button">
    	{{if $praticien->_can->edit}}

      <!-- Création via select classique -->

      <select name="_choix_modele" style="width: 85px;" onchange="Document.create(this.value, '{{$object_id}}'); $V(this, '');">
        <option value="">&mdash; Modèle</option>
        {{foreach from=$modelesByOwner key=owner item=_modeles}}
        {{if $owner == "prat"}}{{assign var=ref_owner value=$praticien}}{{/if}}
        {{if $owner == "func"}}{{assign var=ref_owner value=$praticien->_ref_function}}{{/if}}
        {{if $owner == "etab"}}{{assign var=ref_owner value=$praticien->_ref_function->_ref_group}}{{/if}}
        <optgroup label="{{tr}}CCompteRendu-_owner{{/tr}} {{if $ref_owner}}{{$ref_owner->_view}}{{/if}}">
          {{foreach from=$_modeles item=_modele}}
          <option value="{{$_modele->_id}}">{{$_modele->nom}}</option>
          {{foreachelse}}
          <option value="">{{tr}}None{{/tr}}</option>
          {{/foreach}}
        </optgroup>
        {{/foreach}}
      </select>
      
      <select name="_choix_pack" style="width: 70px;" onchange="Document.createPack(this.value, '{{$object_id}}'); $V(this, '');">
        <option value="">&mdash; Pack</option>
        {{foreach from=$packs item=_pack}}
          <option value="{{$_pack->_id}}">{{$_pack->_view}}</option>
        {{foreachelse}}
          <option value="">{{tr}}None{{/tr}}</option>
        {{/foreach}}
      </select>

      {{/if}}

			<!-- Création via ModeleSelector -->

	    <script type="text/javascript">
	      modeleSelector[{{$object_id}}] = new ModeleSelector("DocumentAdd-{{$object->_guid}}", null, "_modele_id", "_object_id");
	    </script>

      <button type="button" class="search" onclick="modeleSelector[{{$object_id}}].pop('{{$object_id}}','{{$object_class}}','{{$praticien->_id}}')">
	    	{{if $praticien->_can->edit}}
        Tous
	    	{{else}}
        Modèles disponibles
	      {{/if}}
      
      </button>
      
	    <input type="hidden" name="_modele_id" value="" />
	    <input type="hidden" name="_object_id" value="" onchange="Document.create(this.form._modele_id.value, this.value,'{{$object_id}}','{{$object_class}}'); $V(this, ''); $V(this.form._modele_id, ''); "/>
    </td>
  </tr>
  
</table>

</form>

{{if $object->_ref_documents|@count && $mode != "hide"}}
<table class="tbl">
  <tr id="DocsEffect-{{$object->_guid}}-trigger">
    <th class="category" colspan="2">
    	{{tr}}{{$object->_class_name}}{{/tr}} :
    	{{$object->_ref_documents|@count}} document(s)
    </th>
  </tr>
  
  {{if $mode == "collapse"}}
    <script type="text/javascript">
      Main.add(function () {
        new PairEffect("DocsEffect-{{$object->_guid}}", { 
          bStoreInCookie: true
        });
      });
    </script>
    <tbody id="DocsEffect-{{$object->_guid}}" style="display:none;">
  {{else}}
    <tbody>
  {{/if}}
  
	  {{foreach from=$object->_ref_documents item=document}}
	  <tr>
	    <td class="text">
		    <form name="DocumentEdit-{{$document->_id}}" action="?m={{$m}}" method="post">
		    <input type="hidden" name="m" value="dPcompteRendu" />
		    <input type="hidden" name="del" value="0" />
		    <input type="hidden" name="dosql" value="do_modele_aed" />
		    <input type="hidden" name="object_id" value="{{$object_id}}" />
		    <input type="hidden" name="object_class" value="{{$object_class}}" />

		    {{mb_field object=$document field="compte_rendu_id" hidden=1 prop=""}}
		    </form>
		    <a href="#" class="tooltip-trigger" onclick="Document.edit({{$document->_id}})" onmouseover="ObjectTooltip.create(this, { mode: 'objectViewHistory', params: { object_class: 'CCompteRendu', object_id: {{$document->_id}} } })">
		      {{$document->nom}}
		    </a>
		  </td>
		  
		  <td class="button" style="width: 1px">
		    <button class="trash notext" onclick="Document.del(document.forms['DocumentEdit-{{$document->_id}}'], '{{$document->nom|smarty:nodefaults|JSAttribute}}')">
		    	{{tr}}Delete{{/tr}}
		    </button>
		  </td>  
		</tr>
	  {{foreachelse}}
	  <tr>
	    <td>
	      <em>
	      	{{tr}}{{$object->_class_name}}{{/tr}} :
	      	Aucun document
	      </em>
	    </td>
	  </tr>
	  {{/foreach}}

	</tbody>
</table>
{{/if}}