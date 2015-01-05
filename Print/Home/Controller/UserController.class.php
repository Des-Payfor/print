<?php

// ===================================================================
// | FileName:      UserController.class.php
// ===================================================================
// | Discription：   UserController 用户控制器
//      <命名规范：>
// ===================================================================
// +------------------------------------------------------------------
// | 云印南开
// +------------------------------------------------------------------
// | Copyright (c) 2014 云印南开团队 All rights reserved.
// +------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +------------------------------------------------------------------
/**
 * Class and Function List:
 * Function list:
 * - index()
 * - auth()
 * - notice()
 * - forget()
 * - change()
 * - logout()
 * - _empty()
 * Classes list:
 * - UserController extends Controller
 */
namespace Home\Controller;
use Think\Controller;

class UserController extends Controller
{
    
    /**
     *用户信息页
     */
    public function index() 
    {
        $id   = Use_id(U('Index/index'));
        if ($id) 
        {
            $User = M('User');
            $data = $User->where("id=" . $id)->find();
            session('student_number', $data['student_number']);
            $this->data = $data;
            $this->display();
        } else
        {
            $this->redirect('Index/index', null,0,'未登录！');
        }
    }
    
    /**
     * auth()
     *登录和注册验证处理
     *@param student_number 学号
     *@param password 密码
     */
    public function auth() 
    {
        $User           = D('User');
        $student_number = I('post.student_number');
        $password       = encode(I('post.password'), $student_number);
        $result         = $User->where("student_number='$student_number'")->find();
        if ($result) 
        {
            if ($result['password'] == $password) 
            {
                session('use_id', $User->id);
                $token = update_token($User->id, C('STUDENT'));
                cookie('token', $token, 3600 * 24 * 30);
                $this->redirect('File/index');
            } else
            {
                $this->error('密码验证错误！');
            }
        } else
        {
            
            if ($User->create()) 
            {
                import(C('VERIFY_WAY'), COMMON_PATH);
                if ($name   = getName($student_number, I('post.password'))) 
                {
                    $data['name']        = $name;
                    $data['student_number']        = $student_number;
                    $data['password']        = $password;
                    $result = $User->add($data);
                    if ($result) 
                    {
                        session('use_id', $result);
                        session('student_number', $student_number);
                        session('first_name', $name);
                        
                        $this->redirect('User/notice');
                    } else
                    {
                        $this->error('注册失败：' . $User->getError());
                    }
                } else
                {
                    $this->error('学校账号实名认证失败！');
                }
            } else
            {
                $this->error('信息不合法：' . $User->getError());
            }
        }
    }
    
    //首次注册
    public function notice() 
    {
        $uid         = session('use_id');
        $stu_number  = session('student_number');
        
        if (session('first_name') && $stu_number) 
        {
            
            $password    = I('post.password');
            $re_password = I('post.re_password');
            if (!$password) 
            {
                $this->data  = session('first_name');
                $this->display();
            } elseif ($password != $re_password) 
            {
                $this->data = session('first_name') . '[密码不一致重新输入]';
                $this->display();
            } else
            {
                $result = M('User')->where('id=' . $uid)->setField('password', encode($password, $stu_number));
                if ($result) 
                {
                    session('first_name', null);
                    $this->redirect('File/add', null,0, '密码修改成功！');
                } else
                {
                    $this->error('密码修改失败！');
                }
            }
        } else
        {
            $this->redirect('Index/index');
        }
    }
    
    //密码找回
    public function forget() 
    {
        if (use_id()) 
        {
            $this->redirect('index');
        }
        
        $student_number = I('post.student_number');
        $urp_password   = I('post.urp_password');
        $password       = I('post.password');
        $re_password    = I('post.re_password');
        if ($password && $re_password && $student_number && $urp_password) 
        {
            import(C('VERIFY_WAY'), COMMON_PATH);
            if (getName($student_number, $urp_password)) 
            {
                if ($password == $re_password) 
                {
                    
                    if (false !== M('User')->where('student_number=' . $student_number)->setField('password', encode($password, $student_number))) 
                    {
                        $this->redirect('Index/index',0,0,"密码重置成功！");
                    } else
                    {
                        $this->error('重置失败！');
                    }
                } else
                {
                    $this->error("密码不一致");
                }
            } else
            {
                $this->error('校园账号验证失败！');
            }
        } else
        {
            $this->display();
        }
    }
    
    /**
     *修改密码
     */
    public function change() 
    {
        $uid                 = use_id(U('Index/index'));
        $deprecated_password = I('post.deprecated_password');
        $password            = I('post.password');
        $re_password         = I('post.re_password');
        if ($uid) 
        {
            $condition['id']                     = $uid;
            $condition['password']                     = encode($deprecated_password, session('student_number'));
            $condition['student_number']                     = session('student_number');
            $User                = D('User');
            $result              = $User->where($condition)->select();
            if ($result) 
            {
                if ($password == $re_password) 
                {
                    $map['id']                     = $uid;
                    M('User')->where($map)->setField('password', encode($password, session('student_number')));
                    $this->redirect('logout'，null,0,'密码修改成功重新登陆！');
                } else
                {
                    $this->error('两次密码输入不一致！');
                }
            } else
            {
                $this->error('原密码错误');
            }
        }
    }
    
    /**
     *注销
     */
    public function logout() 
    {
        delete_token(cookie('token'));
        session(null);
        cookie(null);
        $this->redirect('Index/index');
    }
    
    /**
     *404页
     */
    public function _empty() 
    {
        $this->redirect('index');
    }
}
