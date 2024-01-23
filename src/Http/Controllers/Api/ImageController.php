<?php

namespace Sergmoro1\Imageable\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Sergmoro1\Imageable\Http\Controllers\Controller;
use Sergmoro1\Imageable\Services\ImageKeeper;
use Sergmoro1\Imageable\Models\Image;

class ImageController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/images",
     *     operationId="createImage",
     *     tags={"Images"},
     *     summary="Save image",
     *     description="Save just uploaded image",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\Parameter(
     *             name="imageable_id",
     *             description="ID in the model to which the image belongs",
     *             @OA\Schema(
     *                type="integer",
     *                format="int32"
     *             )
     *         ),
     *         @OA\Parameter(
     *             name="imageable_type",
     *             description="class of a model to which the image belong",
     *             @OA\Schema(
     *                type="string",
     *                example="App\Models\Post"
     *             )
     *         ),
     *         @OA\Parameter(
     *             name="file_input",
     *             description="uploaded image",
     *             @OA\Schema(
     *                 type="string",
     *                 format="binary"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="success",
     *                 type="boolean",
     *                 example="true"
     *             ),
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Image saved."
     *             )
     *         )
     *     )
     * )
     * 
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        return response()->json(
            ImageKeeper::proceed($request), 
            200
        );
    }

    /**
     * @OA\Put(
     *     path="/api/images",
     *     operationId="updateImage",
     *     tags={"Images"},
     *     summary="Update image",
     *     description="Update existing image",
     *     @OA\Parameter(
     *         name="id",
     *         in="query",
     *         description="Image ID",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             format="int32"
     *         )
     *     ),
     *     @OA\RequestBody(
     *         @OA\Parameter(
     *             name="oldIndex",
     *             description="image position before drag & drop, from 0 to the number of images - 1",
     *             required=true,
     *             @OA\Schema(
     *                 type="integer",
     *                 format="int32"
     *             )
     *         ),
     *         @OA\Parameter(
     *             name="newIndex",
     *             description="image position after drag & drop, from 0 to the number of images - 1",
     *             required=true,
     *             @OA\Schema(
     *                 type="integer",
     *                 format="int32"
     *             )
     *         ),
     *         @OA\Parameter(
     *             name="addons",
     *             description="json string with additional parameters",
     *             required=true,
     *             @OA\Schema(
     *                 type="string",
     *                 example="{'caption': 'image description', 'year': '2023', 'category': 'office'}"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="success",
     *                 type="boolean",
     *                 example="true"
     *             ),
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Image updated."
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Wrong parameters",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="success",
     *                 type="boolean",
     *                 example="false"
     *             ),
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Only the 'position' and 'addons' fields can be updated for images."
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Entity not found",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="success",
     *                 type="boolean",
     *                 example="false"
     *             ),
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Not found."
     *             )
     *         )
     *     )
     * )
     * 
     * Update the specified resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $image = Image::find($id);
        if (!$image) {
            return $this->responseNotFound();
        }
        if ($request->has(['oldIndex', 'newIndex'])) {
            $oldIndex = $request->input('oldIndex');
            $newIndex = $request->input('newIndex');

            // when moving from bottom to top, the order of entries should be reversed,
            // then can be lead to the case of moving from the top to bottom
            $order = $oldIndex < $newIndex ? 'asc' : 'desc'; 
            // get all images of the model
            $images = Image::select(['id', 'position'])
                ->where('imageable_type', $image->imageable_type)
                ->where('imageable_id', $image->imageable_id)
                ->orderBy('position', $order)
                ->get();
            
            if ($oldIndex > $newIndex) {
                // moving from bottom to top
                $countImages = count($images) - 1;
                // we swap the indexes and adjust them since the order of records was reversed
                list($oldIndex, $newIndex) = [$countImages - $oldIndex, $countImages - $newIndex];
            }
            
            foreach ($images as $ind => $image) {
                if ($ind < $oldIndex) {
                    continue;
                } else if ($ind == $oldIndex) {
                    $oldImage = $image;
                    $prevPosition = $image->position;
                } else if ($ind <= $newIndex) {
                    $temp = $image->position;
                    $image->position = $prevPosition;
                    $image->update();
                    if ($ind == $newIndex) {
                        $oldImage->position = $temp;
                        $oldImage->update();
                    } else {
                        $prevPosition = $temp;
                    }
                } else {
                    break;
                }
            }
        } else if ($request->has('addons')) {
            $image->addons = $request->input('addons');
            $image->save();
        } else {
            return response()->json([
                'success' => false,
                'message' => 'imageable::messages.only_position_and_addons_can_be_updated',
            ], 403);
        }
        
        return response()->json([
            'success' => true,
            'message' => __('imageable::messages.image_updated',['id' => $id]),
        ], 200);
    }

    /**
     * @OA\Delete(
     *     path="/api/images/{id}",
     *     operationId="deleteImage",
     *     tags={"Images"},
     *     summary="Delete image",
     *     description="Delete image by ID",
     *     @OA\Parameter(
     *         name="id",
     *         in="query",
     *         description="Image ID",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             format="int32"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="success",
     *                 type="boolean",
     *                 example="true"
     *             ),
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Image deleted."
     *             )
     *         )
     *     )
     * )
     * 
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $image = Image::find($id);
        if (!$image) {
            return $this->responseNotFound();
        }
        // find model
        $modelClassName = $image->imageable_type;
        $model = $modelClassName::find($image->imageable_id);
        // delete all files from disk
        Storage::disk($model->getDisk())->delete($image->url);
        Storage::disk($model->getDisk())->delete($image->getThumbnailUrl(false));
        // delete image information
        $image->delete();
        
        return response()->json([
            'success' => true,
            'message' => __('imageable::messages.image_deleted', [
                'id' => $id,
                'thumbnail' => $image->getThumbnailUrl(),
            ]),
        ], 200);
   }
}
