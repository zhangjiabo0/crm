<?php 
	class ContactsBTempViewModel extends ViewModel {
	   public $viewFields = array(
		'contactsB'=>array('contactsB_id','creator_role_id','name','post','department','sex','saltname','telephone','email','qq','weixin','address','zip_code','description','create_time','update_time','is_deleted','delete_role_id','delete_time','_type'=>'LEFT'),
		'RContactsBCustomerB'=>array('_on'=>'contactsB.contactsB_id=RContactsBCustomerB.contactsB_id','_type'=>'LEFT'),
		'customerB'=>array('customerB_id','owner_role_id','name'=>'customerB_name','_on'=>'customerB.customerB_id=RContactsBCustomerB.customerB_id')
	   );
/* 	   public function _initialize(){
			$this->viewFields = array(  'contactsB'=>array('*'),
						'contactsB_data'=>array('*', '_on'=>'contactsB.contactsB_id = contactsB_data.contactsB_id','_type'=>'LEFT'),
						'RContactsBCustomerB'=>array('customerB_id','_on'=>'RContactsBCustomerB.contactsB_id=contactsB.contactsB_id','_type'=>'LEFT'),
						'CustomerB'=>array('name'=>'customerB_name', '_on'=>'RContactsBCustomerB.customerB_id=CustomerB.customerB_id')
				);
	   } */

	} 