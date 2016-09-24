<?php 
	class ContactsBTempViewModel extends ViewModel {
	   public $viewFields = array(
		'contactsB'=>array('contactsB_id','creator_role_id','name','post','department','sex','saltname','telephone','email','qq_no','address','zip_code','description','create_time','update_time','is_deleted','delete_role_id','delete_time','_type'=>'LEFT'),
		'RContactsBCustomer'=>array('_on'=>'contactsB.contactsB_id=RContactsBCustomer.contactsB_id','_type'=>'LEFT'),
		'customer'=>array('customer_id','owner_role_id','name'=>'customer_name','_on'=>'customer.customer_id=RContactsBCustomer.customer_id')
	   );
/* 	   public function _initialize(){
			$this->viewFields = array(  'contactsB'=>array('*'),
						'contactsB_data'=>array('*', '_on'=>'contactsB.contactsB_id = contactsB_data.contactsB_id','_type'=>'LEFT'),
						'RContactsBCustomer'=>array('customer_id','_on'=>'RContactsBCustomer.contactsB_id=contactsB.contactsB_id','_type'=>'LEFT'),
						'Customer'=>array('name'=>'customer_name', '_on'=>'RContactsBCustomer.customer_id=Customer.customer_id')
				);
	   } */

	} 