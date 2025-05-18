<?php

namespace App\Http\Controllers\Auth;

use App\Helpers\UserSystemInfo;
use App\Http\Controllers\Controller;
use App\Jobs\SendReferralBonusJob;
use App\Jobs\WalletCreateJob;
use App\Models\ContentDetails;
use App\Models\NotificationTemplate;
use App\Models\ReferralBonus;
use App\Models\User;
use App\Models\UserLogin;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Facades\App\Services\BasicService;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse;
use Facades\App\Services\Google\GoogleRecaptchaService;
use Illuminate\Validation\ValidationException;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/user/dashboard';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->theme = template();
        $this->middleware('guest');
    }

    /**
     * Show the application registration form.
     *
     * @return \Illuminate\View\View
     */
    public function showRegistrationForm(Request $request)
    {
        $basic = basicControl();
        if ($basic->registration == 0) {
            return redirect('/')->with('warning', 'Registration Has Been Disabled.');
        }

        $referUser = null;
        if ($request->ref) {
            $referUser = User::where('id', $request->ref)->first();

            if ($referUser) {
                session()->put('referral', $referUser->id);
            }
        }
        $data['referUser'] = $referUser;

        $template = ContentDetails::with(['content'])->whereHas('content', function ($query) {
            $query->whereIn('name', ['login-register', 'social']);
        })->get()->groupBy('content.name');
        return view(template() . 'auth.register', compact('template'), $data);
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param array $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        $basicControl = basicControl();
        $validateData = [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'username' => ['required', 'string', 'max:50', 'unique:users,username'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
            'phone_code' => ['required', 'string'],
            'phone' => ['required', 'string', 'unique:users,phone'],
        ];

        // Recaptcha
        if ($basicControl->google_recaptcha && ($basicControl->google_reCaptcha_status_registration)) {
            $res = GoogleRecaptchaService::responseRecaptcha($_POST['g-recaptcha-response']);
            if (is_null($res)) {
                // Throw a validation error if $res is null
                throw ValidationException::withMessages([
                    'g-recaptcha-response' => __('ReCAPTCHA validation is required.'),
                ]);
            }
        }

        // Manual Recaptcha
        if (($basicControl->manual_recaptcha == 1) && ($basicControl->reCaptcha_status_registration == 1)) {
            $validateData['captcha'] = ['required',
                Rule::when((!empty(request()->captcha) && strcasecmp(session()->get('captcha'), $_POST['captcha']) != 0), ['confirmed']),
            ];
        }

        return Validator::make($data, $validateData, [
            'name.required' => 'Full Name field is required',
            'g-recaptcha-response.required' => 'The reCAPTCHA field is required',
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param array $data
     * @return \App\Models\User
     */
    protected function create(array $data)
    {
        $basic = basicControl();
        $referUser = session()->get('referral');

        return User::create([
            'firstname' => $data['first_name'],
            'lastname' => $data['last_name'],
            'username' => $data['username'],
            'email' => $data['email'],
            'phone_code' => '+' . $data['phone_code'],
            'phone' => $data['phone'],
            'password' => Hash::make($data['password']),
            'email_verification' => ($basic->email_verification) ? 0 : 1,
            'sms_verification' => ($basic->sms_verification) ? 0 : 1,
            'referral_id' => $referUser ?? null,
        ]);
    }

    public function register(Request $request)
    {

        $this->validator($request->all())->validate();

        $user = $this->create($request->all());

        $this->guard()->login($user);

        if ($response = $this->registered($request, $user)) {
            return $response;
        }

        if ($request->ajax()) {
            return route('user.home');
        }

        return $request->wantsJson()
            ? new JsonResponse([], 201)
            : redirect($this->redirectPath());
    }

    protected function registered(Request $request, $user)
    {
        $user->last_login = Carbon::now();
        $user->last_seen = Carbon::now();
        $user->two_fa_verify = ($user->two_fa == 1) ? 0 : 1;

        $notificationTemplates = NotificationTemplate::where('notify_for', 0)->get()->unique('template_key');
        $email_key = [];
        $sms_key = [];
        $push_key = [];
        $in_app_key = [];

        foreach ($notificationTemplates as $template) {
            if ($template->status['mail']) {
                array_push($email_key, $template->template_key);
            }

            if ($template->status['sms']) {
                array_push($sms_key, $template->template_key);
            }

            if ($template->status['in_app']) {
                array_push($in_app_key, $template->template_key);
            }

            if ($template->status['push']) {
                array_push($push_key, $template->template_key);
            }
        }

        $user->email_key = $email_key;
        $user->sms_key = $sms_key;
        $user->push_key = $push_key;
        $user->in_app_key = $in_app_key;

        $user->save();

        dispatch(new WalletCreateJob($user->id))->chain([
            new SendReferralBonusJob($user->id, 'registration_bonus'),
        ]);

        $info = @json_decode(json_encode(getIpInfo()), true);
        $ul['user_id'] = $user->id;

        $ul['longitude'] = (!empty(@$info['long'])) ? implode(',', $info['long']) : null;
        $ul['latitude'] = (!empty(@$info['lat'])) ? implode(',', $info['lat']) : null;
        $ul['country_code'] = (!empty(@$info['code'])) ? implode(',', $info['code']) : null;
        $ul['location'] = (!empty(@$info['city'])) ? implode(',', $info['city']) . (" - " . @implode(',', @$info['area']) . "- ") . @implode(',', $info['country']) . (" - " . @implode(',', $info['code']) . " ") : null;
        $ul['country'] = (!empty(@$info['country'])) ? @implode(',', @$info['country']) : null;

        $ul['ip_address'] = UserSystemInfo::get_ip();
        $ul['browser'] = UserSystemInfo::get_browsers();
        $ul['os'] = UserSystemInfo::get_os();
        $ul['get_device'] = UserSystemInfo::get_device();

        UserLogin::create($ul);

    }

    protected function guard()
    {
        return Auth::guard();
    }

}
