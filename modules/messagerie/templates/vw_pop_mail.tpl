{{*
 * $Id$
 *  
 * @category Messagerie
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
*}}

<h1>Mail {{$mail_id}}, {{$overview->subject}}</h1>

<h2>Mail dans MB</h2>
<table class="tbl">
  <tr>
    <th class="title" colspan="2">Mail MB</th>
  </tr>
  <tr>
    <td>{{$mail|@mbTrace}}</td>
  </tr>
</table>

<h2>Overview</h2>
<table class="tbl">
  {{foreach from=$overview key=key item=value}}
    <tr>
      <th class="narrow">{{$key}}</th>
      <td>{{$value}}</td>
    </tr>
  {{/foreach}}
</table>

<h2>Structure</h2>
<table class="tbl">
  <tr>
    <td>
      {{$structure|mbTrace}}
    </td>
  </tr>
</table>

<h2>Infos</h2>
<table class="tbl">
  {{foreach from=$infos key=key item=value}}
    <tr>
      <th class="narrow">{{$key}}</th>
      <td>
        {{if is_array($value)}}
          {{$value|@mbTrace}}
        {{else}}
          {{$value}}
        {{/if}}
      </td>
    </tr>
  {{/foreach}}
</table>

<!-- CONTENT -->
<h2>Body</h2>
<table class="tbl">
  {{foreach from=$content key=key item=_content}}
    <tr>
      <th style="text-align: left;">{{$key}}</th>
    </tr>
    <tr>
      <td>{{$_content|@mbTrace}}</td>
    </tr>
  {{/foreach}}
</table>

<!-- attachments -->
<h2>Attachments</h2>
<table class="tbl">
  <tr>
    <td>{{$attachments|@mbTrace}}</td>
  </tr>
</table>
