<?php
namespace App\Http\Controllers;

use App\Http\Requests\AddressStoreRequest;
use App\Models\Address;
use App\Models\Users;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;


class AddressController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = Address::query();

        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where('name', 'like', "%$search%")
                  ->orWhere('mobile', 'like', "%$search%")
                  ->orWhere('alternate_mobile', 'like', "%$search%")
                  ->orWhere('door_no', 'like', "%$search%")
                  ->orWhere('street_name', 'like', "%$search%")
                  ->orWhere('city', 'like', "%$search%")
                  ->orWhere('pincode', 'like', "%$search%")
                  ->orWhere('state', 'like', "%$search%")
                  ->orWhereHas('users', function ($q) use ($search) {
                    $q->where('name', 'like', "%$search%");
                });
        }

        if ($request->wantsJson()) {
            return response($query->get());
        }
        $address = $query->latest()->paginate(10);
        $users = Users::all();
     
         return view('address.index', compact('address', 'users'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('address.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(AddressStoreRequest $request)
    {
        $address = Address::create([
            'name' => $request->name,
            'mobile' => $request->mobile,
            'alternate_mobile' => $request->alternate_mobile,
            'door_no' => $request->door_no,
            'street_name' => $request->street_name,
            'city' => $request->city,
            'pincode' => $request->pincode,
            'state' => $request->state,
        ]);

        if (!$address) {
            return redirect()->back()->with('error', 'Sorry, Something went wrong while creating address.');
        }
        return redirect()->route('address.index')->with('success', 'Success, New address has been added successfully!');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Address  $address
     * @return \Illuminate\Http\Response
     */
    public function show(Address $address)
    {
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Address  $address
     * @return \Illuminate\Http\Response
     */
    public function edit(Address $address)
    {
        $users = Users::all(); // Fetch all shops
        return view('address.edit', compact('address', 'users'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Address  $address
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Address $address)
    {
        $address->name = $request->name;
        $address->mobile = $request->mobile;
        $address->alternate_mobile = $request->alternate_mobile;
        $address->door_no = $request->door_no;
        $address->street_name = $request->street_name;
        $address->city = $request->city;
        $address->pincode = $request->pincode;
        $address->state = $request->state;

        if (!$address->save()) {
            return redirect()->back()->with('error', 'Sorry, Something went wrong while updating the address.');
        }
        return redirect()->route('address.edit', $address->id)->with('success', 'Success, address has been updated.');
    }

    public function destroy(Address $address)
    {
        $address->delete();

        return response()->json([
            'success' => true
        ]);
    }
}
