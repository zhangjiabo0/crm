<?php 
	class LeadsBViewModel extends ViewModel{
		public $viewFields;
		public function _initialize(){
			$main_must_field = array('leadsB_id','creator_role_id','owner_role_id','create_time','update_time','contacts_id','customerB_id','is_deleted');
            $main_list_part = M('Fields')->where(array('model'=>'leadsB','is_main'=>1))->getField('field', true);
			$main_list = array_unique(array_merge($main_list_part?$main_list_part:array(),$main_must_field));
			$main_list['_type'] = 'LEFT';
			$data_list = M('Fields')->where(array('model'=>'leadsB','is_main'=>0))->getField('field', true);
			$data_list['_on'] = 'leadsB.leadsB_id = leadsB_data.leadsB_id';
            $data_list['_type'] = 'LEFT';
			
			$this->viewFields = array(
				'leadsB'=>$main_list,
				'leadsB_data'=>$data_list, 
			);
		}

	}