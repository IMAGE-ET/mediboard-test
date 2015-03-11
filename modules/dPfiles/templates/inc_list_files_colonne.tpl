{{*
  * List docitems
  *
  * @category dPfiles
  * @package  Mediboard
  * @author   SARL OpenXtrem <dev@openxtrem.com>
  * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
  * @version  SVN: $Id:$
  * @link     http://www.mediboard.org
*}}

{{if isset($category_id|smarty:nodefaults)}}
  <script type="text/javascript">
    // Mise à jour des compteurs des documents
    Main.add(function() {
      var countDocItemTotal = {{$nbItems}};

      {{assign var=count_docitems value=0}}
      {{foreach from=$list item=_item}}
        {{if !$_item->annule}}
          {{math equation=x+1 x=$count_docitems assign=count_docitems}}
        {{/if}}
      {{/foreach}}

      var countDocItemCat = {{$count_docitems}};
      var button = $("docItem_{{$object->_guid}}");
      var tab = $("tab_category_{{$category_id}}");
      var countTab = tab.down("small");
      var tabPatient = $("listViewPatient");
      
      if (button) {
        button.update(countDocItemTotal);
        if (!countDocItemTotal) {
          button.addClassName("right-disabled");
          button.removeClassName("right");
        }
        else {
          button.removeClassName("right-disabled");
          button.addClassName("right");
        }
      }
      if (tab) {
        countTab.update("("+countDocItemCat+")");
        if (!countDocItemCat) {
          tab.addClassName("empty");
        }
        else {
          tab.removeClassName("empty");
        }
      }
      if (tabPatient) {
        tabPatient.down("span").update(countDocItemTotal);
        if (!countDocItemCat) {
          tabPatient.addClassName("empty");
        }
        else {
          tabPatient.removeClassName("empty");
        }
      }

    });
  </script>
{{/if}}

{{if !$app->touch_device}}
  <!-- Draggable -->
  <script>
    Main.add(function() {
      $$(".droppable").each(function(li) {
        Droppables.add(li, {
          onDrop: function(from, to, event) {
            Event.stop(event);
            var destGuid = to.get("guid");
            var fromGuid = from.get("targetFrom");
            var idFile   = from.get("id");
            var classFile   = from.get("class");
            var url = new Url("files","controllers/do_move_file");
            url.addParam("object_id", idFile);
            url.addParam("object_class", classFile);
            url.addParam("destination_guid", destGuid );
            url.requestUpdate("systemMsg", function() {
              $("docItem_"+destGuid).onclick();   //update destination
              $("docItem_"+fromGuid).onclick();   //update provenance
            });
          },
          accept: 'draggable',
          hoverclass:'dropover'
        });
      });

      $$(".draggable").each(function(a) {
        new Draggable(a, {
          onEnd: function(element, event) {
            Event.stop(event);
          },
          ghosting: true});
      });
    });
  </script>
{{/if}}

{{foreach from=$list item=_doc_item}}
  <div style="float: left; width: 220px; position: relative; {{if $_doc_item->annule}}display: none;{{/if}}"
  class="{{if $_doc_item->annule}}file_cancelled{{/if}} {{if $_doc_item->_count_dmp_document}}dmp-sent{{/if}}">
    <table class="tbl">
      <tbody class="hoverable">
        <tr class="{{if $_doc_item->annule}}hatching{{/if}}">
          <td rowspan="2" style="width: 70px; height: 112px; text-align: center">
            <div></div>
            {{assign var="elementId" value=$_doc_item->_id}}
            {{if $_doc_item->_class=="CCompteRendu"}}
              {{if $conf.dPcompteRendu.CCompteRendu.pdf_thumbnails && $app->user_prefs.pdf_and_thumbs}}
                {{assign var="file_id"  value=$_doc_item->_ref_file->_id}}
                {{assign var="srcImg"   value="?m=files&raw=fileviewer&file_id=$file_id&phpThumb=1&w=64&h=92"}}
              {{else}}
                {{assign var="srcImg" value="images/pictures/medifile.png"}}
              {{/if}}
            {{else}}
              {{assign var="srcImg" value="?m=files&raw=fileviewer&file_id=$elementId&phpThumb=1&w=64&h=92"}}
            {{/if}}

            <a href="#" {{if !$app->touch_device}}ondblclick{{else}}onclick{{/if}}="popFile('{{$object->_class}}', '{{$object->_id}}', '{{$_doc_item->_class}}', '{{$elementId}}', '0');">
              <img class="thumbnail {{if !$app->touch_device}}draggable{{/if}}" style="background: white; max-width:64px; max-height:92px;" src="{{$srcImg}}" data-id="{{$elementId}}" data-class="{{$_doc_item->_class}}" data-targetFrom="{{$_doc_item->object_class}}-{{$_doc_item->object_id}}"/>
            </a>
          </td>

          <!-- Tooltip -->
          <td class="text" style="height: 35px; overflow: auto">
            {{if $_doc_item instanceof CCompteRendu && $_doc_item->_is_locked}}
              <img src="style/mediboard/images/buttons/lock.png" onmouseover="ObjectTooltip.createEx(this, '{{$_doc_item->_guid}}', 'locker')"/>
            {{/if}}
            <span onmouseover="ObjectTooltip.createEx(this, '{{$_doc_item->_guid}}');">
              {{$_doc_item->_view|truncate:60}}
              {{if $_doc_item->private}}
                &mdash; <em>{{tr}}CCompteRendu-private{{/tr}}</em>
              {{/if}}
            </span>
          </td>
        </tr>
        <tr class="{{if $_doc_item->annule}}hatching{{/if}}">
          <!-- Toolbar -->
          <td class="button" style="height: 1px;">
            {{mb_include module=files template=inc_file_toolbar notext=notext}}
          </td>
        </tr>
      </tbody>
    </table>
  </div>
{{foreachelse}}
  <div class="empty">{{tr}}CMbObject-back-documents.empty{{/tr}}</div>
{{/foreach}}