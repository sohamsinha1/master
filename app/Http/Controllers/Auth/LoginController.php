<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    // protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    // public function __construct()
    // {
    //     $this->middleware('guest')->except('logout');
    //     $this->middleware('guest:admin')->except('logout');
    //     $this->middleware('guest:writer')->except('logout');
    // }
    // public function showAdminLoginForm()
    // {
    //     return view('auth.login', ['url' => 'admin']);
    // }
    // public function adminLogin(Request $request)
    // {
    //     $this->validate($request, [
    //         'email'   => 'required|email',
    //         'password' => 'required|min:6'
    //     ]);

    //     if (Auth::guard('admin')->attempt(['email' => $request->email, 'password' => $request->password], $request->get('remember'))) {

    //         return redirect()->intended('/admin');
    //     }
    //     return back()->withInput($request->only('email', 'remember'));
    // }
    // public function showWriterLoginForm()
    // {
    //     return view('auth.login', ['url' => 'writer']);
    // }

    // public function writerLogin(Request $request)
    // {
    //     $this->validate($request, [
    //         'email'   => 'required|email',
    //         'password' => 'required|min:6'
    //     ]);

    //     if (Auth::guard('writer')->attempt(['email' => $request->email, 'password' => $request->password], $request->get('remember'))) {

    //         return redirect()->intended('/writer');
    //     }
    //     return back()->withInput($request->only('email', 'remember'));
    // }
    // public function adminLogout(Request $request)
    // {
    //     Auth::guard('admin')->logout();
    //     return redirect()
    //     ->route('login.admin');
    // }
    // public function writerLogout(Request $request)
    // {
    //     // auth('writer')->logout() ;
    //     // Auth::logout();
    //     // return view('auth.login', ['url' => 'writer']);
    //     // Auth::guard('writer')->logout();
    //     // return view('auth.login', ['url' => 'writer']);
        
    //     Auth::guard('writer')->logout();
    //     return redirect()
    //     ->route('login.writer');
    // }

    // This is for insert initially in admins table
// public function insert()
// {
//     $user=User::create([
//                 'name' => 'Soham',
//                 'email' => 'soham@gmail.com',
//                 'password' => Hash::make('123456'),
//             ]);
// }
    public function index()
    {
        return view('auth.login');
    }
    public function processlogin(Request $request)
    {
        $this->validate($request, [
                    'email'   => 'required|email',
                    'password' => 'required'
                ]);        
                if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
                    return redirect()->intended('/profile');
                }
                else
                {
                    return view('auth.login');
                }
        
    }
    
    
    public function logout()
    {
        Auth::logout();
        return redirect('/admin');
    }
    public function pat_list()
    {
        $users = DB::table('book_appointment')->paginate(4);
        return view('auth.patient',['patients' => $users]);
    }
    public function doc_list()
    {
        $users = DB::table('doctors_profile')->paginate(4);
        return view('auth.doctor',['doctors' => $users]);
    }
    public function gnm_list()
    {
        $users = DB::table('nurse_aaya_profile')->paginate(4);
        return view('auth.nurse',['nurses' => $users]);
    }
    public function pat_del($id)
    {
        // $users=DB::table('book_appointment')->where('user_id', '=', $id)->delete();
        // return redirect('/pat_list');
try{
    $user=DB::table('book_appointment')->where('user_id', $id)->update(['is_deleted' => '1',]);
    return redirect('/pat_list')->with('message','Success!');
}catch(\Illuminate\Database\QueryException $ex)
{
    return redirect('/pat_list')->with('message','Success!');
}
        
}
    public function doc_del($id)
    {
        // $users=DB::table('book_appointment')->where('user_id', '=', $id)->delete();
        // return redirect('/pat_list');

        try{
            $user=DB::table('doctors_profile')->where('id', $id)->update(['is_deleted' => '1',]);
        return redirect('/doc_list')->with('message','Success!');

        }catch(\Illuminate\Database\QueryException $ex)
        {
            return redirect('/doc_list')->with('message','Fail!');
        }
        
    }
    public function nur_del($id)
    {
        try{
            $user=DB::table('nurse_aaya_profile')->where('id', $id)->update(['is_deleted' => '1',]);
        return redirect('/gnm_list')->with('message','Success!');
        }catch(\Illuminate\Database\QueryException $ex)
        {
            return redirect('/gnm_list')->with('message','Fail!');
        }
        
    }
    public function doc_appr($id)
    {
        // $users=DB::table('book_appointment')->where('user_id', '=', $id)->delete();
        // return redirect('/pat_list');

        try{

            $doc=DB::table('doctor_verification')->where('doc_id',$id)->first();
            if(!empty($doc))
            {
                return redirect('/doc_list')->with('status','Already approved!');  
            }
            else{
                $user=DB::table('doctors_profile')->where('id', $id)->update(['is_approved' => '1',]);
                DB::insert('insert into doctor_verification (doc_id) values (?)', [$id]);
                return redirect('/doc_list')->with('status','Approved!');
            }
            

        

        }catch(\Illuminate\Database\QueryException $ex)
        {
            return redirect('/doc_list')->with('status','Fail to approve!');
        }
        
    }
    public function image_view()
    {
        
        // $path = Storage::files('app\public\img');
        // $path = Storage::disk('public');
        $path = File::files(public_path('img'));
        
        // $path = File::files('\img');

        $doctors = DB::table('doctors_profile')->paginate(4);        
        return view('auth.doctor',compact('path','doctors'));         
    }

    
}
