<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GuideResource extends JsonResource
{
    // Properti untuk status dan pesan
    public $status;
    public $message;

    // Constructor untuk mengisi properti
    public function __construct($status, $message, $resource)
    {
        parent::__construct($resource);
        $this->status  = $status;
        $this->message = $message;
    }

    /**
     * Transform data menjadi array
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'success'   => $this->status,
            'message'   => $this->message,
            // $this->resource berisi data model Guide yang dikirim dari controller
            'data'      => $this->resource
        ];
    }
}
