{{mb_include_script module=system script="mb_object"}}

<script type="text/javascript">
Main.add(function () {
  var tabs = Control.Tabs.create('tab-fusion', false);
});

function setField (field, value, form) {
  field = $(document.forms[form].elements[field]);

  var dateView = $(form+'_'+field.name+'_da');
  if (dateView) {
    dateView.update(value);
    $V(field, (value ? Date.fromLocaleDate(value).toDATE() : ''));
    return;
  }
  
  $V(field, value); 
  if (field.fire) {
    field.fire('mask:check');
  }
}
</script>

{{assign var=object1 value=$patient1}}
{{assign var=object2 value=$patient2}}
{{assign var=object_final value=$finalPatient}}

<h2 class="module {{$m}}">Fusion de patients</h2>

{{if $testMerge}}
<div class="big-warning">
  <strong>La fusion de ces deux patients n'est pas possible</strong> à cause des problèmes suivants :<br />
  - {{$testMerge}}<br />
  Veuillez corriger ces problèmes avant toute fusion.
</div>
{{/if}}

<form name="editFrm" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">
  <input type="hidden" name="dosql" value="do_patients_fusion" />
  <input type="hidden" name="del" value="0" />
  <input type="hidden" name="_merging" value="1" />
  <input type="hidden" name="patient1_id" value="{{$patient1->_id}}" />
  <input type="hidden" name="patient2_id" value="{{$patient2->_id}}" />
  
  <ul id="tab-fusion" class="control_tabs">
    <li><a href="#identite">Identité</a></li>
    <li><a href="#medical">Médical</a></li>
    <li><a href="#correspondance">Correspondance</a></li>
    <li><a href="#assure">Assuré social</a></li>
  </ul>
  <hr class="control_tabs" />
  <div id="identite" style="display: none;">{{include file="inc_acc/inc_acc_fusion_identite.tpl"}}</div>
  <div id="medical" style="display: none;">{{include file="inc_acc/inc_acc_fusion_medical.tpl"}}</div>
  <div id="correspondance" style="display: none;">{{include file="inc_acc/inc_acc_fusion_corresp.tpl"}}</div>
  <div id="assure" style="display: none;">{{include file="inc_acc/inc_acc_fusion_assure.tpl"}}</div>
  
  <div class="button">
    <button type="button" class="search" onclick="MbObject.viewBackRefs('{{$patient1->_class_name}}', ['{{$patient1->_id}}', '{{$patient2->_id}}']);">
      {{tr}}CMbObject-merge-moreinfo{{/tr}}
    </button>
    <button type="submit" class="submit">
      {{tr}}Merge{{/tr}}
    </button>
  </div>
</form>