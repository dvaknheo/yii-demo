<?php declare(strict_types=1);
/**
 * DuckPHP
 * From this time, you never be alone~
 */
namespace MY\Base\Helper;

use DuckPhp\Helper\ControllerHelper as Helper;
use MY\Base\BasePager;

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
    public static function pageExt($url,$pageNum)
    {
        BasePager::G()->pageExt($url,$pageNum);
    }
    
    
    public static function MyExitXml($data)
    {
        //<response><status>success</status><data>
        $data=[
            'response'=>
            [
                'status'=>'success',
                'data'=>$data,
            ]
        ];
        $content = '';
        if ($data !== null) {
            $dom = new \DOMDocument('1.0','UTF-8');

            static::buildXml($dom, $data);
            $content = $dom->saveXML();
        }

        static::header('Content-Type:application/xml; UTF-8');
        echo $content;
    }
    public static function MyExitJson($data)
    {
        $ret=[
            'status'=>'success',
            'data'=>$data,
        ];
        return static::ExitJson($ret,true);
    }

    protected static function buildXml($element, $data): void
    {
        if (is_array($data)) {
            foreach ($data as $name => $value) {
                if (is_int($name) ) {
                    static::buildXml($element, $value);
                } elseif (is_array($value) ) {
                    $child = new \DOMElement($name);
                    $element->appendChild($child);
                    static::buildXml($child, $value);
                } else {
                    $child = new \DOMElement($name);
                    $element->appendChild($child);
                    $child->appendChild(new \DOMText(is_string($value)?$value:json_encode($value)));
                }
            }
        } else {
            var_dump($data);
            $element->appendChild(new \DOMText(is_string($data)?$data:json_encode($data)));
        }
    }
    
}
