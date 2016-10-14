<?php 
class PriceSheetAction extends CommonAction {

	public function _initialize(){
		$action = array(
			'permission'=>array(),
			'allow'=>array('index','add','upload','getParentsDeptName')
		);
		B('Authenticate', $action);
	}
	
	public function index(){
		if ($_REQUEST["field"]) {//查询条件处理
			if (trim($_REQUEST['field']) == "all") {
				$field = is_numeric(trim($_REQUEST['search'])) ? 'number|price|contract.description' : 'number|contract.description';
			} else {
				$field = trim($_REQUEST['field']);
			}
			$search = empty($_REQUEST['search']) ? '' : trim($_REQUEST['search']);
			$condition = empty($_REQUEST['condition']) ? 'is' : trim($_REQUEST['condition']);
		
			if	('create_time' == $field || 'update_time' == $field || 'start_date' == $field || 'end_date' == $field) {
				$search = is_numeric($search)?$search:strtotime($search);
			}
			switch ($condition) {
				case "is" :
					if($field == 'customer_id'){
						$where['customer.'.$field] = array('eq',$search);
					}else{
						$where['contract.'.$field] = array('eq',$search);
					}break;
				case "isnot" :  $where['contract.'.$field] = array('neq',$search);break;
				case "contains" :  $where['contract.'.$field] = array('like','%'.$search.'%');break;
				case "not_contain" :  $where['contract.'.$field] = array('notlike','%'.$search.'%');break;
				case "start_with" :  $where['contract.'.$field] = array('like',$search.'%');break;
				case "end_with" :  $where['contract.'.$field] = array('like','%'.$search);break;
				case "is_empty" :  $where['contract.'.$field] = array('eq','');break;
				case "is_not_empty" :  $where['contract.'.$field] = array('neq','');break;
				case "gt" :  $where['contract.'.$field] = array('gt',$search);break;
				case "egt" :  $where['contract.'.$field] = array('egt',$search);break;
				case "lt" :  $where['contract.'.$field] = array('lt',$search);break;
				case "elt" :  $where['contract.'.$field] = array('elt',$search);break;
				case "eq" : $where['contract.'.$field] = array('eq',$search);break;
				case "neq" : $where['contract.'.$field] = array('neq',$search);break;
				case "between" : $where['contract.'.$field] = array('between',array($search-1,$search+86400));break;
				case "nbetween" : $where['contract.'.$field] = array('not between',array($search,$search+86399));break;
				case "tgt" :  $where['contract.'.$field] = array('gt',$search+86400);break;
				default :	$where[$field] = array('eq',$search);
			}
			$params = array('field='.trim($_REQUEST['field']), 'condition='.$condition, 'search='.$_REQUEST["search"]);
		}
		
		$all_ids = getSubRoleIdByYuan(true);
		$p = isset($_GET['p']) ? intval($_GET['p']) : 1 ;
		if($_GET['listrows']){
			$listrows = $_GET['listrows'];
			$params[] = "listrows=" . trim($_GET['listrows']);
		}else{
			$listrows = 15;
			$params[] = "listrows=15";
		}
		$where['PriceSheet.is_del'] = 0;
		$where['PriceSheet.role_id'] = array('in',implode(',',$all_ids));
		$price = D('PriceSheetView');
		$list = $price -> where($where) -> order('PriceSheet.create_time DESC')->page($p.','.$listrows) -> select();
		$count = $price -> where($where) -> count();
		import("@.ORG.Page");
		$Page = new Page($count,$listrows);
		if (!empty($_GET['by'])) {
			$params[] = "by=" . trim($_GET['by']);
		}
		if (!empty($_GET['content'])) {
			$params[] = "content=" . trim($_GET['content']);
		}
		$this->parameter = implode('&', $params);
		
		if ($_GET['desc_order']) {
			$params[] = "desc_order=" . trim($_GET['desc_order']);
		} elseif($_GET['asc_order']){
			$params[] = "asc_order=" . trim($_GET['asc_order']);
		}
		$Page->parameter = implode('&', $params);
		$this->assign('page',$Page->show());
		$this->listrows = $listrows;
		$this->list = $list;
		$this->assign('count',$count);
		$this->alert = parseAlert();
		$this->display();
	}
	
	/**
	 * 添加报价单
	 */
	public function add(){
		$widget['date'] = true;
		$widget['uploader'] = true;
		$widget['editor'] = true;
		$this -> assign("widget", $widget);
		if($this->isPost()){
			$sheet = M('priceSheet');
			if(!$_POST['number'])	alert('error', L('CONTRACT_NO_EMPTY'), $_SERVER['HTTP_REFERER']);
			else $data['number'] = trim($_POST['number']);//合同编号
			if(!$_POST['customer_id'])	alert('error', L('CUSTOMER_NAME_NOTNULL'), $_SERVER['HTTP_REFERER']);
			else $data['customer_id'] = trim($_POST['customer_id']);//客户名称
			if(!$_POST['customerB_id'])	alert('error', L('SERVICE_NAME_NOTNULL'), $_SERVER['HTTP_REFERER']);
			else $data['customerB_id'] = trim($_POST['customerB_id']);//服务商名称
			if(!is_array($_POST['product']))alert('error', L('PLEASE_SELECT_PRODUCT'), $_SERVER['HTTP_REFERER']);
			else{
				foreach ($_POST['product'] as $v){
					if(empty($v['provider_price'])) alert('error',L('PROVIDER_PRICES') , $_SERVER['HTTP_REFERER']);
				}
			}
			$data['role_id'] = $_POST['owner_role_id']?$_POST['owner_role_id']:session('role_id');//业务员id
			$data['department'] = trim($_POST['department']);//所属部门
			$data['reason'] = trim($_POST['reason']);//申请事由
			$data['add_file'] = trim($_POST['add_file']);//添加附件
			$data['create_time'] = time();
			$data['total_amount'] = trim($_POST['total_amount']);//产品个数
			$data['subtotal_val'] = trim($_POST['subtotal_val']);//产品总价
			$data['willtotal_val'] = trim($_POST['willtotal_val']);//产品客户意向总价
			$data['service_val'] = trim($_POST['service_val']);//产品服务商总价
			if($sid = $sheet -> add($data)){
				//添加产品
				$m_rbusinessProduct = M('RPriceSheetProduct');
				if(is_array($_POST['product'])){
					foreach($_POST['product'] as $val){
						$data['product_id'] = $val['product_id'];//产品id
						$data['product_name'] = $val['product_name'];//产品名称
						$data['amount'] = $val['amount'];//数量
						$data['unit_price'] = $val['unit_price'];//原价
						$data['subtotal'] = $val['subtotal'];//金额
						$data['will_price'] = $val['will_price'];//客户意向单价
						$data['will_total'] = $val['will_total'];//客户意向金额
						$data['provider_price'] = $val['provider_price'];//服务商单价
						$data['provider_total'] = $val['provider_total'];//服务商金额
						$data['tax_rate'] = $val['tax_rate'];//折扣
						$data['lirun'] = $val['lirun'];//利润
						$data['discount_rate'] = $val['discount_rate'];//产品说明
						$data['description'] = $val['description'];//备注
						$data['sheet_id'] = $sid;//报价单id
						$m_rbusinessProduct->add($data);
					}
				}
			actionLog($sid);
			}
			if($_POST['submit'] == L('SAVE')) {
				if($_POST['refer_url']){
					alert('success', L('CREATE_A_CONTRACT_SUCCESSFULLY'), $_POST['refer_url']);
				}else{
					alert('success', L('CREATE_A_CONTRACT_SUCCESSFULLY'), U('priceSheet/index'));
				}
			}else{
				alert('error', L('FAILED_TO_CREATE_THE_CONTRACT'), U('priceSheet/add'));
			}
		}else{
			if(intval($_GET['business_id'])){
				$this->assign('business_id',intval($_GET['business_id']));
				$this->assign('contract_custom', 'BJ'.date('Ymd').rand(1000,9999));
				$this->alert = parseAlert();
				$this->refer_url = $_SERVER['HTTP_REFERER'];
				$this->display('adddialog');
			}else{
				$this->assign('contract_custom', 'BJ'.date('Ymd').rand(1000,9999));
				$this->refer_url=$_SERVER['HTTP_REFERER'];
				$this->alert = parseAlert();
				$this->display();
			}
		}
	}
	/**
	 * 查看报价单
	 */
	public function view(){
		$id = $_GET['id'];
		$sheet = D('PriceSheetView');
		$list = $sheet -> where('PriceSheet.is_del=0') -> find($id);
		//绑定报价单下面的产品信息
		if(!empty($list['id'])){
			$product = M('RPriceSheetProduct');
			$list['products'] = $product -> where('sheet_id = '.$list['id']) -> select();
		}
		$this-> vo = $list;
		$this -> display();
	}
	/**
	 * 作废报价单
	 */
	public function del(){
		$id = $_GET['id'];
		$flag = M('PriceSheet')->save(array('id'=>$id,'is_del'=>1));
		alert('success', '作废成功!', U('priceSheet/index'));
	}
}