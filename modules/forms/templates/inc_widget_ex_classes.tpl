{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage system
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{if $ex_classes|@count}}
  <div style="display: inline-block;">
	
		{{if $count_available == 1}}
      <!-- Un seul formulaire : bouton -->
		  {{assign var=_ex_class value=$ex_classes|@reset}}
		  <button class="new" value="{{$_ex_class->_id}}" onclick="selectExClass(this, '{{$object->_guid}}', '{{$event}}', '{{$_element_id}}')">
		  	{{$_ex_class->name}}
		  </button>
			
		{{elseif $count_available > 1}}
		  <!-- plusieurs formulaire : select -->
		  <select onchange="selectExClass(this, '{{$object->_guid}}', '{{$event}}', '{{$_element_id}}')" style="width: 12em;">
		    <option selected="selected" disabled="disabled">
		      {{$count_available}} formulaire(s) disponible(s)
		    </option>
		    
		    {{foreach from=$ex_classes item=_ex_class}}
		      <option value="{{$_ex_class->_id}}" {{if $_ex_class->_disabled}}disabled="disabled"{{/if}}>{{$_ex_class->name}}</option>
		    {{/foreach}}
		  </select>
		{{/if}}
  </div>
	
	{{* <script type="text/javascript">
  {{foreach from=$ex_classes item=_ex_class}}
    ExObject.classes["{{$_ex_class->host_class}}"] = ExObject.classes["{{$_ex_class->host_class}}"] || [];
    ExObject.classes["{{$_ex_class->host_class}}"]["{{$_ex_class->event}}"] = ExObject.classes["{{$_ex_class->event}}"] || [];
		ExObject.classes["{{$_ex_class->host_class}}"]["{{$_ex_class->event}}"].push({{$_ex_class->getDBFields()|@json}});
		
		{{if }}
  {{/foreach}}
	</script> 
	*}}
	
{{else}}
  <em style="color: #999;">Aucun formulaire disponible</em>
{{/if}}

{{if $ex_objects|@count > 1}}
<button class="down notext" onclick="ObjectTooltip.createDOM(this, $(this).next(), {duration: 0});">
	Fiches enregistrées
</button>

<ul id="CExObject.{{$object->_guid}}.{{$event}}" style="display: none;">
{{assign var=_events value=$object->_spec->events}}

{{foreach from=$ex_objects item=_ex_objects key=_ex_class_id}}
  {{assign var=_ex_class value=$ex_classes.$_ex_class_id}}
	
	{{if $_events.$event.multiple}}
	  <li>
	    <strong>{{$_ex_class->name}}</strong>
	    <ul>
	    {{foreach from=$_ex_objects item=_ex_object}}
	      <li>
	        <a href="#{{$_ex_object->_guid}}" 
	           onclick="showExClassForm({{$_ex_class_id}}, '{{$object->_guid}}', '{{$_ex_object}}', '{{$_ex_object->_id}}', '{{$event}}', '{{$_element_id}}')">
	          {{mb_value object=$_ex_object->_ref_last_log field=date}} &ndash; 
	          {{mb_value object=$_ex_object->_ref_last_log field=user_id}}
	        </a>
	      </li>
	    {{foreachelse}}
	      <li class="empty">{{tr}}CExObject.none{{/tr}}</li>
	    {{/foreach}}
	    </ul>
	  </li>
	{{else}}
	  {{foreach from=$_ex_objects item=_ex_object}}
    <li>
	    <a href="#{{$_ex_object->_guid}}" style="font-weight: bold;"
	       onclick="showExClassForm({{$_ex_class_id}}, '{{$object->_guid}}', '{{$_ex_object}}', '{{$_ex_object->_id}}', '{{$event}}', '{{$_element_id}}')">
	      {{$_ex_class->name}}
	    </a>
			<ul>
				{{foreach from=$_ex_object->_ref_logs|@array_reverse item=_log}}
				  <li>
				  	<span style="float: right">
				  		{{mb_include module=mediusers template=inc_vw_mediuser mediuser=$_log->_ref_user->_ref_mediuser}}
							 &ndash; {{mb_value object=$_log field=date}}
				  	</span>
				  	<strong>{{mb_value object=$_log field=type}}</strong> &nbsp;&nbsp;
					</li>
				{{/foreach}}
			</ul>
    </li>
		{{/foreach}}
	{{/if}}
{{/foreach}}
</ul>
{{else if $ex_objects|@count == 1}}
	{{foreach from=$ex_objects item=_ex_objects key=_ex_class_id}}
    {{assign var=_ex_class value=$ex_classes.$_ex_class_id}}
	  {{foreach from=$_ex_objects item=_ex_object}}
		  <button class="edit" onclick="showExClassForm({{$_ex_class_id}}, '{{$object->_guid}}', '{{$_ex_object}}', '{{$_ex_object->_id}}', '{{$event}}', '{{$_element_id}}')">
		    {{$_ex_class->name}}
		  </button>
	  {{/foreach}}
	{{/foreach}}
{{/if}}
