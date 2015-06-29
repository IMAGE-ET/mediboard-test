{{mb_default var=mozaic value=0}}

{{if $can->admin}}
  <form name="DeleteAll-{{$object->_guid}}" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">
    <input type="hidden" name="m" value="dPfiles" />
    <input type="hidden" name="dosql" value="do_file_multi_delete" />
    <input type="hidden" name="del" value="1" />
    <input type="hidden" name="object_guid" value="{{$object->_guid}}">

    <button class="trash" type="button" style="float: right;" onclick="File.removeAll(this, '{{$object->_guid}}')">
      {{tr}}Delete-all{{/tr}}
    </button>
  </form>
{{/if}}

{{if $show_actions}}
  {{if $can_files->edit && $object->_can->read}}
    <button class="new" onclick="File.upload('{{$object->_class}}','{{$object->_id}}', '')" >
      {{tr}}CFile-title-create{{/tr}}
    </button>

    {{if $mozaic}}
      <button class="new" onclick="File.createMozaic('{{$object->_class}}-{{$object->_id}}', '')">{{tr}}CFile-create-mozaic{{/tr}}</button>
    {{/if}}

    {{if "drawing"|module_active}}
      <button class="drawing notext" type="button" onclick="editDrawing(null, null, '{{$object->_guid}}');">{{tr}}CDrawingItem.new{{/tr}}</button>
    {{/if}}

    {{if $app->user_prefs.directory_to_watch}}
      <button class="new yopletbutton" disabled onclick="File.applet.modalOpen('{{$object->_guid}}')">
        {{tr}}Upload{{/tr}}
      </button>
    {{/if}}
  {{/if}}
{{/if}}

{{if $object->_nb_cancelled_files}}
  <button class="hslip" style="float: right;" data-show="" onclick="File.showCancelled(this, $('list_{{$object->_class}}{{$object->_id}}'))">
    Afficher / Masquer {{$object->_nb_cancelled_files}} fichier(s) annulé(s)
  </button>
{{/if}}

<div id="list_{{$object->_class}}{{$object->_id}}">
  <table class="form">
    {{mb_include module=cabinet template=inc_widget_list_files}}
  </table>
  {{if $object->_ref_hypertext_links}}
    {{mb_include module=sante400 template=inc_widget_list_hypertext_links}}
  {{/if}}
</div>