{{foreach from=$sources_ldap item=_source key=number}}
  <div id="CSourceLDAP-number-{{$number}}">
    <h3>
      {{if $_source->_id}}
        {{tr}}CSourceLDAP{{/tr}} [{{$_source->name}}]
      {{else}}
        {{tr}}CSourceLDAP-title-create{{/tr}}
      {{/if}}
    </h3>
    {{mb_include module=admin template=inc_source_ldap source_ldap=$_source}}
  </div>
{{/foreach}}