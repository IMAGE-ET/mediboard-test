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
    <th>{{mb_label object=$mail field=subject}}</th><td style="text-align: left;" colspan="3">{{mb_value object=$mail field=subject}}</td>
  </tr>
  <tr>
    <th>{{mb_label object=$mail field=from}}</th><td style="text-align: left;" colspan="3">{{mb_value object=$mail field=from}}</td>
  </tr>
  <tr>
    <th>{{mb_label object=$mail field=to}}</th><td style="text-align: left;" colspan="3">{{mb_value object=$mail field=to}}</td>
  </tr>
  <tr>
    <th>{{mb_label object=$mail field=date_inbox}}</th><td colspan="3">{{mb_value object=$mail field=date_inbox}}</td>
  </tr>
  <tr>
    <th colspan="4">{{mb_label object=$mail field=text_plain}}</th>
  </tr>
  <tr>
    <td colspan="4" style="text-align: left;">
      {{if $mail->text_html|@count && $app->user_prefs.ViewMailAsHtml}}
        {{* <textarea id="mailarea">{{$mail->content.text.html|smarty:nodefaults}}</textarea> *}}
        {{$mail->text_html|smarty:nodefaults}}
      {{else}}
        {{$mail->text_plain}}
      {{/if}}
    </td>
  </tr>
  {{if $mail->attachments|count}}
    <tr><th colspan="4">{{tr}}Attachments{{/tr}}</th></tr>
    <style>
      svg,img {
        max-width:100%;
        max-height:30%;
      }
    </style>
    {{foreach from= $mail->attachments key=type item=_attachment}}
      <tr>
        <td style="text-align:center;">{{mb_include template=inc_show_attachments}}</td>
        <td>{{$_attachment->name}}</td>
        <td>{{$_attachment->subtype}}</td>
        <td>{{$_attachment->bytes}} bytes</td>
      </tr>
    {{/foreach}}
  {{/if}}
</table>



