<?php

header("Access-Control-Allow-Origin:*");
/*星号表示所有的域都可以接受，*/
header("Access-Control-Allow-Methods:GET,POST");
class CommonAction extends Action {
	/**查看页面 **/
	function read() {
		$this -> _edit();
	}

	/**编辑页面 **/
	function edit() {
		$this -> _edit();
	}

	/** 保存操作  **/
	function save() {
		$this -> _save();
	}

	protected function _save($name = null) {
		$opmode = $_POST["opmode"]?$_POST["opmode"]:$_GET["opmode"];
		switch($opmode) {
			case "add" :
				$this -> _insert($name);
				break;
			case "edit" :
				$this -> _update($name);
				break;
			case "del" :
				$this -> _del($name);
				break;
			default :
				$this -> error("非法操作");
		}
	}

	/** 插入新新数据  **/
	protected function _insert($name = null) {
		if (empty($name)) {
			$name = $this -> getActionName();
		}
		$model = D($name);
		
		if(is_mobile_request()){
			unset($_GET['id']);
			unset($_GET['token']);
			if (false === $model -> create($_GET)) {
				$this -> error($model -> getError());
			}
		}else{
			if (false === $model -> create()) {
				$this -> error($model -> getError());
			}
		}
		/*保存当前数据对象 */
		$list = $model -> add();
		if ($list !== false) {//保存成功
			$this -> assign('jumpUrl', get_return_url());
			$this -> success('新增成功!'.$list);
		} else {
			$this -> error('新增失败!');
			//失败提示
		}
	}

	/* 更新数据  */
	protected function _update($name = null) {
		if (empty($name)) {
			$name = $this -> getActionName();
		}
		$model = D($name);
		if (false === $model -> create()) {
			$this -> error($model -> getError());
		}
		$list = $model -> save();
		if (false !== $list) {
			$this -> assign('jumpUrl', get_return_url());
			$this -> success('编辑成功!');
			//成功提示
		} else {
			$this -> error('编辑失败!');
			//错误提示
		}
	}

	/** 删除标记  **/
	protected function _del($id = null, $name = null, $return_flag = false) {
		if (empty($id)) {
			$id = $_REQUEST['id'];
			if (empty($id)) {
				$this -> error('没有可删除的数据!');
			}
		}
		if (empty($name)) {
			$name = $this -> getActionName();
		}
		$model = M($name);
		if (!empty($model)) {
			if (isset($id)) {
				if (is_array($id)) {
					$where['id'] = array("in", array_filter($id));
				} else {
					$where['id'] = array('in', array_filter(explode(',', $id)));
				}
				$result = $model -> where($where) -> setField("is_del", 1);
				if ($return_flag) {
					return $result;
				}
				if ($result !== false) {
					$this -> assign('jumpUrl', get_return_url());
					$this -> success("成功删除{$result}条!");
				} else {
					$this -> error('删除失败!');
				}
			} else {
				$this -> error('没有可删除的数据!');
			}
		}
	}

	/** 永久删除数据  **/
	protected function _destory($id = null, $name = null, $return_flag = false) {
		if (empty($id)) {
			$id = $_REQUEST['id'];
			if (empty($id)) {
				$this -> error('没有可删除的数据!');
			}
		}
		if (empty($name)) {
			$name = $this -> getActionName();
		}
		$model = M($name);
		if (!empty($model)) {
			if (isset($id)) {
				if (is_array($id)) {
					$where['id'] = array("in", array_filter($id));
				} else {
					$where['id'] = array('in', array_filter(explode(',', $id)));
				}
				$app_type = $this -> config['app_type'];

				if ($app_type == "personal") {
					$where['user_id'] = get_user_id();
				}

				$file_list = $model -> where($where) -> getField("id,add_file");
				$file_list = array_filter(explode(";", implode($file_list)));
				$this -> _destory_file($file_list);

				$result = $model -> where($where) -> delete();
				if ($return_flag) {
					return $result;
				}
				if ($result !== false) {
					$this -> assign('jumpUrl', get_return_url());
					$this -> success("彻底删除{$result}条!");
				} else {
					$this -> error('删除失败!');
				}
			} else {
				$this -> error('没有可删除的数据!');
			}
		}
	}

	public function del_file() {
		$file_list = $_REQUEST['sid'];
		$this -> _destory_file($file_list);
	}

	protected function _destory_file($file_list) {
		if (isset($file_list)) {
			if (is_array($file_list)) {
				$where["sid"] = array("in", $file_list);
			} else {
				$where["sid"] = array('in', array_filter(explode(',', $file_list)));
			}
		} else {
			exit();
		}

		$model = M("File");
		$where['module'] = MODULE_NAME;
		$admin = $this -> config['auth']['admin'];

		if ($admin) {
			$where['user_id'] = array('eq', get_user_id());
		};

		$list = $model -> where($where) -> select();
		$save_path = get_save_path();

		foreach ($list as $file) {
			if (file_exists($_SERVER["DOCUMENT_ROOT"] . "/" . $save_path . $file['savename'])) {
				unlink($_SERVER["DOCUMENT_ROOT"] . "/" . $save_path . $file['savename']);
			}
		}

		$result = $model -> where($where) -> delete();
		if ($result !== false) {
			return true;
		} else {
			return false;
		}
	}
	public function upload($flag = false){	
		$this -> _upload($flag);
	}
	protected function _upload($flag = false) {
		header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
		header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
		header("Cache-Control: no-store, no-cache, must-revalidate");
		header("Cache-Control: post-check=0, pre-check=0", false);
		header("Pragma: no-cache");
		if (!empty($_FILES)) {
			import("@.ORG.UploadFile");
			$upload = new UploadFile();
			$upload -> subFolder = strtolower(MODULE_NAME);
			$upload -> savePath = get_save_path();
			$upload -> saveRule = "uniqid";
			$upload -> autoSub = true;
			$upload -> subType = "date";
			
			$defaultinfo = M('config') -> where('name="defaultinfo"')->getField('value');
			$defaultinfo = unserialize($defaultinfo);
			$allow_file_type = $defaultinfo['allow_file_type'];
			
			if($flag){$upload -> allowExts = array('xlsx','xls');}else{$upload -> allowExts = array_filter(explode(",", $allow_file_type), 'upload_filter');}
			if (!$upload -> upload()) {
				$data['error'] = 1;
				$data['message'] = $upload -> getErrorMsg();
				$data['status'] = 0;
				exit(json_encode($data));
				//exit($upload -> getErrorMsg());
			} else {
				//取得成功上传的文件信息
				$upload_list = $upload -> getUploadFileInfo();
				$sid = get_sid();
				$file_info = $upload_list[0];
				$model = M("File");
				$model -> create($upload_list[0]);
				$model -> create_time = time();
				$model -> user_id = session('user_id');
				$model -> role_id = session('role_id');
				$model -> sid = $sid;
				$model -> module = MODULE_NAME;
				$file_id = $model -> add();
				$file_info['sid'] = $sid;
				$file_info['error'] = 0;
				$file_info['url'] = "/" . $file_info['savepath'] . $file_info['savename'];
				$file_info['status'] = 1;
				if($flag){record_upload($file_info['savepath'] . $file_info['savename']);}
				exit(json_encode($file_info));
			}
		}
	}
	
	public function down(){
		$this->_down();
	}
	protected function _down() {
		$attach_id = $_REQUEST["attach_id"];
		$file_id = f_decode($attach_id);
		
		$open=fopen("C:\log.txt","a" ); 
		fwrite($open,$file_id."\r\n"); 
		fclose($open);
		
		$File = M("File") -> find($file_id);
		$filepath = get_save_path() . $File['savename'];
		$filePath = realpath($filepath);
		$fp = fopen($filePath, 'rb');

		$ua = $_SERVER["HTTP_USER_AGENT"];
		if (!preg_match("/MSIE/", $ua)) {
			header("Content-Length: " . filesize($filePath));
			Header("Content-type: application/octet-stream");
			header("Content-Length: " . filesize($filePath));
			header("Accept-Ranges: bytes");
			header("Accept-Length: " . filesize($filePath));
		}

		header("Content-Disposition:attachment;filename =" . str_ireplace('+', '%20', URLEncode($File['name'])));
		header('Cache-Control:must-revalidate, post-check=0,pre-check=0');
		header('Expires:     0');
		header('Pragma:     public');
		//echo $query;
		fpassthru($fp);
		exit ;
	}

	//生成查询条件
	protected function _search($name = null) {
		$map = array();
		//过滤非查询条件
		$request = array_filter(array_keys(array_filter($_REQUEST)), "filter_search_field");
		if (empty($name)) {
			$name = $this -> getActionName();
		}
		$model = D($name);
		$fields = get_model_fields($model);

		foreach ($request as $val) {
			$field = substr($val, 3);
			$prefix = substr($val, 0, 3);
			if (in_array($field, $fields)) {
				if ($prefix == "be_") {
					if (isset($_REQUEST["en_" . $field])) {
						if (strpos($field, "time")) {
							$map[$field] = array( array('egt', date_to_int(trim($_REQUEST[$val]))), array('elt', date_to_int(trim($_REQUEST["en_" . $field])) + 86400));
						}
						if (strpos($field, "date")) {
							$map[$field] = array( array('egt', trim($_REQUEST[$val])), array('elt', trim($_REQUEST["en_" . substr($val, 3)])));
						}
					}
				}

				if ($prefix == "li_") {
					$map[$field] = array('like', '%' . trim($_REQUEST[$val]) . '%');
				}
				if ($prefix == "eq_") {
					$map[$field] = array('eq', trim($_REQUEST[$val]));
				}
				if ($prefix == "gt_") {
					$map[$field] = array('egt', trim($_REQUEST[$val]));
				}
				if ($prefix == "lt_") {
					$map[$field] = array('elt', trim($_REQUEST[$val]));
				}
			}
		}
		return $map;
	}

	protected function _list($model, $map, $sortBy = '', $asc = false,$temp='list',$page_temp='page',$p_temp='p') {
		//排序字段 默认为主键名
		if (isset($_REQUEST['_order'])) {
			$order = $_REQUEST['_order'];
		} else if (!empty($sortBy)) {
			$order = $sortBy;
		} else if (in_array('sort', get_model_fields($model))) {
			$order = 'sort';
			$asc = true;
		} else {
			$order = $model -> getPk();
		}
		//排序方式默认按照倒序排列
		//接受 sost参数 0 表示倒序 非0都 表示正序
		if (isset($_REQUEST['_sort'])) {
			$sort = $_REQUEST['_sort'] ? 'asc' : 'desc';
		} else if (strpos($sortBy, ',')) {
			$sort = '';
		} else {
			$sort = $asc ? 'asc' : 'desc';
		}

		//取得满足条件的记录数
		$count_model = clone $model;
		//取得满足条件的记录数
		if (!empty($count_model -> pk)) {
			$count = $count_model -> where($map) -> count($model -> pk);
		} else {
			$count = $count_model -> where($map) -> count();
		}
		if ($count > 0) {
			import("@.ORG.Util.Page");
			//创建分页对象
			if (!empty($_REQUEST['list_rows'])) {
				$listRows = $_REQUEST['list_rows'];
			} else {
				$listRows = get_user_config('list_rows');
			}
			$p = new Page($count, $listRows);
			//分页查询数据
			
			if ($sort) {
				if(is_mobile_request()){
					$voList = $model -> where($map) -> order("`" . $order . "` " . $sort) -> select();
				}else{
					$voList = $model -> where($map) -> order("`" . $order . "` " . $sort) -> limit($p -> firstRow . ',' . $p -> listRows) -> select();
				}
			} else {
				if(is_mobile_request()){
					$voList = $model -> where($map) -> order($order) -> select();
				}else{
					$voList = $model -> where($map) -> order($order) -> limit($p -> firstRow . ',' . $p -> listRows) -> select();
				}
			}
			//echo $model->getlastSql();die;
			$p -> parameter = $this -> _search();
			//分页显示
			$page = $p -> show();

			//列表排序显示
			$sortImg = $sort;

			//排序图标
			$sortAlt = $sort == 'desc' ? '升序排列' : '倒序排列';

			//排序提示
			$sort = $sort == 'desc' ? 1 : 0;

			//排序方式

			//模板赋值显示
			$name = $this -> getActionName();
			$this -> assign($temp, $voList);
			$this -> assign('sort', $sort);
			$this -> assign('order', $order);
			$this -> assign('sortImg', $sortImg);
			$this -> assign('sortType', $sortAlt);
			$this -> assign($page_temp, $page);
		}
		return $voList;
	}


	protected function _set_field($id, $field, $val, $name = '') {
		if (empty($name)) {
			$name = $this -> getActionName();
		}
		$model = M($name);
		if (!empty($model)) {
			if (isset($id)) {
				if (is_array($id)) {
					$where['id'] = array("in", array_filter($id));
				} else {
					$where['id'] = array('in', array_filter(explode(',', $id)));
				}
				$admin = $this -> config['auth']['admin'];
				if (in_array('user_id', $model -> getDbFields()) && !$admin) {
					$where['user_id'] = array('eq', get_user_id());
				};
				$list = $model -> where($where) -> setField($field, $val);
				if ($list !== false) {
					return true;
				} else {
					return false;
				}
			} else {
				return false;
			}
		}
	}


	protected function _pushReturn($data, $info, $status, $user_id, $time = null) {
		$model = M("Push");

		$model -> data = $data;
		$model -> info = $info;
		$model -> status = $status;

		if (empty($user_id)) {
			$model -> user_id = get_user_id();
		} else {
			$model -> user_id = $user_id;
		}

		if (empty($time)) {
			$model -> time = time();
		} else {
			$model -> time = $time;
		}
		$model -> add();
	}
	/*
	public function get_dept_child(){
		$dept_id_0 = $_GET['dept_id_0'];
		$dept_menu = M('dept') -> field('id,pid,name') -> where("is_del=0") -> order('sort asc') -> select();
		$dept_tree = list_to_tree($dept_menu, $dept_id_0);
		$tree_menu = select_tree_menu($dept_tree);
		$this -> ajaxReturn($tree_menu);
	}
	public function get_depts_child(){
		$dept_id_0 = $_GET['dept_id_0'];
		$dept_ids = array_filter(explode('|',$dept_id_0));
		$dept_menu = M('dept') -> field('id,pid,name') -> where("is_del=0") -> order('sort asc') -> select();
		$pos_ids = array();
		$html = "<ul>\r\n";
		foreach ($dept_ids as $dept_id){
			$pos_ids = array_merge($pos_ids,get_child_dept_all($dept_id));
		}
		foreach ($pos_ids as $pos_id){
			$res = M('Dept')->field('id,name')->where(array('id'=>$pos_id,'is_del'=>0,'is_real_dept'=>0))->find();
			if($res){
				$id = $res['id'];
				$name = $res['name'];
				$html .= "<li>\r\n";
				$html .= "<input type=\"checkbox\" name=\"pos[]\" id=\"pos_$id\" value=\"$id\" name2=\"$name\">\r\n";
				$html .= "<label for=\"pos_$id\">$name</label>\r\n";
				$html .= "</li>\r\n";
			}
		}
		$html .= "</ul>\r\n";
// 		$html .= "<div class=\"bottom2\">\r\n";
// 		$html .= "<span class=\"bottom_s1\" id=\"qd2\">确定</span>\r\n";
// 		$html .= "<span class=\"bottom_s2\" id=\"qx2\">取消</span>\r\n";
// 		$html .= "</div>\r\n";
		$this -> ajaxReturn($html);
	}
	public function get_real_dept(){
		$node = D("Dept");
		$menu = array();
		$dept_menu = $node -> field('id,pid,name') -> where("is_del=0 and is_real_dept=1") -> order('sort asc') -> select();
		$dept_tree = list_to_tree($dept_menu);
		$html = tree_to_html($dept_tree);
// 		$tree_menu = select_tree_menu($dept_tree);
		$this -> ajaxReturn($html);
	}
	public function get_username_by_dept(){
		$dept_id = $_GET['dept_id'];
		$child_dept = get_child_dept_all($dept_id);
		$users = D('UserView')->field('id,name,pos_id')->where(array('pos_id'=>array('in',$child_dept),'is_del'=>'0'))->select();
		foreach ($users as $k=>$v){
			$pos_name = M('Dept')->field('name')->find($v['pos_id']);
			$users[$k]['pos_name'] = $pos_name['name'];
		}
		$this -> ajaxReturn($users);
	}
	*/
	
}
?>