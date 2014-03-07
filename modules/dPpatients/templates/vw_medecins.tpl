{{* $Id$ *}}

<script>
  setClose= function (id, view) {
    window.parent.Medecin.set(id, view);
    window.parent.Control.Modal.close();
  };

  var formVisible = false;
  function showAddCorres() {
    if (!formVisible) {
      $('addCorres').show();
      getForm('editFrm').focusFirstElement();
      formVisible = true;
    } else {
      hideAddCorres();
    }
  }

  function hideAddCorres() {
    $('addCorres').hide();
    formVisible = false;
  }


  function onSubmitCorrespondant(form) {
    return onSubmitFormAjax(form, { onComplete : function() {
      hideAddCorres();
      var formFind = getForm('find_medecin');
      formFind.elements.medecin_nom.value    = form.elements.nom.value;
      formFind.elements.medecin_prenom.value = form.elements.prenom.value;
      formFind.elements.medecin_cp.value     = form.elements.cp.value;
      formFind.submit();
    }});
  }
</script>


{{if !$dialog}}
  <form name="fusion" action="?" method="get">
    <input type="hidden" name="m" value="system" />
    <input type="hidden" name="a" value="object_merger" />
    <input type="hidden" name="objects_class" value="CMedecin" />
    <input type="hidden" name="readonly_class" value="true" />
{{/if}}

{{mb_include module=system template=inc_pagination current=$start_med step=$step_med total=$count_medecins change_page=refreshPageMedecin}}

<table class="tbl">
  <tr>
    {{if !$dialog}}
      <th class="narrow">
        <button type="button" onclick="Medecin.doMerge('fusion');" class="merge notext compact" title="{{tr}}Merge{{/tr}}">
          {{tr}}Merge{{/tr}}
        </button>
      </th>
    {{/if}}
    <th>{{mb_title class=CMedecin field=nom}}</th>
    <th>{{mb_title class=CMedecin field=adresse}}</th>
    <th>{{mb_title class=CMedecin field=type}}</th>
    <th>{{mb_title class=CMedecin field=disciplines}}</th>
    <th>{{mb_title class=CMedecin field=tel}}</th>
    <th>{{mb_title class=CMedecin field=fax}}</th>
    <th>{{mb_title class=CMedecin field=email}}</th>
    {{if $dialog}}
      <th id="vw_medecins_th_select">{{tr}}Select{{/tr}}</th>
    {{/if}}
  </tr>
  {{foreach from=$medecins item=_medecin}}
    {{assign var=medecin_id value=$_medecin->_id}}
    <tr>
      {{mb_ternary var=href test=$dialog value="#choose" other="?m=$m&tab=vw_correspondants&medecin_id=$medecin_id"}}

      {{if !$dialog}}
        <td>
          <input type="checkbox" name="objects_id[]" value="{{$_medecin->_id}}" />
        </td>
      {{/if}}

      <td class="text">
        {{if !$dialog}}
            <a href="#" onclick="Medecin.editMedecin('{{$_medecin->_id}}', refreshPageMedecin);">
        {{/if}}
          {{$_medecin->nom}} {{$_medecin->prenom|strtolower|ucfirst}}
        {{if !$dialog}}
            </a>
        {{/if}}
      </td>
      <td class="text compact">{{$_medecin->adresse}}<br/>
        {{mb_value object=$_medecin field=cp}} {{mb_value object=$_medecin field=ville}}</td>
      <td>{{mb_value object=$_medecin field=type}}</td>
      <td class="text">{{mb_value object=$_medecin field=disciplines}}</td>
      <td style="text-align: center;">{{mb_value object=$_medecin field=tel}}</td>
      <td style="text-align: center;">{{mb_value object=$_medecin field=fax}}</td>
      <td>{{mb_value object=$_medecin field=email}}</td>
      {{if $dialog}}
        <td>
          <button type="button" class="tick" onclick="setClose({{$_medecin->_id}}, '{{$_medecin->_view|smarty:nodefaults|JSAttribute}}' )">
            {{tr}}Select{{/tr}}
          </button>
        </td>
      {{/if}}
    </tr>
  {{foreachelse}}
    <tr><td colspan="9" class="empty">{{tr}}CMedecin.none{{/tr}}</td></tr>
  {{/foreach}}
</table>

{{mb_include module=system template=inc_pagination current=$start_med step=$step_med total=$count_medecins change_page=refreshPageMedecin}}

{{if !$dialog}}
  </form>
{{/if}}