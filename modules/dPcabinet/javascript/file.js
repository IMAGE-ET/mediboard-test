var File = {
  popup: function(object_class, object_id, element_class, element_id, sfn) {
    var url = new Url;
    url.ViewFilePopup(object_class, object_id, element_class, element_id, sfn);
  },
    
  upload: function(object_class, object_id, file_category_id){
    var url = new Url("dPfiles", "upload_file");
    url.addParam("object_class", object_class);
    url.addParam("object_id", object_id);
    url.addParam("file_category_id", file_category_id);
    url.popup(600, 200, "uploadfile");
  },
  
  remove: function(oButton, object_id, object_class){
    var oOptions = {
      typeName: 'le fichier',
      objName: oButton.form._view.value,
      ajax: 1,
      target: 'systemMsg'
    };
    var oAjaxOptions = {
      onComplete: function() { File.refresh(object_id, object_class); } 
    };
    confirmDeletion(oButton.form, oOptions, oAjaxOptions);
  },
  
  refresh: function(object_id, object_class) {
  	var div_id = printf("files-%s-%s", object_id, object_class);
  	
    var url = new Url("dPcabinet", "httpreq_widget_files");
    url.addParam("object_id", object_id);
    url.addParam("object_class", object_class);
    url.requestUpdate("files-"+object_id+"-"+object_class, {onComplete: function(){
		    //File.applet.watchDirectory();
				
	  }});
  },
  
  register: function(object_id, object_class, container) {
    var div = document.createElement("div");
    div.style.minWidth = "200px";
    div.style.minHeight = "50px";
    div.id = printf("files-%s-%s", object_id, object_class);
    $(container).insert(div);
    
    Main.add( function() {
      File.refresh(object_id,object_class)
    } );
  }
};

if (!window.File.applet) {
	File.applet = {
		object_guid: null,
		directory: null,
    uploader: null,
    executer: null,
		url: null,
		listFiles: [],
		current_list: [],
		current_list_status: [],
    modaleWindow: null,
		found_files: false,
/*	  appletCode: DOM.applet({id: 'uploader', name: 'yopletuploader', width: 0, height: 0,
		                        code: 'org.yoplet.Yoplet.class', archive: 'includes/applets/yoplet2.jar'},
	    DOM.param({name: 'action', value: ''}),
			DOM.param({name: 'url', value: document.location.href.replace(/((index\.php)?\?.*)$/, "modules/dPfiles/ajax_yoplet_upload.php")}),
			DOM.param({name: 'content', value: 'a'})
    ),*/
		watchDirectory: function() {
	    // Lister les nouveaux fichiers
      this.executer = new PeriodicalExecuter(function(){
        File.applet.uploader.listFiles(File.applet.directory, "false");
      }, 2);
    },
		uploadFiles: function() {
	    var files_to_upload = $$(".upload-file:checked");
	    var files = files_to_upload.pluck("value");
			
			// Mettre la case Envoi en loading pour les fichiers
			files_to_upload.each(function(elem) {				
				elem.up("tr").down(".upload").addClassName("loading");
			});
      
	    var json = Object.toJSON(files);
	    var rename = $V(getForm("addFastFile").file_rename) || "upload";
      
	    this.uploader.performUpload(rename, json);
	  },
		handleUploadKO: function(result) {
      alert('L\'envoi du fichier ' + result.path + ' sur le serveur a échoué');
    },

	  handleUploadOk: function(result) {
	    var elem = this.modaleWindow.container.select("input[type=checkbox]:checked").detect(function(n) { return n.value == result.path });
      
      if (!elem) return; // warning
      
	    // Cochage de la case envoi pour le fichier
	    var elem_td = elem.up("tr").down(".upload");
	    elem_td.className = "tick";
			
	    // Après l'upload du fichier, on peut créer le CFile
	    var fast_file_form = getForm("addFastFile");
	    $V(fast_file_form._checksum, result.checksum);
			$V(fast_file_form.object_class, this.object_guid.split('-')[0]);
			$V(fast_file_form.object_id, this.object_guid.split('-')[1]);
	    $V(fast_file_form._file_path, result.path);
	    fast_file_form.onsubmit();
	  },
	  handleListFiles: function(result) {
	    var list_files = $("file-list");
	    var nb_files = 0;
	    result.files.each(function(res, index) {
	        // Si le fichier n'est pas dans la liste sauvegardée
	        if (File.applet.listFiles.indexOf(res.path) == -1) {
	          // Ajout du fichier dans la liste et dans la modale
	          list_files.insert(
						  DOM.tr({},
							  DOM.td({},
								  DOM.input({className: "upload-file", type: "checkbox", value: res.path, checked: 'checked'}),
									DOM.span({}, res.path)),
							  DOM.td({className: "upload"}),
								DOM.td({className: "assoc"}),
								DOM.td({className: "delete"})));
	          File.applet.listFiles.push(res.path);
	          File.applet.current_list.push(res);
						File.applet.current_list_status.push([res.path, 0]);
	          nb_files ++;
	        }
	      });
				
	     if (nb_files > 0) {
			 	 this.found_files = 1;
				 //console.log($$(".yopletbutton"));
         // On active tous les boutons upload disponibles
	       $$(".yopletbutton").each(function(button) {
				 	  button.disabled = "";
            button.style.border = "3px solid #080";
				 });
	     }
	  },
	  handleDeletionOK: function(result) {
	    var elem = $$("input:checked").detect(function(n) { return n.value == result.path });
	    elem.up("tr").down(".delete").className = "tick";
	  },
	
	  handleDeletionKO: function(result) {
	    alert('La suppression du fichier a échoué : ' + result.path);
	    var elem = this.modaleWindow.container.select("input:checked").detect(function(n) { return n.value == result.path });
      if (!elem) return; //warning
	    elem.up("tr").down(".delete").className = "warning";
	  },
		cancelModal: function() {
		  Control.Modal.close();
	    // Ferme la modale en cliquant sur annuler,
		},
		modalOpen: function(object_guid) {
		  this.modaleWindow = modal($("modal-yoplet"));
			$$(".uploadinmodal")[0].disabled = '';
			this.object_guid = object_guid;
		  this.modaleWindow.position();
      this.executer.stop();
		},
		closeModal: function() {
			this.found_files = false;
			Control.Modal.close();
			// Clique sur Ok dans la modale,
      // alors on vide la liste des fichiers dans la modale
      // et on désactive les boutons upload
			File.applet.current_list = [];
			File.applet.current_list_status = [];
			File.applet.listFiles = [];
			$('file-list').update();
			$$('.yopletbutton').each(function(elem) {
          elem.disabled='disabled';
          elem.style.border = '1px solid #888';
      });
			File.refresh(this.object_guid.split("-")[1], this.object_guid.split("-")[0]);
      this.executer.resume();
		},
		addfile_callback: function(id, args) {
		  var file_name = args["file_name"];
		  var elem = this.modaleWindow.container.select("input:checked").detect(function(n){ 
        return n.value.replace(/[^\x00-\xFF]/g, "?").indexOf(file_name) != -1; // vieux hack des sous bois
      });
      if (!elem) return; //warning
      
		  var td_el = elem.up("tr").down(".assoc");
		  
      if (id) {
		    td_el.className = 'tick';
		    this.listFiles
		    var file = this.current_list.detect(function(n) { return n.path.replace(/[^\x00-\xFF]/g, "?").indexOf(file_name) != -1});
		    file = file.path;
				// Ajouter le status associé dans la liste des fichiers.
				var cur_status = this.current_list_status.detect(function(n) { return n[0].replace(/[^\x00-\xFF]/g, "?").indexOf(file_name) != -1});
				cur_status[1] = 1;

				// S'ils sont tous associés, alors on peut lancer la suppression 		    
				if (this.current_list_status.all(function(n){ return n[1] == 1;})) {
					// Si la suppression auto est cochée
					
					if (getForm("addFastFile").delete_auto.checked) {
						File.applet.uploader.performDelete(Object.toJSON(this.current_list_status.pluck("0")));
					}
					else {
						if (confirm("Voulez-vous supprimer les fichiers qui ont été envoyés sur le serveur ?")) {
              File.applet.uploader.performDelete(Object.toJSON(this.current_list_status.pluck("0")));
						}
						else {
							td_el.up("tr").down(".delete").className = 'warning';
						}
					}
				}
		  } else {
		      td_el.className = 'warning';
		  }
		}
	};
  
  // La fonction appletCallBack ne peut pas être incluse dans l'objet File.applet
  function appletCallBack(args) {
    // Ajouter l'url du script comme paramètre
    if (!File.applet.uploader) {
      File.applet.uploader = document.applets.yopletuploader;
      File.applet.directory = File.appletDirectory;
    }
    
    if (args) {
      var operation = args.evalJSON();
      var opname = operation.name;
      switch (opname) {
        case 'init':
                File.applet.uploader.setFileFilters("bmp gif jpeg jpg png tif pdf"); // case sensitive !
                File.applet.watchDirectory();
                break;
        case 'listfiles':
                File.applet.handleListFiles(operation.result);
                break;
        case 'uploadok':
                File.applet.handleUploadOk(operation.result);
                break;
        case 'uploadko':
                File.applet.handleUploadKO(operation.result);
                break;
        case 'deleteok':
                File.applet.handleDeletionOK(operation.result);
                break;
        case 'deleteko':
                File.applet.handleDeletionKO(operation.result);
                break;
        default:
                break;
      }
    }
      else {
        alert('could not parse callback message');
    }
  }
}
