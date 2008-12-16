<div class="big-info">
  Interface permettant :
  <ul>
    <li>d'associer les types de documents e-Cap aux catégories Mediboard ;</li>
    <li>d'informer si les documents d'une catégorie Mediboard sont envoyés automatiquement.</li>
  </ul>
</div>

<h1>Types de documents dans eCap (fake)</h1>

<ul>
  {{foreach from=$typesEcap item=typesEcapByClass key=class}}
  <li>
	{{tr}}{{$class}}{{/tr}}
	  <ul>
		  {{foreach from=$typesEcapByClass item=typesEcapByEcapObject key=ecapObject}}
	    <li>{{$ecapObject}}
	      <ul>
				  {{foreach from=$typesEcapByEcapObject key=type item=libelle}}
				  <li><strong>{{$type}}</strong> : {{$libelle}}</li>
				  {{/foreach}}
	      </ul>
	    </li>
	    {{/foreach}}
	  </ul>
  </li>
	{{/foreach}}
</ul>

<h1>Catégories Mediboard</h1>

<table class="tbl">
  {{foreach from=$categories key=class item=_catsByClass}}
  <tr>
	  <th colspan="10" class="title">{{tr}}CFilesCategory{{/tr}} pour {{tr}}{{$class}}{{/tr}}</th>
	</tr>
	
  <tr>
	  <th>{{mb_title class=CFilesCategory field=nom}}</th>
	  <th colspan="2">{{mb_title class=CFilesCategory field=validation_auto}}</th>
	  <th colspan="2">Identifiant e-Cap</th>
	</tr>

	{{foreach from=$_catsByClass item=_category}}
  <tr>
	  <td>{{mb_value object=$_category field=nom}}</td>
	 	<td>{{mb_value object=$_category field=validation_auto}}</td>
	 	<td>

      <form name="Edit-{{$_category->_guid}}" action="?m={{$m}}&amp;tab={{$tab}}" method="post" onsubmit="return checkForm(this)">

      <input type="hidden" name="m" value="dPfiles" />
      <input type="hidden" name="dosql" value="do_filescategory_aed" />
	  	<input type="hidden" name="file_category_id" value="{{$_category->_id}}" />
      <input type="hidden" name="del" value="0" />
      <input type="hidden" name="validation_auto" {{if $_category->validation_auto}}value="0"{{else}}value="1"{{/if}} />
      
      <button class="tick">{{tr}}Change{{/tr}}</button>
      
	 		</form>
	 	</td>

		{{assign var=category_id value=$_category->_id}}
		{{assign var=idEcap value=$idsEcap.$category_id}}
	 	<td>{{mb_value object=$_category field=validation_auto}}</td>
	 	<td>

			<form name="EditIdEcap-{{$_category->_guid}}" action="?m={{$m}}&amp;tab={{$tab}}" method="post" onsubmit="return checkForm(this)">
			  <input type="hidden" name="m" value="dPsante400" />
			  <input type="hidden" name="dosql" value="do_idsante400_aed" />
			  <input type="hidden" name="del" value="0" />
			  <input type="hidden" name="id_sante400_id" value="{{$idEcap->_id}}" />
			  <input type="hidden" name="object_class" value="{{$idEcap->object_class}}" />
			  <input type="hidden" name="object_id" value="{{$idEcap->object_id}}" />
			  <input type="hidden" name="tag" value="{{$idEcap->tag}}" />
			  <input type="hidden" name="last_update" value="now" />
			  
			  <select name="id400" > 
			    <option value="">&mdash; Choisir un type e-Cap</option>
					{{assign var=category_class value=$_category->class}}
			    {{foreach from=$typesEcap.$category_class key=EcapObject item=typesByEcapObject}}
			    <optgroup label="{{$EcapObject}}">
				  {{foreach from=$typesByEcapObject key=type item=libelle}}
				  <option value="{{$type}}" {{if $idEcap->id400 == $type}}selected="selected"{{/if}}>{{$libelle}}</option>
				  {{/foreach}}
			    </optgroup>
					{{/foreach}}
			  </select>
			  
			  <button type="submit" class="submit">{{tr}}Submit{{/tr}}</button>
			</form>

	  </td>

	</tr>

	{{/foreach}}	
	{{/foreach}}
</table>
