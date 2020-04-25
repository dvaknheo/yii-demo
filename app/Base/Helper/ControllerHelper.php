<?php declare(strict_types=1);
/**
 * DuckPHP
 * From this time, you never be alone~
 */
namespace MY\Base\Helper;

use DuckPhp\Core\Route;
use DuckPhp\Helper\ControllerHelper as Helper;

class ControllerHelper extends Helper
{
   public static function getUploadFile($field)
   {
        $files = $request->getUploadedFiles();
        if (!empty($files[$field]) && $files[$field]->getError() === UPLOAD_ERR_OK) {
            $file = $files[$field];
        }else{
            $file=null;
        }
        return $file;
    }
}
