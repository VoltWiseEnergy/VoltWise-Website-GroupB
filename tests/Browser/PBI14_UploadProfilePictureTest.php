<?php

namespace Tests\Browser;

use App\Models\User;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class PBI14_UploadProfilePictureTest extends DuskTestCase
{
    public function test_user_can_upload_profile_picture()
    {
        $user = User::first();

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                    ->visit('/profile')
                    ->assertPathIs('/profile')
                    ->attach('avatar', __DIR__.'/fixtures/test-photo.jpg')
                    ->press('Save Changes')
                    ->assertSee('Profile updated successfully!')
                    ->screenshot('PBI14-upload-profile-picture');
        });
    }
}