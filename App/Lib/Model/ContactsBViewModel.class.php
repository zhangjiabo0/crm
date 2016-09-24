<?php 
	class ContactsBViewModel extends ViewModel {
	   public $viewFields = array(
		'contactsB'=>array('contactsB_id','creator_role_id','name','post','department','sex','saltname','telephone','email','qq_no','address','zip_code','description','create_time','update_time','is_deleted','delete_role_id','delete_time','_type'=>'LEFT'),
		'RContactsBCustomerB'=>array('_on'=>'contactsB.contactsB_id=RContactsBCustomerB.contactsB_id','_type'=>'LEFT'),
		'customerB'=>array('customerB_id','owner_role_id','name'=>'customerB_name','_on'=>'customerB.customerB_id=RContactsBCustomerB.customerB_id')
	   );
	} 