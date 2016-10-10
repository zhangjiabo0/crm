<?php 
class PriceSheetAction extends Action {

	public function _initialize(){
		$action = array(
			'permission'=>array(),
			'allow'=>array('index')
		);
		B('Authenticate', $action);
	}
	
	public function index(){
		
		$this -> display();
	}
	
	/**
	 * 添加报价单
	 */
	public function add(){
		$contract_custom = M('config') -> where('name="contract_custom"')->getField('value');
		if(!$contract_custom)  $contract_custom = '5k_crm';
		if($this->isPost()){
			$contract = M('contract');
			if(!$_POST['number'])	alert('error', L('CONTRACT_NO_EMPTY'), $_SERVER['HTTP_REFERER']);
			else $data['number'] = trim($_POST['number']);
			$data['due_time'] = $_POST['due_time']?strtotime($_POST['due_time']):time();
			$data['business_id'] = intval($_POST['business_id']);
			if(empty($data['business_id'])){
				alert('error',L('PLEASE_SELECT_A_BUSINESS_OPPORTUNITY'),$_SERVER['HTTP_REFERER']);
			}
			$data['customer_id'] = intval($_POST['customer_id']);
			$data['owner_role_id'] = $_POST['owner_role_id']?$_POST['owner_role_id']:session('role_id');
			$data['creator_role_id'] = session('role_id');
			$data['price'] = intval($_POST['price']);
			$data['content'] = trim($_POST['content']);
			$data['description'] = trim($_POST['description']);
			$data['start_date'] = strtotime($_POST['start_date']);
			$data['end_date'] = strtotime($_POST['end_date']);
			$data['create_time'] = time();
			$data['update_time'] = time();
			$data['status'] = L('HAS_BEEN_CREATED');
	
			if($contractId = $contract->add($data)){
				M('RBusinessContract')->add(array('contract_id'=>$contractId,'business_id'=>$data['business_id']));
				actionLog($contractId);
				if($_POST['refer_url']){
					alert('success', L('CREATE_A_CONTRACT_SUCCESSFULLY'), $_POST['refer_url']);
				}else{
					alert('success', L('CREATE_A_CONTRACT_SUCCESSFULLY'), U('contract/index'));
				}
			}else{
				alert('error', L('FAILED_TO_CREATE_THE_CONTRACT'), U('contract/add'));
			}
		}else{
			if(intval($_GET['business_id'])){
				$this->assign('business_id',intval($_GET['business_id']));
				$this->assign('contract_custom', $contract_custom.date('Ymd').rand(1000,9999));
				$this->alert = parseAlert();
				$this->refer_url = $_SERVER['HTTP_REFERER'];
				$this->display('adddialog');
			}else{
				$this->assign('contract_custom', $contract_custom.date('Ymd').rand(1000,9999));
				$this->refer_url=$_SERVER['HTTP_REFERER'];
				$this->alert = parseAlert();
				$this->display();
			}
		}
	}
}