<?php 
class PriceSheetAction extends CommonAction {

	public function _initialize(){
		$action = array(
			'permission'=>array(),
			'allow'=>array('index','add','upload','flowlog')
		);
		B('Authenticate', $action);
	}
	
	public function index(){
		$where = array();
		$below_ids = getSubRoleId(false);
		$all_ids = getSubRoleIdByYuan(true);
		$order = 'PriceSheet.create_time desc';
		if($_GET['desc_order']){
			$order = trim($_GET['desc_order']).' desc';
		}elseif($_GET['asc_order']){
			$order = trim($_GET['asc_order']).' asc';
		}
		switch ($_GET['by']){
			case 'create':
				$where['PriceSheet.role_id'] = session('role_id');
				break;
			case 'sub' :
				$where['PriceSheet.role_id'] = array('in',implode(',', $below_ids));
				break;
			case 'subcreate' :
				$where['PriceSheet.role_id'] = array('in',implode(',', $below_ids));
				break;
			case 'today' :
				$where['PriceSheet.due_time'] =  array('between',array(strtotime(date('Y-m-d')) -1 ,strtotime(date('Y-m-d')) + 86400));
				break;
			case 'week' :
				$week = (date('w') == 0)?7:date('w');
				$where['PriceSheet.due_time'] =  array('between',array(strtotime(date('Y-m-d')) - ($week-1) * 86400 -1 ,strtotime(date('Y-m-d')) + (8-$week) * 86400));
				break;
			case 'month' :
				$next_year = date('Y')+1;
				$next_month = date('m')+1;
				$month_time = date('m') ==12 ? strtotime($next_year.'-01-01') : strtotime(date('Y').'-'.$next_month.'-01');
				$where['PriceSheet.due_time'] = array('between',array(strtotime(date('Y-m-01')) -1 ,$month_time));
				break;
			case 'add' :
				$order = 'PriceSheet.create_time desc';
				break;
			case 'deleted' :
				$where['PriceSheet.is_del'] = 1;
				break;
			case 'update' :
				$order = 'PriceSheet.update_time desc';
				break;
			case 'me' :
				$where['PriceSheet.role_id'] = session('role_id');
				break;
			default:
				$where['PriceSheet.role_id'] = array('in',implode(',', $all_ids));
				break;
		}
		
		if (!isset($where['PriceSheet.is_del'])) {
			$where['PriceSheet.is_del'] = 0;
		}
		if (!isset($where['PriceSheet.role_id'])) {
			$where['PriceSheet.role_id'] = array('in',implode(',', $all_ids));
		}
		if ($_REQUEST["field"]) {//查询条件处理
			if (trim($_REQUEST['field']) == "all") {
				$field = is_numeric(trim($_REQUEST['search'])) ? 'number|PriceSheet.reason' : 'number|PriceSheet.reason';
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
						$where['PriceSheet.'.$field] = array('eq',$search);
					}break;
				case "isnot" :  $where['PriceSheet.'.$field] = array('neq',$search);break;
				case "contains" :  $where['PriceSheet.'.$field] = array('like','%'.$search.'%');break;
				case "not_contain" :  $where['PriceSheet.'.$field] = array('notlike','%'.$search.'%');break;
				case "start_with" :  $where['PriceSheet.'.$field] = array('like',$search.'%');break;
				case "end_with" :  $where['PriceSheet.'.$field] = array('like','%'.$search);break;
				case "is_empty" :  $where['PriceSheet.'.$field] = array('eq','');break;
				case "is_not_empty" :  $where['PriceSheet.'.$field] = array('neq','');break;
				case "gt" :  $where['PriceSheet.'.$field] = array('gt',$search);break;
				case "egt" :  $where['PriceSheet.'.$field] = array('egt',$search);break;
				case "lt" :  $where['PriceSheet.'.$field] = array('lt',$search);break;
				case "elt" :  $where['PriceSheet.'.$field] = array('elt',$search);break;
				case "eq" : $where['PriceSheet.'.$field] = array('eq',$search);break;
				case "neq" : $where['PriceSheet.'.$field] = array('neq',$search);break;
				case "between" : $where['PriceSheet.'.$field] = array('between',array($search-1,$search+86400));break;
				case "nbetween" : $where['PriceSheet.'.$field] = array('not between',array($search,$search+86399));break;
				case "tgt" :  $where['PriceSheet.'.$field] = array('gt',$search+86400);break;
				default :	$where[$field] = array('eq',$search);
			}
			$params = array('field='.trim($_REQUEST['field']), 'condition='.$condition, 'search='.$_REQUEST["search"]);
		}
		
		$p = isset($_GET['p']) ? intval($_GET['p']) : 1 ;
		if($_GET['listrows']){
			$listrows = $_GET['listrows'];
			$params[] = "listrows=" . trim($_GET['listrows']);
		}else{
			$listrows = 15;
			$params[] = "listrows=15";
		}
		$price = D('PriceSheetView');
		$list = $price -> where($where) -> order($order)->page($p.','.$listrows) -> select();
		$count = $price -> where($where) -> count();
		//循环添加可以审批的人
		$log = M('PriceSheetFlowLog');
		foreach ($list as $k => $v){
			$where2['price_flow_id'] = $v['id'];
			$where2['result'] = '-1';
			$tmp = $log -> where($where2) -> getField('role_id');
			$sign = false;
			if(strpos($tmp,',')){
				$rs = array_filter(explode(',',$tmp));
				foreach ($rs as $kk => $vv){
					if($_SESSION['role_id'] == $vv){
						$sign = true;break;
					}
				}
			}else{
				if($tmp == $_SESSION['role_id']){$sign = true;}
			}
			$list[$k]['flow'] = $sign ;
		}
		//判断是否是审批人
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
		$this->role_id = $_SESSION['role_id'];
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
				$lirun_total = 0;$zhekou_total = 0;
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
						
						$lt = is_numeric(substr($val['lirun'],0,-1)) ? floatval(substr($val['lirun'],0,-1)) : 0;
						$lirun_total = $lirun_total > $lt ? $lirun_total : $lt ;
						$rate = $val['tax_rate'] == '无折扣' ? 10 : $val['tax_rate'] ;
						$zt = is_numeric($rate) ? floatval($rate) : 0;
						$zhekou_total = $zhekou_total > $zt ? $zhekou_total : $zt ;
					}
				}
				//添加流程
				if($lirun_total > 20 && $zhekou_total > 8){//利润大于8折...
					list($info['confirm'],$info['confirm_name']) = getPriceSheetFlow(session('role_id'),true);
				}else{
					if($lirun_total <= 20 && $zhekou_total <= 8){
						list($info['confirm'],$info['confirm_name']) = getPriceSheetFlow(session('role_id'),false,false);
					}else{
						list($info['confirm'],$info['confirm_name']) = getPriceSheetFlow(session('role_id'),false);
					}
				}
				$info['id'] = $sid;
				$info['is_again'] = $info['confirm'];
				if($sheet -> save($info)){
					//加上流程日志
					$confirm_array = array_filter(explode('|',$info['confirm']));
					if(!empty($confirm_array[0])){
						$flow_data['price_flow_id'] = $sid;
						$flow_data['emp_no'] = $confirm_array[0];
						$flow_data['user_id'] = M('User')->where(array('name'=>$confirm_array[0]))->getField('user_id');
						$flow_data['role_id'] = M('User')->where(array('name'=>$confirm_array[0]))->getField('role_id');
						$flow_data['user_name'] = M('User')->where(array('name'=>$confirm_array[0]))->getField('true_name');
						$flow_data['step'] = '21';
						$flow_data['result'] = '-1';//-1表示未审核
						$flow_data['create_time'] = time();
						$flow_data['is_read'] = 0;
						M('PriceSheetFlowLog')->add($flow_data);
						//发站内信，通过审核
						$content = '<a href="'.U('priceSheet/view','id='.$sid).'">有一个报价单需要您审批，点击查看</a>';
						sendMessage($flow_data['role_id'],$content,1);
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
			$flow =  M('PriceSheetFlowLog') -> where(array('price_flow_id'=>$list['id'],'result'=>'-1'))->field('id,role_id') -> find();
			$list['flow'] = $flow;
			$role_id = session('role_id');
			$flag = false;
			if(($list['approve_status']== '0') && !empty($flow)){
				if(strpos($flow['role_id'],',')){//多人审批
					$emps = array_filter(explode(',',$flow['role_id']));
					foreach ($emps as $k => $v){
						if($v == $role_id){
							$flag = true;break;
						}
					}
				}else{//单人审批
					if($role_id == $flow['role_id']){
						$flag = true;
					}
				}
			}
			$list['isflow'] = $flag;
		}
		$this-> vo = $list;
		//审批流程日志
		$log = M("PriceSheetFlowLog");
		$flowLog = $log -> where(array('price_flow_id'=>$id,'result'=>array('neq','-1'))) -> order("step DESC") -> select();
		$lastFlowLog = $log -> where(array('price_flow_id'=>$id)) -> order("step DESC") -> find();
		foreach ($flowLog as $k => $v){
			$position_name = D('UserView')->where(array('user_id'=>$v['user_id']))->getField('role_name');
			if($k == 0 && $v['result'] == '1' && $lastFlowLog['id'] == $v['id']){
				$flowLog[$k]['title'] = $position_name .'归档'; 
			}else{
				$flowLog[$k]['title'] = $position_name .'审批';
			}
		}
		$this -> flowlog = $flowLog;
		//添加流程头部信息
		$flow_log_should = array_filter(explode('|',$list['is_again']));
		$flow_log_should_name = array_filter(explode('<>',$list['confirm_name']));
		$flow_log_last = M('PriceSheetFlowLog')->where(array('price_flow_id'=>$id))->order('step desc')->limit(1)->find();
		$user_creator = D('UserView') -> where('role_id = '.$list['role_id'])->find();
		if($flow_log_last['result'] === '1'){//通过
			$flowinfo[] = array('color'=>'green','class'=>'li1','title'=>'申请人','name'=>$user_creator['true_name'],'role_id'=>$list['role_id'],'department_name'=>$user_creator['department_name'],'position_name'=>$user_creator['role_name']);
			$flowinfo = $this -> toolFlowLog($list,1);
		}elseif($flow_log_last['result'] === '0'){//拒绝
			$flowinfo[] = array('color'=>'orange','class'=>'li2','title'=>'申请人','name'=>$user_creator['true_name'],'role_id'=>$info['creator_role_id'],'department_name'=>$user_creator['department_name'],'position_name'=>$user_creator['role_name']);
			$flowinfo = $this -> toolFlowLog($list,3);
		}else{//默认
			$flowinfo[] = array('color'=>'green','class'=>'li1','title'=>'申请人','name'=>$user_creator['true_name'],'role_id'=>$info['creator_role_id'],'department_name'=>$user_creator['department_name'],'position_name'=>$user_creator['role_name']);
			foreach ($flow_log_should as $k=>$v){
				if(strpos($v,',') === false){//单人审批
					$user = D('UserView')->where(array('name'=>$v))->find();
					if($v == $flow_log_last['emp_no'] && $k == $flow_log_last['step']-21){
						$flowinfo[] = array('color'=>'orange','class'=>'li2','name'=>$flow_log_should_name[$k],'emp_no'=>$v,'role_id'=>$user['role_id'],'department_name'=>$user['department_name'],'position_name'=>$user['role_name']);
					}else{
						$flowinfo[] = array('color'=>'green','class'=>'li1','name'=>$flow_log_should_name[$k],'emp_no'=>$v,'role_id'=>$user['role_id'],'department_name'=>$user['department_name'],'position_name'=>$user['role_name']);
					}
				}else{//多人审批
					$emc = array_filter(explode(',',$v));
					$emcnm = array_filter(explode(',',$flow_log_should_name[$k]));
					foreach ($emc as $kk => $vv){
						$user = D('UserView')->where(array('name'=>$vv))->find();
						$role_ids .= $user['role_id'].',';
						$role_names .= $user['role_name'].',';
						$department_names .= $user['department_name'].',';
						$emp_no .= $vv.',';
						$name .= $emcnm[$kk].',';
					}
					if($v == $flow_log_last['emp_no'] && $k == $flow_log_last['step']-21){
						$flowinfo[] = array('color'=>'orange','class'=>'li2','name'=>rtrim($name,','),'emp_no'=>$emp_no,'role_id'=>$role_ids,'department_name'=>rtrim($department_names,','),'position_name'=>rtrim($role_names,','));
					}else{
						$flowinfo[] = array('color'=>'green','class'=>'li1','name'=>rtrim($name,','),'emp_no'=>$emp_no,'role_id'=>$role_ids,'department_name'=>rtrim($department_names,','),'position_name'=>rtrim($role_names,','));
					}
					}
				}
			}
		$this -> flow_all = $flowinfo;
		$this -> display();
	}
	/**
	 * 作废报价单
	 */
	public function del(){
		$id = $_GET['id'];
		$flag = M('PriceSheet')->save(array('id'=>$id,'is_del'=>1,'update_time'=>time()));
		alert('success', '作废成功!', U('priceSheet/index'));
	}
	/**
	 * 审批流程操作
	 */
	public function flowlog(){
			$data['price_flow_id'] = $_REQUEST['id'];
			$data['id'] = $_REQUEST['fid'];
			$data['result'] = $_REQUEST['re'];
			$data['comment'] = $_REQUEST['comm'];
			$data['update_time'] = time();
			$log = M('PriceSheetFlowLog');
			$log->startTrans(); //开启事物
			$tmp = $log ->save($data);
			$flag = true;
			//加上流程日志
			$confirm = M('PriceSheet') -> find($data['price_flow_id']);
			if($data['result'] == '1'){//同意
				$confirm_array = array_filter(explode('|',$confirm['confirm']));
				$flow_log = $log -> where('id = '.$data['id'])->find();
				if(strpos($flow_log['emp_no'],',')){
					$userinfo['emp_no'] = $_SESSION['name'];
					$userinfo['user_id'] = $_SESSION['user_id'];
					$userinfo['role_id'] = $_SESSION['role_id'];
					$userinfo['user_name'] = $_SESSION['true_name'];
					$userinfo['id'] = $flow_log['id'];
					$log -> save($userinfo);
				}
				$i = array_search($flow_log['emp_no'],$confirm_array);
				if($i < count($confirm_array)-1){//下一步
					$next_emp_no = $confirm_array[$i+1];
					$next_data['price_flow_id'] = $data['price_flow_id'];
					$next_data['emp_no'] = $next_emp_no;
					$last_step = $log->where(array('price_flow_id'=>$data['price_flow_id']))->order('step desc')->limit(1)->getField('step');
					$next_data['step'] = $last_step?$last_step+1:21;
					$next_data['result'] = '-1';
					$next_data['create_time'] = time();
					if(strpos($next_emp_no,',') === false){//单人审批
						$next_data['user_id'] = M('User')->where(array('name'=>$next_emp_no))->getField('user_id');
						$next_data['role_id'] = M('User')->where(array('name'=>$next_emp_no))->getField('role_id');
						$next_data['user_name'] = M('User')->where(array('name'=>$next_emp_no))->getField('true_name');
						//发站内信，通过审核
						$content = '<a href="'.U('priceSheet/view','id='.$confirm['id']).'">有一个报价单需要您审批，点击查看</a>';
						sendMessage($next_data['role_id'],$content,1);
					}else{//多人审批
						//当轮到多人审批的时候去除掉重复审批人
						$k = array_search($flow_log['emp_no'],$confirm_array);
						unset($confirm_array[$k]);
						$arr = array_values($confirm_array);
						$confirm['confirm'] = implode('|',$arr);
						M('PriceSheet') -> save($confirm);
						$emps = array_filter(explode(',',$next_emp_no));
						foreach ($emps as $k => $v){
							$user = M('User')->where(array('name'=>$v))->field('user_id,role_id,true_name')->find();
							$next_data['user_id'] .= $user['user_id'].',';
							$next_data['role_id'] .= $user['role_id'].',';
							$next_data['user_name'] .= $user['true_name'].',';
							//发站内信，通过审核
							$content = '<a href="'.U('priceSheet/view','id='.$confirm['id']).'">有一个报价单需要您审批，点击查看</a>';
							sendMessage($user['role_id'],$content,1);
						}
					}
					$flag =$log -> add($next_data);
				}else{//最后一人审批
					$confirm['approve_status'] = 1;
					$flag = M('PriceSheet') -> save($confirm);
					//发站内信，通过审核
					$content = '<a href="'.U('priceSheet/index','id='.$confirm['id']).'">您的报价单已通过审核，点击查看</a>';
					sendMessage($confirm['role_id'],$content,1);
				}
			}else{//拒绝
				$flag = M('PriceSheet') -> save(array('id'=>$data['price_flow_id'],'approve_status'=>-1));
				//发站内信，通过拒绝
				$content = '<a href="'.U('priceSheet/index','id='.$confirm['id']).'">您有一个报价单没有通过审核，点击查看</a>';
				sendMessage($confirm['role_id'],$content,1);
			}
			if($flag && $tmp){
				$log -> commit();
				$this -> ajaxReturn('1');
			}else{
				$log -> rollback();
				$this -> ajaxReturn('0');
			}
	}
	
	/**
	 * 组装流程日志信息
	 */
	private function toolFlowLog($list,$i){
		$flow_all = array();
		$flow_log_should = array_filter(explode('|',$list['is_again']));
		$flow_log_should_name = array_filter(explode('<>',$list['confirm_name']));
		foreach ($flow_log_should as $k=>$v){
			if(strpos($v,',') === false){//单人审批
				$user = D('UserView')->where(array('name'=>$v))->find();
				$flow_all[] = array('color'=>'green','class'=>"li$i",'name'=>$flow_log_should_name[$k],'emp_no'=>$v,'role_id'=>$user['role_id'],'department_name'=>$user['department_name'],'position_name'=>$user['role_name']);
			}else{//多人审批
				$emc = array_filter(explode(',',$v));
				$emcnm = array_filter(explode(',',$flow_log_should_name[$k]));
				foreach ($emc as $kk => $vv){
					$user = D('UserView')->where(array('name'=>$vv))->find();
					$role_ids .= $user['role_id'].',';
					$role_names .= $user['role_name'].',';
					$department_names .= $user['department_name'].',';
					$emp_no .= $vv.',';
					$name .= $emcnm[$kk].',';
				}
				$flow_all[] = array('color'=>'green','class'=>"li$i",'name'=>rtrim($name,','),'emp_no'=>rtrim($emp_no,','),'role_id'=>rtrim($role_ids,','),'department_name'=>rtrim($department_names,','),'position_name'=>rtrim($role_names,','));
			}
		}
		return $flow_all;
	}
}