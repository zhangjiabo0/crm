<?php 
class ServiceAction extends Action {

	public function _initialize(){
		$action = array(
			'permission'=>array(),
			'allow'=>array('checklistdialog','getServiceList', 'revert', 'mdelete','radiolistdialog','changedialog','add_dialog','qrcode','changetofirstcontact')
		);
		B('Authenticate', $action);
	}
	
	public function add(){
		if ($_GET['r'] && $_GET['module'] && $_GET['id']) {
			$this -> r = $_GET['r'];
			$this -> module = $_GET['module'];
			$this -> id = $_GET['id'];
			$this->refer_url=$_SERVER['HTTP_REFERER'];
			$this->display('Service:add_dialog');
		}elseif($this->isPost()){
			$service_time = trim($_POST['service_time']);
			$customerB_id = trim($_POST['customerB_id']);
			if ($service_time == '' || $service_time == null) {
				$this -> error(L('SERVICE TIME CANNOT BE EMPTY'));
			}
			if ($customerB_id == '' || $customerB_id == null) {
				$this->error(L('SERVICE_CUSTOMERB_CANNOT_BE_EMPTY'));
			}
			$service = M('service');
			
			//自定义字段数据存入service_data表
			$field_list = M('Fields')->where('model = "service" and in_add = 1')->order('order_id')->select();
			foreach ($field_list as $v){
				switch($v['form_type']) {
					case 'address':
						$_POST[$v['field']] = implode(chr(10),$_POST[$v['field']]);
					break;
					case 'datetime':
						$_POST[$v['field']] = strtotime($_POST[$v['field']]);
					break;
					case 'box':
						eval('$field_type = '.$v['setting'].';');
						if($field_type['type'] == 'checkbox'){
							$a =array_filter($_POST[$v['field']]);
							$_POST[$v['field']] = !empty($a) ? implode(chr(10),$a) : '';
						}
					break;
				}
			}
			
			$service->create();
			$service->create_time = time();
			$service->update_time = time();
			$service->creator_role_id = session('role_id');
			if($service_id = $service->add()){
				if($_POST['customerB_id']){
					$rServiceCustomerB['service_id'] =  $service_id;
					$rServiceCustomerB['customerB_id'] =  $_POST['customerB_id'];
					M('rServiceCustomerB') ->add($rServiceCustomerB);
				}
				
				if($_POST['redirect'] == 'customerB'){
					//alert('success','添加成功!',U('customerB/view','id='.intval($_POST['redirect_id'])));
					if($_POST['refer_url']){
						alert('success', '添加服务记录成功', $_POST['refer_url']);
					}else{
						alert('success',L('ADD A SUCCESS'),U('service/view','id='.$service_id));
					}
				}else{
					if($_POST['submit'] == L('SAVE')){
						alert('success',L('ADD A SUCCESS'),U('service/index'));
					}else{
						alert('success',L('ADD A SUCCESS'),U('service/add'));
					}
					
				}
			}else{
				alert('error',L('ADD FAILURE'),$_SERVER['HTTP_REFERER']);
			}		
		}else{
			if($_GET['redirect']){
				$this->redirect_id = $_GET['redirect_id'];
				$this->redirect = $_GET['redirect'];
			}
			$customerB = M('customerB');
			$this->customerB_id =$customerB_id = $_GET['customerB_id'];
			$this->customerB_name =$customerB->where('customerB_id =%s',$customerB_id)->getField('name');
			$this->refer_url=$_SERVER['HTTP_REFERER'];
			$this->customerB = $customerB->where('customerB_id = %s', $_GET['redirect_id'])->find();
			$this->alert = parseAlert();
			$this->display();
		}
	}
	
	public function edit(){
		$m_service = M('service');
		$rServiceCustomerB = M('rServiceCustomerB');
		$service_id = $_GET['id'] ? intval($_GET['id']) : intval($_POST['service_id']);
		if(empty($service_id)){
			alert('error',L('PARAMETER_ERROR'),$_SERVER['HTTP_REFERER']);
		}
		$service = D('ServiceView')->where(array('service_id'=>$service_id))->find();
		if(empty($service)) alert('error', L('RECORD_NOT_EXIST_OR_HAVE_BEEN_DELETED',array(L('SERVICE'))),U('service/index'));
		//检查权限(联系人编辑权限跟随客户，如果可以编辑客户即可编辑联系人)
		$customerB_id = $rServiceCustomerB->where('service_id = %d', $service_id)->getField('customerB_id');
		if(!vali_permission('customerB','edit') || !check_permission($customerB_id, 'customerB')) $this->error(L('HAVE NOT PRIVILEGES'));
		
		if ($this->isPost()) {
			$m_service->create();
			$m_service->update_time = time();
			$service_time = trim($_POST['service_time']);
			if ($service_time == '' || $service_time == null) {
				alert('error',L('SERVICE TIME CANNOT BE EMPTY'),$_SERVER['HTTP_REFERER']);
			}
			if (!empty($_POST['customerB_id'])) {
				if (empty($customerB_id)) {
					$data['service_id'] = $_POST['service_id'];
					$data['customerB_id'] = $_POST['customerB_id'];
					$rServiceCustomerB ->where('service_id = %d', $_POST['service_id'])->delete();
					$rServiceCustomerB -> add($data);
				}elseif ($_POST['customerB_id'] != $customerB_id) {
					$rServiceCustomerB -> where('service_id = %d' , $_POST['service_id']) -> setField('customerB_id',$_POST['customerB_id']);
				}	
			}else{
				alert('error', L('NOT NULL',array(L('CUSTOMERB'))), $_SERVER['HTTP_REFERER']);
			}
			if ($m_service->save()) {
				alert('success',L('THE SERVICE INFORMATION OF SUCCESS'),U('service/view') . "&id=" . $_POST['service_id']);
			} else {
				alert('error',L('THE SERVICE INFORMATION CHANGE FAILED'),$_SERVER['HTTP_REFERER']);
			}
		}else{
			$this->service = $service;
			$this->alert = parseAlert();
			$this->display();
		}
	}
	
	public function view(){
		$service_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
		$rServiceCustomerB = M('rServiceCustomerB');
		$d_service = D('ServiceView');
		if (0 == $service_id) {
			alert('error', L('PARAMETER_ERROR'), U('service/index'));
		} else {
			//检查权限(联系人查看权限跟随客户，如果可以查看客户即可查看联系人)
			$customerB_id = $rServiceCustomerB->where('service_id = %d', $service_id)->getField('customerB_id');
			if(!vali_permission('customerB','view') || !check_permission($customerB_id, 'customerB')){
				$this->error(L('HAVE NOT PRIVILEGES'));
			}
			$service = D('ServiceView')->where('service.service_id = %d' , $service_id)->find();
			if(empty($service)){
				alert('error',L('RECORD_NOT_EXIST_OR_HAVE_BEEN_DELETED',array(L('SERVICE'))),U('service/index'));
			}
			$this->service = $service;		
			$this->alert = parseAlert();
			$this->display();
		}		
	}

	public function index(){
		$d_service = D('ServiceView');
		$p = isset($_GET['p']) ? intval($_GET['p']) : 1 ;
		$by = isset($_GET['by']) ? trim($_GET['by']) : '';
		$below_ids = getSubRoleId(false);
		$all_ids = getSubRoleIdByYuan(true);
		$where = array();
		$params = array();
		$order = "create_time desc";
		
		if($_GET['desc_order']){
			$order = trim($_GET['desc_order']).' desc';
		}elseif($_GET['asc_order']){
			$order = trim($_GET['asc_order']).' asc';
		}
		
		switch ($by) {
			case 'today' : $where['create_time'] =  array('gt',strtotime(date('Y-m-d', time()))); break;
			case 'week' : $where['create_time'] =  array('gt',(strtotime(date('Y-m-d', time())) - (date('N', time()) - 1) * 86400)); break;
			case 'month' : $where['create_time'] = array('gt',strtotime(date('Y-m-01', time()))); break;
			case 'add' : $order = 'create_time desc'; break;
			case 'update' : $order = 'update_time desc'; break;
			case 'deleted' : $where['is_deleted'] = 1; break;
			default : $where['creator_role_id'] = array('in',$all_ids); break;
		}
		if (!isset($where['creator_role_id'])) {
			$where['creator_role_id'] = array('in', $all_ids);
		}
		if (!isset($where['is_deleted'])) {
			$where['is_deleted'] = 0;
		}
		if ($_REQUEST["field"]) {
			$field = trim($_REQUEST['field']) == 'all' ? 'name|telephone|email|address|post|department|description' : $_REQUEST['field'];
			$search = empty($_REQUEST['search']) ? '' : trim($_REQUEST['search']);
			$condition = empty($_REQUEST['condition']) ? 'is' : trim($_REQUEST['condition']);
			if	('create_time' == $field || 'update_time' == $field) {
				$search = is_numeric($search)?$search:strtotime($search);
			}
			switch ($condition) {
				case "is" : $where[$field] = array('eq',$search);break;
				case "isnot" :  $where[$field] = array('neq',$search);break;
				case "contains" :  $where[$field] = array('like','%'.$search.'%');break;
				case "not_contain" :  $where[$field] = array('notlike','%'.$search.'%');break;
				case "start_with" :  $where[$field] = array('like',$search.'%');break;
				case "end_with" :  $where[$field] = array('like','%'.$search);break;
				case "is_empty" :  $where[$field] = array('eq','');break;
				case "is_not_empty" :  $where[$field] = array('neq','');break;
				case "gt" :  $where[$field] = array('gt',$search);break;
				case "egt" :  $where[$field] = array('egt',$search);break;
				case "lt" :  $where[$field] = array('lt',$search);break;
				case "elt" :  $where[$field] = array('elt',$search);break;
				case "eq" : $where[$field] = array('eq',$search);break;
				case "neq" : $where[$field] = array('neq',$search);break;
				case "between" : $where[$field] = array('between',array($search-1,$search+86400));break;
				case "nbetween" : $where[$field] = array('not between',array($search,$search+86399));break;
				case "tgt" :  $where[$field] = array('gt',$search+86400);break;
				default : $where[$field] = array('eq',$search);
			}
			$params = array('field='.$field, 'condition='.$condition, 'search='.$_REQUEST["search"]);
		}
		if(trim($_GET['act']) == 'excel'){
			if(vali_permission('service', 'export')){
				$order = $order ? $order : 'create_time desc';
				$serviceList = $d_service->where($where)->order($order)->select();		
				$this->excelExport($serviceList);
			}else{
				alert('error', L('HAVE NOT PRIVILEGES'), $_SERVER['HTTP_REFERER']);
			}
			
		}else{
			if($_GET['listrows']){
				$listrows = $_GET['listrows'];
				$params[] = "listrows=" . trim($_GET['listrows']);
			}else{
				$listrows = 15;
				$params[] = "listrows=15";
			}
				$serviceList = $d_service->where($where)->order($order)->page($p.','.$listrows)->select();
				$count = $d_service->where($where)->count();
				import("@.ORG.Page");
				$Page = new Page($count,$listrows);
				if (!empty($_GET['by'])) {
					$params[] = "by=".trim($_GET['by']);
				}
				
				$this->parameter = implode('&', $params);
			
				if ($_GET['desc_order']) {
					$params[] = "desc_order=" . trim($_GET['desc_order']);
				} elseif($_GET['asc_order']){
					$params[] = "asc_order=" . trim($_GET['asc_order']);
				}
				
				$Page->parameter = implode('&', $params);
				$this->assign('page',$Page->show());

			if($by == 'deleted') {
				foreach ($serviceList as $k => $v) {
					$serviceList[$k]["delete_role"] = getUserByRoleId($v['delete_role_id']);
					$serviceList[$k]["creator"] = getUserByRoleId($v['creator_role_id']);
				}
			}else{
				foreach ($serviceList as $k => $v) {		
					$serviceList[$k]["creator"] = getUserByRoleId($v['creator_role_id']);
				}
			}
			$this->listrows = $listrows;
			//获取下级和自己的岗位列表,搜索用
			$d_role_view = D('RoleView');
			$this->role_list = $d_role_view->where('role.role_id in (%s)', implode(',', $below_ids))->select();
			$this->assign('serviceList',$serviceList);
			$this->alert = parseAlert();
			$this->display();
		}
	}

	public function completeDelete(){
		$m_service = M('service');
		$rServiceCustomerB = M('rServiceCustomerB');
		//检查权限
		$all_ids = getSubRoleId();
		$customerB_idArr = M('customerB')->where(array('owner_role_id'=>array('in', $all_ids)))->getField('customerB_id', true);
		
		$r_module = array('File'=>'RServiceFile', 'Log'=>'RServiceLog', 'RServiceCustomerB', 'RServiceTask','RServiceEvent');
		if ($_POST['service_id']) {
			if (!session('?admin')) {
				foreach ($_POST['service_id'] as $value) {
					$customerB_id = $rServiceCustomerB->where('service_id = %d', $value)->getField('customerB_id');
					if(!in_array($customerB_id, $customerB_idArr)){
						alert('error', L('YOU DO NOT HAVE PERMISSION TO ALL'), $_SERVER['HTTP_REFERER']);
					}
				}
			}
			if ($m_service->where('service_id in (%s)', join($_POST['service_id'],','))->delete()) {
				foreach ($_POST['service_list'] as $value) {
					foreach ($r_module as $key2=>$value2) {
						$module_ids = M($value2)->where('service_id = %d', $value)->getField($key2 . '_id',true);
						M($value2)->where('service_id = %d', $value) -> delete();
						if(!is_int($key2)){
							M($key2)->where($key2 . '_id in (%s)', implode(',', $module_ids))->delete();
						}
					}
				}
				alert('success', L('DELETED SUCCESSFULLY'),U('service/index','by=deleted'));
			} else {
				alert('error',L('DELETE FAILED SERVICE THE ADMINISTRATOR'),$_SERVER['HTTP_REFERER']);
			}
		}elseif($_GET['id']){
			$service_id = intval($_GET['id']);
			$service = $m_service->where('service_id = %d', $service_id)->find();
			if (is_array($service)) {
				//检查权限
				$customerB_id = $rServiceCustomerB->where('service_id = %d', $service_id)->getField('customerB_id');
				if (session('?admin') || in_array($customerB_id, $customerB_idArr)) {
					if($m_service->where('service_id = %d', $service_id)->delete()){
						foreach ($r_module as $key2=>$value2) {
							if(!is_int($key2)){
								$module_ids = M($value2)->where('service_id = %d', $service_id)->getField($key2 . '_id',true);
								M($value2)->where('service_id = %d', $service_id)->delete();
								$m_key = M($key2);
								$m_key->where($key2 . '_id in (%s)', implode(',', $module_ids))->delete();
							}
						}
						alert('success', L('DELETED SUCCESSFULLY'),U('service/index','by=deleted'));
					} else {
						alert('error', L('DELETE FAILED'),$_SERVER['HTTP_REFERER']);
					}
				} else {
					alert('error', L('HAVE NOT PRIVILEGES'),$_SERVER['HTTP_REFERER']);
				}
			} else {
				alert('error', L('YOU WANT TO DELETE THE RECORD DOES NOT EXIST'), $_SERVER['HTTP_REFERER']);
			}
		}else{
			alert('error',L('PLEASE CHOOSE TO DELETE THE SERVICE'),$_SERVER['HTTP_REFERER']);
		}
	}
	
	public function delete(){
		$m_service = M('service');
		$rServiceCustomerB = M('rServiceCustomerB');
		//检查权限
		$all_ids = getSubRoleId();;
		$customerB_idArr = M('customerB')->where(array('owner_role_id'=>array('in', $all_ids)))->getField('customerB_id', true);
		if ($_POST['service_id']) {
			if (!session('?admin')) {
				foreach ($_POST['service_id'] as $value) {
					//检查权限
					$customerB_id = $rServiceCustomerB->where('service_id = %d', $value)->getField('customerB_id');
					if(!in_array($customerB_id, $customerB_idArr)){
						alert('error', L('YOU DO NOT HAVE PERMISSION TO ALL'), $_SERVER['HTTP_REFERER']);
					}
				}
			}
			$data = array('is_deleted'=>1, 'delete_role_id'=>session('role_id'), 'delete_time'=>time());
			if ($m_service->where('service_id in (%s)', implode(',', $_POST['service_id']))->setField($data)) {
				alert('success', L('DELETED SUCCESSFULLY'),U('service/index'));
			} else {
				echo $m_service->getLastSql(); die();
				alert('error', L('DELETE FAILED SERVICE THE ADMINISTRATOR'),$_SERVER['HTTP_REFERER']);
			}
		}elseif($_GET['id']){
			$service_id = intval($_GET['id']);
			//检查权限
			$customerB_id = $rServiceCustomerB->where('service_id = %d', $service_id)->getField('customerB_id');
			
			$service = $m_service->where('service_id = %d', $service_id)->find();
			if (is_array($service)) {
				if (session('?admin') || in_array($customerB_id, $customerB_idArr)) {
					$data = array('is_deleted'=>1, 'delete_role_id'=>session('role_id'), 'delete_time'=>time());
					if($m_service->where('service_id = %d', $service_id)->setField($data)){
						alert('success', L('DELETED SUCCESSFULLY'),U('service/index'));
					} else {
						alert('error', L('DELETE FAILED'),$_SERVER['HTTP_REFERER']);
					}
				} else {
					alert('error', L('HAVE NOT PRIVILEGES'),$_SERVER['HTTP_REFERER']);
				}
			} else {
				alert('error',L('YOU WANT TO DELETE THE RECORD DOES NOT EXIST'), $_SERVER['HTTP_REFERER']);
			}
		}else{
			alert('error',L('PLEASE CHOOSE TO DELETE THE SERVICE'),$_SERVER['HTTP_REFERER']);
		}
	}
	
	public function mDelete(){
		if($_GET['r'] && $_GET['id'] && $_GET['module_id']){
			$m_r = M($_GET['r']);
			if($m_r->where("service_id = %d and customerB_id", $_GET['id'], $_GET['module_id'])->delete()){
				M('CustomerB')->where("customerB_id", $_GET['module_id'])->setField('service_id', 0);
				alert('success',L('DELETED SUCCESSFULLY'),$_SERVER['HTTP_REFERER']);
			} else {
				alert('error',L('DELETE FAILED'),$_SERVER['HTTP_REFERER']);
			}
		} else {
			alert('error',L('PARAMETER_ERROR'),$_SERVER['HTTP_REFERER']);
		}
	}
	
	public function getServiceList(){
		$d_service = D('ServiceView');
		$idArray = getSubRoleId();
		//获取下级和自己的客户列表,搜索
		$serviceList = D('Service')->where(array('owner_role_id'=>array('in',$idArray),'is_deleted'=>array('eq', 0)))->select();
		$this->ajaxReturn($serviceList, '', 1);
	}
	public function checkListDialog(){
		if($this->isPost()){
			$r = $_POST['r'];
			$model_id = isset($_POST['id']) ? intval($_POST['id']) : 0;
			$m_r = M($r);
			$m_id = $_POST['module'] . '_id';  //对应模块的id字段
			$data[$m_id] = $model_id;
			foreach ($_POST['service_id'] as $value) {
				$data['service_id'] = $value;
				if ($m_r -> add($data) <= 0) {
					alert('error', L('SELECT THE SERVICE FAILURE'),$_SERVER['HTTP_REFERER']);
				}
			}
			alert('success', L('SELECT THE SERVICE SUCCESS'),$_SERVER['HTTP_REFERER']);
		}elseif ($_GET['r'] && $_GET['module'] && $_GET['id']) {
			$list = M($_GET['r']) -> getField('service_id', true);
			$m_service = M('Service');
			$underling_ids = getSubRoleId();
			$list[] = 0;
			$this->serviceList = $m_service->where('service_id not in (%s) and creator_role_id in (%s) and is_deleted <> 1', implode(',',$list), implode(',', $underling_ids))->order('create_time desc')->limit(10)->select();
			$count = $m_service->where('service_id not in (%s) and owner_role_id in (%s) and is_deleted = 0', implode(',',$list), implode(',', $underling_ids))->count();
			$this->total = $count%10 > 0 ? ceil($count/10) : $count/10;
			$this->count_num = $count;
			$this -> r = $_GET['r'];
			$this -> module = $_GET['module'];
			$this -> model_id = $_GET['id'];
			$this->display();
		}else{
			alert('error', L('PARAMETER_ERROR'),$_SERVER['HTTP_REFERER']);
		}
	}

	public function excelExport($serviceList){
		C('OUTPUT_ENCODE', false);
		import("ORG.PHPExcel.PHPExcel");
		$objPHPExcel = new PHPExcel();    
		$objProps = $objPHPExcel->getProperties();    
		$objProps->setCreator("crm");
		$objProps->setLastModifiedBy("crm");    
		$objProps->setTitle("crm Contact");    
		$objProps->setSubject("crm Contact Data");    
		$objProps->setDescription("crm Contact Data");    
		$objProps->setKeywords("crm Contact");    
		$objProps->setCategory("crm");
		$objPHPExcel->setActiveSheetIndex(0);     
		$objActSheet = $objPHPExcel->getActiveSheet(); 
		   
		$objActSheet->setTitle('Sheet1');
		$objActSheet->setCellValue('A1', L('NAME'));
		$objActSheet->setCellValue('B1', L('RESPECTFULLY'));
		$objActSheet->setCellValue('C1', L('DEPARTMENT'));
		$objActSheet->setCellValue('D1', L('POSITION'));
		$objActSheet->setCellValue('E1', 'QQ');
		$objActSheet->setCellValue('F1', L('PHONE'));
		$objActSheet->setCellValue('G1', 'Email');
		$objActSheet->setCellValue('H1', L('ADDRESS'));
		$objActSheet->setCellValue('I1', L('POSTCODE'));
		$objActSheet->setCellValue('J1', L('REMARK'));
		$objActSheet->setCellValue('K1', L('BELONGS TO THE CUSTOMERB'));
		$objActSheet->setCellValue('L1', L('OWNER_ROLE'));
		$objActSheet->setCellValue('M1', L('CREATOR_ROLE'));
		$objActSheet->setCellValue('N1', L('CREATE_TIME'));

		
		if(empty($serviceList)){
			$where['owner_role_id'] = array('in',implode(',', getSubRoleId()));
			$where['is_deleted'] = 0;
			$list = M('service')->where($where)->select();
		}else{
			$list = $serviceList;
		}
		
		$i = 1;
		foreach ($list as $k => $v) {
			$i++;
			$owner = D('RoleView')->where('role.role_id = %d', $v['owner_role_id'])->find();
			$creator = D('RoleView')->where('role.role_id = %d', $v['creator_role_id'])->find();
			$objActSheet->setCellValue('A'.$i, $v['name']);
			$objActSheet->setCellValue('B'.$i, $v['saltname']);
			$objActSheet->setCellValue('C'.$i, $v['department']);
			$objActSheet->setCellValue('D'.$i, $v['post']);
			$objActSheet->setCellValue('E'.$i, $v['qq']);
			$objActSheet->setCellValue('F'.$i, $v['telephone']);
			$objActSheet->setCellValue('G'.$i, $v['email']);
			$objActSheet->setCellValue('H'.$i, $v['address']);
			$objActSheet->setCellValue('I'.$i, $v['zip_code']);
			$objActSheet->setCellValue('J'.$i, $v['description']);
			$customerB_id = M('rServiceCustomerB')->where('service_id = %d', $v['service_id'])->getField('customerB_id');
			$customerB_name = M('customerB')->where('customerB_id = %d' ,$customerB_id)->getField('name');
			$objActSheet->setCellValue('K'.$i, $customerB_name);
			$objActSheet->setCellValue('L'.$i, $owner['user_name'] .'['.$owner['department_name'].'-'.$owner['role_name'].']');
			$objActSheet->setCellValue('M'.$i, $creator['user_name'].'['.$creator['department_name'].'-'.$creator['role_name'].']');
			$objActSheet->setCellValue('N'.$i, date("Y-m-d H:i:s", $v['create_time']));
		}
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		ob_end_clean();
		header("Content-Type: application/vnd.ms-excel;");
        header("Content-Disposition:attachment;filename=crm_service_".date('Y-m-d',mktime()).".xls");
        header("Pragma:no-cache");
        header("Expires:0");
        $objWriter->save('php://output'); 
	}
	
	public function excelImport(){
		$m_service = M('service');
		if($_POST['submit']){
			if (isset($_FILES['excel']['size']) && $_FILES['excel']['size'] != null) {
				import('@.ORG.UploadFile');
				$upload = new UploadFile();
				$upload->maxSize = 20000000;
				$upload->allowExts  = array('xls');
				$dirname = UPLOAD_PATH . date('Ym', time()).'/'.date('d', time()).'/';
				if (!is_dir($dirname) && !mkdir($dirname, 0777, true)) {
					alert('error', L('ATTACHMENTS TO UPLOAD DIRECTORY CANNOT WRITE'), U('service/index'));
				}
				$upload->savePath = $dirname;
				if(!$upload->upload()) {
					alert('error', $upload->getErrorMsg(), U('service/index'));
				}else{
					$info =  $upload->getUploadFileInfo();
				}
			}
			if(is_array($info[0]) && !empty($info[0])){
				$savePath = $dirname . $info[0]['savename'];
			}else{
				alert('error', L('UPLOAD FAILED'), U('service/index'));
			};
			import("ORG.PHPExcel.PHPExcel");
			$PHPExcel = new PHPExcel();
			$PHPReader = new PHPExcel_Reader_Excel2007();
			if(!$PHPReader->canRead($savePath)){
				$PHPReader = new PHPExcel_Reader_Excel5();
			}
			$PHPExcel = $PHPReader->load($savePath);
			$currentSheet = $PHPExcel->getSheet(0);
			$allRow = $currentSheet->getHighestRow();
			for ($currentRow = 3;$currentRow <= $allRow;$currentRow++) {
				$data = array();
				$data['creator_role_id'] = session('role_id');
				$data['create_time'] = time();
				$data['update_time'] = time();
				$data['owner_role_id'] = trim($_POST['owner_role_id']);
				$name = (String)$currentSheet->getCell('A'.$currentRow)->getValue();
				$name != '' && $name != null ? $data['name']=$name : ''; 
				
				/* $customerB_name = (String)$currentSheet->getCell('C'.$currentRow)->getValue();
				$customerB_id = M('CustomerB')->where('name = "%s"' ,trim($customerB_name))->getField('customerB_id');
				if($customerB_name){
					if($customerB_id > 0){
						$r_c_c['customerB_id'] = $customerB_id;
						$data['customerB_id'] = $customerB_id;
					} else {
						alert('error', '导入至第' . $currentRow . '行出错, 原因："'.$customerB_name.'"客户不存在', U('service/index'));
						break;
					}
				} */
				
				$saltname = (String)$currentSheet->getCell('B'.$currentRow)->getValue();
				$saltname != '' && $saltname != null ? $data['saltname'] = $saltname : '';
				$department = (String)$currentSheet->getCell('C'.$currentRow)->getValue();
				$department != '' && $department != null ? $data['department'] = $department : '';
				$post = (String)$currentSheet->getCell('D'.$currentRow)->getValue();
				$post != '' && $post != null ? $data['post'] = $post : '';
				$qq = (String)$currentSheet->getCell('E'.$currentRow)->getValue();
				$qq != '' && $qq != null ? $data['qq'] = $qq : '';
				$telephone = (String)$currentSheet->getCell('F'.$currentRow)->getValue();
				$telephone != '' && $telephone != null ? $data['telephone'] = $telephone : '';				
				$email = (String)$currentSheet->getCell('G'.$currentRow)->getValue();
				$email != '' && $email != null ? $data['email'] = $email : '';
				$address = (String)$currentSheet->getCell('H'.$currentRow)->getValue();
				$address != '' && $address != null ? $data['address'] = $address : '';
				$zip_code = (String)$currentSheet->getCell('I'.$currentRow)->getValue();
				$zip_code != '' && $zip_code != null ? $data['zip_code'] = $zip_code : '';
				$description = (String)$currentSheet->getCell('J'.$currentRow)->getValue();
				$description != '' && $description != null ? $data['description'] = $description : '';
				if(!$service_id = $m_service->add($data)) {
					if($this->_post('error_handing','intval',0) == 0){
							alert('error',L('ERROR INTRODUCED INTO THE LINE',array($currentRow,$m_service->getError())) , U('service/index'));
						}else{
							$error_message .= L('LINE ERROR',array($currentRow,$m_service->getError()));
							$m_service->clearError();
						}
					break;
				}
			}
			alert('success', L('IMPORT SUCCESS',array($error_message)), U('service/index'));
		} else {
			$this->display();
		}
	}
	
	public function revert(){
		$service_id = isset($_GET['id']) ? intval(trim($_GET['id'])) : 0;
		if ($service_id > 0) {
			$m_service = M('service');
			$service = $m_service->where('service_id = %d', $service_id)->find();
			if ($service['delete_role_id'] == session('role_id') || session('?admin')) {
				if (isset($service['is_deleted']) || $service['is_deleted'] == 1) {
					if ($m_service->where('service_id = %d', $service_id)->setField('is_deleted', 0)) {
						alert('success', L('REDUCTION OF SUCCESS'), $_SERVER['HTTP_REFERER']);
					} else {
						alert('error', L('REDUCTION OF FAILED'), $_SERVER['HTTP_REFERER']);
					}
				} else {
					alert('error', L('ALREADY REDUCTION'), $_SERVER['HTTP_REFERER']);
				}
			} else {
				alert('error', L('YOU HAVE NO PERMISSION TO RESTORE'), $_SERVER['HTTP_REFERER']);
			}
		} else {
			alert('error', L('PARAMETER_ERROR'), $_SERVER['HTTP_REFERER']);
		}
	}
	
	public function changeToFirstContact(){
		$id = $_GET['id'];
		$customerB_id = $_GET['customerB_id'];
		if(isset($id) && isset($customerB_id)){
			$m_customerB = M('CustomerB');
			$data['service_id'] = $id;
			if($m_customerB->where('customerB_id = %d',$customerB_id)->save($data)){
				alert('success', L('SET THE FIRST SERVICE SUCCESS') ,$_SERVER['HTTP_REFERER']);
			}else{
				alert('error', L('NO CHANGE INFORMATION') ,$_SERVER['HTTP_REFERER']);
			}
		}else{
			alert('error', L('PARAMETER_ERROR'),$_SERVER['HTTP_REFERER']);
		}
	}
	
	public function radioListDialog(){
		$id = $_GET['id'];
		$rcc =  M('RServiceCustomerB');
		$m_service = M('service');
		$where['owner_role_id'] = array('in', implode(',', getSubRoleId()));
		$where['is_deleted'] = 0;
	
		if($id){
			$service_id = $rcc->where('customerB_id = %d', $id)->getField('service_id', true);
			$where['service_id'] = array('in', implode(',', $service_id));
			$this->customerB_id = $id;
		}
		$list = $m_service->where($where)->order('create_time desc')->limit(10)->select();
		//echo $m_service->getLastSql();
		$count = $m_service->where($where)->order('create_time desc')->count();
		
		
		foreach ($list as $k=>$value) {
			$customerB_id = $rcc->where('service_id = %d', $value['service_id'])->getField('customerB_id');
			$list[$k]['customerB'] = M('customerB')->where('customerB_id = %d', $customerB_id)->find();
		}
		
		$this->total = $count%10 > 0 ? ceil($count/10) : $count/10;
		$this->count_num = $count;
		//获取下级和自己的岗位列表,搜索用
		$below_ids = getSubRoleId(false);
		$d_role_view = D('RoleView');
		$this->role_list = $d_role_view->where('role.role_id in (%s)', implode(',', $below_ids))->select();
		$this->serviceList = $list;
		$this->display();
	}

	public function changeDialog(){
		if($this->isAjax()){
			$m_service = M('service');
			$m_customerB = M('customerB');
			$p = !$_REQUEST['p']||$_REQUEST['p']<=0 ? 1 : intval($_REQUEST['p']);
			$where = array();
			$params = array();

			$where['owner_role_id'] = array('in',implode(',', getSubRoleId(true)));
			$where['is_deleted'] = array('neq', 1);
			if($_REQUEST['customerB_id'] != 0){
				$service_id = M('RServiceCustomerB')->where('customerB_id = %d', $_REQUEST['customerB_id'])->getField('service_id', true);
				$where['service_id'] = array('in', implode(',', $service_id));
			}elseif($_REQUEST['is_check']){
				$list = M($_REQUEST['r']) -> getField('service_id', true);
				$list[] = 0;
				$where['service_id'] = array('not in', implode(',', $list));
			}
			if ($_REQUEST["field"]) {
				$field = trim($_REQUEST['field']) == 'all' ? 'name|telephone|email|address|post|department|description' : $_REQUEST['field'];
				$search = empty($_REQUEST['search']) ? '' : trim($_REQUEST['search']);
				$condition = empty($_REQUEST['condition']) ? 'is' : trim($_REQUEST['condition']);
				if	('create_time' == $field || 'update_time' == $field) {
					$search = is_numeric($search)?$search:strtotime($search);
				}
				switch ($condition) {
					case "is" : $where[$field] = array('eq',$search);break;
					case "isnot" :  $where[$field] = array('neq',$search);break;
					case "contains" :  $where[$field] = array('like','%'.$search.'%');break;
					case "not_contain" :  $where[$field] = array('notlike','%'.$search.'%');break;
					case "start_with" :  $where[$field] = array('like',$search.'%');break;
					case "end_with" :  $where[$field] = array('like','%'.$search);break;
					case "is_empty" :  $where[$field] = array('eq','');break;
					case "is_not_empty" :  $where[$field] = array('neq','');break;
					case "gt" :  $where[$field] = array('gt',$search);break;
					case "egt" :  $where[$field] = array('egt',$search);break;
					case "lt" :  $where[$field] = array('lt',$search);break;
					case "elt" :  $where[$field] = array('elt',$search);break;
					case "eq" : $where[$field] = array('eq',$search);break;
					case "neq" : $where[$field] = array('neq',$search);break;
					case "between" : $where[$field] = array('between',array($search-1,$search+86400));break;
					case "nbetween" : $where[$field] = array('not between',array($search,$search+86399));break;
					case "tgt" :  $where[$field] = array('gt',$search+86400);break;
					default : $where[$field] = array('eq',$search);
				}
			}
			$serviceList = $m_service->where($where)->order('create_time desc')->page($p.',10')->select();
			$count = $m_service->where($where)->count();
			if(!$_REQUEST['is_check']){
				foreach ($serviceList as $k => $v) {
					if($customerB_id = M('rServiceCustomerB')->where('service_id = %d', $v['service_id'])->getField('customerB_id')){
						$serviceList[$k]['customerB'] = $m_customerB->where('customerB_id = %d' ,$customerB_id)->find();
					}
				}
			}
			$data['list'] = $serviceList;
			$data['p'] = $p;
			$data['count'] = $count;
			$data['total'] = $count%10 > 0 ? ceil($count/10) : $count/10;
//echo '<pre>';print_r($data);echo '</pre>'; die();
			$this->ajaxReturn($data,"",1);
		}
	}
	
	public function qrcode(){
		$service_id = intval($_GET['service_id']);
		if($service = M('Service')->where('service_id = %d', $service_id)->find()){
			$customerB_id = M('RServiceCustomerB')->where('service_id = %d',$service_id)->getField('customerB_id');
			$service['customerB'] = M('CustomerB')->where('customerB_id = %d', $customerB_id)->getField('name');
			$qrOpt = '';
			$qrOpt = "BEGIN:VCARD\nVERSION:3.0\n";
			$qrOpt .= $service['name'] ? ("FN:".$service['name']."\n") : "";
			$qrOpt .= $service['telephone'] ? ("TEL:".$service['telephone']."\n") : "";
			$qrOpt .= $service['email'] ? ("EMAIL;PREF;INTERNET:".$service['email']."\n") : "";
			$qrOpt .= $service['customerB'] ? ("ORG:".$service['customerB']."\n") : "";	
			$qrOpt .= $service['post'] ? ("TITLE:".$service['post']."\n") : "";
			$qrOpt .= $service['address'] ? ("ADR;WORK;POSTAL:".$service['address']."\n") : "";
			$qrOpt .= "END:VCARD";
			
			$png_temp_dir = UPLOAD_PATH.'/qrpng/';
			$filename = $png_temp_dir.$service['service_id'].'.png';
			if (!is_dir($png_temp_dir) && !mkdir($png_temp_dir, 0777, true)) { echo 3;$this->error('二维码保存目录不可写'); }

			import("@.ORG.QRCode.qrlib");
			QRcode::png($qrOpt, $filename, 'M', 4, 2);
			header('Content-type: image/png');	
			header("Content-Disposition: attachment; filename=".$service['service_id'].'.png');
			echo file_get_contents($filename);
			unlink($filename);
		}
	}
}