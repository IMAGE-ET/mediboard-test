<script>
  Main.add(function(){
    Control.Tabs.create("mesaages-tab");
  });
</script>

<table class="main layout">
  <tr>
    <td style="vertical-align: top; white-space: nowrap;" class="narrow">
      <ul id="mesaages-tab" class="control_tabs_vertical small" style="min-width: 10em;">
        {{foreach from=$messages item=_message key=_key}}
          <li>
            <a href="#message-{{$_key}}" {{if $_message->errors}} class="wrong" {{/if}} title="{{$_message->filename}}">
              <strong style="float: left; margin-right: 1em;">{{$_message->name}}</strong> {{$_message->version}}
            </a>
          </li>
        {{/foreach}}
      </ul>
    </td>

    <td class="text" style="padding: 3px;">
      {{foreach from=$messages item=_message key=_key}}
        <script>
          Main.add(function(){
            Control.Tabs.create("message-tab-{{$_key}}");
          });
        </script>
        <div style="display: none;" id="message-{{$_key}}">
          <h1>{{$_message->description}} ({{$_message->version}}) <span class="type">{{$_message->name}}</span></h1>
          
          <ul class="control_tabs" id="message-tab-{{$_key}}">
            <li><a href="#message-{{$_key}}-tree">Arbre</a></li>
            <li><a href="#message-{{$_key}}-er7-input">ER7 Input</a></li>
            <li><a href="#message-{{$_key}}-er7-output">ER7 Output</a></li>
            <li><a href="#message-{{$_key}}-errors" {{if $_message->errors|@count}} class="wrong" {{/if}}>Erreurs</a></li>
          </ul>
          <hr class="control_tabs" />
            
          <ul id="message-{{$_key}}-tree" style="display: none;" class="hl7-tree">
            {{mb_include module=hl7 template=inc_segment_group_children segment_group=$_message}}
          </ul>
          
          <div id="message-{{$_key}}-er7-input" style="display: none;">
            {{$_message->highlight_er7($_message->data)|smarty:nodefaults}}
          </div>
          
          <div id="message-{{$_key}}-er7-output" style="display: none;">
            {{$_message->flatten(true)|smarty:nodefaults}}
          </div>
          
          <div id="message-{{$_key}}-errors" style="display: none;">
            {{mb_include module=hl7 template=inc_hl7v2_errors errors=$_message->errors}}
          </div>
        </div>
      {{/foreach}}
    </td>
  </tr>
</table>