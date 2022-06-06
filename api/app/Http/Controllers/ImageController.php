<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Exception;
use Response;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Input;
use File;
use Log;
use Image;
use FFMpeg;

class ImageController extends Controller
{

    //verify image
    public function verifyImage($image_array)
    {
        $image_type = $image_array->getMimeType();
        $image_size = $image_array->getSize();

        $MAXIMUM_FILESIZE = 10 * 1024 * 1024;

        if (!($image_type == 'image/png' || $image_type == 'image/jpeg'))
            $response = Response::json(array('code' => 201, 'message' => 'Please select PNG or JPEG ', 'cause' => '', 'data' => json_decode("{}")));
        elseif ($image_size > $MAXIMUM_FILESIZE)
            $response = Response::json(array('code' => 201, 'message' => 'File Size is greater then 5MB', 'cause' => '', 'data' => json_decode("{}")));
        else
            $response = '';
        return $response;
    }

    //generate image new name
    public function generateNewFileName($image_type, $image_array)
    {

        $fileData = pathinfo(basename($image_array->getClientOriginalName()));
        $new_file_name = uniqid() . '_' . $image_type . '_' . time() . '.' . $fileData['extension'];
        $path = Config::get('constant.ORIGINAL_IMAGES_DIRECTORY') . $new_file_name;
        if (File::exists($path))
            $new_file_name = uniqid() . '_' . $image_type . '_' . time() . '.' . $fileData['extension'];
        return $new_file_name;
    }

    //generate New thumnail File Name
    public function generateNewthumnailFileName($image_type, $image_array)
    {

        $fileData = pathinfo(basename($image_array->getClientOriginalName()));
        $new_file_name = uniqid() . '_' . $image_type . '_' . time() . '.' . 'png';
        $path = Config::get('constant.ORIGINAL_IMAGES_DIRECTORY') . $new_file_name;
        if (File::exists($path))
            $new_file_name = uniqid() . '_' . $image_type . '_' . time() . '.' . 'png';
        return $new_file_name;
    }

    // Save original Image
    public function saveOriginalImage($img)
    {
        $original_path = '../..' . Config::get('constant.ORIGINAL_IMAGES_DIRECTORY');
        $image = Input::file('file')->move($original_path, $img);
        //Log::info('saveOriginalImage',['Original_Name'=>$image]);
    }

    // Save original Video
    public function saveOriginalVideo($video)
    {
        $original_path = '../..' . Config::get('constant.ORIGINAL_VIDEO_DIRECTORY');
        Input::file('video_file')->move($original_path, $video);
    }

    // Save encoded Image
    public function saveEncodedImage($image_array, $profile_image)
    {
        $path = '../..' . Config::get('constant.ORIGINAL_IMAGES_DIRECTORY') . $profile_image;
        file_put_contents($path, $image_array);
    }

    // Save Compressed Image
    public function saveCompressedImage($cover_img)
    {
        try {
            $original_path = '../..' . Config::get('constant.ORIGINAL_IMAGES_DIRECTORY') . $cover_img;
            $compressed_path = '../..' . Config::get('constant.COMPRESSED_IMAGES_DIRECTORY') . $cover_img;
            $img = Image::make($original_path);
            $img->save($compressed_path, 75);

            $original_img_size = filesize($original_path);
            $compress_img_size = filesize($compressed_path);
            //Log::info(["Original Img Size :"=>$original_img_size,"Compress Img Size :"=>$compress_img_size]);
            if ($compress_img_size >= $original_img_size) {
                //save original image in Compress image
                Log::info("Compress Image Deleted.!", ["Original Img Size :" => $original_img_size, "Compress Img Size :" => $compress_img_size]);
                File::delete($compressed_path);
                File::copy($original_path, $compressed_path);
            }

        } catch (Exception $e) {
            $dest1 = base_path() . Config::get('constant.ORIGINAL_IMAGES_DIRECTORY') . $cover_img;
            $dest2 = base_path() . Config::get('constant.COMPRESSED_IMAGES_DIRECTORY') . $cover_img;
            foreach ($_FILES['file'] as $check) {
                chmod($dest1, 0777);
                copy($dest1, $dest2);
                Log::info("Exception :", [$e->getMessage()]);
            }
        }
    }

    // Get Thumbnail Width Height
    public function getThumbnailWidthHeight($profile_image)
    {
        $original_path = '../..' . Config::get('constant.ORIGINAL_IMAGES_DIRECTORY') . $profile_image;
        $image_size = getimagesize($original_path);
        $width_orig = $image_size[0];
        $height_orig = $image_size[1];
        $ratio_orig = $width_orig / $height_orig;

        $width = $width_orig < Config::get('constant.THUMBNAIL_WIDTH') ? $width_orig : Config::get('constant.THUMBNAIL_WIDTH');
        $height = $height_orig < Config::get('constant.THUMBNAIL_HEIGHT') ? $height_orig : Config::get('constant.THUMBNAIL_HEIGHT');

        if ($width / $height > $ratio_orig)
            $width = $height * $ratio_orig;
        else
            $height = $width / $ratio_orig;

        $array = array('width' => $width, 'height' => $height);
        return $array;
    }

    // Save Thumbnail Image
    public function saveThumbnailImage($profile_image)
    {
        try {
            $array = $this->getThumbnailWidthHeight($profile_image);
            $width = $array['width'];
            $height = $array['height'];
            $original_path = '../..' . Config::get('constant.ORIGINAL_IMAGES_DIRECTORY') . $profile_image;
            $thumbnail_path = '../..' . Config::get('constant.THUMBNAIL_IMAGES_DIRECTORY') . $profile_image;
            $img = Image::make($original_path)->resize($width, $height);
            $img->save($thumbnail_path);
        } catch (Exception $e) {
            $dest1 = Config::get('constant.ORIGINAL_IMAGES_DIRECTORY') . $profile_image;
            $dest2 = Config::get('constant.THUMBNAIL_IMAGES_DIRECTORY') . $profile_image;
            foreach ($_FILES['file'] as $check) {
                chmod($dest1, 0777);
                copy($dest1, $dest2);
            }
        }
    }

    // save Compressed and Thumbnail Image
    public function saveCompressedThumbnailImage($source_url, $destination_url, $thumbnail_path)
    {
        $info = getimagesize($source_url);
        $width_orig = $info[0];
        $height_orig = $info[1];
        $ratio_orig = $width_orig / $height_orig;

        $width = $width_orig < Config::get('constant.THUMBNAIL_WIDTH') ? $width_orig : Config::get('constant.THUMBNAIL_WIDTH');
        $height = $height_orig < Config::get('constant.THUMBNAIL_HEIGHT') ? $height_orig : Config::get('constant.THUMBNAIL_HEIGHT');

        if ($width / $height > $ratio_orig)
            $width = $height * $ratio_orig;
        else
            $height = $width / $ratio_orig;

        if ($info['mime'] == 'image/jpeg') {
            // save compress image
            $image = imagecreatefromjpeg($source_url);
            imagejpeg($image, $destination_url, 75);

            // save thumbnail image
            $tmp_img = imagecreatetruecolor($width, $height);
            imagecopyresized($tmp_img, $image, 0, 0, 0, 0, $width, $height, $width_orig, $height_orig);
            imagejpeg($tmp_img, $thumbnail_path);

        } elseif ($info['mime'] == 'image/png') {
            // save compress image
            $image = imagecreatefrompng($source_url);
            imagepng($image, $destination_url, 9);

            // save thumbnail image
            $tmp_img = imagecreatetruecolor($width, $height);
            imagealphablending($tmp_img, false);
            imagesavealpha($tmp_img, true);
            $transparent = imagecolorallocatealpha($tmp_img, 255, 255, 255, 127);
            imagefilledrectangle($tmp_img, 0, 0, $width_orig, $height_orig, $transparent);
            imagecopyresized($tmp_img, $image, 0, 0, 0, 0, $width, $height, $width_orig, $height_orig);
            imagepng($tmp_img, $thumbnail_path);
        }
    }

    //=====================================================Save Multiple image========================================================
    // Save original Image
    public function saveMultipleOriginalImage($img, $file_name)
    {

        $original_path = '../..' . Config::get('constant.ORIGINAL_IMAGES_DIRECTORY');
        Input::file($file_name)->move($original_path, $img);

    }

    public function saveMultipleCompressedImage($cover_img, $file_name)
    {
        try {
            $original_path = '../..' . Config::get('constant.ORIGINAL_IMAGES_DIRECTORY') . $cover_img;
            $compressed_path = '../..' . Config::get('constant.COMPRESSED_IMAGES_DIRECTORY') . $cover_img;
            $img = Image::make($original_path);
            $img->save($compressed_path, 75);

            $original_img_size = filesize($original_path);
            $compress_img_size = filesize($compressed_path);
            Log::info(["Original Img Size :" => $original_img_size, "Compress Img Size :" => $compress_img_size]);
            if ($compress_img_size >= $original_img_size) {
                //save original image in Compress image
                Log::info("Compress Image Deleted.!");
                File::delete($compressed_path);
                File::copy($original_path, $compressed_path);
            }

        } catch (Exception $e) {
            $dest1 = '../..' . Config::get('constant.ORIGINAL_IMAGES_DIRECTORY') . $cover_img;
            $dest2 = '../..' . Config::get('constant.COMPRESSED_IMAGES_DIRECTORY') . $cover_img;
            foreach ($_FILES[$file_name] as $check) {
                chmod($dest1, 0777);
                copy($dest1, $dest2);
                Log::info("Exception :", [$e->getMessage()]);
            }
        }
    }

    public function saveMultipleThumbnailImage($professional_img, $file_name)
    {
        try {
            $array = $this->getThumbnailWidthHeight($professional_img);
            $width = $array['width'];
            $height = $array['height'];
            $original_path = '../..' . Config::get('constant.ORIGINAL_IMAGES_DIRECTORY') . $professional_img;
            $thumbnail_path = '../..' . Config::get('constant.THUMBNAIL_IMAGES_DIRECTORY') . $professional_img;
            $img = Image::make($original_path)->resize($width, $height);
            $img->save($thumbnail_path);


        } catch (Exception $e) {
            $dest1 = '../..' . Config::get('constant.ORIGINAL_IMAGES_DIRECTORY') . $professional_img;
            $dest2 = '../..' . Config::get('constant.THUMBNAIL_IMAGES_DIRECTORY') . $professional_img;
            foreach ($_FILES[$file_name] as $check) {
                chmod($dest1, 0777);
                copy($dest1, $dest2);
            }
        }
    }

    //Delete Images In Directory
    public function deleteImage($image_name)
    {
        try {
            $this->unlinkFileFromLocalStorage($image_name, Config::get('constant.ORIGINAL_IMAGES_DIRECTORY'));
            $this->unlinkFileFromLocalStorage($image_name, Config::get('constant.COMPRESSED_IMAGES_DIRECTORY'));
            $this->unlinkFileFromLocalStorage($image_name, Config::get('constant.THUMBNAIL_IMAGES_DIRECTORY'));

        } catch (Exception $e) {
            Log::error("deleteImage : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            DB::rollBack();
            return Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . ' delete image.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
        }
    }

    // unlinkFileFromLocalStorage
    public function unlinkFileFromLocalStorage($file, $path)
    {
        try {
            $original_image_path = '../..' . $path . $file;
            if (($is_exist = ($this->checkFileExist($original_image_path)) != 0)) {
                unlink($original_image_path);
                //Log::info('unlink',['unlink'=>$original_image_path]);
            }
        } catch (Exception $e) {
            Log::debug("unlinkFileFromLocalStorage : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
        }
    }

    public function checkFileExist($file_path)
    {
        try {
            //if (fopen($original_sourceFile, "r")) {
            if (File::exists($file_path)) {
                //Log::info('file exist : ',['path' => $file_path]);
                $response = 1;
            } else {
                $response = 0;
                //Log::info('file does not exist : ', ['path' => $file_path]);
            }

        } catch (Exception $e) {
            $response = 0;
            Log::debug("checkFileExist : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
        }
        return $response;
    }
}
