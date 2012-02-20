{{mb_include template=inc_form_stats}}

{{if $filter->_function_id || $filter->_user_id}}
  <script type="text/javascript">
    Main.add(function() {
      var tabs = Control.Tabs.create('tabs_stats');
    });
  </script>
  <ul id="tabs_stats" class="control_tabs">
    {{if $filter->_function_id}}
      <li>
        <a href="#function">Cabinet</a>
      </li>
    {{/if}}
    {{if $filter->_user_id}}
      <li>
        <a href="#prat">Praticien</a>
      </li>
    {{/if}}
  </ul>
  <hr class="control_tabs" />
  {{if $filter->_function_id}}
    <div id="function" style="display: none;">
      {{mb_include module=dPcabinet template=inc_stats_cab}}
    </div>
  {{/if}}
  {{if $filter->_user_id}}
    <div id="prat" style="display: none;">
      {{mb_include module=dPcabinet template=inc_stats_prat}}
    </div>
  {{/if}}
{{/if}}


