<?php

namespace Tests\Feature;

use Sergmoro1\Imageable\Tests\TestCase;
use Sergmoro1\Imageable\Tests\BasicAuth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;
use Sergmoro1\Imageable\Models\User;
use Sergmoro1\Imageable\Models\Image;

class ImageApiTest extends TestCase
{
    /**
     * Api image tests - store, update, delete.
     *
     * @return void
     */
    public function test_api_image()
    {
        // set credentials
        BasicAuth::setKey('sergmoro1@ya.ru', 'password');
        // create user
        $user = User::factory()->create([
            'email' => 'sergmoro1@ya.ru',
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
        ]);
        
        /* Store */

        // create picture
        $file = UploadedFile::fake()->image('avatar.jpg');
        // send post request
        $this->withHeaders(["Authorization" => BasicAuth::getKey()])
            ->postJson('api/images', [
                'imageable_type' => 'Sergmoro1\Imageable\Models\User',
                'imageable_id' => $user->id,
                'file_input' => $file,
            ])->assertJsonFragment(['success' => 1]);
        // verify the uploaded file exists
        Storage::disk($user->getDisk())->assertExists($user->getFullPath() . '/' . $file->hashName());

        /* Update addons */

        // find image by file name
        $image = Image::where(['original' => 'avatar.jpg'])->first();
        // update addons
        $image->addons = '{"caption": "Jhon Tatarin", "age": "30"}';
        // send put request
        $this->withHeaders(["Authorization" => BasicAuth::getKey()])
            ->putJson('api/images/' . $image->id, $image->toArray())
            ->assertStatus(200);

        /* Update position */

        // add second image
        $file = UploadedFile::fake()->image('photo.jpg');
        $this->withHeaders(["Authorization" => BasicAuth::getKey()])
            ->postJson('api/images', [
                'imageable_type' => 'Sergmoro1\Imageable\Models\User',
                'imageable_id' => $user->id,
                'file_input' => $file,
            ])->assertJsonFragment(['success' => 1]);

        // find just added image
        $swapping_image = Image::where(['original' => 'photo.jpg'])->first();
        // the previous image has a position smaller than the one just added
        $this->assertLessThan($swapping_image->position, $image->position);

        // swap positions
        $this->withHeaders(["Authorization" => BasicAuth::getKey()])
            ->putJson('api/images/' . $image->id, ['oldIndex' => 0, 'newIndex' => 1])
            ->assertStatus(200);

        // find first image
        $image = Image::where(['original' => 'avatar.jpg'])->first();
        // find second image
        $swapping_image = Image::where(['original' => 'photo.jpg'])->first();
        // now the newly added image has a smaller position
        $this->assertLessThan($image->position, $swapping_image->position);
        
        /* Delete both images */

        $this->withHeaders(["Authorization" => BasicAuth::getKey()])
            ->deleteJson('api/images/' . $image->id)
            ->assertStatus(200);

        $this->withHeaders(["Authorization" => BasicAuth::getKey()])
            ->deleteJson('api/images/' . $swapping_image->id)
            ->assertStatus(200);
    }
}
