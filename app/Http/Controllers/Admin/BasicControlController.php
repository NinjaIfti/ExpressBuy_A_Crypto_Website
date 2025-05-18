<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\GoogleAuthenticator;
use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Announcement;
use App\Models\BasicControl;
use App\Traits\Notify;
use hisorange\BrowserDetect\Parser as Browser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Facades\App\Services\BasicService;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use PragmaRX\Google2FA\Google2FA;

class BasicControlController extends Controller
{
    use Notify;

    public function index($settings = null)
    {
        $settings = $settings ?? 'settings';
        abort_if(!in_array($settings, array_keys(config('generalsettings'))), 404);
        $settingsDetails = config("generalsettings.{$settings}");
        return view('admin.control_panel.settings', compact('settings', 'settingsDetails'));
    }

    public function basicControl()
    {
        $data['basicControl'] = basicControl();
        $data['timeZones'] = timezone_identifiers_list();
        $data['dateFormat'] = config('dateformat');

        return view('admin.control_panel.basic_control', $data);
    }

    public function basicControlUpdate(Request $request)
    {
        $request->validate([
            'site_title' => 'required|string|min:3|max:100',
            'time_zone' => 'required|string',
            'base_currency' => 'required|string|min:1|max:100',
            'currency_symbol' => 'required|string|min:1|max:100',
            'fraction_number' => 'required|integer|not_in:0',
            'paginate' => 'required|integer|not_in:0',
            'date_format' => 'required',
            'admin_prefix' => 'required|string|min:3|max:100',
            'primary_color' => 'required|string'
        ]);

        try {
            $basic = BasicControl();
            $response = BasicControl::updateOrCreate([
                'id' => $basic->id ?? ''
            ], [
                'site_title' => $request->site_title,
                'time_zone' => $request->time_zone,
                'base_currency' => $request->base_currency,
                'currency_symbol' => $request->currency_symbol,
                'fraction_number' => $request->fraction_number,
                'date_time_format' => $request->date_format,
                'paginate' => $request->paginate,
                'admin_prefix' => $request->admin_prefix,
                'primary_color' => $request->primary_color,
            ]);

            if (!$response)
                throw new Exception('Something went wrong, when updating data');

            $env = [
                'APP_TIMEZONE' => $response->time_zone,
                'APP_DEBUG' => $response->error_log == 0 ? 'true' : 'false'
            ];
            BasicService::setEnv($env);

            session()->flash('success', 'Basic Control Configure Successfully');
            Artisan::call('optimize:clear');
            return back();
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function exchangeControl(Request $request)
    {
        $basicControl = basicControl();
        if ($request->method() == 'GET') {
            return view('admin.control_panel.exchange_control', compact('basicControl'));
        } elseif ($request->method() == 'POST') {
            try {
                $request->validate([
                    'exchange_rate' => 'required|min:0|not_in:0',
                    'floating_rate_update_time' => 'required|numeric|min:20',
                    'floating_rate_update_status' => 'required|numeric|in:0,1',
                    'crypto_send_time' => 'required|numeric|min:5',
                    'fiat_send_time' => 'required|numeric|min:5',
                ]);

                $response = BasicControl::updateOrCreate([
                    'id' => $basicControl->id ?? ''
                ], [
                    'exchange_rate' => $request->exchange_rate,
                    'refund_exchange_status' => $request->refund_exchange_status,
                    'refund_exchange_note' => $request->refund_exchange_note,
                    'floating_rate_update_time' => $request->floating_rate_update_time,
                    'floating_rate_update_status' => $request->floating_rate_update_status,
                    'crypto_send_time' => $request->crypto_send_time,
                    'fiat_send_time' => $request->fiat_send_time,
                ]);

                if (!$response) throw new Exception('Something went wrong, when updating data');

                session()->flash('success', 'Exchange Configure Successfully');
                Artisan::call('optimize:clear');
                return back();
            } catch (\Exception $e) {
                return back()->with('error', $e->getMessage());
            }
        }
    }

    public function twoFaControl()
    {
        $basic = basicControl();
        $admin = Auth::guard('admin')->user();
        $google2fa = new Google2FA();
        $secret = $admin->two_fa_code ?? $this->generateSecretKeyForUser($admin);

        // Generate QR code URL
        $qrCodeUrl = $google2fa->getQRCodeUrl(
            auth()->user()->username,
            $basic->site_title,
            $secret
        );
        return view('admin.control_panel.twofa_control', compact('secret', 'qrCodeUrl'));
    }

    public function twoFaRegenerate()
    {
        $admin = Auth::guard('admin')->user();
        $admin->two_fa_code = null;
        $admin->save();
        session()->flash('success', 'Re-generate Successfully');
        return redirect()->route('admin.twoFa.control');
    }

    private function generateSecretKeyForUser(Admin $user)
    {
        $google2fa = new Google2FA();
        $secret = $google2fa->generateSecretKey();
        $user->update(['two_fa_code' => $secret]);

        return $secret;
    }

    public function twoFaEnable(Request $request)
    {
        $user = Auth::guard('admin')->user();
        $secret = auth()->user()->two_fa_code;
        $google2fa = new Google2FA();
        $valid = $google2fa->verifyKey($secret, $request->code);

        if ($valid) {
            $user['two_fa'] = 1;
            $user['two_fa_verify'] = 1;
            $user->save();

            $browser = new Browser();
            $this->adminMail('TWO_STEP_ENABLED', [
                'action' => 'Enabled',
                'code' => $user->two_fa_code,
                'ip' => request()->ip(),
                'browser' => $browser->browserName() . ', ' . $browser->platformName(),
                'time' => date('d M, Y h:i:s A'),
            ]);

            return back()->with('success', 'Google Authenticator Has Been Enabled.');
        } else {
            return back()->with('error', 'Wrong Verification Code.');
        }
    }

    public function twoFaDisable(Request $request)
    {
        $this->validate($request, [
            'password' => 'required',
        ]);

        if (!Hash::check($request->password, auth()->user()->password)) {
            return back()->with('error', 'Incorrect password. Please try again.');
        }

        Auth::guard('admin')->user()->update([
            'two_fa' => 0,
            'two_fa_verify' => 1,
        ]);
        return back()->with('success', 'Two-step authentication disabled successfully.');
    }

    public function twoFaCheck(Request $request)
    {
        if ($request->method() == 'GET') {
            return view('admin.auth.twofa-verify');
        } elseif ($request->method() == 'POST') {
            $this->validate($request, [
                'code' => 'required',
            ], [
                'code.required' => '2 FA verification code is required',
            ]);

            $ga = new GoogleAuthenticator();
            $user = Auth::guard('admin')->user();
            $getCode = $ga->getCode($user->two_fa_code);
            if ($getCode == trim($request->code)) {
                $user->two_fa_verify = 1;
                $user->save();
                return redirect()->intended(route('admin.dashboard'));
            }
            return back()->with('error', 'Wrong Verification Code.');
        }
    }


    public function basicControlActivityUpdate(Request $request)
    {

        $request->validate([
            'strong_password' => 'nullable|numeric|in:0,1',
            'registration' => 'nullable|numeric|in:0,1',
            'error_log' => 'nullable|numeric|in:0,1',
            'is_active_cron_notification' => 'nullable|numeric|in:0,1',
            'has_space_between_currency_and_amount' => 'nullable|numeric|in:0,1',
            'is_force_ssl' => 'nullable|numeric|in:0,1',
            'is_currency_position' => 'nullable|string|in:left,right',
        ]);

        try {
            $basic = BasicControl();
            $response = BasicControl::updateOrCreate([
                'id' => $basic->id ?? ''
            ], [
                'error_log' => $request->error_log,
                'strong_password' => $request->strong_password,
                'registration' => $request->registration,
                'is_active_cron_notification' => $request->is_active_cron_notification,
                'has_space_between_currency_and_amount' => $request->has_space_between_currency_and_amount,
                'is_currency_position' => $request->is_currency_position,
                'is_force_ssl' => $request->is_force_ssl,
                'default_mode' => $request->default_mode,
                'changeable_mode' => $request->changeable_mode,
            ]);

            if (!$response) throw new Exception('Something went wrong, when updating data');

            session()->flash('success', 'Basic Control Configure Successfully');
            Artisan::call('optimize:clear');
            return back();
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function currencyExchangeApiConfig()
    {

        $data['scheduleList'] = config('schedulelist.schedule_list');
        $data['basicControl'] = basicControl();
        return view('admin.control_panel.exchange_api_setting', $data);

    }

    public function currencyExchangeApiConfigUpdate(Request $request)
    {
        $request->validate([
            'currency_layer_access_key' => 'required|string',
            'coin_market_cap_app_key' => 'required|string',
        ]);

        try {
            $basicControl = basicControl();
            $basicControl->update([
                'currency_layer_access_key' => $request->currency_layer_access_key,
                'currency_layer_auto_update' => $request->currency_layer_auto_update,
                'currency_layer_auto_update_at' => $request->currency_layer_auto_update_at,
                'coin_market_cap_app_key' => $request->coin_market_cap_app_key,
                'coin_market_cap_auto_update' => $request->coin_market_cap_auto_update,
                'coin_market_cap_auto_update_at' => $request->coin_market_cap_auto_update_at
            ]);
            return back()->with('success', 'Configuration changes successfully');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function announcement(Request $request)
    {
        $announcement = Announcement::firstOrCreate();
        if ($request->method() == 'GET') {
            return view('admin.announcement.index', compact('announcement'));
        } elseif ($request->method() == 'POST') {
            $request->validate([
                'announcement_text' => 'required|min:1',
                'btn_name' => 'sometimes|min:1',
                'btn_link' => 'sometimes|min:1',
                'btn_display' => 'required|in:0,1',
                'status' => 'required|in:0,1',
            ]);
            $fillData = $request->only($announcement->getFillable());
            $announcement->fill($fillData)->save();
            return back()->with('success', 'Updated Successfully');
        }
    }
}
