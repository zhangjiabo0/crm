<?php	class CustomerBViewModel extends ViewModel {        protected $viewFields;		public function _initialize(){			$main_must_field = array('customerB_id','owner_role_id','is_locked','creator_role_id','contactsB_id','delete_role_id','create_time','delete_time','update_time','is_deleted','add_file','service');			$main_list_part = M('Fields')->where(array('model'=>'customerB','is_main'=>1))->getField('field', true);			$main_list = array_unique(array_merge($main_list_part?$main_list_part:array(),$main_must_field));			$main_list['_type'] = 'LEFT';			$data_list = M('Fields')->where(array('model'=>'customerB','is_main'=>0))->getField('field', true);			$data_list['_on'] = 'customerB.customerB_id = customerB_data.customerB_id';			$data_list['_type'] = 'LEFT';			$provide = array('provide_id','provide_name');			$provide['_on'] = 'customerB.customerB_id = provide.customerB_id';			$provide['_type'] = 'LEFT';			$this->viewFields = array('customerB'=>$main_list,'customerB_data'=>$data_list,'provide'=>$provide);		}	}