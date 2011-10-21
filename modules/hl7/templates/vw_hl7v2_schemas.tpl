{{* $Id: configure.tpl 6341 2009-05-21 11:52:48Z mytto $ *}}

{{*
 * @package Mediboard
 * @subpackage hl7
 * @version $Revision: 6341 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script>
  function displaySchema(path) {
    var url = new Url('hl7', 'ajax_display_hl7v2_schema');
    url.addParam("path", path);
    url.requestUpdate("schema-view");
  }

  Main.add(function(){
    var tree = new TreeView("schemas-list");
    tree.collapseAll();
  });
</script>

<h2>Schemas HL7 v. {{$version}}</h2>

<table class="main">
  <tr>
    <td style="width: 20%" id="schemas-list">
      <ul>
      {{foreach from=$schemas key=type item=_paths}}
        <li>
          <strong>{{$type}}</strong>
          
          <ul style="font-family: monospace;">
            {{foreach from=$_paths item=_path key=_title}}
              <li>
                {{if $_path|@is_array}}
                  {{$_title}}
                  <ul>
                  {{foreach from=$_path item=_sub_path key=_sub_title}}
                    <li>
                      <a href="#1" data-path="{{$_sub_path}}" onclick="displaySchema(this.get('path')); return false">
                        {{$_sub_title}}
                      </a>
                    </li>
                  {{/foreach}}
                  </ul>
                {{else}}
                  <a href="#1" data-path="{{$_path}}" onclick="displaySchema(this.get('path')); return false">
                    {{$_title}}
                  </a>
                {{/if}}
              </li>
            {{/foreach}}
          </ul>
        </li>
      {{/foreach}}
      </ul>
    </td>
    <td>
      <div id="schema-view" style="position: fixed;"></div>
    </td>
  </tr>
</table>