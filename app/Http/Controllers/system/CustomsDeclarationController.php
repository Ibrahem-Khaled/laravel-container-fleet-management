<?php

namespace App\Http\Controllers\system;

use App\Http\Controllers\Controller;
use App\Models\CustomsDeclaration;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class CustomsDeclarationController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'clearance_office_id' => 'required|exists:users,id',
            'statement_number' => ['required', 'numeric', Rule::unique('customs_declarations')],
            'client_selection_method' => 'required|in:existing,new',

            // CORRECTED RULE: Now it correctly checks for a non-empty value only when required.
            'client_id' => 'required_if:client_selection_method,existing|nullable|exists:users,id',

            'new_client_name' => 'required_if:client_selection_method,new|string|max:255',
            'new_client_phone' => 'nullable|string|max:20|unique:users,phone',
            'expire_date' => 'nullable|date',
            'weight' => 'nullable|numeric|min:0',

            'statement_status' => 'nullable|string',
            'containers' => 'required|array|min:1',
            'containers.*.number' => 'required|string|max:255',
            'containers.*.size' => 'required|in:20,40,box',
            'containers.*.is_rent' => 'required|boolean',
        ]);

        DB::beginTransaction();
        try {
            $clientId = $request->client_id;

            if ($request->client_selection_method === 'new') {
                $clientRole = Role::where('name', 'client')->firstOrFail();
                $newClient = User::create([
                    'name' => $request->new_client_name,
                    'phone' => $request->new_client_phone,
                    'role_id' => $clientRole->id,
                    'is_active' => true,
                ]);
                $clientId = $newClient->id;
            }

            $declaration = CustomsDeclaration::create([
                'clearance_office_id' => $request->clearance_office_id,
                'statement_number' => $request->statement_number,
                'client_id' => $clientId,
                'expire_date' => $request->expire_date,
                'weight' => $request->weight,
                'statement_status' => $request->statement_status,
            ]);

            foreach ($request->containers as $containerData) {
                $declaration->containers()->create([
                    'client_id' => $clientId,
                    'number' => $containerData['number'],
                    'size' => $containerData['size'],
                    'is_rent' => $containerData['is_rent'],
                ]);
            }

            DB::commit();
            return back()->with('success', 'تم إنشاء البيان الجمركي بنجاح.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating customs declaration: ' . $e->getMessage());
            return back()->with('error', 'حدث خطأ غير متوقع، يرجى مراجعة البيانات والمحاولة مرة أخرى.')->withInput();
        }
    }
}
