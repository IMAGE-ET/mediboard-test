{{*
 * $Id$
 *  
 * @category Pmsi
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @link     http://www.mediboard.org*}}

{{mb_script module="pmsi"           script="PMSI"}}
<script type="text/javascript">
  Main.add(function () {
    Calendar.regField(getForm("changeDate").date, null, {noView: true});
    Control.Tabs.create("tabs-category", true, {
      afterChange: function (container) {
        switch (container.id) {
          case "operations"  :
            var form = getForm("changeDate");
            PMSI.loadOperations(form);
            break;
          case "urgences" :
            var form = getForm("changeDate");
            PMSI.loadUrgences(form);
            break;
          default :
            var form = getForm("changeDate");
            PMSI.loadOperations(form);
            break;
        }
      }
    });
  });

  changePageOp = function (page) {
    PMSI.loadOperations(getForm("changeDate"),page);
  };

  changePageUrg  = function (page) {
    PMSI.loadUrgences(getForm("changeDate"),page);
  };
</script>

<ul id="tabs-category" class="control_tabs">
  {{foreach from=$counts key=category item=count}}
    <li onmousedown="">
      <a href="#{{$category}}"
         {{if !$count.total}}class="empty"{{/if}}
        {{if $count.facturees != $count.total}}class="wrong"{{/if}}>
        {{tr}}COperation-{{$category}}{{/tr}}
        <small>
          {{if $count.facturees == $count.total}}
            ({{$count.total}})
          {{else}}
            ({{$count.facturees}}/{{$count.total}})
          {{/if}}
        </small>
      </a>
    </li>
  {{/foreach}}
</ul>
<div id="operations" style="display: none;"></div>
<div id="urgences" style="display: none;"></div>