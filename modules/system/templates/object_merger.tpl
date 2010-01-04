{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage system
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{mb_include_script module=system script=mb_object}}
{{mb_include_script module=system script=object_selector}}

<script type="text/javascript">
function setField(field, source) {
  var form = source.form;
	var value = $V(source);
  var field = $(form.elements[field]);

  // Update Value
  $V(field, value);
  
	if (!field.hasClassName) {
	  return;
	}

  var oView = null;
	var oProperties = field.getProperties();
	
  // Can't we use Calendar.js helpers ???
  if (oProperties.dateTime) {
	  oView = $(form.elements[field.name+'_da']);
	  $V(oView, value ? Date.fromDATETIME(value).toLocaleDateTime() : "");
	}
  
  if (oProperties.date) {
    oView = $(form.elements[field.name+'_da']);
    $V(oView, value ? Date.fromDATE(value).toLocaleDate() : "");
  }

  if (oProperties.time) {
    oView = $(form.elements[field.name+'_da']);
    $V(oView, value);
  }

  if (oProperties.ref) {
    oView = $(form.elements["_"+field.name+'_view']);
	  $V(oView, Element.getLabel(source).textContent.strip());
  }

  if (oProperties.mask) {
    $V(field, Element.getLabel(source).textContent.strip(), false);
	}
}

function updateOptions(field) {
  var form = field.form;
	$A(form.elements["_choix_"+field.name]).each(function (element) {
	  element.checked = element.value.stripAll() == field.value.stripAll();
	} );
}

var objectsList = [];

ObjectSelector.init = function(key) {
  this.sForm     = "objects-selector";
  this.sView     = "objects_view["+key+"]";
  this.sId       = "objects_id["+key+"]";
  this.sClass    = "objects_class";
  this.onlyclass = "true";
  this.pop();
}

var emptyObjectField = '<div class="object readonly">';
emptyObjectField += '<input type="hidden" name="objects_id[%KEY]" value="%ID" />';
emptyObjectField += '<input type="text" size="40" name="objects_view[%KEY]" readonly="readonly" value="%VIEW" ondblclick="ObjectSelector.init(%KEY)" />';
emptyObjectField += '<button type="button" onclick="ObjectSelector.init(%KEY)" class="search notext">{{tr}}Search{{/tr}}</button>';
emptyObjectField += '<button type="button" onclick="this.up().remove();" class="remove notext">{{tr}}Remove{{/tr}}</button>';
emptyObjectField += '</div>';
var key = 0;

function addObjectField(id, view) {
  var field = emptyObjectField.gsub('%KEY', key++).gsub('%ID', id || '').gsub('%VIEW', view || '');
  $("objects-list").insert(field);
}

function confirmMerge(button, fast) {
  $V(button.form.fast, fast);
  return confirm("Etes-vous sûr(e) de vouloir fusionner ces objets ?");
}


Main.add(function() {
  {{foreach from=$objects item=object}}
    addObjectField('{{$object->_id}}', '{{$object->_view}}');
    objectsList.push({{$object->_id}});
  {{foreachelse}}
    addObjectField();
    addObjectField();
  {{/foreach}}
}); 
</script>

<form name="objects-selector" method="get" action="">
  <input type="hidden" name="m" value="{{$m}}" />
  <input type="hidden" name="{{$actionType}}" value="{{$action}}" />
  <input type="hidden" name="readonly_class" value="{{$readonly_class}}" />
  <table class="main form">
    <tr>
      <th>Type d'objet</th>
      <td>
        {{if $readonly_class}}
          <input type="hidden" name="objects_class" value="{{$objects_class}}" />
          {{tr}}{{$objects_class}}{{/tr}}
        {{else}}
          <select name="objects_class">
            {{foreach from=$list_classes item=class}}
              <option value="{{$class}}" {{if $objects_class == $class}}selected="selected"{{/if}}>{{tr}}{{$class}}{{/tr}}</option>
            {{/foreach}}
          </select>
        {{/if}}
      </td>
      <th>Objets</th>
      <td id="objects-list"></td>
    </tr>

    <tr>
      <td />
      <td>
        <button type="submit" class="submit">{{tr}}Submit{{/tr}}</button>
      </td>
      <th />
      <td><button type="button" class="add notext" onclick="addObjectField()">{{tr}}Add{{/tr}}</button></td>
    </tr>

    <tr>
      <th>Afficher les champs</th>
      <td>
        <label>
          <input type="checkbox" onclick="$$('tr.duplicate').invoke('setVisible', $V(this));" />
          avec une valeur multiple identiques
          <strong>({{$counts.duplicate}} valeurs)</strong>
        </label>
        <br />
        <label>
          <input type="checkbox" onclick="$$('tr.unique').invoke('setVisible', $V(this));" />
          avec une valeur unique
          <em>({{$counts.unique}} valeurs)</em>
        </label>
        <br />
        <label>
          <input type="checkbox" onclick="$$('tr.none'  ).invoke('setVisible', $V(this));" />
          sans valeurs
          <em>({{$counts.none}} valeurs)</em>
        </label>
      </td>
      <th />
      <td />
    </tr>
  </table>
</form>

{{if $result}}

<h2>Fusion de {{tr}}{{$result->_class_name}}{{/tr}}</h2>

{{if $checkMerge}}
<div class="small-warning">
  <strong>La fusion de ces deux objets n'est pas possible</strong> à cause des problèmes suivants :<br />
  - {{$checkMerge}}<br />
  Veuillez corriger ces problèmes avant toute fusion.
</div>
{{/if}}

{{if $alternative_mode}}
  <div class="small-info">
    Vous êtes en mode SIP, vous ne pourrez fusionner que deux objets au maximum.
  </div>
{{/if}}

<form name="form-merge" action="?m={{$m}}" method="post" onsubmit="{{if $checkMerge}}false{{else}}checkForm(this){{/if}}">
  <input type="hidden" name="dosql" value="do_object_merge" />
  <input type="hidden" name="{{$actionType}}" value="{{$action}}" />
  <input type="hidden" name="m" value="system" />
  <input type="hidden" name="del" value="0" />
  <input type="hidden" name="fast" value="0" />
  {{foreach from=$objects item=object name=object}}
  <input type="hidden" name="_merging[{{$object->_id}}]" value="{{$object->_id}}" />
  <input type="hidden" name="_objects_id[{{$smarty.foreach.object.index}}]" value="{{$object->_id}}" />
  {{/foreach}}
  <input type="hidden" name="_objects_class" value="{{$result->_class_name}}" />
  
  {{math equation="100/(count+1)" count=$objects|@count assign=width}}
  <table class="form merger">
    <tr>
      <th class="category" style="width: 1%;">
      </th>
      <th class="category" style="width: {{$width}}%;">Résultat</th>

      {{foreach from=$objects item=object name=object}}
      <th class="category" style="width: {{$width}}%;">
        <span onmouseover="ObjectTooltip.createEx(this, '{{$object->_guid}}')">
					{{tr}}{{$object->_class_name}}{{/tr}} 
					{{$smarty.foreach.object.iteration}}
					<br/>
          {{$object}}
				</span>

        {{if $alternative_mode}}
				<br />
        <label style="font-weight: normal;">
          <input type="radio" name="_base_object_id" value="{{$object->_id}}" {{if $smarty.foreach.object.first}}checked="checked"{{/if}} />
          Utiliser comme base [#{{$object->_id}}]
        </label>
        {{/if}} 
      </th>
      {{/foreach}}
    </tr>
    
    {{foreach from=$result->_specs item=spec name=spec}}
      {{if $spec->fieldName != $result->_spec->key && ($spec->fieldName|substr:0:1 != '_' || $spec->reported)}}
        {{mb_include module=system template=inc_merge_field field=$spec->fieldName}}
      {{/if}}
    {{/foreach}}

	  <tr>
	  	<td colspan="100" class="text">
       <button type="button" class="search" onclick="MbObject.viewBackRefs('{{$result->_class_name}}', objectsList);">
         {{tr}}CMbObject-merge-moreinfo{{/tr}}
       </button>
       
	  	 <div class="big-warning">
	  	   Vous êtes sur le point d'effectuer une fusion d'objets.
	  	   <br />
	  	   <strong>Cette opération est irréversible, il est donc impératif d'utiliser cette fonction avec une extrême prudence !</strong>
	  	   <br />

         {{if $alternative_mode}}
         La<strong>procédure alternative est sélectionnée</strong>, elle limite la fusion à 2 objets et se déroule en trois phases :
         <ol>
           <li>Modification d'un des deux objets avec les propriétés choisies ci-dessus</li>
           <li>Transfert des relations depuis l'autre objet</li>
           <li>Suppression de l'autre objet</li>
         </ol>
				 {{else}}
         La <strong>procédure normale</strong> de fusion se passe en trois phases :
         <ol>
           <li>Création d'un nouvel objet, avec les propriétés choisies ci-dessus</li>
           <li>Transfert des relations depuis les anciens objets vers le nouveau</li>
           <li>Suppression des anciens objets</li>
         </ol>
         {{/if}}
	   	 </div>

    	 <div class="big-info">
    	   Il existe deux options possibles pour effectuer la fusion :
    	   <ul>
    	     <li><strong>La fusion standard qui automatise des sauvegardes normales</strong>. Ce processus
    	       <ul>
    	         <li>effectue des vérifications d'intégrité, au risque d'échouer dans certaines circonstances</li>
    	         <li>journalise tous les transferts d'objet</li>
    	         <li>est très lent</li>
    	       </ul>
    	     </li>
    	      
    	     <li><strong>La fusion massive, qui modifie la base de données en direct</strong>. Ce processus
    	       <ul>
    	         <li>n'effectue aucune vérification d'intégrité</li>
    	         <li>ne journalise que la création du nouvel objet et l'opération de fusion</li>
    	         <li>est très rapide</li>
    	       </ul>
    	     </li>
    	   </ul> 
    	 </div>

      </td>
    </tr>

    <tr>
	  	<td colspan="100" class="button">
		    <button type="submit" class="submit" onclick="return confirmMerge(this, '0')">
		      {{tr}}Merge{{/tr}}
		    </button>
		    <button type="submit" class="submit" onclick="return confirmMerge(this, '1');">
		      {{tr}}Merge{{/tr}} {{tr}}massively{{/tr}}
		    </button>
	    </td>
	  </tr>
  </table>
</form>

{{else}}
<div class="small-info">
  Veuillez choisir des objets existants à fusionner.
</div>
{{/if}}