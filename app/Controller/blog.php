<?php declare(strict_types=1);
/**
 * DuckPHP
 * From this time, you never be alone~
 */
namespace MY\Controller;

use MY\Base\Helper\ControllerHelper as C;
use MY\Service\BlogService;

class blog
{
    public function __construct()
    {
        C::setViewWrapper('layout/head','layout/foot');
    }
    protected function getAttribute($key,$default)
    {
        $data=C::getParameters();
        return $data[$key]??$default;
    }
    public function index()
    {
        $pageNum = (int)$this->getAttribute('page', 1);
        
        $data = BlogService::G()->getDataToIndex($pageNum);
        
        C::Show($data,'blog/index');
    }
    public function postx()
    {
        $slug = $this->getAttribute('slug', null);
        $data = BlogService::G()->getPostData($slug);
        if ($data === null) {
            C::Exit404();
            return;
        }
        
        C::Show($data,'blog/post/index');
    }
    public function tag()
    {
        $label = $this->getAttribute('label', null);
        $pageNum = (int)$this->getAttribute('page', 1);
        $data =  BlogService::G()->getTagData($label, $pageNum);
        if ($data['item'] === null) {
            C::Exit404();
            return;
        }
        C::Show($data,'blog/tag/index');
    }
    public function archive()
    {
        $archive = BlogService::G()->getArchiveData();
        
        C::Show(['archive'=>$archive],'blog/archive/index');
    }
    public function archive_yearly()
    {
        $year = $this->getAttribute('year', null);
        $items = BlogService::G()->getArchiveDataYearly((int)$year);
        $data = [
            'year' => $year,
            'items' => $items,
        ];
        
        C::Show($data,'blog/archive/yearly-archive');
    }
    public function archive_monthly()
    {
        $pageNum = (int)$this->getAttribute('page', 1);
        $year = (int)$this->getAttribute('year', null);
        $month = (int)$this->getAttribute('month', null);
        
        $data = BlogService::G()->getArchiveDataMonthly($year,$month,$pageNum);
        
        C::Show($data,'blog/archive/monthly-archive');
    }
}
