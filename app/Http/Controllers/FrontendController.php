<?php

namespace App\Http\Controllers;

use App\Mail\SendMail;
use App\Models\BuyRequest;
use App\Models\ContentDetails;
use App\Models\ExchangeRequest;
use App\Models\PageDetail;
use App\Models\SellRequest;
use App\Models\Subscribe;
use App\Models\Wallet;
use App\Traits\Frontend;
use App\Traits\Notify;
use App\Traits\SendNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Stevebauman\Purify\Facades\Purify;
use Facades\App\Services\BasicService;

class FrontendController extends Controller
{
    use Notify, Frontend, SendNotification;

    public function __construct()
    {
        $this->theme = template();
    }

    public function page($slug = '/')
    {


        try {
            $selectedTheme = basicControl()->theme ?? 'light';
            $existingSlugs = collect([]);
            DB::table('pages')->select('slug')->get()->map(function ($item) use ($existingSlugs) {
                $existingSlugs->push($item->slug);
            });
            if (!in_array($slug, $existingSlugs->toArray())) {
                abort(404);
            }

            $pageDetails = PageDetail::with('page')
                ->whereHas('page', function ($query) use ($slug, $selectedTheme) {
                    $query->where(['slug' => $slug, 'template_name' => $selectedTheme]);
                })
                ->first();

            $pageSeo = [
                'page_title' => optional($pageDetails->page)->page_title,
                'meta_title' => optional($pageDetails->page)->meta_title,
                'meta_keywords' => implode(',', optional($pageDetails->page)->meta_keywords ?? []),
                'meta_description' => optional($pageDetails->page)->meta_description,
                'og_description' => optional($pageDetails->page)->og_description,
                'meta_robots' => optional($pageDetails->page)->meta_robots,
                'meta_image' => getFile(optional($pageDetails->page)->meta_image_driver, optional($pageDetails->page)->meta_image),
                'breadcrumb_image' => optional($pageDetails->page)->breadcrumb_status ?
                    getFile(optional($pageDetails->page)->breadcrumb_image_driver, optional($pageDetails->page)->breadcrumb_image) : null,
            ];

            $sectionsData = $this->getSectionsData($pageDetails->sections, $pageDetails->content, $selectedTheme);
            return view("themes.{$selectedTheme}.page", compact('sectionsData', 'pageSeo'));

        } catch (\Exception $exception) {
            \Cache::forget('ConfigureSetting');
            if ($exception->getCode() == 404) {
                abort(404);
            }
            if ($exception->getCode() == 403) {
                abort(403);
            }
            if ($exception->getCode() == 401) {
                abort(401);
            }
            if ($exception->getCode() == 503) {
                return redirect()->route('maintenance');
            }
            if ($exception->getCode() == "42S02") {
                die($exception->getMessage());
            }
            if ($exception->getCode() == 1045) {
                die("Access denied. Please check your username and password.");
            }
            if ($exception->getCode() == 1044) {
                die("Access denied to the database. Ensure your user has the necessary permissions.");
            }
            if ($exception->getCode() == 1049) {
                die("Unknown database. Please verify the database name exists and is spelled correctly.");
            }
            if ($exception->getCode() == 2002) {
                die("Unable to connect to the MySQL server. Check the database host and ensure the server is running.");
            }
            if ($exception->getCode() == 0) {
                abort(404);
            }
            return redirect()->route('instructionPage');
        }
    }

    public function blogDetails(Request $request)
    {
        $search = $request->all();
        $data['blogDetails'] = ContentDetails::select(['id', 'description', 'content_id', 'created_at'])->with('content')
            ->where('id', $request->id)->firstOrFail();

        $data['popularContentDetails'] = ContentDetails::select('id', 'content_id', 'description', 'created_at')
            ->where('id', '!=', $request->id)
            ->whereHas('content', function ($query) {
                return $query->where('type', 'multiple')->whereIn('name', ['blog']);
            })
            ->when(isset($search['title']), function ($query) use ($search) {
                $query->where('description', 'LIKE', '%' . $search['title'] . '%');
            })
            ->get()->groupBy('content.name');

        $selectedTheme = basicControl()->theme;
        $pageDetails = PageDetail::with('page')
            ->whereHas('page', function ($query) use ($selectedTheme) {
                $query->where(['slug' => 'blog', 'template_name' => $selectedTheme]);
            })
            ->first();

        $pageSeo = [
            'page_title' => 'Blog Details',
            'breadcrumb_image' => optional($pageDetails->page)->breadcrumb_status ? getFile(optional($pageDetails->page)->breadcrumb_image_driver, optional($pageDetails->page)->breadcrumb_image) : null,
        ];

        return view($this->theme . 'blog_details', $data, compact('pageSeo'));
    }


    public function subscribe(Request $request)
    {
        $purifiedData = $request->all();
        $validationRules = [
            'email' => 'required|email|min:8|max:100|unique:subscribes',
        ];
        $validate = Validator::make($purifiedData, $validationRules);
        if ($validate->fails()) {
            session()->flash('error', 'Email Field is required');
            return back()->withErrors($validate)->withInput();
        }
        $purifiedData = (object)$purifiedData;

        $subscribe = new Subscribe();
        $subscribe->email = $purifiedData->email;
        $subscribe->save();

        return back()->with('success', 'Subscribed successfully');
    }

    public function contactSend(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|max:50',
            'email' => 'required|email|max:91',
            'subject' => 'required|max:100',
            'message' => 'required|max:1000',
        ]);
        $requestData = $request->except('_token', '_method');

        $name = $requestData['name'];
        $email_from = $requestData['email'];
        $subject = $requestData['subject'];
        $message = $requestData['message'] . "<br>Regards<br>" . $name;
        $from = $email_from;

        Mail::to(basicControl()->sender_email)->send(new SendMail($from, $subject, $message));
        return back()->with('success', 'Mail has been sent');
    }

    public function tracking(Request $request)
    {
        $data = array();
        if ($request->trx_id) {
            $firstCharacter = substr($request->trx_id, 0, 1);
            if ($firstCharacter == 'E') {
                $exchange = ExchangeRequest::whereIn('status', [2, 3, 5, 6])->where('utr', $request->trx_id)->latest()->first();
                if ($exchange) {
                    $data['type'] = 'exchange';
                    $data['object'] = $exchange;
                }
            } elseif ($firstCharacter == 'B') {
                $buy = BuyRequest::whereIn('status', [2, 3, 5, 6])->where('utr', $request->trx_id)->latest()->first();
                if ($buy) {
                    $data['type'] = 'buy';
                    $data['object'] = $buy;
                }
            } elseif ($firstCharacter == 'S') {
                $sell = SellRequest::whereIn('status', [2, 3, 5, 6])->where('utr', $request->trx_id)->latest()->first();
                if ($sell) {
                    $data['type'] = 'sell';
                    $data['object'] = $sell;
                }
            }
        }
        return view($this->theme . 'tracking', $data);
    }

    public function getAssetsBalance()
    {
        if (auth()->check()) {
            $wallets = Wallet::with(['cryptoCurrency:id,name,code,usd_rate,image,driver'])
                ->where('user_id', auth()->id())->get();

            return response()->json(['status' => 'success', 'wallets' => $wallets]);

        }
        return response()->json(['status' => 'error']);
    }
}
