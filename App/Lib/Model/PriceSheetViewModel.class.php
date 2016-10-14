<?php 
	class PriceSheetViewModel extends ViewModel{
		public $viewFields = array(
			'PriceSheet'=>array('id','number','customer_id','customerB_id','department','role_id','reason','add_file','create_time','total_amount','subtotal_val','willtotal_val','service_val','_type'=>'LEFT'),
			'Customer'=>array('gongsiquancheng'=>'customer_name','_on'=>'PriceSheet.customer_id=Customer.customer_id','_type'=>'LEFT'),
			'CustomerB'=>array('name'=>'service_name', '_on'=>'PriceSheet.customerB_id = CustomerB.customerB_id','_type'=>'LEFT'),
			'User'=>array('true_name','name'=>'user_name','_on'=>'PriceSheet.role_id = User.role_id')
		);
		
	}