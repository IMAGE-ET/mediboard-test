{{foreach from=$tree item=_subtree}}
  <li>
  {{if $_subtree.type == "segment"}}
    <a href="#" onclick="HL7_Transformation.viewFields('{{$profil}}', '{{$_subtree.name}}',
      '{{$version}}', '{{$extension}}', '{{$message}}')">
      <span class="type-{{$_subtree.type}}">{{$_subtree.name}}</span>
    </a>
    <strong class="field-description">{{$_subtree.description}}</strong>
  {{else}}
    <span class="type-{{$_subtree.type}}">{{$_subtree.name}}</span>

    <ul>
      {{mb_include module=hl7 template=inc_segment_tree tree=$_subtree.children}}
    </ul>
  {{/if}}
  </li>
{{/foreach}}