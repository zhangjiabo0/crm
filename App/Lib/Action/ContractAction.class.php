<?php 
class ContractAction extends CommonAction {
	public function _initialize(){
		$action = array(
			'permission'=>array(),
			'allow'=>array('changecontent','listdialog','getcontractlist','upload','del_file','mark','cancel')
		);
		B('Authenticate', $action);
	}
	public function add(){
		$widget['date'] = true;
		$widget['uploader'] = true;
		$widget['editor'] = true;
		$this -> assign("widget", $widget);
		
		$contract_custom = M('config') -> where('name="contract_custom"')->getField('value');
		if(!$contract_custom)  $contract_custom = 'FWHT';
		if($this->isPost()){
			$contract = M('contract');
			if(!$_POST['number'])	alert('error', L('CONTRACT_NO_EMPTY'), $_SERVER['HTTP_REFERER']);
			else $data['number'] = trim($_POST['number']);
			$data['due_time'] = $_POST['due_time']?strtotime($_POST['due_time']):time();
			$data['price_sheet_id'] = intval($_POST['price_sheet_id']);
			if(empty($data['price_sheet_id'])){
				alert('error',L('PLEASE_SELECT_A_PRICE_SHEET_OPPORTUNITY'),$_SERVER['HTTP_REFERER']);
			}
// 			$data['customer_id'] = intval($_POST['customer_id']);
			$data['owner_role_id'] = $_POST['owner_role_id']?$_POST['owner_role_id']:session('role_id');
			$data['creator_role_id'] = session('role_id');
			$data['dept_name'] = $_POST['dept_name'];
			$data['price'] = intval($_POST['price']);
			$data['add_file'] = trim($_POST['add_file']);
			$data['description'] = trim($_POST['description']);
			$data['start_date'] = strtotime($_POST['start_date']);
			$data['end_date'] = strtotime($_POST['end_date']);
			$data['create_time'] = time();
			$data['update_time'] = time();
			$data['status'] = L('HAS_BEEN_CREATED');
			list($data['confirm'],$data['confirm_name']) = getContractFlow(session('role_id'));
			
			if($contractId = $contract->add($data)){
				//在price_sheet中加上标识表示已转服务合同
				M('PriceSheet')->where(array('id'=>intval($_POST['price_sheet_id'])))->save(array('t_service'=>1));
				
				//加上流程
				$confirm_array = array_filter(explode('|',$data['confirm']));
				if(!empty($confirm_array[0])){
					$flow_data['contract_flow_id'] = $contractId;
					$flow_data['emp_no'] = $confirm_array[0];
					$flow_data['user_id'] = M('User')->where(array('name'=>$confirm_array[0]))->getField('user_id');
					$flow_data['role_id'] = M('User')->where(array('name'=>$confirm_array[0]))->getField('role_id');
					$flow_data['user_name'] = M('User')->where(array('name'=>$confirm_array[0]))->getField('true_name');
					$flow_data['step'] = '21';
					$flow_data['create_time'] = time();
					$flow_data['is_read'] = 0;
					M('ContractFlowLog')->add($flow_data);
				}
// 				M('RBusinessContract')->add(array('contract_id'=>$contractId,'business_id'=>$data['business_id']));
				actionLog($contractId);
				if($_POST['refer_url']){
					alert('success', L('CREATE_A_CONTRACT_SUCCESSFULLY'), $_POST['refer_url']);
				}else{
					alert('success', L('CREATE_A_CONTRACT_SUCCESSFULLY'), U('contract/index'));
				}
			}else{
				alert('error', L('FAILED_TO_CREATE_THE_CONTRACT'), U('contract/add?price_sheet_id='.intval($_POST['price_sheet_id'])));
			}
		}else{
			if(intval($_GET['price_sheet_id'])){
				$this->assign('price_sheet_id',intval($_GET['price_sheet_id']));
				$price_sheet = D('PriceSheetView')->where(array('id'=>intval($_GET['price_sheet_id']),'is_del'=>0))->find();
				$product = M('RPriceSheetProduct')->where(array('sheet_id'=>intval($_GET['price_sheet_id'])))->select();
				
				$this->assign('price_sheet', $price_sheet);
				$this->assign('product', $product);
				
				$last_number = M('contract')->where(array('number'=>array('like',$contract_custom.date('Ymd').'%')))->order('number desc')->limit(1)->getField('number');
				if($last_number){
					$num = intval(substr($last_number,-4));
					$num_str = formatto4w($num+1);
				}else{
					$num_str = formatto4w(1);
				}
				$this->assign('contract_custom', $contract_custom.date('Ymd').$num_str);
				$this->alert = parseAlert();
				$this->refer_url = $_SERVER['HTTP_REFERER'];
				$this->display();
			}else{
				alert('error', L('PLEASE_CREATE_FROM_PRICESHEET'), U('priceSheet/index'));
				
// 				$this->assign('contract_custom', $contract_custom.date('Ymd').rand(1000,9999));
// 				$this->refer_url=$_SERVER['HTTP_REFERER'];
// 				$this->alert = parseAlert();
// 				$this->display();
			}
		}
	}
	
	public function edit(){
		$widget['date'] = true;
		$widget['uploader'] = true;
		$widget['editor'] = true;
		$this -> assign("widget", $widget);
		
		
		$contract = D('ContractView');
		$contract_id = intval($_REQUEST['id']) ? intval($_REQUEST['id']) : alert('error', L('PARAMETER_ERROR'),$_SERVER['HTTP_REFERER']);
		
		//默认不能审核
		$this -> assign("can_confirm", false);
		
		$contract_info = $contract->where('contract.contract_id = %d',$contract_id)->find();
		//检验是否可以审核
		$ContractFlowLogLast = M('ContractFlowLog')->where(array('contract_flow_id'=>$contract_id))->order('step desc')->limit(1)->find();
		if($_GET['type'] == 'confirm'){
			$flow_log = M('ContractFlowLog')->where(array('role_id'=>session('role_id'),'contract_flow_id'=>$contract_id,'_string'=>'result is null'))->find();
			if(empty($flow_log)){
				$this->error(L('HAVE NOT PRIVILEGES'));
			}else{
				$this -> assign("can_confirm", true);
			}
		}else{
			if(!check_permission($contract_id, 'contract')) $this->error(L('HAVE NOT PRIVILEGES'));
			
			if(!($ContractFlowLogLast['result'] === '0' && $contract_info['owner_role_id'] == session('role_id'))){
				$this->error(L('HAVE NOT PRIVILEGES'));
			}
		}
		if($contract_info['is_cancel'] == '1'){
			$contract_info['status'] = '已作废';
		}else{
			if($ContractFlowLogLast['result'] === '0'){
				$contract_info['status'] = '否决';
			}else if($ContractFlowLogLast['result'] === '1'){
				$contract_info['status'] = '通过';
			}else if(!empty($ContractFlowLogLast)){
				$contract_info['status'] = '审批中';
			}
		}
		
		$product = M('RPriceSheetProduct')->where(array('sheet_id'=>$contract_info['price_sheet_id']))->select();
		$this->assign('price_sheet_id',$contract_info['price_sheet_id']);
		if (is_array($contract_info)) {
			if($_POST['submit']){
				$data['due_time'] = $_POST['due_time']?strtotime($_POST['due_time']):time();
				$data['price_sheet_id'] = $_POST['price_sheet_id']?$_POST['price_sheet_id']:alert('error',L('PLEASE_SELECT_A_PRICE_SHEET_OPPORTUNITY'),$_SERVER['HTTP_REFERER']);
				$data['owner_role_id'] = $_POST['owner_role_id']?$_POST['owner_role_id']:session('role_id');
				$data['price'] = intval($_POST['price']);
				$data['dept_name'] = $_POST['dept_name'];
				$data['description'] = trim($_POST['description']);
				$data['start_date'] = strtotime($_POST['start_date']);
				$data['end_date'] = strtotime($_POST['end_date']);
				$data['update_time'] = time();
				$data['status'] = $_POST['status'];
				list($data['confirm'],$data['confirm_name']) = getContractFlow(session('role_id'));
				
				if(M('contract')->where(array('contract_id'=>$contract_id))->save($data)){
					$confirm_array = array_filter(explode('|',$data['confirm']));
					if(!empty($confirm_array[0])){
						$flow_data['contract_flow_id'] = $contract_id;
						$flow_data['emp_no'] = $confirm_array[0];
						$flow_data['user_id'] = M('User')->where(array('name'=>$confirm_array[0]))->getField('user_id');
						$flow_data['role_id'] = M('User')->where(array('name'=>$confirm_array[0]))->getField('role_id');
						$flow_data['user_name'] = M('User')->where(array('name'=>$confirm_array[0]))->getField('true_name');
						$last_step = M('ContractFlowLog')->where(array('contract_flow_id'=>$contract_id))->order('step desc')->limit(1)->getField('step');
						$flow_data['step'] = $last_step?$last_step+1:21;
						$flow_data['create_time'] = time();
						$flow_data['is_read'] = 0;
						M('ContractFlowLog')->add($flow_data);
					}
// 					M('rBusinessContract')->where(array('contract_id'=>$contract_id))->save(array('business_id'=>$data['business_id']));
					alert('success', L('MODIFY_THE_SUCCESS'),U('contract/view','id='.$contract_id));
				}else{
					alert('success', L('THERE_WERE_NO_CHANGES_IN_DATA'),$_SERVER['HTTP_REFERER']);
				}
			}else{
				$this->assign('info',$contract_info);
				$this->assign('product',$product);
				$this->alert = parseAlert();
				$this->display();
			}
		}else{
			alert('error', L('THERE_IS_NO_DATA'),$_SERVER['HTTP_REFERER']);
		}
	}
	public function mark(){
		$contract_id = $_POST['contract_id'];
		$type = $_POST['type'];
		$comment = $_POST['comment'];
		$role_id = session('role_id');
		//判断是否可以审核
		$ContractFlowLog = M('ContractFlowLog')->where(array('role_id'=>$role_id,'contract_flow_id'=>$contract_id,'_string'=>'result is null'))->find();
		$is_cancel = M('Contract')->where(array('contract_id'=>$contract_id))->getField('is_cancel');
		if(empty($ContractFlowLog) || $is_cancel == 1){
			$this->ajaxReturn('0', L('HAVE NOT PRIVILEGES'), 0);
		}
// 		$data['contract_flow_id'] = $contract_id;
// 		$data['emp_no'] = M('User')->where(array('role_id'=>$role_id))->getField('name');
// 		$data['role_id'] = $role_id;
// 		$data['user_id'] = M('User')->where(array('role_id'=>$role_id))->getField('user_id');
// 		$data['user_name'] = M('User')->where(array('role_id'=>$role_id))->getField('true_name');
		
		$data['update_time'] = time();
		$data['comment'] = $comment;
		
		if($type == 'approve'){
			$data['result'] = 1;
			$res = M('ContractFlowLog')->where(array('id'=>$ContractFlowLog['id']))->save($data);
			if($res){
				//下一步
				$confirm = M('Contract')->where(array('contract_id'=>$contract_id))->getField('confirm');
				$confirm = array_filter(explode('|',$confirm));
				$emp_no = M('User')->where(array('role_id'=>$role_id))->getField('name');
				$i = array_search($emp_no,$confirm);
				if($i<count($confirm)-1){
					$next_emp_no = $confirm[$i+1];
					$next_data['contract_flow_id'] = $contract_id;
					$next_data['emp_no'] = $next_emp_no;
					$next_data['user_id'] = M('User')->where(array('name'=>$next_emp_no))->getField('user_id');
					$next_data['role_id'] = M('User')->where(array('name'=>$next_emp_no))->getField('role_id');
					$next_data['user_name'] = M('User')->where(array('name'=>$next_emp_no))->getField('true_name');
					$last_step = M('ContractFlowLog')->where(array('contract_flow_id'=>$contract_id))->order('step desc')->limit(1)->getField('step');
					$next_data['step'] = $last_step?$last_step+1:21;
					$next_data['create_time'] = time();
					M('ContractFlowLog')->add($next_data);
				}else{
					//发站内信，通过审核
					$owner_role_id = M('Contract')->where(array('contract_id'=>$contract_id))->getField('owner_role_id');
					$content = '<a href="'.U('contract/index').'">您的服务合同已通过审核，点击查看</a>';
					sendMessage($owner_role_id,$content,1);
				}
			}else{
				$this->ajaxReturn('0', L('CONFIRM FAILED'), 0);
			}
		}elseif ($type == 'reject'){
			$data['result'] = 0;
			$res = M('ContractFlowLog')->where(array('id'=>$ContractFlowLog['id']))->save($data);
			if(!$res){
				$this->ajaxReturn('0', L('CONFIRM FAILED'), 0);
			}else{
				//发站内信，否决审核
				$owner_role_id = M('Contract')->where(array('contract_id'=>$contract_id))->getField('owner_role_id');
				$content = '<a href="'.U('contract/index').'">您的服务合同已被否决，点击查看</a>';
				sendMessage($owner_role_id,$content,1);
			}
		}
		$this->ajaxReturn('1', L('CONFIRM SUCCESS'), 1);
	}
	public function cancel(){
		$contract_id = $_REQUEST['id'];
		$contract = M('Contract')->where(array('contract_id'=>$contract_id))->find();
		if(empty($contract) || $contract['owner_role_id']!=session('role_id') || $contract['is_cancel'] == 1){
			$this->error(L('CAN NOT CANCEL'));
		}
		$res = M('Contract')->where(array('contract_id'=>$contract_id))->save(array('is_cancel'=>1,'cancel_time'=>time()));
		if($res){
			alert('success', L('CANCEL SUCCESS'),U('contract/index'));
		}else{
			alert('error', L('CANCEL FAILED'),U('contract/index'));
		}
	}
	public function view(){
		
		$contract_id = intval($_REQUEST['id']);
// 		if(!check_permission($contract_id, 'contract')) $this->error(L('HAVE NOT PRIVILEGES'));
		$contract = D('ContractView');
		if (0 == $contract_id) alert('error', L('NOT CHOOSE ANY'), U('contract/index'));
		
		$info = $contract->where(array('contract_id'=>$contract_id))->find();
		
		$all_ids = getSubRoleIdByYuan(true);
		if(!in_array($info['owner_role_id'],$all_ids)) $this->error(L('HAVE NOT PRIVILEGES'));
		if(empty($info)) alert('error', L('THE_CONTRACT_DOES_NOT_EXIST_OR_HAS_BEEN_DELETED'), U('contract/index'));
		$info['creator_name'] = M('user')->where('role_id = %d', $info['creator_role_id'])->getField('name');
		//检验是否可以编辑
		$ContractFlowLogLast = M('ContractFlowLog')->where(array('contract_flow_id'=>$contract_id))->order('step desc')->limit(1)->find();
		if($ContractFlowLogLast['result'] === '0' && $info['owner_role_id'] == session('role_id')){
			$this->assign('can_edit',true);
		}else{
			$this->assign('can_edit',false);
		}

		if($info['is_cancel'] == '1'){
			$info['status'] = '已作废';
		}else{
			if($ContractEpibolyFlowLogLast['result'] === '0'){
				$info['status'] = '否决';
			}else if($ContractEpibolyFlowLogLast['result'] === '1'){
				$info['status'] = '通过';
			}else if(!empty($ContractEpibolyFlowLogLast)){
				$info['status'] = '审批中';
			}
		}
		
		//已走流程
		$flow_log_already = M('ContractFlowLog')->where(array('contract_flow_id'=>$contract_id,'_string'=>'result is not null'))->order('step desc')->select();
		$this->assign('flow_log_already',$flow_log_already);
// 		dump($flow_log_already);
// 		$flow_log = M('ContractFlowLog')->where(array('contract_flow_id'=>$contract_id))->order('step asc')->select();
		//已走的最后一步
		$flow_log_last = M('ContractFlowLog')->where(array('contract_flow_id'=>$contract_id))->order('step desc')->limit(1)->find();
		//全部流程
		$flow_all = array();
		$flow_log_should = array_filter(explode('|',$info['confirm']));
		$flow_log_should_name = array_filter(explode('<>',$info['confirm_name']));
		
		$user_creator = D('UserView')->where(array('role_id'=>$info['creator_role_id']))->find();
		if($flow_log_last['result'] === '1'){
			$flow_all[] = array('color'=>'green','class'=>'li1','title'=>'申请人','name'=>$user_creator['true_name'],'role_id'=>$info['creator_role_id'],'department_name'=>$user_creator['department_name'],'position_name'=>$user_creator['role_name']);
			foreach ($flow_log_should as $k=>$v){
				$user = D('UserView')->where(array('name'=>$v))->find();
				$flow_all[] = array('color'=>'green','class'=>'li1','name'=>$flow_log_should_name[$k],'emp_no'=>$v,'role_id'=>$user['role_id'],'department_name'=>$user['department_name'],'position_name'=>$user['role_name']);
			}
		}elseif($flow_log_last['result'] === '0'){
			$flow_all[] = array('color'=>'orange','class'=>'li2','title'=>'申请人','name'=>$user_creator['true_name'],'role_id'=>$info['creator_role_id'],'department_name'=>$user_creator['department_name'],'position_name'=>$user_creator['role_name']);
			foreach ($flow_log_should as $k=>$v){
				$user = D('UserView')->where(array('name'=>$v))->find();
				$flow_all[] = array('color'=>'gray','class'=>'li3','name'=>$flow_log_should_name[$k],'emp_no'=>$v,'role_id'=>$user['role_id'],'department_name'=>$user['department_name'],'position_name'=>$user['role_name']);
			}
		}else{
			$flow_all[] = array('color'=>'green','class'=>'li1','title'=>'申请人','name'=>$user_creator['true_name'],'role_id'=>$info['creator_role_id'],'department_name'=>$user_creator['department_name'],'position_name'=>$user_creator['role_name']);
			foreach ($flow_log_should as $k=>$v){
				$user = D('UserView')->where(array('name'=>$v))->find();
				if($v == $flow_log_last['emp_no'] && $k == $flow_log_last['step']-21){
					$flow_all[] = array('color'=>'orange','class'=>'li2','name'=>$flow_log_should_name[$k],'emp_no'=>$v,'role_id'=>$user['role_id'],'department_name'=>$user['department_name'],'position_name'=>$user['role_name']);
				}else{
					$flow_all[] = array('color'=>'green','class'=>'li1','name'=>$flow_log_should_name[$k],'emp_no'=>$v,'role_id'=>$user['role_id'],'department_name'=>$user['department_name'],'position_name'=>$user['role_name']);
				}
			}
		}
		$this->assign('flow_all',$flow_all);
		
		$info['product'] = M('rPriceSheetProduct')->where('sheet_id = %d', $info['price_sheet_id'])->select();
		$product_count =  M('rPriceSheetProduct')->where('sheet_id = %d', $info['price_sheet_id'])->count();
		$info['product_count'] = empty($product_count)? 0 : $product_count;
		foreach ($info['product'] as $k => $v) {
			$m_product_category = M('productCategory');
			$product = M('product')->where('product_id = %d', $v['product_id'])->find();
			$info['product'][$k]['info'] = $product;
			$info['product'][$k]['category_name'] = $m_product_category->where('category_id = %d',$product['category_id'])->getField('name'); 
		}

		$info['receivables'] = D('ReceivablesView')->where('receivables.contract_id = %d and receivables.is_deleted=0', $contract_id)->select();
		foreach ($info['receivables'] as $k=>$v){
			$info['receivables'][$k]['owner'] = getUserByRoleId($v['owner_role_id']);
		}
		
		$receivables_count =  D('ReceivablesView')->where('receivables.contract_id = %d and receivables.is_deleted=0', $contract_id)->count();
		
		$info['payables'] = D('PayablesView')->where('payables.contract_id = %d and payables.is_deleted=0', $contract_id)->select();
		foreach ($info['payables'] as $k=>$v){
			$info['payables'][$k]['owner'] = getUserByRoleId($v['owner_role_id']);
		}
		$payables_count = count($info['payables']);
		$info['receivables_count'] = $receivables_count; 
		$info['payables_count'] = $payables_count;
		
		$file_ids = M('rContractFile')->where('contract_id = %d', $contract_id)->getField('file_id', true);
		$info['file'] = M('file')->where('file_id in (%s)', implode(',', $file_ids))->select();
		$file_count = 0;
		foreach ($info['file'] as $key=>$value) {
			$info['file'][$key]['owner'] = D('RoleView')->where('role.role_id = %d', $value['role_id'])->find();
			$file_count++;
		}
		$info['file_count'] = $file_count;
		
		$this->assign('info',$info);
		$this->alert = parseAlert();
		$this->display();

	}
	
	public function completeDelete(){
		$contract_id = is_array($_REQUEST['contract_id']) ? implode(',', $_REQUEST['contract_id']) : $_REQUEST['contract_id'];
		if ('' == $contract_id) {
			alert('error', L('NOT CHOOSE ANY'), $_SERVER['HTTP_REFERER']);
		} else {
			if(M('contract')->where('contract_id in (%s)', $contract_id)->delete()){
				M('rBusinessContract')->where(array('contract_id'=>$contract_id))->delete();
				M('rContractProduct')->where(array('contract_id'=>$contract_id))->delete();
				alert('success', L('DELETED SUCCESSFULLY'),U('contract/index','by=deleted'));
			} else {
				alert('error', L('DELETE FAILED'), $_SERVER['HTTP_REFERER']);
			}
		}
	}
	
	public function revert(){
		$contract_id = isset($_GET['id']) ? intval(trim($_GET['id'])) : 0;
		if ($contract_id > 0) {
			$m_contract = M('contract');
			$contract = $m_contract->where('contract_id = %d', $contract_id)->find();
			if (session('?admin') || $contract['delete_role_id'] == session('role_id')) {
				if ($m_contract->where('contract_id = %d', $contract_id)->setField('is_deleted', 0)) {
					alert('success', L('REDUCTION OF SUCCESS'), $_SERVER['HTTP_REFERER']);
				} else {
					alert('error', L('REDUCTION OF FAILED'), $_SERVER['HTTP_REFERER']);
				}
			} else {
				alert('error', L('YOU HAVE NO PERMISSION TO RESTORE'), $_SERVER['HTTP_REFERER']);
			}
		} else {
			alert('error', L('PARAMETER_ERROR'), $_SERVER['HTTP_REFERER']);
		}
	}
	
	public function delete(){
		$contract_ids = is_array($_REQUEST['contract_id']) ? $_REQUEST['contract_id'] : array($_REQUEST['contract_id']);
		if ('' == $contract_ids) {
			alert('error', L('NOT CHOOSE ANY'),$_SERVER['HTTP_REFERER']);
		} else {
			$m_contract = M('Contract');
			$m_receivables = M('Receivables');
			$m_payables = M('Payables');
			$m_r_contract_product = M('rContractProduct');
			$m_r_contract_file = M('rContractFile');
			//如果合同下有产品，财务和文件信息，提示先删除产品，财务和文件数据。
			$data = array('is_deleted'=>1, 'delete_role_id'=>session('role_id'), 'delete_time'=>time());
			foreach($contract_ids as $k=>$v){
				$contract = $m_contract->where('contract_id = %d',$v)->find();
				$contract_product = $m_r_contract_product->where('contract_id = %d',$v)->select();//合同关联的产品记录
				$contract_file = $m_r_contract_file->where('contract_id = %d',$v)->select();//合同关联的文件
				$contract_receivables = $m_receivables->where('is_deleted <> 1 and contract_id = %d',$v)->select();//合同关联的应收款
				$contract_payables = $m_payables->where('is_deleted <> 1 and contract_id = %d',$v)->select();//合同关联的应付款
				
				if(empty($contract_product) && empty($contract_file) && empty($contract_receivables) && empty($contract_payables)){
					if(!empty($contract['owner_role_id']) && $contract['owner_role_id'] != session('role_id')){
						alert('error',L('CAN NOT DELETE CONTRACT NOT ME'),$_SERVER['HTTP_REFERER']);
					}
					if(!$m_contract->where('contract_id = %d', $v)->save($data)){
						alert('error',L('NOT CHOOSE ANY'),$_SERVER['HTTP_REFERER']);
					}
				}else{
					if(!empty($contract_product)){
						alert('error', L('DELETE_FAILED_PLEASE_DELETE_UNDER_THE_CONTRACT_OF_PRODUCT_INFORMATION',array($contract['number'])),$_SERVER['HTTP_REFERER']);
					}elseif(!empty($contract_file)){
						alert('error', L('DELETE_FAILED_PLEASE_DELETE_UNDER_THE_CONTRACT_OF_PRODUCT_INFORMATION',array($contract['number'])),$_SERVER['HTTP_REFERER']);
					}elseif(!empty($contract_receivables)){
						alert('error', L('DELETE_FAILED_PLEASE_DELETE_RECEIVABLES_UNDER_THE_FINANCIAL_INFORMATION_IN_THE_CONTRACT',array($contract['number'])),$_SERVER['HTTP_REFERER']);
					}else{
						alert('error', L('DELETE_FAILED_PLEASE_DELETE_RECEIVABLES_UNDER_THE_FINANCIAL_INFORMATION_IN_THE_CONTRACT',array($contract['number'])),$_SERVER['HTTP_REFERER']);
					}
				}
			}
			alert('success',L('DELETED SUCCESSFULLY'),U('contract/index'));
		}
	}
	
	public function index(){
		//更新最后阅读时间
		$m_user = M('user');
		$last_read_time_js = $m_user->where('role_id = %d', session('role_id'))->getField('last_read_time');
		$last_read_time = json_decode($last_read_time_js, true);
		$last_read_time['contract'] = time();
		$m_user->where('role_id = %d', session('role_id'))->setField('last_read_time',json_encode($last_read_time));
		
		$contract = D('ContractView');
		$below_ids = getSubRoleId(false);
		$all_ids = getSubRoleIdByYuan(true);
		$where = array();
		$order = 'contract.create_time desc';
		if($_GET['desc_order']){
			$order = trim($_GET['desc_order']).' desc';
		}elseif($_GET['asc_order']){
			$order = trim($_GET['asc_order']).' asc';
		}
		switch ($_GET['by']){
			case 'create':
				$where['contract.creator_role_id'] = session('role_id');
				break;
			case 'sub' : 
				$where['contract.owner_role_id'] = array('in',implode(',', $below_ids)); 
				break;
			case 'subcreate' : 
				$where['contract.creator_role_id'] = array('in',implode(',', $below_ids)); 
				break;
			case 'today' : 
				$where['contract.due_time'] =  array('between',array(strtotime(date('Y-m-d')) -1 ,strtotime(date('Y-m-d')) + 86400)); 
				break;
			case 'week' : 
				$week = (date('w') == 0)?7:date('w');
				$where['contract.due_time'] =  array('between',array(strtotime(date('Y-m-d')) - ($week-1) * 86400 -1 ,strtotime(date('Y-m-d')) + (8-$week) * 86400)); 
				break;
			case 'month' : 
				$next_year = date('Y')+1;
				$next_month = date('m')+1;
				$month_time = date('m') ==12 ? strtotime($next_year.'-01-01') : strtotime(date('Y').'-'.$next_month.'-01');
				$where['contract.due_time'] = array('between',array(strtotime(date('Y-m-01')) -1 ,$month_time));
				break;
			case 'add' : 
				$order = 'contract.create_time desc'; 
				break;
			case 'deleted' : 
				$where['contract.is_deleted'] = 1; 
				break;
			case 'update' : 
				$order = 'contract.update_time desc'; 
				break;
			case 'me' :
				$where['contract.owner_role_id'] = session('role_id');
				break;
			default: 
				$where['contract.owner_role_id'] = array('in',implode(',', $all_ids)); 
				break;
		}
		
		if (!isset($where['contract.is_deleted'])) {
			$where['contract.is_deleted'] = 0;
		}
		if (!isset($where['contract.owner_role_id'])) {
			$where['contract.owner_role_id'] = array('in',implode(',', $all_ids)); 
		}
		
		if ($_REQUEST["field"]) {
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
		if($_GET['listrows']){
			$listrows = $_GET['listrows'];
			$params[] = "listrows=" . trim($_GET['listrows']);
		}else{
			$listrows = 15;
			$params[] = "listrows=15";
		}
		$p = intval($_GET['p'])?intval($_GET['p']):1;
		
		$list = $contract->where($where)->page($p.','.$listrows)->order($order)->select();
		$count = $contract->where($where)->count();
		
		import("@.ORG.Page");
		$Page = new Page($count,$listrows);
		if (!empty($_GET['by'])) {
			$params[] =   "by=".trim($_GET['by']);
		}
		
		$this->parameter = implode('&', $params);

		if ($_GET['desc_order']) {
			$params[] = "desc_order=" . trim($_GET['desc_order']);
		} elseif($_GET['asc_order']){
			$params[] = "asc_order=" . trim($_GET['asc_order']);
		}
		
		foreach ($list as $key=>$value) {
			$list[$key]['owner'] = getUserByRoleId($value['owner_role_id']);
			$list[$key]['creator'] = getUserByRoleId($value['creator_role_id']);
			$list[$key]['deletor'] = getUserByRoleId($value['delete_role_id']);
// 			$list[$key]['supplier_name'] = M('supplier')->where('supplier_id = %d',$value['supplier_id'])->getField('name');
// 			$contacts_id = M('Business')->where('business_id = %d',$value['business_id'])->getField('contacts_id');
// 			$list[$key]['contacts_name'] = M('contacts')->where('contacts_id = %d', $contacts_id)->getField('name');
			$end_date =  $contract->where('contract_id = %d', $value['contract_id'])->getField('end_date');
			if($end_date){
				$list[$key]['days'] = floor(($end_date-time())/86400+1);
			}
			$ContractFlowLogToMe = M('ContractFlowLog')->where(array('role_id'=>session('role_id'),'contract_flow_id'=>$value['contract_id'],'_string'=>'result is null'))->find();
			if($ContractFlowLogToMe && $value['is_cancel'] == 0){
				$list[$key]['confirm_to_me'] = true;
			}else{
				$list[$key]['confirm_to_me'] = false;
			}
			$ContractFlowLogLast = M('ContractFlowLog')->where(array('contract_flow_id'=>$value['contract_id']))->order('step desc')->limit(1)->find();
			if($ContractFlowLogLast['result'] === '0' && $value['owner_role_id'] == session('role_id')){
				$list[$key]['can_edit'] = true;
			}else{
				$list[$key]['can_edit'] = false;
			}
			if($value['is_cancel'] == '1'){
				$list[$key]['status'] = '已作废';
			}else{
				if($ContractFlowLogLast['result'] === '0'){
					$list[$key]['status'] = '否决';
				}else if($ContractFlowLogLast['result'] === '1'){
					$list[$key]['status'] = '通过';
				}else if(!empty($ContractFlowLogLast)){
					$list[$key]['status'] = '审批中';
				}
			}
			
			if($value['is_cancel'] == 0 && $value['owner_role_id'] == session('role_id')){
				$list[$key]['can_cancel'] = true;
			}else{
				$list[$key]['can_cancel'] = false;
			}
		}
		// println($list);
		$this->listrows = $listrows;
		$Page->parameter = implode('&', $params);
		$this->assign('page', $Page->show());
		$this->assign('list',$list);
		$this->alert = parseAlert();
		$this->display();
	}
	public function client_index(){
		//更新最后阅读时间
		$m_user = M('user');
		$last_read_time_js = $m_user->where('role_id = %d', session('role_id'))->getField('last_read_time');
		$last_read_time = json_decode($last_read_time_js, true);
		$last_read_time['contract'] = time();
		$m_user->where('role_id = %d', session('role_id'))->setField('last_read_time',json_encode($last_read_time));
	
		$contract = D('ContractView');
		$below_ids = getSubRoleId(false);
		$all_ids = getSubRoleId();
		$where = array();
		$order = 'contract.create_time desc';
		if($_GET['desc_order']){
			$order = trim($_GET['desc_order']).' desc';
		}elseif($_GET['asc_order']){
			$order = trim($_GET['asc_order']).' asc';
		}
		switch ($_GET['by']){
			case 'create':
				$where['contract.creator_role_id'] = session('role_id');
				break;
			case 'sub' :
				$where['contract.owner_role_id'] = array('in',implode(',', $below_ids));
				break;
			case 'subcreate' :
				$where['contract.creator_role_id'] = array('in',implode(',', $below_ids));
				break;
			case 'today' :
				$where['contract.due_time'] =  array('between',array(strtotime(date('Y-m-d')) -1 ,strtotime(date('Y-m-d')) + 86400));
				break;
			case 'week' :
				$week = (date('w') == 0)?7:date('w');
				$where['contract.due_time'] =  array('between',array(strtotime(date('Y-m-d')) - ($week-1) * 86400 -1 ,strtotime(date('Y-m-d')) + (8-$week) * 86400));
				break;
			case 'month' :
				$next_year = date('Y')+1;
				$next_month = date('m')+1;
				$month_time = date('m') ==12 ? strtotime($next_year.'-01-01') : strtotime(date('Y').'-'.$next_month.'-01');
				$where['contract.due_time'] = array('between',array(strtotime(date('Y-m-01')) -1 ,$month_time));
				break;
			case 'add' :
				$order = 'contract.create_time desc';
				break;
			case 'deleted' :
				$where['contract.is_deleted'] = 1;
				break;
			case 'update' :
				$order = 'contract.update_time desc';
				break;
			case 'me' :
				$where['contract.owner_role_id'] = session('role_id');
				break;
			default:
				$where['contract.owner_role_id'] = array('in',implode(',', $all_ids));
				break;
		}
	
		if (!isset($where['contract.is_deleted'])) {
			$where['contract.is_deleted'] = 0;
		}
		if (!isset($where['contract.owner_role_id'])) {
			$where['contract.owner_role_id'] = array('in',implode(',', getSubRoleId()));
		}
	
		if ($_REQUEST["field"]) {
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
		if($_GET['listrows']){
			$listrows = $_GET['listrows'];
			$params[] = "listrows=" . trim($_GET['listrows']);
		}else{
			$listrows = 15;
			$params[] = "listrows=15";
		}
		$p = intval($_GET['p'])?intval($_GET['p']):1;
		$list = $contract->where($where)->page($p.','.$listrows)->order($order)->select();
		$count = $contract->where($where)->count();
	
		import("@.ORG.Page");
		$Page = new Page($count,$listrows);
		if (!empty($_GET['by'])) {
			$params[] =   "by=".trim($_GET['by']);
		}
	
		$this->parameter = implode('&', $params);
	
		if ($_GET['desc_order']) {
			$params[] = "desc_order=" . trim($_GET['desc_order']);
		} elseif($_GET['asc_order']){
			$params[] = "asc_order=" . trim($_GET['asc_order']);
		}
	
		foreach ($list as $key=>$value) {
			$list[$key]['owner'] = getUserByRoleId($value['owner_role_id']);
			$list[$key]['creator'] = getUserByRoleId($value['creator_role_id']);
			$list[$key]['deletor'] = getUserByRoleId($value['delete_role_id']);
			$list[$key]['supplier_name'] = M('supplier')->where('supplier_id = %d',$value['supplier_id'])->getField('name');
			$contacts_id = M('Business')->where('business_id = %d',$value['business_id'])->getField('contacts_id');
			$list[$key]['contacts_name'] = M('contacts')->where('contacts_id = %d', $contacts_id)->getField('name');
			$end_date =  $contract->where('contract_id = %d', $value['contract_id'])->getField('end_date');
			if($end_date){
				$list[$key]['days'] = floor(($end_date-time())/86400+1);
			}
		}
		// println($list);
		$this->listrows = $listrows;
		$Page->parameter = implode('&', $params);
// 		$this->assign('page', $Page->show());
		$this->ajaxReturn(array('list'=>$list,'count'=>$count), '', 1);
// 		$this->assign('list',$list);
// 		$this->alert = parseAlert();
// 		$this->display();
	}
	
	public function changeContent(){
		if($this->isAjax()){
			$contract = D('ContractView');
			$where = array();
			
			$where['contract.is_deleted'] = 0;
			$where['contract.owner_role_id'] = array('in',implode(',', getSubRoleId())); 
			
			if ($_REQUEST["field"]) {
				if (trim($_REQUEST['field']) == "all") {
					$field = is_numeric(trim($_REQUEST['search'])) ? 'number|price|contract.description' : 'number|contract.description';
				} else {
					$field = trim($_REQUEST['field']);
				}
				$search = empty($_REQUEST['search']) ? '' : trim($_REQUEST['search']);
				$condition = empty($_REQUEST['condition']) ? 'is' : trim($_REQUEST['condition']);

				if	('create_time' == $field || 'update_time' == $field || 'due_date' == $field) {
					$search = is_numeric($search)?$search:strtotime($search);
				}
				switch ($condition) {
					case "is" : $where['contract.'.$field] = array('eq',$search);break;
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
					default : $where[$field] = array('eq',$search);
				}
			}
			
			$p = !$_REQUEST['p']||$_REQUEST['p']<=0 ? 1 : intval($_REQUEST['p']);
			$list = $contract->where($where)->page($p.',10')->order('contract.create_time desc')->select();
			$count = $contract->where($where)->count();
			foreach ($list as $key=>$value) {
				$list[$key]['owner'] = getUserByRoleId($value['owner_role_id']);
				$list[$key]['creator'] = getUserByRoleId($value['creator_role_id']);
				$list[$key]['deletor'] = getUserByRoleId($value['delete_role_id']);
			}
			$data['list'] = $list;
			$data['p'] = $p;
			$data['count'] = $count;
			$data['total'] = $count%10 > 0 ? ceil($count/10) : $count/10;
			$this->ajaxReturn($data,"",1);
		}
	}
	
	public function listDialog(){
		$below_ids = getSubRoleId(true);
		$contract = D('ContractView');
		
		$business_id = intval($_GET['bid']);
		if(!empty($business_id)){
			$where['business_id'] = array('eq',$business_id);
		}
		$where['contract.owner_role_id'] = array('in',implode(',', $below_ids));
		$where['contract.is_deleted'] = 0;
		$list = $contract->where($where)->page('0,10')->order('create_time desc')->select();
		$count = $contract->where($where)->count();
		$this->total = $count%10 > 0 ? ceil($count/10) : $count/10;
		$this->count_num = $count;
		$this->assign('contractList',$list);
		$this->display();
	}
	public function getcontractlist(){
		$contract = D('ContractView');
		$list = $contract->where(array('contract.is_deleted' => 0))->select();
		$this->ajaxReturn($list, '', 1);
	}
}