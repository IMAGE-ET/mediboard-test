{{mb_script module="patients" script="autocomplete"}}
<script>
  Main.add(function () {
    InseeFields.initCPVille("editFrm", "cp", "ville");
  });
</script>

<table class="main layout">
  <tr>
    <td style="width: 50%;">
      <a href="?m={{$m}}&amp;tab={{$tab}}&amp;banque_id=0" class="button new">
        {{tr}}CBanque-title-create{{/tr}}
      </a>

      <table class="tbl">
        <tr>
          <th class="category">{{mb_title class=CBanque field=nom}}</th>
          <th class="category">{{mb_title class=CBanque field=description}}</th>
        </tr>
      {{foreach from=$banques item=_banque}}
        <tr {{if $_banque->_id == $banque->_id}}class="selected"{{/if}}>
          <td>
            <a href="?m={{$m}}&amp;tab={{$tab}}&amp;banque_id={{$_banque->_id}}">{{$_banque->nom}}</a>
          </td>
          <td class="text">
            {{mb_value object=$_banque field=description}}
          </td>
        </tr>
      {{/foreach}}
      </table>
    </td>
    <td>
      <form name="editFrm" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">
        <input type="hidden" name="dosql" value="do_banque_aed" />
        <input type="hidden" name="banque_id" value="{{$banque->_id}}" />
        <input type="hidden" name="del" value="0" />
        <table class="form">
          {{mb_include module=system template=inc_form_table_header object=$banque}}
          <tr>
            <th>{{mb_label object=$banque field="nom"}}</th>
            <td>{{mb_field object=$banque field="nom"}}</td>
          </tr>
          <tr>
            <th>{{mb_label object=$banque field="description"}}</th>
            <td>{{mb_field object=$banque field="description"}}</td>
          </tr>
          <tr>
            <th>{{mb_label object=$banque field="departement"}}</th>
            <td>{{mb_field object=$banque field="departement"}}</td>
          </tr>
          <tr>
            <th>{{mb_label object=$banque field="boite_postale"}}</th>
            <td>{{mb_field object=$banque field="boite_postale"}}</td>
          </tr>
          <tr>
            <th>{{mb_label object=$banque field="adresse"}}</th>
            <td>{{mb_field object=$banque field="adresse"}}</td>
          </tr>
          <tr>
            <th>{{mb_label object=$banque field="cp"}}</th>
            <td>{{mb_field object=$banque field="cp"}}</td>
          </tr>
          <tr>
            <th>{{mb_label object=$banque field="ville"}}</th>
            <td>{{mb_field object=$banque field="ville"}}</td>
          </tr>
          <tr>
            <td class="button" colspan="2">
            {{if $banque->_id}}
              <button class="modify" type="submit">{{tr}}Save{{/tr}}</button>
              <button class="trash" type="button"
                      onclick="confirmDeletion(this.form,{typeName:'la banque ',objName:'{{$banque->nom|smarty:nodefaults|JSAttribute}}'})">
                {{tr}}Delete{{/tr}}
              </button>
              {{else}}
              <button class="submit" type="submit">{{tr}}Save{{/tr}}</button>
            {{/if}}
            </td>
          </tr>
        </table>
      </form>
    </td>
  </tr>
</table>
