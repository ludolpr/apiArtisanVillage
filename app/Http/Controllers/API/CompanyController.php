<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CompanyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $company = Company::all();
        return response()->json($company);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Company $company)
    {
        $request->validate([
            'name_company' => 'required|max:50',
            'description_company' => 'required|max:400',
            'picture_company' => 'required|image|max:10000',
            'zipcode' => 'required|max:5',
            'phone' => 'required|max:50',
            'address' => 'required|max:150',
            'siret' => 'required',
            'town' => 'required|max:50',
            'lat' => 'required|max:50',
            'long' => 'required|max:50',
            'id_user' => 'required',
        ]);

        $filename = "";
        if ($request->hasFile('picture_company')) {
            $filenameWithExt = $request->file('picture_company')->getClientOriginalName();
            $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
            $extension = $request->file('picture_company')->getClientOriginalExtension();
            $filename = $filename . '_' . time() . '.' . $extension;
            $request->file('picture_company')->storeAs('public/uploads/companies', $filename);
        }

        $company = Company::create([
            'name_company' => $request->name_company,
            'description_company' => $request->description_company,
            'picture_company' => $filename,
            'zipcode' => $request->zipcode,
            'phone' => $request->phone,
            'address' => $request->address,
            'name_company' => $request->name_company,
            'siret' => $request->siret,
            'town' => $request->town,
            'lat' => $request->lat,
            'long' => $request->long,
            // 'id_user' => auth()->id(),
            'id_user' => $request->id_user,
        ]);


        return response()->json([
            'status' => 'Success',
            'data' => $company,
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Company $company)
    {
        return response()->json($company);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Company $company)
    {
        $request->validate([
            'name_company' => 'required|max:50',
            'description_company' => 'required|max:400',
            'picture_company' => 'required|image|max:10000',
            'zipcode' => 'required|max:5',
            'phone' => 'required|max:50',
            'address' => 'required|max:150',
            'siret' => 'required',
            'town' => 'required|max:50',
            'lat' => 'required|max:50',
            'long' => 'required|max:50',
            'id_user' => 'required',
        ]);

        if ($request->hasFile('picture_company')) {
            // Delete old file
            if ($company->picture_company) {
                Storage::delete('public/uploads/companies/' . $company->picture_company);
            }

            $filenameWithExt = $request->file('picture_company')->getClientOriginalName();
            $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
            $extension = $request->file('picture_company')->getClientOriginalExtension();
            $filename = $filename . '_' . time() . '.' . $extension;
            $request->file('picture_company')->storeAs('public/uploads/companies', $filename);

            $company->picture_company = $filename;
        }

        $company->update([
            'name_company' => $request->name_company,
            'description_company' => $request->description_company,
            'picture_company' => $filename,
            'zipcode' => $request->zipcode,
            'phone' => $request->phone,
            'address' => $request->address,
            'name_company' => $request->name_company,
            'siret' => $request->siret,
            'town' => $request->town,
            'lat' => $request->lat,
            'long' => $request->long,
            'id_user' => $request->id_user,
        ]);

        // Return the updated information in JSON
        return response()->json([
            'status' => 'Update OK',
            'data' => $company,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Company $company)
    {
        $company->delete();

        return response()->json([
            'status' => 'Delete OK',
        ]);
    }
}