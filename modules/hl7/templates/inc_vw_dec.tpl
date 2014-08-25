{{*
 * $Id$
 *  
 * @category HL7
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
*}}

{{mb_script module="system" script="object_selector"}}

<table class="main form">
  <tr>
    <td>
      <form name="object-selector-form" method="get" onsubmit="return Url.update(this, 'obr-list');">
        <input type="hidden" name="m" value="hl7" />
        <input type="hidden" name="a" value="ajax_list_observation_results" />

        <select name="object_class">
          {{foreach from=$object_classes item=_class}}
            <option value="{{$_class}}" {{if $object_class == $_class}} selected {{/if}} >
              {{tr}}{{$_class}}{{/tr}}
            </option>
          {{/foreach}}
        </select>
        <input type="text" name="_object_view" value="{{$object}}" readonly="readonly" size="50" />
        <input type="hidden" name="object_id" value="{{$object_id}}" />
        <button type="button" class="search notext" onclick="ObjectSelector.init()">
          Chercher un objet
        </button>
        <script type="text/javascript">
          ObjectSelector.init = function(){
            this.sForm     = "object-selector-form";
            this.sId       = "object_id";
            this.sView     = "_object_view";
            this.sClass    = "object_class";
            this.onlyclass = "true";
            this.pop();
          }
        </script>

        <button class="search">{{tr}}Display{{/tr}}</button>
      </form>
    </td>
  </tr>
</table>

<div id="obr-list"></div>