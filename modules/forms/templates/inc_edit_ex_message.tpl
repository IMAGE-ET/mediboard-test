{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage forms
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script type="text/javascript">
Main.add(function(){
  var form = getForm("editMessage");

  form.elements.text.select();

  var url = new Url("forms", "ajax_autocomplete_ex_class_field_predicate");
  url.autoComplete(form.elements.predicate_id_autocomplete_view, null, {
    minChars: 2,
    method: "get",
    select: "view",
    dropdown: true,
    afterUpdateElement: function(field, selected){
      var id = selected.get("id");

      if (!id) {
        $V(field.form.predicate_id, "");
        $V(field.form.elements.predicate_id_autocomplete_view, "");
        return;
      }

      $V(field.form.predicate_id, id);

      if (id) {
        showField(id, selected.down('.name').getText());
      }

      if ($V(field.form.elements.predicate_id_autocomplete_view) == "") {
        $V(field.form.elements.predicate_id_autocomplete_view, selected.down('.view').getText());
      }
    },
    callback: function(input, queryString){
      return queryString + "&ex_class_id={{$ex_message->_ref_ex_group->ex_class_id}}";
    }
  });
});
</script>

{{if !$ex_message->_ref_ex_group->_ref_ex_class->pixel_positionning}}
<div class="small-info">
  Les Titres/Textes sont des zones d'information à placer sur la grille (Disposition du formulaire).<br />
  <strong>Le libellé n'a pas nécessairement besoin d'être placé sur la grille</strong>.
</div>
{{/if}}

<form name="editMessage" method="post" action="?" onsubmit="return onSubmitFormAjax(this, {onComplete: ExClass.edit.curry({{$ex_message->_ref_ex_group->ex_class_id}})})">
  <input type="hidden" name="m" value="system" />
  <input type="hidden" name="dosql" value="do_ex_class_message_aed" />
  <input type="hidden" name="del" value="0" />
  {{mb_key object=$ex_message}}
  {{mb_field object=$ex_message field=ex_group_id hidden=true}}
  
  <table class="form">
    {{mb_include module=system template=inc_form_table_header object=$ex_message colspan="4"}}
    
    <tr>
      <th>{{mb_label object=$ex_message field=type}}</th>
      <td>{{mb_field object=$ex_message field=type emptyLabel="Normal" onchange="\$('text-preview').className='small-'+\$V(this)"}}</td>

      <th>{{mb_label object=$ex_message field=title}}</th>
      <td>{{mb_field object=$ex_message field=title}}</td>
    </tr>
    <tr>
      <th>{{mb_label object=$ex_message field=text}}</th>
      <td colspan="3">
        <div id="text-preview" class="small-{{$ex_message->type}}">
          {{mb_field object=$ex_message field=text}}
        </div>
      </td>
    </tr>

    <tr>
      <th>{{mb_label object=$ex_message field=predicate_id}}</th>
      <td colspan="3">
        <input type="text" name="predicate_id_autocomplete_view" size="70"
               value="{{$ex_message->_ref_predicate->_view}}" placeholder=" -- Toujours afficher -- " />
        {{mb_field object=$ex_message field=predicate_id hidden=true}}
        <button class="new notext" onclick="ExFieldPredicate.create(null, null, this.form)" type="button">
          {{tr}}New{{/tr}}
        </button>
      </td>
    </tr>
    
    {{if $ex_message->_ref_ex_group->_ref_ex_class->pixel_positionning}}
    <tr>
      <th>{{mb_label object=$ex_message field=coord_left}}</th>
      <td colspan="3">{{mb_field object=$ex_message field=coord_left increment=true form=editMessage}}</td>
    </tr>
    <tr>
      <th>{{mb_label object=$ex_message field=coord_top}}</th>
      <td colspan="3">{{mb_field object=$ex_message field=coord_top increment=true form=editMessage}}</td>
    </tr>
    <tr>
      <th>{{mb_label object=$ex_message field=coord_width}}</th>
      <td colspan="3">{{mb_field object=$ex_message field=coord_width increment=true form=editMessage}}</td>
    </tr>
    <tr>
      <th>{{mb_label object=$ex_message field=coord_height}}</th>
      <td colspan="3">{{mb_field object=$ex_message field=coord_height increment=true form=editMessage}}</td>
    </tr>
    {{/if}}
    
    <tr>
      <th></th>
      <td colspan="3">
        <button type="submit" class="modify">{{tr}}Save{{/tr}}</button>

        {{if $ex_message->_id}}
          <button type="button" class="trash" onclick="confirmDeletion(this.form,{ajax:true,typeName:'le message ',objName:'{{$ex_message->_view|smarty:nodefaults|JSAttribute}}'})">
            {{tr}}Delete{{/tr}}
          </button>
        {{/if}}
      </td>
    </tr>
  </table>
</form>

{{mb_include module=forms template=inc_list_entity_properties object=$ex_message}}
