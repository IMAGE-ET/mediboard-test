{{*
 * $Id$
 *  
 * @category Search
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @link     http://www.mediboard.org*}}

<!--Vue appellée lors du clic sur la loupe dans la recherche classique et journal de recherche afin d'avoir les détails sous format de volets-->

{{mb_script module=search script=search}}

<script>
  Main.add(function () {
    var tab = Control.Tabs.create('tabs-list_details', false);
    tab.setActiveTab('{{$tabActive}}');
    {{if $user_id}}
    Search.searchMoreDetailsLog('{{$date}}', {{$user_id}}, '{{$tabActive}}');
    {{else}}
    Search.searchMoreDetails('{{$object_ref_id}}', '{{$object_ref_class}}', '{{$fuzzy_search}}', '{{$tabActive}}');
    {{/if}}
  });
</script>
<table class="main layout">
  <tr>
    <td class="narrow" style="vertical-align: top">
      <ul id="tabs-list_details" class="control_tabs_vertical" style="width: 15em">
        {{foreach from=$agregation item=_agreg}}
          <li onmousedown="
          {{if $user_id}}
            Search.searchMoreDetailsLog('{{$date}}', '{{$user_id}}', '{{$_agreg.key}}');
          {{else}}
            Search.searchMoreDetails('{{$object_ref_id}}', '{{$object_ref_class}}', '{{$fuzzy_search}}', '{{$_agreg.key}}');
          {{/if}}
            ">
            <a href="#tab-{{$_agreg.key}}" style="line-height: 1em" class="{{if $_agreg.doc_count == 0}}empty{{/if}}">
              <span class="text">{{tr}}{{$_agreg.key}}{{/tr}} ({{$_agreg.doc_count}})</span>
            </a>
          </li>
        {{/foreach}}
      </ul>
    </td>
    <td style="vertical-align: top">
      {{foreach from=$agregation item=_div_agreg}}
        <div id="tab-{{$_div_agreg.key}}" style="display:none"></div>
      {{/foreach}}
    </td>
  </tr>
</table>