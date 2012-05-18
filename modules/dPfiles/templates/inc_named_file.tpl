{{mb_default var=mode       value=edit}}
{{mb_default var=size       value=120}}
{{mb_default var=bigsize    value=240}}
{{mb_default var=border     value="#aaa"}}
{{mb_default var=background value="#eee"}}

{{assign var=file value=""}} 
{{if (array_key_exists($name, $object->_ref_named_files))}}
  {{assign var=file value=$object->_ref_named_files.$name}}
{{/if}}

{{if $file && $file->_id}}
  {{assign var=file_id value=$file->_id}}
  {{assign var=src  value="?m=files&a=fileviewer&suppressHeaders=1&file_id=$file_id&phpThumb=1&w=$size&h=$size&zc=1"}}
  {{assign var=_src value="?m=files&a=fileviewer&suppressHeaders=1&file_id=$file_id&phpThumb=1&w=$bigsize"}}
{{else}}
  {{assign var=src value="images/pictures/unknown.png"}}
{{/if}}

<div id="{{$object->_guid}}-{{$name}}">

<img 
  src="{{$src}}" 
  style="width: {{$size}}px; height: {{$size}}px; border: 2px solid {{$border}}; background: {{$background}};" 
  alt="{{$name}}" 
  {{if $file && $file->_id}} 
  onmouseover="ObjectTooltip.createDOM(this, 'tooltip-named-file-{{$file->_id}}')"
  {{/if}} 
/>
     
{{if $file && $file->_id}}     
<div id="tooltip-named-file-{{$file->_id}}" style="display: none;">
  <img 
    src="{{$_src}}" 
    style="border: 2px solid {{$border}}; background: {{$background}}" alt="Identité" 
  />
</div>
{{/if}}

{{if $mode == "edit"}}
<script type="text/javascript">
NamedFile = {
  init: function(object_guid, name) {
    this.object_guid = object_guid;
    this.name        = name;
  },
  
  remove: function(object_guid, name, file_id, file_view) {
    this.init(object_guid, name);
    this.file_id = file_id;
    
    var options = {
      typeName: 'le fichier',
      objName: file_view
    };
  
    var ajax = {
      onComplete: NamedFile.refresh.bind(NamedFile)
    };
    
    var name = 'delete-named-file-'+file_id;
    var form = DOM.form({method: "post", action: '?', name: name},
      DOM.input({type: 'hidden', name: 'm'      , value: 'files'      }),
      DOM.input({type: 'hidden', name: 'dosql'  , value: 'do_file_aed'}),
      DOM.input({type: 'hidden', name: 'del'    , value: '1'          }),
      DOM.input({type: 'hidden', name: 'file_id', value: file_id      })
    );
    
    
    return confirmDeletion(form, options, ajax);
  },
  
  refresh: function() {
    var url = new Url('files', 'vw_named_file');
    url.addParam('object_guid', this.object_guid);
    url.addParam('name', this.name);
    url.addParam('mode', 'edit');
    url.requestUpdate(this.object_guid+'-'+this.name);
  },
  
  upload: function(object_guid, name) {
    this.init(object_guid, name);
    uploadFile(object_guid, null, name, 1);
  }
};

reloadAfterUploadFile = NamedFile.refresh.bind(NamedFile);
</script>

<br />
{{if $file && $file->_id}}
  <button onclick="NamedFile.remove('{{$object->_guid}}', '{{$name}}', '{{$file->_id}}', '{{$file}}')" class="trash" type="button">
    {{tr}}Delete{{/tr}}
  </button>

{{else}}
<button type="button" class="search" onclick="NamedFile.upload('{{$object->_guid}}', '{{$name}}');">
  {{tr}}Browse{{/tr}}
</button>

{{/if}}

{{/if}}

</div>