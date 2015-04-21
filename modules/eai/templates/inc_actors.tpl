{{*
 * View Interop Senders EAI
 *  
 * @category EAI
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
*}}

{{mb_default var=count_actors_actif value="-"}}
{{mb_default var=count_actors value="-"}}

<script>
  Main.add(function () {
    var link = $('tabs-actors').select("a[href=#{{$parent_class}}s]")[0];
    link.update("{{tr}}{{$parent_class}}-court{{/tr}} (<span title='Total actifs'>{{$count_actors_actif}}</span> / <span title='Total'>{{$count_actors}}</span>)");
    {{if $actors|@count == '0'}}
      link.addClassName('empty');
    {{else}}
      link.removeClassName('empty');
    {{/if}}
  });
</script>

<table class="main tbl">
  <tr>
    <th class="narrow"></th>
    <th>{{mb_label object=$actor field="nom"}}</th>
    <th>{{mb_label object=$actor field="group_id"}}</th>
    <th></th>
  </tr>
  {{foreach from=$actors key=type_actor item=_actors}}
    <tr class="alternate">
      <th class="section" colspan="6">
        <a style="float: right" class="button new notext" href="#"
            onclick="InteropActor.editActor(null, '{{$type_actor}}', '{{$parent_class}}');"
            title="Créer acteurs {{tr}}{{$type_actor}}{{/tr}}">
          {{tr}}{{$type_actor}}-title-create{{/tr}}
        </a>
        {{tr}}{{$type_actor}}{{/tr}}
      </th>
    </tr>

    {{foreach from=$_actors item=_actor}}
      {{mb_include template=inc_actor}}
    {{/foreach}}
  {{/foreach}}
</table>