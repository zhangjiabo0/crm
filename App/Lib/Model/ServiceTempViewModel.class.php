<?php 
	class ServiceTempViewModel extends ViewModel {
	   public $viewFields = array(
		'service'=>array('service_id','creator_role_id','service_time','service_content','create_time','update_time','is_deleted','delete_role_id','delete_time','_type'=>'LEFT'),
		'RServiceCustomerB'=>array('_on'=>'service.service_id=RServiceCustomerB.service_id','_type'=>'LEFT'),
		'customerB'=>array('customerB_id','owner_role_id','name'=>'customerB_name','_on'=>'customerB.customerB_id=RServiceCustomerB.customerB_id')
	   );
/* 	   public function _initialize(){
			$this->viewFields = array(  'service'=>array('*'),
						'service_data'=>array('*', '_on'=>'service.service_id = service_data.service_id','_type'=>'LEFT'),
						'RServiceCustomerB'=>array('customerB_id','_on'=>'RServiceCustomerB.service_id=service.service_id','_type'=>'LEFT'),
						'CustomerB'=>array('name'=>'customerB_name', '_on'=>'RServiceCustomerB.customerB_id=CustomerB.customerB_id')
				);
	   } */

	} 