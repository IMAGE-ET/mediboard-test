{{mb_script module=sante400 script=hyperTextLink ajax=true}}

<script>
  reloadListHypertextLinks = function() {
    Control.Modal.close();
    HyperTextLink.getListFor('{{$hypertext_link->object_id}}', '{{$hypertext_link->object_class}}', '{{$show_widget}}');
    return false;
  }
</script>

<form name="edit-hypertext_link" method="post" onsubmit="return onSubmitFormAjax(this, {onComplete: reloadListHypertextLinks});">
  {{mb_key object=$hypertext_link}}
  {{mb_class object=$hypertext_link}}
  <input type="hidden" name="del" value="0"/>
  <input type="hidden" name="object_id" value="{{$hypertext_link->object_id}}"/>
  <input type="hidden" name="object_class" value="{{$hypertext_link->object_class}}"/>

  <table class="form">
    <tr>
      <th class="title" colspan="2">{{if $hypertext_link->_id}}{{tr}}CHyperTextLink-title-modify{{/tr}}{{else}}{{tr}}CHyperTextLink-title-create{{/tr}}{{/if}}</th>
    </tr>
    <tr>
      <th>{{mb_label object=$hypertext_link field=name}}</th>
      <td>{{mb_field object=$hypertext_link field=name}}</td>
    </tr>
    <tr>
      <th>{{mb_label object=$hypertext_link field=link}}</th>
      <td>{{mb_field object=$hypertext_link field=link}}</td>
    </tr>
    <tr>
      <td colspan="2" style="text-align: center;">
        <button type="submit" class="save">{{tr}}Save{{/tr}}</button>
        {{if $hypertext_link->_id}}
          <button type="button" class="trash" onclick="confirmDeletion(this.form, {typeName: 'le lien hypertexte', objName: '{{$hypertext_link->name}}'}, { ajax:true, onComplete: reloadListHypertextLinks});">{{tr}}Delete{{/tr}}</button>
        {{/if}}
      </td>
    </tr>
  </table>
</form>