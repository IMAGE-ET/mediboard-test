{{*
 * $Id$
 *  
 * @category Modèles
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
*}}

{{mb_default var=display value="icon"}}

{{if $display == "icon"}}
  {{mb_script module=patients script=fileviewer ajax=true}}

  <table class="layout" style="width: 300px; display: inline-block;" onmouseover="expandDocDisplay(this, true)" onmouseout="expandDocDisplay(this)">
    <tr>
      <td style="width: 80px;">
        {{assign var="src" value="images/pictures/medifile.png"}}
        {{if $conf.dPcompteRendu.CCompteRendu.pdf_thumbnails && $app->user_prefs.pdf_and_thumbs}}
          {{assign var=file value=$doc->loadFile()}}
          {{assign var="src" value="?m=files&raw=fileviewer&file_id=`$file->_id`&phpThumb=1&w=64&h=92"}}
        {{/if}}

        <img class="thumbnail" src="{{$src}}" style="background: white; max-width: 64px; max-height: 92px;"
             onclick="popFile('{{$doc->_class}}', '{{$doc->_id}}', '{{$file->_class}}', '{{$file->_id}}', '0')" />
      </td>
      <td style="visibility: hidden" class="toolbar">
        {{$doc->creation_date|date_format:$conf.datetime}} <br />

        {{mb_include module=compteRendu template=inc_fileviewer_toolbar}}
      </td>
    </tr>
    <tr>
      <td colspan="2">{{$doc}}</td>
    </tr>
  </table>

  {{mb_return}}
{{/if}}

<table class="tbl" onmouseover="expandDocDisplay(this, true)" onmouseout="expandDocDisplay(this)">
  <tr>
    <td style="width: 75%">
      {{$doc}}
    </td>
    <td>
      {{$doc->creation_date|date_format:$conf.datetime}}
      <span class="toolbar" style="float: right; visibility: hidden;">
        {{mb_include module=compteRendu template=inc_fileviewer_toolbar}}
      </span>
    </td>
  </tr>
</table>