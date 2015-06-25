/**
 * Created by flavien on 23/09/14.
 */

FileMbHost = {
  extensions: null,
  extensions_thumb: null,
  paths: null,
  timeoutID: null,

  periodicalUpdateCount: function() {
    MbHost.call('fs/file/count', {"extensions": FileMbHost.extensions}, function(result) {
        var buttons = $$(".mbhostbutton");
        buttons.each(function(button) {
          if (result) {
            button.disabled = "";
          }
          button.style.border = result ? "2px solid #0a0" : "1px solid #888";
        });
      },
      function(error) {
      });

      FileMbHost.timeoutID = setTimeout(FileMbHost.periodicalUpdateCount, 3000);
  },

  modalUpload: function(object_guid) {
    clearTimeout(FileMbHost.timeoutID);
    MbHost.call('fs/file/list', {'extensions': FileMbHost.extensions, 'extensions_thumb': FileMbHost.extensions_thumb, 'maxsize': 10240000}, function(result) {
      var modal = $('mbhost_file_'+object_guid);
      modal.update();

      $H(result).each(function(pair) {
        if (!pair.value.file_name && pair.value.content == '') {
          return;
        }

        modal.insert(DOM.div({
            'onclick': 'this.toggleClassName(\'thumb_selected\')',
            'data-file_name': pair.value.file_name,
            'class': 'thumb_mbhost'
          },
          pair.key + '<br />',
          pair.value.content ?
            DOM.img({
              src: 'data:image/png;base64,'+pair.value.content,
              'style': 'max-width: 160px;'
            })
            : null
        ));
      });
      Modal.open(modal.up('div'), {onClose: FileMbHost.periodicalUpdateCount});
    },
    function(error) {
    });
  },

  sendFiles: function(object_guid) {
    //$('mbhost_file_'+object_guid).up('div').select('buttons')
    FileMbHost.paths =  $$(".thumb_selected");
    FileMbHost.sendFile(object_guid);
  },

  sendFile: function(object_guid) {
    var file = FileMbHost.paths.shift();
    var del_file = $('_del_file_' + object_guid).checked ? 1 : 0;
    var file_name = file.get('file_name');

    MbHost.call("fs/file/getfile", {'path': file_name}, function(content) {
      if (del_file) {
        MbHost.call("fs/file/delfile", {'path': file_name});
      }

      var form = getForm("sendFile" + object_guid);
      $V(form.file_name, file_name);
      $V(form.content, content);
      onSubmitFormAjax(form, function() {
        if (FileMbHost.paths.length > 0) {
          FileMbHost.sendFile(object_guid);
        }
        else {
          var object_class = object_guid.split('-')[0];
          var object_id = object_guid.split('-')[1];

          // Pour le refresh dans les consultations
          File.refresh(object_id, object_class);

          // Pour le refresh dans le dossier patient
          if (window.reloadAfterUploadFile) {
            window.reloadAfterUploadFile();
          }

          // Por le refresh dans l'édition du dossier patient
          if (window.reloadListFileEditPatient) {
            reloadListFileEditPatient("load");
          }

          Control.Modal.close();
          File.refresh()
        }
      });
    },
    function(error) {
    });
  }
};