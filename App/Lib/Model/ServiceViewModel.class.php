<?php 
	class ServiceViewModel extends ViewModel {
	   public $viewFields = array(
		'service'=>array('service_id','creator_role_id','service_time','service_content','create_time','update_time','is_deleted','delete_role_id','delete_time','_type'=>'LEFT'),
		'RServiceCustomerB'=>array('_on'=>'service.service_id=RServiceCustomerB.service_id','_type'=>'LEFT'),
		'customerB'=>array('customerB_id','owner_role_id','name'=>'customerB_name','_on'=>'customerB.customerB_id=RServiceCustomerB.customerB_id')
	   );
	} 