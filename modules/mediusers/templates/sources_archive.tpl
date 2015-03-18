{{foreach from=$fs_sources_tarmed item=_sources_tarmed key=name_source_tarmed}}
  <fieldset>
    <legend>{{tr}}{{$name_source_tarmed}}_tarmed{{/tr}}</legend>

    {{assign var=_source_tarmed value=$_sources_tarmed.0}}

    {{if !$_source_tarmed->_id}}
      <button type="button" class="add"
              onclick="ExchangeSource.editSource('{{$_source_tarmed->_guid}}', true, '{{$_source_tarmed->name}}',
                '{{$_source_tarmed->_wanted_type}}', null, loadArchives)">
        {{tr}}CSourceFileSystem-title-create{{/tr}}
      </button>
    {{/if}}

    {{mb_include module=system template=inc_vw_list_sources sources=$_sources_tarmed callback_source=loadArchives}}
  </fieldset>
{{/foreach}}