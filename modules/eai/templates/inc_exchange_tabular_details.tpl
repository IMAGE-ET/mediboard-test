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

<script>
  Main.add(function(){
    Control.Tabs.create("message-tab");
  });
</script>

<h1>{{$segment_group->description}} ({{$segment_group->version}}) <span class="type">{{$segment_group->name}}</span></h1>

<ul class="control_tabs" id="message-tab">
  <li><a href="#message-tree">Arbre</a></li>
  <li><a href="#message-er7">ER7</a></li>
  <li><a href="#message-errors" {{if $segment_group->errors|@count}} class="wrong" {{/if}}>Erreurs</a></li>
</ul>

<hr class="control_tabs" />
    
<ul id="message-tree" style="display: none;" class="hl7-tree">
  {{mb_include module=hl7 template=inc_segment_group_children segment_group=$segment_group}}
</ul>

<div id="message-er7" style="display: none;">
  {{$segment_group->flatten(true)|smarty:nodefaults}}
</div>

<div id="message-errors" style="display: none;">
  {{mb_include module=hl7 template=inc_hl7v2_errors errors=$segment_group->errors}}
</div>
