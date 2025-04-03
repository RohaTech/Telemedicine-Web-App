<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LabResult extends Model
{

    use HasFactory;

    protected $fillable = [
        'lab_request_id',
        'laboratory_id',
        'result_details',
        'attachment',
    ];

    /**
     * Relationship with LabRequest
     * A lab result belongs to a lab request.
     */
    public function labRequest()
    {
        return $this->belongsTo(LabRequest::class, 'lab_request_id');
    }

    /**
     * Relationship with Laboratory
     * A lab result is provided by a laboratory.
     */
    public function laboratory()
    {
        return $this->belongsTo(Laboratory::class, 'laboratory_id');
    }
}
