<?php declare(strict_types=1);
/**
 * DuckPHP
 * From this time, you never be alone~
 */

namespace MY\Service;

use MY\Base\BaseService;
use MY\Base\Helper\ServiceHelper as S;

use MY\Model\CommentModel;
use MY\Model\PostModel;
use MY\Model\PostTagModel;
use MY\Model\TagModel;
use MY\Model\UserModel;

use Faker\Factory;
use Faker\Generator;


class FixtureService extends BaseService
{
    private Generator $faker;
    /** @var User[] */
    private array $users = [];
    /** @var Tag[] */
    private array $tags = [];

    
    public function run($count)
    {
        $this->faker = Factory::create();
        $this->addUsers($count);
        $this->addTags($count);
        $this->addPosts($count);

    }

    private function addUsers(int $count): void
    {
        for ($i = 0; $i <= $count; ++$i) {
            $login = $this->faker->firstName . rand(0, 9999);
            $user=UserModel::create($login,$login);
            $this->users[] = $user;
        }
    }

    private function addTags(int $count): void
    {
        $tagWords = [];
        for ($i = 0, $fails = 0; $i <= $count; ++$i) {
            $word = $this->faker->word();
            if (in_array($word, $tagWords, true)) {
                --$i;
                ++$fails;
                if ($fails >= $count) {
                    break;
                }
                continue;
            }
            $tagWords[] = $word;
        }
        ////
        foreach ($tagWords as $word) {
            $tag = TagModel::getOrCreateTag($word);
            $this->tags[] = $tag;
        }
    }

    private function addPosts(int $count): void
    {
        if (count($this->users) === 0) {
            throw new \Exception('No users');
        }
        for ($i = 0; $i < $count; ++$i) {
            /** @var User $postUser */
            $user_id = $this->users[array_rand($this->users)];
            
            $title=$this->faker->text(64);
            $slug=static::RandomString(128);
            $content=$this->faker->realText(rand(1000, 4000));
            $post_id=PostModel::addPost($user_id,$title,$slug,$content);
            if(!$post_id){
                var_dump($post_id);
                continue;
            }
            $postTags = (array)array_rand($this->tags, rand(1, count($this->tags)));
            foreach ($postTags as $key) {
                $tag_id=$this->tags[$key];
                PostTagModel::addPostTag($post_id,$tag_id);
            }
            $commentsCount = rand(0, $count);
            for ($j = 0; $j <= $commentsCount; ++$j) {
                $comment_user_id = $this->users[array_rand($this->users)];
                $content= $this->faker->realText(rand(100, 500));
                CommentModel::addComment($comment_user_id,$post_id,$content);
            }
        }
    }

    protected static function RandomString(int $length = 32): string
    {
        if ($length < 1) {
            throw new \InvalidArgumentException('First parameter ($length) must be greater than 0');
        }

        $bytes = random_bytes($length);
        return substr(strtr(base64_encode($bytes), '+/', '-_'), 0, $length);
    }
}
