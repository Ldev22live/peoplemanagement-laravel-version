<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Interest extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    public function people()
    {
        return $this->belongsToMany(Person::class, 'people_interests', 'interest_id', 'person_id');
    }
}
