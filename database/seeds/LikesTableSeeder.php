<?php

use Illuminate\Database\Seeder;

class LikesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach(range(1, 100) as $x){
            $r1 = rand(1, 100);
            $r2 = rand(1, 400);
            $r2 = ($r2 - $r1) == 0 ? rand(1, 400) : $r2;

            while(!\DB::table('likes')->where([
                'user_id' => $r1,
                'tweet_id' => $r2
            ])->count()){
                \DB::table('likes')->insert([
                    'user_id' => $r1,
                    'tweet_id' => $r2
                ]);
            }
        }
    }
}
