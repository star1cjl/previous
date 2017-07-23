<?php
/**
 * 前台登录
 */
namespace Home\Controller;
use Common\Controller\BaseController;

class UserController extends BaseController{
	function _initialize(){
		parent::_initialize();
		$this->pagesize = 10;
		$this->user_mod = D('User');
		$this->user_data_mod = D('Userdata');
		$this->professor_mod = D('Professor');
		$this->behavior_mod = D('Behavior');
	}
	
	/**
	 * 用户中心
	 * int $user_id 用户id
	 * int $p 当前页数
	 */
	public function index(){
		$user_id=I('get.id') ? (int)I('get.id') : session('user.id');
		$p = I('get.p') ? (int)I('get.p') : 1;
		$result = D('Question')->get_user_question($user_id,$p);//获取问题列表
		//展示
		$this->assign($result);
		$this->assign('p',$p);
		$this->assign('id',$user_id);
		if($user_id != session('user.id')){
			$this->user_mod->where('id='.session('user.id'))->setInc('view');
			$view = 'others';
			$array['user']=M('User')->where('id='.$user_id)->find();
			$array['fans']=D('Watch')->get_fans_list($user_id);//获取粉丝列表
			$array['watch']=D('Watch')->get_watch_list($user_id);//获取关注列表
		}else{
			$array['user']=session('user');
		}
		$this->assign($array);
		$this->display($view);
	}

	//第三方登录绑定用户
	public function bind_user($type='qq'){
		if(!empty($_POST)){
			if(empty($_POST['phone'])){
				$this->error('手机号不能为空');
			}
			if(empty($_POST['password'])){
				$this->error('密码不能为空');
			}
			//验证码
			if(C('is_checkcode')){
				captcha($_POST);
			}
			//手机号和密码是否匹配
			$phone = I('post.phone');
			$password =substr_pwd(I('post.password'));
			$where['phone'] = $phone;
			$where['password'] = $password;
			$user = M('User')->where($where)->find();
			if($user){
				//绑定
				$old_id = session('user.id');
				//删除默认新增的user记录
				M('user')->where('id='.$old_id)->delete();
				//修改第三方登录表
				M('oauth_user')->where('uid='.$old_id)->setField('uid',$user->id);
				//修改session
				session('user.id',$user->id);
				session('user.username',$user->username);
			}else{
				$this->error('绑定失败，手机号或密码错误');
			}
		}else{
			$this->assign('type',$type);
			$this->display();
		}

	}
	
	//个人资料
	public function personal_data(){
		$user_id=session('user.id');
		$User = M('User');

		//更新资料
		if(!empty($_POST)){
			if(C('is_checkcode')){
				captcha($_POST);//验证码
			}
			$data=I('post.');//用I过滤表单数据
			$data['city'] = $data['province'].','.$data['city'].','.$data['area'];
			$data['birthday'] = strtotime($data['birthday']);
			if(!$User->create($data)){
				$this->error($User->getError());
			}
			$data = check_fields($data,'username,description,sex,birthday,city,title');
			$data['id']=$user_id;
			$data['updated_time']=time();
			$result = $User->save($data);
			if($result){
				$info = $User->where('id='.$user_id)->field('id,username,avatar,sex')->find();
				session('user',$info);
				$this->success('您的个人资料已更新');
			}else{
				$this->error('数据库错误，个人资料修改失败');
			}
		}else{
			//展示
			$result = $User->where('id='.$user_id)->field('id,username,avatar,sex,birthday,title,description,city')->find();
			$this->assign('user',$result);
			$this->display();
		}
	}

	//支付明细
	public function payment(){
		$p = I('get.p') ? (int)I('get.p') : 1;
		$limit = 10;
		$user_id=session('user.id');
		$count = M('Payment_log')->where('user_id='.$user_id)->count('id');
        $num = ceil($count/$limit);//页数
		$result = M('Payment_log')->where('user_id='.$user_id)->page($p,$limit)->select();
		$pay_type = array(
			'alipay'=>'支付宝',
			'wxpay' => '微信支付',
			);
		$array = array(
			'user'=>session('user'),
			'list'=>$result,
			'count'=>$count,
			'page_control'=>join_page('user/payment',array(),$num,$p),
			'pay_type'=>$pay_type,
			);
		$this->assign($array);
		$this->display();
	}

	//银行卡
	public function bank(){
		$user_id=session('user.id');
		$bank = D('Userdata');
		if(!empty($_POST)){
			$data=I('post.');//用I过滤表单数据
			//短信验证...

			if(!$bank->create($data)){
				$this->error($bank->getError());
			}
			$data = check_fields($data,'bank_card_holder,bank_card');
			$data['user_id'] = $user_id;
			$data['updated_time'] = time();
			$had = $bank->where('user_id='.$user_id)->find();
			if($had){
				$result = $bank->save($data);
			}else{
				$result = $bank->add($data);
			}
			if($result){//写入
				$this->success('银行卡绑定成功');
			}else{
				$this->error($bank->getError());
			}
		}else{
			$array = $bank->where('user_id='.$user_id)->field('id,bank_card,bank_card_holder')->find();
			$this->assign('title','银行账号');
			$this->assign('user',session('user'));
			$this->assign($array);
			$this->display();
		}
	}

	//提问
	public function question(){
		$user_id=I('get.id') ? (int)I('get.id') : session('user.id');
		if($user_id == session('user.id')){
			$my=true;
		}
		$p = I('get.p') ? (int)I('get.p') : 1;
		$limit = 10;
		$result = D('Question')->get_user_question($user_id,$p,$limit);//获取问题列表
		extract($result);//分解为list和count
        $num = ceil($count/$limit);//页数
		$mid = '';
		//获取tag信息
		foreach($list as $vo){
			$time = date('Y-m-d h:i',$vo['created_time']);
			$url = UU('question/detail',array('id'=>$vo['id']));
			$m = '<div class="title">
                    <a href="'.$url.'">'.$vo['title'].'</a>
                  </div>
                  <div class="foottools">
                	  <span class="tags"><i class="am-icon-tags"> </i>';
	        foreach($vo['tags'] as $key=>$value){
	        	$t_url = UU('tag/index',array('id'=>$key));
	        	$m .= '<a href="'.$t_url.'" class="small-icon">'.$value.'</a>';
	        }
            $m .='</span>
                    <span>'.$vo['answer'].'个回答</span>
                    <time class="am-fr">'.$time.'</time>
                    </div>';
			if($my){
				$mid .= '<li>'.$m.'<a href="delete/'.$vo['id'].'html" class="del" title="删除""><i class="am-icon-trash" aria-hidden="true"></i></a>
                        </li>';
			}else{
				$mid .= '<div class="item">'.$m.'</div>';
			}
		}
		if($my){
			$content = '<div class="ask-list"><ul class="am-list">'.$mid.'</ul></div>';
		}else{
			$content='<div class="questions-list">'.$mid.'</div>';
		}
		$params = array('id'=>$user_id);
		$content.= join_page('user/question',$params,$num,$p);
		echo $content;
	}

	//回答
	public function answer(){
		$user_id=I('get.id') ? (int)I('get.id') : session('user.id');
		$p = I('get.p') ? (int)I('get.p') : 1;
		if($user_id == session('user.id')){
			$my=true;
		}
		$limit = 10;
		//获取提问
		$Answer = M('Answer');
		$map['user_id']=array('EQ',$user_id);
		$count = $Answer->where($map)->count('id');
        $num = ceil($count/$limit);
		$result = $Answer->where($map)->order('created_time desc')->page($p,$limit)->select();
		$mid = '';
		//获取问题信息
		foreach ($result as $key => $value) {
			$time = date('Y-m-d h:i',$value['created_time']);
			$url = UU('question/detail',array('id'=>$value['question_id']));
			$question = M('Question')->where('id='.$value['question_id'])->find();
			$m ='<div class="title"><a href="'.$url.'">'.$value['content'].'</a></div>';
            if($value['adopt'])$m .='<div class="Adopt"><i></i>已采纳</div>';
            $m.='<p class="content">问：'.$question['title'].'</p>
                        <div class="foottools">
                            <span><i class="am-icon-thumbs-o-up"></i>'.$value['agree'].'</span>
                            <time class="am-fr">'.$time.'</time>
                        </div>';
            if($my){
				$mid .= '<li>'.$m.'<a href="delete/'.$value['id'].'html" class="del" title="删除""><i class="am-icon-trash" aria-hidden="true"></i></a>
                        </li>';
			}else{
				$mid .= '<div class="item">'.$m.'</div>';
			}
		}
		if($my){
			$content = '<div class="ask-list"><ul class="am-list">'.$mid.'</ul></div>';
		}else{
			$content='<div class="questions-list">'.$mid.'</div>';
		}
		$params = array('id'=>$user_id);
		$content.= join_page('user/answer',$params,$num,$p);
		echo $content;
	}

	//评论
	public function comment(){
		$user_id=I('get.id') ? (int)I('get.id') : session('user.id');
		$p = I('get.p') ? (int)I('get.p') : 1;
		$limit = 10;
		//获取评论
		$Comment = M('Comment');
		$map['user_id']=array('EQ',$user_id);
		$count = $Comment->where($map)->count('id');
        $num = ceil($count/$limit);
		$list = $Comment->where($map)->order('created_time desc')->page($p,$limit)->select();
		$mid = '';
		//获取被评论内容
		foreach ($list as $key => $value) {
			$field=$value['source_type'] == 'article'? 'title':'content';
			$source = M($value['source_type'])->getFieldById($value['source_id'],$field);
			$time = date('Y-m-d h:i',$value['created_time']);
			$m ='<div class="title"><a href="javascript:;">'.$value['content'].'</a></div>
                 <div class="content">'.$source.'</div>
				 <div class="foottools">
                    <span><i class="am-icon-thumbs-o-up"></i>'.$value['agree'].'</span>
                    <time class="am-fr">'.$time.'</time>
                 </div>';
			$mid .= '<li>'.$m.'<a href="delete/'.$value['id'].'html" class="del" title="删除""><i class="am-icon-trash" aria-hidden="true"></i></a>
                        </li>';
		}
		$params = array('id'=>$user_id);
		$content = '<div class="ask-list"><ul class="am-list">'.$mid.'</ul></div>'.join_page('user/comment',$params,$num,$p);
		echo $content;
	}

	//文章
	public function article(){
		$user_id=I('get.id') ? (int)I('get.id') : session('user.id');
		$p = I('get.p') ? (int)I('get.p') : 1;
		if($user_id == session('user.id')){
			$my=true;
		}
		$limit = 10;
		//获取文章
		$Article = M('Article');
		$map['user_id']=array('EQ',$user_id);
		$count = $Article->where($map)->count('id');
        $num = ceil($count/$limit);
		$list = $Article->where($map)->order('created_time desc')->page($p,$limit)->select();
		$mid = '';
		foreach($list as $vo){
			$time = date('Y-m-d h:i',$vo['created_time']);
			$url = UU('article/detail',array('id'=>$vo['id']));
			
			$m = '<div class="title">
                    <a href="'.$url.'">'.$vo['title'].'</a>
                  </div>
                  <div class="foottools">
                	  <span class="tags"><i class="am-icon-tags"> </i>';
            if($vo['tag_ids']){
				$tags=D('Tag')->getTitle($vo['tag_ids']);
				foreach($vo['tags'] as $key=>$value){
		        	$t_url = UU('tag/index',array('id'=>$key));
		        	$m .= '<a href="'.$t_url.'" class="small-icon">'.$value.'</a>';
		        }
			}
            $m .='</span>
            		<span><i class="am-icon-thumbs-o-up"></i> '.$vo['agree'].'</span>
                    <time class="am-fr">'.$time.'</time>
                    </div>';
			if($my){
				$mid .= '<li>'.$m.'<a href="delete/'.$vo['id'].'html" class="del" title="删除""><i class="am-icon-trash" aria-hidden="true"></i></a>
                        </li>';
			}else{
				$mid .= '<div class="item">'.$m.'</div>';
			}
		}
		if($my){
			$content = '<div class="ask-list"><ul class="am-list">'.$mid.'</ul></div>';
		}else{
			$content='<div class="questions-list">'.$mid.'</div>';
		}
		$content.= join_page('user/article',array('id'=>$user_id),$num,$p);
		echo $content;
	}

	//收藏
	public function collection(){
		$type=I('get.type') ? I('get.type') : 'article';
		$user_id=I('get.id') ? (int)I('get.id') : session('user.id');
		$p = I('get.p') ? (int)I('get.p') : 1;
		$limit = 10;

		//根据类型查表
		$Collection = M('Collection');
		$map['user_id']=array('EQ',$user_id);
		$map['source_type']=array('EQ',$type);
		$count = $Collection->where($map)->count('id');
        $num = ceil($count/$limit);
		$list= $Collection->where($map)->order('created_time desc')->page($p,$limit)->select();
		$mid = '';
		foreach($list as $vo){
			$time = date('Y-m-d h:i',$vo['created_time']);
			$url = UU($type.'/detail',array('id'=>$vo['id']));
			$mid .= '<li><div class="title"><a href="'.$url.'">'.$vo['intro'].'</a></div>
                    	 <div class="foottools"><time>收藏于'.$time.'</time></div>
                    	 <a href="" class="del" title="取消收藏"><i class="am-icon-trash" aria-hidden="true"></i></a>
                    </li>';
		}
		$params = array('id'=>$user_id,'type'=>$type);
		$content = '<div class="ask-list"><ul class="am-list">'.$mid.'</ul></div>'.join_page('user/collection',$params,$num,$p);
		echo $content;
	}

	//经验
	public function experience(){
		$user_id= session('user.id');
		$p = I('get.p') ? (int)I('get.p') : 1;
		$limit = 10;
		//根据类型查表
		$Experience = M('Experience_log');
		$map['user_id']=array('EQ',$user_id);
		$count = $Experience->where($map)->count('id');
        $num = ceil($count/$limit);
		$list = $Experience->where($map)->order('created_time desc')->page($p,$limit)->select();
		$mid = '';
		foreach($list as $vo){
			$time = date('Y-m-d h:i',$vo['created_time']);
			$mid .= '<tr>
                        <td>'.$vo['number'].'</td>
                        <td>'.$vo['intro'].'</td>
                        <td>'.$time.'</td>
                    </tr>';
		}
		$content = '<table class="am-table am-table-bordered am-table-striped am-table-hover">
					<tbody><tr><th>数值（点）</th><th>备注</th><th>时间</th></tr>'.$mid.'</tbody></table>'.join_page('user/experience',array(),$num,$p);
		echo $content;
	}

	//关注
	public function watch(){
		$user_id=I('get.id') ? (int)I('get.id') : session('user.id');
		$type=I('get.type') ? I('get.type') : 'user';
		$p = I('get.p') ? (int)I('get.p') : 1;
		$limit = 10;

		//根据类型查表
		$Watch=M('Watch');
		$map['user_id']=array('EQ',$user_id);
		$map['source_type']=array('EQ',$type);
		$count = $Watch->where($map)->count('id');
		$result = $Watch->where($map)->order('created_time desc')->page($p,$limit)->getField('source_id',true);
		$content = '<div class="questions-list">';
		foreach($result as $vo){
			$user=M($type)->where('id='.$vo)->find();
			$u_url = UU('user/index',array('id'=>$vo['id']));
			$content .='<div class="item am-cf">
	                        <div class="am-fl">
	                            <a href="'.$u_url.'">
	                                <img class="am-circle" width="46" height="46" src="'.$user['avatar'].'" />
	                                <span class="am-margin-left">'.$user['username'].'</span>
	                            </a>
	                            <p class="foottools"> <a href="'.$u_url.'" class="am-link-muted">'.$user['title'].'</a> </p>
	                        </div>
	                        <span class="am-fr">
	                            <a class="am-btn am-btn-success am-btn-outline am-btn-xs am-radius" href="javascript:;">关注</a>
	                            <a class="am-btn am-btn-danger am-btn-outline am-btn-xs am-radius" href="'.$u_url.'">查看</a>
	                        </span>
                    	</div>';
		}
		$content.='</div>
                    <ul class="am-pagination am-pagination-centered">';
        if($p>1){$content.='<li><a href="page_up()">上一页</a></li>';}
        for($i=1;$i<$num;$i++){
        	$p_url = UU("user/watch",array('id'=>$user_id,'p'=>$i));
        	if($i == $p){$class = 'current';}
        	$content.='<li class='.$class.'><a href="javascript:;" data-url="'.$p_url.'">'.$i.'</a></li>';
        }
        if($p<$num){$content.='<li><a href="page_down()">下一页</a></li>';}
        $content .= '</ul>';
		echo $content;
	}

	//关注的用户和粉丝
	public function fans(){
		$id=session('user.id');
		$type=I('get.type');
		$p = I('get.p') ? (int)I('get.p') : 1;
		$limit = 10;

		//根据类型查表
		if($type == 'watch'){
			$result=D('Watch')->get_watch_list($id,'user',$p,$limit);//获取关注列表
			$title='我的关注';
		}else{
			$result=D('Watch')->get_fans_list($id,'user',$p,$limit);//获取粉丝列表
			$title='我的粉丝';
		}
		extract($result);
        $num = ceil($count/$limit);
		//展示
		$this->assign('list',$list);
		$this->assign('p',$p);
		$this->assign('id',$user_id);
		$this->assign('type',$type);
		$this->assign('num',$num);
		$this->assign('count',$count);
		$this->assign('title',$title);
		$this->assign('user',session('user'));
		$this->display();
	}

	//消息
	public function message(){
		$user_id=session('user.id');
		$type = I('get.type')?I('get.type') : 0;//消息类型
		$p = I('get.p') ? (int)I('get.p') : 1;
		$limit = 20;
		//获取消息
		$Message = M('Message');
		$where['user_id']=$user_id;
		$where['addressee_deleted']=0;
		$where['type']=$type;
		$count = $Message->where($where)->count('id');
        $num = ceil($count/$limit);
		$array['list'] = $Message->where($where)->order('created_time')->page($p,$limit)->select();
		$params = array('type'=>$type);
		$array['page_control'] = join_page('user/message',$params,$num,$p);
		$array['count'] = $count;
		//展示
		$this->assign($array);
		$this->display();
	}

	//消息设置
	public function msg_setting(){
		$user_id=session('user.id');
		$Userdata = M('User_data');
		//处理表单，存表
		if(!empty($_POST)){
			if (!$Userdata->autoCheckToken($_POST)){
				$this->error('表单令牌验证错误，请勿重复提交表单');
			}
			$data = I("post.");
			$msg_remind = $email_remind = $sms_remind = ',';
			$msg = array('watch','invite','answer','comment','agree','adopt','reply');
			$email = array('e_adopt','e_invite');
			$sms = array('m_adopt','m_invite');
			foreach($data as $key=>$value){
				if($value != 'on') continue;
				if(in_array($key,$msg)){
					$msg_remind .= $key.',';
				}elseif(in_array($key, $email)){
					$email_remind .= substr($key, 2) . ',';
				}elseif(in_array($key,$sms)){
					$sms_remind .= substr($key, 2) . ',';
				}
			}
			$Userdata->user_id = $user_id;
			$Userdata->msg_remind = $msg_remind;
			$Userdata->email_remind = $email_remind;
			$Userdata->sms_remind = $sms_remind;
			$Userdata->updated_time = time();
			if($Userdata->save()){
				$this->success('保存成功');
			}else{
				$this->error('数据库错误，保存失败');
			}
		}else{
			//页面
			$array = $Userdata->where('user_id='.$user_id)->field('msg_remind,email_remind,sms_remind')->find();
			foreach($array as $key=>$value){
				$value = substr($value, 1);
				$array[$key] = explode(',',$value);
			}
			$list = array();
			extract($array);
			foreach($msg_remind as $value){
				$list[$value] = 'checked';
			}
			foreach($email_remind as $value){
				$value = 'e_'.$value;
				$list[$value] = 'checked';
			}
			foreach($sms_remind as $value){
				$value = 'm_'.$value;
				$list[$value] = 'checked';
			}
			$this->assign($list);
			$this->display();
		}
	}

	//安全设置
	public function security(){
		$user_id = session('user.id');
		$Userdata = D('Userdata');
		//用户安全等级评定
		$lv = $Userdata->get_security_lv($user_id);
		if($lv > 60){
			if($lv < 90){
				$array['security'] = '中';
			}else{
				$array['security'] = '高';
			}
		}else{
			$array['security'] = '低';
		}
		$array['security_lv'] = $lv;
		//获取安全信息
		$this->assign($array);
		$this->display();
	}

	//发送邮箱绑定的验证码
	public function send_email(){
		//检查此邮箱是否被绑定过
		$email = I('email');
		$has = M('User')->where('email='.$email)->find();
		if($has){
			$this->ajaxReturn(array('result'=>'false','msg'=>'此邮箱已被绑定，请更换'));
		}
		$code = rand_six_num();
		cookie('email_bind_'.$email,$code,600);
		$message = '亲爱的'.C('site_title').'用户：'.session('user.username').'您正在进行邮箱绑定操作，'.'验证码为：'.$code.'，有效期为10分钟，请尽快验证。';
		$result = send_email($email,C('site_title').'邮箱绑定',$message);
		if($result['error']){
			$this->ajaxReturn(array('result'=>'false','msg'=>$result['msg']));
		}else{
			$this->ajaxReturn(array('result'=>'success','msg'=>'验证码已发送至您的邮箱，十分钟内验证有效'));
		}
	}

	//绑定邮箱
	public function bind_email(){
		if(empty($_POST)){
			$this->ajaxReturn(array('result'=>false,'msg'=>'邮箱地址及验证码不能为空'));
		}
		$User = M('User');
		$data = I('post.');
		if(!$User->create($data)){
			$this->ajaxReturn(array('result'=>false,'msg'=>$User->getError()));
		}
		extract($data);
		if($code != cookie('email_bind_'.$email)){
			$this->ajaxReturn(array('result'=>false,'msg'=>'验证码错误或已失效'));
		}
		$User->id = session('user.id');
		$User->email = $email;
		if($User->save())
		{
			$this->success('邮箱绑定成功');
		}else{
			$this->ajaxReturn(array('result'=>false,'msg'=>'数据库错误，绑定失败'));
		}
	}
	
	//修改头像
	public function avatar(){
		$this->display();
	}

	//打赏记录
	public function award_record(){
		$user_id=session('user.id');

		//获取用户打赏和被打赏的记录
		$Award = M('Award');
		$map['user_id'] = array('EQ',$user_id);
		$map['award_user_id'] = array('EQ',$user_id);
		$map['user_id'] = array('EQ',$user_id);
		$map['_logic'] = 'or';
		$data = $Award->where($map)->order('created_time')->select();

		$this->assign('data',$data);
		$this->display();
	}

	//重置密码
	public function reset_password(){
		if(!I('post.password')){
			$this->error('密码不能为空');
		}
		$user_id=session('user.id');
		$User = M('User');
		if(!$User->create(I('post.'))){
			$this->error($User->getError());
		}
		$record = $User->getFieldById($user_id,'password');
		if($record != substr_pwd(I('post.old_pwd'))){
			$this->error('原密码输入错误');
		}
		$User->id = $user_id;
		$User->password = substr_pwd(I('post.password'));
		$User->updated_time = time();
		if($User->save()){
			session(null);
			cookie('user_id',null);
			cookie('password',null);
			$this->success('密码重置成功,请使用新密码重新登录',UU('login/login'));
		}else{
			$this->error('数据库错误，密码重置失败');
		}
	}

	//收货地址
	public function address(){
		$Address = D('Address');
		$user_id=session('user.id');
		if(!empty($_POST)){
			$Address->id = (int)I('post.id');
			$Address->name = I('post.name');
			$Address->phone = I('post.phone');
			$Address->address = I('post.address');
			if(!$Address->create()){
				$this->error($Address->getError());
			}
			if($Address->id){
				$Address->updated_time = time();
			}else{
				$Address->created_time = time();
			}
			if($Address->save()){
				$this->success('已保存修改');
			}else{
				$this->error('保存失败');
			}
		}else{
			$array['data'] =$Address->where('user_id='.$user_id)->select();
			$array['title'] = '收货地址';
			$this->assign($array);
			$this->display();
		}
	}

	/**
	 * 订单
	 */
	public function order(){
		$orders = M('Exchange')->where('user_id='.session('user.id'))->select();
		$this->assign('list',$orders);
		$this->assign('title','订单');
		$this->display();
	}

}