<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Auth;
use Mail;

class UsersController extends Controller
{
    public function __construct()
    {
        // $this->middleware('auth');
        //访问权限的判断,除了'show', 'create', 'store','index',必须登录才能访问
        $this->middleware('auth', [
            'except' => ['show', 'create', 'store','index','confirmEmail']
        ]);
    }

    public function index()
    {
        $users = User::paginate(5);
        return view('users.index', compact('users'));
    }

    public function create()
    {
        return view('users.create');
    }

    public function show(User $user)
    {
        //只有当前用户才能访问这个方法
         $this->authorize('view',$user);
        return view('users.show',compact('user'));
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|max:50',
            'email' => 'required|email|unique:users|max:255',
            'password' => 'required|confirmed|min:6'
        ]);
        dump($request);

        $user = $request->except('_token','password_confirmation');

        $user = User::create($user);

        $this->sendEmailConfirmationTo($user);

        //添加一个已经登录了的用户实例,日后可以Auth::user()访问
        //这里因为有了发送邮件的验证,所以把存储用户实例给注释
        // Auth::login($user);

        session()->flash('success', '验证邮件已发送到你的注册邮箱上，请注意查收。');

        // return redirect()->route('users.show',['user'=>$user]);
        return redirect('/home');
    }

    protected function sendEmailConfirmationTo($user)
    {
        $view = 'emails.confirm';
        $data = compact('user');
        $from = 'zhuyusong@aliyun.com';
        $name = 'zhusong';
        $to = $user->email;
        $subject = "感谢注册 Sample 应用！请确认你的邮箱。";

        Mail::send($view, $data, function ($message) use ($from, $name, $to, $subject) {
            $message->from($from, $name)->to($to)->subject($subject);
        });
    }

    public function edit(User $user)
    {
        //只有更新操作是当前用户实例才能更新
        $this->authorize('update', $user);
        return view('users.edit', compact('user'));
    }

    public function update(User $user, Request $request)
    {
        $this->validate($request, [
            'name' => 'required|max:50',
            'password' => 'nullable|confirmed|min:6'
        ]);

        $this->authorize('update', $user);

        $data = [];
        $data['name'] = $request->name;
        if ($request->password) {
            $data['password'] = bcrypt($request->password);
        }
        $user->update($data);

        session()->flash('success', '个人资料更新成功！');

        return redirect()->route('users.show', $user->id);
    }

    public function destroy(User $user)
    {
        //被删除用户不是自己的时候才允许执行删除
        $this->authorize('destroy', $user);
        $user->delete();
        session()->flash('success', '成功删除用户！');
        return back();
    }
    //未登录也能通过路由来访问的控制器
    public function confirmEmail($token)
    {
        $user = User::where('activation_token', $token)->firstOrFail();

        $user->activated = true;
        $user->activation_token = null;
        $user->save();

        Auth::login($user);
        session()->flash('success', '恭喜你，激活成功！');
        return redirect()->route('users.show', [$user]);
    }
}













