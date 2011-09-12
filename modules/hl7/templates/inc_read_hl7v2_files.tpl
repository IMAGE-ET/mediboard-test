<style>
  ul, ol {
    line-height: 1.4;
    padding-left: 2em;
    margin-bottom: 6px;
  }
  
  ol {
    list-style-type: none;
  }
  
  .type {
    color: #2D9A00;
  }
  
  .type:before {
    content: "(";
  }
  
  .type:after {
    content: ")";
  }
  
  .field-name {
    color: #3252A7;
    margin-right: 5px;
    display: inline-block;
    width: 5em;
    white-space: nowrap;
  }
  
  .field-description {
    color: #999;
  }
  
  .value {
    background: #eee;
    padding: 0 2px;
  }
  
  .field-item {
    border: 1px dotted #ccc;
    margin: 1px;
  }
  
  .field-item > ol {
    padding-left: 2px;
    margin-bottom: 0px;
  }
  
  .field-empty {
    opacity: 0.5;
  }
</style>

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
            
          <ul id="message-{{$_key}}-tree" style="display: none;">
            {{mb_include module=hl7 template=inc_segment_group_children segment_group=$_message}}
          </ul>
          
          <div id="message-{{$_key}}-er7-input" style="display: none;">
            {{$_message->highlight_er7($_message->data)|smarty:nodefaults}}
          </div>
          
          <div id="message-{{$_key}}-er7-output" style="display: none;">
            {{$_message->highlight_er7($_message->flatten())|smarty:nodefaults}}
          </div>
          
          <div id="message-{{$_key}}-errors" style="display: none;">
            {{mb_include module=hl7 template=inc_hl7v2_errors errors=$_message->errors}}
          </div>
        </div>
      {{/foreach}}
    </td>
  </tr>
</table>