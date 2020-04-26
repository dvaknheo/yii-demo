<?php declare(strict_types=1);

namespace MY\Base;
use DuckPhp\Ext\Pager;

use Yiisoft\Yii\Bootstrap4\Html;
use Yiisoft\Yii\Bootstrap4\Widget;

class BasePager extends Pager
{
    
    protected $pagesCount;
    protected $currentPage;
    protected $template;
    
    public function current() : int
    {
        return $this->options['current']??1;
    }
    public function pageSize($new_value = null)
    {
        return 5;
    }
    public function render($total, $options = []) : string
    {
        $this->pagesCount =(int) ceil($total / $this->pageSize());
        $this->currentPage = $this->current();
        if($this->pagesCount<=1){
            return '';
        }
        $this->prepareButtons();
        
        return implode("\n", [
            Html::beginTag('nav', ['id'=>'w0-post-card','class'=>'Page navigation']),
            Html::beginTag('ul', ['class' => 'pagination']),
            $this->renderButtons(),
            Html::endTag('ul'),
            Html::endTag('nav'),
        ]);
    }

    protected function prepareButtons(): void
    {
        if ($this->pagesCount > 9) {
            if ($this->currentPage <= 4) {
                $this->pages = [...range(1, 5), null, ...range($this->pagesCount - 2, $this->pagesCount)];
            } elseif ($this->pagesCount - $this->currentPage <= 4) {
                $this->pages = [1, 2, null, ...range($this->pagesCount - 5, $this->pagesCount)];
            } else {
                $this->pages = [
                    1,
                    2,
                    null,
                    $this->currentPage - 1,
                    $this->currentPage,
                    $this->currentPage + 1,
                    null,
                    $this->pagesCount - 1,
                    $this->pagesCount,
                ];
            }
        } else {
            $this->pages = range(1, $this->pagesCount);
        }
        $this->prepared = true;
    }

    protected function renderButtons(): string
    {
        $result = '';

        // `Previous` page
        $prevUrl = ($this->currentPage<=1) ? null : $this->getPageLink($this->currentPage - 1);
        $result .= Html::beginTag('li', ['class' => $prevUrl === null ? 'page-item disabled' : 'page-item']);
        $result .= Html::a('Previous', $prevUrl, ['class' => 'page-link']);
        $result .= Html::endTag('li');

        // Numeric buttons
        foreach ($this->pages as $page) {
            $isDisabled = $this->currentPage === $page || $page === null;
            $result .= Html::beginTag('li', ['class' => $isDisabled ? 'page-item disabled' : 'page-item']);
            if ($page === null) {
                $result .= Html::tag('span', '…', ['class' => 'page-link']);
            } else {
                $result .= Html::a((string)$page, $this->getPageLink($page), ['class' => 'page-link']);
            }
            $result .= Html::endTag('li');
        }

        // `Next` page
        $nextUrl = ($this->currentPage >= $this->pagesCount) ? null : $this->getPageLink($this->currentPage + 1);
        $result .= Html::beginTag('li', ['class' => $nextUrl === null ? 'page-item disabled' : 'page-item']);
        $result .= Html::a('Next', $nextUrl, ['class' => 'page-link']);
        $result .= Html::endTag('li');

        return $result;
    }

    protected function getPageLink(int $page): ?string
    {
        return str_replace('{page}',$page,$this->template);
    }
    public function pageExt($url,$pageNum)
    {
        $this->options['current']=$pageNum;
        $this->template=$url;
    }


}
