<?php 
	class contractEpibolyViewModel extends ViewModel {
	   public $viewFields = array(
		'contractEpiboly'=>array('contract_id','number','price_sheet_id','is_deleted','is_cancel','cancel_time','start_date','end_date','delete_role_id','delete_time','price','due_time','dept_name','creator_role_id','owner_role_id','description','create_time','update_time','status','add_file','confirm','confirm_name','_type'=>'LEFT'),
   		'priceSheet'=>array('number'=>'price_sheet_number','customer_id','customerB_id','role_id','reason','total_amount','subtotal_val','willtotal_val','service_val', '_on'=>'contractEpiboly.price_sheet_id=priceSheet.id','_type'=>'LEFT'),
		'customer'=>array('gongsiquancheng'=>'customer_name', '_on'=>'customer.customer_id=priceSheet.customer_id','_type'=>'LEFT'),
	   	'customerB'=>array('name'=>'customerB_name', '_on'=>'priceSheet.customerB_id=customerB.customerB_id','_type'=>'LEFT'),
		'user'=>array('name'=>'owner_name', '_on'=>'contractEpiboly.owner_role_id=user.role_id')
	   );
	} 