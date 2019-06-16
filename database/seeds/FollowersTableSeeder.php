<?php

use Illuminate\Database\Seeder;

class FollowersTableSeeder extends Seeder
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
            $r2 = rand(1, 100);
            $r2 = ($r2 - $r1) == 0 ? rand(1, 100) : $r2;

            while(!\DB::table('followers')->where([
                'follower_id' => $r1,
                'leader_id' => $r2
            ])->count()){
                \DB::table('followers')->insert([
                    'follower_id' => $r1,
                    'leader_id' => $r2
                ]);
            }

        }
    }
}
