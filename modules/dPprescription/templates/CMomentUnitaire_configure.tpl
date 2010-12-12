{{* $Id: configure.tpl 10842 2010-12-08 21:57:35Z MyttO $ *}}

{{*
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision: 10842 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{assign var=class value=CMomentUnitaire}}

<form name="EditConfig-{{$class}}" action="?m={{$m}}&amp;{{$actionType}}=configure" method="post" onsubmit="return onSubmitFormAjax(this)">
  <input type="hidden" name="dosql" value="do_configure" />
  <input type="hidden" name="m" value="system" />

	<table class="form">

    {{assign var="var" value="principaux"}}
    <tr>
     <th class="category" colspan="2">
        <label for="{{$m}}[{{$class}}][{{$var}}]" title="{{tr}}config-{{$m}}-{{$class}}-{{$var}}{{/tr}}">
          {{tr}}config-{{$m}}-{{$class}}-{{$var}}{{/tr}}
        </label>    
     </th>
    </tr>
    <tr>  
      <td colspan="2" style="text-align: center">
        <label for="{{$m}}[{{$class}}][{{$var}}]">Oui</label>
        <input type="radio" name="{{$m}}[{{$class}}][{{$var}}]" value="1" {{if $conf.$m.$class.$var == "1"}}checked="checked"{{/if}}/> 
        <label for="{{$m}}[{{$class}}][{{$var}}]">Non</label>
        <input type="radio" name="{{$m}}[{{$class}}][{{$var}}]" value="0" {{if $conf.$m.$class.$var == "0"}}checked="checked"{{/if}}/> 
      </td>             
    </tr>

    {{assign var="var" value="poso_lite"}}
    <tr>
      <th class="category" colspan="2">
        <label for="{{$m}}[{{$class}}][{{$var}}]" title="{{tr}}config-{{$m}}-{{$class}}-{{$var}}{{/tr}}">
          {{tr}}config-{{$m}}-{{$class}}-{{$var}}{{/tr}}
        </label>    
      </th> 
    </tr>
      
    {{assign var="var_item" value="matin"}}
    <tr>  
      <th>
        <label for="{{$m}}[{{$class}}][{{$var}}][{{$var_item}}]" title="{{tr}}config-{{$m}}-{{$class}}-{{$var}}-{{$var_item}}{{/tr}}">
          {{tr}}config-{{$m}}-{{$class}}-{{$var}}-{{$var_item}}{{/tr}}
        </label>    
      </th>
      <td>
        <label for="{{$m}}[{{$class}}][{{$var}}][{{$var_item}}]">Oui</label>
        <input type="radio" name="{{$m}}[{{$class}}][{{$var}}][{{$var_item}}]" value="1" {{if $conf.$m.$class.$var.$var_item == "1"}}checked="checked"{{/if}}/> 
        <label for="{{$m}}[{{$class}}][{{$var}}][{{$var_item}}]">Non</label>
        <input type="radio" name="{{$m}}[{{$class}}][{{$var}}][{{$var_item}}]" value="0" {{if $conf.$m.$class.$var.$var_item == "0"}}checked="checked"{{/if}}/> 
      </td>             
    </tr>
    {{assign var="var_item" value="midi"}}
    <tr>  
      <th>
        <label for="{{$m}}[{{$class}}][{{$var}}][{{$var_item}}]" title="{{tr}}config-{{$m}}-{{$class}}-{{$var}}-{{$var_item}}{{/tr}}">
          {{tr}}config-{{$m}}-{{$class}}-{{$var}}-{{$var_item}}{{/tr}}
        </label>    
      </th>
      <td>
        <label for="{{$m}}[{{$class}}][{{$var}}][{{$var_item}}]">Oui</label>
        <input type="radio" name="{{$m}}[{{$class}}][{{$var}}][{{$var_item}}]" value="1" {{if $conf.$m.$class.$var.$var_item == "1"}}checked="checked"{{/if}}/> 
        <label for="{{$m}}[{{$class}}][{{$var}}][{{$var_item}}]">Non</label>
        <input type="radio" name="{{$m}}[{{$class}}][{{$var}}][{{$var_item}}]" value="0" {{if $conf.$m.$class.$var.$var_item == "0"}}checked="checked"{{/if}}/> 
      </td>             
    </tr>
    {{assign var="var_item" value="apres_midi"}}
    <tr>  
      <th>
        <label for="{{$m}}[{{$class}}][{{$var}}][{{$var_item}}]" title="{{tr}}config-{{$m}}-{{$class}}-{{$var}}-{{$var_item}}{{/tr}}">
          {{tr}}config-{{$m}}-{{$class}}-{{$var}}-{{$var_item}}{{/tr}}
        </label>    
      </th>
      <td>  
        <label for="{{$m}}[{{$class}}][{{$var}}][{{$var_item}}]">Oui</label>
        <input type="radio" name="{{$m}}[{{$class}}][{{$var}}][{{$var_item}}]" value="1" {{if $conf.$m.$class.$var.$var_item == "1"}}checked="checked"{{/if}}/> 
        <label for="{{$m}}[{{$class}}][{{$var}}][{{$var_item}}]">Non</label>
        <input type="radio" name="{{$m}}[{{$class}}][{{$var}}][{{$var_item}}]" value="0" {{if $conf.$m.$class.$var.$var_item == "0"}}checked="checked"{{/if}}/> 
      </td>             
    </tr>
    {{assign var="var_item" value="soir"}}
    <tr>  
      <th>
        <label for="{{$m}}[{{$class}}][{{$var}}][{{$var_item}}]" title="{{tr}}config-{{$m}}-{{$class}}-{{$var}}-{{$var_item}}{{/tr}}">
          {{tr}}config-{{$m}}-{{$class}}-{{$var}}-{{$var_item}}{{/tr}}
        </label>    
      </th>
      <td>
        <label for="{{$m}}[{{$class}}][{{$var}}][{{$var_item}}]">Oui</label>
        <input type="radio" name="{{$m}}[{{$class}}][{{$var}}][{{$var_item}}]" value="1" {{if $conf.$m.$class.$var.$var_item == "1"}}checked="checked"{{/if}}/> 
        <label for="{{$m}}[{{$class}}][{{$var}}][{{$var_item}}]">Non</label>
        <input type="radio" name="{{$m}}[{{$class}}][{{$var}}][{{$var_item}}]" value="0" {{if $conf.$m.$class.$var.$var_item == "0"}}checked="checked"{{/if}}/> 
      </td>             
    </tr>
    {{assign var="var_item" value="coucher"}}
    <tr>  
      <th>
        <label for="{{$m}}[{{$class}}][{{$var}}][{{$var_item}}]" title="{{tr}}config-{{$m}}-{{$class}}-{{$var}}-{{$var_item}}{{/tr}}">
          {{tr}}config-{{$m}}-{{$class}}-{{$var}}-{{$var_item}}{{/tr}}
        </label>    
      </th>
      <td>
        <label for="{{$m}}[{{$class}}][{{$var}}][{{$var_item}}]">Oui</label>
        <input type="radio" name="{{$m}}[{{$class}}][{{$var}}][{{$var_item}}]" value="1" {{if $conf.$m.$class.$var.$var_item == "1"}}checked="checked"{{/if}}/> 
        <label for="{{$m}}[{{$class}}][{{$var}}][{{$var_item}}]">Non</label>
        <input type="radio" name="{{$m}}[{{$class}}][{{$var}}][{{$var_item}}]" value="0" {{if $conf.$m.$class.$var.$var_item == "0"}}checked="checked"{{/if}}/> 
      </td>             
    </tr>

	  <tr>
	    <td class="button" colspan="2">
	      <button class="modify" type="submit">{{tr}}Save{{/tr}}</button>
	    </td>
	  </tr>
	</table>
</form>
