<?php

namespace Controllers;
use Intervention\Image\ImageManagerStatic as Image;

class ImageController {

    public function waterImage(){

        $image = Image::make(ROOT.'public/uploads/image.jpeg');
        
        $image->insert(ROOT.'public/uploads/water.gif','top-right');

        $image->save(ROOT.'public/uploads/waterImage.jpg');
    }

    public function cropImage(){

        $image = Image::make(ROOT.'public/uploads/image.jpeg');

        $image->crop(300,300,470,25);

        $image->save( ROOT.'public/uploads/cropImage.jpg');
    }
}