{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage messagerie
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script type="text/javascript">
  Main.add(function() {
    var editor = CKEDITOR.replace("mailarea");
    editor.on("instanceReady", function(e) {

    });
  });
</script>

<table class="tbl">
  <tr>
    <th>{{mb_label object=$mail field=subject}}</th><td style="text-align: left;">{{mb_value object=$mail field=subject}}</td>
  </tr>
  <tr>
    <th>{{mb_label object=$mail field=from}}</th><td style="text-align: left;">{{mb_value object=$mail field=from}}</td>
  </tr>
  <tr>
    <th>{{mb_label object=$mail field=to}}</th><td style="text-align: left;">{{mb_value object=$mail field=to}}</td>
  </tr>
  <tr>
    <th>{{mb_label object=$mail field=date}}</th><td>{{mb_value object=$mail field=date}}</td>
  </tr>
  <tr>
    <th colspan="2">{{mb_label object=$mail field=content}}</th>
  </tr>
  <tr>
    <td colspan="2" style="text-align: left;">
      {{if $mail->content.text.html|@count && $app->user_prefs.ViewMailAsHtml}}
        <textarea id="mailarea">{{$mail->content.text.html|smarty:nodefaults}}</textarea>
      {{else}}
        {{$mail->content.text.plain}}
      {{/if}}
    </td>
  </tr>
  {{if $mail->content.attachments|count}}
  <tr><th colspan="2">{{tr}}Attachments{{/tr}}</th></tr>
  <tr>
    <style>
      svg,img {
        max-width:300px;
        max-height:300px;
        float:left;
      }
    </style>
    <td colspan="2">
      {{foreach from= $mail->content.attachments key=type item=_attachment}}
        {{if $type=="IMG"}}
          {{foreach from=$_attachment item=_img}}
            <a href="data:image/png;base64,{{$_img}}"><img src="data:image/png;base64,{{$_img}}" alt=""/></a>
          {{/foreach}}
        {{elseif $type=="SVG"}}
          {{foreach from=$_attachment item=_img}}
            {{$_img|smarty:nodefaults}}
          {{/foreach}}
        {{else}}
          {{foreach from=$_attachment item=_file}}
            <a href="{{$_file}}">test</a>
          {{/foreach}}

        {{/if}}
      {{/foreach}}

    </td>
  </tr>
  {{/if}}
</table>



