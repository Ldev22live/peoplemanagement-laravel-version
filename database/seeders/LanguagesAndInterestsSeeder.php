<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LanguagesAndInterestsSeeder extends Seeder
{
    public function run(): void
    {
        $languages = [
            'English', 'Afrikaans', 'Zulu', 'Xhosa', 'Sotho', 'Tswana', 'Venda', 'Tsonga', 'Swati', 'Ndebele'
        ];
        foreach ($languages as $language) {
            DB::table('languages')->updateOrInsert(['name' => $language]);
        }

        $interests = [
            'Sports', 'Music', 'Art', 'Reading', 'Travel', 'Technology', 'Cooking', 'Gaming', 'Movies', 'Photography'
        ];
        foreach ($interests as $interest) {
            DB::table('interests')->updateOrInsert(['name' => $interest]);
        }
    }
}
