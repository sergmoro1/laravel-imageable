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
    public $user;
    public $avatar;
    public $response;
    
    public function setUp(): void
    {
        parent::setUp();

        // set credentials
        BasicAuth::setKey('sergmoro1@ya.ru', 'password');
        
        // create user
        $this->user = User::factory()->create([
            'email' => 'sergmoro1@ya.ru',
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
        ]);
        // create image
        $this->avatar = UploadedFile::fake()->image('avatar.jpg');

        // send post request
        $this->response = $this->withHeaders(["Authorization" => BasicAuth::getKey()])
            ->postJson('api/images', [
                'imageable_type' => 'Sergmoro1\Imageable\Models\User',
                'imageable_id' => $this->user->id,
                'file_input' => $this->avatar,
            ]);
    }

    /**
     * Api image store test.
     *
     * @return void
     */
    public function test_api_image_store()
    {
        // User and avater created succeessfully
        $this->response->assertJsonFragment(['success' => 1]);
        // verify the uploaded file exists
        Storage::disk($this->user->getDisk())
            ->assertExists($this->user->getFullPath() . '/' . $this->avatar->hashName());
    }

    /**
     * Api image update addons test.
     *
     * @return void
     */
    public function test_api_image_update_addons()
    {
        // find image by file name
        $image = Image::where(['original' => 'avatar.jpg'])->first();
        // update addons
        $image->addons = '{"caption": "Jhon Tatarin", "age": "30"}';
        // send put request
        $this->withHeaders(["Authorization" => BasicAuth::getKey()])
            ->putJson('api/images/' . $image->id, $image->toArray())
            ->assertStatus(200);
    }

    /**
     * Api image swapping two images positions test.
     *
     * @return void
     */
    public function test_api_image_swapping()
    {
        // find image by file name
        $image = Image::where(['original' => 'avatar.jpg'])->first();
        // adding second image for swapping
        $file = UploadedFile::fake()->image('photo.jpg');
        $this->withHeaders(["Authorization" => BasicAuth::getKey()])
            ->postJson('api/images', [
                'imageable_type' => 'Sergmoro1\Imageable\Models\User',
                'imageable_id' => $this->user->id,
                'file_input' => $file,
            ]);
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
        // find swapping image
        $swapping_image = Image::where(['original' => 'photo.jpg'])->first();
        // now the newly added image has a smaller position
        $this->assertLessThan($image->position, $swapping_image->position); 
    }

    /**
     * Api image delete test.
     *
     * @return void
     */
    public function test_api_image_delete()
    {
        // find and delete image
        $image = Image::where(['original' => 'avatar.jpg'])->first();
        $this->withHeaders(["Authorization" => BasicAuth::getKey()])
            ->deleteJson('api/images/' . $image->id)
            ->assertStatus(200);
        // verify the uploaded file not exists
        $this->assertTrue(!Storage::disk($this->user->getDisk())
            ->exists($this->user->getFullPath() . '/' . $this->avatar->hashName()));
    }
}
