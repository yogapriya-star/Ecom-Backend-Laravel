<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests\Auth\StoreAddressRequest;
use App\Http\Requests\Auth\UpdateAddressRequest;
use App\Http\Resources\AddressResource;
use App\Models\Address;


class AddressController extends Controller
{
    /**
     * List all addresses of logged-in user
    */
    public function index()
    {
        $addresses = Address::where('created_by', auth()->id())->get();
        return AddressResource::collection($addresses);
    }

    /**
     * Store new address
    */
    public function store(StoreAddressRequest $request)
    {
        $data = $request->validated();
        $data['created_by'] = auth()->id();
        $address = Address::create($data);
        return new AddressResource($address);
    }

    /**
     * Update address (only if created_by = auth user)
    */
   public function update(UpdateAddressRequest $request, $id)
    {
        $address = Address::where([
            'id' => $id,
            'created_by' => auth()->id(),
        ])->first();

        if (!$address) {
            return response()->json([
                'message' => 'This address does not belong to you or does not exist.'
            ], 403);
        }

        $address->update($request->validated());

        return new AddressResource($address);
}


    /**
     * Delete address
    */
    public function destroy($id)
    {
        $address = Address::where('id', $id)->where('created_by', auth()->id())->first();
        if (!$address) {
            return response()->json([
                'status' => false,
                'message' => 'Address not found',
            ], 404);
        }
        $address->delete();
        return response()->json(['message' => 'Address deleted successfully']);
    }

    /**
     * Set active address
    */
    public function setActiveAddress($id)
    {
        $userId = auth()->id();
        // Step 1: Make all of this user's addresses inactive
        Address::where('created_by', $userId)->update(['is_active' => false]);
        // Step 2: Activate the selected address
        $address = Address::where('id', $id)
            ->where('created_by', $userId)
            ->firstOrFail();
        $address->update(['is_active' => true]);
        return response()->json([
            'message' => 'Address set as active successfully',
            'active_address' => $address
        ], 200);
    }
}
