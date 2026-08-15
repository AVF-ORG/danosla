<?php

namespace App\Http\Requests\Shipment;

use App\Models\ShipmentBid;
use Illuminate\Foundation\Http\FormRequest;

class StoreBidRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('create', [ShipmentBid::class, $this->route('shipment')]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        if ($this->isNegotiable()) {
            return [
                'price' => 'required|numeric|min:0',
                'latest_pickup_date' => 'required|date',
                'latest_pickup_time' => 'required',
                'latest_delivery_date' => 'required|date',
                'latest_delivery_time' => 'required',
                'message' => 'nullable|string',
            ];
        }

        // Direct request: no negotiable terms are collected.
        return [
            'message' => 'nullable|string',
        ];
    }

    /**
     * Whether this submission is a negotiable offer (full terms) rather than
     * a direct request.
     */
    public function isNegotiable(): bool
    {
        return $this->boolean('is_negotiable', true);
    }
}
