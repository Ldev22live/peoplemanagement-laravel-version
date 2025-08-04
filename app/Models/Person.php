<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Person extends Model
{
    use HasFactory;

    protected $table = 'people';

    protected $fillable = [
        'name', 'surname', 'id_number', 'mobile_number', 'email', 'birth_date', 'language_id'
    ];

    public function language()
    {
        return $this->belongsTo(Language::class);
    }

    public function interests()
    {
        return $this->belongsToMany(Interest::class, 'people_interests', 'person_id', 'interest_id');
    }
}
