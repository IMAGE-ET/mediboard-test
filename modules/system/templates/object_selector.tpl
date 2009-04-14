{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage system
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script type="text/javascript">

function setClose(selClass,keywords,key,val){
  var oObject = {
    objClass : selClass,
    id : key,
    view : val,
    keywords : keywords
  }
  
  var oSelector = window.opener.ObjectSelector;
  
  if (oSelector) {
    oSelector.set(oObject);
  }
  else {
    window.opener.setObject(oObject);
  }
  window.close();
}
</script>

<form action="?" name="frmSelector" method="get" onsubmit="return checkForm(this)">

<input type="hidden" name="m" value="system" />
<input type="hidden" name="a" value="object_selector" />
<input type="hidden" name="dialog" value="1" />
<input type="hidden" name="onlyclass" value="{{$onlyclass}}" />
{{if $onlyclass=='true'}}
<input type="hidden" name="selClass" value="{{$selClass}}" />
{{/if}}
<table class="form">
  <tr>
    <th class="category" colspan="3">Critères de sélection</th>
  </tr>
  <tr>
    <th><label for="selClass" title="Veuillez Sélectionner une Class">Choix du type d'objet</label></th>
    <td colspan="2">
      <select class="notNull str" name="selClass" {{if $onlyclass=='true'}}disabled="disabled"{{/if}}>
        <option value="">&mdash; {{tr}}Choose{{/tr}}</option>
        {{foreach from=$classes key=_class item=_fields}}
        <option value="{{$_class}}" 
        	{{if $selClass == $_class}} selected="selected" {{/if}}
        	{{if !$_fields|@count}} style="opacity: .6" {{/if}}
        >	
        	{{tr}}{{$_class}}{{/tr}}
        </option>
        {{/foreach}}
       </select>
    </td>
  </tr>

  <tr>
    <th>
    	<label for="keywords" title="Veuillez saisir un ou plusieurs mot clé">Mots Clés</label>
    </th>
    <td>
    	<input class="str" type="text" name="keywords" value="{{$keywords|stripslashes}}" />
    </td>
  </tr>

	{{if $selClass}}
  {{assign var=fields value=$classes.$selClass}}
  <tr>
    <td colspan="10">
      {{if $fields|@count}}
	      <div class="small-info">
	        Mots clés recherchés dans les champs suivants :
	        {{foreach from=$fields item=_field name=field}}
					{{mb_label class=$selClass field=$_field}}
					{{mb_ternary test=$smarty.foreach.field.last value='.' other=','}}

					{{/foreach}}
	      </div>
			{{else}}
	      <div class="small-warning">
	        <strong>Recherche par mot clés impossible</strong> : 
	        aucun champ de recherche pour ce type d'objet.
	        <br/>
	        Utilisez l'identifiant interne ci-dessous.
	      </div>
			{{/if}}
    </td>
  </tr>
	{{/if}}
  
  <tr>
    <th>
    	<label for="object_id" title="Identifiant interne de l'objet">Identifiant</label>
    </th>
    <td>
    	<input class="ref" type="text" name="object_id" value="{{$object_id}}" />
    </td>
  </tr>

  <tr>
    <td class="button" colspan="2">
    	<button class="search" type="submit">{{tr}}Search{{/tr}}</button>
    </td>
  </tr>
</table>
</form>

{{if $selClass}}
<table class="tbl">
  <tr>
    <th align="center" colspan="2">{{tr}}Results{{/tr}}</th>
  </tr>
  
  {{foreach from=$list item=_object}}
    <tr>
      <td>
      	<label onmouseover="ObjectTooltip.createEx(this, '{{$_object->_guid}}');">{{$_object}}</label>
      </td>     
      <td class="button" style="width: 1%">
      	<button type="button" class="tick" onclick="setClose('{{$selClass}}', '{{$keywords|stripslashes|smarty:nodefaults|JSAttribute}}', {{$_object->_id}}, '{{$_object->_view|smarty:nodefaults|JSAttribute}}')">
      	  {{tr}}Select{{/tr}}
      	</button>
      </td>
    </tr>
  {{/foreach}}
</table>
{{/if}}