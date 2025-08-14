<?php

namespace App\Http\Controllers;

use Exception;
use App\FileUploadMedia;
use App\Models\HeaderFooter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class HeaderAndFooterController extends Controller
{
    public function index()
    {
        $headerFooter = Cache::remember('header_footer_data', 60 * 24, function () {
            return HeaderFooter::find(1);
        });

        return response()->json([
            'status' => 'success',
            'message' => $headerFooter,
        ], 200);
    }

    public function store(Request $request)
    {
        try {

            // return $request->all();

            $request->validate([
                'logo' => 'required',
                'phone' => 'required|string|max:20',
                'messenger' => 'nullable|string',
                'whatsapp' => 'nullable|string',
                'email' => 'required|email|max:255',
                'address' => 'required|string',
                'short_desc' => 'required|string',
                'copy_right' => 'required|string',
                'social1' => 'nullable|url|max:255',
                'social2' => 'nullable|url|max:255',
                'social3' => 'nullable|url|max:255',
                'social4' => 'nullable|url|max:255',
                'social5' => 'nullable|url|max:255',
                'social6' => 'nullable|url|max:255',
                'payment1' => 'nullable|max:255',
                'payment2' => 'nullable|max:255',
                'payment3' => 'nullable|max:255',
                'payment4' => 'nullable|max:255',
                'payment5' => 'nullable|max:255',
                'payment6' => 'nullable|max:255',
            ]);

            $logo = $request->input('logo');

            if ($request->hasFile('logo')) {


                $logo = FileUploadMedia::upload($request->file('logo'), 'logo', 'headerFooter', 'public', $request->oldLogo);
            }

            $favicon = $request->input('favicon');

            if ($request->hasFile('favicon')) {
                $favicon = FileUploadMedia::upload($request->file('favicon'), 'favicon', 'headerFooter', 'public', $request->oldFavicon);
            }

            $phone = $request->input('phone');
            $messenger = $request->input('messenger');
            $whatsapp = $request->input('whatsapp');
            $mail = $request->input('email');
            $address = $request->input('address');
            $short_desc = $request->input('short_desc');
            $copy_right = $request->input('copy_right');
            $social1 = $request->input('social1');
            $social2 = $request->input('social2');
            $social3 = $request->input('social3');
            $social4 = $request->input('social4');
            $social5 = $request->input('social5');
            $social6 = $request->input('social6');

            $payment1 = $request->input('payment1');
            $payment2 = $request->input('payment2');
            $payment3 = $request->input('payment3');
            $payment4 = $request->input('payment4');
            $payment5 = $request->input('payment5');
            $payment6 = $request->input('payment6');



            if ($request->hasFile('payment1')) {
                $payment1 = FileUploadMedia::upload($request->file('payment1'), 'payment1', 'headerFooter', 'public', $request->oldPayment1);
            }


            if ($request->hasFile('payment2')) {
                $payment2 = FileUploadMedia::upload($request->file('payment2'), 'payment2', 'headerFooter', 'public', $request->oldPayment2);
            }

            if ($request->hasFile('payment3')) {
                $payment3 = FileUploadMedia::upload($request->file('payment3'), 'payment3', 'headerFooter', 'public', $request->oldPayment3);
            }

            if ($request->hasFile('payment4')) {
                $payment4 = FileUploadMedia::upload($request->file('payment4'), 'payment4', 'headerFooter', 'public', $request->oldPayment4);
            }

            if ($request->hasFile('payment5')) {
                $payment5 = FileUploadMedia::upload($request->file('payment5'), 'payment5', 'headerFooter', 'public', $request->oldPayment5);
            }

            if ($request->hasFile('payment6')) {
                $payment6 = FileUploadMedia::upload($request->file('payment6'), 'payment6', 'headerFooter', 'public', $request->oldPayment6);
            }

            $data = HeaderFooter::where('id', 1)->update([
                'logo' => $logo,
                'favicon' => $favicon,
                'phone' => $phone,
                'messenger' => $messenger,
                'whatsapp' => $whatsapp,
                'mail' => $mail,
                'address' => $address,
                'short_desc' => $short_desc,
                'copy_right' => $copy_right,
                'social1' => $social1,
                'social2' => $social2,
                'social3' => $social3,
                'social4' => $social4,
                'social5' => $social5,
                'social6' => $social6,
                'payment1' => $payment1,
                'payment2' => $payment2,
                'payment3' => $payment3,
                'payment4' => $payment4,
                'payment5' => $payment5,
                'payment6' => $payment6
            ]);

            if (!$data) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Header and Footer not updated',
                ], 200);
            } else {

                return response()->json([
                    'status' => 'success',
                    'message' => 'Header and Footer updated successfully',
                ], 200);
            }
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 200);
        }
    }
}
