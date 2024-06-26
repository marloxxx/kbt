<?php

namespace App\Http\Controllers\Backend;

use App\Models\Driver;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Intervention\Image\Facades\Image;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class DriverController extends Controller
{
    private $messages;
    public function __construct()
    {
        $this->messages = [
            'name.required' => 'Nama harus diisi',
            'phone.required' => 'Nomor telepon harus diisi',
            'phone.numeric' => 'Nomor telepon harus berupa angka',
            'phone.unique' => 'Nomor telepon sudah digunakan',
            'address.required' => 'Alamat harus diisi',
            'image.required' => 'Gambar harus diisi',
            'image.image' => 'Gambar harus berupa gambar',
            'image.mimes' => 'Gambar harus berupa jpeg, png, jpg, gif, svg',
            'image.max' => 'Gambar maksimal 2048 KB',
        ];
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            return DataTables::of(Driver::query())
                ->addColumn('action', function ($driver) {
                    return '
                    <div class="btn-group" role="group">
                    <a href="' . route('backend.drivers.edit', $driver->id) . '" class="btn btn-sm btn-warning">
                        <i class="fas fa-solid fa-pen-to-square"></i>
                    </a>
                    <a href="javascript:;" onclick="handle_confirm(\'Apakah Anda Yakin?\',\'Yakin\',\'Tidak\',\'DELETE\',\'' . route('backend.drivers.destroy', $driver->id) . '\');" class="btn btn-sm btn-danger">
                        <i class="fa fa-trash"></i>
                    </a>
                    </div>';
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('pages.backend.driver.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('pages.backend.driver.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validators = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'phone' => ['required', 'numeric', Rule::unique('drivers')->whereNull('deleted_at'), 'regex:/^(08)[0-9]{9,12}$/'],
            'address' => 'required|string|max:255',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ], $this->messages);

        if ($validators->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validators->errors()->first(),
            ]);
        }

        $image = $request->file('image');
        $image_name = time() . '.' . $image->extension();
        $image_resize = Image::make($image->getRealPath());
        $image_resize->resize(300, 300);
        if (!file_exists(public_path('images/drivers'))) {
            mkdir(public_path('images/drivers'), 0777, true);
        }
        $image_resize->save(public_path('images/drivers/' . $image_name));


        $driver = Driver::create([
            'name' => $request->name,
            'phone' => $request->phone,
            'address' => $request->address,
            'image' => asset('images/drivers/' . $image_name),
        ]);

        if ($driver) {
            return response()->json([
                'status' => 'success',
                'message' => 'Data berhasil ditambahkan',
                'redirect' => route('backend.drivers.index'),
            ]);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Data gagal ditambahkan',
            ]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Driver $driver)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Driver $driver)
    {
        return view('pages.backend.driver.edit', compact('driver'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Driver $driver)
    {
        $validators = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'phone' => ['required', 'numeric', Rule::unique('drivers')->ignore($driver->id)->whereNull('deleted_at'), 'regex:/^(08)[0-9]{9,12}$/'],
            'address' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ], $this->messages);

        if ($validators->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validators->errors()->first(),
            ]);
        }

        if ($request->hasFile('image')) {
            if (file_exists(public_path('images/drivers/' . $driver->image))) {
                unlink(public_path('images/drivers/' . $driver->image));
            }
            $image = $request->file('image');
            $image_name = time() . '.' . $image->extension();
            $image_resize = Image::make($image->getRealPath());
            $image_resize->resize(300, 300);
            if (!file_exists(public_path('images/drivers'))) {
                mkdir(public_path('images/drivers'), 0777, true);
            }
            $image_resize->save(public_path('images/drivers/' . $image_name));
            $driver->image = asset('images/drivers/' . $image_name);
        }

        $driver->name = $request->name;
        $driver->phone = $request->phone;
        $driver->address = $request->address;
        $driver->save();

        if ($driver) {
            return response()->json([
                'status' => 'success',
                'message' => 'Data berhasil diubah',
                'redirect' => route('backend.drivers.index'),
            ]);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Data gagal diubah',
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Driver $driver)
    {
        if (file_exists(public_path('images/drivers/' . $driver->image))) {
            unlink(public_path('images/drivers/' . $driver->image));
        }

        $driver->delete();
        return response()->json([
            'status' => 'success',
            'message' => 'Data berhasil dihapus',
        ]);
    }
}
