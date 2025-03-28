<?php

namespace App\Http\Controllers\Dr\Panel\DoctorsClinic;

use App\Http\Controllers\Dr\Controller;
use App\Models\Clinic;
use App\Models\Zone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class DoctorsClinicManagementController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $doctorId = Auth::guard('doctor')->user()->id ?? Auth::guard('secretary')->user()->doctor_id;

        // بازیابی مطب‌ها با اطلاعات شهر و استان
        $clinics = Clinic::where('doctor_id', $doctorId)
            ->with(['city', 'province'])
            ->get();
        // بازیابی و کش کردن اطلاعات استان‌ها و شهرها
        $zones = Cache::remember('zones', 86400, function () {
            return Zone::where('status', 1)
                ->orderBy('sort')
                ->get(['id', 'name', 'parent_id', 'level']);
        });

        // دسته‌بندی اطلاعات به استان و شهر
        $provinces = $zones->where('level', 1);                       // سطح 1 => استان‌ها
        $cities    = $zones->where('level', 2)->groupBy('parent_id'); // سطح 2 => شهرها

        // پاسخ به درخواست‌های Ajax
        if ($request->ajax()) {
            return response()->json([
                'clinics'   => $clinics,
                'provinces' => $provinces,
                'cities'    => $cities,
            ]);
        }

        // ارسال داده‌ها به ویو
        return view('dr.panel.doctors-clinic.index', compact('clinics', 'provinces', 'cities'));
    }

    public function getProvincesAndCities()
    {
        // کش کردن لیست استان‌ها و شهرها برای یک روز
        $zones = Cache::remember('zones', 86400, function () {
            return Zone::where('status', 1)
                ->orderBy('sort')
                ->get(['id', 'name', 'parent_id', 'level']);
        });

        // دسته‌بندی داده‌ها به استان‌ها و شهرها
        $provinces = $zones->where('level', 1)->values();             // سطح 1 => استان‌ها
        $cities    = $zones->where('level', 2)->groupBy('parent_id'); // سطح 2 => شهرها

        return view('dr.panel.doctors-clinic.index', compact('provinces', 'cities'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'            => 'required|string|max:255',
            'phone_numbers'   => 'required|array|min:1',
            'phone_numbers.*' => 'required|string|max:15',
            'address'         => 'nullable|string',
            'province_id'     => 'required|exists:zone,id',
            'city_id'         => 'required|exists:zone,id',
            'postal_code'     => 'nullable|string',
            'description'     => 'nullable|string',
        ], [
            'name.required'            => 'وارد کردن نام مطب الزامی است.',
            'name.string'              => 'نام مطب باید یک رشته معتبر باشد.',
            'name.max'                 => 'نام مطب نباید بیشتر از 255 کاراکتر باشد.',
            'phone_numbers.required'   => 'وارد کردن حداقل یک شماره موبایل الزامی است.',
            'phone_numbers.*.required' => 'وارد کردن شماره موبایل الزامی است.',
            'phone_numbers.*.string'   => 'شماره موبایل باید یک رشته معتبر باشد.',
            'phone_numbers.*.max'      => 'شماره موبایل نباید بیشتر از 15 کاراکتر باشد.',
            'address.string'           => 'آدرس باید یک رشته معتبر باشد.',
            'province_id.required'     => 'انتخاب استان الزامی است.',
            'province_id.exists'       => 'استان انتخاب‌شده معتبر نیست.',
            'city_id.required'         => 'انتخاب شهر الزامی است.',
            'city_id.exists'           => 'شهر انتخاب‌شده معتبر نیست.',
            'postal_code.string'       => 'کد پستی باید یک رشته معتبر باشد.',
            'description.string'       => 'توضیحات باید یک رشته معتبر باشد.',
        ]);

        Clinic::create([
            'doctor_id'     => Auth::guard('doctor')->user()->id ?? Auth::guard('secretary')->user()->doctor_id,
            'name'          => $request->name,
            'phone_numbers' => json_encode($request->phone_numbers),
            'address'       => $request->address,
            'province_id'   => $request->province_id,
            'city_id'       => $request->city_id,
            'postal_code'   => $request->postal_code,
            'description'   => $request->description,
        ]);

        return response()->json(['message' => 'مطب با موفقیت اضافه شد']);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name'            => 'required|string|max:255',
            'phone_numbers'   => 'required|array|min:1',
            'phone_numbers.*' => 'required|string|max:15',
            'address'         => 'nullable|string',
            'province_id'     => 'required|exists:zone,id',
            'city_id'         => 'required|exists:zone,id',
            'postal_code'     => 'nullable|string',
            'description'     => 'nullable|string',
        ], [
            'name.required'            => 'وارد کردن نام مطب الزامی است.',
            'name.string'              => 'نام مطب باید یک رشته معتبر باشد.',
            'name.max'                 => 'نام مطب نباید بیشتر از 255 کاراکتر باشد.',
            'phone_numbers.required'   => 'وارد کردن حداقل یک شماره موبایل الزامی است.',
            'phone_numbers.*.required' => 'وارد کردن شماره موبایل الزامی است.',
            'phone_numbers.*.string'   => 'شماره موبایل باید یک رشته معتبر باشد.',
            'phone_numbers.*.max'      => 'شماره موبایل نباید بیشتر از 15 کاراکتر باشد.',
            'address.string'           => 'آدرس باید یک رشته معتبر باشد.',
            'province_id.required'     => 'انتخاب استان الزامی است.',
            'province_id.exists'       => 'استان انتخاب‌شده معتبر نیست.',
            'city_id.required'         => 'انتخاب شهر الزامی است.',
            'city_id.exists'           => 'شهر انتخاب‌شده معتبر نیست.',
            'postal_code.string'       => 'کد پستی باید یک رشته معتبر باشد.',
            'description.string'       => 'توضیحات باید یک رشته معتبر باشد.',
        ]);

        $clinic = Clinic::findOrFail($id);
        $clinic->update([
            'name'          => $request->name,
            'phone_numbers' => json_encode($request->phone_numbers),
            'address'       => $request->address,
            'province_id'   => $request->province_id,
            'city_id'       => $request->city_id,
            'postal_code'   => $request->postal_code,
            'description'   => $request->description,
        ]);

        return response()->json(['message' => 'مطب با موفقیت ویرایش شد']);
    }

    public function edit($id)
    {
        $clinic = Clinic::findOrFail($id);

        return response()->json([
            'id'            => $clinic->id,
            'name'          => $clinic->name,
            'phone_numbers' => json_decode($clinic->phone_numbers, true), // تبدیل به آرایه
            'address'       => $clinic->address,
            'description'   => $clinic->description,
            'province_id'   => $clinic->province_id,
            'city_id'       => $clinic->city_id,
        ]);
    }

    public function getCitiesByProvince($provinceId)
    {
        $cities = Zone::where('parent_id', $provinceId)->get(['id', 'name']);
        return response()->json($cities);
    }

    public function destroy($id)
    {
        $clinic = Clinic::findOrFail($id);
        $clinic->delete();

        return response()->json(['message' => 'مطب با موفقیت حذف شد']);
    }

    public function gallery($id)
    {
        return view("dr.panel.doctors-clinic.gallery",compact('id'));
    }

 
public function medicalDoc()
{
    $doctorId = Auth::guard('doctor')->user()->id ?? Auth::guard('secretary')->user()->doctor_id;
    return view("dr.panel.doctors-clinic.medicalDoc", compact('doctorId'));
}
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }
}
