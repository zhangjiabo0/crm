<?php 
	class ContactsBLViewModel extends ViewModel {
	   public $viewFields = array(
		'contactsB'=>array('contactsB_id','creator_role_id','name','post','department','sex','saltname','telephone','email','qq','weixin','address','zip_code','description','create_time','update_time','is_deleted','delete_role_id','delete_time','_type'=>'LEFT'),
		'RContactsBLeadsB'=>array('_on'=>'contactsB.contactsB_id=RContactsBLeadsB.contactsB_id','_type'=>'LEFT'),
		'leadsB'=>array('leadsB_id','owner_role_id','name'=>'leadsB_name','_on'=>'leadsB.leadsB_id=RContactsBLeadsB.leadsB_id')
	   );
	} 