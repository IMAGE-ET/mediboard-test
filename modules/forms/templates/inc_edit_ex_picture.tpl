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
  var form = getForm("editPicture");
  form.elements.name.select();
  ExFieldPredicate.initAutocomplete(form, '{{$ex_group->ex_class_id}}');
});
</script>

<form name="editPicture" method="post" action="?" onsubmit="return onSubmitFormAjax(this, {onComplete: ExClass.edit.curry({{$ex_group->ex_class_id}})})">
  <input type="hidden" name="m" value="system" />
  {{mb_key object=$ex_picture}}
  {{mb_class object=$ex_picture}}

  {{mb_field object=$ex_picture field=ex_group_id hidden=true}}
  {{mb_field object=$ex_picture field=disabled hidden=true}}

  <table class="form">
    {{mb_include module=system template=inc_form_table_header object=$ex_picture colspan="4"}}
    
    <tr>
      <th>{{mb_label object=$ex_picture field=name}}</th>
      <td colspan="3">{{mb_field object=$ex_picture field=name size=50}}</td>
    </tr>

    <tr>
      <th>{{mb_label object=$ex_picture field=show_label}}</th>
      <td colspan="3">{{mb_field object=$ex_picture field=show_label typeEnum=checkbox}}</td>
    </tr>

    <tr>
      <th>{{mb_label object=$ex_picture field=predicate_id}}</th>
      <td colspan="3">
        <input type="text" name="predicate_id_autocomplete_view" size="70" value="{{$ex_picture->_ref_predicate->_view}}" placeholder=" -- Toujours afficher -- " />
        {{mb_field object=$ex_picture field=predicate_id hidden=true}}
        <button class="new notext" onclick="ExFieldPredicate.create(null, null, this.form)" type="button">
          {{tr}}New{{/tr}}
        </button>
      </td>
    </tr>

    <tr>
      <th class="narrow">{{mb_label object=$ex_picture field=coord_left}}</th>
      <td class="narrow">{{mb_field object=$ex_picture field=coord_left increment=true form=editPicture}}</td>
      <th class="narrow">{{mb_label object=$ex_picture field=coord_top}}</th>
      <td>{{mb_field object=$ex_picture field=coord_top increment=true form=editPicture}}</td>
    </tr>
    <tr>
      <th>{{mb_label object=$ex_picture field=coord_width}}</th>
      <td>{{mb_field object=$ex_picture field=coord_width increment=true form=editPicture}}</td>
      <th>{{mb_label object=$ex_picture field=coord_height}}</th>
      <td>{{mb_field object=$ex_picture field=coord_height increment=true form=editPicture}}</td>
    </tr>
    
    <tr>
      <th></th>
      <td colspan="3">
        <button type="submit" class="modify">{{tr}}Save{{/tr}}</button>

        {{if $ex_picture->_id}}
          <button type="button" class="trash" onclick="confirmDeletion(this.form,{ajax:true,typeName:'l\'image ',objName:'{{$ex_picture->_view|smarty:nodefaults|JSAttribute}}'},ExClass.edit.curry('{{$ex_group->ex_class_id}}'))">
            {{tr}}Delete{{/tr}}
          </button>

          {{if $ex_picture->disabled}}
            <button type="button" class="change"
                    onclick="$V(this.form.elements.disabled, 0); onSubmitFormAjax(this.form, ExClass.edit.curry('{{$ex_group->ex_class_id}}'))">
              {{tr}}Enable{{/tr}}
            </button>
          {{else}}
            <button type="button" class="trash"
                    onclick="if(confirm('Voulez-vous désactiver cette image ?')){ $V(this.form.elements.disabled, 1); onSubmitFormAjax(this.form, ExClass.edit.curry('{{$ex_group->ex_class_id}}')); }">
              {{tr}}Disable{{/tr}}
            </button>
          {{/if}}
        {{/if}}
      </td>
    </tr>
  </table>
</form>

{{if $ex_picture->_id}}
  <table class="main form">
    <tr>
      <th class="category">
        {{tr}}CFile{{/tr}}
      </th>
    </tr>
    <tr>
      <td style="text-align: center;">
        {{assign var=mode value="edit"}}
        {{if $ex_picture->_ref_file && $ex_picture->_ref_file->_id}}
          {{assign var=mode value="view"}}
        {{/if}}
        {{mb_include module=files template=inc_named_file name=file.jpg size=200 object=$ex_picture mode=$mode}}
      </td>
    </tr>
  </table>
{{/if}}
