{{*
 * $Id$
 *  
 * @category Fomulaires
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  OXOL, see http://www.mediboard.org/public/OXOL
 * @version  $Revision$
 * @link     http://www.mediboard.org
*}}

{{mb_default var=display value="icon"}}

{{assign var=ex_object value=$link->_ref_ex_object}}
{{assign var=ex_class value=$ex_object->_ref_ex_class}}

{{if $display == "icon"}}
  {{mb_script module=patients script=fileviewer ajax=true}}
  <table class="layout table_icon_fileview" onmouseover="ObjectTooltip.createEx(this, '{{$ex_object->_class}}_{{$ex_class->_id}}-{{$ex_object->_id}}')">
    <tr>
      <td style="text-align: center; height: 92px;">
        <div style="width: 64px; height: 92px; margin: auto; border: 0 !important;" class="icon_fileview">
          <img src="images/pictures/medifile.png" />
        </div>
      </td>
    </tr>
    <tr>
      <td class="text item_name" style="text-align: center; vertical-align: top;">{{$ex_class->_icon_name}}</td>
    </tr>
  </table>
  {{mb_return}}
{{/if}}

<tr>
  <td class="narrow">
    <span style="font-family: FontAwesome; font-size: 11pt;">&#xf0f7;</span>
  </td>
  <td class="item_name">
    {{$ex_class->name}}
  </td>
  <td style="width: 25%">
  </td>
  <td>
    <span onmouseover="ObjectTooltip.createEx(this, '{{$ex_object->_ref_object->_guid}}')" class="list_file_name">
      {{$ex_object->_ref_object}}
    </span>
  </td>
  <td class="narrow">
    {{mb_value object=$ex_object field=datetime_edit}}
  </td>
</tr>

