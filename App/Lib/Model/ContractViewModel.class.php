<?php 
	class contractViewModel extends ViewModel {
	   public $viewFields = array(
		'contract'=>array('contract_id','number','price_sheet_id','is_deleted','is_cancel','start_date','end_date','delete_role_id','delete_time','price','due_time','dept_name','creator_role_id','owner_role_id','description','create_time','update_time','status','add_file','_type'=>'LEFT'),
   		'priceSheet'=>array('number'=>'price_sheet_number','customer_id','customerB_id','role_id','reason','total_amount','subtotal_val','willtotal_val','service_val', '_on'=>'contract.price_sheet_id=priceSheet.id','_type'=>'LEFT'),
//    		'business'=>array('name'=>'business_name','contacts_id'=>'contacts_id','customer_id'=>'customer_id', '_on'=>'contract.business_id=business.business_id','_type'=>'LEFT'),
// 		'contacts'=>array('name'=>'contacts_name', '_on'=>'contacts.contacts_id=business.contacts_id','_type'=>'LEFT'),
		'customer'=>array('gongsiquancheng'=>'customer_name', '_on'=>'customer.customer_id=priceSheet.customer_id','_type'=>'LEFT'),
	   	'customerB'=>array('name'=>'customerB_name', '_on'=>'priceSheet.customerB_id=customerB.customerB_id','_type'=>'LEFT'),
		'user'=>array('name'=>'owner_name', '_on'=>'contract.owner_role_id=user.role_id')
	   );
	} 