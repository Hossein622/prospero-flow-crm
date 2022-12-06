<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\MainController;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CustomerImportSaveController extends MainController
{
    /**
     * @param  Request  $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function save(Request $request)
    {
        if (! $request->hasFile('upload')) {
            return redirect('/customer')->withErrors(__("Upload file can't be in blank"));
        }

        $file = $request->file('upload');
        $extension = $file->getClientOriginalExtension();

        //if($extension != 'csv')
        //    return redirect('/customer')->withErrors(__("File upload only accept .csv"));

        $filePath = $file->getPath().DIRECTORY_SEPARATOR.$file->getFilename();

        try {
            $handle = fopen($filePath, 'r');
        } catch (\Throwable $t) {
            return redirect('/customer')->withErrors(__("Can't read uploaded file"));
        }

        // HEADER (17)
        //name;business_name;phone;phone2;email;email2;website;country_id;city;notes;facebook;instagram;linkedin;twitter;youtube;tiktok;tags
        $rowCount = 0;
        $separator = (! empty($request->separator)) ? $request->separator : ';';
        while (($data = fgetcsv($handle, 1000, $separator)) !== false) {
            //Skip header starting in 1
            if ($data[0] == 'name') {
                continue;
            }

            // if row is empty continue
            if (empty($data[0])) {
                continue;
            }

            $country = trim($data[7]);
            $customer = new Customer();

            $customer->company_id = Auth::user()->company_id;

            $customer->name = $data[0];
            $customer->business_name = $data[1];
            $customer->phone = str_replace([' ', '(', ')', '.', '-'], '', $data[2]);
            $customer->phone2 = str_replace([' ', '(', ')', '.', '-'], '', $data[3]);
            $customer->email = $data[4];
            $customer->email2 = $data[5];
            $customer->website = rtrim($data[6], '/');
            $customer->country_id = strlen($country) == 2 ? strtolower($country) : '';
            $customer->city = $data[8];
            $customer->notes = $data[9];
            $customer->facebook = (isset($data[10])) ? $data[10] : null;
            $customer->instagram = (isset($data[11])) ? $data[11] : null;
            $customer->linkedin = (isset($data[12])) ? $data[12] : null;
            $customer->twitter = (isset($data[13])) ? $data[13] : null;
            $customer->youtube = (isset($data[14])) ? $data[14] : null;
            $customer->tiktok = (isset($data[15])) ? $data[15] : null;

            $customer->tags = (isset($data[16])) ? $data[16] : null;

            $customer->seller_id = Auth::user()->id;
            $customer->created_at = now();
            try {
                $customer->save();
                $rowCount++;
            } catch (\Throwable $t) {
                Log::error($t->getMessage().' | row number:'.($rowCount + 1));
            }
        }
        fclose($handle);

        $status = ($rowCount > 0) ? true : false;

        $response = [
            'status' => $status,
            'message' => ($status) ? 'Customers imported :count successfully' : 'An error occurred while importing customers',
            'count' => $rowCount,
        ];

        return redirect('/customer')->with($response);
    }
}
