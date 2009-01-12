<script type="text/javascript">
  reloadAfterUploadFile = function() {
    var url = new Url;
    url.setModuleAction("dPpatients", "httpreq_vw_photo_identite");
    url.addParam("patient_id", "{{$patient->_id}}");
    url.addParam("mode", "edit");
    url.requestUpdate("{{$patient->_guid}}-identity", {waitingText: null});
  }

  deletePhoto = function(file_id){
  	var form = getForm('delete-photo-identite-form');
    $V(form.file_id, file_id);
  	return confirmDeletion(
  	    form, {
  	      typeName:'la photo',
  	      objName:'identite.jpg',
  	      ajax:1,
  	      target:'systemMsg'
  	    },{
  	      onComplete:reloadAfterUploadFile
  	    } );
  }
</script>

{{assign var=file value=$patient->_ref_photo_identite}}

{{if $file->_id}}
  {{assign var=id value=$file->_id}}
  {{assign var=src value="?m=dPfiles&a=fileviewer&suppressHeaders=1&file_id=$id&phpThumb=1&w=128"}}
{{else}}
  {{if $patient->sexe == 'm'}}
    {{assign var=src value="images/pictures/identity_male.png"}}
  {{else}}
    {{assign var=src value="images/pictures/identity_female.png"}}
  {{/if}}
  
  {{if $patient->_age < 15}}
    {{assign var=src value="images/pictures/identity_child.png"}}
  {{/if}}
{{/if}}
<img src="{{$src}}" alt="Identit�" />

{{if @$mode == "edit"}}
  <br />
  {{if !$patient->_ref_photo_identite->_id}}
    <button type="button" class="search" onclick="uploadFile('{{$patient->_class_name}}', '{{$patient->_id}}', null, 'identite.jpg')">{{tr}}Browse{{/tr}}</button>
  {{else}}
    <button onclick="deletePhoto({{$patient->_ref_photo_identite->_id}})" class="trash" type="button">{{tr}}Delete{{/tr}}</button>
  {{/if}}
{{/if}}