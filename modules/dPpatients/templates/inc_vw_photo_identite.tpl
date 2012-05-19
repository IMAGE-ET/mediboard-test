{{mb_default var=mode value=read}}
{{mb_default var=size value=120}}

{{if $mode == "edit"}}
<script type="text/javascript">
reloadAfterUploadFile = function() {
  var url = new Url("patients", "httpreq_vw_photo_identite");
  url.addParam("patient_id", "{{$patient->_id}}");
  url.addParam("mode", "edit");
  url.requestUpdate("{{$patient->_guid}}-identity");
};

deletePhoto = function(file_id){
  var options = {
    typeName: 'la photo',
    objName: 'identite.jpg'
  }

  var ajax = {
    onComplete: reloadAfterUploadFile
  }
  
  var form = getForm('delete-photo-identite-form');
  $V(form.file_id, file_id);
  
  return confirmDeletion(form, options, ajax);
};
</script>
{{/if}}

{{assign var=file value=$patient->_ref_photo_identite}}

{{if $file->_id}}
  {{if !$file->private || $patient->_can_see_photo}}
    {{assign var=id value=$file->_id}}
    {{assign var=src value="?m=files&a=fileviewer&suppressHeaders=1&file_id=$id&phpThumb=1&w=$size&h=$size&zc=1"}}
    {{assign var=_src value="?m=files&a=fileviewer&suppressHeaders=1&file_id=$id&phpThumb=1&w=240"}}
  {{else}}
    {{assign var=src value="images/pictures/identity_anonymous.png"}}
    {{assign var=_src value="images/pictures/identity_anonymous.png"}}
  {{/if}}
{{else}}
  {{if $patient->_age < 2 && $patient->naissance && $patient->naissance != "0000-00-00"}}
    {{assign var=src value="images/pictures/identity_baby.png"}}
  {{elseif $patient->_age < $conf.dPpatients.CPatient.adult_age}}
    {{assign var=src value="images/pictures/identity_child.png"}}
  {{elseif $patient->sexe == 'm' && $patient->_age < 60}}
    {{assign var=src value="images/pictures/identity_male.png"}}
  {{elseif $patient->sexe == 'm' && $patient->_age >= 60}}
    {{assign var=src value="images/pictures/identity_male_old.png"}}
  {{elseif $patient->_age < 60}}
    {{assign var=src value="images/pictures/identity_female.png"}}
  {{else}}
    {{assign var=src value="images/pictures/identity_female_old.png"}}
  {{/if}}
{{/if}}

{{assign var=border_photo value="#f88"}}
{{assign var=background_photo value="#fff6f6"}}
{{if $patient->sexe == "m"}}
  {{assign var=border_photo value="#88f"}}
  {{assign var=background_photo value="#f6f6ff"}}
{{/if}}
<img 
  src="{{$src}}" 
  style="width: {{$size}}px; height: {{$size}}px; border: 2px solid {{$border_photo}}; background: {{$background_photo}};" 
  alt="Identité" 
  {{if $file->_id}} 
  onmouseover="ObjectTooltip.createDOM(this, 'tooltip-content-patient-{{$patient->_id}}')"
  {{/if}} 
/>
     
{{if $file->_id}}     
<div id="tooltip-content-patient-{{$patient->_id}}" style="display: none;">
  <img src="{{$_src}}" style="border: 2px solid #777" alt="Identité" />
</div>
{{/if}}

{{if $mode == "edit"}}
  <br />
  {{if !$file->_id}}
    <button type="button" class="search" onclick="uploadFile('{{$patient->_guid}}', null, 'identite.jpg', 1)">
      {{tr}}Browse{{/tr}}
    </button>
  {{elseif $patient->_can_see_photo == 1}}
    <button onclick="deletePhoto('{{$file->_id}}')" class="trash" type="button">
      {{tr}}Delete{{/tr}}
    </button>
  {{/if}}
{{/if}}