<?php 
class ContactsBAction extends Action {

	public function _initialize(){
		$action = array(
			'permission'=>array(),
			'allow'=>array('checklistdialog','getContactsBList', 'revert', 'mdelete','radiolistdialog','changedialog','add_dialog','qrcode','changetofirstcontact')
		);
		B('Authenticate', $action);
	}
	
	public function add(){
		if ($_GET['r'] && $_GET['module'] && $_GET['id']) {
			$this -> r = $_GET['r'];
			$this -> module = $_GET['module'];
			$this -> id = $_GET['id'];
			$this->refer_url=$_SERVER['HTTP_REFERER'];
			$this->display('ContactsB:add_dialog');
		}elseif($this->isPost()){
			$name = trim($_POST['name']);
			$customerB_id = trim($_POST['customerB_id']);
			$leadsB_id = trim($_POST['leadsB_id']);
			if ($name == '' || $name == null) {
				$this -> error(L('CONTACT NAME CANNOT BE EMPTY'));
			}
			if (($customerB_id == '' || $customerB_id == null) && ($leadsB_id == '' || $leadsB_id == null)) {
				$this->error(L('CONTACTSB_CUSTOMERB_CANNOT_BE_EMPTY'));
			}
			$contactsB = M('contactsB');
			
			//自定义字段数据存入contactsB_data表
			$field_list = M('Fields')->where('model = "contactsB" and in_add = 1')->order('order_id')->select();
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
			
			$contactsB->create();
			$contactsB->create_time = time();
			$contactsB->update_time = time();
			$contactsB->creator_role_id = session('role_id');
			if($contactsB_id = $contactsB->add()){
				if($_POST['customerB_id']){
					$rContactsBCustomerB['contactsB_id'] =  $contactsB_id;
					$rContactsBCustomerB['customerB_id'] =  $_POST['customerB_id'];
					M('rContactsBCustomerB') ->add($rContactsBCustomerB);
				}
				if($_POST['leadsB_id']){
					$rContactsBLeadsB['contactsB_id'] =  $contactsB_id;
					$rContactsBLeadsB['leadsB_id'] =  $_POST['leadsB_id'];
					M('rContactsBLeadsB') ->add($rContactsBLeadsB);
				}
				
				if($_POST['redirect'] == 'customerB'){
					//alert('success','添加成功!',U('customerB/view','id='.intval($_POST['redirect_id'])));
					if($_POST['refer_url']){
						alert('success', '添加联系人成功', $_POST['refer_url']);
					}else{
						alert('success',L('ADD A SUCCESS'),U('contactsB/view','id='.$contactsB_id));
					}
				}elseif($_POST['redirect'] == 'leadsB'){
					//alert('success','添加成功!',U('customerB/view','id='.intval($_POST['redirect_id'])));
					if($_POST['refer_url']){
						alert('success', '添加联系人成功', $_POST['refer_url']);
					}else{
						alert('success',L('ADD A SUCCESS'),U('leadsB/view','id='.$leadsB_id));
					}
				}else{
					if($_POST['submit'] == L('SAVE')){
						alert('success',L('ADD A SUCCESS'), $_POST['refer_url']);
					}else{
						alert('success',L('ADD A SUCCESS'),U('contactsB/add'));
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
			$leadsB = M('leadsB');
			$this->customerB_id =$customerB_id = $_GET['customerB_id'];
			$this->customerB_name =$customerB->where('customerB_id =%s',$customerB_id)->getField('name');
			
			$this->leadsB_id =$leadsB_id = $_GET['leadsB_id'];
			$this->leadsB_name =$leadsB->where('leadsB_id =%s',$leadsB_id)->getField('name');
			
			$this->refer_url=$_SERVER['HTTP_REFERER'];
			$this->customerB = $customerB->where('customerB_id = %s', $_GET['redirect_id'])->find();
			$this->leadsB = $leadsB->where('leadsB_id = %s', $_GET['redirect_id'])->find();
			$this->alert = parseAlert();
			$this->display();
		}
	}
	
	public function edit(){
		$m_contactsB = M('contactsB');
		$rContactsBCustomerB = M('rContactsBCustomerB');
		$rContactsBLeadsB = M('rContactsBLeadsB');
		$contactsB_id = $_GET['id'] ? intval($_GET['id']) : intval($_POST['contactsB_id']);
		if(empty($contactsB_id)){
			alert('error',L('PARAMETER_ERROR'),$_SERVER['HTTP_REFERER']);
		}
		$contactsB = D('ContactsBView')->where(array('contactsB_id'=>$contactsB_id))->find();
		if(empty($contactsB['customerB_id'])){
			$contactsB = D('ContactsBLView')->where(array('contactsB_id'=>$contactsB_id))->find();
		}
		
		if(empty($contactsB)) alert('error', L('RECORD_NOT_EXIST_OR_HAVE_BEEN_DELETED',array(L('CONTACTSB'))),U('contactsB/index'));
		//检查权限(联系人编辑权限跟随客户，如果可以编辑客户即可编辑联系人)
		$customerB_id = $rContactsBCustomerB->where('contactsB_id = %d', $contactsB_id)->getField('customerB_id');
		$leadsB_id = $rContactsBLeadsB->where('contactsB_id = %d', $contactsB_id)->getField('leadsB_id');
		if((!vali_permission('customerB','edit') || !check_permission($customerB_id, 'customerB')) && (!vali_permission('leadsB','edit') || !check_permission($leadsB_id, 'leadsB'))) $this->error(L('HAVE NOT PRIVILEGES'));
		
		if ($this->isPost()) {
			$m_contactsB->create();
			$m_contactsB->update_time = time();
			$name = trim($_POST['name']);
			if ($name == '' || $name == null) {
				alert('error',L('CONTACT NAME CANNOT BE EMPTY'),$_SERVER['HTTP_REFERER']);
			}
			if (!empty($_POST['customerB_id'])) {
				if (empty($customerB_id)) {
					$data['contactsB_id'] = $_POST['contactsB_id'];
					$data['customerB_id'] = $_POST['customerB_id'];
					$rContactsBCustomerB ->where('contactsB_id = %d', $_POST['contactsB_id'])->delete();
					$rContactsBCustomerB -> add($data);
				}elseif ($_POST['customerB_id'] != $customerB_id) {
					$rContactsBCustomerB -> where('contactsB_id = %d' , $_POST['contactsB_id']) -> setField('customerB_id',$_POST['customerB_id']);
				}	
			}elseif (!empty($_POST['leadsB_id'])) {
				if (empty($leadsB_id)) {
					$data['contactsB_id'] = $_POST['contactsB_id'];
					$data['leadsB_id'] = $_POST['leadsB_id'];
					$rContactsBLeadsB ->where('contactsB_id = %d', $_POST['contactsB_id'])->delete();
					$rContactsBLeadsB -> add($data);
				}elseif ($_POST['leadsB_id'] != $leadsB_id) {
					$rContactsBLeadsB -> where('contactsB_id = %d' , $_POST['contactsB_id']) -> setField('leadsB_id',$_POST['leadsB_id']);
				}	
			}else{
				alert('error', L('NOT NULL',array(L('CUSTOMERB'))), $_SERVER['HTTP_REFERER']);
			}
			if ($m_contactsB->save()) {
				alert('success',L('THE CONTACT INFORMATION OF SUCCESS'),U('contactsB/view') . "&id=" . $_POST['contactsB_id']);
			} else {
				alert('error',L('THE CONTACT INFORMATION CHANGE FAILED'),$_SERVER['HTTP_REFERER']);
			}
		}else{
			$this->contactsB = $contactsB;
			$this->alert = parseAlert();
			$this->display();
		}
	}
	
	public function view(){
		$contactsB_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
		$rContactsBCustomerB = M('rContactsBCustomerB');
		$rContactsBLeadsB = M('rContactsBLeadsB');
		$d_contactsB = D('ContactsBView');
		if (0 == $contactsB_id) {
			alert('error', L('PARAMETER_ERROR'), U('contactsB/index'));
		} else {
			//检查权限(联系人查看权限跟随客户，如果可以查看客户即可查看联系人)
			$customerB_id = $rContactsBCustomerB->where('contactsB_id = %d', $contactsB_id)->getField('customerB_id');
			if(!empty($customerB_id)){
				if(!vali_permission('customerB','view') || !check_permission($customerB_id, 'customerB')){
					$this->error(L('HAVE NOT PRIVILEGES'));
				}
				$contactsB = D('ContactsBView')->where('contactsB.contactsB_id = %d' , $contactsB_id)->find();
				$this->type = 'customerB';
			}else{
				$leadsB_id = $rContactsBLeadsB->where('contactsB_id = %d', $contactsB_id)->getField('leadsB_id');
				if(!vali_permission('leadsB','view') || !check_permission($leadsB_id, 'leadsB')){
					$this->error(L('HAVE NOT PRIVILEGES'));
				}
				$contactsB = D('ContactsBLView')->where('contactsB.contactsB_id = %d' , $contactsB_id)->find();
				$this->type = 'leadsB';
			}
			
			if(empty($contactsB)){
				alert('error',L('RECORD_NOT_EXIST_OR_HAVE_BEEN_DELETED',array(L('CONTACTSB'))),U('contactsB/index'));
			}
			$this->contactsB = $contactsB;		
			$this->alert = parseAlert();
			$this->display();
		}		
	}

	public function index(){
		$d_contactsB = D('ContactsBView');
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
			if(vali_permission('contactsB', 'export')){
				$order = $order ? $order : 'create_time desc';
				$contactsBList = $d_contactsB->where($where)->order($order)->select();		
				$this->excelExport($contactsBList);
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
				$contactsBList = $d_contactsB->where($where)->order($order)->page($p.','.$listrows)->select();
				$count = $d_contactsB->where($where)->count();
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
				foreach ($contactsBList as $k => $v) {
					$contactsBList[$k]["delete_role"] = getUserByRoleId($v['delete_role_id']);
					$contactsBList[$k]["creator"] = getUserByRoleId($v['creator_role_id']);
				}
			}else{
				foreach ($contactsBList as $k => $v) {		
					$contactsBList[$k]["creator"] = getUserByRoleId($v['creator_role_id']);
				}
			}
			$this->listrows = $listrows;
			//获取下级和自己的岗位列表,搜索用
			$d_role_view = D('RoleView');
			$this->role_list = $d_role_view->where('role.role_id in (%s)', implode(',', $below_ids))->select();
			$this->assign('contactsBList',$contactsBList);
			$this->alert = parseAlert();
			$this->display();
		}
	}

	public function completeDelete(){
		$m_contactsB = M('contactsB');
		$rContactsBCustomerB = M('rContactsBCustomerB');
		//检查权限
		$all_ids = getSubRoleId();
		$customerB_idArr = M('customerB')->where(array('owner_role_id'=>array('in', $all_ids)))->getField('customerB_id', true);
		
		$r_module = array('File'=>'RContactsBFile', 'Log'=>'RContactsBLog', 'RContactsBCustomerB', 'RContactsBTask','RContactsBEvent');
		if ($_POST['contactsB_id']) {
			if (!session('?admin')) {
				foreach ($_POST['contactsB_id'] as $value) {
					$customerB_id = $rContactsBCustomerB->where('contactsB_id = %d', $value)->getField('customerB_id');
					if(!in_array($customerB_id, $customerB_idArr)){
						alert('error', L('YOU DO NOT HAVE PERMISSION TO ALL'), $_SERVER['HTTP_REFERER']);
					}
				}
			}
			if ($m_contactsB->where('contactsB_id in (%s)', join($_POST['contactsB_id'],','))->delete()) {
				foreach ($_POST['contactsB_list'] as $value) {
					foreach ($r_module as $key2=>$value2) {
						$module_ids = M($value2)->where('contactsB_id = %d', $value)->getField($key2 . '_id',true);
						M($value2)->where('contactsB_id = %d', $value) -> delete();
						if(!is_int($key2)){
							M($key2)->where($key2 . '_id in (%s)', implode(',', $module_ids))->delete();
						}
					}
				}
				alert('success', L('DELETED SUCCESSFULLY'),U('contactsB/index','by=deleted'));
			} else {
				alert('error',L('DELETE FAILED CONTACT THE ADMINISTRATOR'),$_SERVER['HTTP_REFERER']);
			}
		}elseif($_GET['id']){
			$contactsB_id = intval($_GET['id']);
			$contactsB = $m_contactsB->where('contactsB_id = %d', $contactsB_id)->find();
			if (is_array($contactsB)) {
				//检查权限
				$customerB_id = $rContactsBCustomerB->where('contactsB_id = %d', $contactsB_id)->getField('customerB_id');
				if (session('?admin') || in_array($customerB_id, $customerB_idArr)) {
					if($m_contactsB->where('contactsB_id = %d', $contactsB_id)->delete()){
						foreach ($r_module as $key2=>$value2) {
							if(!is_int($key2)){
								$module_ids = M($value2)->where('contactsB_id = %d', $contactsB_id)->getField($key2 . '_id',true);
								M($value2)->where('contactsB_id = %d', $contactsB_id)->delete();
								$m_key = M($key2);
								$m_key->where($key2 . '_id in (%s)', implode(',', $module_ids))->delete();
							}
						}
						alert('success', L('DELETED SUCCESSFULLY'),U('contactsB/index','by=deleted'));
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
			alert('error',L('PLEASE CHOOSE TO DELETE THE CONTACT'),$_SERVER['HTTP_REFERER']);
		}
	}
	
	public function delete(){
		$m_contactsB = M('contactsB');
		$rContactsBCustomerB = M('rContactsBCustomerB');
		//检查权限
		$all_ids = getSubRoleId();;
		$customerB_idArr = M('customerB')->where(array('owner_role_id'=>array('in', $all_ids)))->getField('customerB_id', true);
		if ($_POST['contactsB_id']) {
			if (!session('?admin')) {
				foreach ($_POST['contactsB_id'] as $value) {
					//检查权限
					$customerB_id = $rContactsBCustomerB->where('contactsB_id = %d', $value)->getField('customerB_id');
					if(!in_array($customerB_id, $customerB_idArr)){
						alert('error', L('YOU DO NOT HAVE PERMISSION TO ALL'), $_SERVER['HTTP_REFERER']);
					}
				}
			}
			$data = array('is_deleted'=>1, 'delete_role_id'=>session('role_id'), 'delete_time'=>time());
			if ($m_contactsB->where('contactsB_id in (%s)', implode(',', $_POST['contactsB_id']))->setField($data)) {
				alert('success', L('DELETED SUCCESSFULLY'),U('contactsB/index'));
			} else {
				echo $m_contactsB->getLastSql(); die();
				alert('error', L('DELETE FAILED CONTACT THE ADMINISTRATOR'),$_SERVER['HTTP_REFERER']);
			}
		}elseif($_GET['id']){
			$contactsB_id = intval($_GET['id']);
			//检查权限
			$customerB_id = $rContactsBCustomerB->where('contactsB_id = %d', $contactsB_id)->getField('customerB_id');
			
			$contactsB = $m_contactsB->where('contactsB_id = %d', $contactsB_id)->find();
			if (is_array($contactsB)) {
				if (session('?admin') || in_array($customerB_id, $customerB_idArr)) {
					$data = array('is_deleted'=>1, 'delete_role_id'=>session('role_id'), 'delete_time'=>time());
					if($m_contactsB->where('contactsB_id = %d', $contactsB_id)->setField($data)){
						alert('success', L('DELETED SUCCESSFULLY'),U('contactsB/index'));
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
			alert('error',L('PLEASE CHOOSE TO DELETE THE CONTACT'),$_SERVER['HTTP_REFERER']);
		}
	}
	
	public function mDelete(){
		if($_GET['r'] && $_GET['id'] && $_GET['module_id']){
			$m_r = M($_GET['r']);
			if($m_r->where("contactsB_id = %d and customerB_id", $_GET['id'], $_GET['module_id'])->delete()){
				M('CustomerB')->where("customerB_id", $_GET['module_id'])->setField('contactsB_id', 0);
				alert('success',L('DELETED SUCCESSFULLY'),$_SERVER['HTTP_REFERER']);
			} else {
				alert('error',L('DELETE FAILED'),$_SERVER['HTTP_REFERER']);
			}
		} else {
			alert('error',L('PARAMETER_ERROR'),$_SERVER['HTTP_REFERER']);
		}
	}
	
	public function getContactsBList(){
		$d_contactsB = D('ContactsBView');
		$idArray = getSubRoleId();
		//获取下级和自己的客户列表,搜索
		$contactsBList = D('ContactsB')->where(array('owner_role_id'=>array('in',$idArray),'is_deleted'=>array('eq', 0)))->select();
		$this->ajaxReturn($contactsBList, '', 1);
	}
	public function checkListDialog(){
		if($this->isPost()){
			$r = $_POST['r'];
			$model_id = isset($_POST['id']) ? intval($_POST['id']) : 0;
			$m_r = M($r);
			$m_id = $_POST['module'] . '_id';  //对应模块的id字段
			$data[$m_id] = $model_id;
			foreach ($_POST['contactsB_id'] as $value) {
				$data['contactsB_id'] = $value;
				if ($m_r -> add($data) <= 0) {
					alert('error', L('SELECT THE CONTACT FAILURE'),$_SERVER['HTTP_REFERER']);
				}
			}
			alert('success', L('SELECT THE CONTACT SUCCESS'),$_SERVER['HTTP_REFERER']);
		}elseif ($_GET['r'] && $_GET['module'] && $_GET['id']) {
			$list = M($_GET['r']) -> getField('contactsB_id', true);
			$m_contactsB = M('ContactsB');
			$underling_ids = getSubRoleId();
			$list[] = 0;
			$this->contactsBList = $m_contactsB->where('contactsB_id not in (%s) and creator_role_id in (%s) and is_deleted <> 1', implode(',',$list), implode(',', $underling_ids))->order('create_time desc')->limit(10)->select();
			$count = $m_contactsB->where('contactsB_id not in (%s) and owner_role_id in (%s) and is_deleted = 0', implode(',',$list), implode(',', $underling_ids))->count();
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

	public function excelExport($contactsBList){
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

		
		if(empty($contactsBList)){
			$where['owner_role_id'] = array('in',implode(',', getSubRoleId()));
			$where['is_deleted'] = 0;
			$list = M('contactsB')->where($where)->select();
		}else{
			$list = $contactsBList;
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
			$customerB_id = M('rContactsBCustomerB')->where('contactsB_id = %d', $v['contactsB_id'])->getField('customerB_id');
			$customerB_name = M('customerB')->where('customerB_id = %d' ,$customerB_id)->getField('name');
			$objActSheet->setCellValue('K'.$i, $customerB_name);
			$objActSheet->setCellValue('L'.$i, $owner['user_name'] .'['.$owner['department_name'].'-'.$owner['role_name'].']');
			$objActSheet->setCellValue('M'.$i, $creator['user_name'].'['.$creator['department_name'].'-'.$creator['role_name'].']');
			$objActSheet->setCellValue('N'.$i, date("Y-m-d H:i:s", $v['create_time']));
		}
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		ob_end_clean();
		header("Content-Type: application/vnd.ms-excel;");
        header("Content-Disposition:attachment;filename=crm_contactsB_".date('Y-m-d',mktime()).".xls");
        header("Pragma:no-cache");
        header("Expires:0");
        $objWriter->save('php://output'); 
	}
	
	public function excelImport(){
		$m_contactsB = M('contactsB');
		if($_POST['submit']){
			if (isset($_FILES['excel']['size']) && $_FILES['excel']['size'] != null) {
				import('@.ORG.UploadFile');
				$upload = new UploadFile();
				$upload->maxSize = 20000000;
				$upload->allowExts  = array('xls');
				$dirname = UPLOAD_PATH . date('Ym', time()).'/'.date('d', time()).'/';
				if (!is_dir($dirname) && !mkdir($dirname, 0777, true)) {
					alert('error', L('ATTACHMENTS TO UPLOAD DIRECTORY CANNOT WRITE'), U('contactsB/index'));
				}
				$upload->savePath = $dirname;
				if(!$upload->upload()) {
					alert('error', $upload->getErrorMsg(), U('contactsB/index'));
				}else{
					$info =  $upload->getUploadFileInfo();
				}
			}
			if(is_array($info[0]) && !empty($info[0])){
				$savePath = $dirname . $info[0]['savename'];
			}else{
				alert('error', L('UPLOAD FAILED'), U('contactsB/index'));
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
						alert('error', '导入至第' . $currentRow . '行出错, 原因："'.$customerB_name.'"客户不存在', U('contactsB/index'));
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
				if(!$contactsB_id = $m_contactsB->add($data)) {
					if($this->_post('error_handing','intval',0) == 0){
							alert('error',L('ERROR INTRODUCED INTO THE LINE',array($currentRow,$m_contactsB->getError())) , U('contactsB/index'));
						}else{
							$error_message .= L('LINE ERROR',array($currentRow,$m_contactsB->getError()));
							$m_contactsB->clearError();
						}
					break;
				}
			}
			alert('success', L('IMPORT SUCCESS',array($error_message)), U('contactsB/index'));
		} else {
			$this->display();
		}
	}
	
	public function revert(){
		$contactsB_id = isset($_GET['id']) ? intval(trim($_GET['id'])) : 0;
		if ($contactsB_id > 0) {
			$m_contactsB = M('contactsB');
			$contactsB = $m_contactsB->where('contactsB_id = %d', $contactsB_id)->find();
			if ($contactsB['delete_role_id'] == session('role_id') || session('?admin')) {
				if (isset($contactsB['is_deleted']) || $contactsB['is_deleted'] == 1) {
					if ($m_contactsB->where('contactsB_id = %d', $contactsB_id)->setField('is_deleted', 0)) {
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
		$leadsB_id = $_GET['leadsB_id'];
		if(isset($id) && isset($customerB_id)){
			$m_customerB = M('CustomerB');
			$data['contactsB_id'] = $id;
			if($m_customerB->where('customerB_id = %d',$customerB_id)->save($data)){
				alert('success', L('SET THE FIRST CONTACT SUCCESS') ,$_SERVER['HTTP_REFERER']);
			}else{
				alert('error', L('NO CHANGE INFORMATION') ,$_SERVER['HTTP_REFERER']);
			}
		}elseif(isset($id) && isset($leadsB_id)){
			$m_leadsB = M('LeadsB');
			$data['contactsB_id'] = $id;
			$contacts = M('ContactsB')->field('name')->find($id);
			$data['contactsB_name'] = $contacts['name'];
			if($m_leadsB->where('leadsB_id = %d',$leadsB_id)->save($data)){
				alert('success', L('SET THE FIRST CONTACT SUCCESS') ,$_SERVER['HTTP_REFERER']);
			}else{
				alert('error', L('NO CHANGE INFORMATION') ,$_SERVER['HTTP_REFERER']);
			}
		}else{
			alert('error', L('PARAMETER_ERROR'),$_SERVER['HTTP_REFERER']);
		}
	}
	
	public function radioListDialog(){
		$id = $_GET['id'];
		$rcc =  M('RContactsBCustomerB');
		$m_contactsB = M('contactsB');
		$where['creator_role_id'] = array('in', implode(',', getSubRoleId()));
		$where['is_deleted'] = 0;
	
		if($id){
			$contactsB_id = $rcc->where('customerB_id = %d', $id)->getField('contactsB_id', true);
			$where['contactsB_id'] = array('in', implode(',', $contactsB_id));
			$this->customerB_id = $id;
		}
		$list = $m_contactsB->where($where)->order('create_time desc')->limit(10)->select();
		//echo $m_contactsB->getLastSql();
		$count = $m_contactsB->where($where)->order('create_time desc')->count();
		
		
		foreach ($list as $k=>$value) {
			$customerB_id = $rcc->where('contactsB_id = %d', $value['contactsB_id'])->getField('customerB_id');
			$list[$k]['customerB'] = M('customerB')->where('customerB_id = %d', $customerB_id)->find();
		}
		
		$this->total = $count%10 > 0 ? ceil($count/10) : $count/10;
		$this->count_num = $count;
		//获取下级和自己的岗位列表,搜索用
		$below_ids = getSubRoleId(false);
		$d_role_view = D('RoleView');
		$this->role_list = $d_role_view->where('role.role_id in (%s)', implode(',', $below_ids))->select();
		$this->contactsBList = $list;
		$this->display();
	}

	public function changeDialog(){
		if($this->isAjax()){
			$m_contactsB = M('contactsB');
			$m_customerB = M('customerB');
			$p = !$_REQUEST['p']||$_REQUEST['p']<=0 ? 1 : intval($_REQUEST['p']);
			$where = array();
			$params = array();

			$where['owner_role_id'] = array('in',implode(',', getSubRoleId(true)));
			$where['is_deleted'] = array('neq', 1);
			if($_REQUEST['customerB_id'] != 0){
				$contactsB_id = M('RContactsBCustomerB')->where('customerB_id = %d', $_REQUEST['customerB_id'])->getField('contactsB_id', true);
				$where['contactsB_id'] = array('in', implode(',', $contactsB_id));
			}elseif($_REQUEST['is_check']){
				$list = M($_REQUEST['r']) -> getField('contactsB_id', true);
				$list[] = 0;
				$where['contactsB_id'] = array('not in', implode(',', $list));
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
			$contactsBList = $m_contactsB->where($where)->order('create_time desc')->page($p.',10')->select();
			$count = $m_contactsB->where($where)->count();
			if(!$_REQUEST['is_check']){
				foreach ($contactsBList as $k => $v) {
					if($customerB_id = M('rContactsBCustomerB')->where('contactsB_id = %d', $v['contactsB_id'])->getField('customerB_id')){
						$contactsBList[$k]['customerB'] = $m_customerB->where('customerB_id = %d' ,$customerB_id)->find();
					}
				}
			}
			$data['list'] = $contactsBList;
			$data['p'] = $p;
			$data['count'] = $count;
			$data['total'] = $count%10 > 0 ? ceil($count/10) : $count/10;
//echo '<pre>';print_r($data);echo '</pre>'; die();
			$this->ajaxReturn($data,"",1);
		}
	}
	
	public function qrcode(){
		$contactsB_id = intval($_GET['contactsB_id']);
		if($contactsB = M('ContactsB')->where('contactsB_id = %d', $contactsB_id)->find()){
			$customerB_id = M('RContactsBCustomerB')->where('contactsB_id = %d',$contactsB_id)->getField('customerB_id');
			$contactsB['customerB'] = M('CustomerB')->where('customerB_id = %d', $customerB_id)->getField('name');
			$qrOpt = '';
			$qrOpt = "BEGIN:VCARD\nVERSION:3.0\n";
			$qrOpt .= $contactsB['name'] ? ("FN:".$contactsB['name']."\n") : "";
			$qrOpt .= $contactsB['telephone'] ? ("TEL:".$contactsB['telephone']."\n") : "";
			$qrOpt .= $contactsB['email'] ? ("EMAIL;PREF;INTERNET:".$contactsB['email']."\n") : "";
			$qrOpt .= $contactsB['customerB'] ? ("ORG:".$contactsB['customerB']."\n") : "";	
			$qrOpt .= $contactsB['post'] ? ("TITLE:".$contactsB['post']."\n") : "";
			$qrOpt .= $contactsB['address'] ? ("ADR;WORK;POSTAL:".$contactsB['address']."\n") : "";
			$qrOpt .= "END:VCARD";
			
			$png_temp_dir = UPLOAD_PATH.'/qrpng/';
			$filename = $png_temp_dir.$contactsB['contactsB_id'].'.png';
			if (!is_dir($png_temp_dir) && !mkdir($png_temp_dir, 0777, true)) { echo 3;$this->error('二维码保存目录不可写'); }

			import("@.ORG.QRCode.qrlib");
			QRcode::png($qrOpt, $filename, 'M', 4, 2);
			header('Content-type: image/png');	
			header("Content-Disposition: attachment; filename=".$contactsB['contactsB_id'].'.png');
			echo file_get_contents($filename);
			unlink($filename);
		}
	}
}