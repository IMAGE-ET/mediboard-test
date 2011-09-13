{{*
 * Details Exchange Tabular EAI
 *  
 * @category EAI
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
*}}

<style>
  ul, ol {
    line-height: 1.4;
    padding-left: 2em;
    margin-bottom: 6px;
  }
  
  ol {
    list-style-type: none;
  }
  
  .type {
    color: #2D9A00;
  }
  
  .type:before {
    content: "(";
  }
  
  .type:after {
    content: ")";
  }
  
  .field-name {
    color: #3252A7;
    margin-right: 5px;
    display: inline-block;
    width: 5em;
    white-space: nowrap;
  }
  
  .field-description {
    color: #999;
  }
  
  .value {
    background: #eee;
    padding: 0 2px;
  }
  
  .field-item {
    border: 1px dotted #ccc;
    margin: 1px;
  }
  
  .field-item > ol {
    padding-left: 2px;
    margin-bottom: 0px;
  }
  
  .field-empty {
    opacity: 0.5;
  }
  
  #message-tree {
    text-align:left;
  }
</style>

<script>
  Main.add(function(){
    Control.Tabs.create("message-tab");
  });
</script>

<table class="main layout">
  <tr>
    <td class="text">
      <h1>{{$segment_group->description}} ({{$segment_group->version}}) <span class="type">{{$segment_group->name}}</span></h1>
      
      <ul class="control_tabs" id="message-tab">
        <li><a href="#message-tree">Arbre</a></li>
        <li><a href="#message-er7">ER7</a></li>
        <li><a href="#message-errors" {{if $segment_group->errors|@count}} class="wrong" {{/if}}>Erreurs</a></li>
      </ul>
      
      <hr class="control_tabs" />
          
      <ul id="message-tree" style="display: none;">
        {{mb_include module=hl7 template=inc_segment_group_children segment_group=$segment_group}}
      </ul>
      
      <div id="message-er7" style="display: none; overflow: scroll">
        {{$segment_group->flatten(true)|smarty:nodefaults}}
      </div>
      
      <div id="message-errors" style="display: none;">
        {{mb_include module=hl7 template=inc_hl7v2_errors errors=$segment_group->errors}}
      </div>
    </td>
  </tr>
</table>