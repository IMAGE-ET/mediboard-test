{{*
  * File viewer
  *
  * @category dPfiles
  * @package  Mediboard
  * @author   SARL OpenXtrem <dev@openxtrem.com>
  * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
  * @version  SVN: $Id:$
  * @link     http://www.mediboard.org
*}}

{{mb_script module="system" script="object_selector"}}
{{mb_script module="dPcompteRendu" script="modele_selector"}}
{{mb_script module="dPcompteRendu" script="document"}}
{{mb_script module="dPfiles" script="files"}}

<table class="main">
  <tr>
    <td>
      <form name="FrmClass" action="?m={{$m}}" method="get" onsubmit="return checkForm(this);">
      <input type="hidden" name="m" value="{{$m}}" />
      <input type="hidden" name="{{$actionType}}" value="{{$action}}" />
      <input type="hidden" name="dialog" value="{{$dialog}}" />
      <input type="hidden" name="file_id" value="{{$file->file_id}}" />
      
      <table class="form">
        <tr>
          <td>
            <label for="selClass" title="Type de l'objet courant">Type</label>
            <input type="text" readonly="readonly" ondblclick="ObjectSelector.init()" name="selClass" value="{{$selClass}}" />
          </td>
          <td>
            <label title="Nom de l'objet sélectionné">Nom</label>
            <input type="text" size="50" readonly="readonly" ondblclick="ObjectSelector.init()" name="selView" value="{{$selView}}" />
            <button type="button" onclick="ObjectSelector.init()" class="search">{{tr}}Search{{/tr}}</button>
            <input type="hidden" name="selKey" value="{{$selKey}}" onchange="this.form.submit()" />
            <input type="hidden" name="keywords" value="{{$keywords}}" />
            <script type="text/javascript">
              ObjectSelector.init = function(){
                this.sForm     = "FrmClass";
                this.sId       = "selKey";
                this.sView     = "selView";
                this.sClass    = "selClass";
                this.onlyclass = "false"; 
                this.pop();
              }
            </script>
          </td>

          {{if $selKey}}
          <td>
            <select name="typeVue" onchange="if (this.form.onsubmit()) { this.form.submit() };">
              <option value="0" {{if $typeVue == 0}}selected="selected"{{/if}}>Miniatures et aperçus</option>
              <option value="1" {{if $typeVue == 1}}selected="selected"{{/if}}>Miniatures seules</option>
            </select>
          </td>
          {{/if}}
        </tr>
      </table>
      </form>
    </td>
  </tr>
  {{if $selClass && $selKey}}
  <tr>
    <td>
      <table class="form">
        <tr>
          <th class="category" colspan="2">{{$object->_view}}</th>
        </tr>
        <tr>
          {{if $typeVue==1}}
          <td colspan="2" id="listView">
            {{mb_include module=files template=inc_list_view_colonne praticienId=0}}
          {{else}}
          <td style="width: 400px;" id="listView">
            {{mb_include module=files template=inc_list_view}}
          </td>
          <td id="bigView" style="text-align: center;">
            {{mb_include module=files template=inc_preview_file}}
          </td>
          {{/if}}
        </tr>
      </table>
    </td>
  </tr>
  {{/if}}
</table>