<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Mail\FicheEmail;
use App\Models\Company;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;

/**
 * @OA\Schema(
 *     schema="Company",
 *     type="object",
 *     title="Company",
 *     description="Entreprise",
 *     required={"name_company", "description_company", "picture_company", "zipcode", "phone", "address", "siret", "town"},
 *     @OA\Property(
 *         property="id",
 *         type="integer",
 *         description="ID of the company"
 *     ),
 *     @OA\Property(
 *         property="name_company",
 *         type="string",
 *         description="Name of the company"
 *     ),
 *     @OA\Property(
 *         property="description_company",
 *         type="string",
 *         description="Description of the company"
 *     ),
 *     @OA\Property(
 *         property="picture_company",
 *         type="string",
 *         format="binary",
 *         description="Picture of the company"
 *     ),
 *     @OA\Property(
 *         property="zipcode",
 *         type="string",
 *         description="Zipcode of the company"
 *     ),
 *     @OA\Property(
 *         property="phone",
 *         type="string",
 *         description="Phone number of the company"
 *     ),
 *     @OA\Property(
 *         property="address",
 *         type="string",
 *         description="Address of the company"
 *     ),
 *     @OA\Property(
 *         property="siret",
 *         type="string",
 *         description="SIRET number of the company"
 *     ),
 *     @OA\Property(
 *         property="town",
 *         type="string",
 *         description="Town of the company"
 *     ),
 *     @OA\Property(
 *         property="lat",
 *         type="number",
 *         format="float",
 *         description="Latitude of the company location"
 *     ),
 *     @OA\Property(
 *         property="long",
 *         type="number",
 *         format="float",
 *         description="Longitude of the company location"
 *     ),
 *     @OA\Property(
 *         property="id_user",
 *         type="integer",
 *         description="ID of the user who created the company"
 *     )
 * )
 */
class CompanyController extends Controller
{
    /**
     * @OA\Get(
     *     path="/company",
     *     summary="Obtenir toutes les entreprises",
     * tags={"Company"},
     *     @OA\Response(
     *         response=200,
     *         description="A list of companies"
     *     )
     * )
     */
    public function index()
    {
        $company = Company::all();
        return response()->json($company);
    }

    /**
     * @OA\Get(
     *     path="/company/latest",
     *     summary="Obtenez les dernières entreprises",
     * tags={"Company"},
     * 
     *     @OA\Response(
     *         response=200,
     *         description="A list of the latest companies"
     *     )
     * )
     */
    public function getLatestIds()
    {
        $latestIds = Company::orderBy('id', 'desc')->take(2)->get(['id', 'name_company', 'description_company', 'picture_company']);
        return response()->json($latestIds);
    }

    /**
     * @OA\Post(
     *     path="/company",
     *     summary="Créer une nouvelle entreprise",
     * tags={"Company"},
     * 
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name_company", type="string"),
     *             @OA\Property(property="description_company", type="string"),
     *             @OA\Property(property="picture_company", type="string", format="binary"),
     *             @OA\Property(property="zipcode", type="string"),
     *             @OA\Property(property="phone", type="string"),
     *             @OA\Property(property="address", type="string"),
     *             @OA\Property(property="siret", type="string"),
     *             @OA\Property(property="town", type="string"),
     *             @OA\Property(property="lat", type="number", format="float"),
     *             @OA\Property(property="long", type="number", format="float")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Company created successfully"
     *     )
     * )
     */
    public function store(Request $request)
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
        ]);

        $filename = "";
        if ($request->hasFile('picture_company')) {
            $filenameWithExt = $request->file('picture_company')->getClientOriginalName();
            $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
            $extension = $request->file('picture_company')->getClientOriginalExtension();
            $filename = $filename . '_' . time() . '.' . $extension;
            $request->file('picture_company')->storeAs('public/uploads/companies', $filename);
        }

        $user = Auth::user();

        $company = Company::create([
            'name_company' => $request->name_company,
            'description_company' => $request->description_company,
            'picture_company' => $filename,
            'zipcode' => $request->zipcode,
            'phone' => $request->phone,
            'address' => $request->address,
            'siret' => $request->siret,
            'town' => $request->town,
            'lat' => $request->lat,
            'long' => $request->long,
            'id_user' => $user->id,
        ]);

        // Changement du rôle de l'utilisateur 
        if ($user->id_role == 1) {
            $user->id_role = 2;
            $user->save();
        }

        return response()->json([
            'status' => 'Success',
            'data' => $company,
        ]);
    }

    /**
     * @OA\Get(
     *     path="/company/{id}",
     *     summary="Obtenir une entreprise par identifiant",
     * tags={"Company"},
     * 
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Company details"
     *     )
     * )
     */
    public function show(Company $company)
    {
        return response()->json($company);
    }


    /**
     * @OA\Put(
     *     path="/company/{id}",
     *     summary="Mettre à jour une entreprise",
     * tags={"Company"},
     * 
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name_company", type="string"),
     *             @OA\Property(property="description_company", type="string"),
     *             @OA\Property(property="picture_company", type="string", format="binary"),
     *             @OA\Property(property="zipcode", type="string"),
     *             @OA\Property(property="phone", type="string"),
     *             @OA\Property(property="address", type="string"),
     *             @OA\Property(property="siret", type="string"),
     *             @OA\Property(property="town", type="string"),
     *             @OA\Property(property="lat", type="number", format="float"),
     *             @OA\Property(property="long", type="number", format="float")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Company updated successfully"
     *     )
     * )
     */
    public function update(Request $request, Company $company)
    {
        $request->validate([
            'name_company' => 'required|max:50',
            'description_company' => 'required|max:400',
            'zipcode' => 'required|max:5',
            'phone' => 'required|max:50',
            'address' => 'required|max:150',
            'siret' => 'required',
            'town' => 'required|max:50',
        ]);

        $filename = $company->picture_company;
        if ($request->hasFile('picture_company')) {
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

        $user = Auth::user();

        $company->update([
            'name_company' => $request->name_company,
            'description_company' => $request->description_company,
            'picture_company' => $filename,
            'zipcode' => $request->zipcode,
            'phone' => $request->phone,
            'address' => $request->address,
            'siret' => $request->siret,
            'town' => $request->town,
            'lat' => $request->lat,
            'long' => $request->long,
            'id_user' => $user->id,
        ]);

        if ($user->id_role == 1) {
            $user->id_role = 2;
            $user->save();
        }

        return response()->json([
            'status' => 'Update OK',
            'data' => $company,
        ]);
    }

    /**
     * @OA\Delete(
     *     path="/company/{id}",
     *     summary="Supprimer une entreprise",
     * tags={"Company"},
     * 
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Company deleted successfully"
     *     )
     * )
     */
    public function destroy(Company $company)
    {
        $company->delete();
        $user = Auth::user();
        if ($user->id_role == 2) {
            $user->id_role = 1;
            $user->save();
        }

        return response()->json([
            'status' => 'Delete OK',
        ]);
    }
}