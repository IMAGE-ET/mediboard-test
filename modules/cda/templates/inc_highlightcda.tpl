{{*
 * $Id$
 *
 * Vue des différents volets (arbre, xml, validation)
 *
 * @category CDA
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @link     http://www.mediboard.org*}}

<script>
  Main.add(function(){
    Control.Tabs.create("message-tab-cda", true);
    var tree = new TreeView("message-cda-tree");
    tree.collapseAll();
  });
</script>
<div id="message-cda">

  <br/>
  <ul class="control_tabs" id="message-tab-cda">
    <li><a href="#message-cda-tree">{{tr}}tree{{/tr}}</a></li>
    <li><a href="#message-cda-xml">XML</a></li>
    <li>
      <a href="#message-cda-errors" {{if $treecda->validate|@count == 0}}class="special"{{else}} class="wrong" {{/if}}>
        {{tr}}validation{{/tr}} XSD
      </a>
    </li>
    <li>
      <a href="#message-cda-errors-schematron" {{if $treecda->validateSchematron}}class="wrong"{{else}} class="special" {{/if}}>
        {{tr}}validation{{/tr}} schematron
      </a>
    </li>
  </ul>
  <hr class="control_tabs" />

  <div id="message-cda-tree" style="display: none;">
    <ul class="hl7-tree">
      {{mb_include template=inc_tree_cda}}
    </ul>
  </div>

  <div id="message-cda-xml" style="display: none;">
    {{$treecda->xml|smarty:nodefaults}}
  </div>

  <div id="message-cda-errors" style="display: none;">
    {{mb_include template="inc_highlightcda_validate"}}
  </div>

  <div id="message-cda-errors-schematron" style="display: none;">
    {{mb_include template="inc_highlightcda_validate_schematron"}}
  </div>
</div>