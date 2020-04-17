<?php

declare(strict_types=1);

namespace App\Blog\Widget;

use App\Blog\Entity\Post;
use Yiisoft\Router\UrlGeneratorInterface;
use Yiisoft\Yii\Bootstrap4\Html;
use Yiisoft\Yii\Bootstrap4\Widget;

class PostCard extends Widget
{
    private ?Post $post = null;

    private array $options = [];

    private UrlGeneratorInterface $urlGenerator;

    protected $data=[];

    public function __construct(UrlGeneratorInterface $urlGenerator)
    {
        $this->urlGenerator = $urlGenerator;
    }

    //override
    protected function run(): string
    {
        if (!isset($this->options['id'])) {
            $this->options['id'] = "{$this->getId()}-post-card";
        }

        $this->initOptions();

        $this->registerPlugin('page-card', $this->options);


$template=<<<'EOT'
<div id="w0-post-card" class="card mb-4" data-post-slug="{slug}">
<div class="card-body d-flex flex-column align-items-start">
<a class="mb-0 h4 text-decoration-none" href="{url_post}">{title}</a>
<div class="mb-1 text-muted">{publishAt}by <a href="{url_user}">{userLogin}</a></div><p class="card-text mb-auto">{content_short}</p>
<div class="mt-3">
[[
<a class="btn btn-outline-secondary btn-sm mx-1 mt-1" href="{url_tag_label}">{tag_label}</a>
]]
</div>

</div>
</div>

EOT;
'EOT';
//return $template;


        return implode("\n", [
            Html::beginTag('div', $this->options),
            Html::beginTag('div', ['class' => 'card-body d-flex flex-column align-items-start']),
            $this->renderHead(),
            $this->renderBody(),
            $this->renderTags(),
            Html::endTag('div'),
            Html::endTag('div'),
        ]);
    }

    protected function renderHead(): string
    {
        return Html::a(
            Html::encode($this->post->getTitle()),
            $this->urlGenerator->generate('blog/post', ['slug' => $this->post->getSlug()]),
            ['class' => 'mb-0 h4 text-decoration-none'] // stretched-link
        );
    }

    protected function renderBody(): string
    {
        return Html::tag(
            'div',
            $this->post->getPublishedAt()->format('M, d') . ' by ' . Html::a(
                Html::encode($this->post->getUser()->getLogin()),
                $this->urlGenerator->generate('user/profile', ['login' => $this->post->getUser()->getLogin()])
            ),
            ['class' => 'mb-1 text-muted']
        ) . Html::tag(
            'p',
            Html::encode(mb_substr($this->post->getContent(), 0, 400))
            . (mb_strlen($this->post->getContent()) > 400 ? '…' : ''),
            ['class' => 'card-text mb-auto']
        );
    }

    protected function renderTags(): string
    {
        $return = Html::beginTag('div', ['class' => 'mt-3']);
        foreach ($this->post->getTags() as $tag) {
            $return .= Html::a(
                Html::encode($tag->getLabel()),
                $this->urlGenerator->generate('blog/tag', ['label' => $tag->getLabel()]),
                ['class' => 'btn btn-outline-secondary btn-sm mx-1 mt-1']
            );
        }
        return $return . Html::endTag('div');
    }

    protected function initOptions(): void
    {
        Html::addCssClass($this->options, ['widget' => 'card mb-4']);
    }

    public function post(?Post $post): self
    {
        $this->post = $post;
        $this->data=[
            'title'=>$post->getTitle(),
            'slug'=>$post->getSlug(),
            'publishedAt'=>$post->getPublishedAt()->format('M, d'),
            'userLogin'=>$post->getUser()->getLogin(),
            'content'=>$post->getContent(),
            'getTags'=>$post->getTags(),
        ];

        if ($post !== null) {
            $this->options['data']['post-slug'] = $post->getSlug(); //$post->getSlug();
        }

        return $this;
    }
    /**
     * The HTML attributes for the widget container tag. The following special options are recognized.
     *
     * {@see \Yiisoft\Html\Html::renderTagAttributes()} for details on how attributes are being rendered.
     */
    public function options(array $value): self
    {
        $this->options = $value;

        return $this;
    }
}
