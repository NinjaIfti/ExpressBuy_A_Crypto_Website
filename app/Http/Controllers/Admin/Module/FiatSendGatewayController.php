<?php

namespace App\Http\Controllers\Admin\Module;

use App\Http\Controllers\Controller;
use App\Models\FiatCurrency;
use App\Models\FiatSendGateway;
use App\Traits\Upload;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class FiatSendGatewayController extends Controller
{
    use Upload;

    public function index()
    {
        $data['methods'] = FiatSendGateway::orderBy('id', 'desc')->get();
        return view('admin.fiat-send-gateway.list', $data);
    }

    public function create()
    {
        $data['basicControl'] = basicControl();
        $data['fiatCurrencies'] = FiatCurrency::select(['id', 'status', 'sort_by', 'code'])
            ->where('status', 1)->orderBy('sort_by', 'ASC')->get();
        return view('admin.fiat-send-gateway.create', $data);
    }

    public function store(Request $request)
    {
        $rules = [
            'name' => "required|min:3",
            'supported_currency' => "required|min:1",
            'description' => 'sometimes|string|min:3',
            'status' => "nullable|integer|in:0,1",
            'field_name.*' => 'required|string',
            'input_type.*' => 'required|in:text,textarea,file,date,number',
            'is_required.*' => 'required|in:required,optional',
            'image' => 'required|mimes:png,jpeg,gif|max:2048',
        ];


        $input_form = [];
        if ($request->has('field_name')) {
            for ($a = 0; $a < count($request->field_name); $a++) {
                $arr = array();
                $arr['field_name'] = clean($request->field_name[$a]);
                $arr['field_label'] = $request->field_name[$a];
                $arr['type'] = $request->input_type[$a];
                $arr['validation'] = $request->is_required[$a];
                $input_form[$arr['field_name']] = $arr;
            }
        }

        if ($request->hasFile('image')) {
            try {
                $image = $this->fileUpload($request->image, config('filelocation.fiatGateway.path'), null, null, 'webp', 60);
                if ($image) {
                    $gatewayImage = $image['path'];
                    $driver = $image['driver'];
                }
            } catch (\Exception $exp) {
                return back()->with('alert', 'Image could not be uploaded.');
            }
        }

        $request->validate($rules);

        $response = FiatSendGateway::create([
            'name' => $request->name,
            'image' => $gatewayImage ?? null,
            'driver' => $driver ?? null,
            'parameters' => $input_form,
            'supported_currency' => $request->supported_currency,
            'description' => $request->description,
            'status' => $request->status,
        ]);

        if (!$response) {
            throw new \Exception('Unexpected error! Please try again.');
        }

        return back()->with('success', 'Gateway data has been add successfully.');

    }

    public function edit($id)
    {
        $data['basicControl'] = basicControl();
        $data['method'] = FiatSendGateway::where('id', $id)->firstOr(function () {
            throw new \Exception("Invalid Gateways Request");
        });
        $data['fiatCurrencies'] = FiatCurrency::select(['id', 'status', 'sort_by', 'code'])
            ->where('status', 1)->orderBy('sort_by', 'ASC')->get();

        return view('admin.fiat-send-gateway.edit', $data);
    }


    public function update(Request $request, $id)
    {
        $rules = [
            'name' => "required|min:3",
            'supported_currency' => "required|min:1",
            'description' => 'sometimes|string|min:3',
            'status' => "nullable|integer|in:0,1",
            'field_name.*' => 'required|string',
            'input_type.*' => 'required|in:text,textarea,file,date,number',
            'is_required.*' => 'required|in:required,optional',
            'image' => 'nullable|mimes:png,jpeg,gif|max:2048',
        ];


        $gateway = FiatSendGateway::where('id', $id)->firstOr(function () {
            throw new \Exception("Invalid Gateways Request");
        });


        $input_form = [];
        if ($request->has('field_name')) {
            for ($a = 0; $a < count($request->field_name); $a++) {
                $arr = array();
                $arr['field_name'] = clean($request->field_name[$a]);
                $arr['field_label'] = $request->field_name[$a];
                $arr['type'] = $request->input_type[$a];
                $arr['validation'] = $request->is_required[$a];
                $input_form[$arr['field_name']] = $arr;
            }
        }

        $request->validate($rules);

        if ($request->hasFile('image')) {
            try {
                $image = $this->fileUpload($request->image, config('filelocation.fiatGateway.path'), null, null, 'webp', 60, $gateway->image, $gateway->driver);
                if ($image) {
                    $gatewayImage = $image['path'];
                    $driver = $image['driver'];
                }
            } catch (\Exception $exp) {
                return back()->with('alert', 'Image could not be uploaded.');
            }
        }

        $response = $gateway->update([
            'name' => $request->name,
            'image' => $gatewayImage ?? $gateway->image,
            'driver' => $driver ?? $gateway->driver,
            'parameters' => $input_form,
            'supported_currency' => $request->supported_currency,
            'description' => $request->description,
            'status' => $request->status,

        ]);

        if (!$response) {
            throw new \Exception('Unexpected error! Please try again.');
        }

        return back()->with('success', 'Gateway data has been updated.');
    }

    public function statusChange(Request $request)
    {
        try {
            $gateway = FiatSendGateway::findOrFail($request->id);
            $gateway->update([
                'status' => $gateway->status == 1 ? 0 : 1
            ]);
            return back()->with('success', 'Gateway status updated successfully.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
