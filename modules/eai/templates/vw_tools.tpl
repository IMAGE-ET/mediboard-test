{{*
 * View tools EAI
 *  
 * @category EAI
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
*}}

{{foreach from=$tools key=_tool_class item=_tools}}
  {{foreach from=$_tools item=_tool}}
  <script>
    Main.add(function(){
      var form = getForm("tools-{{$_tool_class}}-{{$_tool}}");
      
      form.count.addSpinner({min: 1});
    });
    
    function next{{$_tool}}(){
      var form = getForm("tools-{{$_tool_class}}-{{$_tool}}");
    
      if (!$V(form["continue"])) {
        return;
      }
    
      form.onsubmit();
    }
  </script>
  {{/foreach}}
{{/foreach}}
        
<table class="main tbl" id="CEAI-tools">
  <tr>
    <th colspan="2" class="title">{{tr}}CEAI-tools{{/tr}}</th>
  </tr>
  
  {{foreach from=$tools key=_tool_class item=_tools}}
    <tr>
      <th colspan="2" class="section">{{tr}}CEAI-tools-{{$_tool_class}}{{/tr}}</th>
    </tr>
    
    {{foreach from=$_tools item=_tool}}
    <tr>
      <th colspan="2" class="category">{{tr}}CEAI-tools-{{$_tool_class}}-{{$_tool}}{{/tr}}</th>
    </tr>
    <tr>
      <td class="narrow">
        <form name="tools-{{$_tool_class}}-{{$_tool}}" method="get" action="?" 
          onsubmit="return onSubmitFormAjax(this, null, 'tools-{{$_tool_class}}-{{$_tool}}')">
          <input type="hidden" name="m" value="eai" />
          <input type="hidden" name="a" value="ajax_tools" />
          <input type="hidden" name="tool" value="{{$_tool}}" />
          <input type="hidden" name="suppressHeaders" value="1" />
          
          <select name="group_id">
            <option value=""> &ndash; Tous</option>
            {{foreach from=$groups item=_group}}
              <option value="{{$_group->_id}}" {{if $_group->_id == $g}}selected{{/if}}>{{$_group}}</option>
            {{/foreach}} 
          </select>  <br />
          
          <select name="exchange_class">
            {{foreach from=$exchanges_classes key=sub_classes item=_child_classes}}
              <optgroup label="{{tr}}{{$sub_classes}}{{/tr}}">
                {{foreach from=$_child_classes item=_class}}
                    <option value="{{$_class->_class}}">{{tr}}{{$_class->_class}}{{/tr}}</option>
                {{/foreach}}
              </optgroup>
            {{/foreach}}
          </select> <br />
          
          <input type="text" name="count" value="100" size="3" title="Nombre d'échanges à traiter" />
          <input type="text" name="error_code" value="{{if $_tool == "detect_collision"}}E213{{/if}}" size="15" placeholder="Code erreur" 
            title="Code de l'erreur dans le contenu de l'acquittement"/> <br />
          <label><input type="checkbox" name="continue" value="1" title="Automatique" /> Automatique</label>
  
          <button type="submit" class="change">{{tr}}CEAI-tools-{{$_tool_class}}-{{$_tool}}-button{{/tr}}</button>
        </form>
      </td>
      <td id="tools-{{$_tool_class}}-{{$_tool}}"></td>
    </tr>
    {{/foreach}}
  {{/foreach}}
</table>