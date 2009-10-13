{{if @$object->_aides_all_depends.$property}}

<script type="text/javascript">
function validate(title, text) {
  console.debug(title, text);
}
  
Main.add(function(){
  Control.Tabs.create("tabs-aides-depend");
  Control.Tabs.create("tabs-aides-depend2");
});
</script>

{{assign var=aidesByDepend1 value=$object->_aides_all_depends.$property}}
{{assign var=numCols value=4}}
{{math equation="100/$numCols" assign=width format="%.1f"}}

<table class="main">
  <tr>
    <td style="white-space: nowrap; width: 0.1%;">
      <ul class="control_tabs_vertical" id="tabs-aides-depend">
      {{foreach from=$aidesByDepend1 key=depend1 item=aidesByDepend2}}
        <li>
          <a href="#{{$depend1}}">{{tr}}{{$object->_class_name}}.{{$depend_field_1}}.{{$depend1}}{{/tr}} 
            <small>({{$aidesByDepend2|@count}})</small>
          </a>
        </li>
      {{/foreach}}
      </ul>
    </td>
      
    <td>
      {{foreach from=$aidesByDepend1 key=depend1 item=aidesByDepend2}}
        <div id="{{$depend1}}" style="display: none;">
          <ul class="control_tabs" id="tabs-aides-depend2">
          {{foreach from=$aidesByDepend2 key=depend2 item=aides}}
            <li><a href="#{{$depend2}}">{{tr}}{{$object->_class_name}}.{{$depend_field_2}}.{{$depend2}}{{/tr}} <small>({{$aides|@count}})</small></a></li>
          {{/foreach}}
          </ul>
          <hr class="control_tabs" />
          
          
          {{foreach from=$aidesByDepend2 key=depend2 item=aides}}
            <table id="{{$depend2}}" class="main tbl">
              <tr>
              {{foreach from=$aides item=_aide name=_aides}}
              {{assign var=i value=$smarty.foreach._aides.index}}
                  <td title="{{$_aide->text}}" style="width: {{$width}}%;">
                    <img style="float:right; clear: both; opacity: 0.3;" 
                         src="images/icons/{{if $_aide->_owner == "user"}}user{{else}}user-function{{/if}}.png" 
                         title="{{mb_value object=$_aide field=_owner}}" />
                  
                    <label>
                      <button type="button" class="tick notext" onclick='validate("{{$_aide->name}}","{{$_aide->text}}")'"></button>
                      {{$_aide->name}}
                    </label>
                  </td>
                {{if ($i % $numCols) == ($numCols-1) && !$smarty.foreach._aides.last}}</tr><tr>{{/if}}
              {{/foreach}}
              </tr>
            </table>
          {{/foreach}}
        </div>
      {{/foreach}}
    </td>
  </tr>
</table>
{{/if}}
