<?php  
class LeadsBAction extends CommonAction{

	public function _initialize(){
		$action = array(
			'permission'=>array(),
			'allow'=>array('analytics','transform','changecontent','getaddchartbyroleid','getownchartbyroleid','check','receive','remove','fenpei','batchreceive', 'assigndialog', 'batchassign', 'revert', 'validate','excelimportdownload','getcurrentstatus','upload')
		);
		B('Authenticate', $action);
	}
	/* elseif (!empty($_POST['mobile'])&&!preg_match("/^13[0-9]{1}[0-9]{8}$|15[0189]{1}[0-9]{8}$|189[0-9]{8}$/", $_POST['mobile'])){   
		alert('error', '手机格式错误!',$_SERVER['HTTP_REFERER']);
	} */
	public function check(){
		import("@.ORG.SplitWord");
		$sp = new SplitWord();
		$m_leadsB = M('LeadsB');
		$m_customerB = M('CustomerB');
		//ignore words
		$useless_words = array(L('COMPANY'),L('LIMITED'),L('OF'),L('COMPANY_LIMITED'));
		if ($this->isAjax()) {
			$split_result = $sp->SplitRMM($_POST['name']);
			if(!is_utf8($split_result)) $split_result = iconv("GB2312//IGNORE", "UTF-8", $split_result) ;
			$result_array = explode(' ',trim($split_result));
            if(count($result_array) < 2){
                $this->ajaxReturn(0,'',0);
                die;
            }
			foreach($result_array as $k=>$v){
				if(in_array($v,$useless_words)) unset($result_array[$k]);
			}
			$leadsB_commpany_list = $m_leadsB->getField('name', true);
			$customerB_commpany_list = $m_customerB->getField('name', true);
			
			$search_array = array();
			foreach($leadsB_commpany_list as $k=>$v){
				$search = 0;
				foreach($result_array as $k2=>$v2){
					if(strpos($v, $v2) > -1){
						$v = str_replace("$v2","<span style='color:red;'>$v2</span>", $v, $count);
						$search += $count;
					}
				}
				if($search > 2) $search_array[$k] = array('value'=>$v,'search'=>$search);
			}
			$seach_sort_result['leadsB'] = array_sort($search_array,'search','desc');	
			
			$customerB_search_array = array();
			foreach($customerB_commpany_list as $k=>$v){
				$search = 0;
				foreach($result_array as $k2=>$v2){
					if(strpos($v, $v2) > -1){
						$v = str_replace("$v2","<span style='color:red;'>$v2</span>", $v, $count);
						$search += $count;
					}
				}
				if($search > 2) $customerB_search_array[$k] = array('value'=>$v,'search'=>$search);
			}
			$seach_sort_result['customerB'] = array_sort($customerB_search_array,'search','desc');
			
			$leadsB_search = $seach_sort_result['leadsB'];
			$customerB_search = $seach_sort_result['customerB'];
			
			if(empty($leadsB_search) && empty($customerB_search)){
				$this->ajaxReturn(0,L('YOU_CAN_ADD'),0);
			}else{
				$this->ajaxReturn($seach_sort_result,L('EXIST_SAME_LEADSB_OR_COMPANY'),1);
			}
		}
	}
	public function validate() {
		if($this->isAjax()){
            if(!$this->_request('clientid','trim') || !$this->_request($this->_request('clientid','trim'),'trim')) $this->ajaxReturn("","",3);
            $field = M('Fields')->where('model = "leadsB" and field = "%s"', $this->_request('clientid','trim'))->find();
            $m_leadsB = $field['is_main'] ? D('LeadsB') : D('LeadsBData');
            $where[$this->_request('clientid','trim')] = array('eq',$this->_request($this->_request('clientid','trim'),'trim'));
            if($this->_request('id','intval',0)){
                $where[$m_leadsB->getpk()] = array('neq',$this->_request('id','intval',0));
            }
			if($this->_request('clientid','trim')) {
				if ($m_leadsB->where($where)->find()) {
					$this->ajaxReturn("","",1);
				} else {
					$this->ajaxReturn("","",0);
				}
			}else{
				$this->ajaxReturn("","",0);
			}
           
		}
	}
	
	
	public function add(){
		$widget['date'] = true;
		$widget['uploader'] = true;
		$widget['editor'] = true;
		$this -> assign("widget", $widget);
		
		if($this->isPost()){
			$m_leadsB = D('LeadsB');
			$m_leadsB_data = D('LeadsBData');
			$field_list = M('Fields')->where('model = "leadsB"  and in_add = 1')->order('order_id')->select();
			foreach ($field_list as $v){
				switch($v['form_type']) {
					case 'address':
						$a = array_filter($_POST[$v['field']]);
						$_POST[$v['field']] = !empty($a) ? implode(chr(10),$a) : '';
					break;
					case 'datetime':
						$_POST[$v['field']] = strtotime($_POST[$v['field']]);
					break;
					case 'box':
						eval('$field_type = '.$v['setting'].';');
						if($field_type['type'] == 'checkbox'){
							$b = array_filter($_POST[$v['field']]);
							$_POST[$v['field']] = !empty($b) ? implode(chr(10),$b) : '';
						}
					break;
				}
			}
			if($m_leadsB->create()){
				if($m_leadsB_data->create()!==false){
					if($_POST['contactsB_name']){
						$contactsB = array();
						if($_POST['contactsB_name']) $contactsB['name'] = $_POST['contactsB_name'];
// 						if($_POST['owner_role_id']) $contactsB['owner_role_id'] = $_POST['owner_role_id'];
						if($_POST['sex']) $contactsB['sex'] = $_POST['sex'];
						if($_POST['email']) $contactsB['email'] = $_POST['email'];
						if($_POST['position']) $contactsB['post'] = $_POST['position'];
						if($_POST['qqnumber']) $contactsB['qq'] = $_POST['qqnumber'];
						if($_POST['weixin']) $contactsB['weixin'] = $_POST['weixin'];
						if($_POST['mobile']) $contactsB['telephone'] = $_POST['mobile'];
						if(!empty($contactsB)){
							$contactsB['creator_role_id'] = session('role_id');
							$contactsB['create_time'] = time();
							$contactsB['update_time'] = time();
							if(!$contactsB_id = M('ContactsB')->add($contactsB)){
								alert('error', L('ADD_THE_PRIMARY_CONTACT_FAILURE'), U('leadsB/add'));
							}
						}
					}
					
					if($contactsB_id) $m_leadsB->contactsB_id = $contactsB_id;
					if($_POST['nextstep_time']) $m_leadsB->nextstep_time = $_POST['nextstep_time'];
					$m_leadsB->create_time = time();
					$m_leadsB->update_time = time();
					$m_leadsB->have_time = time();
					
					$service = array_filter($_POST['service']);
					$m_leadsB->service = !empty($service) ? implode(chr(10),$service) : '';
					
					if ($leadsB_id = $m_leadsB->add()) {
						$m_leadsB_data->leadsB_id = $leadsB_id;
						$m_leadsB_data->add();
						M('Provide')->add(array('provide_name'=>$_POST['name'],'leadsB_id'=>$leadsB_id));
						actionLog($leadsB_id);
						if ($contactsB_id && $leadsB_id) {
							$rcc['contactsB_id'] = $contactsB_id;
							$rcc['leadsB_id'] = $leadsB_id;
							M('RContactsBLeadsB')->add($rcc);
						}
						if($_POST['submit'] == L('SAVE')) {
							alert('success', L('LEADSB_ADD_SUCCESS'), U('leadsB/index'));
						} else {
							alert('success', L('LEADSB_ADD_SUCCESS'), U('leadsB/add'));
						}
					} else {
						alert('error', L('INVALIDATE_PARAM_ADD_LEADSB_FAILED'),$_SERVER['HTTP_REFERER']);
					}
				}else{
					$this->error($m_leadsB_data->getError());
				}
			}else{
				$this->error($m_leadsB->getError());
			}
			
		}else{
			$field_list = field_list_html("add","leadsB");
			$service_item_data = array();
			$service_item_str = '';
			$simple_data_mapping = D('OASimpleDataMapping')->where(array('data_code'=>array('like','crm_%')))->select();
			foreach ($simple_data_mapping as $k=>$v){
				$class_id = substr($v['data_code'],4,2);
				$item_id = substr($v['data_code'],-2,2);
				$class_name = $v['data_type'];
				$item_name = $v['data_name'];
				$service_item_data[$class_id][$item_id] = $class_name.'_'.$item_name;
				$service_item_data[$class_id][$item_id] = $class_name.'_'.$item_name;
			}
			foreach ($service_item_data as $k=>$v){
				$temp = explode('_',current($v));
				$service_item_str .= '#'.$temp[0];
				foreach ($v as $kk=>$vv){
					$temp = explode('_',$vv);
					if($kk == '01'){
						$service_item_str .= '$'.$temp[1];
					}else{
						$service_item_str .= '|'.$temp[1];
					}
				}
			}
			$service_item_str = substr($service_item_str,1);
			$this->service_item_data = $service_item_str;
		 	$this->field_list = $field_list;
			$this->alert = parseAlert();		
			$this->display();
		}
	}
	
	public function edit(){
		$widget['date'] = true;
		$widget['uploader'] = true;
		$widget['editor'] = true;
		$this -> assign("widget", $widget);
		
		$leadsB_id = $_POST['leadsB_id'] ? intval($_POST['leadsB_id']) : intval($_REQUEST['id']);
		if(!check_permission($leadsB_id, 'leadsB')) $this->error(L('HAVE NOT PRIVILEGES'));
		$field_list = M('Fields')->where('model = "leadsB"')->order('order_id')->select();
		if($this->isPost()){
			$m_leadsB = M('LeadsB');
			$m_leadsB_data = M('LeadsBData');
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
							$_POST[$v['field']] = implode(chr(10),$_POST[$v['field']]);
						}
					break;
				}
			}
			if($m_leadsB->create()){
				if($m_leadsB_data->create()!==false){
					$m_leadsB->update_time = time();
					
					$service = array_filter($_POST['service']);
					$m_leadsB->service = !empty($service) ? implode(chr(10),$service) : '';
					
					$a = $m_leadsB->where('leadsB_id= %d',$_REQUEST['leadsB_id'])->save();
					$b = $m_leadsB_data->where('leadsB_id=%d',$_REQUEST['leadsB_id'])->save();
					$c = M('Provide')->where(array('leadsB_id'=>$_REQUEST['leadsB_id']))->save(array('provide_name'=>$_POST['name']));
					if($a && $b!==false && $c!==false) {
						actionLog($_REQUEST['leadsB_id']);
						alert('success', L('LEADSB_MODIFIED_SUCCESSFULLY'), U('leadsB/index'));
					} else {
						alert('error', L('LEADSB_MODIFIED_FAILED'), $_SERVER['HTTP_REFERER']);
					}
				}else{
					$this->error($m_leadsB_data->getError());
				}
			}else{
				$this->error($m_leadsB->getError());
			}
		}elseif($_REQUEST['id']){
			$d_v_leadsB = D('LeadsBView')->where('leadsB.leadsB_id = %d',$this->_request('id'))->find();
			$d_v_leadsB['owner'] = D('RoleView')->where('role.role_id = %d', $d_v_leadsB['owner_role_id'])->find();
			if (!$d_v_leadsB) {
				alert('error', L('LEADSB_DOES_NOT_EXIST'),$_SERVER['HTTP_REFERER']);
				die;
			}
			$field_list = field_list_html("edit","leadsB",$d_v_leadsB);
			//服务字段预选
			$service_item_data = array();
			$service_item_str = '';
			$simple_data_mapping = D('OASimpleDataMapping')->where(array('data_code'=>array('like','crm_%')))->select();
			foreach ($simple_data_mapping as $k=>$v){
				$class_id = substr($v['data_code'],4,2);
				$item_id = substr($v['data_code'],-2,2);
				$class_name = $v['data_type'];
				$item_name = $v['data_name'];
				$service_item_data[$class_id][$item_id] = $class_name.'_'.$item_name;
				$service_item_data[$class_id][$item_id] = $class_name.'_'.$item_name;
			}
			foreach ($service_item_data as $k=>$v){
				$temp = explode('_',current($v));
				$service_item_str .= '#'.$temp[0];
				foreach ($v as $kk=>$vv){
					$temp = explode('_',$vv);
					if($kk == '01'){
						$service_item_str .= '$'.$temp[1];
					}else{
						$service_item_str .= '|'.$temp[1];
					}
				}
			}
			$service_item_str = substr($service_item_str,1);
			$this->service_item_data = $service_item_str;
			//服务字段设置
			$service_array = explode(chr(10),$d_v_leadsB['service']);
			$this->service_array = $service_array;
			
			$this->field_list = $field_list;
			$this->leadsB = $d_v_leadsB;
			$this->alert = parseAlert();
			$this->display();
		}else{
			alert('error', L('INVALIDATE_PARAM'),$_SERVER['HTTP_REFERER']);
		}
	}
	
	public function completeDelete() {
		$m_leadsB = M('LeadsB');
		$m_leadsB_data = M('LeadsBData');
		$r_module = array('Log'=>'RLeadsBLog', 'File'=>'RFileLeadsB', 'Event'=>'REventLeadsB', 'Task'=>'RLeadsBTask');
		if($this->isPost()){
			$leadsB_ids = is_array($_POST['leadsB_id']) ? implode(',', $_POST['leadsB_id']) : '';
			if ('' == $leadsB_ids) {
				alert('error', L('NOT CHOOSE ANY'), $_SERVER['HTTP_REFERER']);
			} else {
				if(!session('?admin')){
					alert('error', L('HAVE NOT PRIVILEGES'), $_SERVER['HTTP_REFERER']);
				}
				
				$add_files = $m_leadsB->where('leadsB_id in (%s)', $leadsB_ids)->getField('add_file',true);
				$add_files_string = '';
				foreach ($add_files as $v){
					$add_files_string.=$v;
				}
				$add_files_arr = array_filter(explode(';',$add_files_string));
				
				if(($m_leadsB->where('leadsB_id in (%s)', $leadsB_ids)->delete()) && ($m_leadsB_data->where('leadsB_id in (%s)', $leadsB_ids)->delete())){	
					M('Provide')->where(array('leadsB_id'=>array('in',$leadsB_ids)))->delete();
					foreach ($_POST['leadsB_id'] as $value) {
						actionLog($value);
						foreach ($r_module as $key2=>$value2) {
							$module_ids = M($value2)->where('leadsB_id = %d', $value)->getField($key2 . '_id', true);
							M($value2)->where('leadsB_id = %d', $value) -> delete();
							if(!is_int($key2)){	
								M($key2)->where($key2 . '_id in (%s)', implode(',', $module_ids))->delete();
							}
						}
						//删除附件
						M('File')->where(array('sid'=>array('in',$add_files_arr)))->delete();
					}
					alert('success', L('DELETED SUCCESSFULLY'),U('leadsB/index','by=deleted'));
				} else {
					alert('error', L('DELETE FAILED CONTACT THE ADMINISTRATOR'),$_SERVER['HTTP_REFERER']);
				}
			}
		} elseif($_GET['id']) {
			$leadsB = $m_leadsB->where('leadsB_id = %d', $_GET['id'])->find();
			if (is_array($leadsB)) {
				if($leadsB['owner_role_id'] == session('role_id') || session('?admin')){
					if($m_leadsB->where('leadsB_id = %d', $_GET['id'])->delete()){
						foreach ($r_module as $key2=>$value2) {
							$module_ids = M($value2)->where('leadsB_id = %d', $_GET['id'])->getField($key2 . '_id', true);
							M($value2)->where('leadsB_id = %d', $_GET['id']) -> delete();
							if(!is_int($key2)){
								M($key2)->where($key2 . '_id in (%s)', implode(',', $module_ids))->delete();
							}
						}
						actionLog($_GET['id']);
						alert('success', L('DELETED SUCCESSFULLY'),  U('LeadsB/index','by=deleted'));
					}else{
						alert('error', L('DELETE FAILED CONTACT THE ADMINISTRATOR'), $_SERVER['HTTP_REFERER']);
					}
				} else {
					alert('error', L('HAVE NOT PRIVILEGES'), $_SERVER['HTTP_REFERER']);
				}
			} else {
				alert('error', L('LEADSB_DOES_NOT_EXIST'), $_SERVER['HTTP_REFERER']);
			}			
		} else {
			alert('error', L('SELECT_LEADSB_TO_DELETE'),$_SERVER['HTTP_REFERER']);
		}
	}
	
	public function delete(){
		$m_leadsB = M('LeadsB');
		if($this->isPost()){
			$leadsB_ids = is_array($_POST['leadsB_id']) ? implode(',', $_POST['leadsB_id']) : '';
			if ('' == $leadsB_ids) {
				alert('error', L('NOT CHOOSE ANY'), $_SERVER['HTTP_REFERER']);
			} else {
				$data = array('is_deleted'=>1, 'delete_role_id'=>session('role_id'), 'delete_time'=>time());
				if($m_leadsB->where('leadsB_id in (%s)', $leadsB_ids)->setField($data)){
					foreach($leadsB_ids as $value){
						actionLog($value);
					}
					alert('success', L('DELETED SUCCESSFULLY'),$_SERVER['HTTP_REFERER']);
				} else {
					alert('error', L('DELETE FAILED CONTACT THE ADMINISTRATOR'),$_SERVER['HTTP_REFERER']);
				}
			}
		} elseif($this->isGet()) {
			$leadsB_id = intval(trim($_GET['id']));
			$leadsB = $m_leadsB->where('leadsB_id = %d', $leadsB_id)->find();
			if (is_array($leadsB)) {
				if($leadsB['owner_role_id'] == session('role_id') || session('?admin')){
					$data = array('is_deleted'=>1, 'delete_role_id'=>session('role_id'), 'delete_time'=>time());
					if($m_leadsB->where('leadsB_id = %d', $leadsB_id)->setField($data)){				
						actionLog($leadsB_id);
						//判断线索是否属于线索池
						$outdays = M('config') -> where('name="leadsB_outdays"')->getField('value');
						$outdate = empty($outdays) ? time() : time()-86400*$outdays;						
						if($leadsB['have_time'] < $outdate){						
							alert('success', L('DELETED SUCCESSFULLY'),U('LeadsB/index','by=public'));
						}else{							
							alert('success', L('DELETED SUCCESSFULLY'),U('LeadsB/index'));
						}		
					}else{
						alert('error', L('DELETE FAILED CONTACT THE ADMINISTRATOR'), $_SERVER['HTTP_REFERER']);
					}
				} else {
					alert('error', L('HAVE NOT PRIVILEGES'), $_SERVER['HTTP_REFERER']);
				}
					
			} else {
				alert('error', L('LEADSB_DOES_NOT_EXIST'), $_SERVER['HTTP_REFERER']);
			}			
		} 
	} 
	
	public function index(){
		$by = isset($_GET['by']) ? trim($_GET['by']) : '';
		$p = isset($_GET['p']) ? intval($_GET['p']) : 1 ;
		$below_ids = getSubRoleId(false);
		$below_ids = empty($below_ids) ? -1 : $below_ids;
		$d_v_leadsB = D('LeadsBView');
		$outdays = M('config') -> where('name="leadsB_outdays"')->getField('value');
		$outdate = empty($outdays) ? time() : time()-86400*$outdays;
		$where = array();
		$params = array();
		$order = "create_time desc";
		$where['have_time'] = array('egt',$outdate);
		
		if($_GET['desc_order']){
			$order = trim($_GET['desc_order']).' desc';
		}elseif($_GET['asc_order']){
			$order = trim($_GET['asc_order']).' asc';
		}
		
		switch ($by) {
			case 'today' :
				$where['nextstep_time'] =  array(array('lt',strtotime(date('Y-m-d', time()))+86400), array('gt',0), 'and'); 
				break;
			case 'week' : 
				$where['nextstep_time'] =  array(array('lt',strtotime(date('Y-m-d', time())) + (7 - date('N', time())) * 86400), array('gt', 0),'and'); 
				break;
			case 'month' : 
				$where['nextstep_time'] =  array(array('lt',strtotime(date('Y-m-01', strtotime('+1 month')))), array('gt', 0),'and'); 
				break;
			case 'd7' : 
				$where['update_time'] =  array('lt',strtotime(date('Y-m-d', time()))-86400*6); 
				break;
			case 'd15' : 
				$where['update_time'] =  array('lt',strtotime(date('Y-m-d', time()))-86400*14); 
				break;
			case 'd30' : 
				$where['update_time'] =  array('lt',strtotime(date('Y-m-d', time()))-86400*29); 
				break;
			case 'add' : $order = 'create_time desc';  break;
			case 'update' : $order = 'update_time desc';  break;
			case 'sub' : $where['creator_role_id'] = array('in',implode(',', $below_ids)); break;
			case 'subcreate' : $where['creator_role_id'] = array('in',implode(',', $below_ids)); break;
			case 'public' :
				unset($where['have_time']);
				$where['_string'] = "leadsB.owner_role_id=0 or leadsB.have_time < $outdate";
				break;
			case 'deleted' : $where['is_deleted'] = 1;unset($where['have_time']); break;
			case 'transformed' : $where['is_transformed'] = 1; break;
			case 'me' : $where['creator_role_id'] = session('role_id'); break;
			default : $where['creator_role_id'] = array('in',implode(',', getSubRoleIdByYuan())); break;
		}
		if ($by != 'deleted') {
			$where['is_deleted'] = array('neq',1);
		}
		if ($by != 'transformed' && $by != 'deleted') {
			$where['is_transformed'] = array('neq',1);
		}
// 		dump(getSubRoleIdByYuan());
// 		die;
		if (!isset($where['creator_role_id'])) {
			if(!isset($where['_string'])) $where['creator_role_id'] = array('in', implode(',', getSubRoleIdByYuan(true)));
			else $where['creator_role_id'] = array('in', '0,'.implode(',', getSubRoleIdByYuan(true)));
		}
		
		if ($_REQUEST["field"]) {
			if (trim($_REQUEST['field']) == "all") {
				$field = is_numeric(trim($_REQUEST['search'])) ? 'name|owner_role_id|company|position|saltname|phone|mobile|email|qq|fax|website|source|status|industry|state|zip_code|city|state|description|annual_revenue|no_of_employees|' : 'name|owner_role_id|company|position|saltname|phone|mobile|email|qq|fax|website|source|status|industry|state|zip_code|city|state|description';
			} else {
				$field = trim($_REQUEST['field']);
			}
			
			$field_date = M('Fields')->where('is_main=1 and (model="" or model="leadsB") and form_type="datetime"')->select();
			foreach($field_date as $v){
				if	($field == $v['field']) $search = is_numeric($search)?$search:strtotime($search);
			}
            if ($this->_request('state')){
				$search = $this->_request('state');
				if($this->_request('city')){
					$search .= chr(10) . $this->_request('city');
				}
				if($search){
					$search .= chr(10) .trim($_REQUEST['search']);
				}
			}
			
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
			$params = array('field='.trim($_REQUEST['field']), 'condition='.$condition, 'search='.$_REQUEST["search"]);
		}
		if(trim($_GET['act'] == 'sms')){
			$customerB_list = $d_v_leadsB->where($where)->select();
			$contacts = array();
			foreach ($customerB_list as $k => $v) {
				$contacts[] = array('name'=>$v['contacts_name'], 'customerB_name'=>$v['name'], 'telephone'=>trim($v['mobile']));
			}
			$this->contacts = $contacts;
			$this->alert = parseAlert();
			$this->display('Setting:sendsms');
		}elseif(trim($_GET['act']) == 'excel'){
			if(vali_permission('leadsB', 'export')){
				$order = $order ? $order : 'create_time desc';
				$dc_id = $_GET['daochu'];
				if($dc_id !=''){
					$where['business_id'] = array('in',$dc_id);
				}
				$current_page = intval($_GET['current_page']);
				$export_limit = intval($_GET['export_limit']);
				$limit = ($export_limit*($current_page-1)).','.$export_limit;
				$leadsBList = $d_v_leadsB->where($where)->order($order)->limit($limit)->select();	
				session('export_status', 1);
				$this->excelExport($leadsBList);
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
			$list = $d_v_leadsB->where($where)->page($p.','.$listrows)->order($order)->select();
			$count = $d_v_leadsB->where($where)->count();
			import("@.ORG.Page");
			$Page = new Page($count,$listrows);
			if (!empty($_GET['by'])) {
				$params[] = 'by='.trim($_GET['by']);
			}
			
			$this->parameter = implode('&', $params);

			if ($_GET['desc_order']) {
				$params[] = "desc_order=" . trim($_GET['desc_order']);
			} elseif($_GET['asc_order']){
				$params[] = "asc_order=" . trim($_GET['asc_order']);
			}
			
			$Page->parameter = implode('&', $params);

			$this->assign('page', $Page->show());

			if($by == 'deleted') {
				foreach ($list as $k => $v) {
					$list[$k]["delete_role"] = getUserByRoleId($v['delete_role_id']);
					$list[$k]["owner"] = getUserByRoleId($v['owner_role_id']);
					$list[$k]["creator"] = getUserByRoleId($v['creator_role_id']);
					$list[$k]["creator"]["Dept_3_Name"] = getDept_3_Name($list[$k]["creator"]["role_id"]);
				}
			}elseif($by == 'transformed'){
				foreach ($list as $k => $v) {
					$list[$k]["owner"] = getUserByRoleId($v['owner_role_id']);
					$list[$k]["creator"] = getUserByRoleId($v['creator_role_id']);				
					$list[$k]["creator"]["Dept_3_Name"] = getDept_3_Name($list[$k]["creator"]["role_id"]);
					$list[$k]["transform_role"] = getUserByRoleId($v['transform_role_id']);
					$list[$k]["business_name"] = M('business')->where('business_id = %d', $v['business_id'])->getField('name');
					$list[$k]["contacts_name"] = M('contacts')->where('contacts_id = %d', $v['contacts_id'])->getField('name');
					$list[$k]["customerB_name"] = M('customerB')->where('customerB_id = %d', $v['customerB_id'])->getField('name');
				}
			}else{
				foreach ($list as $k => $v) {
					$days = 0;
					$list[$k]["owner"] = D('RoleView')->where('role.role_id = %d', $v['owner_role_id'])->find();
					$list[$k]["creator"] = D('RoleView')->where('role.role_id = %d', $v['creator_role_id'])->find();
					$list[$k]["creator"]["Dept_3_Name"] = getDept_3_Name($list[$k]["creator"]["role_id"]);
					$days =  M('leadsB')->where('leadsB_id = %d', $v['leadsB_id'])->getField('have_time');
					$list[$k]["days"] = $outdays-floor((time()-$days)/86400);
				}
			}
			//get subordinate's and youself position list
			$d_role_view = D('RoleView');
			$this->role_list = $d_role_view->where('role.role_id in (%s)', implode(',', $below_ids))->select();
			$this->assign('leadsBlist',$list);
			$this->assign('count',$count);
			$this->listrows = $listrows;
			$this->field_array = getIndexFields('leadsB');
			$this->field_list = getMainFields('leadsB');
			$this->alert = parseAlert();
			$this->display();
		}
	}
	public function client_index(){
		$by = isset($_GET['by']) ? trim($_GET['by']) : '';
		$p = isset($_GET['p']) ? intval($_GET['p']) : 1 ;
		$below_ids = getSubRoleId(false);
		$below_ids = empty($below_ids) ? -1 : $below_ids;
		$d_v_leadsB = D('LeadsBView');
		$outdays = M('config') -> where('name="leadsB_outdays"')->getField('value');
		$outdate = empty($outdays) ? time() : time()-86400*$outdays;
		$where = array();
		$params = array();
		$order = "create_time desc";
		$where['have_time'] = array('egt',$outdate);
	
		if($_GET['desc_order']){
			$order = trim($_GET['desc_order']).' desc';
		}elseif($_GET['asc_order']){
			$order = trim($_GET['asc_order']).' asc';
		}
	
		switch ($by) {
			case 'today' :
				$where['nextstep_time'] =  array(array('lt',strtotime(date('Y-m-d', time()))+86400), array('gt',0), 'and');
				break;
			case 'week' :
				$where['nextstep_time'] =  array(array('lt',strtotime(date('Y-m-d', time())) + (7 - date('N', time())) * 86400), array('gt', 0),'and');
				break;
			case 'month' :
				$where['nextstep_time'] =  array(array('lt',strtotime(date('Y-m-01', strtotime('+1 month')))), array('gt', 0),'and');
				break;
			case 'd7' :
				$where['update_time'] =  array('lt',strtotime(date('Y-m-d', time()))-86400*6);
				break;
			case 'd15' :
				$where['update_time'] =  array('lt',strtotime(date('Y-m-d', time()))-86400*14);
				break;
			case 'd30' :
				$where['update_time'] =  array('lt',strtotime(date('Y-m-d', time()))-86400*29);
				break;
			case 'add' : $order = 'create_time desc';  break;
			case 'update' : $order = 'update_time desc';  break;
			case 'sub' : $where['owner_role_id'] = array('in',implode(',', $below_ids)); break;
			case 'subcreate' : $where['creator_role_id'] = array('in',implode(',', $below_ids)); break;
			case 'public' :
				unset($where['have_time']);
				$where['_string'] = "leadsB.owner_role_id=0 or leadsB.have_time < $outdate";
				break;
			case 'deleted' : $where['is_deleted'] = 1;unset($where['have_time']); break;
			case 'transformed' : $where['is_transformed'] = 1; break;
			case 'me' : $where['owner_role_id'] = session('role_id'); break;
			default : $where['owner_role_id'] = array('in',implode(',', getSubRoleId())); break;
		}
		if ($by != 'deleted') {
			$where['is_deleted'] = array('neq',1);
		}
		if ($by != 'transformed' && $by != 'deleted') {
			$where['is_transformed'] = array('neq',1);
		}
		if (!isset($where['owner_role_id'])) {
			if(!isset($where['_string'])) $where['owner_role_id'] = array('in', implode(',', getSubRoleId(true)));
			else $where['owner_role_id'] = array('in', '0,'.implode(',', getSubRoleId(true)));
		}
	
		if ($_REQUEST["field"]) {
			if (trim($_REQUEST['field']) == "all") {
				$field = is_numeric(trim($_REQUEST['search'])) ? 'name|owner_role_id|company|position|saltname|phone|mobile|email|qq|fax|website|source|status|industry|state|zip_code|city|state|description|annual_revenue|no_of_employees|' : 'name|owner_role_id|company|position|saltname|phone|mobile|email|qq|fax|website|source|status|industry|state|zip_code|city|state|description';
			} else {
				$field = trim($_REQUEST['field']);
			}
				
			$field_date = M('Fields')->where('is_main=1 and (model="" or model="leadsB") and form_type="datetime"')->select();
			foreach($field_date as $v){
				if	($field == $v['field']) $search = is_numeric($search)?$search:strtotime($search);
			}
			if ($this->_request('state')){
				$search = $this->_request('state');
				if($this->_request('city')){
					$search .= chr(10) . $this->_request('city');
				}
				if($search){
					$search .= chr(10) .trim($_REQUEST['search']);
				}
			}
				
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
			$params = array('field='.trim($_REQUEST['field']), 'condition='.$condition, 'search='.$_REQUEST["search"]);
		}
		if(trim($_GET['act'] == 'sms')){
			$customerB_list = $d_v_leadsB->where($where)->select();
			$contacts = array();
			foreach ($customerB_list as $k => $v) {
				$contacts[] = array('name'=>$v['contacts_name'], 'customerB_name'=>$v['name'], 'telephone'=>trim($v['mobile']));
			}
			$this->ajaxReturn(array('list'=>$contacts,'count'=>count($contacts)), '', 1);
// 			$this->contacts = $contacts;
// 			$this->alert = parseAlert();
// 			$this->display('Setting:sendsms');
		}elseif(trim($_GET['act']) == 'excel'){
			if(vali_permission('leadsB', 'export')){
				$order = $order ? $order : 'create_time desc';
				$dc_id = $_GET['daochu'];
				if($dc_id !=''){
					$where['business_id'] = array('in',$dc_id);
				}
				$current_page = intval($_GET['current_page']);
				$export_limit = intval($_GET['export_limit']);
				$limit = ($export_limit*($current_page-1)).','.$export_limit;
				$leadsBList = $d_v_leadsB->where($where)->order($order)->limit($limit)->select();
				session('export_status', 1);
				$this->ajaxReturn(array('list'=>$leadsBList,'count'=>count($leadsBList)), '', 1);
// 				$this->excelExport($leadsBList);
			}else{
				$this->ajaxReturn(null, L('HAVE NOT PRIVILEGES'), 0);
			}
		}else{
			if($_GET['listrows']){
				$listrows = $_GET['listrows'];
				$params[] = "listrows=" . trim($_GET['listrows']);
			}else{
				$listrows = 15;
				$params[] = "listrows=15";
			}
			$list = $d_v_leadsB->where($where)->page($p.','.$listrows)->order($order)->select();
			$count = $d_v_leadsB->where($where)->count();
			import("@.ORG.Page");
			$Page = new Page($count,$listrows);
			if (!empty($_GET['by'])) {
				$params[] = 'by='.trim($_GET['by']);
			}
				
			$this->parameter = implode('&', $params);
	
			if ($_GET['desc_order']) {
				$params[] = "desc_order=" . trim($_GET['desc_order']);
			} elseif($_GET['asc_order']){
				$params[] = "asc_order=" . trim($_GET['asc_order']);
			}
				
			$Page->parameter = implode('&', $params);
	
			$this->assign('page', $Page->show());
	
			if($by == 'deleted') {
				foreach ($list as $k => $v) {
					$list[$k]["delete_role"] = getUserByRoleId($v['delete_role_id']);
					$list[$k]["owner"] = getUserByRoleId($v['owner_role_id']);
					$list[$k]["creator"] = getUserByRoleId($v['creator_role_id']);
				}
			}elseif($by == 'transformed'){
				foreach ($list as $k => $v) {
					$list[$k]["owner"] = getUserByRoleId($v['owner_role_id']);
					$list[$k]["creator"] = getUserByRoleId($v['creator_role_id']);
					$list[$k]["transform_role"] = getUserByRoleId($v['transform_role_id']);
					$list[$k]["business_name"] = M('business')->where('business_id = %d', $v['business_id'])->getField('name');
					$list[$k]["contacts_name"] = M('contacts')->where('contacts_id = %d', $v['contacts_id'])->getField('name');
					$list[$k]["customerB_name"] = M('customerB')->where('customerB_id = %d', $v['customerB_id'])->getField('name');
				}
			}else{
				foreach ($list as $k => $v) {
					$days = 0;
					$list[$k]["owner"] = D('RoleView')->where('role.role_id = %d', $v['owner_role_id'])->find();
					$list[$k]["creator"] = D('RoleView')->where('role.role_id = %d', $v['creator_role_id'])->find();
					$days =  M('leadsB')->where('leadsB_id = %d', $v['leadsB_id'])->getField('have_time');
					$list[$k]["days"] = $outdays-floor((time()-$days)/86400);
				}
			}
			//get subordinate's and youself position list
			$d_role_view = D('RoleView');
			$this->role_list = $d_role_view->where('role.role_id in (%s)', implode(',', $below_ids))->select();
			$this->ajaxReturn(array('list'=>$list,'count'=>$count), '', 1);
// 			$this->assign('leadsBlist',$list);
// 			$this->assign('count',$count);
// 			$this->listrows = $listrows;
// 			$this->field_array = getIndexFields('leadsB');
// 			$this->field_list = getMainFields('leadsB');
// 			$this->alert = parseAlert();
// 			$this->display();
		}
	}
	public function view(){		
		$widget['date'] = true;
		$widget['uploader'] = true;
		$widget['editor'] = true;
		$this -> assign("widget", $widget);
		
		$leadsB_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
		if(!check_permission($leadsB_id, 'leadsB')) $this->error(L('HAVE NOT PRIVILEGES'));
		if (0 == $leadsB_id) {
			alert('error', L('PARAMETER_ERROR'), U('leadsB/index'));
		} else {
			$leadsB = D('LeadsBView')->where('leadsB.leadsB_id = %d', $leadsB_id)->find();
			$field_list = M('Fields')->where('model = "leadsB"')->order('order_id')->select();
// 			$leadsB['owner'] = D('RoleView')->where('role.role_id = %d', $leadsB['owner_role_id'])->find();
			$leadsB['creator'] = D('RoleView')->where('role.role_id = %d', $leadsB['creator_role_id'])->find();
			$log_ids = M('rLeadsBLog')->where('leadsB_id = %d', $leadsB_id)->getField('log_id', true);
			$leadsB['log'] = M('log')->where('log_id in (%s)', implode(',', $log_ids))->select();
			$log_count = 0;
			foreach ($leadsB['log'] as $key=>$value) {
				$leadsB['log'][$key]['owner'] = D('RoleView')->where('role.role_id = %d', $value['role_id'])->find();
				$log_count ++;
			}
			$leadsB['log_count'] = $log_count;
			
			$file_ids = M('rFileLeadsB')->where('leadsB_id = %d', $leadsB_id)->getField('file_id', true);
			$leadsB['file'] = M('file')->where('file_id in (%s)', implode(',', $file_ids))->select();
			$file_count = 0;
			foreach ($leadsB['file'] as $key=>$value) {
				$leadsB['file'][$key]['owner'] = D('RoleView')->where('role.role_id = %d', $value['role_id'])->find();
				$file_count ++;
			}
			$leadsB['file_count'] = $file_count;
			
			$task_ids = M('rLeadsBTask')->where('leadsB_id = %d', $leadsB_id)->getField('task_id', true);
			$leadsB['task'] = M('task')->where('task_id in (%s) and is_deleted = 0', implode(',', $task_ids))->select();
			$task_count = 0;
			foreach ($leadsB['task'] as $key=>$value) {
				$leadsB['task'][$key]['owner'] = D('RoleView')->where('role.role_id in (%s)', '0'.$value['owner_role_id'].'0')->select();
				$leadsB['task'][$key]['about_roles'] = D('RoleView')->where('role.role_id in (%s)', '0'.$value['about_roles'].'0')->select();
				$task_count ++;
			}
			$leadsB['task_count'] = $task_count;
			
			$event_ids = M('rEventLeadsB')->where('leadsB_id = %d', $leadsB_id)->getField('event_id', true);
			$leadsB['event'] = M('event')->where('event_id in (%s)', implode(',', $event_ids))->select();
			$event_count = 0;
			foreach ($leadsB['event'] as $key=>$value) {
				$leadsB['event'][$key]['owner'] = D('RoleView')->where('role.role_id = %d', $value['owner_role_id'])->find();
				$event_count++;
			}
			$leadsB['event_count'] = $event_count;
            $leadsB['record'] = M('leadsBRecord')->where('leadsB_id = %d', $leadsB_id)->select();
			$record_count = 0;
			foreach ($leadsB['record'] as $key=>$value) {
				$leadsB['record'][$key]['owner'] = D('RoleView')->where('role.role_id = %d', $value['owner_role_id'])->find();
				$record_count ++;
			}
			$leadsB['record_count'] = $record_count;
			
			$contactsB_ids = M('rContactsBLeadsB')->where('leadsB_id = %d', $leadsB_id)->getField('contactsB_id', true);
			$leadsB['contactsB'] = M('contactsB')->where('contactsB_id in (%s) and is_deleted=0', implode(',', $contactsB_ids))->select();
			foreach($leadsB['contactsB'] as $k=>$v){
				if(M('LeadsB')->where('contactsB_id = %d',$v['contactsB_id'])->select()){
					$leadsB['contactsB'][$k]['is_firstContact'] = 'true';
				}else{
					$leadsB['contactsB'][$k]['is_firstContact'] = 'false';
				}
			}
			
			$contactsB_count = M('contactsB')->where('contactsB_id in (%s) and is_deleted=0', implode(',', $contactsB_ids))->count();
			$leadsB['contactsB_count'] = empty($contactsB_count)?0:$contactsB_count;
			
			$this->statusList = M('BusinessStatus')->order('order_id')->select();
			$this->leadsB = $leadsB;
			
			//服务字段设置
			$service_array = explode(chr(10),$leadsB['service']);
			$this->service_array = $service_array;

			$this->field_list = $field_list;
			$this->alert = parseAlert();
			$this->display();
		}
	}
	
	public function transform(){
		if ($this->isPost()) {
			$leadsB_id = isset($_POST['leadsB_id']) ? $_POST['leadsB_id'] : 0;
			if ($leadsB_id != 0) {
				$m_leadsB = M('LeadsB');
				$m_customerB = M('CustomerB');
				$m_contacts = M('Contacts');
				$m_business = M('Business');
				$m_r = M('RContactsCustomerB');
				$r_module = array(
					array('key'=>'log_id','r1'=>'RCustomerBLog','r2'=>'RLeadsBLog'), 
					array('key'=>'file_id','r1'=>'RCustomerBFile','r2'=>'RFileLeadsB'),
					array('key'=>'event_id','r1'=>'RCustomerBEvent','r2'=>'REventLeadsB'),
					array('key'=>'task_id','r1'=>'RCustomerBTask','r2'=>'RLeadsBTask')
				);
				$leadsB = $m_leadsB->where('leadsB_id = %d',$leadsB_id)->find();
				if(($leadsB['owner_role_id'] != session('role_id')) && !session('?admin')){
					alert('error', L('ONLY_OWNER_CAN_CONVERT_LEADSB'), $_SERVER['HTTP_REFERER']);
				}
				if($leadsB['name'] && $leadsB['company']) {
					if($m_customerB->where('name = "%s"', $leadsB['company'])->find()){
						alert('error', L('CONVERT_LEADSB_FAILED_FOR_EXIST_CUSTOMERB'), $_SERVER['HTTP_REFERER']);
					}
					!empty($leadsB['company']) ? $customerB['name'] = $leadsB['company'] : '';	
					!empty($leadsB['email']) ? $customerB['email'] = $leadsB['email'] : '';
					!empty($leadsB['phone']) ? $customerB['telephone'] = $leadsB['phone'] : '';
					!empty($leadsB['source_id']) ? $customerB['source_id'] = $leadsB['source_id'] : '';
					intval($_POST['owner_role_id']) > 0 ? $customerB['owner_role_id'] = $_POST['owner_role_id'] : '';
					!empty($leadsB['website']) ? $customerB['website'] = $leadsB['website'] : '';
					!empty($leadsB['industry_id']) ? $customerB['industry_id'] = $leadsB['industry_id'] : '';
					!empty($leadsB['annual_revenue']) ? $customerB['annual_revenue'] = $leadsB['annual_revenue'] : '';
					!empty($leadsB['no_of_employees']) ? $customerB['no_of_employees'] = $leadsB['no_of_employees'] : '';
					(!empty($leadsB['state'])&&!empty($leadsB['city'])&&!empty($leadsB['street'])) ? $customerB['address'] = $leadsB['state'] . $leadsB['city'] . $leadsB['street'] : '';
					!empty($leadsB['zip_code']) ? $customerB['zip_code'] = $leadsB['zip_code'] : '';
					!empty($leadsB['rating']) ? $customerB['rating'] = $leadsB['rating'] : '';
					$customerB['creator_role_id'] = session('role_id');
					!empty($leadsB['ownership']) ? $customerB['ownership'] = $leadsB['ownership'] : '';
					$customerB['create_time'] = time();
					$customerB['update_time'] = time();
					if(!$customerB_id = $m_customerB->add($customerB)){
						alert('error', L('CONVERT_LEADSB_FAILED_CONTACTS_ADMINISTRATOR'), $_SERVER['HTTP_REFERER']);
					};
					!empty($leadsB['name']) ? $contacts['name'] = $leadsB['name'] : '';
					!empty($leadsB['saltname']) ? $contacts['saltname'] = $leadsB['saltname'] : '';
					!empty($leadsB['position']) ? $contacts['post'] = $leadsB['position'] : '';
					!empty($leadsB['mobile']) ? $contacts['telephone'] = $leadsB['mobile'] : '';
					!empty($leadsB['email']) ? $contacts['email'] = $leadsB['email'] : '';
					!empty($leadsB['qq']) ? $contacts['qq'] = $leadsB['qq'] : '';
					(!empty($leadsB['state'])&&!empty($leadsB['city'])&&!empty($leadsB['street'])) ? $contacts['address'] = $leadsB['state'] . $leadsB['city'] . $leadsB['street'] : '';
					!empty($leadsB['zip_code']) ? $contacts['zip_code'] = $leadsB['zip_code'] : '';
					!empty($leadsB['name']) ? $contacts['name'] = $leadsB['name'] : '';
					(intval($_POST['owner_role_id'])>0) ? $contacts['owner_role_id'] = intval($_POST['owner_role_id']):'';	
					$contacts['creator_role_id'] = session('role_id');
					$contacts['customerB_id'] = $customerB_id;
					$contacts['create_time'] = time();
					$contacts['update_time'] = time();
					if($contacts_id = $m_contacts->add($contacts)){
						$data['customerB_id'] = $customerB_id;
						$data['contacts_id'] = $contacts_id;
						$data['transform_role_id'] = session('role_id');
						$data['is_transformed'] = 1;
						$data['update_time'] = time();
						$m_leadsB->where('leadsB_id = %d', $leadsB_id)->save($data);
						$m_r->add($data);
						$data['business_id'] = $customerB_id;
						$m_leadsB->where('leadsB_id = %d',$leadsB_id)->save($data);
						foreach ($r_module as $key=>$value) {
							$key_id_array = M($value['r2'])->where('leadsB_id = %d', $leadsB_id)->getField($value['key'],true);
							$r1 = M($value['r1']);
							$data['customerB_id'] = $customerB_id;
							foreach($key_id_array as $k=>$v){
								$data[$value['key']] = $v;
								$r1->add($data);
							}
						}
						if($_POST['business_name'] == "" || $_POST['business_name'] == null){
							alert('success', L('CONVERT_LEADSB_SUCCESSFULY'), U('leadsB/index'));
						}				
					}else{
						alert('error', L('CONVERT_LEADSB_FAILED_CONTACTS_ADMINISTRATOR'), $_SERVER['HTTP_REFERER']);
					}					
					//if create business successfully and the execute this
					if ($_POST['business_name']) {
						if ($m_business->create()) {
							$m_business->creator_role_id = session('role_id');
							$m_business->origin = $leadsB['source'];
							$m_business->name = $_POST['business_name'];
							$m_business->contacts_id = $contacts_id;
							if($_POST['due_date']) $m_business->due_date = strtotime($_POST['due_date']);
							$m_business->customerB_id = $customerB_id;
							$m_business->create_time = time();
							$m_business->update_time = time();
							if ($business_id = $m_business->add()) {
								alert('success', L('CONVERT_LEADSB_SUCCESSFULLY'), U('leadsB/index'));
							} else {
								alert('error', L('CREATE SUCCESS',array(L('BUSINESS'))), $_SERVER['HTTP_REFERER']);
							}
						} else {
							alert('error', L('CREATE FAILED',array(L('BUSINESS'))), $_SERVER['HTTP_REFERER']);
						}
					} else {
						$data['customerB_id'] = $customerB_id;
						$m_leadsB->where('leadsB_id = %d',$leadsB_id)->save($data);
						foreach ($r_module as $key=>$value) {
							$key_id_array = M($value['r2'])->where('leadsB_id = %d', $leadsB_id)->getField($value['key'],true);
							$r1 = M($value['r1']);
							foreach($key_id_array as $k=>$v){
								$data[$value['key']] = $v;
								$r1->add($data);
							}
						}
					
						$m_leadsB->where('leadsB_id = %d', $leadsB_id)->save($data);
						alert('success', L('CONVERT_LEADSB_SUCCESSFULLY'), U('leadsB/index'));
					}
				}else{
					alert('error', L('CONVERT_LEADSB_FAILED_FOR_INCOMPLETE_INFO'), $_SERVER['HTTP_REFERER']);
				}
			} else {
				alert('error', L('PARAMETER_ERROR'), $_SERVER['HTTP_REFERER']);
			}
		} else {
			alert('error', L('SELECT_LEADSB_TO_CONVERT'), $_SERVER['HTTP_REFERER']);
		}
	}
	
	public function excelExport($leadsBList=false){
		C('OUTPUT_ENCODE', false);
		import("ORG.PHPExcel.PHPExcel");
		$objPHPExcel = new PHPExcel();    
		$objProps = $objPHPExcel->getProperties();    
		$objProps->setCreator("crm");    
		$objProps->setLastModifiedBy("crm");    
		$objProps->setTitle("crm LeadsB Data");    
		$objProps->setSubject("crm LeadsB Data");    
		$objProps->setDescription("crm LeadsB Data");    
		$objProps->setKeywords("crm LeadsB Data");    
		$objProps->setCategory("LeadsB");
		$objPHPExcel->setActiveSheetIndex(0);     
		$objActSheet = $objPHPExcel->getActiveSheet(); 
		   
		$objActSheet->setTitle('Sheet1');
        $ascii = 65;
        $cv = '';
        $field_list = M('Fields')->where('model = \'leadsB\'')->order('order_id')->select();
        foreach($field_list as $field){
            $objActSheet->setCellValue($cv.chr($ascii).'1', $field['name']);
            $ascii++;
            if($ascii == 91){
                $ascii = 65;
                $cv .= chr(strlen($cv)+65);
            }
        }
		
		if(is_array($leadsBList)){
			$list = $leadsBList;
		}else{
			$where['owner_role_id'] = array('in',implode(',', getSubRoleId()));
			$where['is_deleted'] = 0;
			$list = M('LeadsB')->where($where)->select();
		}
		
		$i = 1;
		foreach ($list as $k => $v) {
            $data = M('LeadsBData')->where("leadsB_id = $v[leadsB_id]")->find();
            if(!empty($data)){
                $v = $v+$data;
            }
			$i++;
            $ascii = 65;
            $cv = '';
            foreach($field_list as $field){
                if($field['form_type'] == 'datetime'){
					if($v[$field['field']] == 0 || strlen($v[$field['field']]) != 10){
						$objActSheet->setCellValue($cv.chr($ascii).$i, '');
					}else{
						$objActSheet->setCellValue($cv.chr($ascii).$i, date('Y-m-d',$v[$field['field']]));
					}
                }elseif($field['form_type'] == 'number' || $field['form_type'] == 'floatnumber' || $field['form_type'] == 'phone' || $field['form_type'] == 'mobile' || ($field['form_type'] == 'text' && is_numeric($v[$field['field']]))){
					//防止使用科学计数法，在数据前加空格
					$objActSheet->setCellValue($cv.chr($ascii).$i, ' '.$v[$field['field']]);
				}else{
                    $objActSheet->setCellValue($cv.chr($ascii).$i, $v[$field['field']]);
                }
                $ascii++;
                if($ascii == 91){
                    $ascii = 65;
                    $cv .= chr(strlen($cv)+65);
                }
            }
		}
		$current_page = intval($_GET['current_page']);
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		ob_end_clean();
		header("Content-Type: application/vnd.ms-excel;");
        header("Content-Disposition:attachment;filename=crm_leadsB_".date('Y-m-d',mktime())."_".$current_page.".xls");
        header("Pragma:no-cache");
        header("Expires:0");
        $objWriter->save('php://output'); 
		session('export_status', 0);
	}
	public function getCurrentStatus(){
		$this->ajaxReturn(intval(session('export_status')), 'success', 1);
		
	}
	
 	public function excelImportDownload(){
		C('OUTPUT_ENCODE', false);
        import("ORG.PHPExcel.PHPExcel");
		$objPHPExcel = new PHPExcel();    
		$objProps = $objPHPExcel->getProperties();    
		$objProps->setCreator("crm");
		$objProps->setLastModifiedBy("crm");    
		$objProps->setTitle("crm leadsB");    
		$objProps->setSubject("crm leadsB Data");    
		$objProps->setDescription("crm leadsB Data");    
		$objProps->setKeywords("crm leadsB Data");    
		$objProps->setCategory("crm");
		$objPHPExcel->setActiveSheetIndex(0);     
		$objActSheet = $objPHPExcel->getActiveSheet(); 
		   
		$objActSheet->setTitle('Sheet1');
        $ascii = 65;
        $cv = '';
        $field_list = M('Fields')->where('model = \'leadsB\' ')->order('order_id')->select();
        foreach($field_list as $field){
            $objActSheet->setCellValue($cv.chr($ascii).'2', $field['name']);
            $ascii++;
            if($ascii == 91){
                $ascii = 65;
                $cv .= chr(strlen($cv)+65);
            }
        }
		$objActSheet->mergeCells('A1:'.$cv.chr($ascii).'1');
		$objActSheet->getRowDimension('1')->setRowHeight(80);
		$objActSheet->getStyle('A1')->getFont()->getColor()->setARGB('FFFF0000');
		 $objActSheet->getStyle('A1')->getAlignment()->setWrapText(true);
        $content = L('ADRESS');
        $objActSheet->setCellValue('A1', $content);
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		header("Content-Type: application/vnd.ms-excel;");
        header("Content-Disposition:attachment;filename=crm_leadsB.xls");
        header("Pragma:no-cache");
        header("Expires:0");
        $objWriter->save('php://output'); 
    }
	public function excelImport(){
		$m_leadsB = D('LeadsB');
		$m_leadsB_data = D('LeadsBData');
		if($_POST['submit']){
			if (isset($_FILES['excel']['size']) && $_FILES['excel']['size'] != null) {
				import('@.ORG.UploadFile');
				$upload = new UploadFile();
				$upload->maxSize = 20000000;
				$upload->allowExts  = array('xls');
				$dirname = UPLOAD_PATH . date('Ym', time()).'/'.date('d', time()).'/';
				if (!is_dir($dirname) && !mkdir($dirname, 0777, true)) {
					alert('error', L('ATTACHMENTS TO UPLOAD DIRECTORY CANNOT WRITE'), $_SERVER['HTTP_REFERER']);
				}
				$upload->savePath = $dirname;
				if(!$upload->upload()) {
					alert('error', $upload->getErrorMsg(), $_SERVER['HTTP_REFERER']);
				}else{
					$info =  $upload->getUploadFileInfo();
				}
			}
			
			if(is_array($info[0]) && !empty($info[0])){
				$savePath = $dirname . $info[0]['savename'];
			}else{
				alert('error', L('UPLOAD FAILED'), $_SERVER['HTTP_REFERER']);
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
			$highestColumn = $currentSheet->getHighestColumn();//how many columns
            $allColumn = PHPExcel_Cell::columnIndexFromString($highestColumn);
			if ($allRow <= 2) {
				alert('error', L('UPLOAD A FILE WITHOUT A VALID DATA'), $_SERVER['HTTP_REFERER']);
			} else {
				$field_list = M('Fields')->where('model = \'leadsB\'')->order('order_id')->select();
				for($currentRow = 3;$currentRow <= $allRow;$currentRow++){
					$data = array();
					$data['creator_role_id'] = session('role_id');
					$data['owner_role_id'] = intval($_POST['owner_role_id']);
					$data['create_time'] = time();
					$data['update_time'] = time();
					$data['have_time'] = time();
					$ascii = 65;
                    $cv = '';
					foreach($field_list as $field){
                        // $info = (String)$currentSheet->getCell($cv.chr($ascii).$currentRow)->getValue();
						
                        // if ($field['is_main'] == 1){
                            // $data[$field['field']] = ($field['form_type'] == 'datetime' && $info != null) ? intval(PHPExcel_Shared_Date::ExcelToPHP($info))-8*60*60 : $info;
                        // }else{
                            // $data_date[$field['field']] = ($field['form_type'] == 'datetime' && $info != null) ? intval(PHPExcel_Shared_Date::ExcelToPHP($info))-8*60*60 : $info;
                        // }
						
						$cell =$currentSheet->getCell($cv.chr($ascii).$currentRow);
						$info = $cell->getValue();
						if($cell->getDataType()==PHPExcel_Cell_DataType::TYPE_NUMERIC){
							$cellstyleformat=$cell->getParent()->getStyle( $cell->getCoordinate() )->getNumberFormat();

							//formatcode 为 yyyy/m 时间格式
							$formatcode=$cellstyleformat->getFormatCode();
							if (preg_match('/^(\[\$[A-Z]*-[0-9A-F]*\])*[hmsdy]/i', $formatcode)) {
							    $info=gmdate("Y-m-d", PHPExcel_Shared_Date::ExcelToPHP($info));
							}else{
								$info=PHPExcel_Style_NumberFormat::toFormattedString($info,$formatcode);
							}
						}else{
							$info = (String)$cell->getCalculatedValue();
						}
						if ($field['is_main'] == 1){
                            $data[$field['field']] = ($field['form_type'] == 'datetime' && $info != null) ? strtotime($info) : $info;
                        }else{
                            $data_date[$field['field']] = ($field['form_type'] == 'datetime' && $info != null) ? strtotime($info) : $info;
                        }
                       
                        $ascii++;
                        if($ascii == 91){
                            $ascii = 65;
                            $cv .= chr(strlen($cv)+65);
                        }
                    }
			
					if($m_leadsB->create($data) && $m_leadsB_data->create($data_date)) {
						$leadsB_id = $m_leadsB->add();
						$m_leadsB_data->leadsB_id=$leadsB_id;
						$m_leadsB_data->add();
					}else{
						if($this->_post('error_handing','intval',0) == 0){
							alert('error', L('ERROR INTRODUCED INTO THE LINE',array($currentRow,$m_leadsB->getError().$m_leadsB_data->getError())),$_SERVER['HTTP_REFERER']);
						}else{
							$error_message .= L('LINE ERROR',array($currentRow,$m_leadsB->getError().$m_leadsB_data->getError()));
							$m_leadsB->clearError();
							$m_leadsB_data->clearError();
						}
                    }
				}
				alert('success', $error_message.L('IMPORT SUCCESS'), $_SERVER['HTTP_REFERER']);
			}
		} else {
			$this->display();
		}
	}
	
	public function listDialog(){
		$m_leadsB = M('LeadsB');
		$this->leadsBList = $m_leadsB->where('owner_role_id in (%s) and is_deleted = 0 and is_transformed = 0', implode(',', getSubRoleId()))->order('create_time desc')->limit(10)->select();
		$count = $m_leadsB->where('owner_role_id in (%s) and is_deleted = 0 and is_transformed = 0', implode(',', getSubRoleId()))->order('create_time desc')->count();
		$this->total = $count%10 > 0 ? ceil($count/10) : $count/10;
		$this->count_num = $count;
		$this->display();
	}
	
	public function changeContent(){
		if($this->isAjax()){
			$below_ids = getSubRoleId(false);
			$m_leadsB = M('LeadsB');
			$where['is_deleted'] = array('neq',1);
			$where['is_transformed'] = array('neq',1);
			$where['owner_role_id'] = array('in',implode(',', getSubRoleId(true))); 
			
			if ($_REQUEST["field"]) {
				if (trim($_REQUEST['field']) == "all") {
					$field = is_numeric(trim($_REQUEST['search'])) ? 'name|owner_role_id|company|position|saltname|phone|mobile|email|qq|fax|website|source|status|industry|state|zip_code|city|state|description|annual_revenue|no_of_employees|' : 'name|owner_role_id|company|position|saltname|phone|mobile|email|qq|fax|website|source|status|industry|state|zip_code|city|state|description';
				} else {
					$field = trim($_REQUEST['field']);
				}
				
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
			
			$p = !$_REQUEST['p']||$_REQUEST['p']<=0 ? 1 : intval($_REQUEST['p']);
			$list = $m_leadsB->where($where)->page($p.',10')->order('create_time desc')->select();
			$count = $m_leadsB->where($where)->count();
			$data['list'] = $list;
			$data['p'] = $p;
			$data['count'] = $count;
			$data['total'] = $count%10 > 0 ? ceil($count/10) : $count/10;
			$this->ajaxReturn($data,"",1);
		}
	}
	

	public function receive1(){
		$leadsB_id = isset($_GET['id']) ? intval(trim($_GET['id'])) : 0;
		if ($leadsB_id > 0) {
			$m_leadsB = M('LeadsB');
			$leadsB = $m_leadsB->where('leadsB_id = %d', $leadsB_id)->find();
			if (isset($leadsB['owner_role_id']) || $leadsB['owner_role_id'] <= 0) {
				if ($m_leadsB->where('leadsB_id = %d', $leadsB_id)->setField('owner_role_id', session('role_id'))) {
					alert('success', L('RECEIVE_LEADSB_SUCCESSFULLY'), $_SERVER['HTTP_REFERER']);
				} else {
					alert('error', L('RECEIVE_LEADSB_FAILED'), $_SERVER['HTTP_REFERER']);
				}
			} else {
				alert('error', L('RECEIVED_BY_OTHERS'), $_SERVER['HTTP_REFERER']);
			}
		} else {
			alert('error', L('PARAMETER_ERROR'), $_SERVER['HTTP_REFERER']);
		}
	}
	public function remove(){
		if($this->isPost()){
			$m_leadsB = M('leadsB');
			$leadsB_ids = is_array($_POST['leadsB_id']) ? implode(',', $_POST['leadsB_id']) : '';
			if('' == $leadsB_ids){
				alert('error', L('NOT CHOOSE ANY'), $_SERVER['HTTP_REFERER']);
			}
			if($m_leadsB->where('leadsB_id in (%s)', $leadsB_ids)->setField('owner_role_id',0)){
				alert('success', L('BATCH_LEADSB_INTO_THE_POOL_SUCCESSFULLY'), $_SERVER['HTTP_REFERER']);
			}else{
				alert('error', L('BATCH_LEADSB_INTO_THE_POOL_FAILED'), $_SERVER['HTTP_REFERER']);
			}
			
		}
	}
	public function receive(){
		$leadsB_id = isset($_REQUEST['id']) ? intval(trim($_REQUEST['id'])) : 0;
		if($_REQUEST['owner_role_id']) {
			$owner_role_id = intval($_REQUEST['owner_role_id']);
		}else{
			$owner_role_id = session('role_id');
		}
		if ($leadsB_id > 0) {
			$m_leadsB = M('LeadsB');
			$m_config = M('Config');
			$leadsB = $m_leadsB->where('leadsB_id = %d', $leadsB_id)->find();
			$config = $m_config->where(array('name'=>'leadsB_outdays'))->find();
			if((time() - $leadsB['have_time']) < ($config['value'] * 86400) && $leadsB['owner_role_id'] != 0 ){
				alert('error', L('RECEIVED_BY_SOMEONE',array($leadsB['name'])), $_SERVER['HTTP_REFERER']);
			}
			$a = $m_leadsB->where('leadsB_id = %d', $leadsB_id)->setField('owner_role_id', $owner_role_id);
			$b = $m_leadsB->where('leadsB_id = %d',$leadsB_id)->setField('have_time',time());
			if ($a || $b) {
				$d = array('leadsB_id'=>$leadsB_id,'owner_role_id'=>$owner_role_id,'start_time'=>time());
				M('LeadsBRecord')->data($d)->add();
				$title=L('NEW_LEADSB_MESSAGE_NOTICE_TITLE');
				$content=L('NEW_LEADSB_MESSAGE_NOTICE_CONTENT',array(session('name'),U('LeadsB/view','id='.$leadsB_id), $leadsB['name']));
				
				if(intval($_POST['message_alert']) == 1) {
					sendMessage($owner_role_id,$content,1);
				}
				if(intval($_POST['email_alert']) == 1){
					$email_result = sysSendEmail($owner_role_id,$title,$content);
					if(!$email_result) alert('error', L('MAIL_NOTIFICATION_FAILS_FOR_NOT_SET_EMAIL'),$_SERVER['HTTP_REFERER']);
				}
				if(intval($_POST['sms_alert']) == 1){
					$sms_result = sysSendSms($owner_role_id,$content);
					if(100 == $sms_result){
						alert('error', L('SMS_NOTIFICATION_FAILS_FOR_NOT_VALIDATE_NUMBER'),$_SERVER['HTTP_REFERER']);
					}elseif($sms_result < 0){
						alert('error',L('SMS_NOTIFICATION_FAILS_CODE', array($sms_result)), $_SERVER['HTTP_REFERER']);
					}
				}
				
				if($_REQUEST['owner_role_id']){
					alert('success', L('ASSIGN_LEADSB_SUCCESSFULLY'), $_SERVER['HTTP_REFERER']);
				}else{
					alert('success', L('RECEIVE_LEADSB_SUCCESSFULLY'), $_SERVER['HTTP_REFERER']);
				}
			} else {
				if($_REQUEST['owner_role_id']){
					alert('success', L('ASSIGN_LEADSB_FAILED'), $_SERVER['HTTP_REFERER']);
				}else{
					alert('success', L('RECEIVE_LEADSB_FAILED'), $_SERVER['HTTP_REFERER']);
				}
			}
		} else {
			alert('error', L('PARAMETER_ERROR'), $_SERVER['HTTP_REFERER']);
		}
	}
	
	//batchReceive
	public function batchReceive(){
		$leadsB_ids = $_REQUEST['leadsB_id'];
		$owner_role_id = session('role_id');
		if(empty($leadsB_ids)){
			alert('error', L('NOT CHOOSE ANY'), $_SERVER['HTTP_REFERER']);
		}
		$m_leadsB = M('LeadsB');
		$m_config = M('Config');
		foreach($leadsB_ids as $v){
			$leadsB = $m_leadsB->where('leadsB_id = %d',$v)->find();
			$config = $m_config->where(array('name'=>'leadsB_outdays'))->find();
			if( (time() - $leadsB['have_time']) > ($config['value'] * 86400) || $leadsB['owner_role_id'] == 0 ){
				$data['owner_role_id'] = $owner_role_id;
				$data['have_time'] = time();
				if($m_leadsB->where('leadsB_id = %d',$v)->save($data)){
					M('LeadsBRecord')->add(array('leadsB_id'=>$v,'owner_role_id'=>$owner_role_id,'start_time'=>time()));
				}else{
					alert('success', L('RECEIVE_LEADSB_FAILED'), $_SERVER['HTTP_REFERER']);
				}
			}else{
				alert('error', L('RECEIVED_BY_SOMEONE', array($leadsB['name'])), $_SERVER['HTTP_REFERER']);
			}
		}
		alert('success', L('RECEIVE_LEADSB_SUCCESSFULLY'), $_SERVER['HTTP_REFERER']);
	}
	
	//批量分配
	public function batchAssign(){
		$leadsB_ids = $_POST['leadsB_id'];
		$owner_role_id = $_POST['owner_id'];
		$message = empty($_POST['message']) ? 0 :$_POST['message'];
		$sms = empty($_POST['sms']) ? 0 :$_POST['sms'];
		$email = empty($_POST['email']) ? 0 :$_POST['email'];
		if(empty($leadsB_ids)){
			alert('error', L('NOT CHOOSE ANY'), $_SERVER['HTTP_REFERER']);
		}
		$m_leadsB = M('LeadsB');
		$m_config = M('Config');
		$title = L('NEW_LEADSB_MESSAGE_NOTICE_TITLE');
		$content = '';
		$success_leadsB_name='';
		$error_leadsB_name='';
		foreach($leadsB_ids as $v){
			$leadsB = $m_leadsB->where('leadsB_id = %d',$v)->find();
			$config = $m_config->where(array('name'=>'leadsB_outdays'))->find();
			if( (time() - $leadsB['have_time']) > ($config['value'] * 86400) || $leadsB['owner_role_id'] == 0 ){
				$a = $m_leadsB->where('leadsB_id = %d', $v)->setField('owner_role_id', $owner_role_id);
				$b = $m_leadsB->where('leadsB_id = %d',$v)->setField('have_time',time());
				if ($a || $b) {
					$d = array('leadsB_id'=>$v,'owner_role_id'=>$owner_role_id,'start_time'=>time());
					M('LeadsBRecord')->data($d)->add();
					$url=U('leadsB/view','id='.$v);
					$success_leadsB_name .='<a href="'.$url.'">' .$leadsB['name'].'</a>、';
				}else{
					$error_leadsB_name .= $leadsB['name'].'、';
				}
			}else{
				alert('error', L('RECEIVED_BY_SOMEONE',array($leadsB['name'])), $_SERVER['HTTP_REFERER']);
			}
		}
		if($success_leadsB_name){
			$content = L('ASSIGE_LEADSB_MESSAGE_NOTICE_CONTENT' ,array(session('name'), $success_leadsB_name));
			if($message == 1) {
				sendMessage($owner_role_id,$content,1);
			}
			if($email == 1){
				$email_result = sysSendEmail($owner_role_id,$title,$content);
				if(!$email_result) alert('error', L('MAIL_NOTIFICATION_FAILS_FOR_NOT_SET_EMAIL'),$_SERVER['HTTP_REFERER']);
			}
			if($sms == 1){
				$sms_result = sysSendSms($owner_role_id,$content);
				if(100 == $sms_result){
					alert('error', L('SMS_NOTIFICATION_FAILS_FOR_NOT_VALIDATE_NUMBER'),$_SERVER['HTTP_REFERER']);
				}elseif($sms_result < 0){
					alert('error', L('SMS_NOTIFICATION_FAILS_CODE', array($sms_result)) ,$_SERVER['HTTP_REFERER']);
				}
			}
		}
		if($error_leadsB_name){
			alert('error', L('BATCH_ASSIGN_LEADSB_TO_SOMEONE_FAILED', array($error_leadsB_name)), $_SERVER['HTTP_REFERER']);
		}else{
			alert('success', L('BATCH_ASSIGN_LEADSB_SUCCESSFULLY'), $_SERVER['HTTP_REFERER']);
		}
		
	}
	
	public function assignDialog(){
		$this->display();
	}
	
	public function fenpei(){
		$leadsB_id = intval($_GET['id']);
		 if ($leadsB_id > 0) {
			$this->leadsB_id = $leadsB_id;
			$this->display();
		} else {
			alert('error', L('PARAMETER_ERROR'), $_SERVER['HTTP_REFERER']);
		}
	}

	public function analytics(){
		$m_leadsB = M('leadsB');
		if($_GET['role']) {
			$role_id = intval($_GET['role']);
		}else{
			$role_id = 'all';
		}
		if($_GET['department'] && $_GET['department'] != 'all'){
			$department_id = intval($_GET['department']);
		}else{
			$department_id = D('RoleView')->where('role.role_id = %d', session('role_id'))->getField('department_id');			
		}
		if($_GET['start_time']) $start_time = strtotime(date('Y-m-d',strtotime($_GET['start_time'])));
		$end_time = $_GET['end_time'] ?  strtotime(date('Y-m-d 23:59:59',strtotime($_GET['end_time']))) : strtotime(date('Y-m-d 23:59:59',time()));
		if($role_id == "all") {
			$roleList = D('RoleView')->select();
// 			$roleList = getRoleByDepartmentId($department_id);
			$role_id_array = array();
			foreach($roleList as $v2){
				$role_id_array[] = $v2['role_id'];
			}
			$where_source['creator_role_id'] = array('in', implode(',', $role_id_array));
			$where_status['owner_role_id'] = array('in', implode(',', $role_id_array));
		}else{
			$where_source['creator_role_id'] = $role_id;
			$where_status['owner_role_id'] = $role_id;
		}
		if($start_time){
			$where_source['create_time'] = array(array('elt',$end_time),array('egt',$start_time), 'and');
			$where_status['create_time'] = array(array('elt',$end_time),array('egt',$start_time), 'and');
		}else{
			$where_source['create_time'] = array('elt',$end_time);
			$where_status['create_time'] = array('elt',$end_time);
		}
		
		//线索来源统计
		$setting = M('Fields')->where("model = 'leadsB' and field = 'source'")->getField('setting');
		$setting = empty($setting)?'""':$setting;
		$setting_str = '$revenueList='.$setting.';';
		eval($setting_str);
		$source_count_array = array();
		$sourceList = M('leadsB')->field('count(1) as num , source')->group('source')->where($where_source)->select();
		foreach($sourceList as $v){
			$source = $v['source']?$v['source']:L('OTHER');
			$source_count[$source] = $v['num'];
		}
		foreach($revenueList['data'] as $v){
			if($source_count[$v]){
				$source_count_array[] = '["'.$v.'",'.$source_count[$v].']';
			}else{
				$source_count_array[] = '["'.$v.'",0]';
			}
		}
		$this->source_count = implode(',', $source_count_array);
		
		//Statistics Content
		$role_id_array = array();
		if($role_id == "all"){
			if($department_id != "all"){
				if(session('?admin')){
					$roleList = M('role')->where('user_id <> 0')->getField('role_id',true);
				}else{
					$roleList = getRoleByDepartmentId($department_id);
				}
				//$roleList = getRoleByDepartmentId($department_id);
				foreach($roleList as $v){
					$role_id_array[] = $v;
				}
			}else{
				$role_id_array = getSubRoleId();
			}
		}else{
			$role_id_array[] = $role_id;
		}
		if($start_time){
			$create_time= array(array('elt',$end_time),array('egt',$start_time), 'and');
		}else{
			$create_time = array('elt',$end_time);
		}
		$add_count_total = 0;
		$own_count_total = 0;
		$success_count_total = 0;
		$deal_count_total = 0;
		foreach($role_id_array as $v){
			$user = getUserByRoleId($v);
			$add_count = $m_leadsB->where(array('is_deleted'=>0, 'creator_role_id'=>$v, 'create_time'=>$create_time))->count();
			$own_count = $m_leadsB->where(array('is_deleted'=>0, 'owner_role_id'=>$v, 'create_time'=>$create_time))->count();
			$success_count = $m_leadsB->where(array('is_deleted'=>0, 'is_transformed'=>1,'owner_role_id'=>$v, 'create_time'=>$create_time))->count();
			$deal_count = $m_leadsB->where('is_deleted = 0 and owner_role_id = %d and is_transformed != 1 and update_time>create_time', $v)->count();
			$reportList[] = array("user"=>$user,"add_count"=>$add_count,"own_count"=>$own_count,"success_count"=>$success_count,"deal_count"=>$deal_count);
			$add_count_total += $add_count;
			$own_count_total += $own_count;
			$success_count_total += $success_count;
			$deal_count_total += $deal_count;
		}
		$this->total_report = array("add_count"=>$add_count_total, "own_count"=>$own_count_total, "success_count"=>$success_count_total, "deal_count"=>$deal_count_total);
		$this->reportList = $reportList;
		if (session('?admin')){
			$idArray = M('role')->where('user_id <> 0')->getField('role_id',true);
		}else{
			$idArray = getSubRoleId();	
		}
		$roleList = array();
		foreach($idArray as $roleId){				
			$roleList[$roleId] = getUserByRoleId($roleId);
		}
		$this->roleList = $roleList;
		
		$departments = M('roleDepartment')->select();
		$departmentList[] = M('roleDepartment')->where('department_id = %d', session('department_id'))->find();
		$departmentList = array_merge($departmentList, getSubDepartment(session('department_id'),$departments,''));
		$this->assign('departmentList', $departmentList);
		$this->alert = parseAlert();
		$this->display();
	}
	
	
	public function getAddDataByRoleId($id){
		if($id <= 0) $id=session('role_id');
		$moon = date('n');
		$year = date('Y');
		$this_moon_where['creator_role_id'] = $id;
		$this_moon_where['create_time'] = array(array('lt',time()),array('gt',strtotime($year.'-'.$moon.'-'.'1')), 'and');
		if($moon-1 > 0){
			$onemoon = $moon-1;
			$onemoonyear = $year;
		}else{
			$onemoon = $moon+11;
			$onemoonyear = intval($year)-1;
		}
		$onemoon_where['creator_role_id'] = $id;
		$onemoon_where['create_time'] = array(array('lt',strtotime($year.'-'.$moon.'-'.'1')),array('gt',strtotime($onemoonyear.'-'.$onemoon.'-'.'1')), 'and');
		
		if($moon-2 > 0){
			$twomoon = $moon-2;
			$twomoonyear = $year;
		}else{
			$twomoon = $moon+10;
			$twomoonyear = intval($year)-1;
			
		}
		$twomoon_where['creator_role_id'] = $id;
		$twomoon_where['create_time'] = array(array('lt',strtotime($onemoonyear.'-'.$onemoon.'-'.'1')),array('gt',strtotime($twomoonyear.'-'.$twomoon.'-'.'1')), 'and');
		
		if($moon-3 > 0){
			$threemoon = $moon-3;
			$threemoonyear = $year;
		}else{
			$threemoon = $moon+9;
			$threemoonyear = intval($year)-1;
			
		}
		$threemoon_where['creator_role_id'] = $id;
		$threemoon_where['create_time'] = array(array('lt',strtotime($twomoonyear.'-'.$twomoon.'-'.'1')),array('gt',strtotime($threemoonyear.'-'.$threemoon.'-'.'1')), 'and');
		
		if($moon-4 > 0){
			$fourmoon = $moon-4;
			$fourmoonyear = $year;
		}else{
			$fourmoon = $moon+8;
			$fourmoonyear = intval($year)-1;
		}
		$fourmoon_where['creator_role_id'] = $id;
		$fourmoon_where['create_time'] = array(array('lt',strtotime($threemoonyear.'-'.$threemoon.'-'.'1')),array('gt',strtotime($fourmoonyear.'-'.$fourmoon.'-'.'1')), 'and');
		
		if($moon-5 > 0){
			$fivemoon = $moon-5;
			$fivemoonyear = $year;
		}else{
			$fivemoon = $moon+7;
			$fivemoonyear = intval($year)-1;
		}
		$fivemoon_where['creator_role_id'] = $id;
		$fivemoon_where['create_time'] = array(array('lt',strtotime($fourmoonyear.'-'.$fourmoon.'-'.'1')),array('gt',strtotime($fivemoonyear.'-'.$fivemoon.'-'.'1')), 'and');
		
		$role_chart['x_data'] = "'".$fivemoon."月','".$fourmoon."月','".$threemoon."月','".$twomoon."月','".$onemoon."月','本月'";
		
		$m_leadsB = M('LeadsB');
		$data_fivemoon = $m_leadsB->where($fivemoon_where)->count();
		$data_fourmoon = $m_leadsB->where($fourmoon_where)->count();
		$data_threemoon = $m_leadsB->where($threemoon_where)->count();
		$data_twomoon = $m_leadsB->where($twomoon_where)->count();
		$data_onemoon = $m_leadsB->where($onemoon_where)->count();
		$data_thismoon = $m_leadsB->where($this_moon_where)->count();
		
		$fivemoon_where['is_transformed'] = 1;
		$fourmoon_where['is_transformed'] = 1;
		$threemoon_where['is_transformed'] = 1;
		$twomoon_where['is_transformed'] = 1;
		$onemoon_where['is_transformed'] = 1;
		$this_moon_where['is_transformed'] = 1;
		$data_fivemoon_t = $m_leadsB->where($fivemoon_where)->count();
		$data_fourmoon_t = $m_leadsB->where($fourmoon_where)->count();
		$data_threemoon_t = $m_leadsB->where($threemoon_where)->count();
		$data_twomoon_t = $m_leadsB->where($twomoon_where)->count();
		$data_onemoon_t = $m_leadsB->where($onemoon_where)->count();
		$data_thismoon_t = $m_leadsB->where($this_moon_where)->count();
		
		$role_chart['y_data']['all'] = $data_fivemoon.','.$data_fourmoon.','.$data_threemoon.','.$data_twomoon.','.$data_onemoon.','.$data_thismoon;
		$role_chart['y_data']['value'] = $data_fivemoon_t.','.$data_fourmoon_t.','.$data_threemoon_t.','.$data_twomoon_t.','.$data_onemoon_t.','.$data_thismoon_t;
		
		return $role_chart;
	}
	
	public function getOwnDataByRoleId($id){
		if($id <= 0) $id=session('role_id');
		$moon = date('n');
		$year = date('Y');
		$this_moon_where['owner_role_id'] = $id;
		$this_moon_where['create_time'] = array(array('lt',time()),array('gt',strtotime($year.'-'.$moon.'-'.'1')), 'and');
		if($moon-1 > 0){
			$onemoon = $moon-1;
			$onemoonyear = $year;
			
		}else{
			$onemoon = $moon+11;
			$onemoonyear = intval($year)-1;
		}
		$onemoon_where['owner_role_id'] = $id;
		$onemoon_where['create_time'] = array(array('lt',strtotime($year.'-'.$moon.'-'.'1')),array('gt',strtotime($onemoonyear.'-'.$onemoon.'-'.'1')), 'and');
		
		if($moon-2 > 0){
			$twomoon = $moon-2;
			$twomoonyear = $year;
		}else{
			$twomoon = $moon+10;
			$twomoonyear = intval($year)-1;
			
		}
		$twomoon_where['owner_role_id'] = $id;
		$twomoon_where['create_time'] = array(array('lt',strtotime($onemoonyear.'-'.$onemoon.'-'.'1')),array('gt',strtotime($twomoonyear.'-'.$twomoon.'-'.'1')), 'and');
		
		if($moon-3 > 0){
			$threemoon = $moon-3;
			$threemoonyear = $year;
		}else{
			$threemoon = $moon+9;
			$threemoonyear = intval($year)-1;
			
		}
		$threemoon_where['owner_role_id'] = $id;
		$threemoon_where['create_time'] = array(array('lt',strtotime($twomoonyear.'-'.$twomoon.'-'.'1')),array('gt',strtotime($threemoonyear.'-'.$threemoon.'-'.'1')), 'and');
		
		if($moon-4 > 0){
			$fourmoon = $moon-4;
			$fourmoonyear = $year;
		}else{
			$fourmoon = $moon+8;
			$fourmoonyear = intval($year)-1;
		}
		$fourmoon_where['owner_role_id'] = $id;
		$fourmoon_where['create_time'] = array(array('lt',strtotime($threemoonyear.'-'.$threemoon.'-'.'1')),array('gt',strtotime($fourmoonyear.'-'.$fourmoon.'-'.'1')), 'and');
		
		if($moon-5 > 0){
			$fivemoon = $moon-5;
			$fivemoonyear = $year;
		}else{
			$fivemoon = $moon+7;
			$fivemoonyear = intval($year)-1;
		}
		$fivemoon_where['owner_role_id'] = $id;
		$fivemoon_where['create_time'] = array(array('lt',strtotime($fourmoonyear.'-'.$fourmoon.'-'.'1')),array('gt',strtotime($fivemoonyear.'-'.$fivemoon.'-'.'1')), 'and');
		
		$role_chart['x_data'] = "'".$fivemoon."月','".$fourmoon."月','".$threemoon."月','".$twomoon."月','".$onemoon."月','本月'";
		$role_chart['x_data'] = L('X_DATA',array($fivemoon, $fourmoon, $threemoon, $twomoon , $onemoon));
		
		$m_leadsB = M('LeadsB');
		$data_fivemoon = $m_leadsB->where($fivemoon_where)->count();
		$data_fourmoon = $m_leadsB->where($fourmoon_where)->count();
		$data_threemoon = $m_leadsB->where($threemoon_where)->count();
		$data_twomoon = $m_leadsB->where($twomoon_where)->count();
		$data_onemoon = $m_leadsB->where($onemoon_where)->count();
		$data_thismoon = $m_leadsB->where($this_moon_where)->count();
		
		$fivemoon_where['is_transformed'] = 1;
		$fourmoon_where['is_transformed'] = 1;
		$threemoon_where['is_transformed'] = 1;
		$twomoon_where['is_transformed'] = 1;
		$onemoon_where['is_transformed'] = 1;
		$this_moon_where['is_transformed'] = 1;
		$data_fivemoon_t = $m_leadsB->where($fivemoon_where)->count();
		$data_fourmoon_t = $m_leadsB->where($fourmoon_where)->count();
		$data_threemoon_t = $m_leadsB->where($threemoon_where)->count();
		$data_twomoon_t = $m_leadsB->where($twomoon_where)->count();
		$data_onemoon_t = $m_leadsB->where($onemoon_where)->count();
		$data_thismoon_t = $m_leadsB->where($this_moon_where)->count();
		
		$role_chart['y_data']['all'] = $data_fivemoon.','.$data_fourmoon.','.$data_threemoon.','.$data_twomoon.','.$data_onemoon.','.$data_thismoon;
		$role_chart['y_data']['value'] = $data_fivemoon_t.','.$data_fourmoon_t.','.$data_threemoon_t.','.$data_twomoon_t.','.$data_onemoon_t.','.$data_thismoon_t;
		
		return $role_chart;
	}
	
	
	public function getAddChartByRoleId(){
		if($this->isAjax()){
			$id = $_REQUEST['role_id'];
			$role_chart = $this->getAddDataByRoleId($id);
			$this->ajaxReturn($role_chart, '', 1);
		}
	}
	
	public function revert(){
		$leadsB_id = isset($_GET['id']) ? intval(trim($_GET['id'])) : 0;
		if ($leadsB_id > 0) {
			$m_leadsB = M('LeadsB');
			$leadsB = $m_leadsB->where('leadsB_id = %d', $leadsB_id)->find();
			if ($leadsB['delete_role_id'] == session('role_id') || session('?admin')) {
				if (isset($leadsB['is_deleted']) || $leadsB['is_deleted'] == 1) {
					if ($m_leadsB->where('leadsB_id = %d', $leadsB_id)->setField('is_deleted', 0)) {
						alert('success', L('RESTORE SUCCESSFUL'), $_SERVER['HTTP_REFERER']);
					} else {
						alert('error', L('RESTORE FAILURE'), $_SERVER['HTTP_REFERER']);
					}
				} else {
					alert('error', L('ALREADY REDUCTION!'), $_SERVER['HTTP_REFERER']);
				}
			} else {
				alert('error', L('HAVE_NO_PERMISSION_TO_RECOVERY'), $_SERVER['HTTP_REFERER']);
			}
		} else {
			alert('error', L('PARAMETER_ERROR'), $_SERVER['HTTP_REFERER']);
		} 
	}
}