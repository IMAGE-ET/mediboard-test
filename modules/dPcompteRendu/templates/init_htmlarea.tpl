{{if $templateManager->editor == "fckeditor" }}

{{mb_include_script path="lib/fckeditor/fckeditor.js"}}

<script type="text/javascript">

function initFCKEditor() {
	var element = $("htmlarea");
	oFCKeditor = new FCKeditor(element.name, '100%', '100%');
	
	// Be carefull: after that all Js code is executed in lib/fckeditor/
	oFCKeditor.BasePath	= 'lib/fckeditor/';
	
	oFCKeditor.Config['CustomConfigurationsPath'] = '../../../modules/dPcompteRendu/mb_fckeditor.php' ;

	oFCKeditor.ReplaceTextarea() ;
}

Main.add(initFCKEditor);

// Readonly mode
{{if $templateManager->printMode}}
function readonlyFCKEditor() {
	oFCKinstance = FCKeditorAPI.GetInstance('source');
	if (document.all) {
		oFCKinstance.EditorDocument.body.disabled = true;
	}
	else 	{
		oFCKinstance.EditorDocument.designMode = "off";
	}
};

// Ugly Hack : wait for FCKeditorAPI to be available
setTimeout(readonlyFCKEditor, 2000);
{{/if}}

// Surveillance de modification de l'éditeur de texte
function FCKeditor_OnComplete(editorInstance) {
  editorInstance.Events.AttachEvent('OnSelectionChange', FCKeventChanger );
}
  
function FCKeventChanger(editorInstance) {
  if(editorInstance.LastOnChangeTimer) {
    FormObserver.FCKChanged(editorInstance.LastOnChangeTimer);
  }
}

</script>
{{/if}}