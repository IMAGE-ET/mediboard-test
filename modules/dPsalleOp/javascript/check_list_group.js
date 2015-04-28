CheckListGroup = {
  modal_group: null,
  modal_checklist: null,
  
  duplicate: function () {
    var url = new Url('salleOp', 'vw_daily_check_list_group');
    url.addParam("check_list_group_id", 0);
    url.addParam("duplicate"      , 1);
    url.requestModal();
  },
  
  edit: function (check_list_group_id) {
    var url = new Url('salleOp', 'vw_daily_check_list_group');
    url.addParam("check_list_group_id", check_list_group_id);
    url.requestModal();
    url.modalObject.observe("afterClose", function(){
      location.reload();
    });
    CheckListGroup.modal_group = url;
  },
  
  editChecklist: function (check_list_type_id, check_list_group_id) {
    var url = new Url('salleOp', 'ajax_edit_checklist_type');
    url.addParam("check_list_type_id", check_list_type_id);
    url.addParam("modal", 1);
    if (!Object.isUndefined(check_list_group_id)) {
      url.addParam('check_list_group_id', check_list_group_id);
    }
    url.requestModal(500);
    url.modalObject.observe("afterClose", function(){
      CheckListGroup.modal_group.refreshModal();
    });
    CheckListGroup.modal_checklist = url;
  }
};