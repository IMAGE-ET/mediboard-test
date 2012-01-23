<script type="text/javascript">

function onUploadComplete(files, options){
  var url = new Url("topsManufacturer", "ajax_preview_csv");
  url.addObjectParam('file', files.fileupload);
  url.addObjectParam('options', options);
  url.requestUpdate("preview-csv", {waitingText: 'Génération de l\'aperçu', onComplete: onComplete});
}

function checkFileName(form){
  if ($V(form.fileupload).match(/.xml$/i)) {
    return true;
  }
  else {
    alert('Veuillez choisir un fichier au format XML');
    return false;
  }
}

function activateAction(active){
  var selector = 'input[type=checkbox], input[type=text], input[type=hidden], select';
  active.setOpacity(1).select(selector).each(function(e){e.disabled = false});
  
  var radio = active.select('input[type=radio]')[0];
  if (radio) radio.checked = true;
  
  active.siblings().each(function(s){
    if (s.tagName.match(/^td$/i))
      s.setOpacity(0.5).select(selector).each(function(e){e.disabled = true});
  });
}

function activateFirstAction(row){
  var activated = false;
  row.select('td').each(function(td){
    if (!activated && !td.innerHTML.match(/^\s*$/)) {
      activated = true;
      activateAction(td);
    }
  });
}

function doImport(element) {
  var form = element.form || element,
      url = new Url("topsManufacturer", "ajax_import_csv_component"),
      elements = $(form).getElements();
      
  var selected = elements.findAll(function(e) {
    return (e.hasClassName('column') && e.selectedIndex);
  });
  if (selected.length < 2) {
    alert('Veuillez configurer au moins 2 colonnes.');
    return;
  }
  
  form.select('input[type=radio]').each(function(e){e.disabled=true});

  elements.each(function(element){
    if (element.disabled || 
       (element.type.match(/^radio|checkbox/i) && !element.checked)) return;

    var isCol = element.hasClassName('column');
    if ((isCol && element.selectedIndex > 0) || !isCol)
      url.addParam(element.name, (element.tagName.match(/^select$/i) ? $V(element) : element.value));
  });
  url.requestUpdate('import-log', {
    method: 'post',
    waitingText: 'L\'import peut prendre plusieurs minutes en fonction du nombre de lignes',
    onComplete: function(){
      if (element.tagName.match(/^button$/i)) {
        $('after-import').show();
      }
    },
    getParameters: {
      m: "topsManufacturer",
      a: "ajax_import_csv_component"
    }
  });
  
  // If the element is the button
  if (element.tagName.match(/^button$/i)) {
    $('before-import').hide();
  }
}

</script>

<style type="text/css">
  col.used {
    background: #FFE8DD;
  }
  
  tr.title-row {
    font-weight: bold;
    background: #eee;
  }

  #preview-csv-table {
    margin: 4px;
    border-collapse: collapse;
  }
  
  #preview-csv-table th, 
  #preview-csv-table td {
    border: #999 1px solid; 
    padding: 2px; 
  }
  
  #preview-csv-table th {
    background: #ccc;
  }
  
  #preview-csv-table select.column {
    max-width: 12em;
  }
  
  /*#preview-csv-table tr:hover {
    outline: 1px solid #999;
  }*/
</style>


<form name="upload_form" action="?m=system&amp;a=ajax_object_import_configure&dialog=1" enctype="multipart/form-data" method="post" target="upload_iframe" onsubmit="return checkFileName(this)">
  <input type="hidden" name="m" value="system" />
  <input type="hidden" name="a" value="ajax_object_import_configure" />
  <input type="hidden" name="MAX_FILE_SIZE"  value="67108864" /><!-- 64MB -->
  
	<table class="main form">
		<tr>
			<th>
				<label for="fileupload">Fichier à importer</label>
			</th>
			<td class="narrow">
				<input type="file" name="fileupload" size="40" onchange="this.form.onsubmit()" style="width: 30em;" />
			</td>
			<td>
				<button type="submit" class="send">
					Importer
				</button>
			</td>
		</tr>
	</table>
</form>

<table class="main table">
	<tr>
		<td><iframe id="upload_iframe" name="upload_iframe" src="about:blank" style="width: 100%; height: 400px;"></iframe></td>
	</tr>
</table>


<form name="import-object-form" action="?" method="post" onsubmit="return doImport(this)">
  <input type="hidden" name="m" value="system" />
  <input type="hidden" name="dosql" value="do_import_object" />
	
  <div id="preview-csv" style="overflow: auto;">
    <div class="small-info">Veuillez choisir un fichier à importer.</div>
  </div>
  
  <fieldset>
    <legend>Import</legend>
    
    <table class="main form">
      <tr>
        <th style="width: 0.1%;"><label for="options[lines_to_ignore]">Lignes à ignorer</label></th>
        <td>
          <select name="options[lines_to_ignore]" onchange="setIgnoredLines(this.selectedIndex)">
            <option value="0">0</option>
            <option value="1">1</option>
            <option value="2">2</option>
            <option value="3">3</option>
            <option value="4">4</option>
            <option value="5">5</option>
          </select>
        </td>
      </tr>
      <tr>
        <th><label for="options[us_date]">Format de date US (MM/JJ/AAAA)</label>
        <td><input type="checkbox" name="options[us_date]" /></td>
      </tr>
      <tr>
        <td></td>
        <td>
          <button type="submit" class="change">Importer</button>
        </td>
      </tr>
    </table>
  </fieldset>

  <div id="import-log"></div>
</form>