{{*
 * $Id$
 *  
 * @package    Mediboard
 * @subpackage system
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 * @link       http://www.mediboard.org
*}}

{{if $is_last}}
  {{unique_id var=uid}}
  <script type="text/javascript">
    Main.add(function(){
      var cont = $('set-container-{{$uid}}'),
        element = cont.down('input[type=hidden]'),
        tokenField = new TokenField(element);

      cont.select('input[type=checkbox]').invoke('observe', 'click', function(event){
        element.fire('ui:change');
        var elt = Event.element(event);
        tokenField.toggle(elt.value, elt.checked);
      });
    });
  </script>

  <div style="max-height: 24em; overflow-y: scroll; border: 1px solid #999; background: rgba(255,255,255,0.5); padding: 3px;" class="columns-2" id="set-container-{{$uid}}">
    {{assign var=_list value='|'|explode:$_prop.list}}
    {{assign var=_list_value value="|"|explode:$value}}
    <input type="hidden" class="{{$_prop.string}}" name="c[{{$_feature}}]" {{if $is_inherited}} disabled {{/if}} value="{{$value}}" />

    {{foreach from=$_list item=_item}}
      <label title="{{tr}}config-{{$_feature|replace:' ':'-'}}.{{$_item}}{{/tr}}">
        <input type="checkbox" value="{{$_item}}" {{if in_array($_item, $_list_value)}} checked {{/if}} {{if $is_inherited}} disabled {{/if}} />
        {{tr}}config-{{$_feature|replace:' ':'-'}}.{{$_item}}{{/tr}}
      </label>
      <br />
    {{/foreach}}
  </div>
{{else}}
  {{assign var=_list value="|"|explode:$value}}
  {{foreach from=$_list item=_item name=_list}}
    {{tr}}config-{{$feature|replace:' ':'-'}}.{{$_item}}{{/tr}}{{if !$smarty.foreach._list.last}}, {{/if}}
  {{/foreach}}
{{/if}}