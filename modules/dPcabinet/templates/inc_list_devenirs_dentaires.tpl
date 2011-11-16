{{mb_default var=devenir_dentaire_id value=0}}

{{foreach from=$devenirs_dentaires item=_devenir_dentaire}}
  <tr {{if $_devenir_dentaire->_id == $devenir_dentaire_id}}class="selected"{{/if}}>
    <td>
      <a href="#1" onclick="refreshSelected(this.up('tr')); editProjet('{{$_devenir_dentaire->_id}}')">
        {{mb_value object=$_devenir_dentaire field=description}}
      </a>
    </td>
    <td>{{$_devenir_dentaire->_count_ref_actes_dentaires}}</td>
    <td>
      {{if $_devenir_dentaire->_ref_etudiant->_id}}
        {{mb_include module=mediusers template=inc_vw_mediuser mediuser=$_devenir_dentaire->_ref_etudiant}}
      {{/if}}
    </td>
  </tr>
{{foreachelse}}
  <tr>
    <td colspan="3" class="empty">{{tr}}CDevenirDentaire.none{{/tr}}</td>
  </tr>
{{/foreach}}