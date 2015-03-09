<form name="Edit-CSecteur" action="" method="post" onsubmit="return onSubmitFormAjax(this, {onComplete: function() {Control.Modal.close();}});">
  {{mb_key object=$secteur}}
  {{mb_class object=$secteur}}
  {{mb_field object=$secteur field=group_id hidden=true}}
  {{mb_field object=$secteur field=code hidden=true value=$secteur->nom}}

  <table class="form">
    {{mb_include module=system template=inc_form_table_header object=$secteur}}

    <tr>
      <th>{{mb_label object=$secteur field=nom}}</th>
      <td>{{mb_field object=$secteur field=nom onchange="Infrastructure.setValueForm('Edit-CSecteur', 'code', this.value)"}}</td>
    </tr>
    
    <tr>
      <th>{{mb_label object=$secteur field=description}}</th>
      <td>{{mb_field object=$secteur field=description}}</td>
    </tr>
  
    <tr>
      <td class="button" colspan="2">
        {{if $secteur->_id}}
          <button class="submit" type="submit">{{tr}}Save{{/tr}}</button>
          <button class="trash" type="button" onclick="confirmDeletion(this.form,{typeName:'le secteur',objName: $V(this.form.nom) })">
            {{tr}}Delete{{/tr}}
          </button>
        {{else}}
          <button class="submit" type="submit">{{tr}}Create{{/tr}}</button>
        {{/if}}
      </td>
    </tr>
  </table>
</form>

{{if $secteur->_id}}
  <script type="text/javascript">
    Main.add(function() {
      var form = getForm('addService');
      var url = new Url("system", "httpreq_field_autocomplete");
      url.addParam('class', 'CService');
      url.addParam('field', 'service_id');
      url.addParam('view_field', 'nom');
      url.addParam('show_view', true);
      url.addParam("input_field", "_service_autocomplete");
      url.autoComplete(form.elements._service_autocomplete, null, {
        minChars: 2,
        method: "get",
        select: "view",
        dropdown: true,
        afterUpdateElement: function(field,selected) {
          $V(field.form['service_id'], selected.getAttribute('id').split('-')[2]);
          field.form.onsubmit();
          $V(field.form._service_autocomplete, '');
        }
      });
    });

    removeService = function(service_id) {
      var oForm = getForm('delService');
      $V(oForm.service_id, service_id);
      if (confirm('{{tr}}CSecteur-remove_service{{/tr}}')) {
        oForm.onsubmit();
      }
    }

    reloadServices = function() {
      var url = new Url('dPhospi', 'ajax_services_secteur');
      url.addParam('secteur_id', '{{$secteur->_id}}');
      url.requestUpdate('services_secteur');
    }
  </script>
  <form name="addService" method="post" onsubmit="return onSubmitFormAjax(this, {onComplete: reloadServices})">
    <input type="hidden" name="m" value="dPhospi" />
    <input type="hidden" name="dosql" value="do_service_aed" />
    <input type="hidden" name="service_id" value=""/>
    <input type="hidden" name="secteur_id" value="{{$secteur->_id}}" />
    <input type="text"   name="_service_autocomplete"/>
  </form>

  <form name="delService" method="post" onsubmit="return onSubmitFormAjax(this, {onComplete: reloadServices})">
    <input type="hidden" name="m" value="dPhospi" />
    <input type="hidden" name="dosql" value="do_service_aed" />
    <input type="hidden" name="service_id" value="" />
    <input type="hidden" name="secteur_id" value="" />
  </form>

  {{mb_include module=hospi template=inc_services_secteur}}
{{/if}}