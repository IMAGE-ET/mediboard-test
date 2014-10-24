{{*
 * $Id$
 *  
 * @category Tasking
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  OXOL, see http://www.mediboard.org/public/OXOL
 * @version  $Revision$
 * @link     http://www.mediboard.org
*}}

<table id="help" class="tbl">
  {{foreach from=$styles key=style item=_examples}}
    <tr>
      <th colspan="3" class="category">{{tr}}{{$style}}{{/tr}}</th>
    </tr>
    {{if $_examples|@is_array}}
      {{foreach from=$_examples item=_example}}
        <tr>
          <td style="white-space: pre">{{$_example}}</td>
          <td>{{$_example|smarty:nodefaults|markdown}}</td>
          <td style="white-space: pre"><pre>{{$_example|smarty:nodefaults|markdown|htmlentities}}</pre></td>
        </tr>
      {{/foreach}}
    {{else}}
      <tr>
        <td style="white-space: pre">{{$_examples}}</td>
        <td style="white-space: pre">{{$_examples|smarty:nodefaults|markdown}}</td>
        <td style="white-space: pre"><pre>{{$_examples|smarty:nodefaults|markdown|htmlentities}}</pre></td>
      </tr>
    {{/if}}

  {{/foreach}}
</table>