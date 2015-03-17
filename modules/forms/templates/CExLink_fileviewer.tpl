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

{{assign var=ex_object value=$link->_ref_ex_object}}
{{assign var=ex_class value=$ex_object->_ref_ex_class}}

{{mb_default var=display value="icon"}}

{{if $display == "icon"}}
  {{mb_script module=patients script=fileviewer ajax=true}}

  <table class="layout" style="width: 300px; display: inline-block;" onmouseover="expandDocDisplay(this, true)" onmouseout="expandDocDisplay(this)">
    <tr>
      <td style="width: 80px;">
        <img class="thumbnail" src="images/pictures/medifile.png" />
      </td>
      <td style="visibility: hidden" class="toolbar">
        {{$ex_object->datetime_create|date_format:$conf.datetime}} <br />

        {{mb_include module=forms template=inc_fileviewer_toolbar}}
      </td>
    </tr>
    <tr>
      <td colspan="2">{{$ex_class->name}}</td>
    </tr>
  </table>
  {{mb_return}}
{{/if}}

<table class="tbl" onmouseover="expandDocDisplay(this, true)" onmouseout="expandDocDisplay(this)">
  <tr>
    <td style="width: 75%">
      {{$ex_class->name}}
    </td>
    <td>
      {{$ex_object->datetime_create|date_format:$conf.datetime}}

      <span class="toolbar" style="float: right; visibility: hidden;">
        {{mb_include module=forms template=inc_fileviewer_toolbar}}
      </span>
    </td>
  </tr>
</table>
