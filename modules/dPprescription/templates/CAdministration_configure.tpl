{{* $Id: configure.tpl 10842 2010-12-08 21:57:35Z MyttO $ *}}

{{*
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision: 10842 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{assign var=class value=CAdministration}}

<form name="EditConfig-{{$class}}" action="?m={{$m}}&amp;{{$actionType}}=configure" method="post" onsubmit="return onSubmitFormAjax(this)">
  <input type="hidden" name="dosql" value="do_configure" />
  <input type="hidden" name="m" value="system" />

	<table class="form">

    {{mb_include module=system template=inc_config_category}}
		
    {{assign var=var value="hors_plage"}}
    <tr>
      <th class="title" colspan="2">{{tr}}{{$class}}{{/tr}} en dehors des plages prevues</th>
    </tr>
    <tr>  
      <td colspan="2" style="text-align: center">
        <label for="{{$m}}[{{$class}}][{{$var}}]">Oui</label>
        <input type="radio" name="{{$m}}[{{$class}}][{{$var}}]" value="1" {{if $conf.$m.$class.$var == "1"}}checked="checked"{{/if}}/> 
        <label for="{{$m}}[{{$class}}][{{$var}}]">Non</label>
        <input type="radio" name="{{$m}}[{{$class}}][{{$var}}]" value="0" {{if $conf.$m.$class.$var == "0"}}checked="checked"{{/if}}/> 
      </td>             
    </tr>
		
	  <tr>
	    <td class="button" colspan="2">
	      <button class="modify" type="submit">{{tr}}Save{{/tr}}</button>
	    </td>
	  </tr>
	</table>
</form>
