<script type="text/javascript">
Main.add(Control.Tabs.create.curry('tabs-configure', true));
</script>

<ul id="tabs-configure" class="control_tabs">
  <li><a href="#CFile"          >{{tr}}CFile{{/tr}}</a></li>
  <li><a href="#CFilesCategory" >{{tr}}CFilesCategory{{/tr}}</a></li>
  <li><a href="#CDocumentSender">{{tr}}CDocumentSender{{/tr}}</a></li>
  <li><a href="#ooo">OpenOffice.org</a></li>
  <li><a href="#test">{{tr}}CFile-test_operations{{/tr}}</a></li>
</ul>

<hr class="control_tabs" />

<div id="CFile">
  {{mb_include template=CFile_configure}}
</div>

<div id="CFilesCategory">
  {{mb_include template=CFilesCategory_configure}}
</div>

<div id="CDocumentSender">
  {{mb_include template=CDocumentSender_configure}}
</div>

<div id="ooo">
  {{mb_include template=inc_configure_ooo}}
</div>


<div id="test">
  {{mb_include template=inc_test_files}}
</div>
