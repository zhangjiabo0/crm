<?php 
	class ContactsBViewModel extends ViewModel {
	   public $viewFields = array(
		'contactsB'=>array('contactsB_id','creator_role_id','name','post','department','sex','saltname','telephone','email','qq_no','address','zip_code','description','create_time','update_time','is_deleted','delete_role_id','delete_time','_type'=>'LEFT'),
		'RContactsBCustomer'=>array('_on'=>'contactsB.contactsB_id=RContactsBCustomer.contactsB_id','_type'=>'LEFT'),
		'customer'=>array('customer_id','owner_role_id','name'=>'customer_name','_on'=>'customer.customer_id=RContactsBCustomer.customer_id')
	   );
	} 