<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\ValidationException;

class Loan extends Model
{
    use HasFactory;
    
    protected $fillable = ['amount', 'loan_term', 'remain', 'weekly_payment', 'created_by'];
    
}
