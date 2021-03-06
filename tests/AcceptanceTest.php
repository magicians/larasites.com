<?php

use App\User;
use App\Host;
use App\Site;
use Carbon\Carbon;
use App\Submission;

class AcceptanceTest extends TestCase
{
    public function testApprovedAndFeaturedSites()
    {
        Artisan::call('migrate');

        $user = new User;
        $user->twitter_id = 1;
        $user->twitter_nickname = 'laravelphp';
        $user->twitter_avatar = '...';
        $user->twitter_avatar_original = '...';
        $user->save();

        $site = new Site;
        $site->url = 'http://larabelle.com';
        $site->title = 'Larabelle';
        $site->description = '...';
        $site->image_url = '...';
        $site->approved_at = Carbon::now();
        $site->approved_by = $user->id;
        $site->user_id = $user->id;
        $site->featured_at = Carbon::now();
        $site->featured_by = $user->id;
        $site->save();

        $this->visit('/')->see('Larabelle');
        $this->visit('/')->see('http://larabelle.com');

        $this->visit('/latest')->see('Larabelle');
        $this->visit('/latest')->see('http://larabelle.com');

        $this->visit('/popular')->see('Larabelle');
        $this->visit('/popular')->see('http://larabelle.com');
    }

    public function testSubmittingSites()
    {
        Artisan::call('migrate');

        $user = new User;
        $user->twitter_id = 1337;
        $user->twitter_nickname = 'test';
        $user->twitter_avatar = '...';
        $user->twitter_avatar_original = '...';
        $user->save();

        $this->actingAs($user)
            ->visit('/submit')
            ->type('http://laravel.com', 'url')
            ->press('Submit')
            ->seePageIs('/thank-you');

        $this->assertEquals(1, Host::count());
        $this->assertEquals(1, Submission::count());

        $this->assertEquals('laravel.com', Host::first()->name);
    }
}
