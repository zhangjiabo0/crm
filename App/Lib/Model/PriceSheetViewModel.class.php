<?php 
	class PriceSheetViewModel extends ViewModel{
		public $viewFields = array(
			'priceSheet'=>array('id','number','customer_id','customerB_id','role_id','department','reason','add_file','create_time','total_amount','subtotal_val','willtotal_val','service_val' ,'_type'=>'LEFT'),
			'RPriceSheetProduct'=>array('product_id','product_name','amount','unit_price', '_on'=>'priceSheet.id=RPriceSheetProduct.sheet_id','_type'=>'LEFT'),
			'customer'=>array('gongsiquancheng'=>'customer_name', '_on'=>'priceSheet.customer_id=customer.customer_id','_type'=>'LEFT'),
			'customerB'=>array('name'=>'customerB_name', '_on'=>'priceSheet.customerB_id=customerB.customerB_id'),
		);

	}